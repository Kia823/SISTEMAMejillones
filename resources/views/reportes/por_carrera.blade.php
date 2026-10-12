@extends('layouts.app')

@section('title', 'Inventario por Carrera')

@section('content')
<div class="container-fluid px-4 py-6">
    <div style="max-width: 1200px; margin: 0 auto;">

        <a href="{{ route('reportes.index') }}"
           style="display: inline-flex; align-items: center; gap: 6px; color: #64748b; font-size: 13px; font-weight: 700; text-decoration: none; margin-bottom: 16px;">
            ← Volver a Reportes
        </a>

        {{-- ENCABEZADO --}}
        <div style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); padding: 25px 30px; border-radius: 16px; color: white; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; margin-bottom: 25px; box-shadow: 0 10px 20px -3px rgba(15, 23, 42, 0.2);">
            <div>
                <span style="background: rgba(255, 255, 255, 0.15); padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">Reporte Institucional</span>
                <h1 style="font-size: 24px; font-weight: 800; margin: 6px 0 4px 0;">{{ $carrera->nombre }}[cite: 19]</h1>
                <p style="opacity: 0.85; font-size: 13px; margin: 0;">{{ $ambientes->count() }} ambientes registrados[cite: 19]</p>
            </div>
            <div>
                <a href="{{ route('reportes.por.carrera.pdf', $carrera->id_carrera) }}" target="_blank"
                   style="background: #dc2626; color: white; padding: 10px 16px; border-radius: 10px; font-size: 13px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 2px 4px rgba(220, 38, 38, 0.2);">
                    📄 Descargar PDF
                </a>
            </div>
        </div>

        {{-- AMBIENTES GRID --}}
        <h2 style="font-size: 16px; font-weight: 800; color: #0f172a; margin-bottom: 15px;">Ambientes de la Carrera</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 15px; margin-bottom: 25px;">
            @foreach($ambientes as $amb)
                <a href="{{ route('reportes.aula', $amb->id_ambiente) }}"[cite: 19]
                   style="background: white; padding: 18px; border-radius: 14px; border: 1px solid #e2e8f0; text-decoration: none; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.01); transition: all 0.2s;"
                   onmouseover="this.style.borderColor='#2563eb'; this.style.transform='translateY(-2px)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.transform='translateY(0)'">
                    <div>
                        <div style="font-weight: 700; color: #1e293b; font-size: 14px;">{{ $amb->nombre }}[cite: 19]</div>
                        <div style="font-size: 12px; color: #64748b; margin-top: 4px;">{{ $amb->activos_count }} activos[cite: 19]</div>
                    </div>
                    <span style="color: #cbd5e1; font-weight: bold;">→</span>
                </a>
            @endforeach
        </div>

        {{-- RESUMEN --}}
        <div style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);">
            <div style="padding: 20px 24px; border-bottom: 1px solid #e2e8f0;">
                <h3 style="font-size: 16px; font-weight: 800; color: #0f172a; margin: 0;">Resumen por categoría / ambiente</h3>[cite: 19]
            </div>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
                    <thead style="background: #f8fafc; color: #475569; text-transform: uppercase; font-size: 10px; letter-spacing: 0.5px; border-bottom: 1px solid #e2e8f0;">
                        <tr>
                            <th style="padding: 14px 16px;">Categoría</th>
                            <th style="padding: 14px 16px;">Ambiente</th>
                            <th style="padding: 14px 16px; text-align: center;">Total</th>
                            <th style="padding: 14px 16px; text-align: center;">B</th>
                            <th style="padding: 14px 16px; text-align: center;">R</th>
                            <th style="padding: 14px 16px; text-align: center;">M</th>
                            <th style="padding: 14px 16px; text-align: center;">FF</th>
                        </tr>
                    </thead>
                    <tbody style="divide-y: #f1f5f9;">
                        @forelse($resumen as $r)
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 14px 16px; font-weight: 700; color: #0f172a;">{{ $r->categoria }}[cite: 19]</td>
                                <td style="padding: 14px 16px; color: #64748b;">{{ $r->ambiente }}[cite: 19]</td>
                                <td style="padding: 14px 16px; text-align: center; font-weight: 800; color: #1d4ed8;">{{ $r->total }}[cite: 19]</td>
                                <td style="padding: 14px 16px; text-align: center; font-weight: 700; color: #059669;">{{ $r->buenos }}[cite: 19]</td>
                                <td style="padding: 14px 16px; text-align: center; font-weight: 700; color: #d97706;">{{ $r->regulares }}[cite: 19]</td>
                                <td style="padding: 14px 16px; text-align: center; font-weight: 700; color: #ea580c;">{{ $r->malos }}[cite: 19]</td>
                                <td style="padding: 14px 16px; text-align: center; font-weight: 700; color: #dc2626;">{{ $r->fuera }}[cite: 19]</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" style="text-align: center; padding: 40px; color: #64748b; font-weight: 600;">Sin datos[cite: 19]</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
