<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acta de Recepción — {{ $activo->codigo_activo }}</title>
    <style>
        @page { size: 216mm 330mm; margin: 12mm 10mm; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Helvetica, Arial, sans-serif; font-size: 9px; color: #000; line-height: 1.3; }

        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        .header-table td { vertical-align: middle; }
        .logo-cell { width: 80px; text-align: center; }
        .logo-img { width: 70px; height: 70px; object-fit: contain; }
        .header-center { text-align: center; }
        .title-main { font-weight: bold; font-size: 12px; text-transform: uppercase; }
        .title-sub { font-size: 9px; margin-top: 2px; }
        .title-acta { font-weight: bold; font-size: 10px; margin-top: 4px; text-transform: uppercase; }
        .fecha-acta { font-size: 8px; font-weight: bold; margin-top: 2px; }
        .box-right {
            border: 1px solid #000; padding: 4px 6px; text-align: center;
            font-size: 8px; font-weight: bold; width: 110px;
        }

        .legal-text {
            font-size: 8px; text-align: justify; margin: 8px 0;
            border: 1px solid #000; padding: 5px; background: #fcfcfc; line-height: 1.4;
        }

        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        .info-table th, .info-table td {
            border: 1px solid #000; padding: 4px 6px; font-size: 8px; text-align: left;
        }
        .info-table th { background-color: #f2f2f2; width: 22%; font-weight: bold; }

        .section-title {
            font-size: 9px; font-weight: bold; background: #1e3a8a; color: white;
            padding: 4px 8px; margin: 10px 0 6px 0; text-transform: uppercase;
        }

        .fotos-grid {
            display: table; width: 100%; border-collapse: collapse;
            border: 1px solid #000; margin-bottom: 8px;
        }
        .foto-item {
            display: table-cell; width: 33.33%; padding: 8px;
            text-align: center; vertical-align: middle;
            border-right: 1px solid #ccc;
        }
        .foto-item:last-child { border-right: none; }
        .foto-titulo {
            font-size: 8px; font-weight: bold; color: #333;
            margin-bottom: 5px; text-transform: uppercase;
        }
        .foto-img {
            width: 140px; height: 140px; object-fit: cover;
            border: 2px solid #999; border-radius: 4px;
            display: block; margin: 0 auto;
        }
        .foto-vacia {
            width: 140px; height: 140px; border: 2px dashed #ccc;
            border-radius: 4px; display: flex; align-items: center;
            justify-content: center; margin: 0 auto;
            color: #999; font-size: 8px; font-style: italic;
        }

        .caract-table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        .caract-table th, .caract-table td {
            border: 1px solid #000; padding: 5px 6px; font-size: 8px;
            text-align: left; vertical-align: top;
        }
        .caract-table th { background-color: #f2f2f2; width: 25%; font-weight: bold; }

        .signatures-table { width: 100%; border-collapse: collapse; margin-top: 35px; }
        .signatures-table td {
            width: 50%; padding: 8px; vertical-align: top;
            height: 90px; text-align: center;
        }
        .firma-linea {
            border-top: 1px solid #000; padding-top: 4px;
            font-size: 8px; font-weight: bold; line-height: 1.4;
        }
        .firma-detalle { font-weight: normal; font-size: 7px; color: #333; }

        .footer-note {
            font-size: 7px; margin-top: 12px; text-align: justify;
            line-height: 1.4; border-top: 1px solid #ccc; padding-top: 6px;
        }
    </style>
</head>
<body>

    {{-- ENCABEZADO CON LOGO --}}
    <table class="header-table">
        <tr>
            <td class="logo-cell">
                @if(file_exists(public_path('logo.png')))
                    <img class="logo-img" src="{{ public_path('logo.png') }}" alt="Logo">
                @elseif(file_exists(public_path('images/logo.png')))
                    <img class="logo-img" src="{{ public_path('images/logo.png') }}" alt="Logo">
                @else
                    <div style="font-size: 7px; color: #999;">[LOGO]</div>
                @endif
            </td>

            <td class="header-center">
                <div class="title-main">Instituto Tecnológico "Puerto de Mejillones"</div>
                <div class="title-sub">DIRECCIÓN ADMINISTRATIVA</div>
                <div class="title-acta">ACTA DE ENTREGA Y RECEPCIÓN</div>
                <div class="fecha-acta">{{ now()->translatedFormat('d \d\e F \d\e Y') }}</div>
            </td>

            <td style="width: 110px; text-align: right; vertical-align: top;">
                <div class="box-right">
                    FORMULARIO N° 1<br>
                    <span style="font-weight: normal; font-size: 7px;">HOJA N° 1 de 1</span>
                </div>
            </td>
        </tr>
    </table>

    {{-- TEXTO LEGAL --}}
    <div class="legal-text">
        En cumplimiento al D.S. 0181 Normas Básicas del Sistema de Administración de Bienes y Servicios en su Art. 146, 147, 157
        (Asignación de Activos Fijos Muebles), la Dirección Administrativa del Instituto Tecnológico "Puerto de Mejillones",
        procedió a la verificación y asignación del siguiente activo.
    </div>

    {{-- DATOS DEL CUSTODIO --}}
    <table class="info-table">
        <tr>
            <th>NOMBRE DEL CUSTODIO:</th>
            <td>{{ $activo->custodio?->nombre_completo ?? 'N/A' }}</td>
            <th>C.I.:</th>
            <td>{{ $activo->custodio?->ci ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>CARGO:</th>
            <td>{{ $activo->custodio?->cargo ?? 'N/A' }}</td>
            <th>UNIDAD:</th>
            <td>{{ $activo->custodio?->unidad ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>UBICACIÓN / AMBIENTE:</th>
            <td>{{ $activo->ambiente?->nombre ?? 'N/A' }}</td>
            <th>CARRERA:</th>
            <td>{{ $activo->ambiente?->carrera?->nombre ?? 'N/A' }}</td>
        </tr>
    </table>

    {{-- DATOS DEL ACTIVO --}}
    <div class="section-title">Datos del Activo</div>

    <table class="caract-table">
        <tr>
            <th>Código del Activo:</th>
            <td style="color: #dc2626; font-weight: bold; font-family: 'Courier New', monospace; font-size: 10px;">
                {{ $activo->codigo_activo }}
            </td>
            <th>N° de Serie:</th>
            <td>{{ $activo->numero_serie ?? '—' }}</td>
        </tr>
        <tr>
            <th>Nombre del Activo:</th>
            <td colspan="3">{{ $activo->nombre }}</td>
        </tr>
        <tr>
            <th>Descripción:</th>
            <td colspan="3" style="line-height: 1.5;">{{ $activo->descripcion ?? 'Sin descripción registrada.' }}</td>
        </tr>
        <tr>
            <th>Marca:</th>
            <td>{{ $activo->marca ?? '—' }}</td>
            <th>Modelo:</th>
            <td>{{ $activo->modelo ?? '—' }}</td>
        </tr>
        <tr>
            <th>Categoría:</th>
            <td>{{ $activo->categoria?->nombre ?? '—' }}</td>
            <th>Estado Físico:</th>
            <td style="color: #10b981; font-weight: bold;">{{ $activo->estado_fisico_texto ?? '—' }}</td>
        </tr>
        <tr>
            <th>Fuente de Financiamiento:</th>
            <td>{{ $activo->fuente?->nombre ?? '—' }}</td>
            <th>Gestión:</th>
            <td>{{ $activo->gestion ?? now()->format('Y') }}</td>
        </tr>
        <tr>
            <th>Valor de Adquisición:</th>
            <td style="font-weight: bold; color: #1d4ed8;">
                Bs. {{ number_format((float)($activo->valor_adquisicion ?? 0), 2) }}
            </td>
            <th>Fecha de Adquisición:</th>
            <td>{{ $activo->fecha_adquisicion ? $activo->fecha_adquisicion->format('d/m/Y') : '—' }}</td>
        </tr>
    </table>

    {{-- FOTOGRAFÍAS --}}
    <div class="section-title">Registro Fotográfico</div>

    <div class="fotos-grid">
        <div class="foto-item">
            <div class="foto-titulo">📷 Frontal</div>
            @if($activo->imagen && file_exists(public_path('storage/' . $activo->imagen)))
                <img class="foto-img" src="{{ public_path('storage/' . $activo->imagen) }}" alt="Frontal">
            @else
                <div class="foto-vacia">Sin imagen</div>
            @endif
        </div>

        <div class="foto-item">
            <div class="foto-titulo">📷 Lateral</div>
            @if($activo->imagen_lateral && file_exists(public_path('storage/' . $activo->imagen_lateral)))
                <img class="foto-img" src="{{ public_path('storage/' . $activo->imagen_lateral) }}" alt="Lateral">
            @else
                <div class="foto-vacia">Sin imagen</div>
            @endif
        </div>

        <div class="foto-item">
            <div class="foto-titulo">📷 Detalle</div>
            @if($activo->imagen_detalle && file_exists(public_path('storage/' . $activo->imagen_detalle)))
                <img class="foto-img" src="{{ public_path('storage/' . $activo->imagen_detalle) }}" alt="Detalle">
            @else
                <div class="foto-vacia">Sin imagen</div>
            @endif
        </div>
    </div>

    {{-- OBSERVACIONES --}}
    <table class="info-table">
        <tr>
            <th style="width: 15%;">OBSERVACIONES:</th>
            <td style="font-size: 8px;">
                ASIGNACIÓN PROVISIONAL / DEFINITIVA DE ACTIVOS FIJOS SEGÚN REGISTRO SISTEMA DE INVENTARIOS.
            </td>
        </tr>
    </table>

    {{-- FIRMAS --}}
    <table class="signatures-table">
        <tr>
            <td>
                <br><br><br>
                <div class="firma-linea">
                    RESPONSABLE DEL BIEN EN USO / CUSTODIO<br>
                    <span class="firma-detalle">
                        Nombre: {{ $activo->custodio?->nombre_completo ?? '—' }}<br>
                        C.I.: {{ $activo->custodio?->ci ?? '—' }}<br>
                        Cargo: {{ $activo->custodio?->cargo ?? '—' }}
                    </span>
                </div>
            </td>

            <td>
                <br><br><br>
                <div class="firma-linea">
                    RESPONSABLE DE INVENTARIACIÓN / SABS<br>
                    <span class="firma-detalle">
                        Lic. Ana Lía Zapana Cortez<br>
                        C.I.: 4756925<br>
                        Directora Administrativa<br>
                        INSTITUTO TECNOLÓGICO "PUERTO DE MEJILLONES"
                    </span>
                </div>
            </td>
        </tr>
    </table>

    {{-- NOTA FINAL --}}
    <div class="footer-note">
        <strong>IMPORTANTE:</strong> Como responsable del bien de uso me comprometo a dar cumplimiento al
        D.S. 0181 Art. 116 (RESPONSABILIDAD POR EL MANEJO DE BIENES) Parágrafo III. Todos los Servidores Públicos
        son responsables por el debido uso, custodia, preservación y demanda de servicios de mantenimiento de los
        bienes que les fueran asignados, de acuerdo al régimen de Responsabilidad por la Función Pública, establecido
        en la Ley 1178 y sus reglamentos.<br><br>
        <strong>Art. 157 (PROHIBICIONES PARA LOS FUNCIONARIOS PÚBLICOS SOBRE EL USO DE ACTIVOS FIJOS MUEBLES)</strong>
        Inc. A, b, c, d, e, f, g, h.<br><br>
        <strong>NOTA:</strong> COMO RESPONSABLE DEL USO Y CUSTODIO ME COMPROMETO CUMPLIR EL INCISO c :
        PRESTAR O TRANSFERIR EL BIEN A OTRO EMPLEADO PÚBLICO.
    </div>

</body>
</html>
