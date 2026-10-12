<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acta de Transferencia - {{ $acta->numero_acta }}</title>
    <style>
        @page {
            size: legal landscape;
            margin: 1cm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #000;
            line-height: 1.2;
        }
        .header-table, .info-table, .items-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            text-align: center;
            font-weight: bold;
            font-size: 12px;
        }
        .titulo-acta {
            text-align: center;
            font-size: 15px;
            font-weight: bold;
            text-decoration: underline;
            color: #000000;
            margin: 10px 0 4px 0;
        }
        .subtitulo-fecha {
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            color: #cc0000;
            margin-bottom: 12px;
        }
        .info-table {
            margin-bottom: 15px;
            font-size: 10px;
        }
        .info-table th, .info-table td {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: top;
        }
        .info-box-title {
            font-weight: bold;
            background: #f1f1f1;
        }
        .red-text {
            color: #cc0000;
            font-weight: bold;
        }
        .items-table th, .items-table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
            font-size: 10px;
        }
        .items-table th {
            background: #e2e8f0;
            font-weight: bold;
        }
        .text-left {
            text-align: left !important;
        }
        .observaciones {
            margin-top: 15px;
            border: 1px solid #000;
            padding: 8px;
            font-weight: bold;
            font-size: 11px;
        }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td>INSTITUTO TECNOLÓGICO PUERTO DE MEJILLONES</td>
        </tr>
        <tr>
            <td>DIRECCIÓN ADMINISTRATIVA</td>
        </tr>
    </table>

    <div class="titulo-acta">ACTA DE TRANSFERENCIA</div>
    <div class="subtitulo-fecha">
        {{ strtoupper(\Carbon\Carbon::parse($acta->fecha_transferencia)->translatedFormat('d DE F_ DE Y')) }}
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
                <th style="width: 140px;">CODIGO</th>
                <th style="width: 70px;">ESTADO</th>
                <th style="width: 60px;">CANT.</th>
                <th>DESCRIPCIÓN</th>
            </tr>
        </thead>
        <tbody>
            @php $activo = $acta->activo; @endphp
            <tr>
                <td><strong>1</strong></td>
                <td><strong>{{ $activo->codigo_activo ?? '—' }}</strong></td>
                <td><strong>{{ $activo->estado_fisico ?? 'B' }}</strong></td>
                <td><strong>1</strong></td>
                <td class="text-left">
                    <strong>{{ strtoupper($activo->nombre ?? '') }}</strong><br>
                    Marca: {{ $activo->marca ?? 'S/M' }} | Modelo: {{ $activo->modelo ?? 'S/Mod' }} | Serie: {{ $activo->numero_serie ?? 'S/N' }}<br>
                    <span style="font-size: 9px; color: #475569;">{{ $activo->descripcion }}</span>
                </td>
            </tr>
            {{-- Rellenar filas vacías para mantener el formato estético impreso del formato oficial --}}
            @for($i = 2; $i <= 8; $i++)
            <tr>
                <td>{{ $i }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @endfor
        </tbody>
    </table>

    <div class="observaciones">
        OBSERVACIONES: {{ strtoupper($acta->observaciones ?? 'NINGUNA') }}
    </div>

</body>
</html>
