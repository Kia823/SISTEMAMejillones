@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
<div style="max-width:800px;margin:0 auto;">

    <div style="background:white;padding:28px;border-radius:18px;box-shadow:0 4px 12px rgba(0,0,0,.05);">

        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
            <div>
                <a href="{{ route('items.show', $item->numero_item) }}" style="color:#64748b;font-size:13px;font-weight:700;text-decoration:none;">← Volver al ítem</a>
                <h2 style="font-size:24px;font-weight:800;color:#0f172a;margin:4px 0 0 0;">Editar Ítem {{ $item->numero_item }}</h2>
            </div>
        </div>

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

        <form method="POST" action="{{ route('items.update', $item->id_item) }}">
            @csrf
            @method('PUT')

            <style>
                .form-grid { display:grid;grid-template-columns:1fr 1fr;gap:16px; }
                .form-grid label { display:flex;flex-direction:column;font-size:12px;font-weight:700;color:#334155;gap:4px; }
                .form-grid label.full { grid-column:span 2; }
                .form-grid input, .form-grid select {
                    width:100%;padding:10px 12px;border-radius:8px;border:1px solid #cbd5e1;font-size:14px;background:#fff;box-sizing:border-box;
                }
                .form-grid input:focus, .form-grid select:focus {
                    outline:none;border-color:#2563eb;box-shadow:0 0 0 3px rgba(37,99,235,.1);
                }
                .input-error { border-color:#dc2626 !important; background:#fef2f2 !important; }
                .error-msg { color:#dc2626; font-size:11px; font-weight:600; margin-top:2px; display:block; }
            </style>

            <div class="form-grid">
                <label>Número de ítem *
                    <input name="numero_item" maxlength="20" required
                           class="{{ $errors->has('numero_item') ? 'input-error' : '' }}"
                           value="{{ old('numero_item', $item->numero_item) }}">
                    @error('numero_item') <span class="error-msg">{{ $message }}</span> @enderror
                </label>

                <label>Carrera
                    <select name="id_carrera">
                        <option value="">— Sin carrera —</option>
                        @foreach ($carreras as $c)
                            <option value="{{ $c->id_carrera }}" {{ old('id_carrera', $item->id_carrera) == $c->id_carrera ? 'selected' : '' }}>
                                {{ $c->nombre }}
                            </option>
                        @endforeach
                    </select>
                </label>

                <label class="full">Ambiente
                    <select name="id_ambiente">
                        <option value="">— Sin ambiente —</option>
                        @foreach ($ambientes as $a)
                            <option value="{{ $a->id_ambiente }}" {{ old('id_ambiente', $item->id_ambiente) == $a->id_ambiente ? 'selected' : '' }}>
                                {{ $a->nombre }} — [{{ optional($a->carrera)->nombre ?? 'Sin carrera' }}]
                            </option>
                        @endforeach
                    </select>
                    <span style="font-size:11px;color:#64748b;font-weight:400;">
                        Sus custodios son quienes firman la liberación de ese ambiente.
                    </span>
                </label>

                <label class="full">Descripción
                    <input name="descripcion" maxlength="150" value="{{ old('descripcion', $item->descripcion) }}">
                    @error('descripcion') <span class="error-msg">{{ $message }}</span> @enderror
                </label>

                <label class="full">Unidad
                    <input name="unidad" maxlength="100" value="{{ old('unidad', $item->unidad) }}">
                    @error('unidad') <span class="error-msg">{{ $message }}</span> @enderror
                </label>
            </div>

            <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;margin-top:24px;border-top:1px solid #e2e8f0;padding-top:18px;">
                <div>
                    <a href="{{ route('items.index') }}"
                       style="background:#e2e8f0;color:#475569;border:none;padding:11px 22px;border-radius:8px;font-weight:700;text-decoration:none;display:inline-block;">
                        Cancelar
                    </a>
                </div>
                <button type="submit"
                        style="background:#2563eb;color:white;border:none;padding:11px 22px;border-radius:8px;font-weight:700;cursor:pointer;">
                    💾 Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>
</div>
@endsection
