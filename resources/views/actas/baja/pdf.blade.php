<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acta de Baja - {{ $acta->numero_acta }}</title>
    <style>
        @page {
            size: letter landscape;
            margin: 1cm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #000;
            line-height: 1.3;
        }
        .header-title {
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 4px;
        }
        .info-table, .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .items-table th, .items-table td {
            border: 1px solid #cbd5e1;
            padding: 8px;
            text-align: left;
            font-size: 11px;
        }
        .items-table th {
            background: #f1f5f9;
            font-weight: bold;
            color: #334155;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

    <div class="header-title">ACTA DE BAJA DE ACTIVO FIJO</div>
    <div style="font-size: 13px; font-weight: bold; margin-bottom: 15px;">Número: {{ $acta->numero_acta }}</div>

    <div style="display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 12px;">
        <div>
            <strong>Fecha:</strong> {{ \Carbon\Carbon::parse($acta->fecha_baja ?? $acta->created_at)->format('d/m/Y') }}<br>
            <strong>Motivo:</strong> {{ $acta->motivo ?? '—' }}<br>
            <strong>Observaciones:</strong> {{ $acta->observaciones ?? '—' }}
        </div>
        <div>
            <strong>Solicitante:</strong> {{ $acta->usuario->nombre_completo ?? '—' }}
        </div>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 130px;">CÓDIGO</th>
                <th style="width: 180px;">NOMBRE</th>
                <th>CATEGORÍA</th>
                <th>UBICACIÓN</th>
                <th style="width: 100px; text-align: right;">VALOR</th>
            </tr>
        </thead>
        <tbody>
            @if($acta->activo)
            <tr>
                <td><strong>{{ $acta->activo->codigo_activo }}</strong></td>
                <td>{{ $acta->activo->nombre }}</td>
                <td>{{ $acta->activo->categoria->nombre ?? '—' }}</td>
                <td>{{ $acta->activo->ambiente->nombre ?? '—' }}</td>
                <td style="text-align: right;">${{ number_format($acta->activo->valor_adquisicion ?? 0, 2) }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    <div style="margin-top: 25px; font-size: 10px; color: #64748b; border-top: 1px solid #cbd5e1; padding-top: 8px;">
        Este documento se genera desde SISActivos. Los activos incluidos pasan a estado de registro BAJA.
    </div>

</body>
</html>
