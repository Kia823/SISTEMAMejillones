@extends('layouts.app')

@section('content')
    <div class="container-fluid px-4 py-4">
        {{-- ⭐ BREADCRUMB --}}
        <div
            style="background:white;padding:12px 20px;border-radius:12px;margin-bottom:20px;display:flex;align-items:center;gap:8px;font-size:13px;box-shadow:0 2px 4px rgba(0,0,0,.04);border:1px solid #e2e8f0;flex-wrap:wrap;">
            <a href="{{ isset($carrera) ? route('carreras.show', $carrera->id_carrera) : '#' }}"
                style="color:#2563eb;text-decoration:none;font-weight:700;">
                🎓 {{ $carrera->nombre ?? 'Carrera' }}
            </a>
            <span style="color:#94a3b8;">/</span>
            <a href="{{ isset($ambiente) ? route('ambientes.show', $ambiente->id_ambiente) : '#' }}"
                style="color:#2563eb;text-decoration:none;font-weight:700;">
                🏢 {{ $ambiente->nombre ?? 'Ambiente' }}
            </a>
            <span style="color:#94a3b8;">/</span>
            <a href="{{ isset($categoria) ? route('categorias.show', $categoria->id_categoria) : '#' }}"
                style="color:#2563eb;text-decoration:none;font-weight:700;">
                📁 {{ $categoria->nombre ?? 'Categoría' }}
            </a>
            <span style="color:#94a3b8;">/</span>
            <span style="color:#0f172a;font-weight:700;">📦 {{ $nombreDecoded ?? 'Grupo' }}</span>
        </div>

        {{-- ============ ENCABEZADO ============ --}}
        <div
            style="background:linear-gradient(135deg,#0f766e 0%,#0d9488 100%);padding:28px;border-radius:16px;color:white;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:20px;margin-bottom:24px;box-shadow:0 10px 15px -3px rgba(15,118,110,.2);">
            <div style="flex:1;min-width:280px;">
                <span
                    style="background:rgba(255,255,255,.2);padding:5px 12px;border-radius:20px;font-size:11px;font-weight:800;text-transform:uppercase;">
                    Activos
                </span>
                <h1 style="font-size:28px;font-weight:800;margin:8px 0 4px 0;">{{ $nombreDecoded ?? 'Activos Fijo' }}</h1>
                <p style="opacity:.9;font-size:14px;margin:0;">
                    Activos individuales de este grupo dentro de <strong>{{ $categoria->nombre ?? '' }}</strong>
                </p>

                <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:16px;">
                    @if (auth()->check() && auth()->user()->esAdmin() && isset($ambiente))
                        <button type="button" onclick="openActivoModal()"
                            style="background:#10b981;color:white;padding:10px 18px;border-radius:10px;font-weight:700;border:none;cursor:pointer;font-size:14px;display:inline-flex;align-items:center;gap:6px;transition:all .2s;"
                            onmouseover="this.style.background='#059669';" onmouseout="this.style.background='#10b981';">
                            ➕ Nuevo Activo
                        </button>
                    @endif
                </div>
            </div>

            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;width:380px;flex-shrink:0;">
                <div style="background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.2);padding:18px 12px;border-radius:12px;text-align:center;">
                    <div style="font-size:24px;font-weight:800;">{{ $totalActivos ?? 0 }}</div>
                    <div style="font-size:11px;font-weight:700;opacity:.9;text-transform:uppercase;margin-top:4px;">Total</div>
                </div>
                <div style="background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.2);padding:18px 12px;border-radius:12px;text-align:center;">
                    <div style="font-size:24px;font-weight:800;color:#86efac;">{{ $totalBuenos ?? 0 }}</div>
                    <div style="font-size:11px;font-weight:700;opacity:.9;text-transform:uppercase;margin-top:4px;">Buenos</div>
                </div>
                <div style="background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.2);padding:18px 12px;border-radius:12px;text-align:center;">
                    <div style="font-size:24px;font-weight:800;color:#fde047;">{{ $totalRegulares ?? 0 }}</div>
                    <div style="font-size:11px;font-weight:700;opacity:.9;text-transform:uppercase;margin-top:4px;">Regulares</div>
                </div>
                <div style="background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.2);padding:18px 12px;border-radius:12px;text-align:center;">
                    <div style="font-size:24px;font-weight:800;color:#fca5a5;">{{ $totalMalos ?? 0 }}</div>
                    <div style="font-size:11px;font-weight:700;opacity:.9;text-transform:uppercase;margin-top:4px;">Malos/F.F.</div>
                </div>
            </div>
        </div>

        {{-- ============ LISTADO DE ACTIVOS ============ --}}
        <div id="activos-container"
            style="background:white;border-radius:16px;border:1px solid #e2e8f0;padding:25px;box-shadow:0 4px 6px -1px rgba(0,0,0,.05);">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
                <h3 style="font-size:18px;font-weight:800;color:#0f172a;margin:0;">
                    📦 Activos individuales (<span id="total-activos">{{ $totalActivos ?? 0 }}</span>)
                </h3>
            </div>

            @if (isset($activos) && $activos->count() > 0)
                <div style="overflow-x:auto;">
                    <table style="width:100%;border-collapse:collapse;font-size:13px;vertical-align:middle;">
                        <thead>
                            <tr style="background:#f1f5f9;color:#475569;text-transform:uppercase;font-size:11px;font-weight:800;">
                                <th style="padding:14px 10px;text-align:center;width:45px;">Nº</th>
                                <th style="padding:14px 10px;text-align:center;width:65px;">QR</th>
                                <th style="padding:14px 10px;text-align:center;width:65px;">IMAGEN</th>
                                <th style="padding:14px 10px;text-align:left;">CÓDIGO</th>
                                <th style="padding:14px 10px;text-align:left;">CARACTERÍSTICAS</th>
                                <th style="padding:14px 10px;text-align:left;">DESCRIPCIÓN</th>
                                <th style="padding:14px 10px;text-align:left;">CUSTODIO</th>
                                <th style="padding:14px 10px;text-align:left;width:110px;">FECHA INGRESO</th>
                                <th style="padding:14px 10px;text-align:center;width:90px;">ESTADO</th>
                                <th style="padding:14px 10px;text-align:center;width:220px;">ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody id="activos-tbody">
                            @foreach ($activos as $i => $a)
                                <tr style="border-bottom:1px solid #e2e8f0;">
                                    <td style="padding:12px 10px;text-align:center;color:#64748b;font-weight:800;">{{ $i + 1 }}</td>
                                    <td style="padding:12px 10px;text-align:center;">
                                        @if ($a->ruta_qr)
                                            <img src="{{ asset('storage/' . $a->ruta_qr) }}" alt="QR"
                                                style="width:42px;height:42px;object-fit:contain;display:block;margin:0 auto;">
                                        @else
                                            <span style="font-size:11px;color:#94a3b8;">Sin QR</span>
                                        @endif
                                    </td>
                                    <td style="padding:12px 10px;text-align:center;">
                                        @if ($a->imagen)
                                            <img src="{{ asset('storage/' . $a->imagen) }}" alt="Foto"
                                                style="width:42px;height:42px;object-fit:cover;border-radius:8px;border:1px solid #e2e8f0;display:block;margin:0 auto;">
                                        @else
                                            <span style="font-size:11px;color:#94a3b8;font-style:italic;">Sin foto</span>
                                        @endif
                                    </td>
                                    <td style="padding:12px 10px;">
                                        <div style="font-weight:800;color:#0f172a;font-size:13px;">{{ $a->codigo_activo }}</div>
                                        <div style="font-size:11px;color:#64748b;margin-top:2px;">Serie: {{ $a->numero_serie ?? 'S/N' }}</div>
                                    </td>
                                    <td style="padding:12px 10px;">
                                        <div style="font-weight:700;color:#1e293b;font-size:13px;">{{ $a->nombre }}</div>
                                        <div style="font-size:11px;color:#64748b;margin-top:2px;">{{ $a->marca }} {{ $a->modelo }}</div>
                                    </td>
                                    <td style="padding:12px 10px;">
                                        @if (!empty($a->descripcion))
                                            <div style="font-size:11px;color:#475569;font-style:italic;background:#f8fafc;padding:6px 10px;border-radius:6px;border:1px solid #e2e8f0;line-height:1.35;max-width:220px;">
                                                {{ $a->descripcion }}
                                            </div>
                                        @else
                                            <span style="font-size:11px;color:#94a3b8;font-style:italic;">Sin descripción</span>
                                        @endif
                                    </td>
                                    <td style="padding:12px 10px;">
                                        <div style="font-size:12px;color:#0f172a;font-weight:700;">{{ $a->custodio->nombre_completo ?? 'Sin asignar' }}</div>
                                        <div style="font-size:11px;color:#64748b;margin-top:2px;">{{ $a->fuente->nombre ?? ($a->fuente->codigo ?? '—') }}</div>
                                    </td>
                                    <td style="padding:12px 10px;font-size:12px;color:#334155;font-weight:600;">
                                        {{ $a->fecha_adquisicion ? \Carbon\Carbon::parse($a->fecha_adquisicion)->format('d/m/Y') : ($a->created_at ? $a->created_at->format('d/m/Y') : '—') }}
                                    </td>
                                    <td style="padding:12px 10px;text-align:center;">
                                        @php
                                            $badge = match ($a->estado_fisico) {
                                                'B' => ['bg' => '#dcfce7', 'color' => '#166534', 'texto' => 'BUENO'],
                                                'R' => ['bg' => '#fef3c7', 'color' => '#92400e', 'texto' => 'REGULAR'],
                                                'M' => ['bg' => '#fee2e2', 'color' => '#991b1b', 'texto' => 'MALO'],
                                                'FF' => ['bg' => '#6b7280', 'color' => 'white', 'texto' => 'F.F.'],
                                                default => ['bg' => '#f1f5f9', 'color' => '#475569', 'texto' => '—'],
                                            };
                                        @endphp
                                        <span style="background:{{ $badge['bg'] }};color:{{ $badge['color'] }};padding:4px 8px;border-radius:6px;font-weight:800;font-size:10px;display:inline-block;">
                                            {{ $badge['texto'] }}
                                        </span>
                                    </td>
                                    <td style="padding:12px 10px;text-align:center;">
                                        <div style="display:flex;gap:4px;justify-content:center;align-items:center;flex-wrap:wrap;">
                                            <a href="{{ route('activos.tarjeta', $a->id_activo) }}" target="_blank" title="Ver tarjeta"
                                                style="width:30px;height:30px;background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;text-decoration:none;font-size:13px;">👁️</a>

                                            @if (auth()->check() && auth()->user()->esAdmin())
                                                <button type="button" onclick="openEditActivoModal({{ $a->id_activo }})" title="Editar"
                                                    style="width:30px;height:30px;background:#fef9c3;color:#ca8a04;border:1px solid #fde047;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;font-size:13px;">
                                                    ✏️
                                                </button>
                                            @endif

                                            <a href="{{ route('activos.descargar_qr', $a->id_activo) }}" title="Descargar QR"
                                                style="width:30px;height:30px;background:#f3e8ff;color:#7c3aed;border:1px solid #ddd6fe;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;text-decoration:none;font-size:13px;">⬇️</a>

                                            <a href="{{ route('activos.etiqueta', $a->id_activo) }}" target="_blank" title="Etiqueta"
                                                style="width:30px;height:30px;background:#fef3c7;color:#f59e0b;border:1px solid #fde68a;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;text-decoration:none;font-size:13px;">🏷️</a>

                                            <a href="{{ route('actas.alta.pdf', $a->id_activo) }}" target="_blank" title="Acta de Alta"
                                                style="width:30px;height:30px;background:#d1fae5;color:#059669;border:1px solid #a7f3d0;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;text-decoration:none;font-size:13px;">📋</a>

                                            @if (auth()->check() && auth()->user()->esAdmin())
                                                <button type="button"
                                                    onclick="confirmarEliminarActivo({{ $a->id_activo }}, '{{ addslashes($a->codigo_activo) }}', '{{ addslashes($a->nombre) }}')"
                                                    title="Eliminar"
                                                    style="width:30px;height:30px;background:#dc2626;color:white;border:1px solid #b91c1c;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;font-size:13px;">
                                                    🗑️
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div style="text-align:center;padding:60px 20px;color:#94a3b8;font-weight:600;">
                    <div style="font-size:48px;margin-bottom:12px;">📦</div>
                    <h3 style="margin:0 0 6px 0;font-size:16px;color:#64748b;">No hay activos en este grupo</h3>
                    <p style="margin:0;font-size:13px;">Registra un activo nuevo para comenzar.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- MODAL: NUEVO ACTIVO --}}
    {{-- ============================================================ --}}
    <div id="activo-modal"
        style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:9999;justify-content:center;align-items:center;padding:20px;">
        <div style="background:white;border-radius:20px;padding:30px;width:100%;max-width:800px;max-height:90vh;overflow-y:auto;box-shadow:0 25px 50px -12px rgba(0,0,0,.25);">

            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;border-bottom:1px solid #e2e8f0;padding-bottom:12px;">
                <h3 style="margin:0;font-size:18px;font-weight:800;color:#0f172a;">📦 Registrar Nuevo Activo</h3>
                <button type="button" onclick="closeActivoModal()"
                    style="background:#f1f5f9;border:none;border-radius:50%;width:32px;height:32px;display:flex;align-items:center;justify-content:center;font-size:18px;cursor:pointer;color:#475569;">×</button>
            </div>

            <form id="activo-modal-form" method="POST" action="{{ route('activos.store') }}"
                enctype="multipart/form-data" novalidate>
                @csrf
                <input type="hidden" name="id_ambiente" value="{{ $ambiente->id_ambiente ?? '' }}">
                <input type="hidden" name="id_categoria_origen" value="{{ $categoria->id_categoria ?? '' }}">

                <style>
                    .af-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
                    .af-grid > div { display:flex; flex-direction:column; }
                    .af-label { font-size:12px;font-weight:700;display:block;margin-bottom:4px;color:#334155; }
                    .af-input {
                        width:100%;padding:10px;border-radius:8px;border:1px solid #cbd5e1;font-size:13px;box-sizing:border-box;background:white;
                    }
                    .af-span-2 { grid-column:span 2; }
                    .af-box { background:#f8fafc; padding: 12px; border-radius: 10px; border: 1px solid #e2e8f0; }
                    .af-error { display:none;color:#dc2626;font-size:11px;font-weight:700;margin-top:4px; }
                    .af-error.visible { display:block; }
                    .af-input-error { border-color:#dc2626 !important; background:#fef2f2 !important; }
                    .af-asterisco { color:#dc2626; margin-left:2px; display:none; }
                    .af-asterisco.visible { display:inline; }
                </style>

                <div class="af-grid">

                    {{-- TIPO --}}
                    <div class="af-span-2 af-box" style="display:flex;align-items:center;justify-content:space-between;">
                        <div>
                            <strong style="font-size: 13px; color: #0f172a; display: block;">Tipo de Clasificación</strong>
                            <span id="ayuda-tipo-bien" style="font-size: 11px; color: #64748b;">Activo Fijo: marca, modelo y serie son obligatorios (3 fotos).</span>
                        </div>
                        <select name="tipo_bien" id="select-tipo-bien" onchange="cambiarTipoBien(this.value)"
                            style="padding: 8px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-weight: 700; font-size: 13px; background: white;">
                            <option value="ACTIVO_FIJO">🔹 Activo Fijo</option>
                            <option value="NO_ACTIVO">🔸 No Activo</option>
                        </select>
                    </div>

                    <div>
                        <label class="af-label">Código *</label>
                        <input type="text" name="codigo_activo" id="crear-codigo-activo" maxlength="50" class="af-input"
                            value="ACT-{{ date('Y') }}-{{ rand(1000, 9999) }}">
                        <div class="af-error" data-for="crear-codigo-activo"></div>
                    </div>

                    <div>
                        <label class="af-label">Categoría (Ambiente) *</label>
                        <select name="id_categoria" id="crear-id-categoria" class="af-input">
                            <option value="">— Seleccionar categoría —</option>
                            @foreach (\App\Models\Categoria::where('id_ambiente', $ambiente->id_ambiente ?? 0)->orderBy('nombre')->get() as $cat)
                                <option value="{{ $cat->id_categoria }}"
                                    {{ (isset($categoria) && $categoria->id_categoria == $cat->id_categoria) ? 'selected' : '' }}>
                                    [{{ $cat->codigo }}] {{ $cat->nombre }}
                                </option>
                            @endforeach
                        </select>
                        <div class="af-error" data-for="crear-id-categoria"></div>
                    </div>

                    <div>
                        <label class="af-label">Cantidad *</label>
                        <input type="number" name="cantidad" id="crear-cantidad" min="1" max="100" value="1" class="af-input">
                        <div class="af-error" data-for="crear-cantidad"></div>
                    </div>
                    <div>
                        <label class="af-label">Fecha Adquisición *</label>
                        <input type="date" name="fecha_adquisicion" id="crear-fecha-adquisicion" value="{{ date('Y-m-d') }}" class="af-input">
                        <div class="af-error" data-for="crear-fecha-adquisicion"></div>
                    </div>

                    <div class="af-span-2">
                        <label class="af-label">Nombre del Activo *</label>
                        <input type="text" name="nombre" id="crear-nombre" maxlength="150" class="af-input">
                        <div class="af-error" data-for="crear-nombre"></div>
                    </div>

                    {{-- MARCA / MODELO / SERIE --}}
                    <div>
                        <label class="af-label" id="lbl-marca">Marca <span class="af-asterisco" id="req-marca">*</span></label>
                        <input type="text" name="marca" id="crear-input-marca" maxlength="80" class="af-input">
                        <div class="af-error" data-for="crear-input-marca"></div>
                    </div>
                    <div>
                        <label class="af-label" id="lbl-modelo">Modelo <span class="af-asterisco" id="req-modelo">*</span></label>
                        <input type="text" name="modelo" id="crear-input-modelo" maxlength="80" class="af-input">
                        <div class="af-error" data-for="crear-input-modelo"></div>
                    </div>
                    <div>
                        <label class="af-label" id="lbl-serie">N° Serie <span class="af-asterisco" id="req-serie">*</span></label>
                        <input type="text" name="numero_serie" id="crear-input-serie" maxlength="80" class="af-input">
                        <div class="af-error" data-for="crear-input-serie"></div>
                    </div>

                    <div>
                        <label class="af-label">Fuente *</label>
                        <select name="id_fuente" id="crear-id-fuente" class="af-input">
                            <option value="">— Seleccionar —</option>
                            @foreach (\App\Models\FuenteFinanciamiento::orderBy('nombre')->get() as $f)
                                <option value="{{ $f->id_fuente }}">{{ $f->codigo }} - {{ $f->nombre }}</option>
                            @endforeach
                        </select>
                        <div class="af-error" data-for="crear-id-fuente"></div>
                    </div>
                    <div>
                        <label class="af-label">Estado Físico *</label>
                        <select name="estado_fisico" id="crear-estado-fisico" class="af-input">
                            <option value="B">Bueno</option>
                            <option value="R">Regular</option>
                            <option value="M">Malo</option>
                            <option value="FF">Fuera de funcionamiento</option>
                        </select>
                        <div class="af-error" data-for="crear-estado-fisico"></div>
                    </div>
                    <div>
                        <label class="af-label">Valor (Bs.) *</label>
                        <input type="number" name="valor_adquisicion" id="crear-valor-adquisicion" step="0.01" min="0" value="0" class="af-input">
                        <div class="af-error" data-for="crear-valor-adquisicion"></div>
                    </div>

                    <div class="af-span-2">
                        <label class="af-label">Custodio Responsable *</label>
                        <input type="text" id="buscador-custodio-modal" placeholder="🔍 Buscar custodio por Nombre, C.I. o N° de Ítem..."
                            style="width:100%;padding:9px 12px;border-radius:8px 8px 0 0;border:1px solid #cbd5e1;font-size:13px;box-sizing:border-box;background:#f8fafc;"
                            onkeyup="filtrarCustodiosModal(this.value)">
                        <select name="id_custodio" id="crear-id-custodio" size="4"
                            style="width:100%;padding:8px;border-radius:0 0 8px 8px;border:1px solid #cbd5e1;border-top:none;font-size:13px;background:white;box-sizing:border-box;">
                            @foreach (\App\Models\User::where('estado', 'ACTIVO')->with(['items' => fn($q) => $q->whereNull('item_usuario.fecha_fin')])->orderBy('nombre_completo')->get() as $u)
                                @php
                                    $itemActivo = $u->items->first();
                                    $nroItem = $itemActivo ? 'Ítem #' . $itemActivo->numero_item : 'Sin ítem';
                                    $textoBusqueda = strtolower($u->nombre_completo . ' ' . $u->ci . ' ' . ($itemActivo?->numero_item ?? ''));
                                @endphp
                                <option value="{{ $u->id_usuario }}" data-busqueda="{{ $textoBusqueda }}"
                                    style="padding: 6px; border-bottom: 1px solid #f1f5f9;">
                                    {{ $u->nombre_completo }} (C.I.: {{ $u->ci }} · {{ $nroItem }})
                                </option>
                            @endforeach
                        </select>
                        <div class="af-error" data-for="crear-id-custodio"></div>
                    </div>

                    <div class="af-span-2">
                        <label class="af-label">Descripción *</label>
                        <textarea name="descripcion" id="crear-descripcion" rows="2" maxlength="500" class="af-input" style="resize:none;"></textarea>
                        <div class="af-error" data-for="crear-descripcion"></div>
                    </div>

                    {{-- ⭐ 3 FOTOS --}}
                    <div class="af-span-2 af-box">
                        <div style="font-size:12px;font-weight:800;display:block;margin-bottom:8px;color:#1e293b;">
                            📷 Fotografías del Activo <span id="lbl-fotos-info" style="font-weight:600;color:#64748b;"></span>
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;">
                            {{-- FRONTAL --}}
                            <div>
                                <label class="af-label">Foto Frontal <span class="af-asterisco visible" id="req-foto-frontal">*</span></label>
                                <div id="crear-preview-frontal" style="width:100%;height:110px;border:2px dashed #cbd5e1;border-radius:10px;display:flex;align-items:center;justify-content:center;background:#fff;overflow:hidden;margin-bottom:6px;">
                                    <div style="text-align:center;color:#94a3b8;font-size:11px;">📷 Vista frontal</div>
                                </div>
                                <input type="file" name="imagen" id="crear-imagen" accept="image/*" class="af-input" style="padding:6px;font-size:11px;"
                                    onchange="previewImage(this, 'crear-preview-frontal')">
                                <div class="af-error" data-for="crear-imagen"></div>
                            </div>

                            {{-- LATERAL --}}
                            <div id="crear-bloque-lateral">
                                <label class="af-label">Foto Lateral <span class="af-asterisco visible" id="req-foto-lateral">*</span></label>
                                <div id="crear-preview-lateral" style="width:100%;height:110px;border:2px dashed #cbd5e1;border-radius:10px;display:flex;align-items:center;justify-content:center;background:#fff;overflow:hidden;margin-bottom:6px;">
                                    <div style="text-align:center;color:#94a3b8;font-size:11px;">📷 Vista lateral</div>
                                </div>
                                <input type="file" name="imagen_lateral" id="crear-imagen_lateral" accept="image/*" class="af-input" style="padding:6px;font-size:11px;"
                                    onchange="previewImage(this, 'crear-preview-lateral')">
                                <div class="af-error" data-for="crear-imagen_lateral"></div>
                            </div>

                            {{-- DETALLE --}}
                            <div id="crear-bloque-detalle">
                                <label class="af-label">Foto Detalle <span class="af-asterisco visible" id="req-foto-detalle">*</span></label>
                                <div id="crear-preview-detalle" style="width:100%;height:110px;border:2px dashed #cbd5e1;border-radius:10px;display:flex;align-items:center;justify-content:center;background:#fff;overflow:hidden;margin-bottom:6px;">
                                    <div style="text-align:center;color:#94a3b8;font-size:11px;">📷 Placa / Serial</div>
                                </div>
                                <input type="file" name="imagen_detalle" id="crear-imagen_detalle" accept="image/*" class="af-input" style="padding:6px;font-size:11px;"
                                    onchange="previewImage(this, 'crear-preview-detalle')">
                                <div class="af-error" data-for="crear-imagen_detalle"></div>
                            </div>
                        </div>
                    </div>

                </div>

                <div style="display:flex;justify-content:flex-end;gap:10px;border-top:1px solid #e2e8f0;padding-top:16px;margin-top:20px;">
                    <button type="button" onclick="closeActivoModal()"
                        style="background:#e2e8f0;color:#475569;border:none;padding:10px 18px;border-radius:8px;font-weight:700;cursor:pointer;">Cancelar</button>
                    <button type="submit" id="btn-guardar-activo"
                        style="background:#2563eb;color:white;border:none;padding:10px 18px;border-radius:8px;font-weight:700;cursor:pointer;">💾
                        Guardar Activo</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- MODAL: EDITAR ACTIVO --}}
    {{-- ============================================================ --}}
    <div id="edit-activo-modal"
        style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:9999;justify-content:center;align-items:center;padding:20px;">
        <div style="background:white;border-radius:20px;padding:30px;width:100%;max-width:800px;max-height:90vh;overflow-y:auto;box-shadow:0 25px 50px -12px rgba(0,0,0,.25);">

            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;border-bottom:1px solid #e2e8f0;padding-bottom:12px;">
                <h3 style="margin:0;font-size:18px;font-weight:800;color:#0f172a;">✏️ Editar Activo</h3>
                <button type="button" onclick="closeEditActivoModal()"
                    style="background:#f1f5f9;border:none;border-radius:50%;width:32px;height:32px;display:flex;align-items:center;justify-content:center;font-size:18px;cursor:pointer;color:#475569;">×</button>
            </div>

            <form id="edit-activo-modal-form" method="POST" enctype="multipart/form-data" novalidate>
                @csrf
                @method('PUT')
                <input type="hidden" name="id_activo" id="edit-id-activo">
                <input type="hidden" name="id_ambiente" value="{{ $ambiente->id_ambiente ?? '' }}">
                <input type="hidden" name="id_categoria_origen" value="{{ $categoria->id_categoria ?? '' }}">

                <div class="af-grid">

                    <div class="af-span-2 af-box" style="display:flex;align-items:center;justify-content:space-between;">
                        <div>
                            <strong style="font-size: 13px; color: #0f172a; display: block;">Tipo de Clasificación</strong>
                            <span id="edit-ayuda-tipo-bien" style="font-size: 11px; color: #64748b;">Activo Fijo: marca, modelo y serie son obligatorios (3 fotos).</span>
                        </div>
                        <select name="tipo_bien" id="edit-select-tipo-bien" onchange="cambiarTipoBienEdit(this.value)"
                            style="padding: 8px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-weight: 700; font-size: 13px; background: white;">
                            <option value="ACTIVO_FIJO">🔹 Activo Fijo</option>
                            <option value="NO_ACTIVO">🔸 No Activo</option>
                        </select>
                    </div>

                    <div>
                        <label class="af-label">Código *</label>
                        <input type="text" name="codigo_activo" id="edit-codigo-activo" maxlength="50" class="af-input">
                        <div class="af-error" data-for="edit-codigo-activo"></div>
                    </div>

                    <div>
                        <label class="af-label">Categoría (Ambiente) *</label>
                        <select name="id_categoria" id="edit-id-categoria" class="af-input">
                            <option value="">— Seleccionar categoría —</option>
                            @foreach (\App\Models\Categoria::where('id_ambiente', $ambiente->id_ambiente ?? 0)->orderBy('nombre')->get() as $cat)
                                <option value="{{ $cat->id_categoria }}">[{{ $cat->codigo }}] {{ $cat->nombre }}</option>
                            @endforeach
                        </select>
                        <div class="af-error" data-for="edit-id-categoria"></div>
                    </div>

                    <div>
                        <label class="af-label">Cantidad *</label>
                        <input type="number" name="cantidad" id="edit-cantidad" min="1" max="100" class="af-input">
                        <div class="af-error" data-for="edit-cantidad"></div>
                    </div>
                    <div>
                        <label class="af-label">Fecha Adquisición *</label>
                        <input type="date" name="fecha_adquisicion" id="edit-fecha-adquisicion" class="af-input">
                        <div class="af-error" data-for="edit-fecha-adquisicion"></div>
                    </div>

                    <div class="af-span-2">
                        <label class="af-label">Nombre del Activo *</label>
                        <input type="text" name="nombre" id="edit-nombre" maxlength="150" class="af-input">
                        <div class="af-error" data-for="edit-nombre"></div>
                    </div>

                    <div>
                        <label class="af-label" id="edit-lbl-marca">Marca <span class="af-asterisco" id="edit-req-marca">*</span></label>
                        <input type="text" name="marca" id="edit-input-marca" maxlength="80" class="af-input">
                        <div class="af-error" data-for="edit-input-marca"></div>
                    </div>
                    <div>
                        <label class="af-label" id="edit-lbl-modelo">Modelo <span class="af-asterisco" id="edit-req-modelo">*</span></label>
                        <input type="text" name="modelo" id="edit-input-modelo" maxlength="80" class="af-input">
                        <div class="af-error" data-for="edit-input-modelo"></div>
                    </div>
                    <div>
                        <label class="af-label" id="edit-lbl-serie">N° Serie <span class="af-asterisco" id="edit-req-serie">*</span></label>
                        <input type="text" name="numero_serie" id="edit-input-serie" maxlength="80" class="af-input">
                        <div class="af-error" data-for="edit-input-serie"></div>
                    </div>

                    <div>
                        <label class="af-label">Fuente *</label>
                        <select name="id_fuente" id="edit-id-fuente" class="af-input">
                            <option value="">— Seleccionar —</option>
                            @foreach (\App\Models\FuenteFinanciamiento::orderBy('nombre')->get() as $f)
                                <option value="{{ $f->id_fuente }}">{{ $f->codigo }} - {{ $f->nombre }}</option>
                            @endforeach
                        </select>
                        <div class="af-error" data-for="edit-id-fuente"></div>
                    </div>
                    <div>
                        <label class="af-label">Estado Físico *</label>
                        <select name="estado_fisico" id="edit-estado-fisico" class="af-input">
                            <option value="B">Bueno</option>
                            <option value="R">Regular</option>
                            <option value="M">Malo</option>
                            <option value="FF">Fuera de funcionamiento</option>
                        </select>
                        <div class="af-error" data-for="edit-estado-fisico"></div>
                    </div>
                    <div>
                        <label class="af-label">Valor (Bs.) *</label>
                        <input type="number" name="valor_adquisicion" id="edit-valor-adquisicion" step="0.01" min="0" class="af-input">
                        <div class="af-error" data-for="edit-valor-adquisicion"></div>
                    </div>

                    <div class="af-span-2">
                        <label class="af-label">Custodio Responsable *</label>
                        <input type="text" id="edit-buscador-custodio-modal" placeholder="🔍 Buscar custodio por Nombre, C.I. o N° de Ítem..."
                            style="width:100%;padding:9px 12px;border-radius:8px 8px 0 0;border:1px solid #cbd5e1;font-size:13px;box-sizing:border-box;background:#f8fafc;"
                            onkeyup="filtrarCustodiosEditModal(this.value)">
                        <select name="id_custodio" id="edit-id-custodio" size="4"
                            style="width:100%;padding:8px;border-radius:0 0 8px 8px;border:1px solid #cbd5e1;border-top:none;font-size:13px;background:white;box-sizing:border-box;">
                            @foreach (\App\Models\User::where('estado', 'ACTIVO')->with(['items' => fn($q) => $q->whereNull('item_usuario.fecha_fin')])->orderBy('nombre_completo')->get() as $u)
                                @php
                                    $itemActivo = $u->items->first();
                                    $nroItem = $itemActivo ? 'Ítem #' . $itemActivo->numero_item : 'Sin ítem';
                                    $textoBusqueda = strtolower($u->nombre_completo . ' ' . $u->ci . ' ' . ($itemActivo?->numero_item ?? ''));
                                @endphp
                                <option value="{{ $u->id_usuario }}" data-busqueda="{{ $textoBusqueda }}"
                                    style="padding: 6px; border-bottom: 1px solid #f1f5f9;">
                                    {{ $u->nombre_completo }} (C.I.: {{ $u->ci }} · {{ $nroItem }})
                                </option>
                            @endforeach
                        </select>
                        <div class="af-error" data-for="edit-id-custodio"></div>
                    </div>

                    <div class="af-span-2">
                        <label class="af-label">Descripción *</label>
                        <textarea name="descripcion" id="edit-descripcion" rows="2" maxlength="500" class="af-input" style="resize:none;"></textarea>
                        <div class="af-error" data-for="edit-descripcion"></div>
                    </div>

                    {{-- ⭐ 3 FOTOS EDIT --}}
                    <div class="af-span-2 af-box">
                        <div style="font-size:12px;font-weight:800;display:block;margin-bottom:8px;color:#1e293b;">
                            📷 Fotografías del Activo <span id="edit-lbl-fotos-info" style="font-weight:600;color:#64748b;"></span>
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;">
                            {{-- FRONTAL --}}
                            <div>
                                <label class="af-label">Foto Frontal <span class="af-asterisco visible" id="edit-req-foto-frontal">*</span></label>
                                <div id="edit-preview-frontal" style="width:100%;height:110px;border:2px dashed #cbd5e1;border-radius:10px;display:flex;align-items:center;justify-content:center;background:#fff;overflow:hidden;margin-bottom:6px;">
                                    <div style="text-align:center;color:#94a3b8;font-size:11px;">Dejar vacío para mantener</div>
                                </div>
                                <input type="file" name="imagen" id="edit-imagen" accept="image/*" class="af-input" style="padding:6px;font-size:11px;"
                                    onchange="previewImage(this, 'edit-preview-frontal')">
                                <div class="af-error" data-for="edit-imagen"></div>
                            </div>

                            {{-- LATERAL --}}
                            <div id="edit-bloque-lateral">
                                <label class="af-label">Foto Lateral <span class="af-asterisco visible" id="edit-req-foto-lateral">*</span></label>
                                <div id="edit-preview-lateral" style="width:100%;height:110px;border:2px dashed #cbd5e1;border-radius:10px;display:flex;align-items:center;justify-content:center;background:#fff;overflow:hidden;margin-bottom:6px;">
                                    <div style="text-align:center;color:#94a3b8;font-size:11px;">Dejar vacío para mantener</div>
                                </div>
                                <input type="file" name="imagen_lateral" id="edit-imagen_lateral" accept="image/*" class="af-input" style="padding:6px;font-size:11px;"
                                    onchange="previewImage(this, 'edit-preview-lateral')">
                                <div class="af-error" data-for="edit-imagen_lateral"></div>
                            </div>

                            {{-- DETALLE --}}
                            <div id="edit-bloque-detalle">
                                <label class="af-label">Foto Detalle <span class="af-asterisco visible" id="edit-req-foto-detalle">*</span></label>
                                <div id="edit-preview-detalle" style="width:100%;height:110px;border:2px dashed #cbd5e1;border-radius:10px;display:flex;align-items:center;justify-content:center;background:#fff;overflow:hidden;margin-bottom:6px;">
                                    <div style="text-align:center;color:#94a3b8;font-size:11px;">Dejar vacío para mantener</div>
                                </div>
                                <input type="file" name="imagen_detalle" id="edit-imagen_detalle" accept="image/*" class="af-input" style="padding:6px;font-size:11px;"
                                    onchange="previewImage(this, 'edit-preview-detalle')">
                                <div class="af-error" data-for="edit-imagen_detalle"></div>
                            </div>
                        </div>
                    </div>

                </div>

                <div style="display:flex;justify-content:flex-end;gap:10px;border-top:1px solid #e2e8f0;padding-top:16px;margin-top:20px;">
                    <button type="button" onclick="closeEditActivoModal()"
                        style="background:#e2e8f0;color:#475569;border:none;padding:10px 18px;border-radius:8px;font-weight:700;cursor:pointer;">Cancelar</button>
                    <button type="submit" id="btn-actualizar-activo"
                        style="background:#2563eb;color:white;border:none;padding:10px 18px;border-radius:8px;font-weight:700;cursor:pointer;">💾
                        Actualizar Activo</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // ==================== HELPERS ====================

        function mostrarError(idCampo, mensaje) {
            const div = document.querySelector(`.af-error[data-for="${idCampo}"]`);
            const campo = document.getElementById(idCampo);
            if (div) { div.textContent = '⚠️ ' + mensaje; div.classList.add('visible'); }
            if (campo) campo.classList.add('af-input-error');
        }

        function limpiarError(idCampo) {
            const div = document.querySelector(`.af-error[data-for="${idCampo}"]`);
            const campo = document.getElementById(idCampo);
            if (div) { div.textContent = ''; div.classList.remove('visible'); }
            if (campo) campo.classList.remove('af-input-error');
        }

        function limpiarTodosLosErrores() {
            document.querySelectorAll('.af-error').forEach(el => { el.textContent = ''; el.classList.remove('visible'); });
            document.querySelectorAll('.af-input-error').forEach(el => el.classList.remove('af-input-error'));
        }

        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            if (!preview) return;
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => preview.innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;border-radius:8px;">`;
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Limpiar al escribir
        document.querySelectorAll('input, select, textarea').forEach(el => {
            if (el.id) {
                el.addEventListener('input', () => limpiarError(el.id));
                el.addEventListener('change', () => limpiarError(el.id));
            }
        });

        // ==================== TIPO DE BIEN ====================

        function aplicarTipoBien(tipo, prefijo) {
            const esFijo = tipo === 'ACTIVO_FIJO';

            // Asteriscos
            const reqMarca = document.getElementById(prefijo + (prefijo === 'crear' ? '-req-marca' : '-req-marca'));
            const reqModelo = document.getElementById(prefijo + '-req-modelo');
            const reqSerie = document.getElementById(prefijo + '-req-serie');
            const reqFotoFrontal = document.getElementById(prefijo + '-req-foto-frontal');
            const reqFotoLateral = document.getElementById(prefijo + '-req-foto-lateral');
            const reqFotoDetalle = document.getElementById(prefijo + '-req-foto-detalle');

            [reqMarca, reqModelo, reqSerie].forEach(el => { if (el) el.classList.toggle('visible', esFijo); });
            [reqFotoFrontal, reqFotoLateral, reqFotoDetalle].forEach(el => { if (el) el.classList.toggle('visible', esFijo); });

            // Ocultar bloques lateral y detalle si NO_ACTIVO
            const bloqueLateral = document.getElementById(prefijo + '-bloque-lateral');
            const bloqueDetalle = document.getElementById(prefijo + '-bloque-detalle');
            if (bloqueLateral) bloqueLateral.style.display = esFijo ? 'block' : 'none';
            if (bloqueDetalle) bloqueDetalle.style.display = esFijo ? 'block' : 'none';

            // Ajustar grilla
            const labelFotos = document.getElementById(prefijo === 'crear' ? 'lbl-fotos-info' : 'edit-lbl-fotos-info');
            if (labelFotos) labelFotos.textContent = esFijo ? '(3 fotos obligatorias)' : '(1 foto frontal obligatoria)';

            const ayuda = document.getElementById(prefijo + '-ayuda-tipo-bien');
            if (ayuda) {
                ayuda.textContent = esFijo
                    ? 'Activo Fijo: marca, modelo, serie y las 3 fotos son obligatorios.'
                    : 'No Activo: marca, modelo y serie son opcionales. Solo la foto frontal es obligatoria.';
            }

            // Limpiar errores si pasa a NO_ACTIVO
            if (!esFijo) {
                ['marca', 'modelo', 'numero_serie'].forEach(c => {
                    const id = prefijo === 'crear'
                        ? `crear-input-${c === 'numero_serie' ? 'serie' : c}`
                        : `edit-input-${c === 'numero_serie' ? 'serie' : c}`;
                    limpiarError(id);
                });
            }
        }

        function cambiarTipoBien(tipo) { aplicarTipoBien(tipo, 'crear'); }
        function cambiarTipoBienEdit(tipo) { aplicarTipoBien(tipo, 'edit'); }

        // ==================== FILTROS ====================

        function filtrarCustodiosModal(texto) {
            const filtro = texto.toLowerCase().trim();
            const select = document.getElementById('crear-id-custodio');
            for (let i = 0; i < select.options.length; i++) {
                const opt = select.options[i];
                const busqueda = opt.getAttribute('data-busqueda') || "";
                opt.style.display = busqueda.includes(filtro) ? "" : "none";
            }
        }

        function filtrarCustodiosEditModal(texto) {
            const filtro = texto.toLowerCase().trim();
            const select = document.getElementById('edit-id-custodio');
            for (let i = 0; i < select.options.length; i++) {
                const opt = select.options[i];
                const busqueda = opt.getAttribute('data-busqueda') || "";
                opt.style.display = busqueda.includes(filtro) ? "" : "none";
            }
        }

        // ==================== VALIDACIÓN ====================

        function validar(tipoBien, prefijo) {
            limpiarTodosLosErrores();
            let hayErrores = false;
            const esFijo = tipoBien === 'ACTIVO_FIJO';

            const requeridos = [
                { id: prefijo + '-codigo-activo', msg: 'El código es obligatorio.' },
                { id: prefijo + '-id-categoria', msg: 'Selecciona una categoría.' },
                { id: prefijo + '-cantidad', msg: 'La cantidad es obligatoria.' },
                { id: prefijo + '-fecha-adquisicion', msg: 'La fecha es obligatoria.' },
                { id: prefijo + '-nombre', msg: 'El nombre es obligatorio.' },
                { id: prefijo + '-id-fuente', msg: 'Selecciona una fuente.' },
                { id: prefijo + '-estado-fisico', msg: 'Selecciona un estado físico.' },
                { id: prefijo + '-valor-adquisicion', msg: 'El valor es obligatorio.' },
                { id: prefijo + '-id-custodio', msg: 'Selecciona un custodio.' },
                { id: prefijo + '-descripcion', msg: 'La descripción es obligatoria.' },
            ];

            if (esFijo) {
                requeridos.push(
                    { id: prefijo + '-input-marca', msg: 'La marca es obligatoria para Activo Fijo.' },
                    { id: prefijo + '-input-modelo', msg: 'El modelo es obligatorio para Activo Fijo.' },
                    { id: prefijo + '-input-serie', msg: 'El N° de serie es obligatorio para Activo Fijo.' },
                );
            }

            requeridos.forEach(({ id, msg }) => {
                const campo = document.getElementById(id);
                if (!campo) return;
                if ((campo.value || '').trim() === '') {
                    mostrarError(id, msg);
                    hayErrores = true;
                }
            });

            // Validar foto frontal
            const fotoFrontal = document.getElementById(prefijo + '-imagen');
            if (esFijo && (!fotoFrontal.files || fotoFrontal.files.length === 0) && !document.getElementById(prefijo + '-id-activo')?.value) {
                mostrarError(prefijo + '-imagen', 'La foto frontal es obligatoria para Activo Fijo.');
                hayErrores = true;
            }

            // Fotos lateral y detalle solo en ACTIVO_FIJO y solo al crear
            if (esFijo && prefijo === 'crear') {
                const fotoLateral = document.getElementById('crear-imagen_lateral');
                const fotoDetalle = document.getElementById('crear-imagen_detalle');
                if (!fotoLateral.files || fotoLateral.files.length === 0) {
                    mostrarError('crear-imagen_lateral', 'La foto lateral es obligatoria para Activo Fijo.');
                    hayErrores = true;
                }
                if (!fotoDetalle.files || fotoDetalle.files.length === 0) {
                    mostrarError('crear-imagen_detalle', 'La foto detalle es obligatoria para Activo Fijo.');
                    hayErrores = true;
                }
            }

            return !hayErrores;
        }

        // ==================== MODAL CREAR ====================

        function openActivoModal() {
            const form = document.getElementById('activo-modal-form');
            form.reset();
            limpiarTodosLosErrores();
            document.getElementById('select-tipo-bien').value = 'ACTIVO_FIJO';
            cambiarTipoBien('ACTIVO_FIJO');
            document.getElementById('activo-modal').style.display = 'flex';
        }

        function closeActivoModal() {
            document.getElementById('activo-modal').style.display = 'none';
        }

        document.getElementById('activo-modal-form')?.addEventListener('submit', async function(e) {
            e.preventDefault();

            const tipoBien = document.getElementById('select-tipo-bien').value;
            if (!validar(tipoBien, 'crear')) return;

            const form = this;
            const btn = document.getElementById('btn-guardar-activo');
            btn.disabled = true;
            btn.innerHTML = '⏳ Guardando...';
            btn.style.opacity = '0.7';

            try {
                const formData = new FormData(form);
                const res = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    credentials: 'same-origin',
                });
                const json = await res.json();
                if (res.ok && json.success !== false) {
                    closeActivoModal();
                    mostrarToast('✅ Activo creado correctamente. Recargando...', 'success');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    mostrarErroresServidor(json, 'crear');
                }
            } catch (err) {
                console.error(err);
                mostrarToast('⚠️ Error de conexión. Intenta nuevamente.', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '💾 Guardar Activo';
                btn.style.opacity = '1';
            }
        });

        // ==================== MODAL EDITAR ====================

        function openEditActivoModal(idActivo) {
            fetch(`{{ url('/activos') }}/${idActivo}/edit`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                credentials: 'same-origin',
            })
            .then(res => { if (!res.ok) throw new Error('Error'); return res.json(); })
            .then(data => {
                document.getElementById('edit-id-activo').value = data.id_activo;
                document.getElementById('edit-codigo-activo').value = data.codigo_activo || '';
                document.getElementById('edit-id-categoria').value = data.id_categoria || '';
                document.getElementById('edit-cantidad').value = data.cantidad || 1;
                document.getElementById('edit-fecha-adquisicion').value = data.fecha_adquisicion || '';
                document.getElementById('edit-nombre').value = data.nombre || '';
                document.getElementById('edit-input-marca').value = data.marca || '';
                document.getElementById('edit-input-modelo').value = data.modelo || '';
                document.getElementById('edit-input-serie').value = data.numero_serie || '';
                document.getElementById('edit-id-fuente').value = data.id_fuente || '';
                document.getElementById('edit-estado-fisico').value = data.estado_fisico || 'B';
                document.getElementById('edit-valor-adquisicion').value = data.valor_adquisicion || 0;
                document.getElementById('edit-descripcion').value = data.descripcion || '';
                document.getElementById('edit-id-custodio').value = data.id_custodio || '';
                document.getElementById('edit-select-tipo-bien').value = data.tipo_bien || 'ACTIVO_FIJO';

                cambiarTipoBienEdit(data.tipo_bien || 'ACTIVO_FIJO');
                limpiarTodosLosErrores();

                document.getElementById('edit-activo-modal-form').action = `{{ url('/activos') }}/${idActivo}`;
                document.getElementById('edit-activo-modal').style.display = 'flex';
            })
            .catch(err => { console.error(err); mostrarToast('❌ Error al cargar el activo', 'error'); });
        }

        function closeEditActivoModal() {
            document.getElementById('edit-activo-modal').style.display = 'none';
        }

        document.getElementById('edit-activo-modal-form')?.addEventListener('submit', async function(e) {
            e.preventDefault();

            const tipoBien = document.getElementById('edit-select-tipo-bien').value;
            if (!validar(tipoBien, 'edit')) return;

            const form = this;
            const btn = document.getElementById('btn-actualizar-activo');
            btn.disabled = true;
            btn.innerHTML = '⏳ Actualizando...';
            btn.style.opacity = '0.7';

            try {
                const formData = new FormData(form);
                formData.append('_method', 'PUT');
                const res = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    credentials: 'same-origin',
                });
                const json = await res.json();
                if (res.ok && json.success !== false) {
                    closeEditActivoModal();
                    mostrarToast('✅ Activo actualizado correctamente. Recargando...', 'success');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    mostrarErroresServidor(json, 'edit');
                }
            } catch (err) {
                console.error(err);
                mostrarToast('⚠️ Error de conexión. Intenta nuevamente.', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '💾 Actualizar Activo';
                btn.style.opacity = '1';
            }
        });

        // ==================== ERRORES DEL SERVIDOR ====================

        function mostrarErroresServidor(json, prefijo) {
            limpiarTodosLosErrores();
            if (!json.errors) {
                mostrarToast('⚠️ ' + (json.message || 'Error al procesar la solicitud'), 'error');
                return;
            }

            Object.keys(json.errors).forEach(campo => {
                const mensaje = json.errors[campo][0];
                let idCampo = null;
                const map = {
                    'codigo_activo': prefijo + '-codigo-activo',
                    'nombre': prefijo + '-nombre',
                    'id_categoria': prefijo + '-id-categoria',
                    'id_fuente': prefijo + '-id-fuente',
                    'id_custodio': prefijo + '-id-custodio',
                    'estado_fisico': prefijo + '-estado-fisico',
                    'cantidad': prefijo + '-cantidad',
                    'fecha_adquisicion': prefijo + '-fecha-adquisicion',
                    'valor_adquisicion': prefijo + '-valor-adquisicion',
                    'descripcion': prefijo + '-descripcion',
                    'marca': prefijo + '-input-marca',
                    'modelo': prefijo + '-input-modelo',
                    'numero_serie': prefijo + '-input-serie',
                    'imagen': prefijo + '-imagen',
                    'imagen_lateral': prefijo + '-imagen_lateral',
                    'imagen_detalle': prefijo + '-imagen_detalle',
                };
                idCampo = map[campo];
                if (idCampo && document.getElementById(idCampo)) {
                    mostrarError(idCampo, mensaje);
                }
            });
        }

        // ==================== ELIMINAR ====================

        function confirmarEliminarActivo(id, codigo, nombre) {
            Swal.fire({
                title: '¿Eliminar activo?',
                html: `
                <div style="text-align:center; padding: 8px 0;">
                    <div style="font-size: 56px; margin-bottom: 12px;">📦</div>
                    <p style="font-size: 15px; color: #475569; margin: 0 0 8px 0;">¿Estás seguro de eliminar:</p>
                    <p style="font-size: 16px; font-weight: 800; color: #dc2626; margin: 0 0 12px 0;">"${nombre}"</p>
                    <p style="font-size: 13px; color: #64748b; font-family: monospace; margin: 0 0 12px 0;">${codigo}</p>
                    <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 10px; padding: 10px 14px; font-size: 12px; color: #991b1b; font-weight: 600;">
                        ⚠️ Esta acción no se puede deshacer
                    </div>
                </div>`,
                icon: 'warning',
                iconColor: '#dc2626',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: '🗑️ Sí, eliminar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ url('/activos') }}/' + id;
                    const csrf = document.createElement('input');
                    csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = '{{ csrf_token() }}';
                    const method = document.createElement('input');
                    method.type = 'hidden'; method.name = '_method'; method.value = 'DELETE';
                    form.appendChild(csrf); form.appendChild(method);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        // ==================== TOAST ====================

        function mostrarToast(mensaje, tipo = 'success') {
            const c = tipo === 'success' ? { bg: '#dcfce7', border: '#86efac', color: '#166534', icon: '✓' }
                                          : { bg: '#fee2e2', border: '#fca5a5', color: '#991b1b', icon: '✕' };
            const toast = document.createElement('div');
            toast.style.cssText = `position:fixed;top:20px;right:20px;z-index:99999;background:${c.bg};border:1px solid ${c.border};color:${c.color};padding:14px 20px;border-radius:12px;font-size:14px;font-weight:700;box-shadow:0 10px 25px rgba(0,0,0,.15);display:flex;align-items:center;gap:10px;`;
            toast.innerHTML = `<span style="font-size:18px;">${c.icon}</span><span>${mensaje}</span>`;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transition = 'all 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // ==================== ESC / CLICK FUERA ====================

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const m1 = document.getElementById('activo-modal');
                if (m1 && m1.style.display === 'flex') closeActivoModal();
                const m2 = document.getElementById('edit-activo-modal');
                if (m2 && m2.style.display === 'flex') closeEditActivoModal();
            }
        });

        document.getElementById('activo-modal')?.addEventListener('click', function(e) {
            if (e.target === this) closeActivoModal();
        });

        document.getElementById('edit-activo-modal')?.addEventListener('click', function(e) {
            if (e.target === this) closeEditActivoModal();
        });

        // Inicializar al cargar
        document.addEventListener('DOMContentLoaded', () => {
            cambiarTipoBien('ACTIVO_FIJO');
            cambiarTipoBienEdit('ACTIVO_FIJO');
        });
    </script>
@endsection
