@extends('layouts.app')

@section('title', 'Aprobación de Verificaciones')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- ============================================================ --}}
    {{-- ENCABEZADO CORPORATIVO --}}
    {{-- ============================================================ --}}
    <div style="background: linear-gradient(135deg, #0f766e 0%, #0d9488 50%, #2563eb 100%); padding: 32px; border-radius: 18px; color: white; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 24px; margin-bottom: 25px; box-shadow: 0 10px 20px -3px rgba(13, 148, 136, 0.25);">
        <div style="flex: 1; min-width: 280px;">
            <span style="background: rgba(255, 255, 255, 0.2); padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">
                ✅ Aprobación de Verificaciones
            </span>
            <h1 style="font-size: 28px; font-weight: 800; margin: 8px 0 6px 0;">Pendientes de Aprobación</h1>
            <p style="opacity: 0.95; font-size: 14px; margin: 0; line-height: 1.5;">
                Revise las verificaciones realizadas por el personal de inventario y apruebe o rechace según corresponda.
            </p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; min-width: 340px;">
            <div style="background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.25); padding: 14px 10px; border-radius: 12px; text-align: center;">
                <div style="font-size: 22px; font-weight: 800;">{{ $verificaciones->total() }}</div>
                <div style="font-size: 10px; font-weight: 700; opacity: 0.9; text-transform: uppercase; margin-top: 2px;">Total pendientes</div>
            </div>
            @php
                $correctasCount = $verificaciones->filter(fn($v) => $v->es_correspondencia_correcta && empty($v->observaciones))->count();
                $observadasCount = $verificaciones->filter(fn($v) => !empty($v->observaciones))->count();
            @endphp
            <div style="background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.25); padding: 14px 10px; border-radius: 12px; text-align: center;">
                <div style="font-size: 22px; font-weight: 800; color: #86efac;">{{ $correctasCount }}</div>
                <div style="font-size: 10px; font-weight: 700; opacity: 0.9; text-transform: uppercase; margin-top: 2px;">Correctas</div>
            </div>
            <div style="background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.25); padding: 14px 10px; border-radius: 12px; text-align: center;">
                <div style="font-size: 22px; font-weight: 800; color: #fde047;">{{ $observadasCount }}</div>
                <div style="font-size: 10px; font-weight: 700; opacity: 0.9; text-transform: uppercase; margin-top: 2px;">Con obs.</div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- FILTROS DE BÚSQUEDA --}}
    {{-- ============================================================ --}}
    <div style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; padding: 24px; margin-bottom: 25px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);">
        <form method="GET" action="{{ route('verificaciones-aprobacion.index') }}"
              style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; align-items: flex-end;">

            <div>
                <label style="font-size: 11px; font-weight: 800; color: #64748b; display: block; margin-bottom: 6px; text-transform: uppercase;">
                    Filtrar por Carrera
                </label>
                <select name="carrera_id" onchange="this.form.submit()"
                        style="width: 100%; padding: 11px 14px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; color: #1e293b; cursor: pointer; outline: none;">
                    <option value="todas">Todas las carreras</option>
                    @foreach($carreras as $carrera)
                        <option value="{{ $carrera->id_carrera ?? $carrera->id }}" {{ request('carrera_id') == ($carrera->id_carrera ?? $carrera->id) ? 'selected' : '' }}>
                            {{ $carrera->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="font-size: 11px; font-weight: 800; color: #64748b; display: block; margin-bottom: 6px; text-transform: uppercase;">
                    Buscar Activo
                </label>
                <input type="text" name="search"
                       value="{{ request('search') }}"
                       placeholder="Código, nombre u observación..."
                       style="width: 100%; padding: 11px 14px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; color: #1e293b; outline: none;">
            </div>

            <div style="display: flex; gap: 8px;">
                <button type="submit"
                        style="flex: 1; padding: 11px 16px; background: #2563eb; color: white; border: none; border-radius: 10px; font-size: 13px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 6px; box-shadow: 0 2px 4px rgba(37,99,235,0.2);">
                    🔍 Filtrar
                </button>
                @if(request('carrera_id') || request('search'))
                    <a href="{{ route('verificaciones-aprobacion.index') }}"
                       style="padding: 11px 16px; background: #f1f5f9; color: #334155; border-radius: 10px; font-size: 13px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; justify-content: center;">
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
        <div style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; padding: 60px 20px; text-align: center; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);">
            <div style="font-size: 48px; margin-bottom: 12px;">✅</div>
            <h3 style="color: #334155; font-weight: 800; font-size: 18px; margin: 0 0 6px 0;">
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
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 14px; padding-bottom: 8px; border-bottom: 2px solid #e2e8f0;">
                    <span style="font-size: 18px;">🎓</span>
                    <h2 style="font-size: 16px; font-weight: 800; color: #0f172a; margin: 0;">{{ $nombreCarrera }}</h2>
                </div>

                @foreach($gruposAmbiente as $nombreAmbiente => $subGrupos)
                    @php
                        $totalAmbiente = $subGrupos->flatten()->count();
                        $correctas = $subGrupos['correctas'] ?? collect();
                        $observadas = $subGrupos['observadas'] ?? collect();
                    @endphp

                    <div style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; padding: 20px; margin-bottom: 16px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid #f1f5f9;">
                            <div>
                                <h3 style="font-size: 15px; font-weight: 800; color: #1e293b; margin: 0;">
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
                                    <button type="submit"
                                            style="padding: 8px 16px; background: #16a34a; color: white; border: none; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 2px 4px rgba(22,163,74,0.2);">
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

                            <div style="background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0; border-left: 5px solid {{ $cardBorderColor }}; padding: 16px; margin-bottom: 12px;">
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
                                            <span style="background: #fee2e2; color: #991b1b; padding: 2px 8px; border-radius: 6px; font-size: 9px; font-weight: 800; border: 1px solid #fecaca;">
                                                ⚠ Fuera de ubicación
                                            </span>
                                        @elseif($tieneObservaciones)
                                            <span style="background: #fef9c3; color: #854d0e; padding: 2px 8px; border-radius: 6px; font-size: 9px; font-weight: 800; border: 1px solid #fde047;">
                                                ⚠ Con observación
                                            </span>
                                        @else
                                            <span style="background: #dcfce7; color: #166534; padding: 2px 8px; border-radius: 6px; font-size: 9px; font-weight: 800; border: 1px solid #bbf7d0;">
                                                ✓ Correcto
                                            </span>
                                        @endif

                                        @if($ver->cambio_estado_fisico_solicitado ?? $ver->cambioEstadoFisicoSolicitado)
                                            <span style="background: #e0e7ff; color: #3730a3; padding: 2px 8px; border-radius: 6px; font-size: 9px; font-weight: 800; border: 1px solid #c7d2fe;">
                                                📝 Solicitud de cambio de estado
                                            </span>
                                        @endif

                                        @if($solicitarBaja)
                                            <span style="background: #dc2626; color: white; padding: 2px 8px; border-radius: 6px; font-size: 9px; font-weight: 800;">
                                                🗑 Solicita baja
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 8px; font-size: 12px; color: #475569; margin-bottom: 12px;">
                                    <div><strong>Carrera:</strong> {{ $ver->activo->ambiente->carrera->nombre ?? 'Sin carrera' }}</div>
                                    <div><strong>Ambiente:</strong> {{ $ver->activo->ambiente->nombre ?? 'Sin ambiente' }}</div>
                                    <div><strong>Verificador:</strong> {{ $ver->verificador->nombre_completo ?? 'N/A' }}</div>
                                    <div><strong>Fecha:</strong> {{ $ver->fecha_verificacion ? $ver->fecha_verificacion->format('d/m/Y H:i') : 'N/A' }}</div>
                                </div>

                                @if($tieneObservaciones)
                                    <div style="margin-bottom: 12px;">
                                        <div style="font-size: 10px; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 4px;">Observación registrada:</div>
                                        <div style="background: #fffbeb; border: 1px solid #fde047; border-radius: 8px; padding: 10px 12px; font-size: 12px; color: #78350f; line-height: 1.5;">
                                            {{ $ver->observaciones }}
                                        </div>
                                    </div>
                                @endif

                                <div style="padding-top: 10px; border-top: 1px solid #e2e8f0; display: flex; gap: 8px; flex-wrap: wrap;">
                                    @if($estadoAprob === 'PENDIENTE' || $estadoAprob === \App\Models\VerificacionFisica::ESTADO_PENDIENTE)
                                        <form method="POST" action="{{ route('verificaciones-aprobacion.aprobar', $ver->id_verificacion ?? $ver->id) }}" class="form-aprobar" style="margin: 0;">
                                            @csrf
                                            <input type="hidden" name="aplicar_cambio_estado_fisico" value="1">
                                            <button type="submit" style="padding: 7px 14px; background: #16a34a; color: white; border: none; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer;">
                                                ✓ Aprobar
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('verificaciones-aprobacion.rechazar', $ver->id_verificacion ?? $ver->id) }}" class="form-rechazar" style="margin: 0;">
                                            @csrf
                                            <button type="submit" style="padding: 7px 14px; background: #dc2626; color: white; border: none; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer;">
                                                ✕ Rechazar
                                            </button>
                                        </form>
                                    @endif

                                    @if($estadoAprob === 'RECHAZADA')
                                        <form method="POST" action="{{ route('verificaciones-aprobacion.corregir', $ver->id_verificacion ?? $ver->id) }}" class="form-corregir" style="margin: 0;">
                                            @csrf
                                            <button type="submit" style="padding: 7px 14px; background: #ca8a04; color: white; border: none; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer;">
                                                ↻ Marcar como Corregida
                                            </button>
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
