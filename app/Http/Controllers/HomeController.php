<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Mostrar página de inicio
     */
    public function index()
    {
        return view('index');
    }

    /**
     * Mostrar página de contacto
     */
    public function contacto()
    {
        return view('contacto');
    }

    /**
     * Mostrar página de accesibilidad
     */
    public function accesibilidad()
    {
        return view('accesibilidad');
    }
}
