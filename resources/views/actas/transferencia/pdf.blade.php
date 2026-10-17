<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acta de Transferencia - {{ $acta->numero_acta }}</title>
    <style>
        @page {
            size: legal landscape;
            margin: 0.8cm 1.5cm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #000;
            line-height: 1.2;
            position: relative;
            min-height: 100%;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .header-table td {
            vertical-align: middle;
        }
        .logo-container {
            width: 120px;
            text-align: left;
        }
        .logo-container img {
            max-width: 100px;
            max-height: 60px;
        }
        .header-text {
            text-align: center;
            font-weight: bold;
            font-size: 12px;
        }

        .formulario-badge {
            border: 1px solid #000;
            padding: 3px 6px;
            font-size: 9px;
            font-weight: bold;
            display: inline-block;
        }

        .titulo-acta {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            text-decoration: underline;
            color: #000;
            margin: 5px 0 2px 0;
        }
        .subtitulo-fecha {
            text-align: center;
            font-size: 11px;
            font-weight: bold;
            color: #cc0000;
            margin-bottom: 15px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 10px;
        }
        .info-table th, .info-table td {
            border: 1px solid #000;
            padding: 5px;
            vertical-align: top;
        }
        .red-text {
            color: #cc0000;
            font-weight: bold;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .items-table th, .items-table td {
            border: 1px solid #000;
            padding: 3px;
            text-align: center;
            font-size: 9px;
        }
        .items-table th {
            background: #e2e8f0;
            font-weight: bold;
            font-size: 10px;
        }
        .text-left {
            text-align: left !important;
        }

        .observaciones {
            border: 1px solid #000;
            padding: 5px;
            font-weight: bold;
            font-size: 10px;
            margin-bottom: 20px;
            min-height: 30px;
        }

        .total-activos {
            font-size: 11px;
            font-weight: bold;
            text-align: right;
            margin-top: 5px;
            color: #cc0000;
        }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td class="logo-container">
                <div style="width: 80px; height: 60px; border: 1px dashed #ccc; text-align: center; line-height: 60px; font-size: 8px; color: #999;">LOGO</div>
            </td>
            <td class="header-text">
                INSTITUTO TECNOLÓGICO PUERTO DE MEJILLONES<br>
                DIRECCIÓN ADMINISTRATIVA
            </td>
            <td style="width: 120px; text-align: right; vertical-align: top; padding-top: 5px;">
                <div class="formulario-badge">FORMULARIO N° 4</div>
            </td>
        </tr>
    </table>

    <div class="titulo-acta">ACTA DE TRANSFERENCIA</div>
    <div class="subtitulo-fecha">
        {{ strtoupper(\Carbon\Carbon::parse($acta->fecha_transferencia)->translatedFormat('d DE F DE Y')) }}
    </div>

    <table class="info-table">
        <tr>
            <td style="width: 50%;">
                <strong>TRANSFERENCIA INTERNA:</strong> <span class="red-text">{{ $acta->numero_acta }}</span>
            </td>
            <td style="width: 50%;">
                <strong>UBICACIÓN ACTUAL:</strong> <span class="red-text">{{ strtoupper($acta->origenAmbiente->nombre ?? '—') }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <strong>DE OF.:</strong> {{ strtoupper($acta->origenAmbiente->nombre ?? '—') }}<br>
                <strong>RESPONSABLE:</strong> <span class="red-text">{{ strtoupper($acta->custodioAnterior->nombre_completo ?? '—') }}</span><br>
                <strong>CARGO:</strong> <span class="red-text">CUSTODIO / DOCENTE</span>
            </td>
            <td>
                <strong>DE OF.:</strong> <span class="red-text">{{ strtoupper($acta->destinoAmbiente->nombre ?? '—') }}</span><br>
                <strong>RESPONSABLE:</strong> <span class="red-text">{{ strtoupper($acta->custodioNuevo->nombre_completo ?? '—') }}</span><br>
                <strong>CARGO:</strong> <span class="red-text">CUSTODIO / RESPONSABLE</span>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 40px;">ITEM</th>
                <th style="width: 120px;">CÓDIGO</th>
                <th style="width: 60px;">ESTADO</th>
                <th style="width: 50px;">CANT.</th>
                <th>DESCRIPCIÓN</th>
            </tr>
        </thead>
        <tbody>
            @php $contador = 1; @endphp
            @foreach($acta->activos as $activo)
            <tr>
                <td><strong>{{ $contador }}</strong></td>
                <td><strong>{{ $activo->codigo_activo ?? '—' }}</strong></td>
                <td><strong>{{ $activo->estado_fisico ?? 'B' }}</strong></td>
                <td><strong>{{ $activo->pivot->cantidad ?? 1 }}</strong></td>
                <td class="text-left">
                    <strong>{{ strtoupper($activo->nombre ?? '') }}</strong><br>
                    Marca: {{ $activo->marca ?? 'S/M' }}, Modelo: {{ $activo->modelo ?? 'S/Mod' }}, color {{ $activo->color ?? 'S/C' }}<br>
                    <span style="font-size: 8px; color: #475569;">{{ $activo->descripcion }}</span>
                </td>
            </tr>
            @php $contador++; @endphp
            @endforeach
        </tbody>
    </table>

    <div class="total-activos">
        TOTAL DE BIENES TRANSFERIDOS: {{ $acta->activos->count() }}
    </div>

    <div class="observaciones">
        OBSERVACIONES: {{ strtoupper($acta->observaciones ?? '') }}
    </div>

</body>
</html>
