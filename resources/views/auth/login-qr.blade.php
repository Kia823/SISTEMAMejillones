<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login QR - SISActivos</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif; }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background:
                radial-gradient(900px 520px at 12% 6%, rgba(37, 99, 235, .38), transparent 60%),
                radial-gradient(820px 520px at 88% 94%, rgba(220, 38, 38, .30), transparent 60%),
                linear-gradient(135deg, #0b1120 0%, #0f172a 45%, #111c33 100%);
            padding: 20px;
        }

        .card {
            position: relative;
            background: #ffffff;
            border-radius: 24px;
            padding: 44px 40px 36px;
            width: 100%;
            max-width: 480px;
            box-shadow:
                0 30px 60px -12px rgba(2, 6, 23, .55),
                0 0 0 1px rgba(255, 255, 255, .06);
            overflow: hidden;
            animation: rise .5s cubic-bezier(.2, .7, .3, 1) both;
        }

        /* Línea superior degradada azul → rojo */
        .card::before {
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
        .logo {
            width: 96px;
            height: 96px;
            margin: 0 auto 20px;
            padding: 3px;
            border-radius: 26px;
            background: linear-gradient(135deg, #2563eb, #dc2626);
            box-shadow: 0 14px 28px -10px rgba(37, 99, 235, .55);
        }

        .logo img {
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

        h1 {
            font-size: 25px;
            font-weight: 800;
            color: #0f172a;
            text-align: center;
            margin-bottom: 6px;
            letter-spacing: -.01em;
        }

        .subtitle {
            color: #64748b;
            text-align: center;
            font-size: 13px;
            margin-bottom: 24px;
        }

        /* Visor QR con marco degradado */
        .qr-frame {
            position: relative;
            padding: 3px;
            border-radius: 16px;
            background: linear-gradient(135deg, #2563eb, #7c3aed 55%, #dc2626);
            margin-bottom: 14px;
            box-shadow: 0 12px 26px -12px rgba(37, 99, 235, .55);
        }

        #qr-reader {
            width: 100%;
            border-radius: 13px;
            overflow: hidden;
            background: #0f172a;
            min-height: 280px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Quitar bordes por defecto de html5-qrcode */
        #qr-reader video,
        #qr-reader canvas { border-radius: 13px; }
        #qr-reader img[alt="Info icon"] { display: none !important; }

        .status {
            text-align: center;
            color: #64748b;
            font-size: 12.5px;
            font-weight: 600;
            padding: 10px 12px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            transition: all .2s ease;
        }

        .status.is-scanning { color: #1d4ed8; background: #eff6ff; border-color: #bfdbfe; }
        .status.is-success  { color: #047857; background: #ecfdf5; border-color: #a7f3d0; }
        .status.is-error    { color: #b91c1c; background: #fef2f2; border-color: #fecaca; }

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

        .btn {
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
            font-size: 14px;
            text-decoration: none;
            transition: all .2s ease;
        }

        .btn:hover {
            background: #eef4ff;
            border-color: #bfdbfe;
            color: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 10px 18px -12px rgba(37, 99, 235, .6);
        }

        .error-msg {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-left: 4px solid #dc2626;
            color: #991b1b;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 12.5px;
            font-weight: 600;
            margin-bottom: 18px;
            text-align: center;
        }

        .hint {
            text-align: center;
            font-size: 11px;
            color: #94a3b8;
            margin-top: 16px;
        }

        @media (max-width: 440px) {
            .card { padding: 34px 24px 28px; border-radius: 20px; }
            #qr-reader { min-height: 220px; }
        }

        @media (prefers-reduced-motion: reduce) {
            .card { animation: none; }
            * { transition: none !important; }
        }
    </style>
</head>
<body>

    <div class="card">
        <div class="logo">
            <img src="{{ asset('images/Emblema.png') }}"
                 alt="Emblema Tecnológico Puerto de Mejillones">
        </div>

        <p class="brand">SISActivos</p>
        <h1>Acceso por QR</h1>
        <p class="subtitle">Apunta la cámara a tu credencial institucional</p>

        @if($errors->any())
            <div class="error-msg">
                ⚠️ {{ $errors->first() }}
            </div>
        @endif

        <div class="qr-frame">
            <div id="qr-reader"></div>
        </div>

        <p class="status" id="scanner-status">Iniciando cámara...</p>

        <div class="divider">
            <div class="divider-line"></div>
            <span class="divider-text">O</span>
            <div class="divider-line"></div>
        </div>

        <a href="{{ route('login') }}" class="btn">← Volver al login tradicional</a>

        <div class="hint">
            Usa tu credencial impresa para ingresar rápidamente
        </div>
    </div>

    {{-- ⭐ FORMULARIO OCULTO PARA ENVIAR EL QR --}}
    <form id="qr-login-form" method="POST" action="{{ route('login.qr.post') }}" style="display: none;">
        @csrf
        <input type="hidden" name="qr_data" id="qr-data-input">
    </form>

    {{-- ⭐ AUDIOS --}}
    <audio id="audio-bienvenido" src="{{ asset('audios/bienvenido.mp3') }}" preload="auto"></audio>
    <audio id="audio-acceso-denegado" src="{{ asset('audios/acceso_denegado.mp3') }}" preload="auto"></audio>

    <script>
        let html5QrCode = null;
        let isProcessing = false;

        document.addEventListener('DOMContentLoaded', function () {
            const statusEl = document.getElementById('scanner-status');

            const setStatus = (text, state = '') => {
                statusEl.textContent = text;
                statusEl.classList.remove('is-scanning', 'is-success', 'is-error');
                if (state) statusEl.classList.add('is-' + state);
            };

            // Precargar audios
            ['audio-bienvenido', 'audio-acceso-denegado'].forEach(id => {
                const audio = document.getElementById(id);
                if (audio) {
                    audio.play().then(() => { audio.pause(); audio.currentTime = 0; }).catch(() => {});
                }
            });

            // ⭐ Si hay error de login QR, reproducir audio de acceso denegado
            @if(session('login_error') || $errors->any())
                setTimeout(() => {
                    const audio = document.getElementById('audio-acceso-denegado');
                    if (audio) {
                        audio.volume = 0.7;
                        audio.play().catch(e => console.log('Audio bloqueado:', e));
                    }
                }, 300);
            @endif

            // Iniciar escáner
            html5QrCode = new Html5Qrcode("qr-reader");

            html5QrCode.start(
                { facingMode: "environment" },
                { fps: 10, qrbox: { width: 250, height: 250 } },
                (decodedText) => {
                    if (isProcessing) return;
                    isProcessing = true;

                    setStatus('✓ Código detectado. Verificando...', 'success');

                    // Detener cámara
                    html5QrCode.stop().then(() => {
                        document.getElementById('qr-data-input').value = decodedText;
                        document.getElementById('qr-login-form').submit();
                    }).catch(err => {
                        console.error('Error al detener cámara:', err);
                        document.getElementById('qr-data-input').value = decodedText;
                        document.getElementById('qr-login-form').submit();
                    });
                },
                (errorMessage) => { /* ignorar errores de frames */ }
            ).then(() => {
                setStatus('Apunta la cámara al código QR', 'scanning');
            }).catch(err => {
                console.error('Error cámara:', err);
                setStatus('⚠️ No se pudo acceder a la cámara', 'error');
            });
        });
    </script>
</body>
</html>