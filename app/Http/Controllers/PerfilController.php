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
                'mensaje' => 'Nombre actualizado correctamente',
                'nuevoNombre' => $usuario->nombre,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el nombre'
            ], 500);
        }
    }

    /**
     * Verificar si el usuario tiene pedidos
     */
    public function verificarPedidos()
    {
        if (!session()->has('usuario_id')) {
            return response()->json(['error' => 'No autenticado'], 401);
        }

        $usuario = UsuarioApp::find(session('usuario_id'));

        if (!$usuario) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        // TODO: Cuando implementes pedidos, descomentar
        // $tienePedidos = \App\Models\Pedido::where('cedula_cliente', $usuario->cedula_cliente)->exists();
        $tienePedidos = false; // Por ahora siempre false

        return response()->json([
            'success' => true,
            'tienePedidos' => $tienePedidos,
        ]);
    }

    /**
     * Eliminar cuenta del usuario
     */
    public function eliminarCuenta()
    {
        if (!session()->has('usuario_id')) {
            return response()->json(['error' => 'No autenticado'], 401);
        }

        $usuario = UsuarioApp::find(session('usuario_id'));

        if (!$usuario) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        // TODO: Cuando implementes pedidos, verificar
        // $tienePedidos = \App\Models\Pedido::where('cedula_cliente', $usuario->cedula_cliente)->exists();
        $tienePedidos = false;

        if ($tienePedidos) {
            return response()->json([
                'success' => false,
                'mensaje' => '😔 No puedes eliminar tu cuenta porque ya has realizado pedidos. Tus datos están asociados a transacciones.',
            ]);
        }

        try {
            // Obtener el cliente asociado
            $cliente = $usuario->cliente;

            // Eliminar usuario y cliente
            $usuario->delete();
            $cliente->delete();

            // Cerrar sesión
            session()->flush();

            return response()->json([
                'success' => true,
                'mensaje' => 'Tu cuenta ha sido eliminada exitosamente. ¡Gracias por habernos visitado!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'mensaje' => 'Error al eliminar la cuenta. Por favor, inténtalo de nuevo.',
            ], 500);
        }
    }
}
