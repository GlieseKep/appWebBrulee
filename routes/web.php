<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PerfilController;

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

});
