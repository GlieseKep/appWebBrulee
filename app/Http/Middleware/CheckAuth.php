<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('usuario_id')) {
            return redirect()->route('login')
                ->withErrors(['auth' => 'Debes iniciar sesión para acceder.']);
        }

        return $next($request);
    }
}
