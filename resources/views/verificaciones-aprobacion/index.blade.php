@extends('layouts.app')

@section('title', 'Aprobación de Verificaciones')

@section('content')

{{-- ============================================================ --}}
{{-- ESTILOS PROPIOS (MISMO LENGUAJE QUE LOGIN Y PANEL) --}}
{{-- ============================================================ --}}
<style>
    .sis-card {
        position: relative;
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(15, 23, 42, .03), 0 2px 4px -2px rgba(15, 23, 42, .04);
        overflow: hidden;
        padding: 24px;
    }
    .sis-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        background: linear-gradient(90deg, #2563eb, #7c3aed 55%, #dc2626);
    }

    .sis-hero {
        position: relative;
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 45%, #b91c1c 100%);
        border-radius: 20px;
        color: #fff;
        padding: 32px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 24px;
        margin-bottom: 25px;
        overflow: hidden;
        box-shadow: 0 20px 40px -20px rgba(37, 99, 235, .55);
    }
    .sis-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(700px 360px at 88% 12%, rgba(255,255,255,.15), transparent 60%),
            radial-gradient(560px 320px at 8% 92%, rgba(0,0,0,.28), transparent 60%);
        pointer-events: none;
    }
    .sis-hero > * { position: relative; z-index: 1; }

    .sis-pill {
        display: inline-block;
        padding: 5px 14px;
        border-radius: 999px;
        background: rgba(255, 255, 255, .18);
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .08em;
        border: 1px solid rgba(255,255,255,.22);
        backdrop-filter: blur(6px);
    }

    .sis-stat {
        background: rgba(255, 255, 255, .12);
        border: 1px solid rgba(255, 255, 255, .22);
        border-radius: 14px;
        padding: 14px 10px;
        text-align: center;
        backdrop-filter: blur(6px);
        transition: transform .2s ease, background .2s ease;
    }
    .sis-stat:hover {
        background: rgba(255, 255, 255, .18);
        transform: translateY(-2px);
    }
    .sis-stat-num { font-size: 24px; font-weight: 800; line-height: 1.2; }
    .sis-stat-label {
        font-size: 10px;
        font-weight: 700;
        opacity: .9;
        text-transform: uppercase;
        letter-spacing: .06em;
        margin-top: 2px;
    }

    .sis-btn-primary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 11px 20px;
        border-radius: 12px;
        color: #ffffff;
        font-weight: 700;
        font-size: 13.5px;
        text-decoration: none;
        border: none;
        cursor: pointer;
        background: linear-gradient(135deg, #1d4ed8, #2563eb 45%, #dc2626);
        box-shadow: 0 12px 22px -10px rgba(37, 99, 235, .6);
        transition: all .2s ease;
    }
    .sis-btn-primary:hover {
        transform: translateY(-2px);
        filter: brightness(1.06);
        box-shadow: 0 18px 28px -12px rgba(37, 99, 235, .7);
        color: #ffffff;
    }

    .sis-btn-ghost {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 11px 18px;
        border-radius: 12px;
        background: #f8fafc;
        color: #1e3a8a;
        border: 1.5px solid #dbe3f0;
        font-weight: 700;
        font-size: 13px;
        text-decoration: none;
        cursor: pointer;
        transition: all .2s ease;
    }
    .sis-btn-ghost:hover {
        background: #eef4ff;
        border-color: #bfdbfe;
        color: #1d4ed8;
        transform: translateY(-1px);
        box-shadow: 0 10px 18px -12px rgba(37, 99, 235, .6);
    }

    .sis-input,
    .sis-select {
        width: 100%;
        padding: 11px 14px;
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        font-size: 13px;
        color: #0f172a;
        outline: none;
        transition: all .2s ease;
    }
    .sis-input:focus,
    .sis-select:focus {
        border-color: #2563eb;
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, .12);
    }
    .sis-label {
        font-size: 11px;
        font-weight: 800;
        color: #64748b;
        display: block;
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: .06em;
    }

    .sis-section-title {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 14px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e2e8f0;
    }
    .sis-section-title h2 {
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
    }

    .sis-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 9px;
        border-radius: 6px;
        font-size: 9.5px;
        font-weight: 800;
        letter-spacing: .02em;
        text-transform: uppercase;
    }
    .sis-badge-danger { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    .sis-badge-warn   { background: #fef9c3; color: #854d0e; border: 1px solid #fde047; }
    .sis-badge-ok     { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    .sis-badge-info   { background: #e0e7ff; color: #3730a3; border: 1px solid #c7d2fe; }
    .sis-badge-solid-red { background: #dc2626; color: #ffffff; border: 1px solid #b91c1c; }

    .sis-verif-card {
        background: #f8fafc;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        padding: 16px;
        margin-bottom: 12px;
        transition: box-shadow .2s ease, transform .2s ease;
    }
    .sis-verif-card:hover {
        box-shadow: 0 10px 22px -16px rgba(15, 23, 42, .35);
        transform: translateY(-1px);
    }

    .sis-observacion {
        background: #fffbeb;
        border: 1px solid #fde047;
        border-left: 3px solid #ca8a04;
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 12px;
        color: #78350f;
        line-height: 1.5;
    }

    .sis-btn-approve {
        padding: 7px 14px;
        background: #16a34a;
        color: #ffffff;
        border: none;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 4px 10px -4px rgba(22, 163, 74, .5);
        transition: all .15s ease;
    }
    .sis-btn-approve:hover { background: #15803d; transform: translateY(-1px); }

    .sis-btn-reject {
        padding: 7px 14px;
        background: #dc2626;
        color: #ffffff;
        border: none;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 4px 10px -4px rgba(220, 38, 38, .5);
        transition: all .15s ease;
    }
    .sis-btn-reject:hover { background: #b91c1c; transform: translateY(-1px); }

    .sis-btn-correct {
        padding: 7px 14px;
        background: #ca8a04;
        color: #ffffff;
        border: none;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 4px 10px -4px rgba(202, 138, 4, .5);
        transition: all .15s ease;
    }
    .sis-btn-correct:hover { background: #a16207; transform: translateY(-1px); }

    .sis-empty {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid #e2e8f0;
        padding: 60px 20px;
        text-align: center;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,.02);
    }
    .sis-empty::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        background: linear-gradient(90deg, #2563eb, #7c3aed 55%, #dc2626);
    }

    @media (max-width: 900px) {
        .sis-hero { padding: 24px; }
    }
</style>

<div class="container-fluid px-4 py-4">

    {{-- ============================================================ --}}
    {{-- ENCABEZADO CORPORATIVO --}}
    {{-- ============================================================ --}}
    <div class="sis-hero">
        <div style="flex: 1; min-width: 280px;">
            <span class="sis-pill">✅ Aprobación de Verificaciones</span>
            <h1 style="font-size: 28px; font-weight: 800; margin: 10px 0 6px 0;">Pendientes de Aprobación</h1>
            <p style="opacity: .95; font-size: 14px; margin: 0; line-height: 1.5;">
                Revise las verificaciones realizadas por el personal de inventario y apruebe o rechace según corresponda.
            </p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; min-width: 340px;">
            <div class="sis-stat">
                <div class="sis-stat-num">{{ $verificaciones->total() }}</div>
                <div class="sis-stat-label">Total pendientes</div>
            </div>
            @php
                $correctasCount = $verificaciones->filter(fn($v) => $v->es_correspondencia_correcta && empty($v->observaciones))->count();
                $observadasCount = $verificaciones->filter(fn($v) => !empty($v->observaciones))->count();
            @endphp
            <div class="sis-stat">
                <div class="sis-stat-num" style="color:#86efac;">{{ $correctasCount }}</div>
                <div class="sis-stat-label">Correctas</div>
            </div>
            <div class="sis-stat">
                <div class="sis-stat-num" style="color:#fde047;">{{ $observadasCount }}</div>
                <div class="sis-stat-label">Con obs.</div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- FILTROS DE BÚSQUEDA --}}
    {{-- ============================================================ --}}
    <div class="sis-card" style="margin-bottom: 25px;">
        <form method="GET" action="{{ route('verificaciones-aprobacion.index') }}"
              style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; align-items: flex-end;">

            <div>
                <label class="sis-label">Filtrar por Carrera</label>
                <select name="carrera_id" onchange="this.form.submit()" class="sis-select" style="cursor:pointer;">
                    <option value="todas">Todas las carreras</option>
                    @foreach($carreras as $carrera)
                        <option value="{{ $carrera->id_carrera ?? $carrera->id }}" {{ request('carrera_id') == ($carrera->id_carrera ?? $carrera->id) ? 'selected' : '' }}>
                            {{ $carrera->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="sis-label">Buscar Activo</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Código, nombre u observación..." class="sis-input">
            </div>

            <div style="display: flex; gap: 8px;">
                <button type="submit" class="sis-btn-primary" style="flex: 1;">
                    🔍 Filtrar
                </button>
                @if(request('carrera_id') || request('search'))
                    <a href="{{ route('verificaciones-aprobacion.index') }}" class="sis-btn-ghost">
                        Limpiar
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- ============================================================ --}}
    {{-- LISTADO DE VERIFICACIONES --}}
    {{-- ============================================================ --}}
    @if($verificaciones->isEmpty())
        <div class="sis-empty">
            <div style="width: 88px; height: 88px; margin: 0 auto 16px; padding: 3px; border-radius: 24px; background: linear-gradient(135deg, #2563eb, #dc2626); display: flex; align-items: center; justify-content: center; box-shadow: 0 14px 28px -10px rgba(37, 99, 235, .55);">
                <div style="width: 100%; height: 100%; background: #ffffff; border-radius: 21px; display: flex; align-items: center; justify-content: center; font-size: 40px;">✅</div>
            </div>
            <h3 style="color: #0f172a; font-weight: 800; font-size: 18px; margin: 0 0 6px 0;">
                No hay verificaciones pendientes
            </h3>
            <p style="color: #64748b; font-size: 13px; margin: 0;">
                Todas las verificaciones han sido procesadas correctamente.
            </p>
        </div>
    @else
        @php
            $verificacionesAgrupadas = $verificaciones->getCollection()
                ->filter(fn($v) => $v->activo && $v->activo->ambiente)
                ->groupBy(fn($v) => $v->activo->ambiente->carrera->nombre ?? 'Sin carrera')
                ->map(function ($grupoVerifs) {
                    return $grupoVerifs
                        ->groupBy(fn($v) => $v->activo->ambiente->nombre ?? 'Sin ambiente')
                        ->map(function ($subGrupo) {
                            return $subGrupo->groupBy(function ($v) {
                                $tieneObs = !empty($v->observaciones);
                                $esCorrecto = $v->es_correspondencia_correcta && !$tieneObs && !$v->solicitar_baja;
                                return $esCorrecto ? 'correctas' : 'observadas';
                            });
                        });
                });
        @endphp

        @foreach($verificacionesAgrupadas as $nombreCarrera => $gruposAmbiente)
            <div style="margin-bottom: 25px;">
                <div class="sis-section-title">
                    <span style="font-size: 18px;">🎓</span>
                    <h2>{{ $nombreCarrera }}</h2>
                </div>

                @foreach($gruposAmbiente as $nombreAmbiente => $subGrupos)
                    @php
                        $totalAmbiente = $subGrupos->flatten()->count();
                        $correctas = $subGrupos['correctas'] ?? collect();
                        $observadas = $subGrupos['observadas'] ?? collect();
                    @endphp

                    <div class="sis-card" style="margin-bottom: 16px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid #f1f5f9;">
                            <div>
                                <h3 style="font-size: 15px; font-weight: 800; color: #0f172a; margin: 0;">
                                    🏢 {{ $nombreAmbiente }}
                                </h3>
                                <p style="font-size: 11px; color: #64748b; margin: 2px 0 0 0; font-weight: 600;">
                                    {{ $totalAmbiente }} activo(s) pendiente(s) de aprobación
                                </p>
                            </div>
                            @if($correctas->isNotEmpty())
                                <form method="POST"
                                      action="{{ route('verificaciones-aprobacion.aprobar-ambiente', $correctas->first()->ambiente_escaneo_id) }}"
                                      class="form-aprobar-masivo" style="margin: 0;">
                                    @csrf
                                    <button type="submit" class="sis-btn-approve">
                                        ✓ Aprobar {{ $correctas->count() }} correcto(s)
                                    </button>
                                </form>
                            @endif
                        </div>

                        @forelse($observadas as $ver)
                            @php
                                $tieneObservaciones = !empty($ver->observaciones);
                                $esFueraUbicacion = !$ver->es_correspondencia_correcta;
                                $solicitarBaja = $ver->solicitar_baja;
                                $estadoAprob = $ver->estado_aprobacion;

                                $cardBorderColor = '#16a34a';
                                if ($esFueraUbicacion) $cardBorderColor = '#dc2626';
                                elseif ($tieneObservaciones) $cardBorderColor = '#ca8a04';
                            @endphp

                            <div class="sis-verif-card" style="border-left: 5px solid {{ $cardBorderColor }};">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 10px; margin-bottom: 12px;">
                                    <div>
                                        <h4 style="font-size: 14px; font-weight: 800; color: #0f172a; margin: 0;">
                                            {{ $ver->activo->nombre ?? 'Sin nombre' }}
                                        </h4>
                                        <p style="font-size: 11px; color: #64748b; margin: 3px 0 0 0;">
                                            Código: <strong style="font-family: monospace; color: #1e293b;">{{ $ver->activo->codigo_activo ?? '—' }}</strong> · Serie: {{ $ver->activo->numero_serie ?? 'S/N' }}
                                        </p>
                                    </div>

                                    <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                                        @if($esFueraUbicacion)
                                            <span class="sis-badge sis-badge-danger">⚠ Fuera de ubicación</span>
                                        @elseif($tieneObservaciones)
                                            <span class="sis-badge sis-badge-warn">⚠ Con observación</span>
                                        @else
                                            <span class="sis-badge sis-badge-ok">✓ Correcto</span>
                                        @endif

                                        @if($ver->cambio_estado_fisico_solicitado ?? $ver->cambioEstadoFisicoSolicitado)
                                            <span class="sis-badge sis-badge-info">📝 Cambio de estado</span>
                                        @endif

                                        @if($solicitarBaja)
                                            <span class="sis-badge sis-badge-solid-red">🗑 Solicita baja</span>
                                        @endif
                                    </div>
                                </div>

                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 8px; font-size: 12px; color: #475569; margin-bottom: 12px;">
                                    <div><strong style="color:#0f172a;">Carrera:</strong> {{ $ver->activo->ambiente->carrera->nombre ?? 'Sin carrera' }}</div>
                                    <div><strong style="color:#0f172a;">Ambiente:</strong> {{ $ver->activo->ambiente->nombre ?? 'Sin ambiente' }}</div>
                                    <div><strong style="color:#0f172a;">Verificador:</strong> {{ $ver->verificador->nombre_completo ?? 'N/A' }}</div>
                                    <div><strong style="color:#0f172a;">Fecha:</strong> {{ $ver->fecha_verificacion ? $ver->fecha_verificacion->format('d/m/Y H:i') : 'N/A' }}</div>
                                </div>

                                @if($tieneObservaciones)
                                    <div style="margin-bottom: 12px;">
                                        <div style="font-size: 10px; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 4px; letter-spacing: .06em;">
                                            Observación registrada:
                                        </div>
                                        <div class="sis-observacion">
                                            {{ $ver->observaciones }}
                                        </div>
                                    </div>
                                @endif

                                <div style="padding-top: 10px; border-top: 1px solid #e2e8f0; display: flex; gap: 8px; flex-wrap: wrap;">
                                    @if($estadoAprob === 'PENDIENTE' || $estadoAprob === \App\Models\VerificacionFisica::ESTADO_PENDIENTE)
                                        <form method="POST" action="{{ route('verificaciones-aprobacion.aprobar', $ver->id_verificacion ?? $ver->id) }}" class="form-aprobar" style="margin: 0;">
                                            @csrf
                                            <input type="hidden" name="aplicar_cambio_estado_fisico" value="1">
                                            <button type="submit" class="sis-btn-approve">✓ Aprobar</button>
                                        </form>

                                        <form method="POST" action="{{ route('verificaciones-aprobacion.rechazar', $ver->id_verificacion ?? $ver->id) }}" class="form-rechazar" style="margin: 0;">
                                            @csrf
                                            <button type="submit" class="sis-btn-reject">✕ Rechazar</button>
                                        </form>
                                    @endif

                                    @if($estadoAprob === 'RECHAZADA')
                                        <form method="POST" action="{{ route('verificaciones-aprobacion.corregir', $ver->id_verificacion ?? $ver->id) }}" class="form-corregir" style="margin: 0;">
                                            @csrf
                                            <button type="submit" class="sis-btn-correct">↻ Marcar como Corregida</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p style="text-align: center; color: #94a3b8; padding: 15px; font-size: 12px; font-style: italic; margin: 0;">
                                No hay elementos observados pendientes en este ambiente.
                            </p>
                        @endforelse
                    </div>
                @endforeach
            </div>
        @endforeach
    @endif

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Alertas de confirmación con SweetAlert2
        document.querySelectorAll('form.form-aprobar').forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                Swal.fire({
                    title: '¿Aprobar esta verificación?',
                    text: 'Se aplicarán los cambios correspondientes al inventario.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#16a34a',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Sí, aprobar',
                    cancelButtonText: 'Cancelar'
                }).then(res => { if (res.isConfirmed) form.submit(); });
            });
        });

        document.querySelectorAll('form.form-aprobar-masivo').forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                Swal.fire({
                    title: '¿Aprobar todos los correctos?',
                    text: 'Se aprobarán masivamente todos los ítems correctos de este ambiente.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#16a34a',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Sí, aprobar todos',
                    cancelButtonText: 'Cancelar'
                }).then(res => { if (res.isConfirmed) form.submit(); });
            });
        });

        document.querySelectorAll('form.form-rechazar').forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                Swal.fire({
                    title: '¿Rechazar verificación?',
                    input: 'textarea',
                    inputLabel: 'Motivo del rechazo *',
                    inputPlaceholder: 'Escribe el motivo...',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Rechazar',
                    cancelButtonText: 'Cancelar',
                    inputValidator: (value) => (!value || !value.trim()) ? 'Debes ingresar un motivo' : null
                }).then(result => {
                    if (result.isConfirmed && result.value) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'comentario';
                        input.value = result.value.trim();
                        form.appendChild(input);
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endsection