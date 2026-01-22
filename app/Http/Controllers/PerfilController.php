<?php

namespace App\Http\Controllers;

use App\Models\UsuarioApp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PerfilController extends Controller
{
    public function obtenerPerfil()
    {
        $usuarioNombre = session('usuario_id');

        if (!$usuarioNombre) {
            return response()->json(['error' => 'No autenticado'], 401);
        }

        try {
            \Log::info('Buscando perfil para usuario: ' . $usuarioNombre);

            // 1) Traer el usuario (sin depender de relación)
            $usuario = UsuarioApp::find($usuarioNombre);

            if (!$usuario) {
                \Log::error('Usuario no encontrado en BD: ' . $usuarioNombre);
                return response()->json(['error' => 'Usuario no encontrado'], 404);
            }

            // OJO: dependiendo del driver oracle, los atributos pueden venir en minúscula
            // Por eso leo ambos posibles.
            $usrNombre = $usuario->USR_NOMBRE ?? $usuario->usr_nombre ?? null;
            $cliCedula = $usuario->CLI_CEDULA ?? $usuario->cli_cedula ?? null;

            \Log::info('Usuario encontrado:', [
                'usr_nombre' => $usrNombre,
                'cli_cedula' => $cliCedula,
            ]);

            if (!$cliCedula) {
                \Log::error('Usuario existe pero no tiene CLI_CEDULA.');
                return response()->json(['error' => 'Perfil incompleto (sin cliente asociado)'], 500);
            }

            // 2) Traer correo del cliente directo desde BD (100% seguro)
            $row = DB::connection('oracle')
                ->table('CLIENTE')
                ->select('CLI_CORREO')
                ->where('CLI_CEDULA', (string)$cliCedula)
                ->first();

            $correo = $row?->CLI_CORREO ?? '';

            \Log::info('Correo cliente encontrado:', ['correo' => $correo]);

            return response()->json([
                'nombre' => (string)($usrNombre ?? ''),
                'correo' => (string)$correo,
            ]);
        } catch (\Throwable $e) {
            \Log::error('Error en obtenerPerfil: ' . $e->getMessage());
            return response()->json(['error' => 'No se pudo cargar el perfil'], 500);
        }
    }

    public function actualizarNombre(Request $request)
    {
        // Mantener el botón, pero NO cambiar username (PK en Oracle)
        return response()->json([
            'success' => false,
            'message' => 'Por seguridad, el nombre de usuario no se puede cambiar.',
        ], 422);
    }

    public function verificarPedidos()
    {
        if (!session()->has('usuario_id')) {
            return response()->json(['error' => 'No autenticado'], 401);
        }

        return response()->json([
            'success' => true,
            'tienePedidos' => false,
        ]);
    }

    public function eliminarCuenta()
    {
        if (!session()->has('usuario_id')) {
            return response()->json(['error' => 'No autenticado'], 401);
        }

        try {
            $usuario = UsuarioApp::find(session('usuario_id'));
            if (!$usuario) {
                return response()->json(['error' => 'Usuario no encontrado'], 404);
            }

            $cliCedula = $usuario->CLI_CEDULA ?? $usuario->cli_cedula ?? null;
            if (!$cliCedula) {
                return response()->json(['error' => 'Perfil incompleto (sin cliente asociado)'], 500);
            }

            // Por ahora no hay pedidos
            $tienePedidos = false;
            if ($tienePedidos) {
                return response()->json([
                    'success' => false,
                    'mensaje' => 'No puedes eliminar tu cuenta porque ya has realizado pedidos.',
                ]);
            }

            DB::connection('oracle')->beginTransaction();

            // Borrar primero usuario (hijo)
            UsuarioApp::where('USR_NOMBRE', $usuario->USR_NOMBRE ?? $usuario->usr_nombre)->delete();

            // Borrar luego cliente (padre)
            DB::connection('oracle')->table('CLIENTE')
                ->where('CLI_CEDULA', (string)$cliCedula)
                ->delete();

            DB::connection('oracle')->commit();

            session()->flush();

            return response()->json([
                'success' => true,
                'mensaje' => 'Tu cuenta ha sido eliminada exitosamente.',
            ]);
        } catch (\Throwable $e) {
            DB::connection('oracle')->rollBack();
            \Log::error('Error al eliminar cuenta: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'mensaje' => 'Error al eliminar la cuenta. Por favor, inténtalo de nuevo.',
            ], 500);
        }
    }
}
