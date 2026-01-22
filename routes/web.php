<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CarritoController;

/*
|--------------------------------------------------------------------------
| Rutas Públicas
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/contacto', [HomeController::class, 'contacto'])->name('contacto');
Route::get('/accesibilidad', [HomeController::class, 'accesibilidad'])->name('accesibilidad');

/*
|--------------------------------------------------------------------------
| Productos
|--------------------------------------------------------------------------
*/
Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
Route::get('/productos/{id}', [ProductoController::class, 'show'])->name('productos.show');
Route::get('/categoria/{id}', [ProductoController::class, 'porCategoria'])->name('productos.categoria');

/*
|--------------------------------------------------------------------------
| Auth
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'mostrarLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/registro', [AuthController::class, 'mostrarRegistro'])->name('registro');
Route::post('/registro', [AuthController::class, 'registro'])->name('registro.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Carrito (NO middleware: devuelve JSON 401 si no hay login)
|--------------------------------------------------------------------------
*/
Route::get('/carrito/items', [CarritoController::class, 'items'])->name('carrito.items');
Route::post('/carrito/agregar', [CarritoController::class, 'agregar'])->name('carrito.agregar');
Route::post('/carrito/eliminar', [CarritoController::class, 'eliminar'])->name('carrito.eliminar');
Route::post('/carrito/vaciar', [CarritoController::class, 'vaciar'])->name('carrito.vaciar');

/*
|--------------------------------------------------------------------------
| Rutas protegidas
|--------------------------------------------------------------------------
*/
Route::middleware(['auth.custom'])->group(function () {

    Route::get('/perfil/datos', [PerfilController::class, 'obtenerPerfil'])->name('perfil.datos');
    Route::post('/perfil/actualizar-nombre', [PerfilController::class, 'actualizarNombre'])->name('perfil.actualizar.nombre');
    Route::get('/perfil/verificar-pedidos', [PerfilController::class, 'verificarPedidos'])->name('perfil.verificar.pedidos');
    Route::delete('/perfil/eliminar-cuenta', [PerfilController::class, 'eliminarCuenta'])->name('perfil.eliminar.cuenta');

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/procesar', [CheckoutController::class, 'procesar'])->name('checkout.procesar');
});
