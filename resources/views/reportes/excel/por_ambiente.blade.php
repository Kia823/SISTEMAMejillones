<table border="1" style="font-family:Arial; font-size:10px; width:100%;">
    <thead>
        <tr style="background:#e5e5e5;">
            <th colspan="14" style="text-align:center; font-size:12px; padding:6px;">
                INVENTARIO POR AMBIENTE — {{ $ambiente->nombre }}
            </th>
        </tr>
        <tr style="background:#f1f5f9;">
            <th colspan="14" style="text-align:left; padding:4px;">
                INSTITUTO TECNOLÓGICO "PUERTO DE MEJILLONES" · CARRERA: {{ $ambiente->carrera->nombre ?? '—' }} · GENERADO: {{ date('d/m/Y H:i') }}
            </th>
        </tr>
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
    </thead>
    <tbody>
        @forelse($reportes as $r)
            <tr>
                <td align="center">{{ $r->nro }}</td>
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
            <tr><td colspan="15" align="center">Sin activos en este ambiente</td></tr>
        @endforelse
        <tr style="font-weight:bold; background:#f8fafc;">
            <td colspan="4" style="text-align:right;">TOTALES:</td>
            <td align="center">{{ $reportes->sum('cantidad') }}</td>
            <td align="right">{{ number_format($reportes->sum('costo_total'), 2) }}</td>
            <td colspan="9"></td>
        </tr>
    </tbody>
</table>