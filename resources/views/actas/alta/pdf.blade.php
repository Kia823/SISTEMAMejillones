<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acta de Entrega y Recepción — {{ $activo->codigo_activo }}</title>
    <style>
        /* ============ FORMATO HORIZONTAL (LANDSCAPE) ============ */
        @page { size: 330mm 216mm; margin: 10mm 12mm; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9.5px;
            color: #000;
            line-height: 1.35;
            background: #fff;
        }

        /* ============ ENCABEZADO INSTITUCIONAL ============ */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        .header-table td {
            vertical-align: middle;
        }
        .logo-box {
            width: 60px;
            height: 45px;
            text-align: center;
            font-size: 24px;
        }
        .titulo-instituto {
            text-align: center;
        }
        .titulo-instituto h2 {
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 2px;
        }
        .titulo-instituto h3 {
            font-size: 11px;
            font-weight: normal;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        .titulo-instituto .acta-sub {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 1px solid #000;
            display: inline-block;
            padding-bottom: 1px;
            margin-bottom: 2px;
        }
        .titulo-instituto .fecha-acta {
            font-size: 10px;
            font-weight: bold;
        }

        .cajas-derecha {
            width: 180px;
            text-align: right;
            vertical-align: top;
        }
        .caja-form, .caja-hoja {
            border: 1.5px solid #000;
            padding: 3px 8px;
            font-size: 9.5px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 4px;
            background: #fff;
        }

        /* ============ TEXTO INFORMATIVO ============ */
        .info-cumplimiento {
            border: 1px solid #000;
            padding: 6px 8px;
            font-size: 9px;
            margin-bottom: 8px;
            text-align: justify;
            background: #fff;
        }

        /* ============ TABLA PRINCIPAL DE ÍTEMS ============ */
        table.tabla-principal {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
            font-size: 9.5px;
        }
        table.tabla-principal th, table.tabla-principal td {
            border: 1px solid #000;
            padding: 5px 6px;
            vertical-align: top;
        }
        table.tabla-principal th {
            background: #f1f1f1;
            text-align: center;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .col-item { width: 45px; text-align: center; }
        .col-codigo { width: 110px; text-align: center; }
        .col-fuente { width: 75px; text-align: center; }
        .col-est { width: 45px; text-align: center; }
        .col-cant { width: 50px; text-align: center; }
        .col-transferencia { width: 100px; text-align: center; }

        /* ============ CONTENEDOR DE LAS 3 IMÁGENES DENTRO DE LA TABLA ============ */
        .imagenes-row {
            margin-top: 8px;
            text-align: center;
            width: 100%;
        }
        .img-table {
            width: 100%;
            border-collapse: collapse;
        }
        .img-table td {
            border: 1px dashed #666;
            padding: 4px;
            text-align: center;
            width: 33.33%;
            background: #fafafa;
        }
        .img-box {
            width: 100%;
            height: 110px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .img-box img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        .img-label {
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 3px;
            color: #333;
        }

        /* ============ ÍTEMS ASIGNADOS ============ */
        .tabla-asignados {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            font-size: 9.5px;
        }
        .tabla-asignados td {
            border: 1px solid #000;
            padding: 4px 8px;
        }
        .tabla-asignados .label-asig {
            width: 110px;
            font-weight: bold;
            background: #f8f8f8;
        }

        /* ============ BLOQUE DE FIRMAS ============ */
        .tabla-firmas {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        .tabla-firmas td {
            border: 1px solid #000;
            vertical-align: top;
            padding: 6px;
            width: 50%;
        }
        .espacio-firma {
            height: 55px;
        }
        .linea-firma {
            border-top: 1px solid #000;
            text-align: center;
            padding-top: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        .cargo-firma {
            text-align: center;
            font-size: 8.5px;
            color: #333;
        }

        /* ============ OBSERVACIONES Y TEXTO LEGAL ============ */
        .tabla-obs {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
            font-size: 9px;
        }
        .tabla-obs td {
            border: 1px solid #000;
            padding: 4px 6px;
        }
        .texto-legal {
            font-size: 7.5px;
            margin-top: 6px;
            text-align: justify;
            line-height: 1.3;
        }
    </style>
</head>
<body>

    {{-- ============================================================ --}}
    {{-- ENCABEZADO --}}
    {{-- ============================================================ --}}
    <table class="header-table">
        <tr>
            <td class="logo-box">
                @if(file_exists(public_path('images/logo.png')))
                    <img src="{{ public_path('images/logo.png') }}" alt="Logo" style="max-height: 40px; max-width: 50px;">
                @else
                    🏛️
                @endif
            </td>
            <td class="titulo-instituto">
                <h2>INSTITUTO TECNOLÓGICO "PUERTO DE MEJILLONES"</h2>
                <h3>DIRECCIÓN ADMINISTRATIVA</h3>
                <div class="acta-sub">ACTA DE ENTREGA Y RECEPCIÓN</div>
                <div class="fecha-acta">{{ now()->translatedFormat('d \d\e F \d\e Y') }}</div>
            </td>
            <td class="cajas-derecha">
                <div class="caja-form">FORMULARIO N° 1</div>
                <div class="caja-hoja">HOJA N° 1 de 1</div>
            </td>
        </tr>
    </table>

    {{-- TEXTO DE CUMPLIMIENTO --}}
    <div class="info-cumplimiento">
        En cumplimiento al D.S. 0181 Normas Básicas del Sistema de Bienes y Servicios en su Art. 146, 147, 157 (Asignación de Activos Fijos Muebles), la sección de Activos Fijos del Instituto Tecnológico "Puerto de Mejillones", procedió a la verificación y asignación de los siguientes activos:
    </div>

    {{-- ============================================================ --}}
    {{-- TABLA PRINCIPAL DE ACTIVOS --}}
    {{-- ============================================================ --}}
    <table class="tabla-principal">
        <thead>
            <tr>
                <th class="col-item">ITEM</th>
                <th class="col-codigo">CODIGO</th>
                <th class="col-fuente">FUENTE</th>
                <th class="col-est">EST.</th>
                <th class="col-cant">CANT.</th>
                <th>DESCRIPCIÓN</th>
                <th class="col-transferencia">TRANSFERENCIA</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="col-item" style="text-align: center; font-weight: bold;">1</td>
                <td class="col-codigo" style="font-weight: bold; text-align: center;">{{ $activo->codigo_activo }}</td>
                <td class="col-fuente" style="text-align: center;">{{ $activo->fuente->codigo ?? 'GTEL' }}</td>
                <td class="col-est" style="text-align: center; font-weight: bold;">{{ $activo->estado_fisico ?? 'B' }}</td>
                <td class="col-cant" style="text-align: center;">1</td>
                <td>
                    <strong style="text-transform: uppercase;">{{ $activo->nombre }}</strong>,
                    MARCA: {{ $activo->marca ?? '—' }}, MODELO: {{ $activo->modelo ?? '—' }},
                    SERIE: {{ $activo->numero_serie ?? 'S/N' }}.
                    {{ $activo->descripcion ?? '' }}

                    {{-- 3 FOTOS UBICADAS EXACTAMENTE EN LA POSICIÓN DE LA IMAGEN OFICIAL --}}
                    <div class="imagenes-row">
                        <table class="img-table">
                            <tr>
                                <td>
                                    <div class="img-box">
                                        @if($activo->imagen && file_exists(public_path('storage/' . $activo->imagen)))
                                            <img src="{{ public_path('storage/' . $activo->imagen) }}" alt="Frontal">
                                        @elseif($activo->imagen)
                                            <img src="{{ asset('storage/' . $activo->imagen) }}" alt="Frontal">
                                        @else
                                            <span style="font-size:8px; color:#777;">Sin foto frontal</span>
                                        @endif
                                    </div>
                                    <div class="img-label">Vista Frontal</div>
                                </td>
                                <td>
                                    <div class="img-box">
                                        @if($activo->imagen_lateral && file_exists(public_path('storage/' . $activo->imagen_lateral)))
                                            <img src="{{ public_path('storage/' . $activo->imagen_lateral) }}" alt="Lateral">
                                        @elseif($activo->imagen_lateral)
                                            <img src="{{ asset('storage/' . $activo->imagen_lateral) }}" alt="Lateral">
                                        @else
                                            <span style="font-size:8px; color:#777;">Sin foto lateral</span>
                                        @endif
                                    </div>
                                    <div class="img-label">Vista Lateral</div>
                                </td>
                                <td>
                                    <div class="img-box">
                                        @if($activo->imagen_detalle && file_exists(public_path('storage/' . $activo->imagen_detalle)))
                                            <img src="{{ public_path('storage/' . $activo->imagen_detalle) }}" alt="Detalle">
                                        @elseif($activo->imagen_detalle)
                                            <img src="{{ asset('storage/' . $activo->imagen_detalle) }}" alt="Detalle">
                                        @else
                                            <span style="font-size:8px; color:#777;">Sin foto placa/detalle</span>
                                        @endif
                                    </div>
                                    <div class="img-label">Placa / Detalle</div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
                <td class="col-transferencia" style="text-align: center; font-size: 8.5px; color: #555;">—</td>
            </tr>
        </tbody>
    </table>

    {{-- ÍTEMS ASIGNADOS --}}
    <table class="tabla-asignados">
        <tr>
            <td style="width: 150px; font-weight: bold; background: #f1f1f1;">ITEMS ASIGNADOS</td>
            <td style="width: 50px; text-align: center; font-weight: bold;">1</td>
            <td></td>
        </tr>
    </table>

    {{-- ============================================================ --}}
    {{-- SECCIÓN DE FIRMAS Y RESPONSABLES --}}
    {{-- ============================================================ --}}
    <table class="tabla-firmas">
        <tr>
            <td>
                <div class="espacio-firma"></div>
                <div class="linea-firma">{{ $activo->custodio->nombre_completo ?? 'RESPONSABLE DE BIEN DE USO' }}</div>
                <div class="cargo-firma" style="font-weight: bold;">RESPONSABLE DEL BIEN DE USO</div>
            </td>
            <td>
                <div class="espacio-firma"></div>
                <div class="linea-firma">Lic. Ana Lía Zapana Cortez</div>
                <div class="cargo-firma" style="font-weight: bold;">RESPONSABLE DE INVENTARIACION</div>
            </td>
        </tr>
        <tr>
            <td>
                <div style="font-size: 9px;"><strong>NOMBRE:</strong> {{ $activo->custodio->nombre_completo ?? '—' }}</div>
                <div style="font-size: 9px;"><strong>C.I.:</strong> {{ $activo->custodio->ci ?? '—' }}</div>
                <div style="font-size: 9px;"><strong>CARGO:</strong> Custodio / Docente / Estudiante</div>
                <div style="font-size: 9px;"><strong>UNIDAD:</strong> {{ $activo->ambiente->nombre ?? 'ITPM' }}</div>
            </td>
            <td>
                <div style="font-size: 9px;"><strong>NOMBRE:</strong> Lic. Ana Lía Zapana Cortez</div>
                <div style="font-size: 9px;"><strong>C.I.:</strong> 4756925 LP</div>
                <div style="font-size: 9px;"><strong>CARGO:</strong> DIRECTORA ADMINISTRATIVA</div>
                <div style="font-size: 9px;"><strong>UNIDAD:</strong> DIRECCIÓN ADMINISTRATIVA</div>
            </td>
        </tr>
    </table>

    {{-- OBSERVACIONES Y FECHA --}}
    <table class="tabla-obs">
        <tr>
            <td style="width: 70%;"><strong>Observaciones:</strong> ASIGNACIÓN OFICIAL DE ACTIVO FIJO EN AMBIENTE.</td>
            <td style="width: 30%;"><strong>LIBERACIÓN EN APLICACIÓN AL ART. 148 DE SABS</strong></td>
        </tr>
    </table>

    {{-- TEXTO LEGAL INFERIOR --}}
    <div class="texto-legal">
        <strong>IMPORTANTE:</strong> Como responsable del bien de uso me comprometo a dar cumplimiento al D.S.0181 Art. 116 (RESPONSABILIDAD POR EL MANEJO DE BIENES) Parágrafo III. Todos los Servidores Públicos son responsables por el debido uso, custodia, preservación y demanda de servicios de mantenimiento de los bienes que les fueran asignados, de acuerdo al régimen de Responsabilidad por la Función Pública, establecido en la Ley 1178 y sus reglamentos.<br>
        Art. 157 (PROHIBICIONES PARA LOS FUNCIONARIOS PÚBLICOS SOBRE EL USO DE ACTIVOS FIJOS MUEBLES) Inc. A, b, c, d, e, f, g, h<br>
        <strong>NOTA:</strong> COMO RESPONSABLE DEL USO Y CUSTODIO ME COMPROMETO CUMPLIR EL INCISO c: PRESTAR O TRANSFERIR EL BIEN A OTRO EMPLEADO PUBLICO.
    </div>

</body>
</html>
