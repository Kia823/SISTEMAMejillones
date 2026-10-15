@extends('layouts.app')

@section('title', 'Activos Dados de Baja')

@section('content')
<div class="container-fluid px-4 py-6">

    {{-- ENCABEZADO CORPORATIVO --}}
    <div style="background: linear-gradient(135deg, #7c2d12 0%, #9a3412 100%); padding: 30px; border-radius: 18px; color: white; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; margin-bottom: 25px; box-shadow: 0 10px 20px -3px rgba(124, 45, 18, 0.25);">
        <div>
            <span style="background: rgba(255, 255, 255, 0.2); padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">Inventario Institucional</span>
            <h1 style="font-size: 26px; font-weight: 800; margin: 8px 0 4px 0;">Activos Dados de Baja</h1>
            <p style="opacity: 0.9; font-size: 14px; margin: 0;">Historial y consulta de equipos retirados del inventario activo por desuso, obsolescencia o daño.</p>
        </div>
        <div>
            <a href="{{ route('actas.baja.index') }}"
               style="background: white; color: #7c2d12; padding: 12px 20px; border-radius: 12px; font-weight: 800; font-size: 13px; text-decoration: none; box-shadow: 0 4px 6px rgba(0,0,0,0.1); display: inline-flex; align-items: center; gap: 8px;">
                <span>📋</span> Ver Actas de Baja
            </a>
        </div>
    </div>

    {{-- FILTRO DE BÚSQUEDA --}}
    <div style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; padding: 20px; margin-bottom: 25px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);">
        <form method="GET" action="{{ route('activos.baja') }}" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 280px;">
                <label style="display: block; font-size: 11px; font-weight: 800; color: #334155; margin-bottom: 6px; text-transform: uppercase;">Buscar Activo Dado de Baja</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar por código, nombre o marca..." style="width: 100%; padding: 10px 14px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; color: #1e293b; outline: none;">
            </div>
            <div>
                <button type="submit" style="background: #7c2d12; color: white; padding: 10px 20px; border: none; border-radius: 10px; font-weight: 700; font-size: 13px; cursor: pointer;">
                    🔍 Buscar
                </button>
            </div>
        </form>
    </div>

    {{-- TABLA DE ACTIVOS BAJA --}}
    <div style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
                <thead style="background: #f8fafc; color: #475569; text-transform: uppercase; font-size: 10px; letter-spacing: 0.5px; border-bottom: 1px solid #e2e8f0;">
                    <tr>
                        <th style="padding: 14px 16px;">Código</th>

                        <th style="padding: 14px 16px;">Nombre / Características</th>
                        <th style="padding: 14px 16px;">Categoría</th>
                        <th style="padding: 14px 16px;">Ambiente Original</th>
                        <th style="padding: 14px 16px;">Custodio Asignado</th>
                        <th style="padding: 14px 16px;">Fecha de Baja</th>
                        <th style="padding: 14px 16px; text-align: right;">Valor</th>
                        <th style="padding: 14px 16px; text-align: center;">Estado</th>
                    </tr>
                </thead>
                <tbody style="divide-y: #f1f5f9;">
                    @forelse($activos as $activo)
                        <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                            <td style="padding: 14px 16px; font-family: monospace; font-weight: 800; color: #7c2d12;">
                                {{ $activo->codigo_activo }}
                            </td>

                            <td style="padding: 14px 16px;">
                                <div style="font-weight: 700; color: #0f172a;">{{ $activo->nombre }}</div>
                                <div style="font-size: 11px; color: #64748b;">{{ $activo->marca ?? '' }} {{ $activo->modelo ?? '' }}</div>
                            </td>
                            <td style="padding: 14px 16px; color: #475569;">
                                {{ optional($activo->categoria)->nombre ?? '—' }}
                            </td>
                            <td style="padding: 14px 16px; color: #475569;">
                                {{ optional($activo->ambiente)->nombre ?? '—' }}
                            </td>
                            <td style="padding: 14px 16px; color: #475569;">
                                {{ optional($activo->custodio)->nombre_completo ?? '—' }}
                            </td>
                            <td style="padding: 14px 16px; color: #334155; font-weight: 600;">
                                {{ optional($activo->updated_at)->format('d/m/Y') ?? '—' }}
                            </td>
                            <td style="padding: 14px 16px; text-align: right; font-weight: 700; color: #0f172a;">
                                Bs.{{ number_format($activo->valor_adquisicion ?? 0, 2) }}
                            </td>
                            <td style="padding: 14px 16px; text-align: center;">
                                <span style="display: inline-block; padding: 4px 10px; border-radius: 6px; font-weight: 800; font-size: 10px; background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5;">
                                    BAJA
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 50px 20px; color: #64748b; font-weight: 600;">
                                No hay activos registrados con estado de baja.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(method_exists($activos, 'hasPages') && $activos->hasPages())
        <div style="margin-top: 20px;">{{ $activos->links() }}</div>
    @endif
</div>
@endsection
