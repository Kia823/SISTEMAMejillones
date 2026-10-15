@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- ENCABEZADO --}}
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
        <div>
            <h2 style="font-size:24px;font-weight:800;color:#0f172a;margin:0;">Ítems</h2>
            <p style="color:#64748b;font-size:14px;margin:4px 0 0 0;">
                El ítem pertenece a la institución, no a la persona. Un ítem admite 2, 3 o más custodios
                y sobrevive a los cambios de titular.
            </p>
        </div>
        @if(auth()->user()->esAdmin())
            <a href="{{ route('items.create') }}"
               style="background:#2563eb;color:white;padding:10px 18px;border-radius:10px;font-weight:700;text-decoration:none;font-size:14px;">
                + Nuevo Ítem
            </a>
        @endif
    </div>

    {{-- FILTROS --}}
    <form method="GET" action="{{ route('items.index') }}"
          style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;background:white;padding:16px;border-radius:14px;box-shadow:0 2px 8px rgba(0,0,0,.04);margin-bottom:18px;">

        <input type="text" name="buscar" value="{{ request('buscar') }}"
               placeholder="Buscar por número de ítem, descripción o custodio…"
               style="flex:1;min-width:260px;padding:10px 12px;border-radius:8px;border:1px solid #cbd5e1;font-size:14px;">

        <select name="estado" style="padding:10px 12px;border-radius:8px;border:1px solid #cbd5e1;font-size:14px;background:#fff;">
            <option value="Todos" {{ request('estado', 'Todos') === 'Todos' ? 'selected' : '' }}>Todos los estados</option>
            <option value="OCUPADO" {{ request('estado') === 'OCUPADO' ? 'selected' : '' }}>Con custodios</option>
            <option value="VACANTE" {{ request('estado') === 'VACANTE' ? 'selected' : '' }}>Vacantes</option>
        </select>

        <button type="submit"
                style="background:#0f172a;color:white;border:none;padding:10px 18px;border-radius:8px;font-weight:700;cursor:pointer;font-size:14px;">
            Filtrar
        </button>

        @if(request()->hasAny(['buscar', 'estado']))
            <a href="{{ route('items.index') }}" style="color:#64748b;font-size:13px;font-weight:600;">Limpiar</a>
        @endif
    </form>

    {{-- TABLA --}}
    <div style="background:white;border-radius:14px;box-shadow:0 2px 8px rgba(0,0,0,.04);overflow:hidden;">
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                <thead>
                    <tr style="background:#f8fafc;border-bottom:2px solid #e2e8f0;">
                        <th style="padding:12px 14px;text-align:left;font-size:11px;font-weight:800;color:#64748b;text-transform:uppercase;">Ítem</th>
                        <th style="padding:12px 14px;text-align:left;font-size:11px;font-weight:800;color:#64748b;text-transform:uppercase;">Descripción / Unidad</th>
                        <th style="padding:12px 14px;text-align:left;font-size:11px;font-weight:800;color:#64748b;text-transform:uppercase;">Carrera</th>
                        <th style="padding:12px 14px;text-align:center;font-size:11px;font-weight:800;color:#64748b;text-transform:uppercase;">Custodios</th>
                        <th style="padding:12px 14px;text-align:center;font-size:11px;font-weight:800;color:#64748b;text-transform:uppercase;">Activos</th>
                        <th style="padding:12px 14px;text-align:center;font-size:11px;font-weight:800;color:#64748b;text-transform:uppercase;">Estado</th>
                        <th style="padding:12px 14px;text-align:center;font-size:11px;font-weight:800;color:#64748b;text-transform:uppercase;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        @php
                            $ocupado = $item->estado === 'OCUPADO';
                        @endphp
                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:12px 14px;">
                                <a href="{{ route('items.show', $item->numero_item) }}"
                                   style="font-weight:800;color:#2563eb;text-decoration:none;font-family:ui-monospace,monospace;">
                                    {{ $item->numero_item }}
                                </a>
                            </td>
                            <td style="padding:12px 14px;color:#334155;">
                                {{ $item->descripcion ?: '—' }}
                                @if($item->unidad)
                                    <div style="font-size:11px;color:#94a3b8;margin-top:2px;">{{ $item->unidad }}</div>
                                @endif
                            </td>
                            <td style="padding:12px 14px;color:#475569;">
                                {{ $item->carrera?->nombre ?? '—' }}
                            </td>
                            <td style="padding:12px 14px;text-align:center;">
                                <span style="background:{{ $ocupado ? '#dbeafe' : '#f1f5f9' }};color:{{ $ocupado ? '#1d4ed8' : '#64748b' }};padding:4px 10px;border-radius:12px;font-weight:800;font-size:12px;">
                                    {{ $item->custodios_vigentes_count }}
                                </span>
                                @if($item->custodios->isNotEmpty())
                                    <div style="font-size:11px;color:#94a3b8;margin-top:4px;">
                                        {{ $item->custodios->take(2)->pluck('nombre_completo')->implode(', ') }}
                                        @if($item->custodios->count() > 2)
                                            +{{ $item->custodios->count() - 2 }}
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td style="padding:12px 14px;text-align:center;font-weight:800;color:#0f172a;">
                                {{ $item->activos_count }}
                            </td>
                            <td style="padding:12px 14px;text-align:center;">
                                <span style="background:{{ $ocupado ? '#dcfce7' : '#fef3c7' }};color:{{ $ocupado ? '#166534' : '#92400e' }};padding:4px 10px;border-radius:12px;font-weight:800;font-size:11px;">
                                    {{ $item->estado_texto }}
                                </span>
                            </td>
                            <td style="padding:12px 14px;text-align:center;">
                                <a href="{{ route('items.show', $item->numero_item) }}"
                                   style="background:#eff6ff;color:#2563eb;padding:6px 12px;border-radius:8px;font-weight:700;text-decoration:none;font-size:12px;">
                                    Ver detalle
                                </a>
                                @if(auth()->user()->esAdmin())
                                    <a href="{{ route('items.edit', $item->id_item) }}"
                                       style="background:#fef9c3;color:#ca8a04;padding:6px 12px;border-radius:8px;font-weight:700;text-decoration:none;font-size:12px;margin-left:4px;">
                                        Editar
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="padding:40px;text-align:center;color:#94a3b8;font-size:14px;">
                                No hay ítems que coincidan con la búsqueda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="padding:12px 14px;background:#f8fafc;border-top:1px solid #e2e8f0;font-size:12px;color:#64748b;font-weight:600;">
            {{ $items->count() }} ítem(s) registrado(s)
        </div>
    </div>
</div>
@endsection
