<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Etiqueta — {{ $activo->codigo_activo }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', Arial, sans-serif; }

        html, body {
            background: #e2e8f0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .etiqueta-wrapper {
            display: flex;
            align-items: stretch;
            background: white;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,.15);
            border: 1px solid #cbd5e1;
            width: 700px;
            max-width: 100%;
        }

        .etiqueta-info {
            flex: 1;
            padding: 14px 18px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        /* Encabezado con logo y título compacto */
        .etiqueta-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-bottom: 8px;
            border-bottom: 1.5px solid #0f172a;
            margin-bottom: 10px;
        }

        .etiqueta-logo {
            width: 36px;
            height: 36px;
            object-fit: contain;
            flex-shrink: 0;
        }

        .etiqueta-institucion {
            flex: 1;
            font-size: 12px;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Cuerpo de filas compacto */
        .etiqueta-body {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .etiqueta-row {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            line-height: 1.2;
        }

        .etiqueta-row .label {
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
            min-width: 75px;
        }

        .etiqueta-row .value {
            font-weight: 700;
            color: #1e293b;
            font-size: 13px;
        }

        .etiqueta-codigo {
            font-size: 15px !important;
            font-weight: 800 !important;
            color: #dc2626 !important;
        }

        .etiqueta-nombre {
            font-size: 14px !important;
            font-weight: 800 !important;
            color: #0f172a !important;
            text-transform: uppercase;
        }

        .etiqueta-fuente {
            display: inline-block;
            background: #f1f5f9;
            color: #334155;
            font-size: 11px;
            font-weight: 800;
            padding: 2px 8px;
            border-radius: 4px;
            border: 1px solid #cbd5e1;
            text-transform: uppercase;
        }

        /* Sección QR derecha */
        .etiqueta-qr {
            width: 160px;
            background: #f8fafc;
            border-left: 2px dashed #cbd5e1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 12px;
            flex-shrink: 0;
        }

        .etiqueta-qr svg,
        .etiqueta-qr img {
            width: 125px;
            height: 125px;
            display: block;
            background: white;
            padding: 4px;
            border-radius: 4px;
            border: 1px solid #e2e8f0;
        }

        .etiqueta-qr-label {
            margin-top: 6px;
            font-size: 8.5px;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: center;
        }

        /* Botones flotantes de acción */
        .acciones {
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            z-index: 100;
        }

        .btn-accion {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 22px;
            border-radius: 10px;
            font-weight: 700;
            cursor: pointer;
            font-size: 14px;
            border: none;
            text-decoration: none;
            box-shadow: 0 8px 16px rgba(0,0,0,.15);
        }

        .btn-imprimir { background: #2563eb; color: white; }
        .btn-volver { background: #e2e8f0; color: #334155; }

        @media print {
            @page { size: A4 landscape; margin: 10mm; }
            html, body { background: white !important; padding: 0 !important; }
            .acciones { display: none !important; }
            .etiqueta-wrapper {
                width: 190mm !important;
                margin: 20mm auto !important;
                box-shadow: none !important;
                border: 2px solid #0f172a !important;
            }
        }
    </style>
</head>
<body>

    <div class="etiqueta-wrapper">
        <div class="etiqueta-info">
            <div class="etiqueta-header">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="etiqueta-logo">
                <div class="etiqueta-institucion">
                    INST. TECNOLOGICO PUERTO DE MEJILLONES
                </div>
            </div>

            <div class="etiqueta-body">
                <div class="etiqueta-row">
                    <span class="label">GESTIÓN:</span>
                    <span class="value">
                        {{ $activo->fecha_adquisicion ? $activo->fecha_adquisicion->format('Y') : date('Y') }}
                    </span>
                </div>

                <div class="etiqueta-row">
                    <span class="label">CÓDIGO:</span>
                    <span class="value etiqueta-codigo">{{ $activo->codigo_activo }}</span>
                </div>

                <div class="etiqueta-row">
                    <span class="label">UNIDAD:</span>
                    <span class="value">
                        {{ $activo->ambiente->nombre ?? 'TALLER' }}
                    </span>
                </div>

                <div class="etiqueta-row">
                    <span class="label">ACTIVO:</span>
                    <span class="value etiqueta-nombre">{{ $activo->nombre }}</span>
                </div>

                <div class="etiqueta-row">
                    <span class="label">FUENTE:</span>
                    <span class="etiqueta-fuente">
                        {{ $activo->fuente->codigo ?? $activo->fuente->nombre ?? 'PSP' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="etiqueta-qr">
            {!! $qrImage ?? '' !!}
            <div class="etiqueta-qr-label">
                ESCANEAR<br>PARA VERIFICAR
            </div>
        </div>
    </div>

    <div class="acciones">
        <a href="{{ route('activos.index') }}" class="btn-accion btn-volver">← Volver</a>
        <button onclick="window.print()" class="btn-accion btn-imprimir">🖨️ Imprimir Etiqueta</button>
    </div>

</body>
</html>
