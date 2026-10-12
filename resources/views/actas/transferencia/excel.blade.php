@php
    header("Content-Type: application/vnd.ms-excel; charset=utf-8");
    header("Content-Disposition: attachment; filename=Acta_Transferencia_" . $acta->numero_acta . ".xls");
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        table { font-family: Arial, sans-serif; font-size: 11px; border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #000; padding: 6px; text-align: center; vertical-align: middle; }
        .titulo { font-size: 14px; font-weight: bold; text-align: center; color: #0e0e0e; }
        .subtitulo { font-size: 12px; font-weight: bold; text-align: center; color: #cc0000; }
        .header-info { font-weight: bold; background: #f1f1f1; text-align: left; }
        .text-left { text-align: left; }
    </style>
</head>
<body>

    <table>
        <tr>
            <td colspan="5" style="border:none; font-weight:bold; font-size:12px; text-align:center;">INSTITUTO TECNOLÓGICO PUERTO DE MEJILLONES</td>
        </tr>
        <tr>
            <td colspan="5" style="border:none; font-weight:bold; font-size:11px; text-align:center;">DIRECCIÓN ADMINISTRATIVA</td>
        </tr>
        <tr><td colspan="5" style="border:none;"></td></tr>
        <tr>
            <td colspan="5" class="titulo">ACTA DE TRANSFERENCIA</td>
        </tr>
        <tr>
            <td colspan="5" class="subtitulo">{{ strtoupper(\Carbon\Carbon::parse($acta->fecha_transferencia)->translatedFormat('d DE F_ DE Y')) }}</td>
        </tr>
        <tr><td colspan="5" style="border:none;"></td></tr>

        <tr>
            <td colspan="2" class="header-info">TRANSFERENCIA INTERNA: {{ $acta->numero_acta }}</td>
            <td colspan="3" class="header-info">UBICACIÓN ACTUAL: {{ strtoupper($acta->origenAmbiente->nombre ?? '—') }}</td>
        </tr>
        <tr>
            <td colspan="2" class="text-left">
                <strong>DE OF.:</strong> {{ strtoupper($acta->origenAmbiente->nombre ?? '—') }}<br>
                <strong>RESPONSABLE:</strong> {{ strtoupper($acta->custodioAnterior->nombre_completo ?? '—') }}<br>
                <strong>CARGO:</strong> CUSTODIO / DOCENTE
            </td>
            <td colspan="3" class="text-left">
                <strong>DE OF.:</strong> {{ strtoupper($acta->destinoAmbiente->nombre ?? '—') }}<br>
                <strong>RESPONSABLE:</strong> {{ strtoupper($acta->custodioNuevo->nombre_completo ?? '—') }}<br>
                <strong>CARGO:</strong> CUSTODIO / RESPONSABLE
            </td>
        </tr>
        <tr><td colspan="5" style="border:none;"></td></tr>
        <tr style="background:#e2e8f0; font-weight:bold;">
            <th style="width: 40px;">ITEM</th>
            <th style="width: 140px;">CODIGO</th>
            <th style="width: 70px;">ESTADO</th>
            <th style="width: 60px;">CANT.</th>
            <th>DESCRIPCIÓN</th>
        </tr>
        @php $activo = $acta->activo; @endphp
        <tr>
            <td>1</td>
            <td>{{ $activo->codigo_activo ?? '—' }}</td>
            <td>{{ $activo->estado_fisico ?? 'B' }}</td>
            <td>1</td>
            <td class="text-left">
                {{ strtoupper($activo->nombre ?? '') }} - Marca: {{ $activo->marca ?? 'S/M' }} | Serie: {{ $activo->numero_serie ?? 'S/N' }}
            </td>
        </tr>
        @for($i = 2; $i <= 8; $i++)
        <tr>
            <td>{{ $i }}</td>
            <td></td><td></td><td></td><td></td>
        </tr>
        @endfor
        <tr><td colspan="5" style="border:none;"></td></tr>
        <tr>
            <td colspan="5" class="text-left" style="border:1px solid #000; font-weight:bold;">
                OBSERVACIONES: {{ strtoupper($acta->observaciones ?? 'NINGUNA') }}
            </td>
        </tr>
    </table>

</body>
</html>
