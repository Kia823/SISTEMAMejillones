<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PuedeVerificarMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! $user->puedeVerificar()) {
            abort(403, 'No tiene permisos para verificar. Solicite acceso al administrador.');
        }

        return $next($request);
    }
}
