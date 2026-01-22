<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        if (!session()->has('usuario_id')) {
            return redirect()->route('login')
                ->withErrors(['general' => 'Debes iniciar sesión para finalizar la compra.']);
        }

        $cliCedula = $this->cedulaCliente();

        $row = DB::connection('oracle')->table('DATOS_CLI_FACTURA')
            ->whereRaw('TRIM(CLI_CEDULA) = ?', [$cliCedula])
            ->orderByDesc('DCF_CODIGO')
            ->first();

        $aceptaPrevio = false;
        if ($row) {
            $flag = $row->DCF_ACEPTA_TERMINOS ?? $row->dcf_acepta_terminos ?? null;
            $aceptaPrevio = (strtoupper(trim((string) $flag)) === 'S');
        }

        return view('checkout.index', [
            'aceptaPrevio' => $aceptaPrevio,
        ]);
    }

    private function cedulaCliente(): string
    {
        $usrNombre = session('usuario_id');

        $row = DB::connection('oracle')->table('USUARIO_APP')
            ->where('USR_NOMBRE', $usrNombre)
            ->first();

        $ced = $row->CLI_CEDULA ?? $row->cli_cedula ?? null;

        if (!$ced) {
            abort(403, 'No se pudo identificar al cliente.');
        }

        return strtoupper(trim((string) $ced));
    }

    private function obtenerCarritoCodigo(string $cliCedula): ?string
    {
        $car = DB::connection('oracle')->table('CARRITO')
            ->whereRaw('TRIM(CLI_CEDULA) = ?', [$cliCedula])
            ->orderByDesc('CAR_CODIGO')
            ->first();

        if (!$car)
            return null;

        return (string) ($car->CAR_CODIGO ?? $car->car_codigo ?? null);
    }

    /**
     * Genera un código VARCHAR2(10) con baja probabilidad de colisión (sin usar MAX+1).
     * Para tablas remotas (NOTA_VENTA en U_GEN), no verifica exists() para evitar ORA-00942
     */
    private function generarCodigoSeguro10(string $table, string $pkColumn): string
    {
        // Para NOTA_VENTA (tabla remota en U_GEN), no verificar exists()
        $isRemoteTable = in_array($table, ['NOTA_VENTA', 'DETALLE_NOTA_VENTA', 'SUCURSAL']);

        for ($i = 0; $i < 25; $i++) {
            $base = (string) (int) (microtime(true) * 1000);
            $rand = str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
            $cand = substr($base . $rand, -10);

            if ($isRemoteTable) {
                // Para tablas remotas, confiar en timestamp+random (muy baja probabilidad de colisión)
                return $cand;
            }

            // Para tablas locales, verificar que no existe
            $existe = DB::connection('oracle')->table($table)
                ->where($pkColumn, $cand)
                ->exists();

            if (!$existe)
                return $cand;
        }

        return str_pad((string) random_int(1, 9999999999), 10, '0', STR_PAD_LEFT);
    }

    /**
     * Inserta en tabla remota: primero intenta por synonym (tabla normal),
     * si falla por ORA-00942 intenta con @LINK_AD.
     */
    private function insertRemote(string $table, array $data): void
    {
        $tryTables = [$table, $table . '@LINK_AD'];

        $cols = [];
        $vals = [];
        $bindings = [];

        foreach ($data as $col => $val) {
            $cols[] = $col;
            if ($val instanceof \Illuminate\Database\Query\Expression) {
                // DB::raw() - insertar directamente en SQL
                $vals[] = (string) $val;
            } else {
                $vals[] = '?';
                $bindings[] = $val;
            }
        }

        $sqlBase = "INSERT INTO %s (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";

        foreach ($tryTables as $t) {
            try {
                $sql = sprintf($sqlBase, $t);
                DB::connection('oracle')->statement($sql, $bindings);
                return;
            } catch (\Throwable $e) {
                $raw = (string) $e->getMessage();
                // Si la tabla no existe en este contexto, probamos la siguiente opción
                if (str_contains($raw, 'ORA-00942')) {
                    continue;
                }
                throw $e;
            }
        }

        throw new \Exception('No se pudo acceder a la tabla remota: ' . $table);
    }

    /**
     * Lee SUCURSAL (U_GEN). Intenta con synonym o con @LINK_AD.
     */
    private function obtenerSucursalCodigo(): ?string
    {
        // intentar synonym
        try {
            $row = DB::connection('oracle')->table('SUCURSAL')->select('SUC_CODIGO')->first();
            if ($row)
                return (string) ($row->SUC_CODIGO ?? $row->suc_codigo ?? null);
        } catch (\Throwable $e) {
            // si no existe, intentar con dblink
            $raw = (string) $e->getMessage();
            if (!str_contains($raw, 'ORA-00942')) {
                throw $e;
            }
        }

        // intentar con dblink
        $row = DB::connection('oracle')->selectOne("SELECT SUC_CODIGO FROM SUCURSAL@LINK_AD FETCH FIRST 1 ROWS ONLY");
        if (!$row)
            return null;

        return (string) ($row->SUC_CODIGO ?? $row->suc_codigo ?? null);
    }

    private function crearNotaVentaDesdeCarrito(string $carCodigo, string $cliCedula): string
    {
        // total desde carrito
        $car = DB::connection('oracle')->table('CARRITO')->where('CAR_CODIGO', $carCodigo)->first();
        $total = (float) ($car->CAR_MONTO_TOTAL ?? $car->car_monto_total ?? 0);

        // items del carrito (necesitamos PRO_CODIGO y cantidad y precio)
        // precio lo tomamos de PRODUCTO.PRO_PRECIO_VENTA (o accessor equivalente en BD)
        $items = DB::connection('oracle')->table('DETALLE_CARRITO dc')
            ->join('PRODUCTO p', 'p.PRO_CODIGO', '=', 'dc.PRO_CODIGO')
            ->where('dc.CAR_CODIGO', $carCodigo)
            ->selectRaw("
                dc.PRO_CODIGO as PRO_CODIGO,
                dc.DCT_CANTIDAD as CANTIDAD,
                p.PRO_PRECIO_VENTA as PRECIO
            ")
            ->get();

        if ($items->count() <= 0) {
            throw new \Exception('Tu carrito está vacío.');
        }

        $sucCodigo = $this->obtenerSucursalCodigo();
        if (!$sucCodigo) {
            throw new \Exception('No hay sucursales para registrar la venta.');
        }

        $ndvNumero = $this->generarCodigoSeguro10('NOTA_VENTA', 'NDV_NUMERO');

        // NOTA_VENTA (U_GEN)
        $this->insertRemote('NOTA_VENTA', [
            'NDV_NUMERO' => $ndvNumero,
            'SUC_CODIGO' => $sucCodigo,
            'PDD_CODIGO' => $carCodigo,
            'NDV_FECHA_EMISION' => date('Y-m-d'),
            'NDV_MONTO_TOTAL' => $total,
            'NDV_RESPONSABLE' => $cliCedula,
            'NDV_DESCRIPCION' => 'E',
        ]);

        // DETALLE_NOTA_VENTA (U_GEN)
        foreach ($items as $it) {
            $pro = (string) ($it->PRO_CODIGO ?? $it->pro_codigo ?? '');
            $cant = (int) ($it->CANTIDAD ?? $it->cantidad ?? 0);
            $precio = (float) ($it->PRECIO ?? $it->precio ?? 0);
            $monto = $precio * $cant;

            $this->insertRemote('DETALLE_NOTA_VENTA', [
                'NDV_NUMERO' => $ndvNumero,
                'PRO_CODIGO' => $pro,
                'DNV_CANTIDAD' => $cant,
                'DNV_MONTO' => $monto,
            ]);
        }

        return $ndvNumero;
    }

    public function procesar(Request $request)
    {
        if (!session()->has('usuario_id')) {
            return redirect()->route('login')
                ->withErrors(['general' => 'Debes iniciar sesión para finalizar la compra.']);
        }

        $cliCedulaSesion = $this->cedulaCliente();

        // DCF previo
        $dcf = DB::connection('oracle')->table('DATOS_CLI_FACTURA')
            ->whereRaw('TRIM(CLI_CEDULA) = ?', [$cliCedulaSesion])
            ->orderByDesc('DCF_CODIGO')
            ->first();

        $aceptaPrevio = false;
        if ($dcf) {
            $flag = $dcf->DCF_ACEPTA_TERMINOS ?? $dcf->dcf_acepta_terminos ?? null;
            $aceptaPrevio = (strtoupper(trim((string) $flag)) === 'S');
        }

        $aceptaAhora = $request->has('acepta_terminos');
        $aceptaFinal = ($aceptaPrevio || $aceptaAhora) ? 'S' : 'N';

        // Validación:
        // - si ya aceptó antes, tarjeta puede ir vacía (se usa la guardada)
        // - si no aceptó antes, tarjeta requerida (primera vez)
        $rules = [
            'nombre' => 'required|string|max:120',
            'cedula' => 'required|digits:10',
            'direccion' => 'required|string|max:200',
            'metodo_pago' => 'required|string|max:50',
        ];

        if ($aceptaPrevio) {
            $rules = array_merge($rules, [
                'numero_tarjeta' => 'nullable|digits:16',
                'mes_caducidad' => 'nullable|integer|min:1|max:12',
                'year_caducidad' => 'nullable|integer|min:1999',
                'codigo_seguridad' => 'nullable|digits:3',
            ]);
        } else {
            $rules = array_merge($rules, [
                'numero_tarjeta' => 'required|digits:16',
                'mes_caducidad' => 'required|integer|min:1|max:12',
                'year_caducidad' => 'required|integer|min:' . date('Y'),
                'codigo_seguridad' => 'required|digits:3',
            ]);
        }

        $request->validate($rules, [
            'required' => 'El campo :attribute es obligatorio.',
            'digits' => 'El campo :attribute debe tener exactamente :digits dígitos.',
            'integer' => 'El campo :attribute debe ser numérico.',
            'min' => 'El valor de :attribute no es válido.',
            'max' => 'El campo :attribute es muy largo.',
        ]);

        // Cédula del form debe coincidir
        $cedForm = strtoupper(trim((string) $request->cedula));
        if ($cedForm !== $cliCedulaSesion) {
            return back()->withErrors(['cedula' => 'La cédula no coincide con tu cuenta.'])->withInput();
        }

        // tarjeta: si ya aceptó y no envía, usar guardada
        $NUM = $request->numero_tarjeta;
        $MES = $request->mes_caducidad;
        $YEA = $request->year_caducidad;
        $CVV = $request->codigo_seguridad;

        if ($aceptaPrevio && (!$NUM || !$MES || !$YEA || !$CVV)) {
            $NUM = $dcf->DCF_NUMERO_TARJETA ?? $dcf->dcf_numero_tarjeta ?? null;
            $MES = $dcf->DCF_MES_CADUCIDAD ?? $dcf->dcf_mes_caducidad ?? null;
            $YEA = $dcf->DCF_YEAR_CADUCIDAD ?? $dcf->dcf_year_caducidad ?? null;
            $CVV = $dcf->DCF_CODIGO_SEGURIDAD ?? $dcf->dcf_codigo_seguridad ?? null;

            if (!$NUM || !$MES || !$YEA || !$CVV) {
                return back()->withErrors(['general' => 'Debes ingresar los datos de tu tarjeta al menos una vez.'])
                    ->withInput();
            }
        }

        try {
            DB::connection('oracle')->beginTransaction();

            // carrito
            $carCodigo = $this->obtenerCarritoCodigo($cliCedulaSesion);
            if (!$carCodigo) {
                DB::connection('oracle')->rollBack();
                return redirect()->route('home')->withErrors(['general' => 'Tu carrito está vacío.']);
            }

            $cantItems = (int) DB::connection('oracle')->table('DETALLE_CARRITO')
                ->where('CAR_CODIGO', $carCodigo)
                ->count();

            if ($cantItems <= 0) {
                DB::connection('oracle')->rollBack();
                return redirect()->route('home')->withErrors(['general' => 'Tu carrito está vacío.']);
            }

            // ===== DATOS_CLI_FACTURA - Simple UPDATE/INSERT =====
            // Los triggers ya fueron modificados por el usuario para no causar loop

            $dcfExistente = DB::connection('oracle')->table('DATOS_CLI_FACTURA')
                ->whereRaw('TRIM(CLI_CEDULA) = ?', [$cliCedulaSesion])
                ->orderByDesc('DCF_CODIGO')
                ->first();

            if ($dcfExistente) {
                // Actualizar registro existente
                $dcfCodigo = $dcfExistente->DCF_CODIGO ?? $dcfExistente->dcf_codigo ?? null;

                DB::connection('oracle')->table('DATOS_CLI_FACTURA')
                    ->where('DCF_CODIGO', $dcfCodigo)
                    ->update([
                        'DCF_NOMBRE' => $request->nombre,
                        'DCF_DIRECCION' => $request->direccion,
                        'DCF_METODO_PAGO' => $request->metodo_pago,
                        'DCF_NUMERO_TARJETA' => $NUM,
                        'DCF_MES_CADUCIDAD' => (int) $MES,
                        'DCF_YEAR_CADUCIDAD' => (int) $YEA,
                        'DCF_CODIGO_SEGURIDAD' => $CVV,
                        'DCF_ACEPTA_TERMINOS' => $aceptaFinal,
                    ]);
            } else {
                // Insertar nuevo registro
                $dcfCodigo = $this->generarCodigoSeguro10('DATOS_CLI_FACTURA', 'DCF_CODIGO');

                DB::connection('oracle')->table('DATOS_CLI_FACTURA')->insert([
                    'DCF_CODIGO' => $dcfCodigo,
                    'CLI_CEDULA' => $cliCedulaSesion,
                    'DCF_NOMBRE' => $request->nombre,
                    'DCF_DIRECCION' => $request->direccion,
                    'DCF_METODO_PAGO' => $request->metodo_pago,
                    'DCF_NUMERO_TARJETA' => $NUM,
                    'DCF_MES_CADUCIDAD' => (int) $MES,
                    'DCF_YEAR_CADUCIDAD' => (int) $YEA,
                    'DCF_CODIGO_SEGURIDAD' => $CVV,
                    'DCF_ACEPTA_TERMINOS' => $aceptaFinal,
                ]);
            }

            // ===== Generar NOTA_VENTA + DETALLE_NOTA_VENTA (U_GEN) =====
            $ndvNumero = $this->crearNotaVentaDesdeCarrito($carCodigo, $cliCedulaSesion);

            // ===== Vaciar carrito =====
            DB::connection('oracle')->table('DETALLE_CARRITO')
                ->where('CAR_CODIGO', $carCodigo)
                ->delete();

            DB::connection('oracle')->table('CARRITO')
                ->where('CAR_CODIGO', $carCodigo)
                ->update(['CAR_MONTO_TOTAL' => 0]);

            // si no acepta términos, borrar DCF al final
            if ($aceptaFinal === 'N') {
                DB::connection('oracle')->table('DATOS_CLI_FACTURA')
                    ->whereRaw('TRIM(CLI_CEDULA) = ?', [$cliCedulaSesion])
                    ->delete();
            }

            DB::connection('oracle')->commit();

            return redirect()->route('home')
                ->with('purchase_success', '¡Compra realizada con éxito! N° Nota de venta: ' . $ndvNumero);

        } catch (\Throwable $e) {
            DB::connection('oracle')->rollBack();
            \Log::error('Checkout falló: ' . $e->getMessage());

            // Mensaje simple + ORA + constraint
            $raw = (string) $e->getMessage();
            $ora = null;
            $cons = null;
            if (preg_match('/ORA-\d{5}/', $raw, $m))
                $ora = $m[0];
            if (preg_match('/\(([A-Z0-9_\.]+)\)/i', $raw, $m2))
                $cons = $m2[1];

            $msg = 'No se ha podido hacer tu pago. Inténtalo de nuevo.';
            if ($ora && $cons)
                $msg .= " ($ora - $cons)";
            else if ($ora)
                $msg .= " ($ora)";

            return back()->withErrors(['general' => $msg])->withInput();
        }
    }
}
