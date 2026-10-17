<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Acceso al Sistema - SISActivos</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif; }

        body {
            background:
                radial-gradient(900px 520px at 12% 6%, rgba(37, 99, 235, .38), transparent 60%),
                radial-gradient(820px 520px at 88% 94%, rgba(220, 38, 38, .30), transparent 60%),
                linear-gradient(135deg, #0b1120 0%, #0f172a 45%, #111c33 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .login-box {
            position: relative;
            background: #ffffff;
            width: 100%;
            max-width: 430px;
            padding: 44px 40px 36px;
            border-radius: 24px;
            box-shadow:
                0 30px 60px -12px rgba(2, 6, 23, .55),
                0 0 0 1px rgba(255, 255, 255, .06);
            overflow: hidden;
            animation: rise .5s cubic-bezier(.2, .7, .3, 1) both;
        }

        /* Línea superior degradada azul → rojo */
        .login-box::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            background: linear-gradient(90deg, #2563eb, #7c3aed 55%, #dc2626);
        }

        @keyframes rise {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Logo con anillo degradado */
        .login-logo {
            width: 96px;
            height: 96px;
            margin: 0 auto 20px;
            padding: 3px;
            border-radius: 26px;
            background: linear-gradient(135deg, #2563eb, #dc2626);
            box-shadow: 0 14px 28px -10px rgba(37, 99, 235, .55);
        }

        .login-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            background: #ffffff;
            border-radius: 23px;
            padding: 10px;
            display: block;
        }

        .brand {
            text-align: center;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .18em;
            text-transform: uppercase;
            color: #2563eb;
            margin-bottom: 6px;
        }

        h2 {
            font-size: 25px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 6px;
            text-align: center;
            letter-spacing: -.01em;
        }

        .subtitle {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 26px;
            text-align: center;
        }

        .form-group { margin-bottom: 18px; }

        label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
            margin-bottom: 7px;
            color: #475569;
        }

        .input-wrap { position: relative; }

        input {
            width: 100%;
            padding: 13px 14px;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            font-size: 14px;
            color: #0f172a;
            background: #f8fafc;
            outline: none;
            transition: all .2s ease;
        }

        input:focus {
            border-color: #2563eb;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, .12);
        }

        /* Botón mostrar/ocultar contraseña */
        .input-wrap input[type="password"],
        .input-wrap input.with-toggle { padding-right: 46px; }

        .toggle-pass {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            cursor: pointer;
            color: #94a3b8;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 6px;
            border-radius: 8px;
            transition: color .15s ease, background .15s ease;
        }

        .toggle-pass:hover { color: #2563eb; background: #eff6ff; }
        .toggle-pass .icon { display: none; }
        .toggle-pass .icon.active { display: block; }

        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, #1d4ed8, #2563eb 45%, #dc2626);
            color: #ffffff;
            border: none;
            padding: 14px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            letter-spacing: .01em;
            transition: all .2s ease;
            box-shadow: 0 12px 22px -10px rgba(37, 99, 235, .7);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            filter: brightness(1.06);
            box-shadow: 0 18px 28px -12px rgba(37, 99, 235, .75);
        }

        .btn-login:disabled {
            cursor: progress;
            filter: saturate(.7);
            transform: none;
        }

        .spinner {
            width: 14px;
            height: 14px;
            border: 2px solid rgba(255, 255, 255, .4);
            border-top-color: #ffffff;
            border-radius: 50%;
            display: inline-block;
            vertical-align: -2px;
            margin-right: 8px;
            animation: spin .7s linear infinite;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        .error-box {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-left: 4px solid #dc2626;
            color: #991b1b;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 12.5px;
            margin-bottom: 18px;
            font-weight: 600;
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 22px 0 16px 0;
        }

        .divider-line { flex: 1; height: 1px; background: #e2e8f0; }

        .divider-text {
            font-size: 11px;
            color: #94a3b8;
            font-weight: 800;
            letter-spacing: .1em;
            text-transform: uppercase;
        }

        .btn-qr {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            width: 100%;
            padding: 12px 14px;
            background: #f8fafc;
            color: #1e3a8a;
            border: 1.5px solid #dbe3f0;
            border-radius: 12px;
            font-weight: 700;
            text-decoration: none;
            font-size: 14px;
            transition: all .2s ease;
        }

        .btn-qr:hover {
            background: #eef4ff;
            border-color: #bfdbfe;
            color: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 10px 18px -12px rgba(37, 99, 235, .6);
        }

        .hint {
            text-align: center;
            font-size: 11px;
            color: #94a3b8;
            margin-top: 20px;
        }

        @media (max-width: 440px) {
            .login-box { padding: 34px 24px 28px; border-radius: 20px; }
        }

        @media (prefers-reduced-motion: reduce) {
            .login-box { animation: none; }
            * { transition: none !important; }
        }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="login-logo">
            <img src="{{ asset('images/Emblema.png') }}"
                 alt="Emblema Tecnológico Puerto de Mejillones">
        </div>

        <p class="brand">SISActivos</p>
        <h2>Bienvenido de nuevo</h2>
        <p class="subtitle">Ingresa con tu Carnet de Identidad y contraseña</p>

        @if($errors->any())
            <div class="error-box">
                ⚠️ {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" id="login-form">
            @csrf
            <div class="form-group">
                <label>Carnet de Identidad (C.I.)</label>
                <input type="text" name="ci" value="{{ old('ci') }}" required autofocus autocomplete="username">
            </div>

            <div class="form-group">
                <label>Contraseña</label>
                <div class="input-wrap">
                    <input type="password" name="password" id="password" class="with-toggle"
                           required autocomplete="current-password">
                    <button type="button" class="toggle-pass" id="toggle-pass" aria-label="Mostrar contraseña">
                        <svg class="icon active" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                            <line x1="1" y1="1" x2="23" y2="23"/>
                        </svg>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-login">Iniciar Sesión</button>
        </form>

        <div class="divider">
            <div class="divider-line"></div>
            <span class="divider-text">O</span>
            <div class="divider-line"></div>
        </div>

        <a href="{{ route('login.qr') }}" class="btn-qr">
            🔲 Escanear credencial QR
        </a>

        <div class="hint">
            Usa tu credencial impresa para ingresar rápidamente
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- AUDIOS PARA ACCESO DENEGADO --}}
    {{-- ============================================================ --}}
    <audio id="audio-acceso-denegado" src="{{ asset('audios/acceso_denegado.mp3') }}" preload="auto"></audio>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // ⭐ Precargar el audio (desbloquea el contexto en algunos navegadores)
            const audioDenegado = document.getElementById('audio-acceso-denegado');
            if (audioDenegado) {
                audioDenegado.volume = 0;
                audioDenegado.play().then(() => {
                    audioDenegado.pause();
                    audioDenegado.currentTime = 0;
                    audioDenegado.volume = 0.7;
                }).catch(() => {
                    audioDenegado.volume = 0.7;
                });
            }

            // ⭐ Si hay error de login, reproducir audio
            @if(session('login_error') || $errors->any())
                setTimeout(() => {
                    const audio = document.getElementById('audio-acceso-denegado');
                    if (audio) {
                        audio.volume = 0.7;
                        audio.play().catch(e => console.log('🔇 Audio bloqueado:', e.message));
                    }
                }, 300);
            @endif

            // 👁️ Mostrar / ocultar contraseña
            const toggleBtn = document.getElementById('toggle-pass');
            const passInput = document.getElementById('password');
            const icons = toggleBtn ? toggleBtn.querySelectorAll('.icon') : [];

            if (toggleBtn && passInput) {
                toggleBtn.addEventListener('click', () => {
                    const show = passInput.type === 'password';
                    passInput.type = show ? 'text' : 'password';
                    icons[0].classList.toggle('active', !show); // ojo abierto
                    icons[1].classList.toggle('active', show);  // ojo tachado
                    toggleBtn.setAttribute('aria-label', show ? 'Ocultar contraseña' : 'Mostrar contraseña');
                    passInput.focus();
                });
            }

            // ⏳ Estado de carga al enviar el formulario
            const form = document.getElementById('login-form');
            if (form) {
                form.addEventListener('submit', () => {
                    const btn = form.querySelector('.btn-login');
                    if (btn && !btn.disabled) {
                        btn.disabled = true;
                        btn.innerHTML = '<span class="spinner"></span> Verificando...';
                    }
                });
            }
        });
    </script>
</body>
</html>