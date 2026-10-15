<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acta de Liberación</title>
    <style>
        @page { size: 216mm 330mm; margin: 15mm; }
        * { box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 10px; color: #111; line-height: 1.5; }

        .header { text-align: center; margin-bottom: 18px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h1 { font-size: 14px; margin: 0; text-transform: uppercase; }
        .header h2 { font-size: 11px; margin: 3px 0 0 0; font-weight: normal; }
        .header h3 { font-size: 13px; margin: 12px 0 4px 0; text-transform: uppercase; }
        .header .fecha { font-size: 10px; margin-top: 6px; }

        .datos { width: 100%; border-collapse: collapse; font-size: 10px; margin-bottom: 12px; }
        .datos td { padding: 4px 6px; vertical-align: top; border: 0.5px solid #999; }
        .datos .label { font-weight: bold; background: #f0f0f0; width: 22%; }

        .custodios { width: 100%; border-collapse: collapse; font-size: 9px; margin-bottom: 12px; }
        .custodios th { background: #e5e5e5; border: 0.7px solid #000; padding: 4px; text-align: center; text-transform: uppercase; }
        .custodios td { border: 0.5px solid #555; padding: 3px 5px; }

        table.inventario { width: 100%; border-collapse: collapse; font-size: 8px; }
        table.inventario thead th {
            background: #e5e5e5; border: 0.7px solid #000; padding: 4px;
            text-align: center; font-weight: bold; text-transform: uppercase;
        }
        table.inventario tbody td { border: 0.5px solid #555; padding: 3px; vertical-align: top; }
        table.inventario tfoot td { border: 0.7px solid #000; padding: 4px; font-weight: bold; background: #f0f0f0; }

        .resumen { width: 100%; border-collapse: collapse; font-size: 9px; margin-top: 10px; }
        .resumen td { border: 0.5px solid #999; padding: 4px 6px; }
        .resumen .label { font-weight: bold; background: #f7f7f7; width: 25%; }

        .firmas { margin-top: 45px; width: 100%; border-collapse: collapse; }
        .firmas td { text-align: center; padding: 0 8px 0 8px; font-size: 9px; vertical-align: bottom; }
        .firmas .linea { border-top: 1px solid #000; margin: 0 20px; padding-top: 5px; font-weight: bold; }
        .firmas .rol { font-weight: normal; font-size: 8px; }

        .legal { font-size: 8px; color: #444; margin-top: 18px; padding: 8px; border: 1px dashed #999; background: #fafafa; }
    </style>
</head>
<body>

    <div class="header">
        <h1>INSTITUTO TECNOLÓGICO "PUERTO DE MEJILLONES"</h1>
        <h2>{{ $ambiente->carrera?->nombre ?? 'Unidad Administrativa' }}</h2>
        <h3>Acta de Liberación de Custodia</h3>
        <div class="fecha">Gestión {{ date('Y') }} — El Alto, {{ now()->translatedFormat('d \d\e F \d\e Y') }}</div>
    </div>

    <table class="datos">
        <tr>
            <td class="label">Ambiente:</td>
            <td colspan="3"><strong>{{ $ambiente->nombre }}</strong> ({{ $ambiente->codigo }})</td>
        </tr>
        <tr>
            <td class="label">Carrera o unidad:</td>
            <td>{{ $ambiente->carrera?->nombre ?? '—' }}</td>
            <td class="label">Bloque / Piso:</td>
            <td>{{ $ambiente->bloque ?? '—' }} / {{ $ambiente->piso ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Recibe (jefe de carrera):</td>
            <td colspan="3">
                <strong>{{ $receptor->nombre_completo ?? '—' }}</strong>
                @if ($receptor)
                    — C.I.: {{ $receptor->ci }}
                    @if ($receptor->cargo)
                        — {{ $receptor->cargo }}
                    @endif
                @endif
            </td>
        </tr>
        <tr>
            <td class="label">Detalle del acta:</td>
            <td colspan="3">
                @if ($tipo === 'NO_ACTIVO')
                    Bienes no activos (material de consumo)
                @elseif ($tipo === 'ACTIVO_FIJO')
                    Activos fijos (maquinaria, herramientas y equipos)
                @else
                    Activos fijos y bienes no activos
                @endif
            </td>
        </tr>
    </table>

    <p style="font-size: 10px; text-align: justify; margin-bottom: 10px;">
        Los custodios del ambiente <strong>{{ $ambiente->nombre }}</strong> hacen constar que, al cierre de la gestión
        {{ date('Y') }}, entregan al jefe de carrera la custodia de los bienes que a continuación se detallan, en cumplimiento
        al D.S. 0181 <em>Normas Básicas del Sistema de Administración de Bienes y Servicios</em>.
    </p>

    <table class="custodios">
        <thead>
            <tr>
                <th style="width: 8%;">N°</th>
                <th>Custodio (libera)</th>
                <th style="width: 18%;">C.I.</th>
                <th style="width: 22%;">Ítem</th>
                <th style="width: 22%;">Cargo / unidad</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($custodios as $i => $custodio)
                <tr>
                    <td style="text-align: center;">{{ $i + 1 }}</td>
                    <td>{{ $custodio->nombre_completo }}</td>
                    <td style="text-align: center;">{{ $custodio->ci }}</td>
                    <td align="center">
                        @forelse ($custodio->itemsDelAmbiente ?? [] as $item)
                            {{ $item->numero_item }}<br>
                        @empty
                            —
                        @endforelse
                    </td>
                    <td>{{ $custodio->cargo ?? '—' }}<br>{{ $custodio->unidad ?? '' }}</td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align: center;">Sin custodios registrados</td></tr>
            @endforelse
        </tbody>
    </table>

    <table class="inventario">
        <thead>
            <tr>
                <th style="width: 4%;">N°</th>
                <th style="width: 13%;">CÓDIGO</th>
                <th style="width: 8%;">TIPO</th>
                <th style="width: 26%;">DESCRIPCIÓN</th>
                <th style="width: 14%;">CATEGORÍA</th>
                <th style="width: 15%;">CUSTODIO</th>
                <th style="width: 9%;">ESTADO</th>
                <th style="width: 7%;">FUENTE</th>
                <th style="width: 4%;">CANT.</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalValor = 0;
            @endphp
            @forelse ($activos as $i => $act)
                @php
                    $totalValor += (float) $act->valor_adquisicion;
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $i + 1 }}</td>
                    <td>{{ $act->codigo_activo }}</td>
                    <td style="text-align: center;">{{ $act->tipo_bien_texto }}</td>
                    <td>
                        {{ $act->nombre }}
                        @if ($act->marca || $act->modelo)
                            <br><span style="font-size: 7px; color: #444;">
                                {{ collect([$act->marca, $act->modelo])->filter()->implode(' / ') }}
                            </span>
                        @endif
                    </td>
                    <td>{{ $act->categoria->nombre ?? '—' }}</td>
                    <td>{{ $act->custodio?->nombre_completo ?? 'Sin custodio' }}</td>
                    <td style="text-align: center;">{{ $act->estado_fisico }}</td>
                    <td style="text-align: center;">{{ $act->fuente->codigo ?? '—' }}</td>
                    <td style="text-align: center;">1</td>
                </tr>
            @empty
                <tr><td colspan="9" style="text-align: center;">Sin bienes registrados en el ambiente</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="8" style="text-align: right;">TOTAL BIENES / VALOR DE ADQUISICIÓN (Bs.)</td>
                <td style="text-align: center;">{{ $activos->count() }}</td>
            </tr>
            <tr>
                <td colspan="8" style="text-align: right;"></td>
                <td style="text-align: center;">{{ number_format($totalValor, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <table class="resumen">
        <tr>
            <td class="label">Activos fijos en el acta:</td>
            <td>{{ $ambiente->activos()->where('tipo_bien', 'ACTIVO_FIJO')->count() }}</td>
            <td class="label">Bienes no activos:</td>
            <td>{{ $ambiente->activos()->where('tipo_bien', 'NO_ACTIVO')->count() }}</td>
        </tr>
    </table>

    {{-- Firmas: todos los custodios liberan, el jefe de carrera recibe --}}
    <table class="firmas">
        <tr>
            @foreach ($custodios as $custodio)
                <td>
                    <div class="linea">
                        {{ $custodio->nombre_completo }}<br>
                        C.I.: {{ $custodio->ci }}
                        <div class="rol">LIBERA — Custodio</div>
                    </div>
                </td>
            @endforeach
            <td>
                <div class="linea">
                    {{ $receptor->nombre_completo ?? 'Jefe de Carrera' }}<br>
                    @if ($receptor)
                        C.I.: {{ $receptor->ci }}
                    @endif
                    <div class="rol">RECIBE — Jefe de Carrera</div>
                </div>
            </td>
        </tr>
    </table>

    <div class="legal">
        <strong>NOTA:</strong> La presente acta deja constancia de la entrega física de los bienes. La entrega se realiza de
        forma presencial y el bien permanece registrado a nombre de su custodio en el sistema hasta que la Dirección
        Administrativa realice la actualización correspondiente.
        Todo servidor público es responsable del uso, custodia, preservación y demanda de mantenimiento de los bienes
        asignados (D.S. 0181 Art. 116).
    </div>

</body>
</html>