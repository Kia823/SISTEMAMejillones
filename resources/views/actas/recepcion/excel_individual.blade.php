<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Acta de Recepción — {{ $activo->codigo_activo }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 9pt; color: #000; }
        table { border-collapse: collapse; width: 100%; table-layout: fixed; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .border-all { border: 1px solid #000000; }
        .bg-gray { background-color: #f2f2f2; }
        .bg-blue { background-color: #1e3a8a; color: #ffffff; }
        .valign-middle { vertical-align: middle; }
        .valign-top { vertical-align: top; }
        .text-format { mso-number-format: "\@"; }
        .foto-img { width: 100px; height: 100px; object-fit: cover; border: 1px solid #999; }
    </style>
</head>
<body>

<table border="0" cellpadding="4" cellspacing="0">

    {{-- ============================================================ --}}
    {{-- ENCABEZADO CON LOGO --}}
    {{-- ============================================================ --}}
    <tr>
        <td rowspan="3" class="text-center valign-middle border-all" style="width: 90px; height: 75px;">
            @if(file_exists(public_path('logo.png')))
                <img src="{{ public_path('logo.png') }}" width="65" height="65" alt="Logo">
            @elseif(file_exists(public_path('images/logo.png')))
                <img src="{{ public_path('images/logo.png') }}" width="65" height="65" alt="Logo">
            @else
                <span style="font-size: 7pt; color: #999;">[LOGO]</span>
            @endif
        </td>
        <td colspan="3" class="bold text-center valign-middle border-all" style="font-size: 11pt; height: 25px;">
            INSTITUTO TECNOLÓGICO "PUERTO DE MEJILLONES"
        </td>
        <td class="bold text-center border-all bg-gray valign-middle" style="width: 100px; font-size: 9pt;">
            FORMULARIO N° 1
        </td>
    </tr>
    <tr>
        <td colspan="3" class="text-center valign-middle border-all" style="font-size: 9pt; height: 20px;">
            DIRECCIÓN ADMINISTRATIVA
        </td>
        <td class="text-center border-all valign-middle" style="font-size: 8pt;">
            HOJA N° 1 de 1
        </td>
    </tr>
    <tr>
        <td colspan="3" class="bold text-center valign-middle border-all" style="font-size: 10pt; height: 20px;">
            ACTA DE ENTREGA Y RECEPCIÓN INDIVIDUAL
        </td>
        <td class="text-center border-all valign-middle" style="font-size: 8pt;">
            FECHA: {{ date('d/m/Y') }}
        </td>
    </tr>

    <tr><td colspan="5" style="height: 10px;"></td></tr>

    {{-- ============================================================ --}}
    {{-- TEXTO LEGAL --}}
    {{-- ============================================================ -->
    <tr>
        <td colspan="5" class="border-all text-left valign-middle" style="font-size: 8.5pt; padding: 8px; text-align: justify; background-color: #fafafa;">
            En cumplimiento al D.S. 0181 Normas Básicas del Sistema de Administración de Bienes y Servicios en su Art. 146, 147, 157 (Asignación de Activos Fijos Muebles), la Dirección Administrativa del Instituto Tecnológico "Puerto de Mejillones", procedió a la verificación y asignación del siguiente activo.
        </td>
    </tr>

    <tr><td colspan="5" style="height: 10px;"></td></tr>

    {{-- ============================================================ --}}
    {{-- DATOS DEL CUSTODIO --}}
    {{-- ============================================================ --}}
    <tr>
        <td class="bold bg-gray border-all valign-middle text-left" style="width: 130px; height: 22px;">CUSTODIO:</td>
        <td colspan="2" class="border-all valign-middle text-left text-format">{{ $activo->custodio?->nombre_completo ?? 'N/A' }}</td>
        <td class="bold bg-gray border-all valign-middle text-center" style="width: 90px;">C.I.:</td>
        <td class="border-all valign-middle text-left text-format" style="width: 140px;">{{ $activo->custodio?->ci ?? 'N/A' }}</td>
    </tr>
    <tr>
        <td class="bold bg-gray border-all valign-middle text-left" style="height: 22px;">CARGO:</td>
        <td colspan="2" class="border-all valign-middle text-left text-format">{{ $activo->custodio?->cargo ?? 'N/A' }}</td>
        <td class="bold bg-gray border-all valign-middle text-center">UNIDAD:</td>
        <td class="border-all valign-middle text-left text-format">{{ $activo->custodio?->unidad ?? 'N/A' }}</td>
    </tr>
    <tr>
        <td class="bold bg-gray border-all valign-middle text-left" style="height: 22px;">AMBIENTE:</td>
        <td colspan="2" class="border-all valign-middle text-left text-format">{{ $activo->ambiente?->nombre ?? 'N/A' }}</td>
        <td class="bold bg-gray border-all valign-middle text-center">CARRERA:</td>
        <td class="border-all valign-middle text-left text-format">{{ $activo->ambiente?->carrera?->nombre ?? 'N/A' }}</td>
    </tr>

    <tr><td colspan="5" style="height: 10px;"></td></tr>

    {{-- ============================================================ --}}
    {{-- DATOS DEL ACTIVO --}}
    {{-- ============================================================ --}}
    <tr>
        <td colspan="5" class="bold text-center valign-middle bg-blue" style="height: 25px; font-size: 10pt; text-transform: uppercase;">
            DATOS TÉCNICOS DEL ACTIVO
        </td>
    </tr>

    <tr>
        <td class="bold bg-gray border-all valign-middle text-left" style="height: 22px;">CÓDIGO:</td>
        <td colspan="2" class="border-all valign-middle text-left text-format" style="color: #dc2626; font-weight: bold; font-size: 10pt;">
            {{ $activo->codigo_activo }}
        </td>
        <td class="bold bg-gray border-all valign-middle text-center">SERIE:</td>
        <td class="border-all valign-middle text-left text-format">{{ $activo->numero_serie ?? '—' }}</td>
    </tr>
    <tr>
        <td class="bold bg-gray border-all valign-middle text-left" style="height: 22px;">NOMBRE:</td>
        <td colspan="4" class="border-all valign-middle text-left text-format bold">
            {{ $activo->nombre }}
        </td>
    </tr>
    <tr>
        <td class="bold bg-gray border-all valign-middle text-left" style="height: 35px;">DESCRIPCIÓN:</td>
        <td colspan="4" class="border-all valign-middle text-left text-format" style="padding: 6px;">
            {{ $activo->descripcion ?? 'Sin descripción registrada.' }}
        </td>
    </tr>
    <tr>
        <td class="bold bg-gray border-all valign-middle text-left" style="height: 22px;">MARCA:</td>
        <td colspan="2" class="border-all valign-middle text-left text-format">{{ $activo->marca ?? '—' }}</td>
        <td class="bold bg-gray border-all valign-middle text-center">MODELO:</td>
        <td class="border-all valign-middle text-left text-format">{{ $activo->modelo ?? '—' }}</td>
    </tr>
    <tr>
        <td class="bold bg-gray border-all valign-middle text-left" style="height: 22px;">CATEGORÍA:</td>
        <td colspan="2" class="border-all valign-middle text-left text-format">{{ $activo->categoria?->nombre ?? '—' }}</td>
        <td class="bold bg-gray border-all valign-middle text-center">ESTADO:</td>
        <td class="border-all valign-middle text-left text-format bold" style="color: #10b981;">
            {{ $activo->estado_fisico_texto ?? '—' }}
        </td>
    </tr>
    <tr>
        <td class="bold bg-gray border-all valign-middle text-left" style="height: 22px;">FUENTE:</td>
        <td colspan="2" class="border-all valign-middle text-left text-format">{{ $activo->fuente?->nombre ?? '—' }}</td>
        <td class="bold bg-gray border-all valign-middle text-center">GESTIÓN:</td>
        <td class="border-all valign-middle text-left text-format">{{ $activo->gestion ?? now()->format('Y') }}</td>
    </tr>
    <tr>
        <td class="bold bg-gray border-all valign-middle text-left" style="height: 22px;">VALOR (Bs):</td>
        <td colspan="2" class="border-all valign-middle text-left text-format bold" style="color: #1d4ed8;">
            Bs. {{ number_format((float)($activo->valor_adquisicion ?? 0), 2) }}
        </td>
        <td class="bold bg-gray border-all valign-middle text-center">FECHA ADQ.:</td>
        <td class="border-all valign-middle text-left text-format">
            {{ $activo->fecha_adquisicion ? $activo->fecha_adquisicion->format('d/m/Y') : '—' }}
        </td>
    </tr>

    <tr><td colspan="5" style="height: 10px;"></td></tr>

    {{-- ============================================================ --}}
    {{-- FOTOGRAFÍAS --}}
    {{-- ============================================================ --}}
    <tr>
        <td colspan="5" class="bold text-center valign-middle bg-blue" style="height: 25px; font-size: 10pt; text-transform: uppercase;">
            REGISTRO FOTOGRÁFICO
        </td>
    </tr>
    <tr>
        <td colspan="5" class="border-all text-center valign-middle" style="padding: 10px;">
            <table border="0" cellpadding="5" cellspacing="0" style="width: 100%;">
                <tr>
                    {{-- FRONTAL --}}
                    <td class="text-center valign-middle" style="width: 33%;">
                        <div style="font-size: 8pt; font-weight: bold; margin-bottom: 5px;">📷 VISTA FRONTAL</div>
                        @if($activo->imagen && file_exists(public_path('storage/' . $activo->imagen)))
                            <img src="{{ public_path('storage/' . $activo->imagen) }}" width="110" height="110" style="object-fit: cover; border: 1px solid #999;" alt="Frontal">
                        @else
                            <span style="font-size: 8pt; color: #888; font-style: italic;">[Sin imagen frontal]</span>
                        @endif
                    </td>

                    {{-- LATERAL --}}
                    <td class="text-center valign-middle" style="width: 33%;">
                        <div style="font-size: 8pt; font-weight: bold; margin-bottom: 5px;">📷 VISTA LATERAL</div>
                        @if($activo->imagen_lateral && file_exists(public_path('storage/' . $activo->imagen_lateral)))
                            <img src="{{ public_path('storage/' . $activo->imagen_lateral) }}" width="110" height="110" style="object-fit: cover; border: 1px solid #999;" alt="Lateral">
                        @else
                            <span style="font-size: 8pt; color: #888; font-style: italic;">[Sin imagen lateral]</span>
                        @endif
                    </td>

                    {{-- DETALLE --}}
                    <td class="text-center valign-middle" style="width: 33%;">
                        <div style="font-size: 8pt; font-weight: bold; margin-bottom: 5px;">📷 DETALLE / PLACA</div>
                        @if($activo->imagen_detalle && file_exists(public_path('storage/' . $activo->imagen_detalle)))
                            <img src="{{ public_path('storage/' . $activo->imagen_detalle) }}" width="110" height="110" style="object-fit: cover; border: 1px solid #999;" alt="Detalle">
                        @else
                            <span style="font-size: 8pt; color: #888; font-style: italic;">[Sin imagen detalle]</span>
                        @endif
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <tr><td colspan="5" style="height: 10px;"></td></tr>

    {{-- ============================================================ --}}
    {{-- OBSERVACIONES --}}
    {{-- ============================================================ --}}
    <tr>
        <td class="bold bg-gray border-all valign-middle text-left" style="height: 25px;">OBSERVACIONES:</td>
        <td colspan="4" class="border-all valign-middle text-left" style="padding-left: 6px; font-size: 8.5pt;">
            ASIGNACIÓN PROVISIONAL / DEFINITIVA DE ACTIVOS FIJOS SEGÚN REGISTRO SISTEMA DE INVENTARIOS.
        </td>
    </tr>

    <tr><td colspan="5" style="height: 50px;"></td></tr>

    {{-- ============================================================ --}}
    {{-- FIRMAS --}}
    {{-- ============================================================ --}}
    <tr>
        <td colspan="2" class="bold text-center valign-top" style="border-top: 1px solid #000000; font-size: 8.5pt; height: 70px; padding-top: 6px;">
            RESPONSABLE DEL BIEN EN USO / CUSTODIO<br/>
            <span style="font-weight: normal; font-size: 7.5pt;">
                Nombre: {{ $activo->custodio?->nombre_completo ?? '' }}<br/>
                C.I.: {{ $activo->custodio?->ci ?? '' }}<br/>
                Cargo: {{ $activo->custodio?->cargo ?? '' }}
            </span>
        </td>
        <td></td>
        <td colspan="2" class="bold text-center valign-top" style="border-top: 1px solid #000000; font-size: 8.5pt; padding-top: 6px;">
            RESPONSABLE DE INVENTARIACIÓN / SABS<br/>
            <span style="font-weight: normal; font-size: 7.5pt;">
                Lic. Ana Lía Zapana Cortez<br/>
                C.I.: 4756925<br/>
                Directora Administrativa<br/>
                INSTITUTO TECNOLÓGICO "PUERTO DE MEJILLONES"
            </span>
        </td>
    </tr>

    <tr><td colspan="5" style="height: 20px;"></td></tr>

    {{-- ============================================================ --}}
    {{-- NOTA LEGAL --}}
    {{-- ============================================================ --}}
    <tr>
        <td colspan="5" class="text-left" style="font-size: 7pt; font-style: italic; border-top: 1px solid #ccc; padding-top: 8px;">
            <strong>IMPORTANTE:</strong> Como responsable del bien de uso me comprometo a dar cumplimiento al D.S.0181 Art. 116 (RESPONSABILIDAD POR EL MANEJO DE BIENES) Parágrafo III. Todos los servidores públicos son responsables por el debido uso, custodia, preservación y demanda de servicios de mantenimiento de los bienes que les fueran asignados.<br><br>
            <strong>Art. 157 (PROHIBICIONES PARA LOS FUNCIONARIOS PÚBLICOS SOBRE EL USO DE ACTIVOS FIJOS MUEBLES)</strong> Inc. A, b, c, d, e, f, g, h.<br><br>
            <strong>NOTA:</strong> COMO RESPONSABLE DEL USO Y CUSTODIO ME COMPROMETO CUMPLIR EL INCISO c : PRESTAR O TRANSFERIR EL BIEN A OTRO EMPLEADO PÚBLICO.
        </td>
    </tr>

</table>

</body>
</html>
