@extends('layouts.app')

@section('title', 'Acta ' . $acta->numero_acta)

@section('content')
<div class="container-fluid px-4 py-6">
    <div style="max-width: 800px; margin: 0 auto;">

        {{-- NAVEGACIÓN --}}
        <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
            <a href="{{ route('actas.transferencia.index') }}"
               style="display: inline-flex; align-items: center; gap: 6px; color: #ea580c; font-weight: 700; font-size: 13px; text-decoration: none; background: #fff7ed; padding: 8px 14px; border-radius: 10px; border: 1px solid #ffedd5;">
                ← Volver a Transferencias
            </a>
            <a href="{{ route('actas.transferencia.pdf', $acta->id_acta_transferencia ?? $acta) }}" target="_blank"
               style="background: #dc2626; color: white; padding: 10px 18px; border-radius: 10px; font-size: 13px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 4px 6px rgba(220, 38, 38, 0.2);">
                📄 Descargar Acta en PDF
            </a>
            <a href="{{ route('actas.transferencia.excel', $acta) }}" target="_blank"
       style="background: #16a34a; color: white; padding: 10px 18px; border-radius: 8px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; font-size: 13px;">
        📊 Descargar en Excel
    </a>
        </div>

        {{-- TARJETA PRINCIPAL --}}
        <div style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; padding: 30px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);">
            <div style="border-bottom: 1px solid #f1f5f9; padding-bottom: 16px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <span style="font-size: 11px; font-weight: 800; background: #fff7ed; color: #ea580c; padding: 4px 10px; border-radius: 6px; text-transform: uppercase;">Acta Oficial</span>
                    <h1 style="font-size: 22px; font-weight: 800; color: #0f172a; margin: 6px 0 0 0;">{{ $acta->numero_acta }}</h1>
                </div>
                <div style="text-align: right; color: #64748b; font-size: 12px; font-weight: 600;">
                    Fecha: {{ \Carbon\Carbon::parse($acta->fecha_transferencia)->format('d/m/Y') }}
                </div>
            </div>

            {{-- DATOS DEL ACTIVO --}}
            <div style="margin-bottom: 20px;">
                <h3 style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 8px;">Activo(s) Transferido(s)</h3>
                <div style="background: #f8fafc; padding: 16px; border-radius: 12px; border: 1px solid #e2e8f0; max-height: 300px; overflow-y: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                        <thead>
                            <tr style="background: #e2e8f0; color: #475569; text-transform: uppercase; font-size: 10px;">
                                <th style="padding: 8px; text-align: left; border: 1px solid #cbd5e1;">#</th>
                                <th style="padding: 8px; text-align: left; border: 1px solid #cbd5e1;">CÓDIGO</th>
                                <th style="padding: 8px; text-align: left; border: 1px solid #cbd5e1;">NOMBRE</th>
                                <th style="padding: 8px; text-align: left; border: 1px solid #cbd5e1;">MARCA / MODELO</th>
                                <th style="padding: 8px; text-align: left; border: 1px solid #cbd5e1;">ESTADO</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i = 1; @endphp
                            @foreach($acta->activos as $activo)
                            <tr style="border-bottom: 1px solid #e2e8f0;">
                                <td style="padding: 8px; border: 1px solid #cbd5e1; font-weight: 800;">{{ $i++ }}</td>
                                <td style="padding: 8px; border: 1px solid #cbd5e1; font-family: monospace;">{{ $activo->codigo_activo ?? '—' }}</td>
                                <td style="padding: 8px; border: 1px solid #cbd5e1; font-weight: 700;">{{ $activo->nombre ?? '—' }}</td>
                                <td style="padding: 8px; border: 1px solid #cbd5e1; color: #64748b; font-size: 12px;">{{ $activo->marca ?? 'S/M' }} / {{ $activo->modelo ?? 'S/Mod' }}</td>
                                <td style="padding: 8px; border: 1px solid #cbd5e1; color: #64748b;">{{ $activo->estado_fisico_texto ?? $activo->estado_fisico ?? '—' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div style="margin-top: 8px; font-size: 11px; font-weight: 800; color: #ea580c; text-align: right;">
                        Total: {{ $acta->activos->count() }} bien(es)
                    </div>
                </div>
            </div>

            {{-- ORIGEN Y DESTINO --}}
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; margin-bottom: 20px;">
                <div style="background: #fef2f2; padding: 16px; border-radius: 12px; border: 1px solid #fecaca;">
                    <div style="font-size: 11px; font-weight: 800; color: #991b1b; text-transform: uppercase; margin-bottom: 6px;">Origen (Salida)</div>
                    <div style="font-weight: 700; color: #7f1d1d; font-size: 14px;">{{ $acta->origenAmbiente->nombre ?? '—' }}</div>
                    <div style="font-size: 12px; color: #991b1b; margin-top: 4px;">Custodio: <strong>{{ $acta->custodioAnterior->nombre_completo ?? '—' }}</strong></div>
                </div>

                <div style="background: #ecfdf5; padding: 16px; border-radius: 12px; border: 1px solid #a7f3d0;">
                    <div style="font-size: 11px; font-weight: 800; color: #065f46; text-transform: uppercase; margin-bottom: 6px;">Destino (Entrada)</div>
                    <div style="font-weight: 700; color: #064e3b; font-size: 14px;">{{ $acta->destinoAmbiente->nombre ?? '—' }}</div>
                    <div style="font-size: 12px; color: #065f46; margin-top: 4px;">Custodio: <strong>{{ $acta->custodioNuevo->nombre_completo ?? '—' }}</strong></div>
                </div>
            </div>

            {{-- OBSERVACIONES --}}
            @if($acta->observaciones)
                <div>
                    <h3 style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 8px;">Observaciones</h3>
                    <p style="font-size: 13px; color: #334155; background: #f8fafc; padding: 14px; border-radius: 12px; border: 1px solid #e2e8f0; margin: 0;">{{ $acta->observaciones }}</p>
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
