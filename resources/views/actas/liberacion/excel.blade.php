<table border="1" style="font-family:Arial; font-size:10px; width:100%;">
    <thead>
        <tr style="background:#e5e5e5;">
            <th colspan="9" style="text-align:center; font-size:12px;">
                INSTITUTO TECNOLÓGICO "PUERTO DE MEJILLONES" — ACTA DE LIBERACIÓN DE CUSTODIA
            </th>
        </tr>
        <tr>
            <th style="background:#e5e5e5;">Ambiente</th>
            <td>{{ $ambiente->nombre }} ({{ $ambiente->codigo }})</td>
            <th style="background:#e5e5e5;">Carrera o unidad</th>
            <td colspan="2">{{ $ambiente->carrera?->nombre ?? '—' }}</td>
        </tr>
        <tr>
            <th style="background:#e5e5e5;">Bloque / Piso</th>
            <td>{{ $ambiente->bloque ?? '—' }} / {{ $ambiente->piso ?? '—' }}</td>
            <th style="background:#e5e5e5;">Gestión</th>
            <td colspan="2">{{ date('Y') }}</td>
        </tr>
        <tr>
            <th style="background:#e5e5e5;">Recibe (jefe de carrera)</th>
            <td colspan="4">
                {{ $receptor->nombre_completo ?? '—' }}
                @if ($receptor)
                    — C.I.: {{ $receptor->ci }} — {{ $receptor->cargo ?? '' }}
                @endif
            </td>
        </tr>
        <tr>
            <th style="background:#e5e5e5;">Detalle del acta</th>
            <td colspan="4">
                @if ($tipo === 'NO_ACTIVO')
                    Bienes no activos (material de consumo)
                @elseif ($tipo === 'ACTIVO_FIJO')
                    Activos fijos (maquinaria, herramientas y equipos)
                @else
                    Activos fijos y bienes no activos
                @endif
            </td>
        </tr>
        <tr style="background:#e5e5e5;">
            <th colspan="9" style="text-align:left;">CUSTODIOS QUE LIBERAN</th>
        </tr>
        <tr style="background:#f1f5f9;">
            <th>N°</th>
            <th style="text-align:left;">Custodio</th>
            <th>C.I.</th>
            <th>Ítem</th>
            <th style="text-align:left;">Cargo</th>
            <th style="text-align:left;">Unidad</th>
        </tr>
        @forelse ($custodios as $i => $custodio)
            <tr>
                <td align="center">{{ $i + 1 }}</td>
                <td>{{ $custodio->nombre_completo }}</td>
                <td align="center">{{ $custodio->ci }}</td>
                <td align="center">
                    @forelse ($custodio->itemsDelAmbiente ?? [] as $item)
                        {{ $item->numero_item }}<br>
                    @empty
                        —
                    @endforelse
                </td>
                <td>{{ $custodio->cargo ?? '—' }}</td>
                <td>{{ $custodio->unidad ?? '—' }}</td>
            </tr>
        @empty
            <tr><td colspan="6" align="center">Sin custodios registrados</td></tr>
        @endforelse
        <tr style="background:#e5e5e5;">
            <th colspan="9" style="text-align:left;">BIENES DEL AMBIENTE</th>
        </tr>
        <tr style="background:#f1f5f9;">
            <th>N°</th>
            <th>CÓDIGO</th>
            <th>TIPO</th>
            <th style="text-align:left;">DESCRIPCIÓN</th>
            <th style="text-align:left;">CATEGORÍA</th>
            <th style="text-align:left;">CUSTODIO</th>
            <th>ESTADO</th>
            <th>FUENTE</th>
            <th>CANT.</th>
        </tr>
        @php $totalValor = 0; @endphp
        @forelse ($activos as $i => $act)
            @php $totalValor += (float) $act->valor_adquisicion; @endphp
            <tr>
                <td align="center">{{ $i + 1 }}</td>
                <td>{{ $act->codigo_activo }}</td>
                <td align="center">{{ $act->tipo_bien_texto }}</td>
                <td>
                    {{ $act->nombre }}
                    @if ($act->marca || $act->modelo)
                        <br>{{ collect([$act->marca, $act->modelo])->filter()->implode(' / ') }}
                    @endif
                </td>
                <td>{{ $act->categoria->nombre ?? '—' }}</td>
                <td>{{ $act->custodio?->nombre_completo ?? 'Sin custodio' }}</td>
                <td align="center">{{ $act->estado_fisico }}</td>
                <td align="center">{{ $act->fuente->codigo ?? '—' }}</td>
                <td align="center">1</td>
            </tr>
        @empty
            <tr><td colspan="9" align="center">Sin bienes registrados en el ambiente</td></tr>
        @endforelse
        <tr style="background:#f1f5f9;">
            <td colspan="8" align="right"><strong>TOTAL BIENES / VALOR DE ADQUISICIÓN (Bs.)</strong></td>
            <td align="center"><strong>{{ $activos->count() }} / {{ number_format($totalValor, 2) }}</strong></td>
        </tr>
    </thead>
</table>