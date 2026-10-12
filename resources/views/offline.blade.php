@extends('layouts.app')

@section('title', 'Sin conexión')

@section('content')
<div class="container-fluid px-4 py-6">
    <div style="max-width: 620px; margin: 40px auto; background: white; border: 1px solid #e2e8f0; border-radius: 18px; padding: 40px; text-align: center; box-shadow: 0 10px 20px -3px rgba(0,0,0,0.08);">
        <div style="font-size: 56px; margin-bottom: 12px;">📡</div>

        <h1 style="font-size: 22px; font-weight: 800; color: #0f172a; margin: 0 0 10px 0;">Estás sin conexión</h1>

        <p style="color: #475569; font-size: 14px; line-height: 1.6; margin: 0 0 24px 0;">
            No te preocupes: lo que escanees se guarda en este equipo y se enviará solo
            en cuanto vuelva la señal. Puedes seguir verificando activos con normalidad.
        </p>

        <div id="offline-resumen" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; margin-bottom: 24px;">
            <p style="margin: 0; color: #64748b; font-size: 13px; font-weight: 600;">Cargando pendientes…</p>
        </div>

        <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
            <a href="{{ route('verificaciones.index') }}"
               style="background: #2563eb; color: white; padding: 12px 22px; border-radius: 12px; font-weight: 800; font-size: 13px; text-decoration: none;">
                🔍 Ir a verificación física
            </a>
            <button type="button" onclick="window.location.reload()"
                    style="background: #f1f5f9; color: #334155; padding: 12px 22px; border: 1px solid #cbd5e1; border-radius: 12px; font-weight: 800; font-size: 13px; cursor: pointer;">
                🔄 Reintentar ahora
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (!window.InventarioOffline) {
        return;
    }

    window.InventarioOffline.pendientes().then(function (total) {
        document.getElementById('offline-resumen').innerHTML =
            '<p style="margin:0; color:#0f172a; font-size:20px; font-weight:800;">' + total + '</p>' +
            '<p style="margin:4px 0 0 0; color:#64748b; font-size:12px;">verificaciones esperando enviarse</p>';
    });
});
</script>
@endpush
@endsection