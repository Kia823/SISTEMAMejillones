@extends('layouts.app')

@section('content')
<div style="max-width: 880px; margin: 0 auto; background:#ffffff; border-radius:20px; padding:0; box-shadow:0 4px 12px rgba(0,0,0,0.05); overflow:hidden;">

    {{-- Header --}}
    <div style="padding:20px 26px; border-bottom:1.5px solid #f1f5f9; display:flex; justify-content:space-between; align-items:center; background:linear-gradient(135deg,#f8fafc,#ffffff);">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="background:linear-gradient(135deg,#2563eb,#1d4ed8); color:#ffffff; width:42px; height:42px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:20px; box-shadow:0 4px 10px -2px rgba(37,99,235,0.4);">📦</div>
            <div>
                <h2 style="margin:0; font-size:18px; font-weight:800; color:#0f172a;">Nuevo Activo</h2>
                <p style="margin:0; font-size:12px; color:#64748b; font-weight:500;">Completa los datos del activo. Si registras varios, usa el campo "Cantidad".</p>
            </div>
        </div>
        <a href="{{ route('activos.index') }}" style="background:#f1f5f9; border:none; border-radius:10px; width:36px; height:36px; display:flex; align-items:center; justify-content:center; font-size:16px; cursor:pointer; color:#64748b; text-decoration:none; transition:all 0.2s; font-weight:700;" onmouseover="this.style.background='#e2e8f0'; this.style.color='#1e293b';" onmouseout="this.style.background='#f1f5f9'; this.style.color='#64748b';">←</a>
    </div>

    {{-- ERRORES DEL SERVIDOR --}}
    @if ($errors->any())
        <div style="background:#fee2e2;border:1px solid #fecaca;color:#991b1b;padding:14px 20px;border-radius:12px;margin:0 26px 20px 26px;font-size:13px;">
            <strong>⚠️ Corrige los siguientes errores:</strong>
            <ul style="margin:8px 0 0 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('activos.store') }}" enctype="multipart/form-data" id="activo-form" style="padding:24px 26px; overflow-y:auto;">
        @csrf

        @if(request('id_categoria'))
            <input type="hidden" name="id_categoria_origen" value="{{ request('id_categoria') }}">
        @endif>

        <style>
            .af-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
            .af-grid > div { display:flex; flex-direction:column; }
            .af-label { font-size:11px; font-weight:800; color:#475569; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.5px; }
            .af-input {
                width:100%; padding:10px 14px; border-radius:10px; border:1.5px solid #e2e8f0; font-size:13px; font-weight:500; color:#0f172a;
                box-sizing:border-box; background:#f8fafc; transition:all 0.2s ease; font-family:inherit;
            }
            .af-input:hover { border-color:#cbd5e1; }
            .af-input:focus { outline:none; border-color:#2563eb; background:#ffffff; box-shadow:0 0 0 4px rgba(37,99,235,0.1); }
            .af-span-2 { grid-column:span 2; }
            .af-box { background:#f8fafc; padding:16px; border-radius:14px; border:1.5px solid #e2e8f0; }
            .af-error { display:none; color:#ef4444; font-size:11px; font-weight:700; margin-top:5px; }
            .af-error.visible { display:block; }
            .af-input-error { border-color:#ef4444 !important; background:#fef2f2 !important; }
            .af-asterisco { color:#ef4444; margin-left:2px; display:none; font-weight:900; }
            .af-asterisco.visible { display:inline; }
            .af-dropzone {
                width:100%; height:110px; border:2px dashed #cbd5e1; border-radius:12px; display:flex; flex-direction:column;
                align-items:center; justify-content:center; background:#ffffff; overflow:hidden; margin-bottom:8px; transition:all 0.2s; cursor:pointer;
            }
            .af-dropzone:hover { border-color:#2563eb; background:#f0f6ff; }
            .btn-secondary { background:#f1f5f9; color:#475569; border:none; padding:11px 22px; border-radius:10px; font-weight:700; font-size:13px; cursor:pointer; transition:all 0.2s; text-decoration:none; display:inline-block; }
            .btn-secondary:hover { background:#e2e8f0; color:#1e293b; }
            .btn-primary { background:linear-gradient(135deg,#2563eb,#1d4ed8); color:#ffffff; border:none; padding:11px 24px; border-radius:10px; font-weight:700; font-size:13px; cursor:pointer; box-shadow:0 4px 12px -2px rgba(37,99,235,0.4); transition:all 0.2s; }
            .btn-primary:hover { background:linear-gradient(135deg,#1d4ed8,#1e40af); box-shadow:0 8px 16px -4px rgba(37,99,235,0.5); transform:translateY(-1px); }
            .btn-primary:disabled { opacity:0.6; cursor:not-allowed; transform:none; }

            /* Custodio selector */
            .custodio-wrapper {
                border:1.5px solid #e2e8f0; border-radius:12px; overflow:hidden; background:#ffffff;
                transition:all 0.2s;
            }
            .custodio-wrapper:focus-within { border-color:#2563eb; box-shadow:0 0 0 4px rgba(37,99,235,0.1); }
            .custodio-search {
                width:100%; padding:12px 16px; border:none; border-bottom:1.5px solid #f1f5f9; font-size:13px; outline:none;
                background:#f8fafc; font-weight:500; color:#0f172a; box-sizing:border-box;
            }
            .custodio-search::placeholder { color:#94a3b8; font-weight:500; }
            .custodio-search:focus { background:#ffffff; }
            .custodio-list {
                width:100%; padding:6px; border:none; font-size:13px; background:white; outline:none;
                max-height:160px; overflow-y:auto;
            }
            .custodio-list option {
                padding:10px 14px; border-radius:8px; cursor:pointer; color:#1e293b; font-weight:600;
                margin-bottom:2px; transition:background 0.15s; border:1px solid transparent;
            }
            .custodio-list option:hover { background:#eff6ff; }
            .custodio-list option:checked { background:#2563eb; color:white; }
            .custodio-list option:checked::before { content:'✓ '; font-weight:900; }
        </style>

        <div class="af-grid">

            {{-- Clasificación --}}
            <div class="af-span-2 af-box" style="display:flex; align-items:center; justify-content:space-between; border-left:4px solid #2563eb; background:linear-gradient(90deg,#eff6ff,#ffffff);">
                <div>
                    <strong style="font-size:13px; color:#0f172a; display:block; font-weight:800;">🏷️ Clasificación del bien</strong>
                    <span id="aviso-serie" style="font-size:11px; color:#64748b; font-weight:500;">Activo Fijo: marca, modelo, serie y 3 fotos obligatorias.</span>
                </div>
                <select name="tipo_bien" id="select-tipo-bien" onchange="aplicarTipoBien(this.value)" class="af-input" style="width:auto; font-weight:800; padding:9px 16px; background:white;">
                    @php $tipoInicial = old('tipo_bien', 'ACTIVO_FIJO'); @endphp
                    <option value="ACTIVO_FIJO" {{ $tipoInicial === 'ACTIVO_FIJO' ? 'selected' : '' }}>🔹 Activo Fijo</option>
                    <option value="NO_ACTIVO" {{ $tipoInicial === 'NO_ACTIVO' ? 'selected' : '' }}>🔸 No Activo</option>
                </select>
            </div>

            {{-- Código & Categoría --}}
            <div>
                <label class="af-label">Código del activo <span class="af-asterisco visible">*</span></label>
                <input type="text" name="codigo_activo" id="codigo_activo" maxlength="50" class="af-input"
                       style="text-transform:uppercase;" value="{{ old('codigo_activo', $codigoSugerido ?? '') }}">
                <div class="af-error" data-for="codigo_activo"></div>
            </div>

            <div>
                <label class="af-label">Categoría (Ambiente) <span class="af-asterisco visible">*</span></label>
                <select name="id_categoria" id="id_categoria" onchange="filtrarCategoriasPorAmbiente(this.value)" class="af-input">
                    <option value="">— Seleccione categoría del ambiente —</option>
                    @foreach ($categorias as $item)
                        <option value="{{ $item->id_categoria }}" data-ambiente="{{ $item->id_ambiente }}"
                            {{ (old('id_categoria', request('id_categoria')) == $item->id_categoria) ? 'selected' : '' }}>
                            [{{ $item->codigo }}] {{ $item->nombre }}
                        </option>
                    @endforeach
                </select>
                <div class="af-error" data-for="id_categoria"></div>
            </div>

            {{-- Ambiente --}}
            <div>
                <label class="af-label">Ambiente <span class="af-asterisco visible">*</span></label>
                <select name="id_ambiente" id="id_ambiente" class="af-input">
                    <option value="">— Seleccione ambiente —</option>
                    @foreach ($ambientes as $item)
                        <option value="{{ $item->id_ambiente }}"
                            {{ old('id_ambiente', $idAmbientePreseleccionado ?? '') == $item->id_ambiente ? 'selected' : '' }}>
                            {{ $item->nombre }}
                        </option>
                    @endforeach
                </select>
                <div class="af-error" data-for="id_ambiente"></div>
            </div>

            {{-- Cantidad & Fecha --}}
            <div>
                <label class="af-label">Cantidad <span class="af-asterisco visible">*</span></label>
                <input type="number" name="cantidad" id="cantidad" min="1" max="100" value="{{ old('cantidad', 1) }}" class="af-input">
                <div class="af-error" data-for="cantidad"></div>
            </div>

            <div>
                <label class="af-label">Fecha Adquisición <span class="af-asterisco visible">*</span></label>
                <input type="date" name="fecha_adquisicion" id="fecha_adquisicion" value="{{ old('fecha_adquisicion', date('Y-m-d')) }}" class="af-input">
                <div class="af-error" data-for="fecha_adquisicion"></div>
            </div>

            {{-- Nombre --}}
            <div class="af-span-2">
                <label class="af-label">Nombre del activo <span class="af-asterisco visible">*</span></label>
                <input type="text" name="nombre" id="nombre" maxlength="150" placeholder="Ej: Laptop Dell Latitude 5430" class="af-input">
                <div class="af-error" data-for="nombre"></div>
            </div>

            {{-- Marca, Modelo, Serie --}}
            <div>
                <label class="af-label" id="lbl-marca">Marca <span class="af-asterisco" id="req-marca">*</span></label>
                <input type="text" name="marca" id="marca" maxlength="80" placeholder="Ej. Dell, HP, Samsung" class="af-input">
                <div class="af-error" data-for="marca"></div>
            </div>

            <div>
                <label class="af-label" id="lbl-modelo">Modelo <span class="af-asterisco" id="req-modelo">*</span></label>
                <input type="text" name="modelo" id="modelo" maxlength="80" placeholder="Ej. Latitude 3420" class="af-input">
                <div class="af-error" data-for="modelo"></div>
            </div>

            <div class="af-span-2">
                <label class="af-label" id="lbl-serie">N° Serie <span class="af-asterisco" id="req-serie">*</span></label>
                <input type="text" name="numero_serie" id="numero_serie" maxlength="80" placeholder="Ej. SN-9876543210" class="af-input">
                <div class="af-error" data-for="numero_serie"></div>
            </div>

            {{-- Fuente, Estado, Valor --}}
            <div>
                <label class="af-label">Fuente de financiamiento <span class="af-asterisco visible">*</span></label>
                <select name="id_fuente" id="id_fuente" class="af-input">
                    <option value="">— Seleccione fuente —</option>
                    @foreach ($fuentes as $item)
                        <option value="{{ $item->id_fuente }}" {{ old('id_fuente') == $item->id_fuente ? 'selected' : '' }}>
                            {{ $item->codigo ? $item->codigo . ' - ' : '' }}{{ $item->nombre }}
                        </option>
                    @endforeach
                </select>
                <div class="af-error" data-for="id_fuente"></div>
            </div>

            <div>
                <label class="af-label">Estado Físico <span class="af-asterisco visible">*</span></label>
                <select name="estado_fisico" id="estado_fisico" class="af-input">
                    <option value="B" {{ old('estado_fisico') == 'B' ? 'selected' : '' }}>🟢 Bueno</option>
                    <option value="R" {{ old('estado_fisico') == 'R' ? 'selected' : '' }}>🟡 Regular</option>
                    <option value="M" {{ old('estado_fisico') == 'M' ? 'selected' : '' }}>🔴 Malo</option>
                    <option value="FF" {{ old('estado_fisico') == 'FF' ? 'selected' : '' }}>⚫ Fuera de funcionamiento</option>
                </select>
                <div class="af-error" data-for="estado_fisico"></div>
            </div>

            <div class="af-span-2">
                <label class="af-label">Valor Adquisición (Bs.) <span class="af-asterisco visible">*</span></label>
                <input type="number" name="valor_adquisicion" id="valor_adquisicion" step="0.01" min="0" value="{{ old('valor_adquisicion', 0) }}" class="af-input">
                <div class="af-error" data-for="valor_adquisicion"></div>
            </div>

            {{-- ⭐ CUSTODIO RESPONSABLE --}}
            <div class="af-span-2">
                <label class="af-label">👤 Custodio Responsable <span class="af-asterisco visible">*</span></label>
                <div class="custodio-wrapper">
                    <input type="text" id="buscador-custodio"
                        class="custodio-search"
                        placeholder="🔍 Buscar custodio por Nombre, C.I. o N° de Ítem..."
                        onkeyup="filtrarCustodios(this.value)"
                        autocomplete="off">
                    <select name="id_custodio" id="id_custodio" size="5" class="custodio-list">
                        <option value="">— Seleccione custodio de la lista —</option>
                        @foreach ($usuarios as $usr)
                            @php
                                $itemActivo = $usr->items->first();
                                $nroItem = $itemActivo ? 'Ítem #' . $itemActivo->numero_item : 'Sin ítem';
                                $textoMostrar = "{$nroItem} · C.I.: {$usr->ci} · {$usr->nombre_completo}";
                                $textoBusqueda = strtolower($nroItem . ' ' . $usr->ci . ' ' . $usr->nombre_completo);
                            @endphp
                            <option value="{{ $usr->id_usuario }}" data-busqueda="{{ $textoBusqueda }}" {{ old('id_custodio') == $usr->id_usuario ? 'selected' : '' }}>
                                {{ $textoMostrar }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="af-error" data-for="id_custodio"></div>
                <div style="font-size:10px; color:#94a3b8; margin-top:6px; font-weight:600;">
                    Escribe en el campo superior para filtrar al instante por C.I., Nombre o Ítem.
                </div>
            </div>

            {{-- Descripción --}}
            <div class="af-span-2">
                <label class="af-label">Descripción / Características técnicas <span class="af-asterisco visible">*</span></label>
                <textarea name="descripcion" id="descripcion" rows="3" class="af-input" style="resize:none;"></textarea>
                <div class="af-error" data-for="descripcion"></div>
            </div>

            {{-- FOTOGRAFÍAS --}}
            <div class="af-span-2 af-box">
                <div style="font-size:12px; font-weight:800; color:#1e293b; margin-bottom:12px; display:flex; align-items:center; gap:8px;">
                    <span style="font-size:16px;">📷</span>
                    <span>Fotografías del Activo</span>
                    <span id="lbl-fotos-info" style="font-weight:600; color:#64748b; font-size:11px;"></span>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:14px;">
                    {{-- Frontal --}}
                    <div>
                        <label class="af-label" style="font-size:10px;">Foto Frontal <span class="af-asterisco visible" id="req-frontal">*</span></label>
                        <div class="af-dropzone" id="preview-frontal">
                            <span style="font-size:24px; color:#94a3b8;">🖼️</span>
                            <span style="color:#94a3b8; font-size:11px; margin-top:4px; font-weight:600;">Vista frontal</span>
                        </div>
                        <input type="file" name="imagen" id="imagen" accept="image/*" class="af-input" style="padding:6px; font-size:11px;" onchange="previewImage(this, 'preview-frontal')">
                        <div class="af-error" data-for="imagen"></div>
                    </div>

                    {{-- Lateral --}}
                    <div id="bloque-lateral">
                        <label class="af-label" style="font-size:10px;">Foto Lateral <span class="af-asterisco visible" id="req-lateral">*</span></label>
                        <div class="af-dropzone" id="preview-lateral">
                            <span style="font-size:24px; color:#94a3b8;">🖼️</span>
                            <span style="color:#94a3b8; font-size:11px; margin-top:4px; font-weight:600;">Vista lateral</span>
                        </div>
                        <input type="file" name="imagen_lateral" id="imagen_lateral" accept="image/*" class="af-input" style="padding:6px; font-size:11px;" onchange="previewImage(this, 'preview-lateral')">
                        <div class="af-error" data-for="imagen_lateral"></div>
                    </div>

                    {{-- Detalle --}}
                    <div id="bloque-detalle">
                        <label class="af-label" style="font-size:10px;">Foto Detalle <span class="af-asterisco visible" id="req-detalle">*</span></label>
                        <div class="af-dropzone" id="preview-detalle">
                            <span style="font-size:24px; color:#94a3b8;">🏷️</span>
                            <span style="color:#94a3b8; font-size:11px; margin-top:4px; font-weight:600;">Placa / Serial</span>
                        </div>
                        <input type="file" name="imagen_detalle" id="imagen_detalle" accept="image/*" class="af-input" style="padding:6px; font-size:11px;" onchange="previewImage(this, 'preview-detalle')">
                        <div class="af-error" data-for="imagen_detalle"></div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Footer Buttons --}}
        <div style="display:flex; justify-content:flex-end; gap:12px; border-top:1.5px solid #f1f5f9; padding-top:20px; margin-top:24px;">
            <a href="{{ route('activos.index') }}" class="btn-secondary">Cancelar</a>
            <button type="submit" id="btn-guardar" class="btn-primary">💾 Guardar Activo</button>
        </div>
    </form>
</div>

<script>
    // ============================================
    // FILTRAR CATEGORÍAS POR AMBIENTE
    // ============================================
    function filtrarCategoriasPorAmbiente(ambienteId) {
        const selectCat = document.getElementById('id_categoria');
        if (!selectCat) return;
        const opciones = selectCat.querySelectorAll('option');

        selectCat.value = "";
        opciones.forEach(opt => {
            if (opt.value === "") { opt.style.display = "block"; return; }
            const ambOpt = opt.getAttribute('data-ambiente');
            opt.style.display = (!ambienteId || ambOpt === ambienteId) ? "block" : "none";
        });
    }

    // ============================================
    // FILTRAR CUSTODIOS
    // ============================================
    function filtrarCustodios(texto) {
        const filtro = texto.toLowerCase().trim();
        const selectCustodio = document.getElementById('id_custodio');
        if (!selectCustodio) return;
        const opciones = selectCustodio.querySelectorAll('option');

        opciones.forEach(opt => {
            if (opt.value === "") return;
            const busqueda = opt.getAttribute('data-busqueda') || "";
            opt.style.display = busqueda.includes(filtro) ? "block" : "none";
        });
    }

    // ============================================
    // PREVIEW IMÁGENES
    // ============================================
    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        if (!preview) return;
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `<img src="${e.target.result}" alt="Preview" style="width:100%;height:100%;object-fit:cover;border-radius:8px;">`;
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.innerHTML = `<span style="color:#94a3b8;font-size:11px;">Sin imagen</span>`;
        }
    }

    // ============================================
    // LIMPIAR ERROR INLINE
    // ============================================
    function limpiarError(campo) {
        const span = document.querySelector('.af-error[data-for="' + campo.id + '"]');
        if (span) {
            span.classList.remove('visible');
            span.textContent = '';
        }
        campo.classList.remove('af-input-error');
    }

    document.querySelectorAll('#activo-form input, #activo-form select, #activo-form textarea').forEach(el => {
        if (!el.id) return;
        el.addEventListener('input', () => limpiarError(el));
        el.addEventListener('change', () => limpiarError(el));
    });

    // ============================================
    // TIPO DE BIEN — Dinámico
    // ============================================
    function aplicarTipoBien(tipo) {
        const esFijo = tipo === 'ACTIVO_FIJO';

        const aviso = document.getElementById('aviso-serie');
        if (aviso) aviso.style.display = esFijo ? '' : 'block';
        if (aviso && esFijo) aviso.style.display = 'none';

        ['req-marca','req-modelo','req-serie'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.classList.toggle('visible', esFijo);
        });
        ['req-frontal','req-lateral','req-detalle'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.classList.toggle('visible', esFijo);
        });

        const bloqueLateral = document.getElementById('bloque-lateral');
        const bloqueDetalle = document.getElementById('bloque-detalle');
        if (bloqueLateral) bloqueLateral.style.display = esFijo ? 'block' : 'none';
        if (bloqueDetalle) bloqueDetalle.style.display = esFijo ? 'block' : 'none';

        if (!esFijo) {
            ['marca','modelo','numero_serie'].forEach(id => {
                const campo = document.getElementById(id);
                if (campo) limpiarError(campo);
            });
            ['imagen_lateral','imagen_detalle'].forEach(id => {
                const campo = document.getElementById(id);
                if (campo) {
                    campo.value = '';
                    limpiarError(campo);
                    const preview = document.getElementById('preview-' + (id === 'imagen_lateral' ? 'lateral' : 'detalle'));
                    if (preview) preview.innerHTML = '<span style="color:#94a3b8;font-size:11px;">Sin imagen</span>';
                }
            });
        }
    }

    document.getElementById('select-tipo-bien')?.addEventListener('change', function() {
        aplicarTipoBien(this.value);
    });

    (function() {
        aplicarTipoBien(document.getElementById('select-tipo-bien').value);
    })();

    // ============================================
    // VALIDACIÓN AL ENVIAR
    // ============================================
    document.getElementById('activo-form')?.addEventListener('submit', function(e) {
        let hayErrores = false;

        document.querySelectorAll('#activo-form .af-error').forEach(el => {
            el.classList.remove('visible');
            el.textContent = '';
        });
        document.querySelectorAll('#activo-form .af-input-error').forEach(el => {
            el.classList.remove('af-input-error');
        });

        function marcar(campoId, mensaje) {
            const campo = document.getElementById(campoId);
            const span = document.querySelector('.af-error[data-for="' + campoId + '"]');
            if (span) {
                span.textContent = '⚠️ ' + mensaje;
                span.classList.add('visible');
            }
            if (campo) campo.classList.add('af-input-error');
            hayErrores = true;
        }

        const tipo = document.getElementById('select-tipo-bien')?.value || 'ACTIVO_FIJO';
        const esFijo = tipo === 'ACTIVO_FIJO';

        const requeridosBase = [
            { id: 'codigo_activo', msg: 'El código es obligatorio.' },
            { id: 'cantidad', msg: 'La cantidad es obligatoria.' },
            { id: 'nombre', msg: 'El nombre es obligatorio.' },
            { id: 'id_ambiente', msg: 'Selecciona un ambiente.' },
            { id: 'id_categoria', msg: 'Selecciona una categoría.' },
            { id: 'id_fuente', msg: 'Selecciona una fuente de financiamiento.' },
            { id: 'estado_fisico', msg: 'Selecciona un estado físico.' },
            { id: 'id_custodio', msg: 'Selecciona un custodio responsable.' },
            { id: 'fecha_adquisicion', msg: 'La fecha de adquisición es obligatoria.' },
            { id: 'valor_adquisicion', msg: 'El valor es obligatorio.' },
            { id: 'descripcion', msg: 'La descripción es obligatoria.' },
        ];

        requeridosBase.forEach(({ id, msg }) => {
            const campo = document.getElementById(id);
            if (!campo) return;
            if ((campo.value || '').trim() === '') marcar(id, msg);
        });

        if (esFijo) {
            ['marca','modelo','numero_serie'].forEach(id => {
                const campo = document.getElementById(id);
                if (!campo) return;
                if ((campo.value || '').trim() === '') {
                    const nombre = id === 'numero_serie' ? 'N° de serie' : id.charAt(0).toUpperCase() + id.slice(1);
                    marcar(id, 'La ' + nombre + ' es obligatoria para Activo Fijo.');
                }
            });

            const img = document.getElementById('imagen');
            if (!img.files || img.files.length === 0) {
                marcar('imagen', 'La foto frontal es obligatoria para Activo Fijo.');
            }
        }

        if (hayErrores) {
            e.preventDefault();
            const primerError = document.querySelector('#activo-form .af-input-error');
            if (primerError) {
                primerError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                primerError.focus();
            }
            return false;
        }

        const btn = document.getElementById('btn-guardar');
        btn.disabled = true;
        btn.textContent = '⏳ Guardando...';
    });
</script>
@endsection
