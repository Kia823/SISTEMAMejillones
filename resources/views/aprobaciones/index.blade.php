@extends('layouts.app')

@section('title', 'Aprobaciones')

@section('content')
<div class="container-fluid px-4 py-6">

    {{-- ENCABEZADO --}}
    <div style="background: linear-gradient(135deg, #065f46 0%, #047857 100%); padding: 30px; border-radius: 18px; color: white; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; margin-bottom: 25px; box-shadow: 0 10px 20px -3px rgba(6, 95, 70, 0.25);">
        <div>
            <span style="background: rgba(255,255,255,0.2); padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">Administración</span>
            <h1 style="font-size: 26px; font-weight: 800; margin: 8px 0 4px 0;">Bandeja de Aprobaciones</h1>
            <p style="opacity: 0.9; font-size: 14px; margin: 0;">Nada se aplica en el inventario hasta que usted lo apruebe.</p>
        </div>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <span style="background: rgba(255,255,255,0.18); padding: 10px 16px; border-radius: 12px; font-size: 13px; font-weight: 700;">
                ✅ Verificaciones: {{ $totales['verificaciones'] }}
            </span>
            <span style="background: rgba(255,255,255,0.18); padding: 10px 16px; border-radius: 12px; font-size: 13px; font-weight: 700;">
                📄 Bajas: {{ $totales['bajas'] }}
            </span>
            <span style="background: rgba(255,255,255,0.18); padding: 10px 16px; border-radius: 12px; font-size: 13px; font-weight: 700;">
                🔄 Transferencias: {{ $totales['transferencias'] }}
            </span>
        </div>
    </div>

    {{-- FILTRO --}}
    <div style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; padding: 16px; margin-bottom: 25px;">
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            @php
                $filtros = [
                    'TODOS' => 'Todo',
                    'verificaciones' => 'Verificaciones',
                    'bajas' => 'Bajas y sustituciones',
                    'transferencias' => 'Transferencias',
                ];
            @endphp
            @foreach($filtros as $clave => $texto)
                <a href="{{ route('aprobaciones.index', ['tipo' => $clave]) }}"
                   style="padding: 8px 16px; border-radius: 10px; font-size: 12px; font-weight: 800; text-decoration: none;
                          background: {{ $tipo === $clave ? '#047857' : '#f1f5f9' }};
                          color: {{ $tipo === $clave ? 'white' : '#334155' }};">
                    {{ $texto }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- TRANSFERENCIAS --}}
    @if($transferencias->isNotEmpty())
        <div style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden; margin-bottom: 25px;">
            <div style="padding: 16px 20px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; font-weight: 800; font-size: 14px; color: #0f172a;">
                🔄 Transferencias pendientes
            </div>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 13px; text-align: left;">
                    <thead style="background: #f8fafc; color: #475569; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px;">
                        <tr>
                            <th style="padding: 12px 16px;">Acta</th>
                            <th style="padding: 12px 16px;">Activo</th>
                            <th style="padding: 12px 16px;">Origen → Destino</th>
                            <th style="padding: 12px 16px;">Custodio</th>
                            <th style="padding: 12px 16px; text-align: center;">Decisión</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transferencias as $t)
                            <tr style="border-top: 1px solid #f1f5f9;">
                                <td style="padding: 12px 16px; font-family: monospace; font-weight: 800; color: #1d4ed8;">{{ $t->numero_acta }}</td>
                                <td style="padding: 12px 16px;">
                                    <strong>{{ $t->activo?->codigo_activo }}</strong>
                                    <span style="color: #64748b;">{{ $t->activo?->nombre }}</span>
                                </td>
                                <td style="padding: 12px 16px; color: #334155;">
                                    {{ $t->origenAmbiente?->nombre ?? '—' }} → {{ $t->destinoAmbiente?->nombre ?? '—' }}
                                </td>
                                <td style="padding: 12px 16px; color: #334155;">{{ $t->custodioNuevo?->nombre_completo ?? '—' }}</td>
                                <td style="padding: 12px 16px;">
                                    <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                                        <form method="POST" action="{{ route('actas.transferencia.aprobar', $t->id_acta_transferencia) }}">
                                            @csrf
                                            <button type="submit" style="padding: 6px 12px; background: #10b981; color: white; border: none; border-radius: 8px; font-size: 11px; font-weight: 700; cursor: pointer;">✅ Aprobar</button>
                                        </form>
                                        <form method="POST" action="{{ route('actas.transferencia.rechazar', $t->id_acta_transferencia) }}">
                                            @csrf
                                            <button type="submit" style="padding: 6px 12px; background: #ef4444; color: white; border: none; border-radius: 8px; font-size: 11px; font-weight: 700; cursor: pointer;">❌ Rechazar</button>
                                        </form>
                                        <a href="{{ route('actas.transferencia.show', $t->id_acta_transferencia) }}" style="padding: 6px 12px; background: #eff6ff; color: #1d4ed8; border-radius: 8px; font-size: 11px; font-weight: 700; text-decoration: none;">👁️ Ver</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- BAJAS Y SUSTITUCIONES --}}
    @if($bajas->isNotEmpty())
        <div style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden; margin-bottom: 25px;">
            <div style="padding: 16px 20px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; font-weight: 800; font-size: 14px; color: #0f172a;">
                📄 Bajas y sustituciones pendientes
            </div>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 13px; text-align: left;">
                    <thead style="background: #f8fafc; color: #475569; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px;">
                        <tr>
                            <th style="padding: 12px 16px;">Acta</th>
                            <th style="padding: 12px 16px;">Activo que sale</th>
                            <th style="padding: 12px 16px;">Entra</th>
                            <th style="padding: 12px 16px;">Motivo</th>
                            <th style="padding: 12px 16px;">Solicitó</th>
                            <th style="padding: 12px 16px; text-align: center;">Decisión</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bajas as $b)
                            <tr style="border-top: 1px solid #f1f5f9;">
                                <td style="padding: 12px 16px; font-family: monospace; font-weight: 800; color: #7c2d12;">{{ $b->numero_acta }}</td>
                                <td style="padding: 12px 16px;">
                                    <strong>{{ $b->activo?->codigo_activo }}</strong>
                                    <span style="color: #64748b;">{{ $b->activo?->nombre }}</span>
                                </td>
                                <td style="padding: 12px 16px;">
                                    @if($b->es_sustitucion)
                                        <span style="background: #ecfdf5; color: #065f46; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 800;">
                                            {{ $b->datos_sustituto['codigo_activo'] ?? '—' }}
                                        </span>
                                        <div style="font-size: 11px; color: #64748b;">Sustitución (el malo va al almacén)</div>
                                    @else
                                        <span style="color: #94a3b8;">—</span>
                                    @endif
                                </td>
                                <td style="padding: 12px 16px; color: #475569; max-width: 260px;">
                                    {{ \Illuminate\Support\Str::limit($b->motivo, 60) }}
                                </td>
                                <td style="padding: 12px 16px; color: #334155;">{{ $b->usuario?->nombre_completo ?? '—' }}</td>
                                <td style="padding: 12px 16px;">
                                    <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                                        <form method="POST" action="{{ route('actas.baja.aprobar', $b->id_acta_baja) }}">
                                            @csrf
                                            <button type="submit" style="padding: 6px 12px; background: #10b981; color: white; border: none; border-radius: 8px; font-size: 11px; font-weight: 700; cursor: pointer;">✅ Aprobar</button>
                                        </form>
                                        <form method="POST" action="{{ route('actas.baja.rechazar', $b->id_acta_baja) }}">
                                            @csrf
                                            <button type="submit" style="padding: 6px 12px; background: #ef4444; color: white; border: none; border-radius: 8px; font-size: 11px; font-weight: 700; cursor: pointer;">❌ Rechazar</button>
                                        </form>
                                        <a href="{{ route('actas.baja.show', $b->id_acta_baja) }}" style="padding: 6px 12px; background: #eff6ff; color: #1d4ed8; border-radius: 8px; font-size: 11px; font-weight: 700; text-decoration: none;">👁️ Ver</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- VERIFICACIONES --}}
    @if($verificaciones->isNotEmpty())
        <div style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden;">
            <div style="padding: 16px 20px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; font-weight: 800; font-size: 14px; color: #0f172a;">
                ✅ Verificaciones pendientes
            </div>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 13px; text-align: left;">
                    <thead style="background: #f8fafc; color: #475569; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px;">
                        <tr>
                            <th style="padding: 12px 16px;">Activo</th>
                            <th style="padding: 12px 16px;">Ambiente</th>
                            <th style="padding: 12px 16px;">Estado</th>
                            <th style="padding: 12px 16px;">Observaciones</th>
                            <th style="padding: 12px 16px;">Verificó</th>
                            <th style="padding: 12px 16px; text-align: center;">Decisión</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($verificaciones as $v)
                            <tr style="border-top: 1px solid #f1f5f9;">
                                <td style="padding: 12px 16px;">
                                    <strong>{{ $v->activo?->codigo_activo }}</strong>
                                    <span style="color: #64748b;">{{ $v->activo?->nombre }}</span>
                                </td>
                                <td style="padding: 12px 16px; color: #334155;">{{ $v->ambienteEscaneo?->nombre ?? $v->activo?->ambiente?->nombre ?? '—' }}</td>
                                <td style="padding: 12px 16px; color: #334155;">{{ $v->estado_fisico_reportado }}</td>
                                <td style="padding: 12px 16px; color: #475569; max-width: 260px;">
                                    {{ \Illuminate\Support\Str::limit($v->observaciones ?: 'Sin observaciones', 60) }}
                                </td>
                                <td style="padding: 12px 16px; color: #334155;">{{ $v->verificador?->nombre_completo ?? '—' }}</td>
                                <td style="padding: 12px 16px; white-space: nowrap;">
                                    <div style="display: flex; gap: 6px;">
                                        <form method="POST" action="{{ route('verificaciones-aprobacion.aprobar', $v->id_verificacion) }}">
                                            @csrf
                                            <button type="submit" style="padding: 6px 12px; background: #10b981; color: white; border: none; border-radius: 8px; font-size: 11px; font-weight: 700; cursor: pointer;">✅ Aprobar</button>
                                        </form>
                                        <form method="POST" action="{{ route('verificaciones-aprobacion.rechazar', $v->id_verificacion) }}">
                                            @csrf
                                            <input type="hidden" name="comentario" value="Rechazado desde la bandeja de aprobaciones.">
                                            <button type="submit" style="padding: 6px 12px; background: #ef4444; color: white; border: none; border-radius: 8px; font-size: 11px; font-weight: 700; cursor: pointer;">❌ Rechazar</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @if($transferencias->isEmpty() && $bajas->isEmpty() && $verificaciones->isEmpty())
        <div style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; padding: 60px 20px; text-align: center; color: #64748b; font-weight: 600;">
            No hay nada pendiente de aprobación.
        </div>
    @endif
</div>
@endsection