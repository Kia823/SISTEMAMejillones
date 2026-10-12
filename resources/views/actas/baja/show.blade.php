@extends('layouts.app')

@section('title', 'Acta de Baja #' . ($acta->numero_acta ?? ''))

@section('content')
<div class="container-fluid px-4 py-6">
    <div style="max-width: 900px; margin: 0 auto;">

        {{-- BARRA DE NAVEGACIÓN Y ACCIONES --}}
        <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
            <a href="{{ route('actas.baja.index') }}"
               style="display: inline-flex; align-items: center; gap: 6px; color: #7c2d12; font-weight: 700; font-size: 13px; text-decoration: none; background: #fff7ed; padding: 8px 14px; border-radius: 10px; border: 1px solid #ffedd5;">
                ← Volver a Actas de Baja
            </a>
            <div style="display: flex; gap: 10px;">
                <a href="{{ route('actas.baja.pdf', $acta->id_acta_baja ?? $acta->id) }}" target="_blank"
                   style="background: #dc2626; color: white; padding: 10px 18px; border-radius: 10px; font-size: 13px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 4px 6px rgba(220, 38, 38, 0.2);">
                    📄 Descargar PDF
                </a>
                <a href="{{ route('actas.baja.excel', $acta) }}" target="_blank"
           style="background: #16a34a; color: white; padding: 10px 18px; border-radius: 8px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; font-size: 13px;">
            📊 Descargar Excel
        </a>
            </div>
        </div>

        {{-- DETALLES DEL ACTA --}}
        <div style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; padding: 30px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02); margin-bottom: 25px;">
            <div style="border-bottom: 1px solid #f1f5f9; padding-bottom: 16px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                <div>
                    <span style="font-size: 11px; font-weight: 800; background: #fff7ed; color: #7c2d12; padding: 4px 10px; border-radius: 6px; text-transform: uppercase;">Acta de Baja Oficial</span>
                    <h1 style="font-size: 22px; font-weight: 800; color: #0f172a; margin: 6px 0 0 0;">{{ $acta->numero_acta ?? '—' }}</h1>
                </div>
                <div style="text-align: right; color: #64748b; font-size: 12px; font-weight: 600;">
                    Fecha: {{ optional($acta->fecha_baja ?? $acta->fecha_acta)->format('d/m/Y') ?? ($acta->created_at?->format('d/m/Y') ?? '—') }}
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px; margin-bottom: 20px;">
                <div style="background: #f8fafc; padding: 14px 16px; border-radius: 10px; border: 1px solid #e2e8f0;">
                    <div style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase;">Solicitante / Responsable</div>
                    <div style="font-weight: 700; color: #1e293b; font-size: 13px; margin-top: 4px;">
                        {{ optional($acta->solicitante ?? $acta->usuario)->nombre_completo ?? 'Administrador' }}
                        <span style="font-weight: 400; color: #64748b;">({{ optional($acta->solicitante ?? $acta->usuario)->rol ?? 'ADMINISTRADOR' }})</span>
                    </div>
                </div>
                <div style="background: #f8fafc; padding: 14px 16px; border-radius: 10px; border: 1px solid #e2e8f0;">
                    <div style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase;">Ambiente</div>
                    <div style="font-weight: 700; color: #1e293b; font-size: 13px; margin-top: 4px;">{{ optional($acta->ambiente)->nombre ?? optional(optional($acta->activo)->ambiente)->nombre ?? 'Institucional' }}</div>
                </div>
                <div style="background: #f8fafc; padding: 14px 16px; border-radius: 10px; border: 1px solid #e2e8f0;">
                    <div style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase;">Estado</div>
                    <div style="font-weight: 700; color: #1e293b; font-size: 13px; margin-top: 4px;">{{ $acta->estado ?? 'FINALIZADO' }}</div>
                </div>
            </div>

            <div style="margin-bottom: 16px;">
                <div style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 4px;">Motivo de la Baja</div>
                <p style="font-size: 13px; color: #334155; background: #fef2f2; border: 1px solid #fecaca; padding: 12px 14px; border-radius: 10px; margin: 0; font-weight: 600;">{{ $acta->motivo_baja ?? $acta->motivo ?? 'Sin motivo especificado' }}</p>
            </div>

            @if($acta->observaciones ?? null)
                <div>
                    <div style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 4px;">Observaciones</div>
                    <p style="font-size: 13px; color: #334155; background: #f8fafc; border: 1px solid #e2e8f0; padding: 12px 14px; border-radius: 10px; margin: 0;">{{ $acta->observaciones }}</p>
                </div>
            @endif
        </div>

       {{-- TABLA DE ACTIVOS INCLUIDOS --}}
<div style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);">
    <div style="padding: 20px 24px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
        <h3 style="font-size: 16px; font-weight: 800; color: #0f172a; margin: 0;">Activo Incluido</h3>
        @php
            $listaActivos = $acta->activo ? collect([$acta->activo]) : collect();
        @endphp
        <span style="background: #f1f5f9; color: #334155; padding: 4px 10px; border-radius: 8px; font-size: 12px; font-weight: 700;">{{ $listaActivos->count() }} ítem(s)</span>
    </div>
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
            <thead style="background: #f8fafc; color: #475569; text-transform: uppercase; font-size: 10px; letter-spacing: 0.5px; border-bottom: 1px solid #e2e8f0;">
                <tr>
                    <th style="padding: 14px 16px;">Código</th>
                    <th style="padding: 14px 16px;">Nombre</th>
                    <th style="padding: 14px 16px;">Categoría</th>
                    <th style="padding: 14px 16px;">Ubicación</th>
                    <th style="padding: 14px 16px; text-align: right;">Valor</th>
                </tr>
            </thead>
            <tbody>
                @if($acta->activo)
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 14px 16px; font-family: monospace; font-weight: 800; color: #0f172a;">{{ $acta->activo->codigo_activo }}</td>
                    <td style="padding: 14px 16px; color: #334155; font-weight: 700;">{{ $acta->activo->nombre }}</td>
                    <td style="padding: 14px 16px; color: #475569;">{{ $acta->activo->categoria->nombre ?? '—' }}</td>
                    <td style="padding: 14px 16px; color: #475569;">{{ $acta->activo->ambiente->nombre ?? '—' }}</td>
                    <td style="padding: 14px 16px; text-align: right; font-weight: 700; color: #0f172a;">Bs.{{ number_format($acta->activo->valor_adquisicion ?? 0, 2) }}</td>
                </tr>
                @else
                <tr>
                    <td colspan="5" style="text-align: center; padding: 20px; color: #64748b;">No hay activo asociado.</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

        @if(session('status'))
            <div style="background: #dcfce7; border: 1px solid #bbf7d0; color: #166534; padding: 14px 20px; border-radius: 12px; margin-top: 20px; font-weight: 700;">
                ✓ {{ session('status') }}
            </div>
        @endif
    </div>
</div>
@endsection
