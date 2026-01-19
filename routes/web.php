<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CheckoutController;

/*
|--------------------------------------------------------------------------
| Rutas Públicas
|--------------------------------------------------------------------------
*/

// Página principal
Route::get('/', [HomeController::class, 'index'])->name('home');

// Contacto
Route::get('/contacto', [HomeController::class, 'contacto'])->name('contacto');

// Accesibilidad
Route::get('/accesibilidad', [HomeController::class, 'accesibilidad'])->name('accesibilidad');

/*
|--------------------------------------------------------------------------
| Rutas de Productos
|--------------------------------------------------------------------------
*/

// Catálogo completo
Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');

// Detalle de producto
Route::get('/productos/{id}', [ProductoController::class, 'show'])->name('productos.show');

// Productos por categoría
Route::get('/categoria/{id}', [ProductoController::class, 'porCategoria'])->name('productos.categoria');

/*
|--------------------------------------------------------------------------
| Rutas de Autenticación
|--------------------------------------------------------------------------
*/

// Mostrar formulario de login
Route::get('/login', [AuthController::class, 'mostrarLogin'])->name('login');

// Procesar login
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// Mostrar formulario de registro
Route::get('/registro', [AuthController::class, 'mostrarRegistro'])->name('registro');

// Procesar registro
Route::post('/registro', [AuthController::class, 'registro'])->name('registro.post');

// Cerrar sesión
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Rutas Protegidas (Requieren Autenticación)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth.custom'])->group(function () {

    // Obtener datos del perfil (AJAX)
    Route::get('/perfil/datos', [PerfilController::class, 'obtenerPerfil'])->name('perfil.datos');

    // Actualizar nombre
    Route::post('/perfil/actualizar-nombre', [PerfilController::class, 'actualizarNombre'])->name('perfil.actualizar.nombre');

    // Verificar si tiene pedidos
    Route::get('/perfil/verificar-pedidos', [PerfilController::class, 'verificarPedidos'])->name('perfil.verificar.pedidos');

    // Eliminar cuenta
    Route::delete('/perfil/eliminar-cuenta', [PerfilController::class, 'eliminarCuenta'])->name('perfil.eliminar.cuenta');

    /*
    |--------------------------------------------------------------------------
    | Rutas de Checkout (Protegidas)
    |--------------------------------------------------------------------------
    */
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/procesar', [CheckoutController::class, 'procesar'])->name('checkout.procesar');

});
