@extends('layouts.app')

@section('title', 'Verificación Física')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- ============================================================ --}}
    {{-- ENCABEZADO --}}
    {{-- ============================================================ --}}
    <div style="background: linear-gradient(135deg, #0f766e 0%, #0d9488 50%, #2563eb 100%); padding: 32px; border-radius: 18px; color: white; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 24px; margin-bottom: 25px; box-shadow: 0 10px 20px -3px rgba(13, 148, 136, 0.25);">
        <div style="flex: 1; min-width: 280px;">
            <span style="background: rgba(255, 255, 255, 0.2); padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">
                🔍 Auditoría de Activos
            </span>
            <h1 style="font-size: 28px; font-weight: 800; margin: 8px 0 6px 0;">Verificación por Ambiente</h1>
            <p style="opacity: 0.95; font-size: 14px; margin: 0; line-height: 1.5;">
                Selecciona la carrera y el ambiente para verificar la presencia física de los activos mediante códigos QR o de manera manual.
            </p>
        </div>

        @if(isset($ambienteSeleccionado) && $ambienteSeleccionado)
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; min-width: 340px;">
                <div style="background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.25); padding: 14px 10px; border-radius: 12px; text-align: center;">
                    <div id="contador-total" style="font-size: 22px; font-weight: 800;">{{ $activos->count() }}</div>
                    <div style="font-size: 10px; font-weight: 700; opacity: 0.9; text-transform: uppercase; margin-top: 2px;">Total</div>
                </div>
                @php
                    $verificadosCount = 0;
                    $observadosCount = 0;
                    foreach ($activos as $a) {
                        $lv = $a->verificaciones->first();
                        if ($lv) {
                            if ($lv->observaciones && trim($lv->observaciones) !== '') {
                                $observadosCount++;
                            } elseif ($lv->es_correspondencia_correcta) {
                                $verificadosCount++;
                            }
                        }
                    }
                @endphp
                <div style="background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.25); padding: 14px 10px; border-radius: 12px; text-align: center;">
                    <div id="contador-verificados" style="font-size: 22px; font-weight: 800; color: #86efac; transition: transform 0.3s;">{{ $verificadosCount }}</div>
                    <div style="font-size: 10px; font-weight: 700; opacity: 0.9; text-transform: uppercase; margin-top: 2px;">Correctos</div>
                </div>
                <div style="background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.25); padding: 14px 10px; border-radius: 12px; text-align: center;">
                    <div id="contador-observados" style="font-size: 22px; font-weight: 800; color: #fde047; transition: transform 0.3s;">{{ $observadosCount }}</div>
                    <div style="font-size: 10px; font-weight: 700; opacity: 0.9; text-transform: uppercase; margin-top: 2px;">Observados</div>
                </div>
            </div>
        @endif
    </div>

    {{-- ============================================================ --}}
    {{-- CRONÓMETRO PERSISTENTE --}}
    {{-- ============================================================ --}}
    @if(isset($ambienteSeleccionado) && $ambienteSeleccionado && $puedeVerificar)
        <div style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; padding: 18px 24px; margin-bottom: 25px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
            <div>
                <h4 style="margin: 0; font-size: 14px; font-weight: 800; color: #0f172a;">⏱️ Control de Tiempo de Auditoría</h4>
                <p style="margin: 2px 0 0 0; font-size: 12px; color: #64748b;">
                    El cronómetro se guarda automáticamente. No se pierde al salir ni recargar.
                </p>
            </div>
            <div style="display: flex; align-items: center; gap: 15px;">
                <div id="cronometro-display" style="font-family: 'Courier New', monospace; font-size: 22px; font-weight: 800; background: #0f172a; color: #38bdf8; padding: 8px 16px; border-radius: 10px; letter-spacing: 2px; border: 1px solid #1e40af;">
                    00:00:00
                </div>
                <button type="button" id="btn-toggle-timer" onclick="toggleVerificacionTimer()" style="background: #2563eb; color: white; border: none; padding: 10px 20px; border-radius: 10px; font-weight: 700; cursor: pointer; font-size: 13px; box-shadow: 0 2px 4px rgba(37,99,235,0.2); transition: all 0.2s;">
                    ▶ Iniciar Verificación
                </button>
            </div>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- FILTROS --}}
    {{-- ============================================================ --}}
    <div style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; padding: 24px; margin-bottom: 25px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);">
        <form method="GET" action="{{ route('verificaciones.index') }}" id="form-filtros" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; align-items: flex-end;" onsubmit="return validarFormulario()">

            <div>
                <label style="font-size: 11px; font-weight: 800; color: #64748b; display: block; margin-bottom: 6px; text-transform: uppercase;">1. Carrera</label>
                <select name="carrera_id" id="select-carrera" onchange="document.getElementById('form-filtros').submit()" style="width: 100%; padding: 11px 14px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; color: #1e293b; cursor: pointer; outline: none;">
                    <option value="">— Seleccionar carrera —</option>
                    @foreach($carreras as $c)
                        <option value="{{ $c->id_carrera }}" {{ request('carrera_id') == $c->id_carrera ? 'selected' : '' }}>
                            {{ $c->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="font-size: 11px; font-weight: 800; color: #64748b; display: block; margin-bottom: 6px; text-transform: uppercase;">2. Ambiente</label>
                <select name="ambiente_id" id="select-ambiente" onchange="cargarCategoriasYEnviar()" style="width: 100%; padding: 11px 14px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; color: #1e293b; cursor: pointer; outline: none;" {{ $ambientes->isEmpty() ? 'disabled' : '' }}>
                    <option value="">— Seleccionar ambiente —</option>
                    @foreach($ambientes as $a)
                        <option value="{{ $a->id_ambiente }}" {{ request('ambiente_id') == $a->id_ambiente ? 'selected' : '' }}>
                            {{ $a->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="font-size: 11px; font-weight: 800; color: #64748b; display: block; margin-bottom: 6px; text-transform: uppercase;">3. Categoría <span style="font-weight: 400; color: #94a3b8; text-transform: none;">(opcional)</span></label>
                <select name="categoria_id" id="select-categoria" onchange="document.getElementById('form-filtros').submit()" style="width: 100%; padding: 11px 14px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; color: #1e293b; cursor: pointer; outline: none;" {{ $categorias->isEmpty() ? 'disabled' : '' }}>
                    <option value="">Todas las categorías</option>
                    @foreach($categorias as $cat)
                        <option value="{{ $cat->id_categoria }}" {{ request('categoria_id') == $cat->id_categoria ? 'selected' : '' }}>
                            {{ $cat->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; gap: 8px;">
                @if(request('carrera_id') || request('ambiente_id') || request('categoria_id'))
                    <a href="{{ route('verificaciones.index') }}" style="flex: 1; padding: 11px 14px; background: #f1f5f9; color: #334155; border-radius: 10px; font-size: 13px; font-weight: 700; text-align: center; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: 6px;">
                        Limpiar
                    </a>
                @endif

                @if(isset($ambienteSeleccionado) && $ambienteSeleccionado && $puedeVerificar)
                    <button type="button" onclick="openScannerModal()" style="flex: 1; padding: 11px 16px; background: #0d9488; color: white; border: none; border-radius: 10px; font-size: 13px; font-weight: 700; cursor: pointer; box-shadow: 0 2px 4px rgba(13, 148, 136, 0.2); display: inline-flex; align-items: center; justify-content: center; gap: 6px;">
                        📷 Escanear QR
                    </button>
                @endif
            </div>
        </form>

        @if(isset($ambienteSeleccionado) && $ambienteSeleccionado)
            <div style="margin-top: 18px; padding-top: 14px; border-top: 1px solid #f1f5f9; display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                <span style="font-size: 12px; color: #64748b; font-weight: 700;">Filtros activos:</span>
                <span style="background: #eff6ff; color: #1d4ed8; padding: 4px 10px; border-radius: 8px; font-size: 12px; font-weight: 700; border: 1px solid #bfdbfe;">
                    🎓 {{ $ambienteSeleccionado->carrera->nombre ?? '—' }}
                </span>
                <span style="background: #e0e7ff; color: #3730a3; padding: 4px 10px; border-radius: 8px; font-size: 12px; font-weight: 700; border: 1px solid #c7d2fe;">
                    🏢 {{ $ambienteSeleccionado->nombre }}
                </span>
                @if(isset($categoriaSeleccionada) && $categoriaSeleccionada)
                    <span style="background: #fdf2f8; color: #be185d; padding: 4px 10px; border-radius: 8px; font-size: 12px; font-weight: 700; border: 1px solid #fbcfe8;">
                        📁 {{ $categoriaSeleccionada->nombre }}
                    </span>
                @endif
                <span style="font-size: 12px; color: #94a3b8; margin-left: auto; font-weight: 600;">
                    {{ $activos->count() }} activo(s) encontrado(s)
                </span>
            </div>
        @endif
    </div>

    {{-- ============================================================ --}}
    {{-- ACCIONES MASIVAS --}}
    {{-- ============================================================ --}}
    @if(isset($ambienteSeleccionado) && $ambienteSeleccionado && $puedeVerificar)
        <div style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; padding: 18px 24px; margin-bottom: 25px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                <div style="font-size: 13px; color: #475569; display: flex; align-items: center; gap: 8px;">
                    <span style="font-size: 16px;">⚡</span>
                    <span><strong style="color: #1e293b;">Acciones de auditoría:</strong> Exporta los resultados o finaliza la verificación.</span>
                </div>
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <button type="button" onclick="generarReporte('pdf')" style="padding: 9px 16px; background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; border-radius: 10px; font-size: 13px; font-weight: 700; cursor: pointer;">
                        📄 Exportar PDF
                    </button>
                    <button type="button" onclick="generarReporte('excel')" style="padding: 9px 16px; background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; border-radius: 10px; font-size: 13px; font-weight: 700; cursor: pointer;">
                        📊 Exportar Excel
                    </button>
                    <button type="button" onclick="finalizarVerificacion()" id="btn-finalizar-verificacion" style="padding: 9px 16px; background: #7c3aed; color: white; border: none; border-radius: 10px; font-size: 13px; font-weight: 700; cursor: pointer; box-shadow: 0 2px 4px rgba(124, 58, 237, 0.2);">
                        ✅ Finalizar Verificación
                    </button>

                    <button type="button" onclick="descargarCatalogoOffline()" id="btn-catalogo-offline"
                            style="padding: 9px 16px; background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; font-weight: 700; cursor: pointer;"
                            title="Guarda los activos de este ambiente en el equipo para escanear sin señal">
                        📥 Guardar para sin señal
                    </button>

                    <button type="button" onclick="window.InventarioOffline && window.InventarioOffline.sincronizar().then(r => { mostrarNotificacion(r.sincronizado ? 'Se enviaron ' + r.sincronizado + ' verificación(es).' : 'No hay nada pendiente de enviar.', r.sincronizado ? 'success' : 'warning'); window.location.reload(); })"
                            id="btn-sincronizar"
                            data-reload="1"
                            style="padding: 9px 16px; background: #f59e0b; color: white; border: none; border-radius: 10px; font-size: 13px; font-weight: 700; cursor: pointer; display: none;"
                            title="Envía al sistema lo que registraste sin señal">
                        🔄 Sincronizar ahora
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- TABLA DE ACTIVOS --}}
    {{-- ============================================================ --}}
    @if(isset($ambienteSeleccionado) && $ambienteSeleccionado)
        <div style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);">
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
                    <thead style="background: #f8fafc; color: #475569; text-transform: uppercase; font-size: 10px; letter-spacing: 0.5px; border-bottom: 1px solid #e2e8f0;">
                        <tr>
                            <th style="padding: 14px 12px; text-align: center; width: 60px;">QR</th>
                            <th style="padding: 14px 12px; text-align: center; width: 60px;">Foto</th>
                            <th style="padding: 14px 12px;">Código / Serie</th>
                            <th style="padding: 14px 12px;">Descripción</th>
                            <th style="padding: 14px 12px;">Categoría</th>
                            <th style="padding: 14px 12px;">Custodio</th>
                            <th style="padding: 14px 12px; text-align: center; width: 80px;">Estado</th>
                            <th style="padding: 14px 12px; text-align: center; width: 120px;">Verificación</th>
                            <th style="padding: 14px 12px; text-align: center; width: 220px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activos as $act)
                            @php
                                $rowId = 'fila-' . preg_replace('/[^A-Za-z0-9\-_]/', '_', $act->codigo_activo);
                                $obsGuardada = '';
                                $rowClass = 'fila-activo';

                                $ultimaVerifTemp = $act->verificaciones->first();
                                if ($ultimaVerifTemp) {
                                    if ($ultimaVerifTemp->observaciones && trim($ultimaVerifTemp->observaciones) !== '') {
                                        $obsGuardada = $ultimaVerifTemp->observaciones;
                                        $rowClass .= ' fila-observada';
                                    } elseif ($ultimaVerifTemp->es_correspondencia_correcta) {
                                        $rowClass .= ' fila-verificada';
                                    }
                                }
                            @endphp
                            <tr id="{{ $rowId }}"
                                data-id="{{ $act->id_activo }}"
                                data-codigo="{{ $act->codigo_activo }}"
                                data-obs="{{ $obsGuardada }}"
                                class="{{ $rowClass }}"
                                style="border-bottom: 1px solid #f1f5f9; transition: background-color 0.2s;">

                                <td style="padding: 12px; text-align: center;">
                                    @if($act->ruta_qr)
                                        <img src="{{ asset('storage/' . $act->ruta_qr) }}" alt="QR" style="width: 38px; height: 38px; margin: 0 auto; object-fit: contain;">
                                    @else
                                        <span style="color: #94a3b8; font-size: 10px;">Sin QR</span>
                                    @endif
                                </td>

                                <td style="padding: 12px; text-align: center;">
                                    @if($act->imagen)
                                        <img src="{{ asset('storage/' . $act->imagen) }}" alt="Foto" style="width: 38px; height: 38px; margin: 0 auto; object-fit: cover; border-radius: 6px; border: 1px solid #cbd5e1;">
                                    @else
                                        <span style="color: #94a3b8; font-size: 10px; font-style: italic;">Sin foto</span>
                                    @endif
                                </td>

                                <td style="padding: 12px;">
                                    <div style="font-weight: 800; color: #1e293b; font-size: 12px;">{{ $act->codigo_activo }}</div>
                                    <div style="color: #64748b; font-size: 10px; margin-top: 2px;">{{ $act->numero_serie ?? 'S/N' }}</div>
                                </td>

                                <td style="padding: 12px;">
                                    <div style="font-weight: 700; color: #0f172a;">{{ $act->nombre }}</div>
                                    <div style="color: #64748b; font-size: 10px; margin-top: 2px;">{{ $act->marca }} {{ $act->modelo }}</div>
                                </td>

                                <td style="padding: 12px;">
                                    <span style="background: #f1f5f9; color: #334155; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; display: inline-block;">
                                        {{ $act->categoria->nombre ?? '—' }}
                                    </span>
                                </td>

                                <td style="padding: 12px; color: #475569; font-size: 12px;">
                                    {{ $act->custodio?->nombre_completo ?? 'Sin asignar' }}
                                </td>

                                <td style="padding: 12px; text-align: center;">
                                    @php
                                        $estadoColors = [
                                            'B'  => 'background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0;',
                                            'R'  => 'background: #fef9c3; color: #854d0e; border: 1px solid #fde047;',
                                            'M'  => 'background: #ffedd5; color: #9a3412; border: 1px solid #fed7aa;',
                                            'FF' => 'background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5;',
                                        ];
                                    @endphp
                                    <span style="display: inline-block; padding: 3px 8px; border-radius: 6px; font-weight: 800; font-size: 10px; {{ $estadoColors[$act->estado_fisico] ?? 'background: #f1f5f9; color: #334155;' }}">
                                        {{ $act->estado_fisico }}
                                    </span>
                                </td>

                                <td style="padding: 12px; text-align: center;">
                                    @php
                                        $ultimaVerif = $act->verificaciones->first();
                                        $badgeStyle = 'background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;';
                                        $badgeText = 'Pendiente';
                                        $obsText = '';
                                        if ($ultimaVerif) {
                                            if ($ultimaVerif->observaciones && trim($ultimaVerif->observaciones) !== '') {
                                                $badgeStyle = 'background: #fef9c3; color: #854d0e; border: 1px solid #fde047;';
                                                $badgeText = 'Con Observación';
                                                $obsText = '⚠️ ' . $ultimaVerif->observaciones;
                                            } elseif ($ultimaVerif->es_correspondencia_correcta) {
                                                $badgeStyle = 'background: #16a34a; color: white;';
                                                $badgeText = 'Verificado';
                                            }
                                        }
                                    @endphp
                                    <span id="badge-{{ $rowId }}" style="display: inline-block; padding: 4px 10px; border-radius: 6px; font-weight: 800; font-size: 10px; {{ $badgeStyle }}">
                                        {{ $badgeText }}
                                    </span>
                                    <div id="obs-texto-{{ $rowId }}" style="color: #854d0e; margin-top: 4px; font-size: 10px; max-width: 150px; margin-left: auto; margin-right: auto; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; {{ $obsText ? '' : 'display: none;' }}" title="{{ $obsText }}">
                                        {{ $obsText }}
                                    </div>
                                </td>

                                <td style="padding: 12px; text-align: center;">
                                    @if($puedeVerificar)
                                        <div style="display: flex; gap: 6px; justify-content: center;">
                                            <button type="button" onclick="marcarCorrecto('{{ $rowId }}', {{ $act->id_activo }})" title="Marcar verificado" style="padding: 8px 14px; background: #16a34a; color: white; border: none; border-radius: 8px; font-weight: 700; font-size: 12px; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; box-shadow: 0 2px 4px rgba(22,163,74,0.2);">
                                                ✓ Verificado
                                            </button>
                                            <button type="button" onclick="abrirModalObservacion('{{ $rowId }}', '{{ $act->codigo_activo }}', {{ $act->id_activo }}, '{{ $act->estado_fisico }}')" title="Observar" style="padding: 8px 14px; background: #ca8a04; color: white; border: none; border-radius: 8px; font-weight: 700; font-size: 12px; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; box-shadow: 0 2px 4px rgba(202,138,4,0.2);">
                                                ⚠ Observar
                                            </button>
                                        </div>
                                    @else
                                        <span style="color: #94a3b8; font-size: 11px; font-style: italic; font-weight: 600;">Solo lectura</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" style="text-align: center; padding: 50px 20px;">
                                    <div style="font-size: 40px; margin-bottom: 10px;">📦</div>
                                    <p style="color: #64748b; font-weight: 600; font-size: 14px;">No hay activos registrados en este ambiente o categoría seleccionada.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <form id="form-exportar-verificados" method="POST" target="_blank" style="display:none;">
            @csrf
            <input type="hidden" name="ambiente_id" value="{{ $ambienteSeleccionado->id_ambiente }}">
            <input type="hidden" name="verificados" id="input-verificados-json">
        </form>
    @else
        <div style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; padding: 60px 20px; text-align: center; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);">
            <div style="font-size: 50px; margin-bottom: 12px;">👉</div>
            <h3 style="color: #334155; font-weight: 800; font-size: 18px; margin: 0 0 6px 0;">Selecciona una carrera y un ambiente</h3>
            <p style="color: #64748b; font-size: 13px; margin: 0;">Usa los filtros superiores para desplegar el inventario institucional y realizar la auditoría.</p>
        </div>
    @endif

</div>

{{-- ============================================================ --}}
{{-- MODALES --}}
{{-- ============================================================ --}}
<div id="scanner-modal" style="display: none; position: fixed; inset: 0; background: rgba(0, 0, 0, 0.6); z-index: 9999; justify-content: center; align-items: center; padding: 20px;">
    <div style="background: white; border-radius: 16px; padding: 24px; width: 100%; max-width: 480px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); animation: modalIn 0.3s ease-out;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3 style="font-size: 16px; font-weight: 800; color: #1e293b; margin: 0;">📷 Escanear QR de Activo</h3>
            <button type="button" onclick="closeScannerModal()" style="background: #f1f5f9; border: none; border-radius: 50%; width: 32px; height: 32px; cursor: pointer; font-weight: bold; color: #475569;">×</button>
        </div>
        <div id="qr-reader" style="width: 100%; border-radius: 10px; overflow: hidden;"></div>
        <p id="scanner-status" style="text-align: center; color: #64748b; font-size: 13px; margin-top: 12px;">Iniciando cámara...</p>

        <div id="scanner-manual" style="display: none; margin-top: 14px;">
            <label style="display: block; font-size: 11px; font-weight: 800; color: #334155; margin-bottom: 6px; text-transform: uppercase;">Código del activo</label>
            <div style="display: flex; gap: 8px;">
                <input type="text" id="scanner-input" placeholder="Ej: TPM-2026-0001"
                       style="flex: 1; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 14px; font-family: monospace; outline: none;">
                <button type="button" onclick="procesarEscaneo(document.getElementById('scanner-input').value)"
                        style="padding: 10px 18px; background: #2563eb; color: white; border: none; border-radius: 10px; font-weight: 800; cursor: pointer;">
                    Buscar
                </button>
            </div>
            <p style="margin: 8px 0 0 0; font-size: 11px; color: #64748b;">El código está impreso en la etiqueta del activo, debajo del QR.</p>
        </div>
        <div style="margin-top: 16px; text-align: center;">
            <button type="button" onclick="closeScannerModal()" style="padding: 8px 18px; background: #e2e8f0; color: #334155; border: none; border-radius: 8px; font-weight: 700; cursor: pointer;">Cerrar</button>
        </div>
    </div>
</div>

<div id="obs-modal" style="display: none; position: fixed; inset: 0; background: rgba(0, 0, 0, 0.6); z-index: 9999; justify-content: center; align-items: center; padding: 20px;">
    <div style="background: white; border-radius: 16px; padding: 24px; width: 100%; max-width: 420px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); animation: modalIn 0.3s ease-out;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3 style="font-size: 16px; font-weight: 800; color: #1e293b; margin: 0;">⚠️ Registrar Observación</h3>
            <button type="button" onclick="closeObsModal()" style="background: #f1f5f9; border: none; border-radius: 50%; width: 32px; height: 32px; cursor: pointer; font-weight: bold; color: #475569;">×</button>
        </div>
        <p id="obs-modal-sub" style="font-size: 12px; color: #475569; margin-bottom: 12px; background: #f8fafc; padding: 8px 12px; border-radius: 8px; font-weight: 700;"></p>
        <input type="hidden" id="obs-row-id">
        <input type="hidden" id="obs-activo-id">

        <label style="display: block; font-size: 11px; font-weight: 800; color: #334155; margin-bottom: 6px; text-transform: uppercase;">Motivo / Detalle *</label>
        <textarea id="obs-detalle" rows="3" placeholder="Ej: Pantalla rayada, cable faltante..." style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; margin-bottom: 12px; resize: none; outline: none;"></textarea>

        <label style="display: block; font-size: 11px; font-weight: 800; color: #334155; margin-bottom: 6px; text-transform: uppercase;">Cambiar estado físico (opcional)</label>
        <select id="obs-estado-fisico" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; margin-bottom: 12px; background: white; outline: none;">
            <option value="">Mantener estado actual</option>
            <option value="B">Bueno</option>
            <option value="R">Regular</option>
            <option value="M">Malo</option>
            <option value="FF">Fuera de funcionamiento</option>
        </select>

        <div style="background: #fef2f2; border: 1px solid #fecaca; padding: 10px 12px; border-radius: 8px; margin-bottom: 16px;">
            <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #991b1b; font-weight: 700; cursor: pointer;">
                <input type="checkbox" id="obs-solicitar-baja" style="width: 16px; height: 16px; accent-color: #dc2626;"> Solicitar baja del activo
            </label>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 8px;">
            <button type="button" onclick="closeObsModal()" style="padding: 8px 16px; background: #e2e8f0; color: #334155; border: none; border-radius: 8px; font-weight: 700; cursor: pointer;">Cancelar</button>
            <button type="button" onclick="guardarObservacion()" style="padding: 8px 16px; background: #ca8a04; color: white; border: none; border-radius: 8px; font-weight: 700; cursor: pointer;">Guardar</button>
        </div>
    </div>
</div>

<style>
    @keyframes modalIn {
        from { opacity: 0; transform: scale(0.9) translateY(-10px); }
        to   { opacity: 1; transform: scale(1) translateY(0); }
    }

    tr.fila-verificada { background-color: #f0fdf4 !important; }
    tr.fila-observada { background-color: #fefce8 !important; }
</style>

@endsection

@section('scripts')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    let enviandoFormulario = false;
    function validarFormulario() {
        if (enviandoFormulario) return false;
        enviandoFormulario = true;
        setTimeout(() => enviandoFormulario = false, 1000);
        return true;
    }

    function toggleModal(id, show) {
        const el = document.getElementById(id);
        if (!el) return;
        el.style.display = show ? 'flex' : 'none';
    }

    let html5QrCode = null;
    let isProcessingScan = false;
    const procesandoActivos = new Set();

    const AMBIENTE_ID = "{{ $ambienteSeleccionado->id_ambiente ?? '' }}";
    const GUARDAR_ITEM_URL = "{{ route('verificaciones.guardarItem') }}";
    const FINALIZAR_URL = "{{ route('verificaciones.finalizar') }}";
    const CATEGORIAS_URL = "{{ route('verificaciones.categoriasPorAmbiente') }}";
    const CSRF_TOKEN = "{{ csrf_token() }}";
    const PUEDE_VERIFICAR = {{ $puedeVerificar ? 'true' : 'false' }};

    const ambienteActualId = "{{ $ambienteSeleccionado->id_ambiente ?? 'general' }}";
    const storageKeyEstado = 'verificacion_estado_' + ambienteActualId;
    const storageKeySegundos = 'verificacion_segundos_' + ambienteActualId;
    const storageKeyInicioReal = 'verificacion_inicio_' + ambienteActualId;
    const storageKeyProgreso = 'verificacion_progreso_' + ambienteActualId;

    let timerInterval = null;
    let segundosTranscurridos = parseInt(localStorage.getItem(storageKeySegundos) || '0');
    let estaCorriendo = localStorage.getItem(storageKeyEstado) === 'activo';

    function actualizarDisplayCronometro() {
        const display = document.getElementById('cronometro-display');
        if (!display) return;

        const horas = Math.floor(segundosTranscurridos / 3600);
        const minutos = Math.floor((segundosTranscurridos % 3600) / 60);
        const segs = segundosTranscurridos % 60;

        display.textContent =
            (horas < 10 ? '0' : '') + horas + ':' +
            (minutos < 10 ? '0' : '') + minutos + ':' +
            (segs < 10 ? '0' : '') + segs;
    }

    function formatearTiempo(segs) {
        const h = Math.floor(segs / 3600);
        const m = Math.floor((segs % 3600) / 60);
        const s = segs % 60;
        return (h < 10 ? '0' : '') + h + ':' + (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
    }

    function iniciarTimer() {
        if (timerInterval) clearInterval(timerInterval);
        timerInterval = setInterval(() => {
            segundosTranscurridos++;
            localStorage.setItem(storageKeySegundos, segundosTranscurridos);
            actualizarDisplayCronometro();
        }, 1000);
    }

    function toggleVerificacionTimer() {
        const btn = document.getElementById('btn-toggle-timer');
        if (!btn) return;

        if (!estaCorriendo) {
            estaCorriendo = true;
            localStorage.setItem(storageKeyEstado, 'activo');
            if (!localStorage.getItem(storageKeyInicioReal)) {
                localStorage.setItem(storageKeyInicioReal, new Date().toISOString());
            }
            btn.innerHTML = '⏸ Pausar Cronómetro';
            btn.style.background = '#dc2626';
            btn.style.boxShadow = '0 2px 4px rgba(220, 38, 38, 0.3)';
            iniciarTimer();
            guardarEstadoLocal();
            mostrarNotificacion('⏱️ Verificación iniciada. El tiempo se guarda automáticamente.', 'success');
        } else {
            estaCorriendo = false;
            localStorage.setItem(storageKeyEstado, 'pausado');
            if (timerInterval) clearInterval(timerInterval);
            btn.innerHTML = '▶ Continuar Verificación';
            btn.style.background = '#2563eb';
            btn.style.boxShadow = '0 2px 4px rgba(37, 99, 235, 0.2)';
            guardarEstadoLocal();
            mostrarNotificacion('⏸ Cronómetro pausado.', 'warning');
        }
    }

    // ⭐ GUARDAR ESTADO DE LA VERIFICACIÓN LOCALMENTE (PERSISTENCIA)
    function guardarEstadoLocal() {
        const estadosActivos = {};
        document.querySelectorAll('tr[data-id]').forEach(fila => {
            const id = fila.getAttribute('data-id');
            const rowId = fila.id;
            const badge = document.getElementById(`badge-${rowId}`);
            const obsTexto = document.getElementById(`obs-texto-${rowId}`);

            if (fila.classList.contains('fila-verificada')) {
                estadosActivos[id] = { tipo: 'verificado', obs: '' };
            } else if (fila.classList.contains('fila-observada')) {
                estadosActivos[id] = { tipo: 'observado', obs: fila.getAttribute('data-obs') || '' };
            }
        });

        localStorage.setItem(storageKeyProgreso, JSON.stringify(estadosActivos));
    }

    // ⭐ RESTAURAR ESTADO DE LA VERIFICACIÓN AL CARGAR LA VISTA
    function restaurarEstadoLocal() {
        const guardado = localStorage.getItem(storageKeyProgreso);
        if (!guardado) return;

        try {
            const estadosActivos = JSON.parse(guardado);
            for (const [activoId, data] of Object.entries(estadosActivos)) {
                const fila = document.querySelector(`tr[data-id="${activoId}"]`);
                if (!fila) continue;

                const rowId = fila.id;
                const badge = document.getElementById(`badge-${rowId}`);
                const obsTexto = document.getElementById(`obs-texto-${rowId}`);

                if (data.tipo === 'verificado') {
                    fila.classList.remove('fila-observada');
                    fila.classList.add('fila-verificada');
                    fila.setAttribute('data-obs', '');
                    if (badge) {
                        badge.style.cssText = 'display: inline-block; padding: 4px 10px; border-radius: 6px; font-weight: 800; font-size: 10px; background: #16a34a; color: white;';
                        badge.textContent = 'Verificado';
                    }
                    if (obsTexto) {
                        obsTexto.style.display = 'none';
                        obsTexto.textContent = '';
                    }
                } else if (data.tipo === 'observado') {
                    fila.classList.remove('fila-verificada');
                    fila.classList.add('fila-observada');
                    fila.setAttribute('data-obs', data.obs);
                    if (badge) {
                        badge.style.cssText = 'display: inline-block; padding: 4px 10px; border-radius: 6px; font-weight: 800; font-size: 10px; background: #fef9c3; color: #854d0e; border: 1px solid #fde047;';
                        badge.textContent = 'Con Observación';
                    }
                    if (obsTexto) {
                        obsTexto.textContent = '⚠ ' + data.obs;
                        obsTexto.style.display = 'block';
                        obsTexto.title = data.obs;
                    }
                }
            }
            actualizarContadores();
        } catch (e) {
            console.error('Error restaurando estado local:', e);
        }
    }

    async function cargarCategoriasYEnviar() {
        const ambienteId = document.getElementById('select-ambiente').value;
        const selectCategoria = document.getElementById('select-categoria');
        selectCategoria.innerHTML = '<option value="">Todas las categorías</option>';

        if (ambienteId) {
            try {
                const res = await fetch(`${CATEGORIAS_URL}?ambiente_id=${ambienteId}`, {
                    headers: { 'Accept': 'application/json' }
                });
                const json = await res.json();
                if (json.success && json.categorias.length > 0) {
                    selectCategoria.disabled = false;
                    json.categorias.forEach(cat => {
                        const opt = document.createElement('option');
                        opt.value = cat.id_categoria;
                        opt.textContent = cat.nombre;
                        selectCategoria.appendChild(opt);
                    });
                } else {
                    selectCategoria.disabled = true;
                }
            } catch (err) {
                selectCategoria.disabled = true;
            }
        } else {
            selectCategoria.disabled = true;
        }
        document.getElementById('form-filtros').submit();
    }

    function actualizarContadores() {
        const verificados = document.querySelectorAll('tr.fila-verificada').length;
        const observados  = document.querySelectorAll('tr.fila-observada').length;

        const elVerif = document.getElementById('contador-verificados');
        const elObs   = document.getElementById('contador-observados');

        if (elVerif) {
            elVerif.textContent = verificados;
            elVerif.style.transform = 'scale(1.15)';
            setTimeout(() => elVerif.style.transform = 'scale(1)', 200);
        }
        if (elObs) {
            elObs.textContent = observados;
            elObs.style.transform = 'scale(1.15)';
            setTimeout(() => elObs.style.transform = 'scale(1)', 200);
        }
    }

    function mostrarNotificacion(mensaje, tipo = 'success') {
        let contenedor = document.getElementById('notificaciones-container');
        if (!contenedor) {
            contenedor = document.createElement('div');
            contenedor.id = 'notificaciones-container';
            contenedor.style.cssText = 'position:fixed;top:20px;right:20px;z-index:99999;display:flex;flex-direction:column;gap:8px;pointer-events:none;';
            document.body.appendChild(contenedor);
        }

        const notif = document.createElement('div');
        const bg = tipo === 'success' ? '#dcfce7' : (tipo === 'warning' ? '#fef3c7' : '#fee2e2');
        const border = tipo === 'success' ? '#86efac' : (tipo === 'warning' ? '#fcd34d' : '#fca5a5');
        const color = tipo === 'success' ? '#166534' : (tipo === 'warning' ? '#854d0e' : '#991b1b');
        const icon = tipo === 'success' ? '✓' : (tipo === 'warning' ? '⚠' : '✕');

        notif.style.cssText = `background:${bg};border:1px solid ${border};color:${color};padding:12px 16px;border-radius:10px;font-size:13px;font-weight:600;box-shadow:0 4px 12px rgba(0,0,0,.15);pointer-events:auto;display:flex;align-items:center;gap:8px;`;
        notif.innerHTML = `<span style="font-size:16px;">${icon}</span><span>${mensaje}</span>`;
        contenedor.appendChild(notif);

        setTimeout(() => {
            notif.style.opacity = '0';
            setTimeout(() => notif.remove(), 300);
        }, 3000);
    }

    async function guardarVerificacion(activoId, ambienteId, esCorrespondencia, observaciones, estadoFisico = null, solicitarBaja = false) {
        if (!activoId || !ambienteId || !PUEDE_VERIFICAR) return false;
        const cuerpo = {
            activo_id: activoId,
            ambiente_escaneo_id: ambienteId,
            es_correspondencia: esCorrespondencia ? 1 : 0,
            observaciones: observaciones || '',
            estado_fisico: estadoFisico || null,
            solicitar_baja: solicitarBaja ? 1 : 0,
        };
        try {
            const res = await fetch(GUARDAR_ITEM_URL, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                },
                body: JSON.stringify(cuerpo),
            });
            const json = await res.json();
            if (json.success) mostrarNotificacion('Verificación guardada correctamente.', 'success');
            else mostrarNotificacion('Error: ' + (json.error || json.message), 'error');
            return json.success === true;
        } catch (err) {
            if (window.InventarioOffline) {
                await window.InventarioOffline.registrar({
                    codigo_activo: window.__codigoActual || '',
                    ambiente_escaneo_id: ambienteId,
                    estado_fisico: estadoFisico,
                    observaciones: observaciones || '',
                    solicitar_baja: solicitarBaja ? 1 : 0,
                });
                mostrarNotificacion('📡 Sin conexión: se guardó en este equipo y se enviará al recuperar la señal.', 'warning');
                return true;
            }
            mostrarNotificacion('Error de conexión.', 'error');
            return false;
        }
    }

    async function finalizarVerificacion() {
        if (!AMBIENTE_ID) return;

        const tiempoFormateado = formatearTiempo(segundosTranscurridos);

        Swal.fire({
            title: '¿Finalizar verificación?',
            html: `
                <div style="text-align:center;">
                    <div style="font-size:56px;margin-bottom:12px;">✅</div>
                    <p style="font-size:15px;color:#475569;margin:0 0 8px 0;">
                        Se enviará a aprobación y se <strong>notificará al administrador</strong>.
                    </p>
                    <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:10px 14px;font-size:13px;color:#1d4ed8;font-weight:600;">
                        ⏱️ Tiempo: ${tiempoFormateado}
                    </div>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#7c3aed',
            cancelButtonColor: '#64748b',
            confirmButtonText: '✅ Finalizar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true,
        }).then(async (result) => {
            if (!result.isConfirmed) return;

            try {
                const res = await fetch(FINALIZAR_URL, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                    },
                    body: JSON.stringify({
                        ambiente_id: AMBIENTE_ID,
                        tiempo_transcurrido: tiempoFormateado,
                    }),
                });
                const json = await res.json();

                if (json.success) {
                    mostrarNotificacion('✅ Verificación finalizada. Se notificó al administrador.', 'success');

                    const btn = document.getElementById('btn-finalizar-verificacion');
                    if (btn) {
                        btn.disabled = true;
                        btn.style.opacity = '0.5';
                        btn.innerHTML = '✅ Finalizada';
                    }

                    estaCorriendo = false;
                    localStorage.setItem(storageKeyEstado, 'finalizado');
                    localStorage.removeItem(storageKeyProgreso);
                    if (timerInterval) clearInterval(timerInterval);

                    const btnTimer = document.getElementById('btn-toggle-timer');
                    if (btnTimer) {
                        btnTimer.disabled = true;
                        btnTimer.style.opacity = '0.5';
                        btnTimer.innerHTML = '✓ Completado';
                    }
                } else {
                    mostrarNotificacion('Error: ' + (json.error || json.message), 'error');
                }
            } catch (err) {
                mostrarNotificacion('Error de conexión.', 'error');
            }
        });
    }

    async function marcarCorrecto(rowId, activoId = null) {
        if (procesandoActivos.has(rowId)) return;
        procesandoActivos.add(rowId);

        const fila = document.getElementById(rowId);
        const badge = document.getElementById(`badge-${rowId}`);
        const obsTexto = document.getElementById(`obs-texto-${rowId}`);

        if (fila && badge) {
            fila.classList.remove('fila-observada');
            fila.classList.add('fila-verificada');
            fila.setAttribute('data-obs', '');

            badge.style.cssText = 'display: inline-block; padding: 4px 10px; border-radius: 6px; font-weight: 800; font-size: 10px; background: #16a34a; color: white;';
            badge.textContent = 'Verificado';

            if (obsTexto) {
                obsTexto.style.display = 'none';
                obsTexto.textContent = '';
            }
            actualizarContadores();
            guardarEstadoLocal();
        }

        if (activoId && AMBIENTE_ID) {
            await guardarVerificacion(activoId, AMBIENTE_ID, true, '');
        }
        setTimeout(() => procesandoActivos.delete(rowId), 1000);
    }

    function abrirModalObservacion(rowId, codigo, activoId = null, estadoActual = '') {
        const fila = document.getElementById(rowId);
        document.getElementById('obs-row-id').value = rowId;
        document.getElementById('obs-activo-id').value = activoId || '';
        document.getElementById('obs-modal-sub').textContent = `📦 Activo: ${codigo}`;
        document.getElementById('obs-detalle').value = fila ? (fila.getAttribute('data-obs') || '') : '';
        document.getElementById('obs-estado-fisico').value = estadoActual || '';
        document.getElementById('obs-solicitar-baja').checked = false;

        toggleModal('obs-modal', true);
        setTimeout(() => document.getElementById('obs-detalle').focus(), 100);
    }

    function closeObsModal() {
        toggleModal('obs-modal', false);
    }

    async function guardarObservacion() {
        const rowId = document.getElementById('obs-row-id').value;
        const activoId = document.getElementById('obs-activo-id').value;
        const detalle = document.getElementById('obs-detalle').value.trim();
        const estadoFisico = document.getElementById('obs-estado-fisico').value;
        const solicitarBaja = document.getElementById('obs-solicitar-baja').checked;

        if (!detalle) {
            mostrarNotificacion('Ingresa el motivo de la observación.', 'error');
            document.getElementById('obs-detalle').focus();
            return;
        }

        if (procesandoActivos.has(rowId)) return;
        procesandoActivos.add(rowId);

        const fila = document.getElementById(rowId);
        const badge = document.getElementById(`badge-${rowId}`);
        const obsTexto = document.getElementById(`obs-texto-${rowId}`);

        if (fila && badge) {
            fila.classList.remove('fila-verificada');
            fila.classList.add('fila-observada');

            let obsCompleta = detalle;
            if (solicitarBaja) obsCompleta += ' [Solicitar baja]';
            fila.setAttribute('data-obs', obsCompleta);

            badge.style.cssText = 'display: inline-block; padding: 4px 10px; border-radius: 6px; font-weight: 800; font-size: 10px; background: #fef9c3; color: #854d0e; border: 1px solid #fde047;';
            badge.textContent = 'Con Observación';

            if (obsTexto) {
                obsTexto.textContent = '⚠ ' + obsCompleta;
                obsTexto.style.display = 'block';
                obsTexto.title = obsCompleta;
            }

            closeObsModal();
            actualizarContadores();
            guardarEstadoLocal();
        }

        if (activoId && AMBIENTE_ID) {
            await guardarVerificacion(activoId, AMBIENTE_ID, false, detalle, estadoFisico, solicitarBaja);
        }
        setTimeout(() => procesandoActivos.delete(rowId), 1000);
    }

    function generarReporte(tipo) {
        const filasPintadas = document.querySelectorAll('tr.fila-verificada, tr.fila-observada');
        if (filasPintadas.length === 0) {
            mostrarNotificacion('No has verificado ningún activo aún.', 'error');
            return;
        }

        const listaVerificados = [];
        filasPintadas.forEach(fila => {
            const esObs = fila.classList.contains('fila-observada');
            listaVerificados.push({
                id_activo: fila.getAttribute('data-id'),
                estado: esObs ? 'Con Observación' : 'Verificado',
                observacion: fila.getAttribute('data-obs') || 'Sin observaciones',
            });
        });

        const form = document.getElementById('form-exportar-verificados');
        const inputJson = document.getElementById('input-verificados-json');
        if (form && inputJson) {
            inputJson.value = JSON.stringify(listaVerificados);
            form.action = (tipo === 'pdf') ? "{{ route('verificaciones.pdf') }}" : "{{ route('verificaciones.excel') }}";
            form.submit();
        }
    }

    const audioCorrecto  = new Audio("{{ asset('audios/activo_correcto.mp3') }}");
    const audioNoExiste  = new Audio("{{ asset('audios/activo_no_existente.mp3') }}");
    const audioUbicacion = new Audio("{{ asset('audios/activo_encontrado_otro_ambiente.mp3') }}");

    function precargarAudios() {
        [audioCorrecto, audioNoExiste, audioUbicacion].forEach(audio => {
            audio.play().then(() => { audio.pause(); audio.currentTime = 0; }).catch(() => {});
        });
    }

    function openScannerModal() {
        isProcessingScan = false;
        precargarAudios();
        toggleModal('scanner-modal', true);
        const statusEl = document.getElementById('scanner-status');
        if (statusEl) statusEl.textContent = 'Apunta la cámara al código QR...';

        if (typeof Html5Qrcode === 'undefined') {
            const manual = document.getElementById('scanner-manual');
            if (manual) manual.style.display = 'block';
            if (statusEl) statusEl.textContent = 'Sin señal: escribe el código del activo.';
            return;
        }

        if (!html5QrCode) {
            html5QrCode = new Html5Qrcode('qr-reader');
        }
        html5QrCode.start(
            { facingMode: 'environment' },
            { fps: 10, qrbox: { width: 250, height: 250 } },
            async (decodedText) => {
                if (isProcessingScan) return;
                isProcessingScan = true;
                await closeScannerModal();
                await procesarEscaneo(decodedText);
                setTimeout(() => { isProcessingScan = false; }, 1200);
            },
            () => {}
        ).catch(err => {
            console.error('Error cámara:', err);
            if (statusEl) statusEl.textContent = 'No se pudo acceder a la cámara. Revisa los permisos.';
        });
    }

    async function closeScannerModal() {
        try {
            if (html5QrCode && html5QrCode.isScanning) await html5QrCode.stop();
        } catch (e) {}
        toggleModal('scanner-modal', false);
    }

    async function procesarEscaneo(code) {
        const rawCode = (code || '').trim();
        if (!rawCode) return;

        let codigoBusqueda = rawCode;
        if (rawCode.includes('/')) {
            const partes = rawCode.split('/');
            codigoBusqueda = partes[partes.length - 1];
        }

        window.__codigoActual = codigoBusqueda;

        const url = `{{ url('/activos/codigo') }}/${encodeURIComponent(codigoBusqueda)}?ambiente_id=${AMBIENTE_ID}&codigo_raw=${encodeURIComponent(rawCode)}`;

        try {
            const res = await fetch(url, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const json = await res.json().catch(() => null);

            if (!res.ok || !json || !json.success) {
                audioNoExiste.play().catch(()=>{});
                mostrarNotificacion('❌ El código QR escaneado no existe o no está registrado.', 'error');
                return;
            }

            if (!aplicarActivoEscaneado(json)) return;

            const activo = json.activo;

            const rowId = 'fila-' + activo.codigo_activo.replace(/[^A-Za-z0-9\-_]/g, '_');
            const filaTabla = document.getElementById(rowId);
            if (filaTabla) {
                await marcarCorrecto(rowId, activo.id_activo);
                mostrarNotificacion(`✅ Activo ${activo.codigo_activo} validado correctamente.`, 'success');
            } else {
                mostrarNotificacion(`⚠️ El activo ${activo.codigo_activo} fue reconocido, pero no pertenece a la categoría filtrada actual.`, 'warning');
            }

        } catch (err) {
            console.error(err);

            const local = window.InventarioOffline
                ? await window.InventarioOffline.buscar(codigoBusqueda).catch(() => null)
                : null;

            if (local) {
                const corresponde = aplicarActivoEscaneado({
                    success: true,
                    es_del_ambiente: String(local.id_ambiente) === String(AMBIENTE_ID),
                    activo: local,
                });

                if (!corresponde) return;

                const rowId = 'fila-' + local.codigo_activo.replace(/[^A-Za-z0-9\-_]/g, '_');

                if (document.getElementById(rowId)) {
                    await marcarCorrecto(rowId, local.id_activo);
                    mostrarNotificacion(`📡 Sin conexión: ${local.codigo_activo} se verificó localmente y se enviará luego.`, 'warning');
                } else {
                    mostrarNotificacion(`⚠️ ${local.codigo_activo} fue reconocido, pero no está en la categoría filtrada.`, 'warning');
                }

                return;
            }

            mostrarNotificacion('Sin conexión y el código no está en el catálogo descargado.', 'error');
        }
    }

    function aplicarActivoEscaneado(json) {
        const activo = json.activo;

        if (AMBIENTE_ID && json.es_del_ambiente === false) {
            audioUbicacion.play().catch(() => {});
            mostrarNotificacion('⚠️ El activo existe pero pertenece a otro ambiente.', 'warning');
            return false;
        }

        audioCorrecto.play().catch(() => {});
        return true;
    }

    async function descargarCatalogoOffline() {
        if (!window.InventarioOffline) {
            mostrarNotificacion('El modo sin señal no está disponible en este navegador.', 'error');
            return;
        }

        const btn = document.getElementById('btn-catalogo-offline');
        btn.disabled = true;
        btn.textContent = '⏳ Guardando…';

        try {
            const activos = await window.InventarioOffline.catalogo(AMBIENTE_ID);
            mostrarNotificacion(`📥 ${activos.length} activo(s) guardados en este equipo.`, 'success');
        } catch (err) {
            mostrarNotificacion('No se pudo guardar el catálogo: ' + err.message, 'error');
        } finally {
            btn.disabled = false;
            btn.textContent = '📥 Guardar para sin señal';
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        actualizarDisplayCronometro();
        restaurarEstadoLocal();

        if (estaCorriendo) {
            const btn = document.getElementById('btn-toggle-timer');
            if (btn) {
                btn.innerHTML = '⏸ Pausar Cronómetro';
                btn.style.background = '#dc2626';
                btn.style.boxShadow = '0 2px 4px rgba(220, 38, 38, 0.3)';
            }
            iniciarTimer();
        }

        if (localStorage.getItem(storageKeyEstado) === 'finalizado') {
            const btn = document.getElementById('btn-toggle-timer');
            const btnFin = document.getElementById('btn-finalizar-verificacion');
            if (btn) {
                btn.disabled = true;
                btn.style.opacity = '0.5';
                btn.innerHTML = '✓ Completado';
            }
            if (btnFin) {
                btnFin.disabled = true;
                btnFin.style.opacity = '0.5';
                btnFin.innerHTML = '✅ Finalizada';
            }
        }

        actualizarContadores();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            ['scanner-modal', 'obs-modal'].forEach(id => {
                const modal = document.getElementById(id);
                if (modal && modal.style.display === 'flex') {
                    toggleModal(id, false);
                }
            });
        }
    });
</script>
@endsection
