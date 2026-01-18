<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\UsuarioApp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Mostrar formulario de login
     */
    public function mostrarLogin()
    {
        return view('auth.login');
    }

    /**
     * Procesar login
     */
    public function login(Request $request)
    {
        // Validaciones
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|min:3',
            'correo' => [
                'required',
                'email',
                function ($attribute, $value, $fail) {
                    $dominiosPermitidos = ['gmail.com', 'outlook.com', 'hotmail.com', 'yahoo.com'];
                    $dominio = substr(strrchr($value, "@"), 1);

                    if (!in_array(strtolower($dominio), $dominiosPermitidos)) {
                        $fail("El dominio $dominio no está permitido. Usa gmail, outlook, hotmail o yahoo.");
                    }
                },
            ],
            'clave' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput($request->except('clave'));
        }

        // Buscar usuario por nombre
        $usuario = UsuarioApp::where('nombre', $request->nombre)->first();

        if (!$usuario) {
            return back()
                ->withErrors(['correo' => 'No existe una cuenta con esos datos. Regístrate primero.'])
                ->withInput($request->except('clave'));
        }

        // Verificar que el cliente asociado tenga ese correo
        if ($usuario->cliente->correo !== $request->correo) {
            return back()
                ->withErrors(['correo' => 'El correo no coincide con el usuario.'])
                ->withInput($request->except('clave'));
        }

        // Verificar que esté activo
        if (!$usuario->activo) {
            return back()
                ->withErrors(['general' => 'Tu cuenta está bloqueada. Contacta con el administrador.']);
        }

        // Verificar contraseña
        if (!$usuario->verificarContrasena($request->clave)) {
            return back()
                ->withErrors(['clave' => 'Contraseña incorrecta.'])
                ->withInput($request->except('clave'));
        }

        // Iniciar sesión
        session([
            'usuario_id' => $usuario->id,
            'usuario_nombre' => $usuario->nombre,
            'usuario_correo' => $usuario->cliente->correo
        ]);

        return redirect()->route('home')->with('success', 'Sesión iniciada correctamente');
    }

    /**
     * Mostrar formulario de registro
     */
    public function mostrarRegistro()
    {
        return view('auth.registro');
    }

    /**
     * Procesar registro
     */
    public function registro(Request $request)
    {
        // Validaciones
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|min:3|max:50|unique:usuarios_app,nombre',
            'correo' => [
                'required',
                'email',
                'unique:clientes,correo',
                function ($attribute, $value, $fail) {
                    $dominiosPermitidos = ['gmail.com', 'outlook.com', 'hotmail.com', 'yahoo.com'];
                    $dominio = substr(strrchr($value, "@"), 1);

                    if (!in_array(strtolower($dominio), $dominiosPermitidos)) {
                        $fail("El dominio $dominio no está permitido. Usa gmail, outlook, hotmail o yahoo.");
                    }
                },
            ],
            'clave' => 'required|string|min:6',
            'clave_confirmation' => 'required|same:clave',
        ], [
            'nombre.unique' => 'Ya existe un usuario con ese nombre.',
            'correo.unique' => 'Ya existe una cuenta con ese correo.',
            'clave_confirmation.same' => 'Las contraseñas no coinciden.',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput($request->except(['clave', 'clave_confirmation']));
        }

        try {
            DB::beginTransaction();

            // Crear cliente primero (generar cédula temporal)
            $cedula = 'CLI' . str_pad(Cliente::count() + 1, 10, '0', STR_PAD_LEFT);

            $cliente = Cliente::create([
                'cedula' => $cedula,
                'correo' => $request->correo,
                'telefono' => null
            ]);

            // Crear usuario_app
            $usuario = UsuarioApp::create([
                'nombre' => $request->nombre,
                'cedula_cliente' => $cliente->cedula,
                'contrasena' => $request->clave, // Se hasheará automáticamente
                'activo' => true
            ]);

            DB::commit();

            return redirect()->route('login')
                ->with('success', 'Registro exitoso. Ahora puedes iniciar sesión.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withErrors(['general' => 'Error al registrar usuario. Inténtalo de nuevo.'])
                ->withInput($request->except(['clave', 'clave_confirmation']));
        }
    }

    /**
     * Cerrar sesión
     */
    public function logout()
    {
        session()->flush();
        return redirect()->route('home')->with('success', 'Sesión cerrada correctamente');
    }
}
