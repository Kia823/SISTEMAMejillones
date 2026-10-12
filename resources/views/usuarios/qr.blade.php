<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Credencial — {{ $usuario->nombre_completo }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', Arial, sans-serif; }

        html, body {
            background: #e2e8f0;
            min-height: 100vh;
            padding: 30px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 30px;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .credencial-wrapper {
            position: relative;
            width: 340px;
            height: 520px;
            box-shadow: 0 15px 30px rgba(0,0,0,.15);
            border-radius: 20px;
            overflow: hidden;
            background: white;
            border: 1px solid #cbd5e1;
            display: flex;
            flex-direction: column;
        }

        /* ============================================
           FRENTE (Vertical idéntico a la credencial física)
           ============================================ */
        .frente-header {
            background: #0f172a;
            padding: 14px 14px 10px;
            text-align: center;
            border-bottom: 3px solid #dc2626;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
        }

        .frente-logo-img {
            max-width: 140px;
            height: 38px;
            object-fit: contain;
        }

        .frente-sub-text {
            font-size: 12px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            line-height: 1.2;
        }

        .frente-body {
            flex: 1;
            background: #dc2626;
            padding: 16px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            text-align: center;
        }

        .frente-qr-box {
            width: 170px;
            height: 205px;
            background: white;
            border-radius: 12px;
            padding: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 4px;
        }

        .frente-qr-box svg,
        .frente-qr-box img {
            width: 135px;
            height: 135px;
            display: block;
        }

        .frente-qr-label {
            font-size: 8px;
            font-weight: 800;
            color: #1e3a8a;
            letter-spacing: 1px;
            margin-top: 2px;
        }

        .frente-datos {
            width: 100%;
            color: white;
        }

        .frente-nombre {
            font-size: 14px;
            font-weight: 800;
            text-transform: uppercase;
            line-height: 1.2;
            margin-bottom: 6px;
            text-shadow: 0 1px 2px rgba(0,0,0,0.3);
        }

        .frente-ci {
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.5px;
            opacity: 0.95;
        }

        .frente-footer {
            background: #b91c1c;
            padding: 12px;
            text-align: center;
            border-top: 1px solid rgba(255,255,255,0.2);
        }

        .frente-cargo {
            font-size: 12px;
            font-weight: 800;
            color: #ffffff;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 3px;
        }

        .frente-gestion {
            font-size: 10px;
            font-weight: 700;
            color: #fef08a;
            letter-spacing: 0.5px;
        }

        /* ============================================
           REVERSO (Vertical idéntico a la credencial física)
           ============================================ */
        .reverso {
            width: 100%;
            height: 100%;
            background: white;
            display: flex;
            flex-direction: column;
            padding: 16px;
            justify-content: space-between;
        }

        .reverso-header {
            text-align: center;
            border-bottom: 2px solid #dc2626;
            padding-bottom: 10px;
        }

        .reverso-institucion {
            font-size: 10.5px;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .reverso-lema {
            font-size: 9px;
            font-weight: 800;
            color: #dc2626;
            text-transform: uppercase;
            margin-top: 2px;
        }

        .reverso-sub {
            font-size: 7.5px;
            color: #64748b;
            font-weight: 600;
            margin-top: 2px;
        }

        .reverso-texto {
            font-size: 9px;
            color: #1e293b;
            line-height: 1.45;
            text-align: justify;
            text-transform: uppercase;
            font-weight: 600;
            padding: 0 4px;
        }

        .reverso-firmas {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 10px;
            padding: 0 4px;
        }

        .firma-box {
            flex: 1;
            text-align: center;
            font-size: 8px;
            color: #0f172a;
        }

        .firma-box .linea {
            border-top: 1px solid #dc2626;
            margin: 20px 4px 4px;
        }

        .firma-box .nombre {
            font-weight: 800;
            font-size: 8px;
            text-transform: uppercase;
            line-height: 1.1;
        }

        .firma-box .cargo {
            font-size: 7.5px;
            color: #475569;
            font-weight: 600;
            margin-top: 1px;
            text-transform: uppercase;
        }

        .sello-central {
            width: 42px;
            height: 42px;
            border: 1.5px dashed #94a3b8;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 6px;
            color: #94a3b8;
            font-weight: 700;
            text-align: center;
            flex-shrink: 0;
        }

        .reverso-footer {
            background: #dc2626;
            color: white;
            padding: 8px 10px;
            font-size: 7.5px;
            font-weight: 700;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 6px;
        }

        /* ============================================
           IMPRESIÓN
           ============================================ */
        @media print {
            @page {
                size: A4 portrait;
                margin: 10mm;
            }

            html, body {
                background: white !important;
                display: block !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .btn-imprimir, .etiqueta { display: none !important; }

            .credencial-wrapper {
                width: 54mm !important;
                height: 85.5mm !important;
                margin: 0 auto 10mm !important;
                box-shadow: none !important;
                border-radius: 4mm !important;
                page-break-inside: avoid;
            }
        }

        .btn-imprimir {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #2563eb;
            color: white;
            border: none;
            padding: 12px 22px;
            border-radius: 10px;
            font-weight: 700;
            cursor: pointer;
            font-size: 14px;
            box-shadow: 0 8px 16px rgba(37,99,235,0.3);
            z-index: 100;
        }
        .btn-imprimir:hover { background: #1d4ed8; }

        .etiqueta {
            position: absolute;
            top: -22px;
            left: 0;
            background: #1e293b;
            color: white;
            font-size: 10px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 4px;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

    {{-- ⭐ FRENTE VERTICAL CON LOGOTIPO Y QR --}}
    <div style="position: relative;">
        <span class="etiqueta">Frente</span>
        <div class="credencial-wrapper">
            <div class="frente-header">
                <img src="{{ asset('images/logo.png') }}" alt="Logo ITPM" class="frente-logo-img">
                <div class="frente-sub-text">INSTITUTO TECNOLÓGICO<br>"PUERTO DE MEJILLONES"</div>
            </div>

            <div class="frente-body">
                <div class="frente-qr-box">
                    {!! $qrImage !!}
                    <span class="frente-qr-label">VERIFICADO</span>
                </div>

                <div class="frente-datos">
                    <div class="frente-nombre">{{ $usuario->nombre_completo }}</div>
                    <div class="frente-ci">C.I. {{ $usuario->ci }}</div>
                </div>
            </div>

            <div class="frente-footer">
                <div class="frente-cargo">{{ $usuario->cargo ?? $usuario->rol }}</div>
                <div class="frente-gestion">Gestión 2026</div>
            </div>
        </div>
    </div>

    {{-- ⭐ REVERSO VERTICAL --}}
    <div style="position: relative;">
        <span class="etiqueta">Reverso</span>
        <div class="credencial-wrapper">
            <div class="reverso">
                <div class="reverso-header">
                    <div class="reverso-institucion">INSTITUTO TECNOLÓGICO</div>
                    <div class="reverso-lema">PUERTO DE MEJILLONES</div>
                    <div class="reverso-sub">ORGANIZACIÓN AL SERVICIO DE LA HUMANIDAD</div>
                </div>

                <div class="reverso-texto">
                    El titular de esta credencial es <strong>{{ strtolower($usuario->cargo ?? 'usuario') }}</strong> del Instituto Tecnológico Puerto de Mejillones. Agradecemos a todas las autoridades gubernamentales, judiciales, policiales y otros, prestar la colaboración requerida en caso necesario.
                </div>

                <div class="reverso-firmas">
                    <div class="firma-box">
                        <div class="linea"></div>
                        <div class="nombre">Lic. Jimmy Ovidio Sirpa Choque</div>
                        <div class="cargo">Rector</div>
                    </div>

                    <div class="sello-central">
                        SELLO<br>OFICIAL
                    </div>

                    <div class="firma-box">
                        <div class="linea"></div>
                        <div class="nombre">Ing. Ana María Alvarez Tellez</div>
                        <div class="cargo">Directora Académica</div>
                    </div>
                </div>

                <div class="reverso-footer">
                    <span>📞 22810641 - 22816390</span>
                    <span>📍 Ciudad Satélite Plan 405</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Botón imprimir --}}
    <button class="btn-imprimir" onclick="window.print()">
        🖨️ Imprimir Credencial
    </button>

</body>
</html>
