<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\UsuarioApp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function mostrarLogin()
    {
        return view('auth.login');
    }

    public function mostrarRegistro()
    {
        return view('auth.registro');
    }

    public function login(Request $request)
    {
        $request->validate(
            [
                'nombre' => 'required|string|min:3|max:50',
                'contrasena' => 'required|string|min:6|max:50',
            ],
            [
                'required' => 'El campo :attribute es obligatorio.',
                'min' => 'El campo :attribute debe tener al menos :min caracteres.',
                'max' => 'El campo :attribute es muy largo.',
            ]
        );

        try {
            $usr = UsuarioApp::query()
                ->where('USR_NOMBRE', $request->nombre)
                ->first();

            if (!$usr) {
                return back()
                    ->withErrors(['auth' => 'No existe una cuenta con ese usuario. Regístrate primero.'])
                    ->withInput();
            }

            $hash = sha1((string) $request->contrasena);

            // OJO: puede venir en mayúsculas o minúsculas según hidratación
            $guardada = trim((string) (($usr->USR_CONTRASENA ?? $usr->usr_contrasena) ?? ''));

            if (!hash_equals($guardada, $hash)) {
                return back()
                    ->withErrors(['auth' => 'Contraseña incorrecta.'])
                    ->withInput();
            }

            // Guardar usuario en sesión (middleware usa esto)
            $usrNombre = (string) (($usr->USR_NOMBRE ?? $usr->usr_nombre) ?? $request->nombre);
            session(['usuario_id' => $usrNombre]);

            return redirect()
                ->route('home')
                ->with('success', '¡Bienvenido/a!');
        } catch (\Throwable $e) {
            \Log::error('Login falló: ' . $e->getMessage());

            return back()
                ->withErrors([
                    'auth' => 'No se pudo conectar a la base de datos. Inténtalo de nuevo.',
                ])
                ->withInput();
        }
    }

    public function registro(Request $request)
    {
        $request->validate(
            [
                'nombre' => 'required|string|min:3|max:50',
                'cedula' => 'required|digits:10',
                'correo' => [
                    'required',
                    'email',
                    'max:320',
                    'regex:/@(gmail\.com|hotmail\.com|outlook\.com)$/i',
                ],
                'telefono' => 'nullable|string|max:14',
                'contrasena' => 'required|string|min:6|max:50|confirmed',
            ],
            [
                'required' => 'El campo :attribute es obligatorio.',
                'digits' => 'El campo :attribute debe tener exactamente :digits dígitos.',
                'email' => 'Ingresa un correo válido.',
                'regex' => 'El correo debe ser gmail.com, hotmail.com u outlook.com.',
                'confirmed' => 'Las contraseñas no coinciden.',
                'min' => 'El campo :attribute debe tener al menos :min caracteres.',
                'max' => 'El campo :attribute es muy largo.',
            ]
        );

        $USR_NOMBRE = trim((string) $request->nombre);
        $CLI_CEDULA = strtoupper(trim((string) $request->cedula));
        $CLI_CORREO = trim((string) $request->correo);
        $CLI_TELEFONO = $request->telefono ? trim((string) $request->telefono) : null;

        try {
            // Usuario duplicado
            $EXISTE_USUARIO = DB::connection('oracle')
                ->table('USUARIO_APP')
                ->whereRaw('TRIM(USR_NOMBRE) = ?', [$USR_NOMBRE])
                ->exists();

            if ($EXISTE_USUARIO) {
                return back()->withErrors(['nombre' => 'ESE NOMBRE DE USUARIO YA ESTÁ EN USO.'])->withInput();
            }

            $EMP_CEDULA_RUC = Cliente::obtenerEmpresaCedulaRuc();
            if (!$EMP_CEDULA_RUC) {
                return back()->withErrors(['general' => 'NO EXISTE UNA EMPRESA CONFIGURADA EN LA BASE DE DATOS.'])->withInput();
            }
            $EMP_CEDULA_RUC = strtoupper(trim((string) $EMP_CEDULA_RUC));

            DB::connection('oracle')->beginTransaction();

            // 1) Crear/asegurar CLIENTE en ADMIN (U_GEN)
            try {
                DB::connection('oracle')->statement("
                    MERGE INTO CLIENTE@LINK_AD C
                    USING (SELECT ? AS CLI_CEDULA FROM DUAL) S
                    ON (TRIM(C.CLI_CEDULA) = TRIM(S.CLI_CEDULA))
                    WHEN NOT MATCHED THEN
                    INSERT (CLI_CEDULA, EMP_CEDULA_RUC, CLI_CORREO, CLI_TELEFONO)
                    VALUES (?, ?, ?, ?)
                ", [$CLI_CEDULA, $CLI_CEDULA, $EMP_CEDULA_RUC, $CLI_CORREO, $CLI_TELEFONO]);
            } catch (\Throwable $e) {
                $RAW = (string) $e->getMessage();
                // si ya existe en ADMIN, ok
                if (!str_contains($RAW, 'ORA-00001'))
                    throw $e;
            }

            // 2) Asegurar CLIENTE en ECOMMERCE (U_COM) antes de crear USUARIO_APP
            //    Esto es necesario porque TIB_USUARIO_APP exige que exista CLIENTE en U_COM.
            //    Si el trigger de réplica rebota, intentamos confirmar si el cliente ya quedó creado.
            try {
                DB::connection('oracle')->table('CLIENTE')->insert([
                    'CLI_CEDULA' => $CLI_CEDULA,
                    'EMP_CEDULA_RUC' => $EMP_CEDULA_RUC,
                    'CLI_CORREO' => $CLI_CORREO,
                    'CLI_TELEFONO' => $CLI_TELEFONO,
                ]);
            } catch (\Throwable $e) {
                $RAW = (string) $e->getMessage();

                // Si ya existe (PK_CLIENTE), seguimos
                if (str_contains($RAW, 'ORA-00001') && str_contains($RAW, 'PK_CLIENTE')) {
                    // ok
                } else {
                    // Si es error de triggers distribuidos, igual verificamos si el cliente ya existe localmente
                    $EXISTE_CLIENTE_LOCAL = DB::connection('oracle')
                        ->table('CLIENTE')
                        ->whereRaw('TRIM(CLI_CEDULA) = ?', [$CLI_CEDULA])
                        ->exists();

                    if (!$EXISTE_CLIENTE_LOCAL) {
                        throw $e; // si no existe, sí es fatal
                    }
                    // si existe, seguimos
                }
            }

            // 3) Insertar USUARIO_APP (ya con CLIENTE existente en U_COM)
            DB::connection('oracle')->table('USUARIO_APP')->insert([
                'USR_NOMBRE' => $USR_NOMBRE,
                'CLI_CEDULA' => $CLI_CEDULA,
                'USR_CONTRASENA' => sha1((string) $request->contrasena),
            ]);

            DB::connection('oracle')->commit();

            session(['usuario_id' => $USR_NOMBRE]);

            return redirect()->route('home')->with('success', 'REGISTRO EXITOSO. ¡BIENVENIDO/A!');

        } catch (\Throwable $e) {
            DB::connection('oracle')->rollBack();

            $RAW = (string) $e->getMessage();
            \Log::error('ERROR REGISTRO', ['MSG' => $RAW]);

            $ORA = null;
            if (preg_match('/ORA-\d{5}/', $RAW, $M))
                $ORA = $M[0];

            // mensaje simple
            $MSG = 'NO SE PUDO COMPLETAR EL REGISTRO. INTÉNTALO DE NUEVO.';
            if ($ORA)
                $MSG .= " ($ORA)";

            return back()->withErrors(['general' => $MSG])->withInput();
        }
    }




    public function logout(Request $request)
    {
        $request->session()->forget('usuario_id');

        return redirect()
            ->route('home')
            ->with('success', 'Sesión cerrada.');
    }
}
