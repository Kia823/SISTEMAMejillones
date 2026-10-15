@extends('layouts.app')

@section('title', 'Registrar Nueva Transferencia')

@section('content')
<div class="container-fluid px-4 py-4">
    <div style="background:white;border-radius:20px;padding:30px;width:100%;max-width:800px;margin:0 auto;box-shadow:0 25px 50px -12px rgba(0,0,0,.15);">

        <h2 style="font-size:20px;font-weight:800;color:#0f172a;margin-bottom:6px;">Registrar Nueva Transferencia</h2>
        <p style="font-size:13px;color:#64748b;margin-bottom:20px;">Selecciona el activo, el ambiente de destino y el nuevo custodio responsable.</p>

        <form id="formTransferencia" method="POST" action="{{ route('actas.transferencia.store') }}" enctype="multipart/form-data">
            @csrf

            <div style="display:flex;flex-direction:column;gap:16px;">

                {{-- ========================================== --}}
                {{-- 1. ACTIVO A TRANSFERIR (Buscador por Código y Nombre) --}}
                {{-- ========================================== --}}
                <div>
                    <label style="font-size:12px;font-weight:700;display:block;margin-bottom:4px;color:#334155;">Activo a Transferir *</label>
                    <input type="text" id="buscador-activo-transferencia" placeholder="🔍 Buscar activo por código o nombre..."
                           style="width:100%;padding:9px 12px;border-radius:8px 8px 0 0;border:1px solid #cbd5e1;font-size:13px;box-sizing:border-box;background:#f8fafc;"
                           onkeyup="filtrarActivosTransferencia(this.value)">
                    <select name="id_activo" id="select-activo-transferencia" size="5"
                            style="width:100%;padding:8px;border-radius:0 0 8px 8px;border:1px solid #cbd5e1;border-top:none;font-size:13px;background:white;box-sizing:border-box;">
                        @foreach($activosDisponibles ?? [] as $a)
                            @php
                                $textoActivo = strtolower($a->codigo_activo . ' ' . $a->nombre . ' ' . ($a->marca ?? ''));
                            @endphp
                            <option value="{{ $a->id_activo }}" data-busqueda="{{ $textoActivo }}" style="padding: 6px; border-bottom: 1px solid #f1f5f9;">
                                [{{ $a->codigo_activo }}] {{ $a->nombre }} ({{ $a->marca ?? 'S/M' }})
                            </option>
                        @endforeach
                    </select>
                    <div id="errorActivo" style="display:none;color:#dc2626;font-size:11px;font-weight:700;margin-top:4px;">⚠️ Selecciona un activo válido.</div>
                </div>

                {{-- ========================================== --}}
                {{-- 2. AMBIENTE DESTINO (Buscador por Nombre o Código) --}}
                {{-- ========================================== --}}
                <div>
                    <label style="font-size:12px;font-weight:700;display:block;margin-bottom:4px;color:#334155;">Ambiente Destino *</label>
                    <input type="text" id="buscador-ambiente-transferencia" placeholder="🔍 Buscar ambiente por nombre o código..."
                           style="width:100%;padding:9px 12px;border-radius:8px 8px 0 0;border:1px solid #cbd5e1;font-size:13px;box-sizing:border-box;background:#f8fafc;"
                           onkeyup="filtrarAmbientesTransferencia(this.value)">
                    <select name="id_ambiente_destino" id="select-ambiente-transferencia" size="4"
                            style="width:100%;padding:8px;border-radius:0 0 8px 8px;border:1px solid #cbd5e1;border-top:none;font-size:13px;background:white;box-sizing:border-box;">
                        @foreach($ambientesDisponibles ?? [] as $amb)
                            @php
                                $textoAmbiente = strtolower(($amb->codigo ?? '') . ' ' . $amb->nombre . ' ' . ($amb->carrera?->nombre ?? ''));
                            @endphp
                            <option value="{{ $amb->id_ambiente }}" data-busqueda="{{ $textoAmbiente }}" style="padding: 6px; border-bottom: 1px solid #f1f5f9;">
                                {{ $amb->nombre }} @if(!empty($amb->codigo)) ({{ $amb->codigo }}) @endif — 🎓 {{ $amb->carrera?->nombre ?? '' }}
                            </option>
                        @endforeach
                    </select>
                    <div id="errorAmbiente" style="display:none;color:#dc2626;font-size:11px;font-weight:700;margin-top:4px;">⚠️ Selecciona un ambiente de destino.</div>
                </div>

                {{-- ========================================== --}}
                {{-- 3. NUEVO CUSTODIO (Buscador por Nombre, C.I. o Ítem) --}}
                {{-- ========================================== --}}
                <div>
                    <label style="font-size:12px;font-weight:700;display:block;margin-bottom:4px;color:#334155;">Nuevo Custodio *</label>
                    <input type="text" id="buscador-custodio-transferencia" placeholder="🔍 Buscar custodio por Nombre, C.I. o N° de Ítem..."
                           style="width:100%;padding:9px 12px;border-radius:8px 8px 0 0;border:1px solid #cbd5e1;font-size:13px;box-sizing:border-box;background:#f8fafc;"
                           onkeyup="filtrarCustodiosTransferencia(this.value)">
                    <select name="id_custodio" id="select-custodio-transferencia" size="4"
                            style="width:100%;padding:8px;border-radius:0 0 8px 8px;border:1px solid #cbd5e1;border-top:none;font-size:13px;background:white;box-sizing:border-box;">
                        @foreach($usuarios ?? [] as $u)
                            @php
                                $itemActivo = $u->items->first();
                                $nroItem = $itemActivo ? 'Ítem #' . $itemActivo->numero_item : 'Sin ítem';
                                $textoCustodio = strtolower($u->nombre_completo . ' ' . $u->ci . ' ' . ($itemActivo?->numero_item ?? ''));
                            @endphp
                            <option value="{{ $u->id_usuario }}" data-busqueda="{{ $textoCustodio }}" style="padding: 6px; border-bottom: 1px solid #f1f5f9;">
                                {{ $u->nombre_completo }} (C.I.: {{ $u->ci }} · {{ $nroItem }})
                            </option>
                        @endforeach
                    </select>
                    <div id="errorCustodio" style="display:none;color:#dc2626;font-size:11px;font-weight:700;margin-top:4px;">⚠️ Selecciona un nuevo custodio.</div>
                </div>

                <div>
                    <label style="font-size:12px;font-weight:700;display:block;margin-bottom:4px;color:#334155;">Fecha de Transferencia *</label>
                    <input type="date" name="fecha_transferencia" id="inputFecha" required value="{{ date('Y-m-d') }}"
                           style="width:100%;padding:10px;border-radius:8px;border:1px solid #cbd5e1;font-size:13px;background:white;">
                    <div id="errorFecha" style="display:none;color:#dc2626;font-size:11px;font-weight:700;margin-top:4px;">⚠️ La fecha es obligatoria.</div>
                </div>

                <div>
                    <label style="font-size:12px;font-weight:700;display:block;margin-bottom:4px;color:#334155;">Observaciones</label>
                    <textarea name="observaciones" rows="2" placeholder="Motivo o detalles de la transferencia..."
                              style="width:100%;padding:10px;border-radius:8px;border:1px solid #cbd5e1;font-size:13px;resize:none;"></textarea>
                </div>

            </div>

            <div style="display:flex;justify-content:flex-end;gap:10px;border-top:1px solid #e2e8f0;padding-top:16px;margin-top:20px;">
                <a href="{{ route('actas.transferencia.index') }}" style="background:#e2e8f0;color:#475569;border:none;padding:10px 18px;border-radius:8px;font-weight:700;text-decoration:none;font-size:13px;display:inline-flex;align-items:center;">Cancelar</a>
                <button type="button" onclick="validarTransferencia()" style="background:#ea580c;color:white;border:none;padding:10px 18px;border-radius:8px;font-weight:700;cursor:pointer;font-size:13px;">Registrar Transferencia</button>
            </div>
        </form>
    </div>
</div>

<script>
    function filtrarActivosTransferencia(texto) {
        const filtro = texto.toLowerCase().trim();
        const select = document.getElementById('select-activo-transferencia');
        for (let i = 0; i < select.options.length; i++) {
            const opt = select.options[i];
            const busqueda = opt.getAttribute('data-busqueda') || "";
            opt.style.display = busqueda.includes(filtro) ? "" : "none";
        }
    }

    function filtrarAmbientesTransferencia(texto) {
        const filtro = texto.toLowerCase().trim();
        const select = document.getElementById('select-ambiente-transferencia');
        for (let i = 0; i < select.options.length; i++) {
            const opt = select.options[i];
            const busqueda = opt.getAttribute('data-busqueda') || "";
            opt.style.display = busqueda.includes(filtro) ? "" : "none";
        }
    }

    function filtrarCustodiosTransferencia(texto) {
        const filtro = texto.toLowerCase().trim();
        const select = document.getElementById('select-custodio-transferencia');
        for (let i = 0; i < select.options.length; i++) {
            const opt = select.options[i];
            const busqueda = opt.getAttribute('data-busqueda') || "";
            opt.style.display = busqueda.includes(filtro) ? "" : "none";
        }
    }

    function validarTransferencia() {
        let isValid = true;

        const activo = document.getElementById('select-activo-transferencia');
        const errActivo = document.getElementById('errorActivo');
        if (!activo.value) {
            errActivo.style.display = 'block';
            activo.style.borderColor = '#dc2626';
            isValid = false;
        } else {
            errActivo.style.display = 'none';
            activo.style.borderColor = '#cbd5e1';
        }

        const ambiente = document.getElementById('select-ambiente-transferencia');
        const errAmbiente = document.getElementById('errorAmbiente');
        if (!ambiente.value) {
            errAmbiente.style.display = 'block';
            ambiente.style.borderColor = '#dc2626';
            isValid = false;
        } else {
            errAmbiente.style.display = 'none';
            ambiente.style.borderColor = '#cbd5e1';
        }

        const custodio = document.getElementById('select-custodio-transferencia');
        const errCustodio = document.getElementById('errorCustodio');
        if (!custodio.value) {
            errCustodio.style.display = 'block';
            custodio.style.borderColor = '#dc2626';
            isValid = false;
        } else {
            errCustodio.style.display = 'none';
            custodio.style.borderColor = '#cbd5e1';
        }

        const fecha = document.getElementById('inputFecha');
        const errFecha = document.getElementById('errorFecha');
        if (!fecha.value) {
            errFecha.style.display = 'block';
            fecha.style.borderColor = '#dc2626';
            isValid = false;
        } else {
            errFecha.style.display = 'none';
            fecha.style.borderColor = '#cbd5e1';
        }

        if (isValid) {
            document.getElementById('formTransferencia').submit();
        }
    }
</script>
@endsection
