@extends('layouts.app')

@section('content')
    <section class="panel">
        <h2 style="margin-top:0;">Transferir activo</h2>
        <p style="color:#64748b;font-size:13px;">Pase el activo a otro custodio. Sólo activos activos (no de baja) y de su ámbito.</p>

        <form id="filtro-tr" method="GET" action="{{ route('actas.transferencia.create') }}" style="margin-bottom:10px;">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar por código o nombre..."
                style="width:100%;padding:8px;border:1px solid var(--border);border-radius:8px;">
        </form>
        <small style="color:#94a3b8;">Tipee para filtrar y seleccione un activo.</small>

        <form method="POST" action="{{ route('actas.transferencia.store') }}" style="margin-top:14px;">
            @csrf
            <div class="form-grid">
                <div class="full">
                    <label style="font-weight:700">Activo *</label>
                    <div style="max-height:240px;overflow:auto;border:1px solid var(--border);border-radius:8px;">
                        <table style="width:100%;margin:0;">
                            <tbody>
                            @forelse($activos as $a)
                                <tr>
                                    <td style="padding:8px;vertical-align:top;">
                                        <input type="radio" name="activo_id" value="{{ $a->id }}" required>
                                    </td>
                                    <td style="padding:8px;">
                                        <div style="font-weight:700;font-size:13px;">{{ $a->codigo_activo }}</div>
                                        <div>{{ $a->nombre }}</div>
                                        <div style="color:#64748b;font-size:12px;">
                            {{ optional($a->categoria)->nombre }} · {{ optional(optional($a->ambiente)->carrera)->nombre }} / {{ optional($a->ambiente)->nombre }}
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="2" style="padding:12px;color:#94a3b8;">No hay activos disponibles.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <label>Destinatario (custodio) *</label>
                <select name="responsable_entrante_id" required style="width:100%;padding:8px;border:1px solid var(--border);border-radius:8px;">
                    <option value="">Seleccione...</option>
                    @foreach($destinos as $d)
                        <option value="{{ $d->id }}">{{ $d->nombre_completo }} ({{ $d->rol }})</option>
                    @endforeach
                </select>

                <div class="full">
                    <label>Motivo de la transferencia *</label>
                    <textarea name="motivo_transferencia" required rows="2" maxlength="500"
                        style="width:100%;padding:8px;border:1px solid var(--border);border-radius:8px;"></textarea>
                </div>
                <div class="full">
                    <label>Observaciones</label>
                    <textarea name="observaciones" rows="2"
                        style="width:100%;padding:8px;border:1px solid var(--border);border-radius:8px;"></textarea>
                </div>
            </div>

            <div style="margin-top:14px;display:flex;gap:10px;justify-content:flex-end;">
                <a href="{{ route('actas.transferencia.index') }}" class="btn" style="background:#e2e8f0;">Cancelar</a>
                <button type="submit" class="btn" style="background:#7c3aed;color:#fff;">Generar transferencia</button>
            </div>
        </form>

        @if($errors->any())
            <div class="alert alert-danger" style="margin-top:12px;">
                <ul style="margin:0;padding-left:18px;">@foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
            </div>
        @endif
    </section>
@endsection
