@extends('layouts.app')

@section('content')
<div style="max-width: 500px; margin: 0 auto; background:#ffffff; border-radius:18px; padding:28px; box-shadow:0 4px 12px rgba(0,0,0,0.05);">

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <div>
            <h2 style="font-size:24px; font-weight:800; color:#0f172a; margin:0;">Editar Carrera</h2>
            <p style="color:#64748b; font-size:14px; margin:4px 0 0 0;">Modifica los datos de la carrera {{ $carrera->nombre }}.</p>
        </div>
        <a href="{{ route('carreras.index') }}" style="background:#e2e8f0; color:#334155; padding:8px 16px; border-radius:10px; font-weight:700; border:none; cursor:pointer; text-decoration:none;">← Volver</a>
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

    <form method="POST" action="{{ route('carreras.update', $carrera->id_carrera) }}">
        @csrf
        @method('PUT')

        <label style="display:flex; flex-direction:column; font-size:12px; font-weight:700; color:#334155; gap:4px; margin-bottom:16px;">
            Código *
            <input type="text" id="codigo" name="codigo" maxlength="20" style="text-transform:uppercase;" value="{{ old('codigo', $carrera->codigo) }}"
                   placeholder="Ej. SIF">
            <span class="error-msg" data-for="codigo"></span>
        </label>

        <label style="display:flex; flex-direction:column; font-size:12px; font-weight:700; color:#334155; gap:4px; margin-bottom:24px;">
            Nombre *
            <input type="text" id="nombre" name="nombre" maxlength="100" value="{{ old('nombre', $carrera->nombre) }}"
                   placeholder="Ej. Sistemas Informáticos">
            <span class="error-msg" data-for="nombre"></span>
        </label>

        <div style="display:flex; justify-content:flex-end; gap:10px;">
            <a href="{{ route('carreras.index') }}" style="background:#e2e8f0; color:#475569; border:none; padding:10px 20px; border-radius:8px; font-weight:700; cursor:pointer; text-decoration:none;">Cancelar</a>
            <button type="submit" style="background:#2563eb; color:white; border:none; padding:10px 20px; border-radius:8px; font-weight:700; cursor:pointer;">💾 Guardar Cambios</button>
        </div>
    </form>

    @if(auth()->check() && auth()->user()->esAdmin())
        <form action="{{ route('carreras.destroy', $carrera->id_carrera) }}" method="POST" style="margin-top:12px; text-align:right;" onsubmit="return confirm('¿Eliminar la carrera {{ $carrera->nombre }}? Esta acción no se puede deshacer.');">
            @csrf
            @method('DELETE')
            <button type="submit" style="background:#dc2626; color:white; border:none; padding:10px 20px; border-radius:8px; font-weight:700; cursor:pointer;">Eliminar carrera</button>
        </form>
    @endif

    <style>
        input { width:100%; padding:10px 14px; border-radius:8px; border:1px solid #cbd5e1; font-size:14px; background:#fff; box-sizing:border-box; }
        input:focus { outline:none; border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,.1); }
        .error-msg { color:#dc2626; font-size:11px; font-weight:600; margin-top:2px; display:none; }
        .error-msg.visible { display:block; }
    </style>

    <script>
        document.querySelectorAll('form input').forEach(el => {
            if (!el.id) return;
            el.addEventListener('input', function() {
                const span = document.querySelector('.error-msg[data-for="' + el.id + '"]');
                if (span) { span.classList.remove('visible'); span.textContent = ''; }
                el.classList.remove('input-error');
            });
        });
    </script>
</div>
@endsection