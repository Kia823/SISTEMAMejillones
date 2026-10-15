<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class LoginController extends Controller
{
    // ============================================================
    // MOSTRAR FORMULARIO DE LOGIN
    // ============================================================
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // ============================================================
    // LOGIN CON CI + CONTRASEÑA
    // ============================================================
    public function login(Request $request)
    {
        // 1. VALIDAR ENTRADA
        $credentials = $request->validate([
            'ci'       => 'required|string|max:20',
            'password' => 'required|string',
        ], [
            'ci.required'       => 'El Carnet de Identidad es obligatorio.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        // 2. RATE LIMITING
        $key = 'login:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            return back()
                ->withErrors([
                    'ci' => "Demasiados intentos fallidos. Intenta de nuevo en {$seconds} segundos.",
                ])
                ->with('login_error', true)
                ->onlyInput('ci');
        }

        // 3. BUSCAR USUARIO
        $user = User::where('ci', $credentials['ci'])
                    ->where('estado', 'ACTIVO')
                    ->first();

        // 4. VERIFICAR CONTRASEÑA
        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            RateLimiter::hit($key, 60);

            return back()
                ->withErrors(['ci' => 'El Carnet de Identidad o la contraseña son incorrectos.'])
                ->with('login_error', true)
                ->onlyInput('ci');
        }

        // ==========================================
        // 5. LOGIN EXITOSO ⭐
        // ==========================================
        RateLimiter::clear($key);

        Auth::login($user, $request->boolean('remember'));

        // ⭐ CRÍTICO: PRIMERO put, DESPUÉS regenerate
        session()->put('login_success', true);
        session()->put('login_user_name', $user->nombre_completo);

        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    // ============================================================
    // LOGOUT
    // ============================================================
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    // ============================================================
    // MOSTRAR ESCÁNER QR
    // ============================================================
    public function showQrLogin()
    {
        return view('auth.login-qr');
    }

    // ============================================================
    // LOGIN POR QR
    // ============================================================
    public function loginQr(Request $request)
    {
        $request->validate([
            'qr_data' => 'required|string',
        ], [
            'qr_data.required' => 'No se recibió ningún código QR.',
        ]);

        $contenido = trim($request->qr_data);
        $ci = $this->extraerCiDeQr($contenido);

        $user = User::where('ci', $ci)
                    ->where('estado', 'ACTIVO')
                    ->first();

        if (! $user) {
            return back()
                ->withErrors(['qr_data' => 'Credencial no válida o usuario inactivo.'])
                ->with('login_error', true);
        }

        Auth::login($user, true);

        // ⭐ MISMO patrón: put primero, regenerate después
        session()->put('login_success', true);
        session()->put('login_user_name', $user->nombre_completo);

        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    // ============================================================
    // LIMPIAR FLAG DE BIENVENIDA (AJAX)
    // ============================================================
    public function clearWelcome()
    {
        session()->forget(['login_success', 'login_user_name']);
        return response()->json(['ok' => true]);
    }

    // ============================================================
    // HELPER: Extraer CI desde el contenido del QR
    // ============================================================
    protected function extraerCiDeQr(string $contenido): ?string
    {
        if (preg_match('/C\.?\s*I\.?\s*:?\s*([0-9\-]+)/i', $contenido, $m)) {
            return trim($m[1]);
        }

        if (preg_match('/[?&]ci=([0-9\-]+)/i', $contenido, $m)) {
            return trim($m[1]);
        }

        $json = json_decode($contenido, true);
        if (is_array($json) && isset($json['ci'])) {
            return trim((string) $json['ci']);
        }

        if (preg_match('/^([0-9\-]+)$/', $contenido, $m)) {
            return trim($m[1]);
        }

        if (preg_match('/([0-9]{6,15})/', $contenido, $m)) {
            return trim($m[1]);
        }

        return null;
    }
}
