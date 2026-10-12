@extends('layouts.app')

@section('title', 'Liberación de Custodia')

@section('content')
<div class="container-fluid px-4 py-6">

    {{-- Encabezado corporativo --}}
    <div style="background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%); padding: 30px; border-radius: 18px; color: white; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; margin-bottom: 25px; box-shadow: 0 10px 20px -3px rgba(124, 58, 237, 0.25);">
        <div>
            <span style="background: rgba(255, 255, 255, 0.2); padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">Cierre de Gestión</span>
            <h1 style="font-size: 26px; font-weight: 800; margin: 8px 0 4px 0;">Liberación de Custodia</h1>
            <p style="opacity: 0.9; font-size: 14px; margin: 0;">
                Los custodios de cada ambiente liberan los bienes que tienen a su cargo y los entregan al jefe de carrera.
            </p>
        </div>
        <div style="text-align: right;">
            <div style="font-size: 30px; font-weight: 800;">{{ date('Y') }}</div>
            <div style="font-size: 11px; font-weight: 700; opacity: .85; text-transform: uppercase;">Gestión</div>
        </div>
    </div>

    {{-- Qué es la liberación --}}
    <div style="max-width: 1200px; margin: 0 auto 22px auto; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 14px; padding: 16px 20px; display: flex; align-items: flex-start; gap: 12px;">
        <span style="font-size: 20px;">ℹ️</span>
        <div style="font-size: 13px; color: #1e3a8a; line-height: 1.6;">
            <strong>Al inicio del año el jefe de carrera es custodio de todos los bienes de la carrera.</strong>
            Como no puede atenderlos todos, cada ambiente tiene sus propios custodios (pueden ser 1, 2 o más) que los usan
            durante la gestión. Al cerrar el año, esos custodios firman la liberación y entregan los bienes al jefe de carrera.
            <br>El acta solo deja constancia: <strong>la entrega es presencial y el bien sigue registrado a nombre de su custodio</strong>
            hasta que la Dirección Administrativa actualice el sistema.
        </div>
    </div>

    {{-- Ambientes --}}
    <div style="max-width: 1200px; margin: 0 auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; margin-bottom: 14px;">
            <h2 style="font-size: 18px; font-weight: 800; color: #0f172a; margin: 0;">
                Ambientes listos para liberar
                <span style="background: #ede9fe; color: #5b21b6; padding: 3px 10px; border-radius: 20px; font-size: 12px; vertical-align: middle;">
                    {{ $ambientes->count() }}
                </span>
            </h2>
            <span style="font-size: 12px; color: #64748b; font-weight: 600;">
                El PDF y el Excel se pueden generar por tipo de bien: todos, solo activos fijos o solo no activos.
            </span>
        </div>

        @forelse ($ambientes as $ambiente)
            @php
                $jefe = $ambiente->carrera?->jefeCarrera;
                $soyCustodio = $ambiente->custodios->contains('id_usuario', auth()->id());
                $tipos = ['TODOS' => 'Todos', 'ACTIVO_FIJO' => 'Solo activos', 'NO_ACTIVO' => 'Solo no activos'];
            @endphp
            <div style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; padding: 20px; margin-bottom: 14px; box-shadow: 0 4px 6px -1px rgba(0,0,0,.02);">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px;">

                    <div style="flex: 1 1 320px;">
                        <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                            <h3 style="font-size: 16px; font-weight: 800; color: #0f172a; margin: 0;">
                                {{ $ambiente->nombre }}
                            </h3>
                            <span style="background: #f1f5f9; color: #475569; padding: 2px 8px; border-radius: 6px; font-family: ui-monospace, monospace; font-size: 11px;">
                                {{ $ambiente->codigo }}
                            </span>
                            @if ($soyCustodio)
                                <span style="background: #dcfce7; color: #166534; padding: 2px 8px; border-radius: 6px; font-size: 10px; font-weight: 800; text-transform: uppercase;">
                                    Tú custodias
                                </span>
                            @endif
                        </div>

                        <div style="font-size: 12px; color: #64748b; margin-top: 6px;">
                            {{ $ambiente->carrera?->nombre ?? 'Sin carrera (unidad administrativa)' }}
                            @if ($ambiente->bloque || $ambiente->piso)
                                — Bloque {{ $ambiente->bloque ?? '—' }}, piso {{ $ambiente->piso ?? '—' }}
                            @endif
                        </div>

                        <div style="font-size: 12px; color: #334155; margin-top: 8px;">
                            <strong style="font-weight: 800;">Recibe:</strong>
                            {{ $jefe?->nombre_completo ?? 'Administración (sin jefe de carrera)' }}
                            @if ($jefe)
                                <span style="color: #64748b;">— {{ $jefe->cargo ?? 'Jefe de Carrera' }}</span>
                            @endif
                        </div>

                        <div style="font-size: 12px; color: #334155; margin-top: 4px;">
                            <strong style="font-weight: 800;">Liberan:</strong>
                            @forelse ($ambiente->custodios as $custodio)
                                {{ $custodio->nombre_completo }}@if ($custodio->id_usuario === auth()->id()) (tú)@endif{{ $loop->last ? '' : ', ' }}
                            @empty
                                <span style="color: #dc2626;">Sin custodio asignado</span>
                            @endforelse
                        </div>

                        @if ($ambiente->custodios->contains('id_usuario', auth()->id()))
                            <div style="font-size: 11px; color: #64748b; margin-top: 4px;">
                                @foreach ($ambiente->custodios as $custodio)
                                    @continue($custodio->id_usuario !== auth()->id())
                                    @foreach ($custodio->itemsDelAmbiente ?? [] as $item)
                                        Tu ítem en este ambiente: <strong>{{ $item->numero_item }}</strong>
                                    @endforeach
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div style="display: flex; gap: 10px; flex-shrink: 0;">
                        <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 12px; padding: 12px 16px; text-align: center;">
                            <div style="font-size: 22px; font-weight: 800; color: #1d4ed8;">{{ $ambiente->activos_fijos_count }}</div>
                            <div style="font-size: 10px; font-weight: 800; color: #1e40af; text-transform: uppercase;">Activos fijos</div>
                        </div>
                        <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 12px; padding: 12px 16px; text-align: center;">
                            <div style="font-size: 22px; font-weight: 800; color: #b45309;">{{ $ambiente->no_activos_count }}</div>
                            <div style="font-size: 10px; font-weight: 800; color: #92400e; text-transform: uppercase;">No activos</div>
                        </div>
                    </div>
                </div>

                <div style="display: flex; gap: 8px; flex-wrap: wrap; margin-top: 16px; padding-top: 14px; border-top: 1px solid #f1f5f9;">
                    @foreach ($tipos as $valor => $etiqueta)
                        <a href="{{ route('actas.liberacion.pdf', ['ambiente' => $ambiente->id_ambiente, 'tipo_bien' => $valor]) }}"
                           target="_blank"
                           style="padding:7px 12px;border-radius:9px;font-size:12px;font-weight:800;text-decoration:none;border:1.5px solid #cbd5e1;background:#fff;color:#334155;">
                            📄 PDF · {{ $etiqueta }}
                        </a>
                    @endforeach
                    <span style="width: 100%; height: 1px; background: #f1f5f9; margin: 2px 0;"></span>
                    @foreach ($tipos as $valor => $etiqueta)
                        <a href="{{ route('actas.liberacion.excel', ['ambiente' => $ambiente->id_ambiente, 'tipo_bien' => $valor]) }}"
                           style="padding:7px 12px;border-radius:9px;font-size:12px;font-weight:800;text-decoration:none;border:1.5px solid #a7f3d0;background:#ecfdf5;color:#065f46;">
                            📊 Excel · {{ $etiqueta }}
                        </a>
                    @endforeach
                </div>
            </div>
        @empty
            <div style="background: white; border-radius: 16px; border: 1px dashed #cbd5e1; padding: 40px; text-align: center; color: #64748b; font-weight: 600;">
                No hay ambientes con bienes registrados a tu nombre para liberar.
            </div>
        @endforelse
    </div>

</div>
@endsection