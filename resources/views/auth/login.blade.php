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
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #2563eb 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .login-box {
            background: white;
            width: 100%;
            max-width: 420px;
            padding: 40px;
            border-radius: 22px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.3);
        }
        .login-logo {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #2563eb, #7c3aed);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 32px;
            color: white;
            box-shadow: 0 8px 16px rgba(37,99,235,.35);
        }
        h2 {
            font-size: 24px;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 8px;
            text-align: center;
        }
        .subtitle {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 24px;
            text-align: center;
        }
        .form-group { margin-bottom: 18px; }
        label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 6px;
            color: #334155;
        }
        input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            font-size: 14px;
            outline: none;
            transition: all .2s ease;
        }
        input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37,99,235,.12);
        }
        .btn-login {
            width: 100%;
            background-color: #2563eb;
            color: white;
            border: none;
            padding: 13px;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all .2s ease;
        }
        .btn-login:hover {
            background-color: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 8px 16px rgba(37,99,235,.3);
        }
        .error-box {
            background: #fee2e2;
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 12px;
            margin-bottom: 18px;
            font-weight: 600;
        }
        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 22px 0 16px 0;
        }
        .divider-line {
            flex: 1;
            height: 1px;
            background: #e2e8f0;
        }
        .divider-text {
            font-size: 12px;
            color: #94a3b8;
            font-weight: 700;
            text-transform: uppercase;
        }
        .btn-qr {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 13px 14px;
            background: #0f766e;
            color: white;
            border-radius: 10px;
            font-weight: 700;
            text-decoration: none;
            font-size: 14px;
            transition: all .2s ease;
        }
        .btn-qr:hover {
            background: #115e59;
            transform: translateY(-1px);
            box-shadow: 0 8px 16px rgba(15,118,110,.3);
        }
        .hint {
            text-align: center;
            font-size: 11px;
            color: #94a3b8;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="login-logo">🔐</div>
        <h2>Ingreso al Sistema</h2>
        <p class="subtitle">Ingresa tu Carnet de Identidad y contraseña</p>

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
                <input type="password" name="password" required autocomplete="current-password">
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
        });
    </script>
</body>
</html>
