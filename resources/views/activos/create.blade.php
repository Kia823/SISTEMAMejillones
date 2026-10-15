@extends('layouts.app')

@section('content')
<div style="max-width: 900px; margin: 0 auto; background: white; border-radius: 18px; padding: 28px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">

    {{-- ENCABEZADO --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h2 style="font-size: 24px; font-weight: 800; color: #0f172a; margin: 0;">
                Nuevo Activo
            </h2>
            <p style="color: #64748b; font-size: 14px; margin: 4px 0 0 0;">
                Completa los datos del activo. Si registras varios, usa el campo "Cantidad".
            </p>
        </div>
        <button type="button" onclick="history.back()"
           style="background: #e2e8f0; color: #334155; padding: 8px 16px; border-radius: 10px; font-weight: 700; border: none; cursor: pointer;">
            ← Volver
        </button>
    </div>

    {{-- ERRORES DEL SERVIDOR --}}
    @if ($errors->any())
        <div style="background:#fee2e2;border:1px solid #fecaca;color:#991b1b;padding:14px 20px;border-radius:12px;margin-bottom:20px;font-size:13px;">
            <strong>⚠️ Corrige los siguientes errores:</strong>
            <ul style="margin:8px 0 0 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('activos.store') }}" enctype="multipart/form-data" id="activo-form">
        @csrf

        @if(request('id_categoria'))
            <input type="hidden" name="id_categoria_origen" value="{{ request('id_categoria') }}">
        @endif

        <style>
            .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
            .form-grid label { display:flex; flex-direction:column; font-size:12px; font-weight:700; color:#334155; gap:4px; }
            .form-grid label.full { grid-column:span 2; }
            .form-grid input, .form-grid select, .form-grid textarea {
                width:100%; padding:9px 12px; border-radius:8px; border:1px solid #cbd5e1; font-size:14px; background:#fff; box-sizing:border-box;
            }
            .form-grid input:focus, .form-grid select:focus, .form-grid textarea:focus {
                outline:none; border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,.1);
            }
            .input-error { border-color:#dc2626 !important; background:#fef2f2 !important; }
            .error-msg { color:#dc2626; font-size:11px; font-weight:600; margin-top:2px; display:none; }
            .error-msg.visible { display:block; }
            .section-title {
                grid-column:span 2; font-size:11px; font-weight:800; color:#2563eb; text-transform:uppercase;
                margin:8px 0 -6px 0; padding-bottom:6px; border-bottom:1px solid #e2e8f0;
            }
            .img-preview {
                width: 100%; height: 130px; border: 2px dashed #cbd5e1; border-radius: 10px;
                display: flex; align-items: center; justify-content: center; background: #f8fafc;
                margin-bottom: 8px; overflow: hidden;
            }
            .img-preview img { width: 100%; height: 100%; object-fit: cover; border-radius: 8px; }
            .img-preview .placeholder { text-align: center; color: #94a3b8; }
            .asterisco { color:#dc2626; margin-left:2px; display:none; }
            .asterisco.visible { display:inline; }
        </style>

        <div class="form-grid">

            <div class="section-title">Identificación</div>

            @php $tipoInicial = old('tipo_bien', 'ACTIVO_FIJO'); @endphp

            <div class="full" style="padding:14px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;">
                <div style="font-size:12px;font-weight:800;color:#334155;margin-bottom:8px;">Clasificación del bien *</div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                    <label id="opcion-fijo" style="display:flex;gap:9px;align-items:flex-start;cursor:pointer;padding:11px;background:#fff;border:2px solid #cbd5e1;border-radius:10px;">
                        <input type="radio" name="tipo_bien" value="ACTIVO_FIJO" data-radio-tipo @checked($tipoInicial === 'ACTIVO_FIJO') style="margin-top:2px;">
                        <span>
                            <strong style="font-size:13px;color:#1d4ed8;">Activo fijo</strong>
                            <small style="display:block;color:#64748b;font-size:11px;">Se capitaliza. Marca, modelo, serie y 3 fotos obligatorias.</small>
                        </span>
                    </label>
                    <label id="opcion-no-activo" style="display:flex;gap:9px;align-items:flex-start;cursor:pointer;padding:11px;background:#fff;border:2px solid #cbd5e1;border-radius:10px;">
                        <input type="radio" name="tipo_bien" value="NO_ACTIVO" data-radio-tipo @checked($tipoInicial === 'NO_ACTIVO') style="margin-top:2px;">
                        <span>
                            <strong style="font-size:13px;color:#475569;">No activo</strong>
                            <small style="display:block;color:#64748b;font-size:11px;">No se capitaliza. Solo 1 foto, marca/modelo/serie opcionales.</small>
                        </span>
                    </label>
                </div>
                <small id="aviso-serie" style="display:none;margin-top:9px;color:#b45309;font-size:11px;">
                    En los no activos, la marca, el modelo y el número de serie no son obligatorios.
                </small>
            </div>

            <label>Código del activo *
                <input name="codigo_activo" id="codigo_activo" maxlength="50"
                       style="text-transform:uppercase;"
                       value="{{ old('codigo_activo', $codigoSugerido ?? '') }}">
                <span class="error-msg" data-for="codigo_activo"></span>
            </label>

            <label>Cantidad *
                <input name="cantidad" id="cantidad" type="number" min="1" max="100" value="{{ old('cantidad', 1) }}">
                <span class="error-msg" data-for="cantidad"></span>
            </label>

            <label class="full">Nombre del activo *
                <input name="nombre" id="nombre" maxlength="150" placeholder="Ej: Laptop Dell Latitude 5430" value="{{ old('nombre') }}">
                <span class="error-msg" data-for="nombre"></span>
            </label>

            <div class="section-title">Clasificación</div>

            {{-- AMBIENTE --}}
            <label>Ambiente *
                <select name="id_ambiente" id="id_ambiente" onchange="filtrarCategoriasPorAmbiente(this.value)">
                    <option value="">— Seleccione ambiente —</option>
                    @foreach ($ambientes as $item)
                        <option value="{{ $item->id_ambiente }}"
                            {{ old('id_ambiente', $idAmbientePreseleccionado ?? '') == $item->id_ambiente ? 'selected' : '' }}>
                            {{ $item->nombre }}
                        </option>
                    @endforeach
                </select>
                <span class="error-msg" data-for="id_ambiente"></span>
            </label>

            {{-- CATEGORÍA --}}
            <label>Categoría *
                <select name="id_categoria" id="id_categoria">
                    <option value="">— Seleccione categoría del ambiente —</option>
                    @foreach ($categorias as $item)
                        <option value="{{ $item->id_categoria }}" data-ambiente="{{ $item->id_ambiente }}"
                            {{ (old('id_categoria', request('id_categoria')) == $item->id_categoria) ? 'selected' : '' }}>
                            {{ $item->nombre }}
                        </option>
                    @endforeach
                </select>
                <span class="error-msg" data-for="id_categoria"></span>
            </label>

            <label>Fuente de financiamiento *
                <select name="id_fuente" id="id_fuente">
                    <option value="">— Seleccione fuente —</option>
                    @foreach ($fuentes as $item)
                        <option value="{{ $item->id_fuente }}" {{ old('id_fuente') == $item->id_fuente ? 'selected' : '' }}>
                            {{ $item->codigo ? $item->codigo . ' - ' : '' }}{{$item->nombre }}
                        </option>
                    @endforeach
                </select>
                <span class="error-msg" data-for="id_fuente"></span>
            </label>

            <label>Estado físico *
                <select name="estado_fisico" id="estado_fisico">
                    <option value="">— Seleccione estado —</option>
                    <option value="B"  {{ old('estado_fisico') == 'B'  ? 'selected' : '' }}>Bueno</option>
                    <option value="R"  {{ old('estado_fisico') == 'R'  ? 'selected' : '' }}>Regular</option>
                    <option value="M"  {{ old('estado_fisico') == 'M'  ? 'selected' : '' }}>Malo</option>
                    <option value="FF" {{ old('estado_fisico') == 'FF' ? 'selected' : '' }}>Fuera de funcionamiento</option>
                </select>
                <span class="error-msg" data-for="estado_fisico"></span>
            </label>

             {{-- CUSTODIOS CON BUSCADOR --}}
             <label class="full">Custodios *
                 <input type="text" id="buscador-custodio" placeholder="🔍 Buscar custodio por Nombre, C.I. o N° de Ítem..."
                        style="margin-bottom: 6px; background: #f8fafc; font-size: 13px;"
                        onkeyup="filtrarCustodios(this.value)">

                 <select name="id_custodios[]" id="id_custodios" multiple size="5" style="height: 110px; width: 100%;">
                     <option value="">— Seleccione custodios de la lista —</option>
                     @foreach ($usuarios as $usr)
                         @php
                             $itemActivo = $usr->items->first();
                             $nroItem = $itemActivo ? 'Ítem #' . $itemActivo->numero_item : 'Sin ítem';
                         @endphp
                         <option value="{{ $usr->id_usuario }}"
                                 data-busqueda="{{ strtolower($usr->nombre_completo . ' ' . $usr->ci . ' ' . ($itemActivo?->numero_item ?? '')) }}"
                                 {{ is_array(old('id_custodios')) && in_array($usr->id_usuario, old('id_custodios')) ? 'selected' : '' }}>
                             {{ $usr->nombre_completo }} — C.I.: {{ $usr->ci }} ({{$nroItem }})
                         </option>
                     @endforeach
                 </select>
                 <span class="error-msg" data-for="id_custodios"></span>
                 <small style="color: #64748b; font-size: 11px;">Escribe en el campo superior para filtrar al instante por C.I., Nombre o Ítem.</small>
             </label>

            <div class="section-title">Especificaciones técnicas</div>

            <label>Marca <span class="asterisco" id="req-marca">*</span>
                <input name="marca" id="marca" maxlength="80" value="{{ old('marca') }}">
                <span class="error-msg" data-for="marca"></span>
            </label>
            <label>Modelo <span class="asterisco" id="req-modelo">*</span>
                <input name="modelo" id="modelo" maxlength="80" value="{{ old('modelo') }}">
                <span class="error-msg" data-for="modelo"></span>
            </label>
            <label>N° de serie <span class="asterisco" id="req-serie">*</span>
                <input name="numero_serie" id="numero_serie" maxlength="80" value="{{ old('numero_serie') }}">
                <span class="error-msg" data-for="numero_serie"></span>
            </label>
            <label>Fecha de adquisición *
                <input name="fecha_adquisicion" id="fecha_adquisicion" type="date" value="{{ old('fecha_adquisicion', date('Y-m-d')) }}">
                <span class="error-msg" data-for="fecha_adquisicion"></span>
            </label>
            <label>Valor de adquisición (Bs) *
                <input name="valor_adquisicion" id="valor_adquisicion" type="number" step="0.01" min="0" value="{{ old('valor_adquisicion', 0) }}">
                <span class="error-msg" data-for="valor_adquisicion"></span>
            </label>

            <div class="section-title">📷 Fotografías del activo</div>

            {{-- FOTO FRONTAL --}}
            <label id="bloque-frontal">Foto Frontal <span class="asterisco" id="req-frontal">*</span>
                <div id="preview-frontal" class="img-preview"><div class="placeholder">📷 Vista frontal</div></div>
                <input type="file" name="imagen" id="imagen" accept="image/*" onchange="previewImage(this, 'preview-frontal')">
                <span class="error-msg" data-for="imagen"></span>
            </label>

            {{-- FOTO LATERAL --}}
            <label id="bloque-lateral">Foto Lateral <span class="asterisco" id="req-lateral">*</span>
                <div id="preview-lateral" class="img-preview"><div class="placeholder">📷 Vista lateral</div></div>
                <input type="file" name="imagen_lateral" id="imagen_lateral" accept="image/*" onchange="previewImage(this, 'preview-lateral')">
                <span class="error-msg" data-for="imagen_lateral"></span>
            </label>

            {{-- FOTO DETALLE --}}
            <label id="bloque-detalle" class="full">Foto de Detalle <span class="asterisco" id="req-detalle">*</span>
                <div id="preview-detalle" class="img-preview"><div class="placeholder">📷 Placa / Serial</div></div>
                <input type="file" name="imagen_detalle" id="imagen_detalle" accept="image/*" onchange="previewImage(this, 'preview-detalle')">
                <span class="error-msg" data-for="imagen_detalle"></span>
            </label>

            <div class="section-title">Detalles adicionales</div>
            <label class="full">Descripción / Características técnicas *
                <textarea name="descripcion" id="descripcion" rows="3">{{ old('descripcion') }}</textarea>
                <span class="error-msg" data-for="descripcion"></span>
            </label>
        </div>

        <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:24px;border-top:1px solid #e2e8f0;padding-top:18px;">
            <button type="button" onclick="history.back()" style="background:#e2e8f0;color:#475569;border:none;padding:11px 22px;border-radius:8px;font-weight:700;cursor:pointer;">Cancelar</button>
            <button type="submit" id="btn-guardar" style="background:#2563eb;color:white;border:none;padding:11px 22px;border-radius:8px;font-weight:700;cursor:pointer;">💾 Guardar Activo</button>
        </div>
    </form>
</div>

<script>
    // ============================================
    // FILTRAR CATEGORÍAS POR AMBIENTE
    // ============================================
    function filtrarCategoriasPorAmbiente(ambienteId) {
        const selectCat = document.getElementById('id_categoria');
        const opciones = selectCat.querySelectorAll('option');

        selectCat.value = "";
        opciones.forEach(opt => {
            if (opt.value === "") { opt.style.display = "block"; return; }
            const ambOpt = opt.getAttribute('data-ambiente');
            opt.style.display = (!ambienteId || ambOpt === ambienteId) ? "block" : "none";
        });

        // Auto-seleccionar custodios cuando cambia el ambiente
        autoSeleccionarCustodiosPorAmbiente(ambienteId);
    }

    // ============================================
    // FILTRAR CUSTODIOS
    // ============================================
    function filtrarCustodios(texto) {
        const filtro = texto.toLowerCase().trim();
        const selectCustodio = document.getElementById('id_custodios');
        const opciones = selectCustodio.querySelectorAll('option');

        opciones.forEach(opt => {
            if (opt.value === "") return;
            const busqueda = opt.getAttribute('data-busqueda') || "";
            opt.style.display = busqueda.includes(filtro) ? "block" : "none";
        });
    }

    // ============================================
    // AUTO-SELECCIONAR CUSTODIOS CUANDO CAMBIA EL AMBIENTE
    // ============================================
    function autoSeleccionarCustodiosPorAmbiente(ambienteId) {
        if (!ambienteId) {
            // Limpiar selección si no hay ambiente
            document.getElementById('id_custodios').value = [];
            return;
        }

        // Hacer petición fetch para obtener custodios del ambiente
        fetch('/activos/custodios-por-ambiente/' + ambienteId)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                const select = document.getElementById('id_custodios');
                if (data && data.length > 0) {
                    const ids = data.map(item => String(item.id));
                    select.value = ids;
                } else {
                    select.value = [];
                }
            })
            .catch(error => {
                console.error('Error fetching custodios por ambiente:', error);
                // En caso de error, limpiar selección
                document.getElementById('id_custodios').value = [];
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
                preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // ============================================
    // LIMPIAR ERROR INLINE
    // ============================================
    function limpiarError(campo) {
        const span = document.querySelector('.error-msg[data-for="' + campo.id + '"]');
        if (span) {
            span.classList.remove('visible');
            span.textContent = '';
        }
        campo.classList.remove('input-error');
    }

    // Al escribir/cambiar cualquier campo, limpiar su error
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

        // Tarjetas visuales
        const cardFijo = document.getElementById('opcion-fijo');
        const cardNoActivo = document.getElementById('opcion-no-activo');
        if (cardFijo) {
            cardFijo.style.borderColor = esFijo ? '#2563eb' : '#cbd5e1';
            cardFijo.style.background = esFijo ? '#eff6ff' : '#fff';
        }
        if (cardNoActivo) {
            cardNoActivo.style.borderColor = !esFijo ? '#f59e0b' : '#cbd5e1';
            cardNoActivo.style.background = !esFijo ? '#fffbeb' : '#fff';
        }

        // Aviso
        const aviso = document.getElementById('aviso-serie');
        if (aviso) aviso.style.display = esFijo ? 'none' : 'block';

        // Asteriscos marca/modelo/serie
        ['req-marca','req-modelo','req-serie'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.classList.toggle('visible', esFijo);
        });
        const reqFrontal = document.getElementById('req-frontal');
        if (reqFrontal) reqFrontal.classList.toggle('visible', esFijo);

        // Bloque lateral y detalle (solo ACTIVO_FIJO)
        const bloqueLateral = document.getElementById('bloque-lateral');
        const bloqueDetalle = document.getElementById('bloque-detalle');
        if (bloqueLateral) bloqueLateral.style.display = esFijo ? 'flex' : 'none';
        if (bloqueDetalle) bloqueDetalle.style.display = esFijo ? 'flex' : 'none';

        // Asteriscos lateral y detalle
        ['req-lateral','req-detalle'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.classList.toggle('visible', esFijo);
        });

        // Si pasa a NO_ACTIVO, limpiar errores de marca/modelo/serie y quitar valores de archivos
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
                    if (preview) preview.innerHTML = '<div class="placeholder">📷 ' + (id === 'imagen_lateral' ? 'Vista lateral' : 'Placa / Serial') + '</div>';
                }
            });
        }
    }

    document.querySelectorAll('[data-radio-tipo]').forEach(r => {
        r.addEventListener('change', () => aplicarTipoBien(r.value));
    });

     // Aplicar al cargar
     (function() {
         const checked = document.querySelector('[data-radio-tipo]:checked');
         aplicarTipoBien(checked ? checked.value : 'ACTIVO_FIJO');
     })();

      // Aplicar filtros iniciales de ambiente y custodios al cargar
      (function() {
          const ambienteSelect = document.getElementById('id_ambiente');
          if (ambienteSelect.value) {
              filtrarCategoriasPorAmbiente(ambienteSelect.value);
              autoSeleccionarCustodiosPorAmbiente(ambienteSelect.value);
          }
      })();

    // ============================================
    // VALIDACIÓN AL ENVIAR
    // ============================================
    document.getElementById('activo-form')?.addEventListener('submit', function(e) {
        let hayErrores = false;

        // Limpiar errores previos
        document.querySelectorAll('#activo-form .error-msg').forEach(el => {
            el.classList.remove('visible');
            el.textContent = '';
        });
        document.querySelectorAll('#activo-form .input-error').forEach(el => {
            el.classList.remove('input-error');
        });

        function marcar(campoId, mensaje) {
            const campo = document.getElementById(campoId);
            const span = document.querySelector('.error-msg[data-for="' + campoId + '"]');
            if (span) {
                span.textContent = '⚠️ ' + mensaje;
                span.classList.add('visible');
            }
            if (campo) campo.classList.add('input-error');
            hayErrores = true;
        }

        const tipo = document.querySelector('[data-radio-tipo]:checked')?.value || 'ACTIVO_FIJO';
        const esFijo = tipo === 'ACTIVO_FIJO';

         // Campos base obligatorios
         const requeridosBase = [
             { id: 'codigo_activo', msg: 'El código es obligatorio.' },
             { id: 'cantidad', msg: 'La cantidad es obligatoria.' },
             { id: 'nombre', msg: 'El nombre es obligatorio.' },
             { id: 'id_ambiente', msg: 'Selecciona un ambiente.' },
             { id: 'id_categoria', msg: 'Selecciona una categoría.' },
             { id: 'id_fuente', msg: 'Selecciona una fuente de financiamiento.' },
             { id: 'id_custodios', msg: 'Seleccione al menos un custodio.' },
             { id: 'fecha_adquisicion', msg: 'La fecha de adquisición es obligatoria.' },
             { id: 'valor_adquisicion', msg: 'El valor es obligatorio.' },
             { id: 'descripcion', msg: 'La descripción es obligatoria.' },
         ];

        requeridosBase.forEach(({ id, msg }) => {
            const campo = document.getElementById(id);
            if (!campo) return;
            if ((campo.value || '').trim() === '') marcar(id, msg);
        });

        // Si es ACTIVO_FIJO
        if (esFijo) {
            ['marca','modelo','numero_serie'].forEach(id => {
                const campo = document.getElementById(id);
                if (!campo) return;
                if ((campo.value || '').trim() === '') {
                    const nombre = id === 'numero_serie' ? 'N° de serie' : id.charAt(0).toUpperCase() + id.slice(1);
                    marcar(id, 'La ' + nombre + ' es obligatoria para Activo Fijo.');
                }
            });

            // Foto frontal obligatoria
            const img = document.getElementById('imagen');
            if (!img.files || img.files.length === 0) {
                marcar('imagen', 'La foto frontal es obligatoria para Activo Fijo.');
            }
        }

        if (hayErrores) {
            e.preventDefault();
            const primerError = document.querySelector('#activo-form .input-error');
            if (primerError) {
                primerError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                primerError.focus();
            }
            return false;
        }

        // Deshabilitar botón para evitar doble envío
        const btn = document.getElementById('btn-guardar');
        btn.disabled = true;
        btn.textContent = '⏳ Guardando...';
    });
</script>
@endsection
