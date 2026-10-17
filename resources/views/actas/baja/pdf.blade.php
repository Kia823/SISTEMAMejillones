<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acta de Baja - {{ $acta->numero_acta }}</title>
    <style>
        @page {
            size: legal landscape; /* HOJA OFICIO HORIZONTAL */
            margin: 8mm 10mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9px;
            color: #000;
            line-height: 1.15;
        }

        /* === ENCABEZADO === */
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .header-table td { vertical-align: middle; }
        .logo { width: 55px; }
        .header-text { text-align: center; font-weight: bold; font-size: 11px; }
        .header-text h1 { margin: 0; font-size: 12px; }
        .header-text h2 { margin: 1px 0; font-size: 10px; font-weight: normal; }
        
        .formulario-box {
            border: 1.5px solid #000;
            padding: 2px 8px;
            font-size: 9px;
            font-weight: bold;
            text-align: center;
            display: inline-block;
            border-radius: 4px;
        }
        .hoja-box {
            border: 1.5px solid #000;
            padding: 1px 8px;
            font-size: 9px;
            font-weight: bold;
            text-align: center;
            border-radius: 4px;
            margin-top: 2px;
        }

        /* === TÍTULO Y FECHA === */
        .titulo-acta { text-align: center; font-size: 12px; font-weight: bold; margin: 8px 0 2px 0; }
        .fecha-acta { text-align: center; font-size: 10px; font-weight: bold; margin-bottom: 8px; text-transform: uppercase; }

        /* === PÁRRAFO LEGAL === */
        .parrafo-legal {
            border: 1.5px solid #000;
            border-radius: 6px;
            padding: 6px 10px;
            font-size: 9px;
            text-align: justify;
            margin-bottom: 8px;
            background: #fff;
        }

        /* === TABLA PRINCIPAL === */
        .tabla-datos {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .tabla-datos th, .tabla-datos td {
            border: 1px solid #000;
            padding: 3px 5px;
            font-size: 9px;
        }
        .tabla-datos th {
            background: #f8fafc;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
        }
        .tabla-datos td { text-align: center; }
        .tabla-datos td.left { text-align: left; }

        /* === SECCIÓN CAUSALES + FOTO === */
        .causales-wrapper {
            display: table;
            width: 100%;
            border: 1px solid #000;
            margin-bottom: 8px;
        }
        .causales-col {
            display: table-cell;
            width: 35%;
            padding: 6px;
            vertical-align: top;
            border-right: 1px solid #000;
        }
        .foto-col {
            display: table-cell;
            width: 65%;
            padding: 6px;
            vertical-align: middle;
            text-align: center;
        }
        .causales-title {
            font-size: 9px;
            font-weight: bold;
            margin-bottom: 4px;
            text-transform: uppercase;
        }
        .causales-table { width: 100%; border-collapse: collapse; }
        .causales-table td {
            border: 1px solid #000;
            padding: 3px 5px;
            font-size: 9px;
        }
        .causales-table .marca {
            width: 35px;
            text-align: center;
            font-weight: bold;
        }

        /* === CONTENEDOR DE FOTOS EN TABLA (Para DomPDF) === */
        .fotos-tabla {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }
        .fotos-tabla td {
            width: 33.33%;
            text-align: center;
            vertical-align: top;
            padding: 2px;
        }
        .foto-item {
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 4px;
            background: #fff;
        }
        
        /* === AJUSTE PARA QUE LA IMAGEN SE VEA NORMAL === */
        .foto-item img {
            width: 100%;
            height: 75px; /* Altura de la caja de la foto */
            object-fit: contain; /* Hace que la foto se vea completa sin recortes */
            background-color: #f8fafc; /* Fondo suave para los bordes vacíos */
            border-radius: 2px;
            display: block;
            margin: 0 auto;
        }
        
        .foto-item .foto-empty {
            height: 75px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px dashed #ccc;
            color: #999;
            font-size: 8px;
            border-radius: 2px;
        }
        .foto-label-item {
            font-size: 8px;
            font-weight: bold;
            margin-top: 3px;
            color: #475569;
        }

        /* === TOTAL Y FIRMAS === */
        .total-box {
            display: inline-block;
            border: 1px solid #000;
            padding: 2px 10px;
            font-weight: bold;
            font-size: 10px;
            margin-bottom: 8px;
        }

        .firmas-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .firmas-table td {
            border: 1px solid #000;
            text-align: center;
            padding: 6px;
            vertical-align: bottom;
            height: 55px;
            font-size: 9px;
        }
        .firma-line {
            border-top: 1px solid #000;
            width: 85%;
            margin: 0 auto;
            margin-top: 25px;
            padding-top: 3px;
        }

        /* === DATOS RESPONSABLES === */
        .datos-resp-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        .datos-resp-table td {
            border: 1px solid #000;
            padding: 3px 5px;
            font-size: 8.5px;
            vertical-align: middle;
        }
        .datos-resp-table .label {
            font-weight: bold;
            width: 140px;
            background: #f8fafc;
        }

        .footer-nota {
            font-size: 7.5px;
            text-align: justify;
            margin-top: 6px;
        }
        
        /* Evitar cortes de página */
        .tabla-datos, .causales-wrapper, .firmas-table, .datos-resp-table { page-break-inside: avoid; }
    </style>
</head>
<body>

    {{-- ================= ENCABEZADO ================= --}}
    <table class="header-table">
        <tr>
            <td style="width: 15%; text-align: left;">
                @if($logoDataUri)
                    <img src="{{ $logoDataUri }}" class="logo" alt="Logo">
                @endif
            </td>
            <td style="width: 55%;" class="header-text">
                <h1>INSTITUTO TECNOLOGICO "PUERTO DE MEJILLONES"</h1>
                <h2>DIRECCIÓN ADMINISTRATIVA</h2>
            </td>
            <td style="width: 30%; text-align: right;">
                <div class="formulario-box">FORMULARIO N° 5</div>
                <div class="hoja-box">HOJA N° 1 de 1</div>
            </td>
        </tr>
    </table>

    {{-- ================= TÍTULO Y FECHA ================= --}}
    <div class="titulo-acta">ACTA BAJA DE ACTIVO</div>
    <div class="fecha-acta">
        {{ \Carbon\Carbon::parse($acta->fecha_baja ?? $acta->created_at)->translatedFormat('d \d\e F \d\e Y') }}
    </div>

    {{-- ================= PÁRRAFO LEGAL ================= --}}
    <div class="parrafo-legal">
        En cumplimiento al D.S. 0181 Normas Básicas del Sistema de Administración de Bienes y Servicios en su Art. 234 - 235 los bienes dados de baja por las causales descritas en los incisos a), e) y f), la sección de Activos Fijos del Instituto Tecnológico "Puerto de Mejillones", procedió a la verificación de los siguientes activos:
    </div>

    {{-- ================= TABLA DE DATOS DEL ACTIVO ================= --}}
    <table class="tabla-datos">
        <thead>
            <tr>
                <th style="width: 5%;">ITEM</th>
                <th style="width: 15%;">CÓDIGO</th>
                <th style="width: 8%;">CANT.</th>
                <th style="width: 42%;">DESCRIPCIÓN</th>
                <th style="width: 30%;">MOTIVO DE BAJA</th>
            </tr>
        </thead>
        <tbody>
            @if($acta->activo)
            <tr>
                <td>1</td>
                <td><strong>{{ $acta->activo->codigo_activo }}</strong></td>
                <td>1</td>
                <td class="left">
                    {{ $acta->activo->nombre }} 
                    {{ $acta->activo->marca ? '- ' . $acta->activo->marca : '' }} 
                    {{ $acta->activo->modelo ? $acta->activo->modelo : '' }}
                </td>
                <td>{{ $acta->motivo ?? 'Obsolescencia' }}</td>
            </tr>
            @else
            <tr>
                <td colspan="5" style="text-align: center;">No hay activo asociado a esta acta.</td>
            </tr>
            @endif
        </tbody>
    </table>

    {{-- ================= CAUSALES Y FOTO ================= --}}
    <div class="causales-wrapper">
        <!-- Columna Izquierda: Causales -->
        <div class="causales-col">
            <div class="causales-title">CAUSAL: marque "X" en el recuadro</div>
            <table class="causales-table">
                <tr>
                    <td style="width: 80%;">a) Disposición definitiva de bienes</td>
                    <td class="marca">{{ $acta->causal_baja === 'a) Disposición definitiva de bienes' ? 'X' : '' }}</td>
                </tr>
                <tr>
                    <td>b) Hurto, robo o pérdida fortuita</td>
                    <td class="marca">{{ $acta->causal_baja === 'b) Hurto, robo o pérdida fortuita' ? 'X' : '' }}</td>
                </tr>
                <tr>
                    <td>c) Inutilización</td>
                    <td class="marca">{{ $acta->causal_baja === 'c) Inutilización' ? 'X' : '' }}</td>
                </tr>
                <tr>
                    <td>d) Obsolescencia</td>
                    <td class="marca">{{ $acta->causal_baja === 'd) Obsolescencia' ? 'X' : '' }}</td>
                </tr>
            </table>
        </div>

        <!-- Columna Derecha: Fotos en Horizontal (Usando Tabla para DomPDF) -->
        <div class="foto-col">
            <div class="causales-title" style="text-align: center; margin-bottom: 6px;">FOTOGRAFÍAS DE EVIDENCIA:</div>

            <table class="fotos-tabla">
                <tr>
                    @for($i = 1; $i <= 3; $i++)
                        <td>
                            <div class="foto-item">
                                @if(isset($fotosEvidenciaDataUri[$i]) && $fotosEvidenciaDataUri[$i])
                                    <img src="{{ $fotosEvidenciaDataUri[$i] }}" alt="Foto {{ $i }}">
                                @else
                                    <div class="foto-empty">Sin foto</div>
                                @endif
                                <div class="foto-label-item">Foto {{ $i }}</div>
                            </div>
                        </td>
                    @endfor
                </tr>
            </table>
            
            <div class="foto-label" style="text-align: center; margin-top: 6px;">Evidencia del estado actual del bien</div>
        </div>
    </div>

    {{-- ================= TOTAL ================= --}}
    <div>
        <span style="font-weight: bold; font-size: 11px;">TOTAL ITEMS ASIGNADOS</span>
        <div class="total-box">1</div>
    </div>

    {{-- ================= FIRMAS ================= --}}
    <table class="firmas-table">
        <tr>
            <td>
                <div class="firma-line">
                    <strong>RESPONSABLE DEL BIEN DE USO</strong><br>
                    <span style="font-size: 9px;">DIRECCIÓN ADMINISTRATIVA</span>
                </div>
            </td>
            <td>
                <div class="firma-line">
                    <strong>RESPONSABLE CUSTODIO TEMPORAL</strong><br>
                    <span style="font-size: 9px;">JEFE DE CARRERA / UNIDAD</span>
                </div>
            </td>
            <td>
                <div class="firma-line">
                    <strong>RESPONSABLE DE INVENTARIOS</strong><br>
                    <span style="font-size: 9px;">DIRECCIÓN ADMINISTRATIVA</span>
                </div>
            </td>
        </tr>
    </table>

    {{-- ================= DATOS RESPONSABLES ================= --}}
    <table class="datos-resp-table">
        <tr>
            <td class="label">NOMBRE Y APELLIDOS</td>
            <td>{{ $acta->responsable_nombre ?? ($acta->activo->custodio?->nombre_completo ?? 'SIN CUSTODIO') }}</td>
            <td class="label">C.I.</td>
            <td>{{ $acta->responsable_ci ?? ($acta->activo->custodio?->ci ?? '—') }}</td>
        </tr>
        <tr>
            <td class="label">CARGO: JEFE DE CARRERA</td>
            <td>{{ $acta->activo->ambiente?->carrera?->jefe_nombre ?? '—' }}</td>
            <td class="label">C.I.</td>
            <td>{{ $acta->activo->ambiente?->carrera?->jefe_ci ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">UNIDAD / AMBIENTE</td>
            <td>{{ $acta->activo->ambiente?->nombre ?? '—' }}</td>
            <td class="label">FECHA DE BAJA</td>
            <td>{{ \Carbon\Carbon::parse($acta->fecha_baja ?? $acta->created_at)->format('d/m/Y') }}</td>
        </tr>
    </table>

    {{-- ================= NOTA FINAL ================= --}}
    <div class="footer-nota">
        <strong>IMPORTANTE:</strong> Como responsable del bien de uso me comprometo a dar cumplimiento a lo estipulado en el D.S. 0181 Art. 236 (PROCEDIMIENTOS) Parágrafo II. La baja por disposición definitiva de acuerdo al Art. 234 (PROCEDIMIENTOS) Parágrafo II. La baja por disposición definitiva de la nota: COMO RESPONSABLE DEL ACTIVO FIJO DEBE ELABORAR INFORME TECNICO ADJUNTO AL ACTA DEBIDAMENTE FIRMADO Y SELLADO.
    </div>

</body>
</html>