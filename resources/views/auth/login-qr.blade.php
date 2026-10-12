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
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
            padding: 20px;
        }
        .card {
            background: white;
            border-radius: 24px;
            padding: 40px;
            width: 100%;
            max-width: 480px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        .logo {
            width: 64px;
            height: 64px;
            background: #2563eb;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            margin: 0 auto 20px;
        }
        h1 { font-size: 24px; font-weight: 800; color: #0f172a; text-align: center; margin-bottom: 8px; }
        p { color: #64748b; text-align: center; font-size: 14px; margin-bottom: 24px; }
        #qr-reader { width: 100%; border-radius: 12px; overflow: hidden; margin-bottom: 20px; }
        .btn {
            display: block;
            width: 100%;
            padding: 14px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
            text-decoration: none;
        }
        .btn:hover { background: #1d4ed8; }
        .btn-secondary {
            background: #f1f5f9;
            color: #475569;
            margin-top: 10px;
        }
        .btn-secondary:hover { background: #e2e8f0; }
        .status {
            text-align: center;
            color: #64748b;
            font-size: 13px;
            margin-top: 12px;
        }
        .error-msg {
            background: #fee2e2;
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: 12px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="card">
        <div class="logo">📱</div>
        <h1>Acceso por QR</h1>
        <p>Apunta la cámara a tu credencial institucional</p>

        @if($errors->any())
            <div class="error-msg">
                @foreach($errors->all() as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif

        <div id="qr-reader"></div>
        <p class="status" id="scanner-status">Iniciando cámara...</p>

        <div style="margin-top: 20px;">
            <a href="{{ route('login') }}" class="btn btn-secondary">← Volver al login tradicional</a>
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

                    // Mostrar feedback
                    document.getElementById('scanner-status').textContent = '✓ Código detectado. Verificando...';
                    document.getElementById('scanner-status').style.color = '#10b981';

                    // Detener cámara
                    html5QrCode.stop().then(() => {
                        // Enviar el formulario
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
                document.getElementById('scanner-status').textContent = 'Apunta la cámara al código QR';
            }).catch(err => {
                console.error('Error cámara:', err);
                document.getElementById('scanner-status').textContent = '⚠️ No se pudo acceder a la cámara';
                document.getElementById('scanner-status').style.color = '#dc2626';
            });
        });
    </script>
</body>
</html>
