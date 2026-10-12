@extends('layouts.app')

@section('title', 'Configuración del Sistema')

@section('content')
<div class="container-fluid px-4 py-4" style="color: #0f172a;">

    {{-- ENCABEZADO CORPORATIVO --}}
    <div style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); padding: 30px; border-radius: 18px; color: white; margin-bottom: 25px; box-shadow: 0 10px 20px -3px rgba(15, 23, 42, 0.25);">
        <span style="background: rgba(255, 255, 255, 0.15); padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">Panel de Control</span>
        <h1 style="font-size: 26px; font-weight: 800; margin: 8px 0 4px 0; color: #ffffff;">Configuración General del Sistema</h1>
        <p style="opacity: 0.9; font-size: 14px; margin: 0; color: #cbd5e1;">Administra los parámetros institucionales y los prefijos globales de SISActivos.</p>
    </div>

    {{-- ALERTA TAILWIND DE ÉXITO --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center gap-3 shadow-sm" role="alert">
            <span class="text-xl">✅</span>
            <div class="text-sm font-semibold">{{ session('success') }}</div>
        </div>
    @endif

    {{-- CONTENEDOR DE FORMULARIOS --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(450px, 1fr)); gap: 24px;">

        {{-- 1. DATOS INSTITUCIONALES --}}
        <div style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; padding: 26px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px; border-bottom: 2px solid #f1f5f9; padding-bottom: 12px;">
                <span style="font-size: 20px;">🏛️</span>
                <h3 style="font-size: 16px; font-weight: 800; color: #000000; margin: 0;">Datos Institucionales</h3>
            </div>

            <form action="{{ route('configuracion.update') }}" method="POST" style="display: flex; flex-direction: column; gap: 16px;">
                @csrf
                @method('PUT')

                <div>
                    <label style="display: block; font-size: 12px; font-weight: 800; color: #000000; margin-bottom: 6px; text-transform: uppercase;">Nombre de la Institución</label>
                    <input type="text" name="institucion_nombre" value="{{ $config->institucion_nombre ?? 'Instituto Tecnológico \'Puerto de Mejillones\'' }}" style="width: 100%; padding: 10px 14px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; color: #000000; font-weight: 600; outline: none;">
                </div>

                <div>
                    <label style="display: block; font-size: 12px; font-weight: 800; color: #000000; margin-bottom: 6px; text-transform: uppercase;">Dirección / Ubicación</label>
                    <input type="text" name="institucion_direccion" value="{{ $config->institucion_direccion ?? 'El Alto, La Paz - Bolivia' }}" style="width: 100%; padding: 10px 14px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; color: #000000; font-weight: 600; outline: none;">
                </div>

                <div>
                    <label style="display: block; font-size: 12px; font-weight: 800; color: #000000; margin-bottom: 6px; text-transform: uppercase;">Director / Rector Actual</label>
                    <input type="text" name="institucion_director" value="{{ $config->institucion_director ?? 'Lic. Jimmy Ovidio Sirpa Choque' }}" style="width: 100%; padding: 10px 14px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; color: #000000; font-weight: 600; outline: none;">
                </div>

                <div style="text-align: right; margin-top: 10px;">
                    <button type="submit" style="background: #2563eb; color: white; border: none; padding: 10px 20px; border-radius: 10px; font-size: 13px; font-weight: 700; cursor: pointer; transition: background 0.2s;">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>

        {{-- 2. PARÁMETROS DE ACTIVOS Y QR --}}
        <div style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; padding: 26px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px; border-bottom: 2px solid #f1f5f9; padding-bottom: 12px;">
                <span style="font-size: 20px;">⚙️</span>
                <h3 style="font-size: 16px; font-weight: 800; color: #000000; margin: 0;">Parámetros de Activos y QR</h3>
            </div>

            <form action="{{ route('configuracion.updateParametros') }}" method="POST" style="display: flex; flex-direction: column; gap: 16px;">
                @csrf
                @method('PUT')

                <div>
                    <label style="display: block; font-size: 12px; font-weight: 800; color: #000000; margin-bottom: 6px; text-transform: uppercase;">Gestión Fiscal Activa</label>
                    <input type="text" name="gestion_fiscal" value="{{ $config->gestion_fiscal ?? '2026' }}" style="width: 100%; padding: 10px 14px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; color: #000000; font-weight: 700;">
                </div>

                <div>
                    <label style="display: block; font-size: 12px; font-weight: 800; color: #000000; margin-bottom: 6px; text-transform: uppercase;">Prefijo para Códigos de Activo</label>
                    <input type="text" name="prefijo_activo" value="{{ $config->prefijo_activo ?? 'TPM-' }}" style="width: 100%; padding: 10px 14px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; color: #000000; font-weight: 600; outline: none;">
                    <span style="font-size: 11px; color: #64748b; margin-top: 4px; display: block;">Este prefijo se autocompletará en el formulario al registrar un nuevo activo.</span>
                </div>

                <div style="background: #f8fafc; padding: 14px; border-radius: 10px; border: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div style="font-weight: 800; color: #000000; font-size: 13px;">Validación Estricta de QR</div>
                        <div style="font-size: 11px; color: #334155;">Requerir escaneo obligatorio en auditorías físicas por ambiente.</div>
                    </div>
                    <input type="checkbox" name="validacion_estricta" value="1" {{ ($config->validacion_estricta ?? true) ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: #2563eb; cursor: pointer;">
                </div>

                <div style="text-align: right; margin-top: 10px;">
                    <button type="submit" style="background: #0f172a; color: white; border: none; padding: 10px 20px; border-radius: 10px; font-size: 13px; font-weight: 700; cursor: pointer;">
                        Actualizar Parámetros
                    </button>
                </div>
            </form>
        </div>

    </div>

</div>
@endsection
