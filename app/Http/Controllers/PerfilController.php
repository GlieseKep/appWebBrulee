<?php

namespace App\Http\Controllers;

use App\Models\UsuarioApp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PerfilController extends Controller
{
    /**
     * Mostrar datos del perfil (para AJAX)
     */
    public function obtenerPerfil()
    {
        $usuarioId = session('usuario_id');

        if (!$usuarioId) {
            return response()->json(['error' => 'No autenticado'], 401);
        }

        $usuario = UsuarioApp::with('cliente')->find($usuarioId);

        return response()->json([
            'nombre' => $usuario->nombre,
            'correo' => $usuario->cliente->correo
        ]);
    }

    /**
     * Actualizar nombre del usuario
     */
    public function actualizarNombre(Request $request)
    {
        $usuarioId = session('usuario_id');

        if (!$usuarioId) {
            return response()->json(['error' => 'No autenticado'], 401);
        }

        $usuario = UsuarioApp::find($usuarioId);

        // Validar
        $request->validate([
            'nombre' => [
                'required',
                'string',
                'min:3',
                'max:50',
                Rule::unique('usuarios_app', 'nombre')->ignore($usuario->id)
            ]
        ]);

        try {
            $usuario->nombre = $request->nombre;
            $usuario->save();

            // Actualizar sesión
            session(['usuario_nombre' => $request->nombre]);

            return response()->json([
                'success' => true,
                'message' => 'Nombre actualizado correctamente',
                'nombre' => $usuario->nombre
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el nombre'
            ], 500);
        }
    }
}
