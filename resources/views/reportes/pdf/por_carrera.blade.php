<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inventario por Carrera</title>
    @include('reportes.pdf._styles')
    <style>
        .resumen-table { width:100%; border-collapse:collapse; font-size:8px; margin-top:6px; }
        .resumen-table thead th {
            background:#e5e5e5; border:0.7px solid #000; padding:3px;
            text-align:center; font-weight:bold; font-size:8px; text-transform:uppercase;
        }
        .resumen-table tbody td { border:0.5px solid #555; padding:3px; }
        .resumen-table tfoot td { background:#f1f5f9; font-weight:bold; border:0.7px solid #000; padding:4px; }
    </style>
</head>
<body>

    <div class="header">
        <table style="width:100%;">
            <tr>
                <td style="text-align:left; font-size:9px; font-weight:bold;">FORMULARIO A</td>
                <td style="text-align:center; font-size:9px; font-weight:bold;">
                    EQUIPOS OTORGADOS EL MINISTERIO DE EDUCACIÓN
                </td>
                <td style="text-align:right; font-size:9px; font-weight:bold;">GESTIÓN {{ date('Y') }}</td>
            </tr>
        </table>
        <div class="titulo" style="margin-top:3px;">
            INVENTARIO CONSOLIDADO POR CARRERA
        </div>
    </div>

    <table class="datos">
        <tr>
            <td class="label">INSTITUTO:</td>
            <td colspan="3">TECNOLÓGICO "PUERTO DE MEJILLONES"</td>
            <td class="label">DEPARTAMENTO:</td>
            <td>LA PAZ</td>
        </tr>
        <tr>
            <td class="label">CARRERA:</td>
            <td colspan="3"><strong>{{ $carrera->nombre }}</strong></td>
            <td class="label">PROVINCIA:</td>
            <td>MURILLO</td>
        </tr>
        <tr>
            <td class="label">CÓDIGO:</td>
            <td>{{ $carrera->codigo }}</td>
            <td class="label">AMBIENTES:</td>
            <td>{{ $ambientes->count() }}</td>
            <td class="label">CIUDAD:</td>
            <td>EL ALTO</td>
        </tr>
        <tr>
            <td class="label">GENERADO:</td>
            <td colspan="5">{{ date('d/m/Y H:i') }}</td>
        </tr>
    </table>

    {{-- RESUMEN POR CATEGORÍA / AMBIENTE --}}
    <h3 style="font-size:9px; margin:8px 0 4px 0;">Resumen por Categoría / Ambiente</h3>

    <table class="resumen-table">
        <thead>
            <tr>
                <th style="width:5%;">N°</th>
                <th style="width:25%;">CATEGORÍA</th>
                <th style="width:25%;">AMBIENTE</th>
                <th style="width:9%;">TOTAL</th>
                <th style="width:9%;">BUENO</th>
                <th style="width:9%;">REGULAR</th>
                <th style="width:9%;">MALO</th>
                <th style="width:9%;">F. SERV.</th>
            </tr>
        </thead>
        <tbody>
            @php $totTotal=0; $totB=0; $totR=0; $totM=0; $totFF=0; @endphp
            @forelse($resumen as $i => $r)
                @php
                    $totTotal += $r->total;
                    $totB += $r->buenos;
                    $totR += $r->regulares;
                    $totM += $r->malos;
                    $totFF += $r->fuera;
                @endphp
                <tr>
                    <td style="text-align:center;">{{ $i + 1 }}</td>
                    <td>{{ $r->categoria }}</td>
                    <td>{{ $r->ambiente }}</td>
                    <td style="text-align:center; font-weight:bold;">{{ $r->total }}</td>
                    <td style="text-align:center;">{{ $r->buenos }}</td>
                    <td style="text-align:center;">{{ $r->regulares }}</td>
                    <td style="text-align:center;">{{ $r->malos }}</td>
                    <td style="text-align:center;">{{ $r->fuera }}</td>
                </tr>
            @empty
                <tr><td colspan="8" style="text-align:center;">Sin datos</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="text-align:right;">TOTALES:</td>
                <td style="text-align:center;">{{ $totTotal }}</td>
                <td style="text-align:center;">{{ $totB }}</td>
                <td style="text-align:center;">{{ $totR }}</td>
                <td style="text-align:center;">{{ $totM }}</td>
                <td style="text-align:center;">{{ $totFF }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- AMBIENTES DE LA CARRERA --}}
    <h3 style="font-size:9px; margin:12px 0 4px 0;">Ambientes que componen la carrera</h3>

    <table class="resumen-table">
        <thead>
            <tr>
                <th style="width:5%;">N°</th>
                <th style="width:15%;">CÓDIGO</th>
                <th style="width:50%;">AMBIENTE</th>
                <th style="width:15%;">BLOQUE</th>
                <th style="width:15%;">TOTAL ACTIVOS</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ambientes as $i => $a)
                <tr>
                    <td style="text-align:center;">{{ $i + 1 }}</td>
                    <td>{{ $a->codigo }}</td>
                    <td>{{ $a->nombre }}</td>
                    <td style="text-align:center;">{{ $a->bloque ?? '—' }}</td>
                    <td style="text-align:center; font-weight:bold;">{{ $a->activos_count }}</td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align:center;">Sin ambientes</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- FIRMAS --}}
    <table class="firmas">
        <tr>
            <td><div class="linea">Jefe de Carrera<br>C.I.: —</div></td>
            <td><div class="linea">Responsable de Inventario<br>C.I.: —</div></td>
            <td><div class="linea">Dirección Administrativa<br>C.I.: —</div></td>
        </tr>
    </table>

</body>
</html>
