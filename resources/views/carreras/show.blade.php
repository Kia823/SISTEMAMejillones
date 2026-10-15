@extends('layouts.app')
@section('content')
@php use Illuminate\Support\Str; @endphp

    @if (session('success'))
        <div style="background: #dcfce7; border: 1px solid #bbf7d0; color: #166534; padding: 14px 20px; border-radius: 12px; margin-bottom: 20px; font-weight: 700; display: flex; align-items: center; gap: 10px; box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);">
            <svg style="width: 20px; height: 20px; color: #16a34a;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <section class="hero-card" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); padding: 30px; border-radius: 16px; color: white; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; margin-bottom: 25px; box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.2);">
        <div>
            <div style="display:inline-block;padding:6px 10px;border-radius:999px;background:rgba(255,255,255,0.18);font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;">Carrera</div>
            <h1 style="margin:10px 0 8px;font-size:28px;">{{ $nombre }}</h1>
            <p style="margin: 0; opacity: 0.9;">Jefe de Carrera: <strong>{{ $carrera->jefeCarrera?->nombre_completo ?? 'No asignado' }}</strong></p>
        </div>
        <div class="stat-grid" style="display: flex; gap: 10px;">
            <div style="background: rgba(15, 23, 42, 0.35); border: 1px solid rgba(255, 255, 255, 0.1); padding: 12px 18px; border-radius: 12px; text-align: center; min-width: 90px;">
                <div style="font-size: 20px; font-weight: 800; color: white;">{{ $ambientes->count() }}</div>
                <span style="font-size: 10px; font-weight: 700; opacity: 0.8; text-transform: uppercase;">Ambientes</span>
            </div>
            <div style="background: rgba(15, 23, 42, 0.35); border: 1px solid rgba(255, 255, 255, 0.1); padding: 12px 18px; border-radius: 12px; text-align: center; min-width: 90px;">
                <div style="font-size: 20px; font-weight: 800; color: white;">{{ $ambientes->sum(fn($amb) => $amb->activos_count ?? 0) }}</div>
                <span style="font-size: 10px; font-weight: 700; opacity: 0.8; text-transform: uppercase;">Activos</span>
            </div>
        </div>
    </section>

    <section class="panel" style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; padding: 25px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px; margin-bottom:18px;">
            <div>
                <h2 style="font-size: 18px; font-weight: 800; color: #0f172a; margin: 0;">Ambientes relacionados</h2>
                <p style="font-size: 13px; color: #64748b; margin: 4px 0 0 0;">Haz doble clic en cualquier ambiente para ingresar.</p>
            </div>

            @if (auth()->check() && auth()->user()->rol === 'ADMINISTRADOR')
                <button type="button" onclick="openAmbienteModal()" style="background: #2563eb; color: white; border: none; padding: 10px 18px; border-radius: 10px; font-weight: 700; cursor: pointer; box-shadow: 0 4px 6px -1px rgba(37,99,235,0.2);">+ Nuevo ambiente</button>
            @endif
        </div>

        @if ($ambientes->count())
            <style>
                .folders-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
                    gap: 16px;
                    margin-top: 15px;
                }
                .folder-card {
                    background: #ffffff;
                    border: 1px solid #e2e8f0;
                    border-radius: 14px;
                    padding: 18px;
                    text-align: left;
                    cursor: pointer;
                    transition: all 0.2s ease;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.02);
                    display: flex;
                    flex-direction: column;
                    justify-content: space-between;
                    position: relative;
                }
                .folder-card:hover {
                    transform: translateY(-3px);
                    border-color: #2563eb;
                    box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.1);
                }
            </style>

            <div class="folders-grid">
                @foreach ($ambientes as $amb)
                    @php
                     // Get custodios from activos y ítems (new system)
                     $custodiosDesdeActivosYItems = \App\Models\User::whereHas('activosCustodiados.ambiente', function($q) use ($amb) {
                         $q->where('id_ambiente', $amb->id_ambiente);
                     })
                     ->orWhereHas('itemsVigentes', function($q) use ($amb) {
                         $q->where('items.id_ambiente', $amb->id_ambiente);
                     })
                     ->where('estado', 'ACTIVO')
                     ->get(['usuarios.id_usuario', 'usuarios.nombre_completo', 'usuarios.ci']);
                        
                         // Get custodios from direct ambiente assignments (old system - for backward compatibility)
                         $custodiosDesdeAsignacionDirecta = $amb->custodios()->where('estado', 'ACTIVO')->get(['ambiente_user.id_usuario', 'usuarios.nombre_completo', 'usuarios.ci']);
                        
                        // Combine and remove duplicates
                        $custodiosCombinados = $custodiosDesdeActivosYItems->merge($custodiosDesdeAsignacionDirecta)->unique('id_usuario');
                    @endphp
                    <div class="folder-card" ondblclick="window.location.href='{{ route('ambientes.show', $amb->id_ambiente) }}'">

                        {{-- ⭐ BOTÓN ARRINCONADO ARRIBA A LA DERECHA --}}
                        @if (auth()->check() && auth()->user()->rol === 'ADMINISTRADOR')
                            <button type="button" onclick="event.stopPropagation(); openAsignarCustodiosModal('{{ $amb->id_ambiente }}', '{{ addslashes($amb->nombre) }}', @json(collect($custodiosCombinados)->pluck('id_usuario')))"
                                style="position: absolute; top: 12px; right: 12px; background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; padding: 3px 8px; border-radius: 6px; font-weight: 700; font-size: 11px; cursor: pointer; display: flex; align-items: center; gap: 3px; z-index: 5;">
                                👥 Asignar
                            </button>
                        @endif

                        <div>
                            {{-- ⭐ Acolchado a la derecha (padding-right: 75px) para que el botón no tape el código --}}
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; padding-right: 75px;">
                                <span style="font-size: 28px;">📂</span>
                                <span style="font-size: 10px; font-weight: 800; background: #eff6ff; color: #1d4ed8; padding: 2px 6px; border-radius: 4px;">
                                    {{ $amb->codigo ?? 'S/C' }}
                                </span>
                            </div>

                            <h3 style="font-size: 15px; font-weight: 800; color: #0f172a; margin: 0 0 4px 0;">{{ $amb->nombre }}</h3>
                            <p style="font-size: 11px; color: #64748b; margin: 0 0 10px 0;">{{ $amb->bloque ? 'Bloque ' . $amb->bloque : 'Bloque N/A' }} · {{ $amb->piso ? 'Piso ' . $amb->piso : 'Piso N/D' }}</p>

                            {{-- Múltiples Docentes Custodios --}}
                            <div style="font-size: 10px; background: #f8fafc; padding: 8px; border-radius: 8px; border: 1px solid #f1f5f9; margin-bottom: 12px;">
                                <span style="font-weight: 700; color: #334155; display: block; margin-bottom: 3px;">👥 Docentes Custodios:</span>
                                @forelse($custodiosCombinados ?? [] as $custodio)
                                    <div style="color: #475569;">• {{ $custodio->nombre_completo }}</div>
                                @empty
                                    <span style="color: #94a3b8; font-style: italic;">Sin custodios asignados</span>
                                @endforelse
                            </div>
                        </div>

                         {{-- Botones de edición y eliminación --}}
                         <div style="display: flex; gap: 6px; justify-content: flex-end; border-top: 1px solid #f1f5f9; padding-top: 10px;" onclick="event.stopPropagation();">
                            @if (auth()->check() && auth()->user()->rol === 'ADMINISTRADOR')
                                <button type="button" onclick='editAmbiente({{ json_encode($amb, JSON_UNESCAPED_UNICODE) }})' title="Editar ambiente"
                                    style="background:#fef9c3; color:#ca8a04; width: 32px; height: 32px; border-radius: 8px; border: 1px solid #fde047; display: flex; align-items: center; justify-content: center; font-size: 14px; cursor:pointer;">
                                    ✏️
                                </button>

                                <form id="delete-form-{{ $amb->id_ambiente }}" method="POST" action="{{ route('ambientes.destroy', ['id_carrera' => $carrera->id_carrera, 'id_ambiente' => $amb->id_ambiente]) }}" style="margin: 0; display: flex;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="openDeleteModal('{{ $amb->id_ambiente }}')" title="Eliminar ambiente"
                                        style="background:#fee2e2; color:#dc2626; width: 32px; height: 32px; border-radius: 8px; border: 1px solid #fca5a5; display: flex; align-items: center; justify-content: center; font-size: 14px; cursor:pointer;">
                                        🗑️
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p style="text-align: center; padding: 40px; color: #94a3b8; font-weight: 600;">No hay ambientes registrados para esta carrera.</p>
        @endif
    </section>

    {{-- ============================================================ --}}
    {{-- MODAL ASIGNAR CUSTODIOS (BÚSQUEDA POR ÍTEM, C.I., N° de Ítem) --}}
    {{-- ============================================================ --}}
    <div id="modal-asignar-custodios" style="display: none !important; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 10005; justify-content: center; align-items: center; padding: 20px;">
        <div style="background: white; border-radius: 20px; padding: 25px; width: 100%; max-width: 550px; max-height: 85vh; display: flex; flex-direction: column; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);">

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;">
                <h3 id="modal-ambiente-titulo" style="margin: 0; font-size: 16px; font-weight: 800; color: #0f172a;">Asignar Custodios al Ambiente</h3>
                <button type="button" onclick="closeAsignarCustodiosModal()" style="background: #f1f5f9; border: none; border-radius: 50%; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; font-size: 16px; cursor: pointer; color: #475569;">×</button>
            </div>

            <form id="form-asignar-custodios" method="POST">
                @csrf

                {{-- Buscador instantáneo --}}
                <div style="margin-bottom: 12px;">
                    <input type="text" id="buscador-docentes" placeholder="🔍 Buscar por Nombre, C.I. o N° de Ítem..."
                           style="width: 100%; padding: 9px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px; box-sizing: border-box;"
                           onkeyup="filtrarDocentesModal(this.value)">
                </div>

                {{-- Lista con checkboxes de usuarios activos --}}
                <div id="lista-docentes-container" style="max-height: 240px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px; background: #f8fafc; margin-bottom: 15px;">
                    @foreach(\App\Models\User::where('estado', 'ACTIVO')->with(['items' => fn($q) => $q->whereNull('item_usuario.fecha_fin')])->orderBy('nombre_completo')->get() as $docente)
                        @php
                            $itemActivo = $docente->items->first();
                            $nroItem = $itemActivo ? 'Ítem #' . $itemActivo->numero_item : 'Sin ítem';
                            $cadenaBusqueda = strtolower($docente->nombre_completo . ' ' . $docente->ci . ' ' . ($itemActivo?->numero_item ?? ''));
                        @endphp
                        <label class="docente-item" data-busqueda="{{ $cadenaBusqueda }}"
                               style="display: flex; align-items: center; gap: 10px; padding: 8px; border-bottom: 1px solid #e2e8f0; cursor: pointer; background: white; border-radius: 6px; margin-bottom: 4px;">
                            <input type="checkbox" name="usuarios[]" value="{{ $docente->id_usuario }}" class="checkbox-docente" style="width: 16px; height: 16px;">
                            <div style="font-size: 12px;">
                                <strong style="color: #0f172a; display: block;">{{ $docente->nombre_completo }}</strong>
                                <span style="color: #64748b;">C.I.: {{ $docente->ci }} · <span style="color: #2563eb; font-weight: 600;">{{ $nroItem }}</span></span>
                            </div>
                        </label>
                    @endforeach
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px; border-top: 1px solid #e2e8f0; padding-top: 14px;">
                    <button type="button" onclick="closeAsignarCustodiosModal()" style="background: #e2e8f0; color: #475569; border: none; padding: 9px 18px; border-radius: 8px; font-weight: 700; cursor: pointer; font-size: 13px;">Cancelar</button>
                    <button type="submit" style="background: #2563eb; color: white; border: none; padding: 9px 18px; border-radius: 8px; font-weight: 700; cursor: pointer; font-size: 13px;">💾 Guardar Asignación</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal flotante para crear/editar ambiente -->
    <div id="ambiente-modal" style="display: none !important; position: fixed; inset: 0; background: rgba(0, 0, 0, 0.5); z-index: 9999; justify-content: center; align-items: center; padding: 20px;">
        <div style="background: white; border-radius: 20px; padding: 30px; width: 100%; max-width: 500px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); position: relative;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;">
                <h3 id="ambiente-modal-title" style="margin: 0; font-size: 18px; font-weight: 800; color: #0f172a;">Nuevo ambiente</h3>
                <button type="button" onclick="closeAmbienteModal()" style="background: #f1f5f9; border: none; border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-center: center; font-size: 18px; cursor: pointer; color: #475569;">×</button>
            </div>

            <form id="ambiente-form" method="POST" action="{{ route('ambientes.store', ['id_carrera' => $carrera->id_carrera]) }}">
                @csrf

                <input id="ambiente-method" type="hidden" name="_method" value="POST">

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 15px;">
                    <div>
                        <label style="font-size: 12px; font-weight: 700; display: block; margin-bottom: 4px; color: #334155;">Código *</label>
                        <input id="amb-codigo" name="codigo" value="{{ old('codigo') }}" maxlength="20" style="width: 100%; padding: 9px 12px; border-radius: 8px; border: 1px solid {{ $errors->has('codigo') ? '#dc2626' : '#cbd5e1' }};">
                        @error('codigo')
                            <span style="color: #dc2626; font-size: 11px; font-weight: 600; display: block; margin-top: 3px;">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label style="font-size: 12px; font-weight: 700; display: block; margin-bottom: 4px; color: #334155;">Nombre *</label>
                        <input id="amb-nombre" name="nombre" value="{{ old('nombre') }}" maxlength="100" style="width: 100%; padding: 9px 12px; border-radius: 8px; border: 1px solid {{ $errors->has('nombre') ? '#dc2626' : '#cbd5e1' }};">
                        @error('nombre')
                            <span style="color: #dc2626; font-size: 11px; font-weight: 600; display: block; margin-top: 3px;">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label style="font-size: 12px; font-weight: 700; display: block; margin-bottom: 4px; color: #334155;">Bloque *</label>
                        <input id="amb-bloque" name="bloque" value="{{ old('bloque') }}" maxlength="10" style="width: 100%; padding: 9px 12px; border-radius: 8px; border: 1px solid {{ $errors->has('bloque') ? '#dc2626' : '#cbd5e1' }};">
                        @error('bloque')
                            <span style="color: #dc2626; font-size: 11px; font-weight: 600; display: block; margin-top: 3px;">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label style="font-size: 12px; font-weight: 700; display: block; margin-bottom: 4px; color: #334155;">Piso *</label>
                        <input id="amb-piso" name="piso" type="number" value="{{ old('piso') }}" style="width: 100%; padding: 9px 12px; border-radius: 8px; border: 1px solid {{ $errors->has('piso') ? '#dc2626' : '#cbd5e1' }};">
                        @error('piso')
                            <span style="color: #dc2626; font-size: 11px; font-weight: 600; display: block; margin-top: 3px;>{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px; border-top: 1px solid #e2e8f0; padding-top: 15px; margin-top: 15px;">
                    <button type="button" onclick="closeAmbienteModal()" style="background: #e2e8f0; color: #475569; border: none; padding: 10px 18px; border-radius: 8px; font-weight: 700; cursor: pointer;">Cancelar</button>
                    <button type="submit" style="background: #2563eb; color: white; border: none; padding: 10px 18px; border-radius: 8px; font-weight: 700; cursor: pointer;">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal pequeño de confirmación para eliminar -->
    <div id="delete-confirm-modal" style="display: none !important; position: fixed; inset: 0; background: rgba(0, 0, 0, 0.5); z-index: 10000; justify-content: center; align-items: center; padding: 20px;">
        <div style="background: white; border-radius: 16px; padding: 25px; width: 100%; max-width: 380px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); text-align: center;">
            <div style="font-size: 32px; margin-bottom: 10px;">⚠️</div>
            <h3 style="margin: 0 0 8px; font-size: 18px; font-weight: 800; color: #0f172a;">¿Eliminar ambiente?</h3>
            <p style="font-size: 13px; color: #64748b; margin: 0 0 20px;">Esta acción no se puede deshacer y borrará los datos asociados.</p>
            <div style="display: flex; justify-content: center; gap: 10px;">
                <button type="button" onclick="closeDeleteModal()" style="background: #e2e8f0; color: #475569; border: none; padding: 9px 16px; border-radius: 8px; font-weight: 700; cursor: pointer; font-size: 13px;">Cancelar</button>
                <button type="button" id="confirm-delete-btn" style="background: #dc2626; color: white; border: none; padding: 9px 16px; border-radius: 8px; font-weight: 700; cursor: pointer; font-size: 13px;">Sí, eliminar</button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    let currentAmbienteIdToDelete = null;

    // Modal Asignar Custodios
    function openAsignarCustodiosModal(ambienteId, ambienteNombre, custodiosActuales) {
        const modal = document.getElementById('modal-asignar-custodios');
        const form = document.getElementById('form-asignar-custodios');

        form.action = '{{ url("/ambientes") }}/' + ambienteId + '/custodios';
        document.getElementById('modal-ambiente-titulo').textContent = 'Asignar Custodios a: ' + ambienteNombre;

        document.querySelectorAll('.checkbox-docente').forEach(chk => {
            chk.checked = custodiosActuales.includes(parseInt(chk.value));
        });

        document.getElementById('buscador-docentes').value = '';
        filtrarDocentesModal('');

        modal.style.setProperty('display', 'flex', 'important');
    }

    function closeAsignarCustodiosModal() {
        document.getElementById('modal-asignar-custodios').style.setProperty('display', 'none', 'important');
    }

    function filtrarDocentesModal(texto) {
        const filtro = texto.toLowerCase().trim();
        document.querySelectorAll('.docente-item').forEach(item => {
            const busqueda = item.getAttribute('data-busqueda') || "";
            if (busqueda.includes(filtro)) {
                item.style.display = "flex";
            } else {
                item.style.display = "none";
            }
        });
    }

    // Modal Ambiente
    function openAmbienteModal() {
        const form = document.getElementById('ambiente-form');
        form.reset();
        form.action = '{{ route('ambientes.store', ['id_carrera' => $carrera->id_carrera]) }}';
        document.getElementById('ambiente-method').value = 'POST';
        document.getElementById('ambiente-modal-title').textContent = 'Nuevo ambiente';
        document.getElementById('ambiente-modal').style.setProperty('display', 'flex', 'important');
    }

    function closeAmbienteModal() {
        document.getElementById('ambiente-modal').style.setProperty('display', 'none', 'important');
    }

    function editAmbiente(serialized) {
        const amb = typeof serialized === 'string' ? JSON.parse(serialized) : serialized;
        const form = document.getElementById('ambiente-form');
        const carreraId = '{{ $carrera->id_carrera }}';
        const ambienteId = amb.id_ambiente;

        form.action = '{{ url('/carreras') }}/' + carreraId + '/ambientes/' + ambienteId;
        document.getElementById('ambiente-method').value = 'PUT';
        document.getElementById('ambiente-modal-title').textContent = 'Editar ambiente';
        document.getElementById('amb-codigo').value = amb.codigo || '';
        document.getElementById('amb-nombre').value = amb.nombre || '';
        document.getElementById('amb-bloque').value = amb.bloque || '';
        document.getElementById('amb-piso').value = amb.piso || '';
        document.getElementById('ambiente-modal').style.setProperty('display', 'flex', 'important');
    }

    function openDeleteModal(ambienteId) {
        currentAmbienteIdToDelete = ambienteId;
        document.getElementById('delete-confirm-modal').setProperty('display', 'flex', 'important');
    }

    function closeDeleteModal() {
        currentAmbienteIdToDelete = null;
        document.getElementById('delete-confirm-modal').setProperty('display', 'none', 'important');
    }

    document.getElementById('confirm-delete-btn').addEventListener('click', function() {
        if (currentAmbienteIdToDelete) {
            document.getElementById('delete-form-' + currentAmbienteIdToDelete).submit();
        }
    });
</script>
@endsection