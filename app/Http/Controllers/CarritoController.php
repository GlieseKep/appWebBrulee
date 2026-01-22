<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CarritoController extends Controller
{
    private function cedulaCliente(): ?string
    {
        $usrNombre = session('usuario_id');
        if (!$usrNombre) return null;

        $row = DB::connection('oracle')
            ->table('USUARIO_APP')
            ->where('USR_NOMBRE', $usrNombre)
            ->first();

        if (!$row) return null;

        // ✅ Oracle puede devolver propiedades en minúsculas
        $ced = $row->CLI_CEDULA ?? $row->cli_cedula ?? null;
        if (!$ced) return null;

        return strtoupper(trim((string)$ced));
    }

    private function obtenerOCrearCarrito(string $cliCedula): string
    {
        // 1) Buscar último carrito del cliente
        $car = DB::connection('oracle')
            ->table('CARRITO')
            ->where('CLI_CEDULA', $cliCedula)
            ->orderByDesc('CAR_CODIGO')
            ->first();

        if ($car) {
            $codigo = $car->CAR_CODIGO ?? $car->car_codigo ?? null;
            if ($codigo) return (string)$codigo;
        }

        // 2) Crear uno nuevo (CAR_CODIGO VARCHAR2(10))
        // Solo tomamos códigos numéricos para evitar problemas
        $rowMax = DB::connection('oracle')
            ->table('CARRITO')
            ->selectRaw("NVL(MAX(TO_NUMBER(CAR_CODIGO)), 0) AS MAX_NUM")
            ->whereRaw("REGEXP_LIKE(CAR_CODIGO, '^[0-9]+$')")
            ->first();

        $maxNum = 0;
        if ($rowMax) {
            $maxNum = (int)($rowMax->MAX_NUM ?? $rowMax->max_num ?? 0);
        }

        $nuevo = str_pad((string)($maxNum + 1), 10, '0', STR_PAD_LEFT);

        DB::connection('oracle')->table('CARRITO')->insert([
            'CAR_CODIGO'      => $nuevo,
            'CLI_CEDULA'      => $cliCedula,
            'CAR_MONTO_TOTAL' => 0,
        ]);

        return $nuevo;
    }

    private function recalcularTotal(string $carCodigo): float
    {
        $row = DB::connection('oracle')
            ->table('DETALLE_CARRITO')
            ->where('CAR_CODIGO', $carCodigo)
            ->selectRaw('NVL(SUM(DCT_MONTO),0) AS TOTAL')
            ->first();

        $total = (float)($row->TOTAL ?? $row->total ?? 0);

        DB::connection('oracle')->table('CARRITO')
            ->where('CAR_CODIGO', $carCodigo)
            ->update(['CAR_MONTO_TOTAL' => $total]);

        return $total;
    }

    // GET /carrito/items
    public function items()
    {
        $cliCedula = $this->cedulaCliente();
        if (!$cliCedula) {
            return response()->json(['error' => 'No autenticado'], 401);
        }

        try {
            $car = DB::connection('oracle')
                ->table('CARRITO')
                ->where('CLI_CEDULA', $cliCedula)
                ->orderByDesc('CAR_CODIGO')
                ->first();

            if (!$car) {
                return response()->json(['items' => [], 'total' => 0]);
            }

            $carCodigo = (string)($car->CAR_CODIGO ?? $car->car_codigo ?? '');

            $rows = DB::connection('oracle')
                ->table('DETALLE_CARRITO D')
                ->join('PRODUCTO P', 'P.PRO_CODIGO', '=', 'D.PRO_CODIGO')
                ->where('D.CAR_CODIGO', $carCodigo)
                ->select([
                    'P.PRO_CODIGO AS ID',
                    'P.PRO_NOMBRE AS NOMBRE',
                    'P.PRO_PRECIO_VENTA AS PRECIO',
                    'D.DCT_CANTIDAD AS CANTIDAD',
                    'P.PRO_IMAGEN AS IMAGEN',
                    'P.PRO_ALT_IMAGEN AS ALT_IMAGEN',
                ])
                ->get();

            $items = $rows->map(function ($r) {
                $img = $r->IMAGEN ?? $r->imagen ?? '';
                $img = trim((string)$img);
                if ($img && !preg_match('/^https?:\/\//i', $img)) {
                    $img = asset(ltrim($img, '/'));
                }

                return [
                    'id' => (string)($r->ID ?? $r->id ?? ''),
                    'nombre' => (string)($r->NOMBRE ?? $r->nombre ?? ''),
                    'precio' => (float)($r->PRECIO ?? $r->precio ?? 0),
                    'cantidad' => (int)($r->CANTIDAD ?? $r->cantidad ?? 0),
                    'imagen' => $img,
                    'alt_imagen' => (string)($r->ALT_IMAGEN ?? $r->alt_imagen ?? ''),
                ];
            })->values();

            $total = $this->recalcularTotal($carCodigo);

            return response()->json(['items' => $items, 'total' => $total]);
        } catch (\Throwable $e) {
            \Log::error('Carrito items falló: ' . $e->getMessage());
            return response()->json(['error' => 'No se pudo cargar el carrito.'], 500);
        }
    }

    // POST /carrito/agregar
    public function agregar(Request $request)
    {
        $cliCedula = $this->cedulaCliente();
        if (!$cliCedula) {
            return response()->json(['error' => 'No autenticado'], 401);
        }

        $data = $request->validate([
            'pro_codigo' => 'required|string',
            'cantidad'   => 'required|integer|min:1|max:99',
        ]);

        $proCodigo = strtoupper(trim((string)$data['pro_codigo']));
        $cantidad  = (int)$data['cantidad'];

        try {
            DB::connection('oracle')->beginTransaction();

            // Verificar producto y obtener precio
            $prod = DB::connection('oracle')->table('PRODUCTO')
                ->where('PRO_CODIGO', $proCodigo)
                ->first();

            if (!$prod) {
                DB::connection('oracle')->rollBack();
                return response()->json(['error' => 'Producto no encontrado.'], 404);
            }

            $precio = (float)($prod->PRO_PRECIO_VENTA ?? $prod->pro_precio_venta ?? 0);

            // Obtener/crear carrito
            $carCodigo = $this->obtenerOCrearCarrito($cliCedula);

            // Si ya existe el detalle, sumamos; si no, insertamos
            $det = DB::connection('oracle')->table('DETALLE_CARRITO')
                ->where('CAR_CODIGO', $carCodigo)
                ->where('PRO_CODIGO', $proCodigo)
                ->first();

            if ($det) {
                $cantActual = (int)($det->DCT_CANTIDAD ?? $det->dct_cantidad ?? 0);
                $nuevaCant  = $cantActual + $cantidad;

                DB::connection('oracle')->table('DETALLE_CARRITO')
                    ->where('CAR_CODIGO', $carCodigo)
                    ->where('PRO_CODIGO', $proCodigo)
                    ->update([
                        'DCT_CANTIDAD' => $nuevaCant,
                        'DCT_MONTO'    => $precio * $nuevaCant,
                    ]);
            } else {
                DB::connection('oracle')->table('DETALLE_CARRITO')->insert([
                    'PRO_CODIGO'   => $proCodigo,
                    'CAR_CODIGO'   => $carCodigo,
                    'DCT_CANTIDAD' => $cantidad,
                    'DCT_MONTO'    => $precio * $cantidad,
                ]);
            }

            $total = $this->recalcularTotal($carCodigo);

            DB::connection('oracle')->commit();
            return response()->json(['success' => true, 'total' => $total]);
        } catch (\Throwable $e) {
            DB::connection('oracle')->rollBack();
            \Log::error('Carrito agregar falló: ' . $e->getMessage());

            // Mensaje simple pero útil
            $raw = (string)$e->getMessage();
            $ora = null;
            if (preg_match('/ORA-\d{5}/', $raw, $m)) $ora = $m[0];

            $msg = 'No se pudo agregar al carrito. Inténtalo de nuevo.';
            if ($ora === 'ORA-20002') {
                $msg = 'No se pudo crear el carrito para este usuario. Verifica que tu cliente exista correctamente.';
            }

            return response()->json(['error' => $msg], 500);
        }
    }

    // POST /carrito/eliminar
    public function eliminar(Request $request)
    {
        $cliCedula = $this->cedulaCliente();
        if (!$cliCedula) {
            return response()->json(['error' => 'No autenticado'], 401);
        }

        $data = $request->validate([
            'pro_codigo' => 'required|string',
        ]);

        $proCodigo = strtoupper(trim((string)$data['pro_codigo']));

        try {
            DB::connection('oracle')->beginTransaction();

            $car = DB::connection('oracle')
                ->table('CARRITO')
                ->where('CLI_CEDULA', $cliCedula)
                ->orderByDesc('CAR_CODIGO')
                ->first();

            if (!$car) {
                DB::connection('oracle')->commit();
                return response()->json(['success' => true, 'total' => 0]);
            }

            $carCodigo = (string)($car->CAR_CODIGO ?? $car->car_codigo ?? '');

            DB::connection('oracle')->table('DETALLE_CARRITO')
                ->where('CAR_CODIGO', $carCodigo)
                ->where('PRO_CODIGO', $proCodigo)
                ->delete();

            $total = $this->recalcularTotal($carCodigo);

            DB::connection('oracle')->commit();
            return response()->json(['success' => true, 'total' => $total]);
        } catch (\Throwable $e) {
            DB::connection('oracle')->rollBack();
            \Log::error('Carrito eliminar falló: ' . $e->getMessage());
            return response()->json(['error' => 'No se pudo eliminar el producto. Inténtalo de nuevo.'], 500);
        }
    }

    // POST /carrito/vaciar
    public function vaciar()
    {
        $cliCedula = $this->cedulaCliente();
        if (!$cliCedula) {
            return response()->json(['error' => 'No autenticado'], 401);
        }

        try {
            DB::connection('oracle')->beginTransaction();

            $car = DB::connection('oracle')
                ->table('CARRITO')
                ->where('CLI_CEDULA', $cliCedula)
                ->orderByDesc('CAR_CODIGO')
                ->first();

            if ($car) {
                $carCodigo = (string)($car->CAR_CODIGO ?? $car->car_codigo ?? '');

                DB::connection('oracle')->table('DETALLE_CARRITO')
                    ->where('CAR_CODIGO', $carCodigo)
                    ->delete();

                DB::connection('oracle')->table('CARRITO')
                    ->where('CAR_CODIGO', $carCodigo)
                    ->update(['CAR_MONTO_TOTAL' => 0]);
            }

            DB::connection('oracle')->commit();
            return response()->json(['success' => true, 'total' => 0]);
        } catch (\Throwable $e) {
            DB::connection('oracle')->rollBack();
            \Log::error('Carrito vaciar falló: ' . $e->getMessage());
            return response()->json(['error' => 'No se pudo vaciar el carrito. Inténtalo de nuevo.'], 500);
        }
    }
}
