<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        body { font-family: Arial, sans-serif; font-size: 9pt; }
        table { border-collapse: collapse; width: 100%; }
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
        .foto-img { width: 60px; height: 60px; object-fit: cover; border: 1px solid #999; }
    </style>
</head>
<body>

<table border="0" cellpadding="3" cellspacing="0">

    {{-- ============================================================ --}}
    {{-- ENCABEZADO CON LOGO --}}
    {{-- ============================================================ --}}
    <tr>
        <td rowspan="3" class="text-center valign-middle" style="width: 80px;">
            @if(file_exists(public_path('logo.png')))
                <img src="{{ public_path('logo.png') }}" width="70" height="70" alt="Logo">
            @elseif(file_exists(public_path('images/logo.png')))
                <img src="{{ public_path('images/logo.png') }}" width="70" height="70" alt="Logo">
            @else
                <span style="font-size: 7pt; color: #999;">[LOGO]</span>
            @endif
        </td>
        <td colspan="4" class="bold text-center valign-middle" style="font-size: 11pt; height: 25px;">
            INSTITUTO TECNOLÓGICO "PUERTO DE MEJILLONES"
        </td>
        <td colspan="2" class="bold text-center border-all bg-gray valign-middle" style="font-size: 9pt;">
            FORMULARIO N° 1
        </td>
    </tr>
    <tr>
        <td colspan="4" class="text-center valign-middle" style="font-size: 9pt;">
            DIRECCIÓN ADMINISTRATIVA
        </td>
        <td colspan="2" class="text-center border-all valign-middle" style="font-size: 7pt;">
            HOJA N° 1 de 1
        </td>
    </tr>
    <tr>
        <td colspan="4" class="bold text-center valign-middle" style="font-size: 10pt; height: 20px;">
            ACTA DE ENTREGA Y RECEPCIÓN
        </td>
        <td colspan="2" class="text-center border-all valign-middle" style="font-size: 8pt;">
            FECHA: {{ date('d/m/Y') }}
        </td>
    </tr>

    {{-- Espaciador --}}
    <tr><td colspan="7" style="height: 8px;"></td></tr>

    {{-- ============================================================ --}}
    {{-- TEXTO LEGAL --}}
    {{-- ============================================================ --}}
    <tr>
        <td colspan="7" class="border-all text-left valign-middle" style="font-size: 8pt; padding: 6px; text-align: justify;">
            En cumplimiento al D.S. 0181 Normas Básicas del Sistema de Administración de Bienes y Servicios en su Art. 146, 147, 157 (Asignación de Activos Fijos Muebles), la Dirección Administrativa del Instituto Tecnológico "Puerto de Mejillones", procedió a la verificación y asignación de los siguientes activos.
        </td>
    </tr>

    {{-- Espaciador --}}
    <tr><td colspan="7" style="height: 8px;"></td></tr>

    {{-- ============================================================ --}}
    {{-- DATOS DEL CUSTODIO --}}
    {{-- ============================================================ --}}
    @php $first = $activos->first(); @endphp

    <tr>
        <td class="bold bg-gray border-all valign-middle text-left" style="width: 80px; height: 22px;">CUSTODIO:</td>
        <td colspan="2" class="border-all valign-middle text-left text-format" style="width: 200px;">{{ $first?->custodioActual?->nombre_completo ?? 'N/A' }}</td>
        <td class="bold bg-gray border-all valign-middle text-center" style="width: 60px;">C.I.:</td>
        <td colspan="3" class="border-all valign-middle text-left text-format" style="width: 250px;">{{ $first?->custodioActual?->ci ?? 'N/A' }}</td>
    </tr>
    <tr>
        <td class="bold bg-gray border-all valign-middle text-left" style="height: 22px;">CARGO:</td>
        <td colspan="2" class="border-all valign-middle text-left text-format">{{ $first?->custodioActual?->cargo ?? 'N/A' }}</td>
        <td class="bold bg-gray border-all valign-middle text-center">UNIDAD:</td>
        <td colspan="3" class="border-all valign-middle text-left text-format">{{ $first?->custodioActual?->unidad_organizacional ?? $first?->custodioActual?->unidad ?? 'N/A' }}</td>
    </tr>
    <tr>
        <td class="bold bg-gray border-all valign-middle text-left" style="height: 22px;">AMBIENTE:</td>
        <td colspan="2" class="border-all valign-middle text-left text-format">{{ $first?->ambiente?->nombre ?? 'N/A' }}</td>
        <td class="bold bg-gray border-all valign-middle text-center">CARRERA:</td>
        <td colspan="3" class="border-all valign-middle text-left text-format">{{ $first?->ambiente?->carrera?->nombre ?? 'N/A' }}</td>
    </tr>

    {{-- Espaciador --}}
    <tr><td colspan="7" style="height: 8px;"></td></tr>

    {{-- ============================================================ --}}
    {{-- TABLA PRINCIPAL DE ACTIVOS --}}
    {{-- ============================================================ --}}
    <thead>
        <tr class="bg-blue bold text-center valign-middle" style="height: 25px;">
            <th class="border-all text-center" style="width: 40px;">ITEM</th>
            <th class="border-all text-center" style="width: 100px;">CÓDIGO</th>
            <th class="border-all text-center" style="width: 70px;">FUENTE</th>
            <th class="border-all text-center" style="width: 45px;">EST.</th>
            <th class="border-all text-center" style="width: 40px;">CANT.</th>
            <th class="border-all text-center" style="width: 280px;">DESCRIPCIÓN Y ESPECIFICACIONES</th>
            <th class="border-all text-center" style="width: 200px;">FOTOGRAFÍAS</th>
        </tr>
    </thead>
    <tbody>
        @foreach($activos as $index => $act)
            <tr>
                <td class="border-all text-center valign-middle" style="height: 75px;">{{ $index + 1 }}</td>
                <td class="border-all bold text-center valign-middle text-format">
                    {{ $act->codigo_activo }}<br>
                    <span style="font-size: 7pt; font-weight: normal; color: #666;">{{ $act->numero_serie ?? 'S/N' }}</span>
                </td>
                <td class="border-all text-center valign-middle">{{ $act->fuente?->codigo ?? $act->fuenteFinanciamiento?->codigo ?? 'GAEDP' }}</td>
                <td class="border-all text-center valign-middle">{{ $act->estado_fisico }}</td>
                <td class="border-all text-center valign-middle">1</td>
                <td class="border-all text-left valign-middle text-format" style="padding: 5px;">
                    <strong>{{ $act->nombre }}</strong><br>
                    MARCA: {{ $act->marca ?? '—' }} | MODELO: {{ $act->modelo ?? '—' }}<br>
                    <span style="font-size: 7pt; color: #555;">{{ $act->descripcion }}</span>
                </td>
                <td class="border-all text-center valign-middle" style="padding: 3px;">
                    <table border="0" cellpadding="1" cellspacing="0" style="width: 100%;">
                        <tr>
                            {{-- FOTO FRONTAL --}}
                            <td style="text-align: center; width: 33%;">
                                @if($act->imagen && file_exists(public_path('storage/' . $act->imagen)))
                                    <img src="{{ public_path('storage/' . $act->imagen) }}" class="foto-img" alt="Frontal">
                                @else
                                    <span style="font-size: 6pt; color: #888;">Sin foto</span>
                                @endif
                                <div style="font-size: 6pt; color: #666; font-weight: bold;">FRONTAL</div>
                            </td>

                            {{-- FOTO LATERAL --}}
                            <td style="text-align: center; width: 33%;">
                                @if($act->imagen_lateral && file_exists(public_path('storage/' . $act->imagen_lateral)))
                                    <img src="{{ public_path('storage/' . $act->imagen_lateral) }}" class="foto-img" alt="Lateral">
                                @else
                                    <span style="font-size: 6pt; color: #888;">Sin foto</span>
                                @endif
                                <div style="font-size: 6pt; color: #666; font-weight: bold;">LATERAL</div>
                            </td>

                            {{-- FOTO DETALLE --}}
                            <td style="text-align: center; width: 33%;">
                                @if($act->imagen_detalle && file_exists(public_path('storage/' . $act->imagen_detalle)))
                                    <img src="{{ public_path('storage/' . $act->imagen_detalle) }}" class="foto-img" alt="Detalle">
                                @else
                                    <span style="font-size: 6pt; color: #888;">Sin foto</span>
                                @endif
                                <div style="font-size: 6pt; color: #666; font-weight: bold;">DETALLE</div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        @endforeach
    </tbody>

    {{-- Espaciador --}}
    <tr><td colspan="7" style="height: 8px;"></td></tr>

    {{-- ============================================================ --}}
    {{-- OBSERVACIONES --}}
    {{-- ============================================================ --}}
    <tr>
        <td class="bold bg-gray border-all valign-middle text-left" style="height: 22px;">OBSERVACIONES:</td>
        <td colspan="6" class="border-all valign-middle text-left" style="padding-left: 5px;">
            ASIGNACIÓN PROVISIONAL / DEFINITIVA DE ACTIVOS FIJOS SEGÚN REGISTRO SISTEMA DE INVENTARIOS.
        </td>
    </tr>

    {{-- Espaciador para firmas --}}
    <tr><td colspan="7" style="height: 45px;"></td></tr>

    {{-- ============================================================ --}}
    {{-- BLOQUE DE FIRMAS (3 columnas) --}}
    {{-- ============================================================ --}}
    <tr>
        {{-- FIRMA 1 --}}
        <td colspan="2" class="bold text-center valign-top" style="border-top: 1px solid #000000; font-size: 8pt; height: 60px; padding-top: 5px;">
            RESPONSABLE DEL BIEN EN USO / CUSTODIO<br>
            <span style="font-weight: normal; font-size: 7pt;">
                Nombre: {{ $first?->custodioActual?->nombre_completo ?? '—' }}<br>
                C.I.: {{ $first?->custodioActual?->ci ?? '—' }}<br>
                Cargo: {{ $first?->custodioActual?->cargo ?? '—' }}
            </span>
        </td>

        {{-- FIRMA 2 --}}
        <td colspan="2" class="bold text-center valign-top" style="border-top: 1px solid #000000; font-size: 8pt; height: 60px; padding-top: 5px;">
            RESPONSABLE ANTERIOR / ENTREGA<br>
            <span style="font-weight: normal; font-size: 7pt;">
                Nombre: ____________________<br>
                C.I.: ____________________<br>
                Cargo: ____________________
            </span>
        </td>

        {{-- FIRMA 3 --}}
        <td colspan="3" class="bold text-center valign-top" style="border-top: 1px solid #000000; font-size: 8pt; height: 60px; padding-top: 5px;">
            RESPONSABLE DE INVENTARIACIÓN / SABS<br>
            <span style="font-weight: normal; font-size: 7pt;">
                Lic. Ana Lía Zapana Cortez<br>
                C.I.: 4756925<br>
                Directora Administrativa<br>
                INSTITUTO TECNOLÓGICO "PUERTO DE MEJILLONES"
            </span>
        </td>
    </tr>

    {{-- Espaciador --}}
    <tr><td colspan="7" style="height: 12px;"></td></tr>

    {{-- ============================================================ --}}
    {{-- NOTA DE RESPONSABILIDAD --}}
    {{-- ============================================================ --}}
    <tr>
        <td colspan="7" class="text-left" style="font-size: 7pt; font-style: italic; padding: 5px; border-top: 1px solid #ccc;">
            <strong>IMPORTANTE:</strong> Como responsable del bien de uso me comprometo a dar cumplimiento al D.S.0181 Art. 116 (RESPONSABILIDAD POR EL MANEJO DE BIENES) Parágrafo III. Todos los servidores públicos son responsables por el debido uso, custodia, preservación y demanda de servicios de mantenimiento de los bienes que les fueran asignados.<br><br>
            <strong>Art. 157 (PROHIBICIONES PARA LOS FUNCIONARIOS PÚBLICOS SOBRE EL USO DE ACTIVOS FIJOS MUEBLES)</strong> Inc. A, b, c, d, e, f, g, h.<br><br>
            <strong>NOTA:</strong> COMO RESPONSABLE DEL USO Y CUSTODIO ME COMPROMETO CUMPLIR EL INCISO c : PRESTAR O TRANSFERIR EL BIEN A OTRO EMPLEADO PÚBLICO.
        </td>
    </tr>

</table>

</body>
</html>
