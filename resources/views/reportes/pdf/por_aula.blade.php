<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inventario por Aula</title>
    @include('reportes.pdf._styles')
    <style>
        /* Forzar formato Oficio Horizontal (Landscape) en PDF */
        @page {
            size: 330mm 215mm;
            margin: 8mm;
        }
        body {
            font-family: sans-serif;
            font-size: 8.5px;
            color: #000;
        }
        .header .titulo {
            text-align: center;
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 4px;
        }
        table.inventario th {
            background-color: #2563eb !important;
            color: #ffffff !important;
            text-align: center;
            vertical-align: middle;
            font-size: 7.5px;
            padding: 3px;
        }
        table.inventario td {
            padding: 3px;
            vertical-align: middle;
            font-size: 8px;
            border: 1px solid #cbd5e1;
        }
        table.datos td {
            font-size: 8.5px;
            padding: 2px 4px;
        }
        .stats {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
            margin-bottom: 6px;
        }
        .stats td {
            border: 1px solid #000;
            padding: 3px;
            text-align: center;
            width: 20%;
        }
        .stats .num { 
            font-size: 11px; 
            font-weight: bold; 
            display: block; 
        }
        
        /* ESTILOS DE TEXTO PURO PARA LEYENDAS */
        .leyendas-texto-libre {
            margin-top: 12px;
            font-size: 6.5px;
            color: #1e293b;
            line-height: 1.4;
        }
    </style>
</head>
<body>

    {{-- ENCABEZADO --}}
    <div class="header">
        <table style="width:100%;">
            <tr>
                <td style="text-align:left; font-size:8.5px; font-weight:bold;">FORMULARIO A</td>
                <td style="text-align:center; font-size:8.5px; font-weight:bold;">
                    INVENTARIO POR AULA
                </td>
                <td style="text-align:right; font-size:8.5px; font-weight:bold;">GESTIÓN {{ date('Y') }}</td>
            </tr>
        </table>
        <div class="titulo" style="margin-top:2px;">
            {{ $ambiente->nombre }} — {{ $ambiente->carrera->nombre ?? '' }}
        </div>
    </div>

    {{-- DATOS --}}
    <table class="datos" style="width:100%; margin-bottom: 6px;">
        <tr>
            <td class="label" style="font-weight:bold; width:15%;">AMBIENTE:</td>
            <td style="width:35%;">{{ $ambiente->nombre }}</td>
            <td class="label" style="font-weight:bold; width:15%;">CÓDIGO:</td>
            <td style="width:35%;">{{ $ambiente->codigo }}</td>
        </tr>
        <tr>
            <td class="label" style="font-weight:bold;">CARRERA:</td>
            <td>{{ $ambiente->carrera->nombre ?? '—' }}</td>
            <td class="label" style="font-weight:bold;">UBICACIÓN:</td>
            <td>Bloque {{ $ambiente->bloque ?? '—' }} — Piso {{ $ambiente->piso ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label" style="font-weight:bold;">FECHA REPORTE:</td>
            <td>{{ date('d/m/Y H:i') }}</td>
            <td class="label" style="font-weight:bold;">RESPONSABLE:</td>
            <td>{{ Auth::user()->nombre_completo ?? '—' }}</td>
        </tr>
    </table>

    {{-- ESTADÍSTICAS --}}
    <table class="stats">
        <tr>
            <td><span class="num">{{ $activos->count() }}</span>TOTAL</td>
            <td><span class="num">{{ $activos->where('estado_fisico','B')->count() }}</span>BUENOS</td>
            <td><span class="num">{{ $activos->where('estado_fisico','R')->count() }}</span>REGULARES</td>
            <td><span class="num">{{ $activos->where('estado_fisico','M')->count() }}</span>MALOS</td>
            <td><span class="num">{{ $activos->where('estado_fisico','FF')->count() }}</span>FUERA SERV.</td>
        </tr>
    </table>

    {{-- TABLA PRINCIPAL CON ESTADO EN 4 COLUMNAS B/R/M/FF --}}
    <table class="inventario" style="width:100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th rowspan="2" style="width:25px;">N°</th>
                <th rowspan="2" style="width:65px;">CÓDIGO</th>
                <th rowspan="2" style="width:110px;">DESCRIPCIÓN</th>
                <th rowspan="2" style="width:140px;">CARACTERÍSTICAS</th>
                <th rowspan="2" style="width:35px;">CANT.</th>
                <th colspan="4">ESTADO</th>
                <th rowspan="2" style="width:45px;">FUENTE</th>
                <th rowspan="2" style="width:50px;">GESTIÓN</th>
                <th rowspan="2" style="width:110px;">OBS.</th>
            </tr>
            <tr>
                <th style="width:18px;">B</th>
                <th style="width:18px;">R</th>
                <th style="width:18px;">M</th>
                <th style="width:22px;">FF</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($agrupados as $i => $g)
                <tr>
                    <td style="text-align:center;">{{ $i + 1 }}</td>
                    <td style="text-align:center;">{!! nl2br(e($g->codigos)) !!}</td>
                    <td>{{ $g->nombre }}</td>
                    <td>
                        {{ $g->descripcion }}
                        @if($g->marca) <br><strong>Marca:</strong> {{ $g->marca }} @endif
                        @if($g->modelo) <br><strong>Modelo:</strong> {{ $g->modelo }} @endif
                    </td>
                    <td style="text-align:center;">{{ $g->cantidad }}</td>
                    
                    {{-- ESTADO: Marca con "X" según corresponda --}}
                    <td style="text-align:center; font-weight:bold;">{{ $g->estado_fisico == 'B' ? 'X' : '' }}</td>
                    <td style="text-align:center; font-weight:bold;">{{ $g->estado_fisico == 'R' ? 'X' : '' }}</td>
                    <td style="text-align:center; font-weight:bold;">{{ $g->estado_fisico == 'M' ? 'X' : '' }}</td>
                    <td style="text-align:center; font-weight:bold;">{{ $g->estado_fisico == 'FF' ? 'X' : '' }}</td>

                    <td style="text-align:center;">{{ $g->fuente_codigo }}</td>
                    <td style="text-align:center;">{{ $g->gestion }}</td>
                    <td>{{ $g->observaciones }}</td>
                </tr>
            @empty
                <tr><td colspan="11" style="text-align:center;">Sin activos en este ambiente</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" style="text-align:right; font-weight:bold;">TOTALES:</td>
                <td style="text-align:center; font-weight:bold;">{{ $activos->sum('cantidad') }}</td>
                <td colspan="6"></td>
            </tr>
        </tfoot>
    </table>

    {{-- FIRMAS --}}
    <table class="firmas" style="margin-top: 15px; width: 100%; border-collapse: collapse;">
        <tr>
            <td style="width: 33.33%; vertical-align: top; padding: 10px; text-align: center;">
                <div class="linea" style="border-top: 1px solid #000; padding-top: 3px; display: inline-block; width: 75%; font-size: 8px;">
                    {{ Auth::user()->nombre_completo ?? 'Jefe de Carrera' }}<br>
                    C.I.: {{ Auth::user()->ci ?? '—' }}<br>
                    <small style="font-size: 7px; color: #555;">JEFE DE CARRERA</small>
                </div>
            </td>
            <td style="width: 33.33%; vertical-align: top; padding: 10px; text-align: center;">
                <div class="linea" style="border-top: 1px solid #000; padding-top: 3px; display: inline-block; width: 75%; font-size: 8px;">
                    Responsable de Inventario<br>
                    C.I.: —<br>
                    <small style="font-size: 7px; color: #555;">INVENTARIADOR</small>
                </div>
            </td>
            <td style="width: 33.33%; vertical-align: top; padding: 10px; text-align: center;">
                <div class="linea" style="border-top: 1px solid #000; padding-top: 3px; display: inline-block; width: 75%; font-size: 8px;">
                    Dirección Administrativa<br>
                    C.I.: —<br>
                    <small style="font-size: 7px; color: #555;">ADMINISTRADORA</small>
                </div>
            </td>
        </tr>
    </table>

    {{-- LEYENDAS EN TEXTO PURO (SIN TABLAS, LETRA PEQUEÑA) --}}
    <div class="leyendas-texto-libre">
        <div style="margin-bottom: 3px;">
            <strong>ESTADO:</strong> 
            <strong>B</strong> BUENO &nbsp;&bull;&nbsp; 
            <strong>R</strong> REGULAR &nbsp;&bull;&nbsp; 
            <strong>M</strong> MALO &nbsp;&bull;&nbsp; 
            <strong>FF</strong> FUERA DE FUNCIONAMIENTO
        </div>
        <div>
            <strong>FUENTES:</strong> 
            <strong>GD</strong> GOBIERNO DEPARTAMENTAL &nbsp;&bull;&nbsp; 
            <strong>GM</strong> GOBIERNO MUNICIPAL &nbsp;&bull;&nbsp; 
            <strong>GN</strong> GOBIERNO NACIONAL &nbsp;&bull;&nbsp; 
            <strong>PSP</strong> PROYECTO SOCIO PRODUCTIVO &nbsp;&bull;&nbsp; 
            <strong>ADQ</strong> ADQUISICIÓN &nbsp;&bull;&nbsp; 
            <strong>RPC</strong> RECURSOS PROPIOS POR CARRERA &nbsp;&bull;&nbsp; 
            <strong>RPI</strong> RECURSOS PROPIOS INSTITUCIONALES &nbsp;&bull;&nbsp; 
            <strong>OTR</strong> OTRO
        </div>
    </div>

</body>
</html>