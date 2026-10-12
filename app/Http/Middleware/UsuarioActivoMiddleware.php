<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Corta la sesión de quien la administradora desactivó. Es lo que permite
 * que el ayudante temporal deje de entrar sin borrar su registro ni su
 * historial de ítems.
 */
class UsuarioActivoMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && ! $user->estaActivo()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['ci' => 'Tu cuenta fue desactivada. Contacta a la administración.']);
        }

        return $next($request);
    }
}
