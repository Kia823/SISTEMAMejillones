@extends('layouts.app')

@section('title', 'Actas de Transferencia')

@section('content')
<div class="container-fluid px-4 py-6">

    {{-- ENCABEZADO CORPORATIVO --}}
    <div style="background: linear-gradient(135deg, #c2410c 0%, #ea580c 100%); padding: 30px; border-radius: 18px; color: white; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; margin-bottom: 24px; box-shadow: 0 10px 20px -3px rgba(234, 88, 12, 0.25);">
        <div>
            <span style="background: rgba(255, 255, 255, 0.2); padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">Módulo Legal</span>
            <h1 style="font-size: 26px; font-weight: 800; margin: 8px 0 4px 0;">Actas de Transferencia Interna</h1>
            <p style="opacity: 0.9; font-size: 14px; margin: 0;">Gestiona y controla los traslados de activos entre ambientes y custodios.</p>
        </div>
        <div>
            <a href="{{ route('actas.transferencia.create') }}"
               style="background: white; color: #c2410c; padding: 12px 20px; border-radius: 12px; font-weight: 800; font-size: 13px; text-decoration: none; box-shadow: 0 4px 6px rgba(0,0,0,0.1); display: inline-flex; align-items: center; gap: 8px;">
                <span>➕</span> Nueva Transferencia
            </a>
        </div>
    </div>

    @if(session('status'))
        <div style="background: #dcfce7; border: 1px solid #bbf7d0; color: #166534; padding: 14px 20px; border-radius: 12px; margin-bottom: 20px; font-weight: 700;">
            ✓ {{ session('status') }}
        </div>
    @endif

    {{-- TABLA DE REGISTROS --}}
    <div style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
                <thead style="background: #f8fafc; color: #475569; text-transform: uppercase; font-size: 10px; letter-spacing: 0.5px; border-bottom: 1px solid #e2e8f0;">
                    <tr>
                        <th style="padding: 14px 16px;">N° Acta</th>
                        <th style="padding: 14px 16px;">Activo</th>
                        <th style="padding: 14px 16px;">Origen</th>
                        <th style="padding: 14px 16px;">Destino</th>
                        <th style="padding: 14px 16px;">Fecha</th>
                        <th style="padding: 14px 16px; text-align: center;">Acciones</th>
                    </tr>
                </thead>
                <tbody style="divide-y: #f1f5f9;">
                    @forelse($transferencias as $t)
                        <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                            <td style="padding: 14px 16px; font-family: monospace; font-weight: 800; color: #c2410c;">
                                {{ $t->numero_acta }}
                            </td>
                            <td style="padding: 14px 16px;">
                                <div style="font-weight: 700; color: #1e293b;">{{ $t->activo->nombre ?? '—' }}</div>
                                <div style="font-size: 11px; color: #64748b; font-family: monospace;">{{ $t->activo->codigo_activo ?? '' }}</div>
                            </td>
                            <td style="padding: 14px 16px; color: #334155; font-size: 12px;">
                                <div style="font-weight: 700;">{{ $t->origenAmbiente->nombre ?? '—' }}</div>
                                <div style="color: #64748b; font-size: 11px;">{{ $t->custodioAnterior->nombre_completo ?? '—' }}</div>
                            </td>
                            <td style="padding: 14px 16px; color: #334155; font-size: 12px;">
                                <div style="font-weight: 700; color: #059669;">{{ $t->destinoAmbiente->nombre ?? '—' }}</div>
                                <div style="color: #64748b; font-size: 11px;">{{ $t->custodioNuevo->nombre_completo ?? '—' }}</div>
                            </td>
                            <td style="padding: 14px 16px; color: #475569; font-size: 12px; font-weight: 600;">
                                {{ $t->fecha_transferencia ? \Carbon\Carbon::parse($t->fecha_transferencia)->format('d/m/Y') : '—' }}
                            </td>
                            <td style="padding: 14px 16px; text-align: center;">
                                <div style="display: flex; gap: 6px; justify-content: center;">
                                    <a href="{{ route('actas.transferencia.show', $t->id_acta_transferencia ?? $t) }}"
                                       style="padding: 6px 12px; background: #eff6ff; color: #1d4ed8; border-radius: 8px; font-size: 11px; font-weight: 700; text-decoration: none;">
                                        👁️ Ver
                                    </a>
                                    <a href="{{ route('actas.transferencia.pdf', $t->id_acta_transferencia ?? $t) }}" target="_blank"
                                       style="padding: 6px 12px; background: #fee2e2; color: #991b1b; border-radius: 8px; font-size: 11px; font-weight: 700; text-decoration: none;">
                                        📄 PDF
                                    </a>
                                    <a href="{{ route('actas.transferencia.excel', $t) }}" target="_blank" title="Descargar Excel"
           style="background: #dcfce7; color: #166534; padding: 6px 10px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 700;">
            📊 Excel
        </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 50px 20px; color: #64748b; font-weight: 600;">
                                No hay transferencias registradas en el sistema.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($transferencias->hasPages())
        <div style="margin-top: 20px;">{{ $transferencias->links() }}</div>
    @endif
</div>
@endsection
