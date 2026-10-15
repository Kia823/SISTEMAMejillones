@extends('layouts.app')

@section('title', 'Liberación de Activos')

@section('content')
<div class="container-fluid px-4 py-6">

    {{-- Encabezado corporativo --}}
    <div style="background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%); padding: 30px; border-radius: 18px; color: white; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; margin-bottom: 25px; box-shadow: 0 10px 20px -3px rgba(124, 58, 237, 0.25);">
        <div>
            <span style="background: rgba(255, 255, 255, 0.2); padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">Módulo Legal</span>
            <h1 style="font-size: 26px; font-weight: 800; margin: 8px 0 4px 0;">Liberación de Activos</h1>
            <p style="opacity: 0.9; font-size: 14px; margin: 0;">Genera el acta oficial de liberación de un ambiente específico y certifica la entrega formal.</p>
        </div>
    </div>

    {{-- Formulario de selección --}}
    <div style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; padding: 30px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02); max-width: 800px; margin: 0 auto;">
        <h2 style="font-size: 18px; font-weight: 800; color: #0f172a; margin: 0 0 16px 0;">Seleccionar Ambiente a Liberar</h2>

        <form id="formLiberacion" method="GET" onsubmit="event.preventDefault(); generarActaLiberacion();" style="display: flex; flex-direction: column; gap: 18px;">

            {{-- FILTRAR POR CARRERA --}}
            <div>
                <label style="display: block; font-size: 12px; font-weight: 800; color: #334155; margin-bottom: 6px; text-transform: uppercase;">
                    Filtrar por Carrera
                </label>
                <select id="filtroCarreraLib" onchange="filtrarAmbientesLib(this.value)"
                        style="width: 100%; padding: 11px 14px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; color: #1e293b; outline: none;">
                    <option value="">— Todas las carreras —</option>
                    {{-- ⭐ Consulta directa al modelo para asegurar que aparezcan todas las registradas --}}
                    @foreach(\App\Models\Carrera::orderBy('nombre')->get() as $car)
                        <option value="{{ $car->id_carrera ?? $car->id }}">{{ $car->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="display: block; font-size: 12px; font-weight: 800; color: #334155; margin-bottom: 6px; text-transform: uppercase;">
                    Ambiente *
                </label>
                <select name="ambiente_id" id="selectAmbienteLib"
                        style="width: 100%; padding: 11px 14px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; color: #1e293b; outline: none;">
                    <option value="">— Seleccionar ambiente —</option>
                    @foreach($ambientes as $a)
                        <option value="{{ $a->id_ambiente }}" data-carrera="{{ $a->id_carrera ?? optional($a->carrera)->id }}">
                            {{ $a->nombre }} — [{{ optional($a->carrera)->nombre ?? 'Sin carrera' }}]
                        </option>
                    @endforeach
                </select>
                {{-- Mensaje de error abajo si falta seleccionar --}}
                <span id="errorAmbienteLib" style="font-size: 11px; color: #dc2626; font-weight: 700; margin-top: 4px; display: none;">⚠️ Debe seleccionar un ambiente para generar el acta.</span>
            </div>

            <div style="display: flex; justify-content: flex-end; pt: 16px; border-top: 1px solid #f1f5f9;">
                <button type="button" onclick="generarActaLiberacion()"
                        style="padding: 11px 20px; background: #7c3aed; color: white; border: none; border-radius: 10px; font-size: 13px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 2px 4px rgba(124, 58, 237, 0.2);">
                    📄 Generar Acta de Liberación
                </button>
            </div>
        </form>
    </div>

    {{-- Información adicional --}}
    <div style="max-width: 800px; margin: 20px auto 0 auto; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 14px; padding: 16px 20px; display: flex; align-items: flex-start; gap: 12px;">
        <span style="font-size: 20px;">ℹ️</span>
        <div style="font-size: 13px; color: #1e3a8a; line-height: 1.5;">
            <strong style="font-weight: 800;">¿Qué es un acta de liberación?</strong><br>
            Es el documento oficial que certifica la entrega formal de los activos fijos de un determinado ambiente, transfiriendo de manera legal la responsabilidad de custodia[cite: 25].
        </div>
    </div>

</div>

<script>
    function filtrarAmbientesLib(carreraId) {
        const select = document.getElementById('selectAmbienteLib');
        const opciones = select.options;
        for (let i = 0; i < opciones.length; i++) {
            let opt = opciones[i];
            if (!opt.value) continue;
            let car = opt.getAttribute('data-carrera');
            if (!carreraId || car === carreraId) {
                opt.style.display = '';
            } else {
                opt.style.display = 'none';
            }
        }
        select.value = "";
    }

    function generarActaLiberacion() {
        const selectAmbiente = document.getElementById('selectAmbienteLib');
        const errorAmbiente = document.getElementById('errorAmbienteLib');

        if (!selectAmbiente.value) {
            errorAmbiente.style.display = 'block';
            selectAmbiente.style.borderColor = '#dc2626';
        } else {
            errorAmbiente.style.display = 'none';
            selectAmbiente.style.borderColor = '#cbd5e1';
            window.open('{{ url('actas/liberacion') }}/' + selectAmbiente.value + '/pdf', '_blank');
        }
    }
</script>
@endsection
