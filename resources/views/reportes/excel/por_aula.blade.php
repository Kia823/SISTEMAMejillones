<table border="1" style="font-family:Arial; font-size:10px; width:100%;">
    <thead>
        <tr style="background:#e5e5e5;">
            <th colspan="9" style="text-align:center; font-size:12px; padding:6px;">
                INVENTARIO POR AULA — {{ $ambiente->nombre }}
            </th>
        </tr>
        <tr style="background:#f1f5f9;">
            <th colspan="9" style="text-align:left; padding:4px;">
                INSTITUTO TECNOLÓGICO "PUERTO DE MEJILLONES" · CARRERA: {{ $ambiente->carrera->nombre ?? '—' }} · GENERADO: {{ date('d/m/Y H:i') }}
            </th>
        </tr>
        <tr style="background:#e5e5e5;">
            <th>N°</th><th>CÓDIGO</th><th>DESCRIPCIÓN</th><th>CARACTERÍSTICAS</th>
            <th>CANT.</th><th>ESTADO</th><th>FUENTE</th><th>GESTIÓN</th><th>OBS.</th>
        </tr>
    </thead>
    <tbody>
        @php $i = 1; @endphp
        @foreach ($agrupados as $g)
            <tr>
                <td align="center">{{ $i++ }}</td>
                <td>{{ $g->codigos }}</td>
                <td>{{ $g->nombre }}</td>
                <td>
                    {{ $g->descripcion }}
                    @if($g->marca) | Marca: {{ $g->marca }} @endif
                    @if($g->modelo) | Modelo: {{ $g->modelo }} @endif
                </td>
                <td align="center">{{ $g->cantidad }}</td>
                <td align="center">{{ $g->estado_texto }}</td>
                <td align="center">{{ $g->fuente_codigo }}</td>
                <td align="center">{{ $g->gestion }}</td>
                <td>{{ $g->observaciones }}</td>
            </tr>
        @endforeach
        <tr style="background:#f1f5f9; font-weight:bold;">
            <td colspan="4" align="right">TOTAL ITEMS ASIGNADOS:</td>
            <td align="center">{{ $activos->count() }}</td>
            <td colspan="4"></td>
        </tr>
    </tbody>
</table>
