<table border="1" style="font-family:Arial; font-size:10px; width:100%;">
    <thead>
        <tr style="background:#e5e5e5;">
            <th>Nº</th>
            <th>CÓDIGO</th>
            <th>DESCRIPCIÓN DEL EQUIPO</th>
            <th>CARACTERÍSTICAS</th>
            <th>CANTIDAD</th>
            <th>COSTO (Bs.)</th>
            <th>ESTADO</th>
            <th>FUENTE</th>
            <th>FECHA ENTREGA</th>
            <th>DÍA</th>
            <th>MES</th>
            <th>AÑO</th>
            <th>USO EN ÁREA (CARRERA)</th>
            <th>OBSERVACIONES</th>
            <th>IMAGEN</th>
        </tr>
        <tr>
            <td colspan="15" style="background:#f8fafc;font-weight:bold;">
                @if(($tipoBien ?? 'TODOS') === 'NO_ACTIVO')
                    INVENTARIO DE BIENES NO ACTIVOS (MATERIAL DE CONSUMO)
                @elseif(($tipoBien ?? 'TODOS') === 'ACTIVO_FIJO')
                    INVENTARIO DE MAQUINARIA, HERRAMIENTAS Y EQUIPOS DE LABORATORIO
                @else
                    INVENTARIO GENERAL DE BIENES (AGRUPADOS)
                @endif
            </td>
        </tr>
    </thead>
    <tbody>
        @forelse($reportes as $r)
            <tr>
                <td>{{ $r->nro }}</td>
                <td>{{ $r->codigos }}</td>
                <td>{{ $r->descripcion }}</td>
                <td>{{ $r->caracteristicas }}</td>
                <td align="center">{{ $r->cantidad }}</td>
                <td align="right">{{ number_format($r->costo_total, 2) }}</td>
                <td align="center">{{ $r->estado_fisico_texto }}</td>
                <td align="center">{{ $r->fuente ?? '—' }}</td>
                <td align="center">{{ $r->fecha_entrega ? $r->fecha_entrega->format('d/m/Y') : '—' }}</td>
                <td align="center">{{ $r->dia }}</td>
                <td align="center">{{ $r->mes }}</td>
                <td align="center">{{ $r->anio }}</td>
                <td>{{ $r->carrera }}</td>
                <td>{{ $r->observaciones ?? '—' }}</td>
                <td align="center">
                    @if($r->imagen)
                        [IMG]
                    @else
                        —
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="15" align="center">Sin bienes</td></tr>
        @endforelse
        <tr style="font-weight:bold; background:#f8fafc;">
            <td colspan="4" style="text-align:right;">TOTALES:</td>
            <td align="center">{{ $reportes->sum('cantidad') }}</td>
            <td align="right">{{ number_format($reportes->sum('costo_total'), 2) }}</td>
            <td colspan="9"></td>
        </tr>
    </tbody>
</table>