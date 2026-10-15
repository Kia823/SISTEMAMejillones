@php
    header("Content-Type: application/vnd.ms-excel; charset=utf-8");
    header("Content-Disposition: attachment; filename=Acta_Baja_" . $acta->numero_acta . ".xls");
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        table { font-family: Arial, sans-serif; font-size: 11px; border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #000; padding: 6px; text-align: center; vertical-align: middle; }
        .titulo { font-size: 14px; font-weight: bold; text-align: center; color: #7c2d12; }
        .subtitulo { font-size: 12px; font-weight: bold; text-align: center; color: #475569; }
        .header-info { font-weight: bold; background: #f1f1f1; text-align: left; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
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
            <td colspan="5" class="titulo">ACTA DE BAJA DE ACTIVO FIJO</td>
        </tr>
        <tr>
            <td colspan="5" class="subtitulo">NÚMERO: {{ $acta->numero_acta }}</td>
        </tr>
        <tr><td colspan="5" style="border:none;"></td></tr>

        <tr>
            <td colspan="3" class="header-info">FECHA: {{ \Carbon\Carbon::parse($acta->fecha_baja ?? $acta->created_at)->format('d/m/Y') }}</td>
            <td colspan="2" class="header-info">ESTADO: {{ strtoupper($acta->estado_texto) }}</td>
        </tr>
        <tr>
            <td colspan="3" class="text-left">
                <strong>MOTIVO:</strong> {{ strtoupper($acta->motivo ?? '—') }}<br>
                <strong>OBSERVACIONES:</strong> {{ strtoupper($acta->observaciones ?? '—') }}
            </td>
            <td colspan="2" class="text-left">
                <strong>SOLICITANTE:</strong> {{ strtoupper($acta->usuario->nombre_completo ?? '—') }}<br>
                <strong>CARGO:</strong> INVENTARIADOR / OPERATIVO
            </td>
        </tr>
        <tr><td colspan="5" style="border:none;"></td></tr>

        <tr style="background:#e2e8f0; font-weight:bold;">
            <th style="width: 140px;">CÓDIGO</th>
            <th style="width: 180px;">NOMBRE</th>
            <th>CATEGORÍA</th>
            <th>UBICACIÓN</th>
            <th style="width: 100px; text-align: right;">VALOR</th>
        </tr>

        @if($acta->activo)
        <tr>
            <td style="font-weight: bold;">{{ $acta->activo->codigo_activo }}</td>
            <td class="text-left">{{ $acta->activo->nombre }}</td>
            <td class="text-left">{{ $acta->activo->categoria->nombre ?? '—' }}</td>
            <td class="text-left">{{ $acta->activo->ambiente->nombre ?? '—' }}</td>
            <td class="text-right">${{ number_format($acta->activo->valor_adquisicion ?? 0, 2) }}</td>
        </tr>
        @endif

        <tr><td colspan="5" style="border:none;"></td></tr>
        <tr>
            <td colspan="5" class="text-left" style="border:1px solid #000; font-size: 10px; color: #475569;">
                Este documento se genera desde SISActivos. Los activos incluidos pasan a estado de registro BAJA.
            </td>
        </tr>
    </table>

</body>
</html>
