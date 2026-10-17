@extends('layouts.app')

@section('title', 'Nueva Acta de Baja')

@section('content')
<div class="container-fluid px-4 py-6">
    <div style="max-width: 950px; margin: 0 auto;">

        <a href="{{ route('actas.baja.index') }}"
           style="display: inline-flex; align-items: center; gap: 6px; color: #7c2d12; font-weight: 700; font-size: 13px; text-decoration: none; background: #fff7ed; padding: 8px 14px; border-radius: 10px; border: 1px solid #ffedd5; margin-bottom: 16px;">
            ← Volver a Actas de Baja
        </a>

        <div style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; padding: 30px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);">
            <h1 style="font-size: 22px; font-weight: 800; color: #0f172a; margin: 0 0 6px 0;">Solicitar Baja de Activo</h1>
            <p style="font-size: 13px; color: #64748b; margin: 0 0 24px 0;">
                Complete los datos del acta: seleccione el activo, marque la causal, agregue evidencia fotográfica y los datos del responsable.
            </p>

            <form id="formBaja" method="POST" action="{{ route('actas.baja.store') }}" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 24px;" novalidate>
                @csrf
                <input type="hidden" name="ambiente_escaneo_id" value="">

                {{-- 1. SELECCIÓN DE ACTIVO --}}
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 20px;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                        <span style="font-size: 14px; font-weight: 800; color: #1e293b;">1.</span>
                        <label style="font-size: 12px; font-weight: 800; color: #1e293b; margin-bottom: 0; text-transform: uppercase;">Seleccione el activo a dar de baja *</label>
                    </div>

                    <div style="margin-bottom: 14px;">
                        <input type="text" name="q" value="{{ old('q', request('q')) }}"
                            style="width: 100%; padding: 10px 14px; background: white; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; outline: none;"
                            onkeyup="this.form.submit()" form="filtro-baja" placeholder="🔍 Filtrar por código o nombre (presiona Enter)">
                    </div>

                    <div style="background: white; border-radius: 12px; border: 1px solid #cbd5e1; overflow: hidden;">
                        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
                            <thead style="background: #f1f5f9; color: #475569; text-transform: uppercase; font-size: 10px; letter-spacing: 0.5px; border-bottom: 1px solid #cbd5e1;">
                                <tr>
                                    <th style="padding: 12px; text-align: center; width: 50px;">✔</th>
                                    <th style="padding: 12px;">Código</th>
                                    <th style="padding: 12px;">Nombre</th>
                                    <th style="padding: 12px;">Categoría</th>
                                    <th style="padding: 12px;">Ubicación</th>
                                    <th style="padding: 12px;">Custodio</th>
                                </tr>
                            </thead>
                            <tbody style="divide-y: #f1f5f9;">
                                @forelse($activos as $a)
                                    <tr style="border-bottom: 1px solid #f1f5f9;">
                                        <td style="padding: 12px; text-align: center;">
                                            <input type="checkbox" name="activo_ids[]" value="{{ $a->id_activo ?? $a->id }}"
                                                {{ (is_array(old('activo_ids')) && in_array($a->id_activo ?? $a->id, old('activo_ids'))) ? 'checked' : '' }}
                                                style="width: 16px; height: 16px; accent-color: #7c2d12; cursor: pointer;" class="check-activo">
                                        </td>
                                        <td style="padding: 12px; font-family: monospace; font-weight: 700; font-size: 12px;">{{ $a->codigo_activo }}</td>
                                        <td style="padding: 12px; font-weight: 600; color: #334155;">{{ $a->nombre }}</td>
                                        <td style="padding: 12px; font-size: 12px; color: #64748b;">{{ optional($a->categoria)->nombre }}</td>
                                        <td style="padding: 12px; font-size: 12px; color: #64748b;">{{ optional($a->ambiente)->nombre }}</td>
                                        <td style="padding: 12px; font-size: 12px; color: #64748b;">{{ optional($a->custodioActual)->nombre_completo ?? optional($a->custodio)->nombre_completo }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" style="padding: 24px; text-align: center; color: #94a3b8; font-weight: 600;">No hay activos disponibles en su ámbito.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <span id="errorActivos" style="font-size: 11px; color: #dc2626; font-weight: 700; margin-top: 6px; display: none;">⚠️ Debe seleccionar al menos un activo para continuar.</span>
                </div>

                {{-- 2. CAUSAL DE BAJA --}}
                <div style="background: #fefce8; border: 1px solid #d97706; border-radius: 14px; padding: 20px;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                        <span style="font-size: 14px; font-weight: 800; color: #92400e;">2.</span>
                        <label style="font-size: 12px; font-weight: 800; color: #92400e; margin-bottom: 0; text-transform: uppercase;">Marque la causal de la baja *</label>
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px;">
                        <label style="display: flex; align-items: flex-start; gap: 8px; font-size: 13px; color: #334155; cursor: pointer; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px; background: white; transition: all 0.2s;">
                            <input type="radio" name="causal_baja" value="a) Disposicion definitiva de bienes"
                                {{ old('causal_baja') === 'a) Disposicion definitiva de bienes' ? 'checked' : '' }}
                                style="width: 16px; height: 16px; accent-color: #7c2d12; margin-top: 2px; cursor: pointer;" class="check-causal">
                            <span style="font-weight: 700; color: #7c2d12;">a)</span> Disposición definitiva de bienes
                        </label>
                        <label style="display: flex; align-items: flex-start; gap: 8px; font-size: 13px; color: #334155; cursor: pointer; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px; background: white; transition: all 0.2s;">
                            <input type="radio" name="causal_baja" value="b) Hurto, robo o pérdida fortuita"
                                {{ old('causal_baja') === 'b) Hurto, robo o pérdida fortuita' ? 'checked' : '' }}
                                style="width: 16px; height: 16px; accent-color: #7c2d12; margin-top: 2px; cursor: pointer;" class="check-causal">
                            <span style="font-weight: 700; color: #7c2d12;">b)</span> Hurto, robo o pérdida fortuita
                        </label>
                        <label style="display: flex; align-items: flex-start; gap: 8px; font-size: 13px; color: #334155; cursor: pointer; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px; background: white; transition: all 0.2s;">
                            <input type="radio" name="causal_baja" value="c) Inutilización"
                                {{ old('causal_baja') === 'c) Inutilización' ? 'checked' : '' }}
                                style="width: 16px; height: 16px; accent-color: #7c2d12; margin-top: 2px; cursor: pointer;" class="check-causal">
                            <span style="font-weight: 700; color: #7c2d12;">c)</span> Inutilización
                        </label>
                        <label style="display: flex; align-items: flex-start; gap: 8px; font-size: 13px; color: #334155; cursor: pointer; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px; background: white; transition: all 0.2s;">
                            <input type="radio" name="causal_baja" value="d) Obsolescencia"
                                {{ old('causal_baja') === 'd) Obsolescencia' ? 'checked' : '' }}
                                style="width: 16px; height: 16px; accent-color: #7c2d12; margin-top: 2px; cursor: pointer;" class="check-causal">
                            <span style="font-weight: 700; color: #7c2d12;">d)</span> Obsolescencia
                        </label>
                    </div>
                    <span id="errorCausal" style="font-size: 11px; color: #dc2626; font-weight: 700; margin-top: 6px; display: none;">⚠️ Debe seleccionar una causal de baja.</span>
                </div>

                {{-- 3. MOTIVO Y OBSERVACIONES --}}
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 800; color: #1e293b; margin-bottom: 6px; text-transform: uppercase;">Motivo de la baja *</label>
                        <textarea id="inputMotivo" name="motivo_baja" rows="3" maxlength="500"
                            placeholder="Detalle el motivo institucional de la baja..."
                            style="width: 100%; padding: 10px 14px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; outline: none; resize: none;">{{ old('motivo_baja') }}</textarea>
                        <span id="errorMotivo" style="font-size: 11px; color: #dc2626; font-weight: 700; margin-top: 4px; display: none;">⚠️ Debe detallar el motivo de la baja institucional.</span>
                        @error('motivo_baja') <p style="font-size: 11px; color: #dc2626; margin-top: 4px;">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 800; color: #1e293b; margin-bottom: 6px; text-transform: uppercase;">Observaciones adicionales</label>
                        <textarea name="observaciones" rows="3"
                            placeholder="Observaciones adicionales (opcional)..."
                            style="width: 100%; padding: 10px 14px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; outline: none; resize: none;">{{ old('observaciones') }}</textarea>
                    </div>
                </div>

                {{-- 3. FOTOS DE EVIDENCIA (hasta 3) --}}
                <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 14px; padding: 20px;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                        <span style="font-size: 14px; font-weight: 800; color: #065f46;">3.</span>
                        <label style="font-size: 12px; font-weight: 800; color: #065f46; margin-bottom: 0; text-transform: uppercase;">Fotografías de evidencia del activo (hasta 3)</label>
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 10px;">
                        @for($i = 1; $i <= 3; $i++)
                            <div style="border: 1px dashed #cbd5e1; border-radius: 10px; padding: 12px; text-align: center; background: white;">
                                <label style="cursor: pointer; display: flex; flex-direction: column; align-items: center; gap: 6px; font-size: 12px; color: #64748b; font-weight: 600; width: 100%;">
                                    <span style="font-size: 20px;">📷</span>
                                    <span>Foto {{ $i }}</span>
                                    <input type="file" name="foto_evidencia_{{ $i }}" accept="image/*"
                                        style="display: none;"
                                        onchange="previewFoto({{ $i }}, this)">
                                </label>
                                <img id="previewFoto{{ $i }}" src="" style="max-width: 100%; max-height: 120px; border-radius: 6px; border: 1px solid #e2e8f0; object-fit: cover; margin-top: 8px; display: none;">
                            </div>
                        @endfor
                    </div>
                    <p style="font-size: 11px; color: #64748b; margin-top: 6px;">Formatos aceptados: JPG, PNG. Máximo 5MB cada una.</p>
                </div>

                {{-- 4. DATOS DEL RESPONSABLE --}}
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 20px;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                        <span style="font-size: 14px; font-weight: 800; color: #1e293b;">4.</span>
                        <label style="font-size: 12px; font-weight: 800; color: #1e293b; margin-bottom: 0; text-transform: uppercase;">Datos del responsable del bien</label>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                            <label style="display: block; font-size: 11px; font-weight: 800; color: #334155; margin-bottom: 6px; text-transform: uppercase;">Nombre y apellidos *</label>
                            <input type="text" name="responsable_nombre" value="{{ old('responsable_nombre') }}"
                                placeholder="Nombre completo del responsable"
                                style="width: 100%; padding: 10px 14px; background: white; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; outline: none;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 11px; font-weight: 800; color: #334155; margin-bottom: 6px; text-transform: uppercase;">C.I. *</label>
                            <input type="text" name="responsable_ci" value="{{ old('responsable_ci') }}"
                                placeholder="Carné de Identidad"
                                style="width: 100%; padding: 10px 14px; background: white; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; outline: none;">
                        </div>
                    </div>
                </div>

                {{-- SUSTITUCIÓN --}}
                <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 18px;">
                    <label style="display: flex; align-items: center; gap: 10px; font-size: 13px; font-weight: 800; color: #065f46; cursor: pointer;">
                        <input type="checkbox" id="chkSustitucion" name="es_sustitucion" value="1"
                               style="width: 17px; height: 17px; accent-color: #16a34a; cursor: pointer;">
                        🔁 Es una sustitución: entra un equipo nuevo del almacén
                    </label>

                    <p style="font-size: 12px; color: #047857; margin: 8px 0 0 0; line-height: 1.5;">
                        Al aprobarse, el equipo marcado queda de baja y se guarda en el almacén;
                        el nuevo entra a trabajar en el mismo ambiente y con el mismo custodio.
                    </p>

                    <div id="panelSustitucion" style="display: none; margin-top: 16px;">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                            <div>
                                <label style="display: block; font-size: 11px; font-weight: 800; color: #334155; margin-bottom: 6px; text-transform: uppercase;">Código del equipo nuevo *</label>
                                <input type="text" name="sustituto_codigo" value="{{ old('sustituto_codigo') }}"
                                       style="width: 100%; padding: 10px 14px; background: white; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; outline: none;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 11px; font-weight: 800; color: #334155; margin-bottom: 6px; text-transform: uppercase;">Nombre del equipo nuevo *</label>
                                <input type="text" name="sustituto_nombre" value="{{ old('sustituto_nombre') }}"
                                       style="width: 100%; padding: 10px 14px; background: white; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; outline: none;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 11px; font-weight: 800; color: #334155; margin-bottom: 6px; text-transform: uppercase;">Marca</label>
                                <input type="text" name="sustituto_marca" value="{{ old('sustituto_marca') }}"
                                       style="width: 100%; padding: 10px 14px; background: white; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; outline: none;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 11px; font-weight: 800; color: #334155; margin-bottom: 6px; text-transform: uppercase;">Modelo</label>
                                <input type="text" name="sustituto_modelo" value="{{ old('sustituto_modelo') }}"
                                       style="width: 100%; padding: 10px 14px; background: white; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; outline: none;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 11px; font-weight: 800; color: #334155; margin-bottom: 6px; text-transform: uppercase;">Número de serie</label>
                                <input type="text" name="sustituto_serie" value="{{ old('sustituto_serie') }}"
                                       style="width: 100%; padding: 10px 14px; background: white; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; outline: none;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 11px; font-weight: 800; color: #334155; margin-bottom: 6px; text-transform: uppercase;">Valor (Bs)</label>
                                <input type="number" step="0.01" min="0" name="sustituto_valor" value="{{ old('sustituto_valor') }}"
                                       style="width: 100%; padding: 10px 14px; background: white; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; outline: none;">
                            </div>
                        </div>

                        <div style="margin-top: 16px;">
                            <label style="display: block; font-size: 11px; font-weight: 800; color: #334155; margin-bottom: 6px; text-transform: uppercase;">¿A cuál de los activos marcados sustituye?</label>
                            <select name="sustituto_de" style="width: 100%; padding: 10px 14px; background: white; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; outline: none;">
                                <option value="">— Elegir de la lista —</option>
                                @foreach($activos as $a)
                                    <option value="{{ $a->id_activo }}" {{ (int) old('sustituto_de') === (int) $a->id_activo ? 'selected' : '' }}>
                                        {{ $a->codigo_activo }} — {{ $a->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- BOTONES DE ACCIÓN --}}
                <div style="display: flex; justify-content: flex-end; gap: 10px; padding-top: 16px; border-top: 1px solid #f1f5f9;">
                    <a href="{{ route('actas.baja.index') }}"
                       style="padding: 10px 18px; background: #e2e8f0; color: #334155; border-radius: 10px; font-size: 13px; font-weight: 700; text-decoration: none;">
                       Cancelar
                    </a>
                    <button type="button" onclick="validarFormulario()"
                            style="padding: 10px 20px; background: #7c2d12; color: white; border: none; border-radius: 10px; font-size: 13px; font-weight: 700; cursor: pointer; box-shadow: 0 2px 4px rgba(124, 45, 18, 0.2);">
                        Solicitar Baja
                    </button>
                </div>
            </form>

            <form id="filtro-baja" method="GET" action="{{ route('actas.baja.create') }}"></form>
        </div>
    </div>
</div>

<script>
    (function () {
        const chk = document.getElementById('chkSustitucion');
        const panel = document.getElementById('panelSustitucion');

        if (!chk || !panel) return;

        function alternar() {
            panel.style.display = chk.checked ? 'block' : 'none';
        }

        chk.addEventListener('change', alternar);
        if ({{ old('es_sustitucion') ? 'true' : 'false' }}) {
            chk.checked = true;
        }
        alternar();
    })();

    // Vista previa de las fotos
    for (let i = 1; i <= 3; i++) {
        const fotoInput = document.querySelector('input[name="foto_evidencia_' + i + '"]');
        const imgPreview = document.getElementById('previewFoto' + i);

        if (fotoInput && imgPreview) {
            fotoInput.addEventListener('change', function (e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (evt) {
                        imgPreview.src = evt.target.result;
                        imgPreview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    }

    function validarFormulario() {
        let isValid = true;

        // Validar que al menos un checkbox esté marcado
        const checkboxes = document.querySelectorAll('.check-activo:checked');
        const errorActivos = document.getElementById('errorActivos');
        if (checkboxes.length === 0) {
            errorActivos.style.display = 'block';
            isValid = false;
        } else {
            errorActivos.style.display = 'none';
        }

        // Validar causal
        const causalSeleccionada = document.querySelector('input[name="causal_baja"]:checked');
        const errorCausal = document.getElementById('errorCausal');
        if (!causalSeleccionada) {
            errorCausal.style.display = 'block';
            isValid = false;
        } else {
            errorCausal.style.display = 'none';
        }

        // Validar Motivo
        const motivoInput = document.getElementById('inputMotivo');
        const errorMotivo = document.getElementById('errorMotivo');
        if (!motivoInput.value.trim()) {
            errorMotivo.style.display = 'block';
            motivoInput.style.borderColor = '#dc2626';
            isValid = false;
        } else {
            errorMotivo.style.display = 'none';
            motivoInput.style.borderColor = '#cbd5e1';
        }

        // Validar Responsable
        const nombreResp = document.querySelector('input[name="responsable_nombre"]');
        const ciResp = document.querySelector('input[name="responsable_ci"]');
        if (!nombreResp.value.trim()) {
            nombreResp.style.borderColor = '#dc2626';
            isValid = false;
        } else {
            nombreResp.style.borderColor = '#cbd5e1';
        }
        if (!ciResp.value.trim()) {
            ciResp.style.borderColor = '#dc2626';
            isValid = false;
        } else {
            ciResp.style.borderColor = '#cbd5e1';
        }

        if (isValid) {
            document.getElementById('formBaja').submit();
        }
    }
</script>
@endsection
