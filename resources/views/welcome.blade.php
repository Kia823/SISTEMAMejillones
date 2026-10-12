@extends('layouts.app')

@section('title', 'Inicio - Panel Principal')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- ============================================================ --}}
    {{-- HERO CORPORATIVO SUPERIOR --}}
    {{-- ============================================================ --}}
    <div style="background: linear-gradient(135deg, #0f766e 0%, #0d9488 50%, #1e3a8a 100%); padding: 32px; border-radius: 20px; color: white; display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 24px; align-items: center; margin-bottom: 25px; box-shadow: 0 10px 20px -3px rgba(13, 148, 136, 0.25);">

        {{-- Lado Izquierdo: Textos e Identidad --}}
        <div>
            <span style="display: inline-block; padding: 5px 14px; border-radius: 999px; background: rgba(255, 255, 255, 0.2); font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: .08em; margin-bottom: 12px; backdrop-filter: blur(4px);">
                Panel Institucional
            </span>
            <h1 style="margin: 0 0 8px 0; font-size: 26px; font-weight: 800; line-height: 1.2;">
                @auth
                    Bienvenido, {{ auth()->user()->nombre_completo ?? 'Administrador' }}
                @else
                    Bienvenido al Sistema SISActivos
                @endauth
            </h1>
            <p style="margin: 0 0 16px 0; color: rgba(255, 255, 255, 0.9); font-size: 13px; font-weight: 500;">
                Sistema de Control y Gestión de Inventarios de Laboratorio
            </p>
            <p style="margin: 0; color: rgba(255, 255, 255, 0.85); font-size: 13px; line-height: 1.5;">
                Monitoreo en tiempo real del estado físico de los bienes y flujos de activos registrados en el instituto.
            </p>
        </div>

        {{-- Lado Derecho: Tarjetas Estadísticas 2x2 --}}
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px;">
            <div style="background: rgba(255, 255, 255, 0.15); padding: 16px; border-radius: 14px; border: 1px solid rgba(255, 255, 255, 0.22); backdrop-filter: blur(4px);">
                <div style="font-size: 26px; font-weight: 800; line-height: 1.2;">{{ $totalActivos ?? 7 }}</div>
                <div style="font-size: 11px; color: rgba(255, 255, 255, 0.85); font-weight: 700; text-transform: uppercase; margin-top: 4px;">Total Activos</div>
            </div>

            <div style="background: rgba(255, 255, 255, 0.15); padding: 16px; border-radius: 14px; border: 1px solid rgba(255, 255, 255, 0.22); backdrop-filter: blur(4px);">
                <div style="font-size: 26px; font-weight: 800; line-height: 1.2; color: #86efac;">{{ $activosBuenos ?? 5 }}</div>
                <div style="font-size: 11px; color: rgba(255, 255, 255, 0.85); font-weight: 700; text-transform: uppercase; margin-top: 4px;">En Buen Estado</div>
            </div>

            <div style="background: rgba(255, 255, 255, 0.15); padding: 16px; border-radius: 14px; border: 1px solid rgba(255, 255, 255, 0.22); backdrop-filter: blur(4px);">
                <div style="font-size: 26px; font-weight: 800; line-height: 1.2; color: #fde047;">{{ $enReparacion ?? 0 }}</div>
                <div style="font-size: 11px; color: rgba(255, 255, 255, 0.85); font-weight: 700; text-transform: uppercase; margin-top: 4px;">En Reparación</div>
            </div>

            <div style="background: rgba(255, 255, 255, 0.15); padding: 16px; border-radius: 14px; border: 1px solid rgba(255, 255, 255, 0.22); backdrop-filter: blur(4px);">
                <div style="font-size: 26px; font-weight: 800; line-height: 1.2; color: #fca5a5;">{{ ($enMantenimiento ?? 0) + ($fueraServicio ?? 2) }}</div>
                <div style="font-size: 11px; color: rgba(255, 255, 255, 0.85); font-weight: 700; text-transform: uppercase; margin-top: 4px;">Atención / Baja</div>
            </div>
        </div>

    </div>

    {{-- ============================================================ --}}
    {{-- SECCIÓN CENTRAL: GRÁFICA ESTADÍSTICA MENSUAL Y ESTADOS --}}
    {{-- ============================================================ --}}
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 25px;">

        {{-- Gráfica Estadística Mensual (Altas vs Bajas) --}}
        <div style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <div>
                    <h2 style="font-size: 16px; font-weight: 800; color: #0f172a; margin: 0;">Flujo Mensual de Activos</h2>
                    <p style="font-size: 12px; color: #64748b; margin: 2px 0 0 0;">Comparativa mensual de altas (nuevos) y bajas registradas</p>
                </div>
                <span style="font-size: 11px; background: #f1f5f9; color: #475569; padding: 4px 10px; border-radius: 8px; font-weight: 700;">Gestión {{ date('Y') }}</span>
            </div>
            <div style="position: relative; height: 260px; width: 100%;">
                <canvas id="graficaActivosMensual"></canvas>
            </div>
        </div>

        {{-- Panel de Desglose por Estado Físico --}}
        <div style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <h2 style="font-size: 16px; font-weight: 800; color: #0f172a; margin: 0 0 4px 0;">Estado de Inventario</h2>
                <p style="font-size: 12px; color: #64748b; margin: 0 0 16px 0;">Distribución actual por condición física.</p>

                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <a href="{{ route('activos.index') }}" style="display: flex; justify-content: space-between; align-items: center; padding: 10px 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; text-decoration: none; color: #1e293b; font-size: 13px; font-weight: 700;">
                        <span>🟢 Buenos</span>
                        <span style="background: #dcfce7; color: #166534; padding: 2px 8px; border-radius: 6px; font-size: 12px;">{{ $activosBuenos ?? 5 }}</span>
                    </a>
                    <a href="{{ route('activos.index') }}" style="display: flex; justify-content: space-between; align-items: center; padding: 10px 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; text-decoration: none; color: #1e293b; font-size: 13px; font-weight: 700;">
                        <span>🟡 En Reparación</span>
                        <span style="background: #fef9c3; color: #854d0e; padding: 2px 8px; border-radius: 6px; font-size: 12px;">{{ $enReparacion ?? 0 }}</span>
                    </a>
                    <a href="{{ route('activos.index') }}" style="display: flex; justify-content: space-between; align-items: center; padding: 10px 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; text-decoration: none; color: #1e293b; font-size: 13px; font-weight: 700;">
                        <span>🟠 Mantenimiento</span>
                        <span style="background: #ffedd5; color: #9a3412; padding: 2px 8px; border-radius: 6px; font-size: 12px;">{{ $enMantenimiento ?? 0 }}</span>
                    </a>
                    <a href="{{ route('activos.index') }}" style="display: flex; justify-content: space-between; align-items: center; padding: 10px 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; text-decoration: none; color: #1e293b; font-size: 13px; font-weight: 700;">
                        <span>🔴 Fuera de servicio</span>
                        <span style="background: #fee2e2; color: #991b1b; padding: 2px 8px; border-radius: 6px; font-size: 12px;">{{ $fueraServicio ?? 2 }}</span>
                    </a>
                </div>
            </div>
        </div>

    </div>

    {{-- ============================================================ --}}
    {{-- ÚLTIMAS ACTIVIDADES DEL SISTEMA --}}
    {{-- ============================================================ --}}
    @if(!empty($recentActivities) && $recentActivities->count())
    <div style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);">
        <h2 style="font-size: 16px; font-weight: 800; color: #0f172a; margin: 0 0 12px 0;">Últimas Actividades Registradas</h2>
        <div style="display: flex; flex-direction: column; gap: 8px;">
            @foreach($recentActivities as $act)
                <div style="padding: 10px 14px; background: #f8fafc; border-radius: 8px; border: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; font-size: 13px;">
                    <span style="color: #334155; font-weight: 600;">
                        ⚡ <strong>{{ $act->accion }}</strong> en tabla <em>{{ $act->tabla_afectada }}</em> (ID: {{ $act->registro_id }})
                    </span>
                    <span style="color: #64748b; font-size: 11px; font-weight: 700;">
                        {{ $act->fecha_cambio->format('d/m/Y H:i') }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>
    @endif

</div>

{{-- SCRIPT PARA RENDERIZAR LA GRÁFICA ESTADÍSTICA CON CHART.JS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('graficaActivosMensual').getContext('2d');

        // Datos dinámicos mensuales (puedes pasarlos desde tu controlador o usar valores base institucionales)
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                datasets: [
                    {
                        label: 'Activos Nuevos (Altas)',
                        data: [2, 4, 3, 5, 7, 6, 8, 4, 6, 9, 5, 7],
                        borderColor: '#0d9488',
                        backgroundColor: 'rgba(13, 148, 136, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.3
                    },
                    {
                        label: 'Activos de Baja',
                        data: [0, 1, 0, 2, 1, 0, 1, 2, 0, 1, 0, 1],
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.05)',
                        borderWidth: 2,
                        borderDash: [4, 4],
                        fill: false,
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: { size: 11, weight: 'bold' },
                            color: '#475569'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: { font: { size: 11 }, color: '#64748b' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 }, color: '#64748b' }
                    }
                }
            }
        });
    });
</script>
@endsection
