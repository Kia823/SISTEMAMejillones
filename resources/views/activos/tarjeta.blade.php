<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Tarjeta de Activo - {{ $activo->codigo_activo }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f1f5f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        /* Contenedor principal de la tarjeta */
        .tarjeta-card {
            width: 420px;
            background: #ffffff;
            border: 2px solid #cbd5e1;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }

        .title {
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
            margin: 0;
            text-transform: uppercase;
        }

        .subtitle {
            font-size: 11px;
            color: #2563eb;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .media-grid {
            display: flex;
            gap: 12px;
            justify-content: center;
            align-items: center;
            margin-bottom: 16px;
        }

        .img-box {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        .data-table td {
            padding: 5px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .label {
            font-weight: bold;
            color: #475569;
            width: 40%;
        }

        .value {
            color: #0f172a;
        }

        .btn-print {
            display: block;
            width: 100%;
            padding: 10px;
            background: #2563eb;
            color: #ffffff;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 16px;
            text-align: center;
        }

        /* CONFIGURACIÓN EXCLUSIVA PARA IMPRESIÓN EN HOJA */
        @media print {
            @page {
                size: portrait;
                margin: 0;
            }

            body {
                background: #ffffff;
                min-height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .btn-print {
                display: none !important; /* Oculta el botón al imprimir */
            }

            .tarjeta-card {
                box-shadow: none;
                border: 2px solid #000000;
                margin: auto;
            }
        }
    </style>
</head>
<body>

    <div class="tarjeta-card">
        <div class="header">
            <div class="subtitle">SISTEMA DE GESTIÓN DE ACTIVOS FIJOS</div>
            <h2 class="title">{{ $activo->nombre }}</h2>
        </div>

        <div class="media-grid">
            @if($activo->ruta_qr)
                <img src="{{ asset('storage/' . $activo->ruta_qr) }}" class="img-box" alt="QR">
            @endif

            @if($activo->imagen)
                <img src="{{ asset('storage/' . $activo->imagen) }}" class="img-box" alt="Foto">
            @endif
        </div>

        <table class="data-table">
            <tr>
                <td class="label">Código:</td>
                <td class="value"><strong>{{ $activo->codigo_activo }}</strong></td>
            </tr>
            <tr>
                <td class="label">Marca / Modelo:</td>
                <td class="value">{{ $activo->marca }} / {{ $activo->modelo }}</td>
            </tr>
            <tr>
                <td class="label">N° Serie:</td>
                <td class="value">{{ $activo->numero_serie ?? '—' }}</td>
            </tr>
            <tr>
                <td class="label">Categoría:</td>
                <td class="value">{{ $activo->categoria?->nombre ?? '—' }}</td>
            </tr>
            <tr>
                <td class="label">Ambiente:</td>
                <td class="value">{{ $activo->ambiente?->nombre ?? '—' }}</td>
            </tr>
            <tr>
                <td class="label">Custodio:</td>
                <td class="value">{{ $activo->custodioActual?->nombre_completo ?? 'Sin asignar' }}</td>
            </tr>
            <tr>
                <td class="label">Estado:</td>
                <td class="value"><strong>{{ $activo->estado_registro }}</strong></td>
            </tr>
        </table>

        <button class="btn-print" onclick="window.print()">🖨️ Imprimir Tarjeta</button>
    </div>

</body>
</html>
