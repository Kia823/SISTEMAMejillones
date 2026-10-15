@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- ENCABEZADO --}}
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
        <div>
            <a href="{{ route('items.index') }}" style="color:#64748b;font-size:13px;font-weight:700;text-decoration:none;">
                ← Volver a ítems
            </a>
            <h2 style="font-size:24px;font-weight:800;color:#0f172a;margin:4px 0 0 0;font-family:ui-monospace,monospace;">
                Ítem {{ $item->numero_item }}
            </h2>
            <p style="color:#64748b;font-size:14px;margin:4px 0 0 0;">
                {{ $item->descripcion ?: 'Sin descripción' }}
            </p>
        </div>
        <div style="display:flex;gap:8px;align-items:center;">
            <span style="background:{{ $item->estado === 'OCUPADO' ? '#dcfce7' : '#fef3c7' }};color:{{ $item->estado === 'OCUPADO' ? '#166534' : '#92400e' }};padding:8px 16px;border-radius:10px;font-weight:800;font-size:13px;">
                {{ $item->estado_texto }}
            </span>
            @if(auth()->user()->esAdmin())
                <a href="{{ route('items.edit', $item->id_item) }}"
                   style="background:#e2e8f0;color:#334155;padding:9px 16px;border-radius:10px;font-weight:700;text-decoration:none;font-size:13px;">
                    Editar ítem
                </a>
            @endif
        </div>
    </div>

    {{-- FICHA --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px;margin-bottom:22px;">
        @php
            $fichas = [
                ['etiqueta' => 'Activos del ítem', 'valor' => $activos->count(), 'color' => '#2563eb'],
                ['etiqueta' => 'Custodios vigentes', 'valor' => $custodios->where('estado', 'ACTIVO')->whereNull('pivot.fecha_fin')->count(), 'color' => '#16a34a'],
                ['etiqueta' => 'Carrera', 'valor' => $item->carrera?->nombre ?? '—', 'color' => '#7c3aed'],
                ['etiqueta' => 'Ambiente', 'valor' => $item->ambiente?->nombre ?? '—', 'color' => '#0891b2'],
                ['etiqueta' => 'Unidad', 'valor' => $item->unidad ?? '—', 'color' => '#0f172a'],
            ];
        @endphp
        @foreach($fichas as $ficha)
            <div style="background:white;padding:16px;border-radius:14px;box-shadow:0 2px 8px rgba(0,0,0,.04);">
                <div style="font-size:11px;font-weight:800;color:#64748b;text-transform:uppercase;letter-spacing:.3px;">{{ $ficha['etiqueta'] }}</div>
                <div style="font-size:22px;font-weight:800;color:{{ $ficha['color'] }};margin-top:6px;word-break:break-word;">{{ $ficha['valor'] }}</div>
            </div>
        @endforeach
    </div>

    <div style="display:grid;grid-template-columns:1fr 1.6fr;gap:18px;align-items:start;">

        {{-- CUSTODIOS --}}
        <div style="background:white;border-radius:14px;box-shadow:0 2px 8px rgba(0,0,0,.04);overflow:hidden;">
            <div style="padding:14px 18px;border-bottom:1px solid #e2e8f0;background:#f8fafc;">
                <h3 style="font-size:14px;font-weight:800;color:#0f172a;margin:0;">Custodios</h3>
                <p style="font-size:11px;color:#64748b;margin:2px 0 0;">Un ítem puede tener 2, 3 o más custodios.</p>
            </div>

            <div style="padding:14px 18px;border-bottom:1px solid #f1f5f9;">
                @forelse($custodios as $custodio)
                    @php
                        $vigente = $custodio->estado === 'ACTIVO' && $custodio->pivot->fecha_fin === null;
                    @endphp
                    <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;padding:10px 0;border-bottom:1px solid #f8fafc;">
                        <div>
                            <div style="font-weight:700;color:#0f172a;font-size:13px;">
                                {{ $custodio->nombre_completo }}
                                @if(! $vigente)
                                    <span style="background:#f1f5f9;color:#64748b;padding:2px 7px;border-radius:8px;font-size:10px;font-weight:800;margin-left:4px;">HISTÓRICO</span>
                                @endif
                            </div>
                            <div style="font-size:11px;color:#94a3b8;margin-top:2px;">
                                C.I. {{ $custodio->ci }} · {{ $custodio->cargo ?: 'Sin cargo' }}
                            </div>
                            <div style="font-size:11px;color:#64748b;margin-top:2px;">
                                {{ $custodio->pivot->tipo }}
                                @if($custodio->pivot->fecha_fin)
                                    · hasta {{ \Carbon\Carbon::parse($custodio->pivot->fecha_fin)->format('d/m/Y') }}
                                @elseif($custodio->pivot->fecha_inicio)
                                    · desde {{ \Carbon\Carbon::parse($custodio->pivot->fecha_inicio)->format('d/m/Y') }}
                                @endif
                            </div>
                        </div>
                        <div style="display:flex;align-items:center;gap:6px;">
                            <span style="background:#eff6ff;color:#1d4ed8;padding:4px 9px;border-radius:10px;font-size:11px;font-weight:800;white-space:nowrap;">
                                {{ $activosPorCustodio->get($custodio->id_usuario, collect())->count() }} activos
                            </span>
                            @if($vigente && auth()->user()->esAdmin())
                                <form method="POST" action="{{ route('items.custodios.destroy', [$item->id_item, $custodio->id_usuario]) }}"
                                      onsubmit="return confirm('¿Desvincular a {{ $custodio->nombre_completo }} como custodio de este ítem?\n\nLos activos {{ $activosPorCustodio->get($custodio->id_usuario, collect())->count() }} seguirán asignados al ítem {{ $item->numero_item }}.');">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="motivo" value="Desvinculación manual">
                                    <button type="submit"
                                            style="background:#fee2e2;color:#dc2626;border:1px solid #fecaca;padding:5px 10px;border-radius:8px;font-size:11px;font-weight:800;cursor:pointer;">
                                        Desvincular
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <p style="font-size:13px;color:#94a3b8;font-style:italic;padding:12px 0;">
                        Este ítem todavía no tiene custodios asignados.
                    </p>
                @endforelse
            </div>

            @if(auth()->user()->esAdmin())
                <form method="POST" action="{{ route('items.custodios.store', $item->id_item) }}"
                      style="padding:14px 18px;background:#f8fafc;border-top:1px solid #e2e8f0;">
                    @csrf
                    <div style="font-size:11px;font-weight:800;color:#64748b;text-transform:uppercase;margin-bottom:8px;">
                        Agregar custodio
                    </div>
                    <x-person-picker
                        name="id_usuario"
                        id="custodioItem"
                        :required="true"
                        placeholder="Buscar por nombre, C.I. o número de ítem…" />
                    <button type="submit"
                            style="width:100%;background:#2563eb;color:white;border:none;padding:10px;border-radius:8px;font-weight:800;cursor:pointer;font-size:13px;margin-top:8px;">
                        Asignar custodio
                    </button>
                </form>
            @endif
        </div>

        {{-- ACTIVOS --}}
        <div style="background:white;border-radius:14px;box-shadow:0 2px 8px rgba(0,0,0,.04);overflow:hidden;">
            <div style="padding:14px 18px;border-bottom:1px solid #e2e8f0;background:#f8fafc;">
                <h3 style="font-size:14px;font-weight:800;color:#0f172a;margin:0;">Activos del ítem</h3>
                <p style="font-size:11px;color:#64748b;margin:2px 0 0;">
                    Estos activos pertenecen al ítem. Cambiar de custodio no los desvincula.
                </p>
            </div>

            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;font-size:12px;">
                    <thead>
                        <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
                            <th style="padding:10px 12px;text-align:left;font-size:10px;font-weight:800;color:#64748b;text-transform:uppercase;">Código</th>
                            <th style="padding:10px 12px;text-align:left;font-size:10px;font-weight:800;color:#64748b;text-transform:uppercase;">Activo</th>
                            <th style="padding:10px 12px;text-align:left;font-size:10px;font-weight:800;color:#64748b;text-transform:uppercase;">Custodio actual</th>
                            <th style="padding:10px 12px;text-align:center;font-size:10px;font-weight:800;color:#64748b;text-transform:uppercase;">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activos as $activo)
                            <tr style="border-bottom:1px solid #f1f5f9;">
                                <td style="padding:10px 12px;font-family:ui-monospace,monospace;font-weight:700;color:#2563eb;">
                                    <a href="{{ route('activos.tarjeta', $activo->id_activo) }}" style="color:#2563eb;text-decoration:none;">
                                        {{ $activo->codigo_activo }}
                                    </a>
                                </td>
                                <td style="padding:10px 12px;color:#334155;">
                                    {{ $activo->nombre }}
                                    <div style="font-size:10px;color:#94a3b8;">{{ $activo->categoria?->nombre ?? '—' }}</div>
                                </td>
                                <td style="padding:10px 12px;color:#475569;">
                                    {{ $activo->custodio?->nombre_completo ?? 'Sin custodio' }}
                                </td>
                                <td style="padding:10px 12px;text-align:center;">
                                    <span style="background:#f1f5f9;color:#475569;padding:3px 8px;border-radius:8px;font-size:10px;font-weight:800;">
                                        {{ $activo->estado_registro_texto }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="padding:36px;text-align:center;color:#94a3b8;">
                                    Este ítem todavía no tiene activos asignados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- HISTORIAL --}}
    <div style="background:white;border-radius:14px;box-shadow:0 2px 8px rgba(0,0,0,.04);margin-top:18px;overflow:hidden;">
        <div style="padding:14px 18px;border-bottom:1px solid #e2e8f0;background:#f8fafc;">
            <h3 style="font-size:14px;font-weight:800;color:#0f172a;margin:0;">Historial de titulares</h3>
            <p style="font-size:11px;color:#64748b;margin:2px 0 0;">
                Aunque el registro de la persona no se borre al renunciar, queda asentado quién estuvo antes.
            </p>
        </div>

        <div style="padding:14px 18px;">
            @forelse($item->historial as $h)
                <div style="display:flex;gap:12px;padding:10px 0;border-bottom:1px solid #f8fafc;font-size:13px;">
                    <div style="min-width:96px;color:#64748b;font-weight:700;">
                        {{ \Carbon\Carbon::parse($h->fecha_evento)->format('d/m/Y') }}
                    </div>
                    <div style="min-width:150px;">
                        <span style="background:#eff6ff;color:#1d4ed8;padding:3px 9px;border-radius:8px;font-size:10px;font-weight:800;">
                            {{ $h->tipo }}
                        </span>
                    </div>
                    <div style="color:#334155;flex:1;">
                        @if($h->nombre_anterior)
                            <strong>{{ $h->nombre_anterior }}</strong>
                        @endif
                        @if($h->nombre_anterior && $h->nombre_nuevo)
                            <span style="color:#94a3b8;"> → </span>
                        @endif
                        @if($h->nombre_nuevo)
                            <strong>{{ $h->nombre_nuevo }}</strong>
                        @endif
                        @if(! $h->nombre_anterior && ! $h->nombre_nuevo)
                            <span style="color:#94a3b8;">—</span>
                        @endif
                        @if($h->motivo)
                            <div style="font-size:11px;color:#64748b;margin-top:2px;">{{ $h->motivo }}</div>
                        @endif
                    </div>
                </div>
            @empty
                <p style="font-size:13px;color:#94a3b8;font-style:italic;">Sin movimientos registrados.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
