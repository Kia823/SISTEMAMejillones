@extends('layouts.app')

@section('title', 'Actas de Baja')

@section('content')
<div class="container-fluid px-4 py-6">

    {{-- ENCABEZADO CORPORATIVO --}}
    <div style="background: linear-gradient(135deg, #7c2d12 0%, #9a3412 100%); padding: 30px; border-radius: 18px; color: white; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; margin-bottom: 25px; box-shadow: 0 10px 20px -3px rgba(124, 45, 18, 0.25);">
        <div>
            <span style="background: rgba(255, 255, 255, 0.2); padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">Módulo Legal</span>
            <h1 style="font-size: 26px; font-weight: 800; margin: 8px 0 4px 0;">Actas de Baja de Activos</h1>
            <p style="opacity: 0.9; font-size: 14px; margin: 0;">Gestiona, revisa, exporta o da de alta nuevamente los activos institucionales.</p>
        </div>
        <div>
            <a href="{{ route('actas.baja.create') }}"
               style="background: white; color: #7c2d12; padding: 12px 20px; border-radius: 12px; font-weight: 800; font-size: 13px; text-decoration: none; box-shadow: 0 4px 6px rgba(0,0,0,0.1); display: inline-flex; align-items: center; gap: 8px;">
                <span>➖</span> Nueva Baja
            </a>
        </div>
    </div>

    {{-- FILTROS Y BÚSQUEDA --}}
    <div style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; padding: 20px; margin-bottom: 25px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);">
        <form method="GET" action="{{ route('actas.baja.index') }}" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 220px;">
                <label style="display: block; font-size: 11px; font-weight: 800; color: #334155; margin-bottom: 6px; text-transform: uppercase;">Filtrar por Estado</label>
                <select name="estado" onchange="this.form.submit()" style="width: 100%; padding: 10px 14px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; color: #1e293b; outline: none; cursor: pointer;">
                    <option value="">Todos los estados</option>
                    <option value="PENDIENTE" {{ request('estado')=='PENDIENTE'?'selected':'' }}>Pendiente de aprobación</option>
                    <option value="APROBADA" {{ request('estado')=='APROBADA'?'selected':'' }}>Aprobada</option>
                    <option value="RECHAZADA" {{ request('estado')=='RECHAZADA'?'selected':'' }}>Rechazada</option>
                </select>
            </div>
        </form>
    </div>

    {{-- TABLA INSTITUCIONAL --}}
    <div style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
                <thead style="background: #f8fafc; color: #475569; text-transform: uppercase; font-size: 10px; letter-spacing: 0.5px; border-bottom: 1px solid #e2e8f0;">
                    <tr>
                        <th style="padding: 14px 16px;">Número / Fecha</th>
                        <th style="padding: 14px 16px;">Activo (Nombre y Código)</th>
                        <th style="padding: 14px 16px;">Ambiente Original</th>
                        <th style="padding: 14px 16px;">Motivo de Baja</th>
                        <th style="padding: 14px 16px; text-align: center;">Estado</th>
                        <th style="padding: 14px 16px; text-align: center;">Acciones & Reasignación</th>
                    </tr>
                </thead>
                <tbody style="divide-y: #f1f5f9;">
                    @forelse($actas as $a)
                        <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                            <td style="padding: 14px 16px;">
                                <div style="font-family: monospace; font-weight: 800; color: #7c2d12;">
                                    <a href="{{ route('actas.baja.show', $a->id_acta_baja) }}" style="color: #7c2d12; text-decoration: none;">{{ $a->numero_acta }}</a>
                                </div>
                                <div style="font-size: 11px; color: #64748b; font-weight: 600; margin-top: 2px;">
                                    {{ optional($a->fecha_baja)->format('d/m/Y') ?? $a->created_at?->format('d/m/Y') }}
                                </div>
                            </td>
                            <td style="padding: 14px 16px;">
                                <div style="font-weight: 700; color: #0f172a;">{{ $a->activo->nombre ?? 'Sin activo asociado' }}</div>
                                <div style="font-size: 11px; font-family: monospace; color: #2563eb; font-weight: 600;">{{ $a->activo->codigo_activo ?? '—' }}</div>
                            </td>
                            <td style="padding: 14px 16px; color: #475569;">
                                {{ optional($a->activo->ambiente)->nombre ?? '—' }}
                            </td>
                            <td style="padding: 14px 16px; color: #475569; max-width: 220px;">
                                {{ \Illuminate\Support\Str::limit($a->motivo, 40) }}
                            </td>
                            <td style="padding: 14px 16px; text-align: center;">
                                @php
                                    $estiloEstado = match($a->estado) {
                                        'APROBADA' => 'background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0;',
                                        'RECHAZADA' => 'background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5;',
                                        default => 'background: #fef9c3; color: #854d0e; border: 1px solid #fde047;'
                                    };
                                @endphp
                                <span style="display: inline-block; padding: 4px 10px; border-radius: 6px; font-weight: 800; font-size: 10px; {{ $estiloEstado }}">
                                    {{ $a->estado_texto }}
                                </span>
                            </td>
                            <td style="padding: 14px 16px; text-align: center;">
                                <div style="display: flex; gap: 6px; justify-content: center; flex-wrap: wrap; align-items: center;">
                                    <a href="{{ route('actas.baja.show', $a->id_acta_baja) }}"
                                       style="padding: 6px 10px; background: #eff6ff; color: #1d4ed8; border-radius: 6px; font-size: 11px; font-weight: 700; text-decoration: none;">
                                        👁️ Ver
                                    </a>
                                    @if($a->esAprobada())
                                        <a href="{{ route('actas.baja.pdf', $a->id_acta_baja) }}" target="_blank"
                                           style="padding: 6px 10px; background: #fee2e2; color: #991b1b; border-radius: 6px; font-size: 11px; font-weight: 700; text-decoration: none;">
                                            📄 PDF
                                        </a>
                                        <a href="{{ route('actas.baja.excel', $a->id_acta_baja) }}" target="_blank"
                                           style="padding: 6px 10px; background: #dcfce7; color: #166534; border-radius: 6px; font-size: 11px; font-weight: 700; text-decoration: none;">
                                            📊 Excel
                                        </a>

                                        {{-- BOTÓN PARA DAR DE ALTA --}}
                                        @if(auth()->user()->esAdmin() && $a->activo && $a->activo->estado_registro === 'BAJA')
                                            <button type="button" onclick="document.getElementById('modal-alta-{{ $a->id_acta_baja }}').style.display='flex'"
                                                    style="padding: 6px 10px; background: #2563eb; color: white; border: none; border-radius: 6px; font-size: 11px; font-weight: 700; cursor: pointer;">
                                                ♻️ Dar de Alta
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>

                        {{-- MODAL PARA REASIGNAR Y DAR DE ALTA (CON CATEGORÍAS FILTRADAS POR AMBIENTE) --}}
                        @if($a->esAprobada() && auth()->user()->esAdmin() && $a->activo && $a->activo->estado_registro === 'BAJA')
                        <div id="modal-alta-{{ $a->id_acta_baja }}" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center; padding: 20px;">
                            <div style="background: white; border-radius: 16px; width: 100%; max-width: 500px; padding: 25px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);">
                                <h3 style="font-size: 18px; font-weight: 800; color: #0f172a; margin-top: 0;">Reactivar y Dar de Alta Activo</h3>
                                <p style="font-size: 13px; color: #64748b; margin-bottom: 20px;">
                                    Asigne la nueva ubicación para devolver el activo <strong>{{ $a->activo->nombre }}</strong> ({{ $a->activo->codigo_activo }}) al inventario activo.
                                </p>
                                <form method="POST" action="{{ route('actas.baja.darDeAlta', $a->id_acta_baja) }}">
                                    @csrf
                                    <div style="margin-bottom: 15px;">
                                        <label style="display: block; font-size: 12px; font-weight: 700; color: #334155; margin-bottom: 5px;">Nuevo Ambiente Destino:</label>
                                        <select name="id_ambiente" id="ambiente-select-{{ $a->id_acta_baja }}" onchange="filtrarCategorias({{ $a->id_acta_baja }})" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px;">
                                            @foreach(\App\Models\Ambiente::with('carrera')->orderBy('nombre')->get() as $amb)
                                                <option value="{{ $amb->id_ambiente }}">{{ $amb->nombre }} ({{ $amb->carrera->nombre ?? '' }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div style="margin-bottom: 15px;">
                                        <label style="display: block; font-size: 12px; font-weight: 700; color: #334155; margin-bottom: 5px;">Categoría (Correspondiente al ambiente):</label>
                                        <select name="id_categoria" id="categoria-select-{{ $a->id_acta_baja }}" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px;">
                                            {{-- Se cargan dinámicamente vía JavaScript --}}
                                        </select>
                                    </div>
                                    <div style="margin-bottom: 20px;">
                                        <label style="display: block; font-size: 12px; font-weight: 700; color: #334155; margin-bottom: 5px;">Nuevo Custodio:</label>
                                        <select name="id_custodio" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px;">
                                            @foreach(\App\Models\User::where('estado', 'ACTIVO')->orderBy('nombre_completo')->get() as $usr)
                                                <option value="{{ $usr->id_usuario }}" {{ $usr->id_usuario == $a->activo->id_custodio ? 'selected' : '' }}>{{ $usr->nombre_completo }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div style="display: flex; justify-content: flex-end; gap: 10px;">
                                        <button type="button" onclick="document.getElementById('modal-alta-{{ $a->id_acta_baja }}').style.display='none'"
                                                style="padding: 8px 16px; background: #e2e8f0; color: #334155; border: none; border-radius: 8px; font-weight: 700; cursor: pointer;">
                                            Cancelar
                                        </button>
                                        <button type="submit" style="padding: 8px 16px; background: #2563eb; color: white; border: none; border-radius: 8px; font-weight: 700; cursor: pointer;">
                                            Confirmar Alta
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        {{-- SCRIPT PARA FILTRAR CATEGORÍAS SEGÚN EL AMBIENTE SELECCIONADO --}}
                        <script>
                            const categoriasPorAmbiente_{{ $a->id_acta_baja }} = @json(\App\Models\Categoria::all()->groupBy('id_ambiente'));

                            function filtrarCategorias(idActa) {
                                const selectAmbiente = document.getElementById('ambiente-select-' + idActa);
                                const selectCategoria = document.getElementById('categoria-select-' + idActa);
                                const ambienteId = selectAmbiente.value;

                                selectCategoria.innerHTML = '';

                                const cats = categoriasPorAmbiente_{{ $a->id_acta_baja }}[ambienteId] || [];

                                if (cats.length === 0) {
                                    selectCategoria.innerHTML = '<option value="">No hay categorías en este ambiente</option>';
                                    return;
                                }

                                cats.forEach(cat => {
                                    const opt = document.createElement('option');
                                    opt.value = cat.id_categoria;
                                    opt.textContent = cat.nombre;
                                    if (cat.id_categoria == {{ $a->activo->id_categoria ?? 'null' }}) {
                                        opt.selected = true;
                                    }
                                    selectCategoria.appendChild(opt);
                                });
                            }

                            // Ejecutar al abrir el modal
                            document.addEventListener("DOMContentLoaded", function() {
                                filtrarCategorias({{ $a->id_acta_baja }});
                            });
                        </script>
                        @endif
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 50px 20px; color: #64748b; font-weight: 600;">
                                No hay actas de baja registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(method_exists($actas, 'hasPages') && $actas->hasPages())
        <div style="margin-top: 20px;">{{ $actas->links() }}</div>
    @endif
</div>
@endsection
