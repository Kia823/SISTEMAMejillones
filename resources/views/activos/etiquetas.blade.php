<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Etiquetas de Activos</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', Arial, Helvetica, sans-serif; }

        @page {
            size: letter portrait;
            margin: 4mm;
        }

        body {
            font-size: 10px;
            color: #000;
            background: #fff;
            padding: 0;
        }

        .sheet {
            width: 202mm;
            background: white;
            padding: 1mm;
            margin: 0 auto;
        }

        .sheet-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #000;
            padding-bottom: 3px;
            margin-bottom: 5px;
        }
        .sheet-header .inst   { font-size: 10px; font-weight: 800; color: #000; text-transform: uppercase; }
        .sheet-header .titulo { font-size: 11px; font-weight: 800; color: #000; text-transform: uppercase; }
        .sheet-header .gestion{ font-size: 9px; font-weight: 700; color: #334155; }

        .subtitulo {
            text-align: center;
            font-size: 9px;
            font-weight: 800;
            color: #334155;
            text-transform: uppercase;
            margin-bottom: 5px;
            letter-spacing: .5px;
        }

        /* Grid: 3 etiquetas por fila */
        table.grid {
            width: 100%;
            border-collapse: separate;
            border-spacing: 2mm;
            table-layout: fixed;
        }
        table.grid td {
            width: 33.33%;
            vertical-align: top;
            padding: 0;
        }

        /* ===== ETIQUETA 63mm x 28mm ===== */
        .etiqueta {
            position: relative;
            width: 63mm;
            height: 20mm;
            border: 1.5px solid #000;
            border-radius: 4px;
            overflow: hidden;
            background: #ffffff;
        }

        .etiqueta-info {
            position: absolute;
            top: 0;
            left: 0;
            right: 17mm;
            bottom: 0;
            padding: 1mm 1.5mm;      /* menos padding vertical */
            display: flex;
            flex-direction: column;
            justify-content: space-between; /* distribuye el alto */
        }

        .etiqueta-header {
            display: flex;
            align-items: center;
            gap: 4px;
            padding-bottom: 1px;
            border-bottom: 1px solid #000;
            margin-bottom: 1px;
        }
        .etiqueta-logo {
            width: 12px;
            height: 12px;
            object-fit: contain;
            flex-shrink: 0;
        }
        .etiqueta-institucion {
            flex: 1;
            font-size: 6.5px;
            font-weight: 800;
            color: #000;
            text-transform: uppercase;
            line-height: 1;
        }

        .etiqueta-body {
            display: flex;
            flex-direction: column;
            gap: 0.5px;
            flex: 1;
            justify-content: space-evenly; /* mejor distribución vertical */
        }

        .etiqueta-row {
            display: flex;
            align-items: center;
            gap: 3px;
            line-height: 1.1;
        }
        .etiqueta-row .label {
            font-weight: 800;
            color: #000000;
            text-transform: uppercase;
            font-size: 8px;           /* antes 7.5 → más legible */
            min-width: 30px;
            flex-shrink: 0;
        }
        .etiqueta-row .value {
            font-weight: 700;
            color: #000000;
            font-size: 9.5px;         /* antes 8.5 → más legible */
            flex: 1;
        }

        /* Código resalta en rojo oscuro claro sobre blanco */
        .etiqueta-codigo {
            font-size: 12px !important;  /* antes 10.5 */
            font-weight: 900 !important;
            color: #dc2626 !important;
        }
        
        /* Nombre del activo bien legible */
        .etiqueta-nombre {
            font-size: 11px !important;  /* antes 9.5 */
            font-weight: 900 !important;
            color: #000000 !important;
            text-transform: uppercase;
        }

        /* Badge de Fuente visible */
        .etiqueta-fuente {
            display: inline-block;
            background: #e2e8f0;
            color: #0f172a;
            font-size: 8.5px;            /* antes 7.5 */
            font-weight: 800;
            padding: 0.5px 4px;
            border-radius: 2px;
            border: 1px solid #94a3b8;
            text-transform: uppercase;
        }

        /* ===== QR DERECHA ===== */
        .etiqueta-qr {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            width: 17mm;
            background: #f1f5f9;
            border-left: 1px solid #000;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 0.5mm;             /* menos padding para aprovechar alto */
        }
        .etiqueta-qr img {
            width: 16mm;                /* un poco más grande */
            height: 16mm;
            display: block;
            background: white;
            border-radius: 2px;
            border: 1px solid #000;
        }
        .etiqueta-qr-label {
            margin-top: 1px;
            font-size: 9px;             /* antes 4.5 → legible */
            font-weight: 800;
            color: #000;
            text-transform: uppercase;
            text-align: center;
            line-height: 1;
        }

        @media print {
            body {
                background: white !important;
                padding: 0 !important;
            }
            .sheet {
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
            }
        }
    </style>
</head>
<body>

    <div class="sheet">
        <div class="sheet-header">
            <div class="inst">INST. TECNOLOGICO PUERTO DE MEJILLONES</div>
            <div class="titulo">ETIQUETAS DE ACTIVOS</div>
            <div class="gestion">GESTIÓN {{ date('Y') }}</div>
        </div>

        <div class="subtitulo">
            {{ count($activos) }} etiqueta{{ count($activos) !== 1 ? 's' : '' }} para imprimir
        </div>

        <table class="grid">
            @foreach ($activos as $i => $activo)
                @if ($i % 3 === 0)
                    <tr>
                @endif

                <td>
                    @php
                        $logoImage = $logoDataUri ?? '';
                        $qrImage = '';
                        if (!empty($activo->qr_data_uri)) {
                            $qrImage = $activo->qr_data_uri;
                        } elseif (!empty($activo->ruta_qr) && \Storage::disk('public')->exists($activo->ruta_qr)) {
                            $qrImage = 'data:image/png;base64,' . base64_encode(\Storage::disk('public')->get($activo->ruta_qr));
                        }
                    @endphp

                    <div class="etiqueta">
                        <div class="etiqueta-info">
                            <div class="etiqueta-header">
                                @if($logoImage)
                                    <img src="{{ $logoImage }}" alt="Logo" class="etiqueta-logo">
                                @elseif(file_exists(public_path('images/logo.png')))
                                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="etiqueta-logo">
                                @endif
                                <div class="etiqueta-institucion">INST. TECNOLOGICO PUERTO DE MEJILLONES</div>
                            </div>

                            <div class="etiqueta-body">
                                <div class="etiqueta-row">
                                    <span class="label">GESTIÓN:</span>
                                    <span class="value">{{ $activo->fecha_adquisicion ? $activo->fecha_adquisicion->format('Y') : date('Y') }}</span>
                                </div>
                                <div class="etiqueta-row">
                                    <span class="label">CÓDIGO:</span>
                                    <span class="value etiqueta-codigo">{{ $activo->codigo_activo }}</span>
                                </div>
                                <div class="etiqueta-row">
                                    <span class="label">UNIDAD:</span>
                                    <span class="value">{{ $activo->ambiente->nombre ?? 'TALLER' }}</span>
                                </div>
                                <div class="etiqueta-row">
                                    <span class="label">ACTIVO:</span>
                                    <span class="value etiqueta-nombre">{{ $activo->nombre }}</span>
                                </div>
                                <div class="etiqueta-row">
                                    <span class="label">FUENTE:</span>
                                    <span class="etiqueta-fuente">
                                        {{ $activo->fuente->codigo ?? $activo->fuente->nombre ?? 'PSP' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="etiqueta-qr">
                            @if($qrImage)
                                <img src="{{ $qrImage }}" alt="QR">
                            @else
                                <div style="width:14mm;height:14mm;display:flex;align-items:center;justify-content:center;font-size:8px;font-weight:bold;color:#dc2626;background:white;border:1px solid #000;border-radius:2px;">QR</div>
                            @endif
                            <div class="etiqueta-qr-label">ESCANEAR<br>VERIFICAR</div>
                        </div>
                    </div>
                </td>

                @if ($i % 3 === 2 || $loop->last)
                    </tr>
                @endif
            @endforeach
        </table>
    </div>

</body>
</html>