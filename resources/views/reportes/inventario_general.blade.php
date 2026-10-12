@extends('layouts.app')

@section('title', 'Inventario General Agrupado')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- ============================================================ --}}
    {{-- ENCABEZADO CORPORATIVO --}}
    {{-- ============================================================ --}}
    <div style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%); padding: 30px; border-radius: 18px; color: white; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; margin-bottom: 25px; box-shadow: 0 10px 20px -3px rgba(37, 99, 235, 0.25);">
        <div>
            <span style="background: rgba(255, 255, 255, 0.2); padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">Módulo Institucional</span>
            <h1 style="font-size: 26px; font-weight: 800; margin: 8px 0 4px 0;">Inventario General de Activos (Agrupados)</h1>
            <p style="opacity: 0.9; font-size: 14px; margin: 0;">Equipos agrupados por modelo/marca. Códigos concatenados, cantidad sumada, costo total.</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('reportes.inventario.general.pdf') }}" target="_blank" style="background: #dc2626; color: white; padding: 10px 16px; border-radius: 10px; font-size: 13px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 2px 4px rgba(220, 38, 38, 0.2);">
                📄 PDF
            </a>
            <a href="{{ route('reportes.inventario.general.excel') }}" target="_blank" style="background: #059669; color: white; padding: 10px 16px; border-radius: 10px; font-size: 13px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 2px 4px rgba(5, 150, 105, 0.2);">
                📊 Excel
            </a>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- FILTROS --}}
    {{-- ============================================================ --}}
    <div style="background: white; padding: 20px; border-radius: 14px; border: 1px solid #e2e8f0; box-shadow: 0 2px 4px rgba(0,0,0,0.02); margin-bottom: 20px;">
        <form method="GET" action="{{ route('reportes.inventario.general') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; align-items: end;">
            <div>
                <label style="display: block; font-size: 11px; font-weight: 700; color: #64748b; margin-bottom: 4px; text-transform: uppercase;">Carrera</label>
                <select name="carrera" class="form-select form-select-sm" style="padding: 8px 12px; font-size: 13px;">
                    <option value="">— Todas —</option>
                    @foreach($carreras as $c)
                        <option value="{{ $c->id_carrera }}" {{ request('carrera') == $c->id_carrera ? 'selected' : '' }}>{{ $c->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 11px; font-weight: 700; color: #64748b; margin-bottom: 4px; text-transform: uppercase;">Ambiente</label>
                <select name="ambiente" class="form-select form-select-sm" style="padding: 8px 12px; font-size: 13px;">
                    <option value="">— Todos —</option>
                    @foreach($ambientes as $a)
                        <option value="{{ $a->id_ambiente }}" {{ request('ambiente') == $a->id_ambiente ? 'selected' : '' }}>{{ $a->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 11px; font-weight: 700; color: #64748b; margin-bottom: 4px; text-transform: uppercase;">Estado Físico</label>
                <select name="estado_fisico" class="form-select form-select-sm" style="padding: 8px 12px; font-size: 13px;">
                    <option value="">— Todos —</option>
                    <option value="B" {{ request('estado_fisico') == 'B' ? 'selected' : '' }}>BUENO</option>
                    <option value="R" {{ request('estado_fisico') == 'R' ? 'selected' : '' }}>REGULAR</option>
                    <option value="M" {{ request('estado_fisico') == 'M' ? 'selected' : '' }}>MALO</option>
                    <option value="FF" {{ request('estado_fisico') == 'FF' ? 'selected' : '' }}>FUERA DE FUNCIONAMIENTO</option>
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 11px; font-weight: 700; color: #64748b; margin-bottom: 4px; text-transform: uppercase;">Fuente</label>
                <select name="fuente" class="form-select form-select-sm" style="padding: 8px 12px; font-size: 13px;">
                    <option value="">— Todas —</option>
                    @foreach($fuentes as $f)
                        <option value="{{ $f->id_fuente }}" {{ request('fuente') == $f->id_fuente ? 'selected' : '' }}>{{ $f->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 11px; font-weight: 700; color: #64748b; margin-bottom: 4px; text-transform: uppercase;">Año</label>
                <select name="anio" class="form-select form-select-sm" style="padding: 8px 12px; font-size: 13px;">
                    <option value="">— Todos —</option>
                    @for($y = 2026; $y >= 2010; $y--)
                        <option value="{{ $y }}" {{ request('anio') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <button type="submit" style="width: 100%; padding: 10px; background: #2563eb; color: white; border: none; border-radius: 8px; font-weight: 700; font-size: 13px; cursor: pointer;">
                    🔍 Filtrar
                </button>
            </div>
            <div>
                <a href="{{ route('reportes.inventario.general') }}" style="width: 100%; padding: 10px; background: #f1f5f9; color: #475569; border: none; border-radius: 8px; font-weight: 700; font-size: 13px; text-align: center; text-decoration: none; display: block;">
                    Limpiar
                </a>
            </div>
        </form>
    </div>

    {{-- ============================================================ --}}
    {{-- TABLA DE REPORTE --}}
    {{-- ============================================================ --}}
    <div style="background: white; border-radius: 14px; border: 1px solid #e2e8f0; box-shadow: 0 2px 4px rgba(0,0,0,0.02); overflow: hidden;">
        <div style="padding: 16px 20px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 16px; font-weight: 800; color: #1e293b;">
                Total grupos: {{ $reportes->count() }} | Total items: {{ $reportes->sum('cantidad') }} | Total costo: Bs. {{ number_format($reportes->sum('costo_total'), 2) }}
            </h3>
            <div style="display: flex; gap: 8px;">
                <a href="{{ route('reportes.inventario.general.excel') }}" class="btn btn-success btn-sm" target="_blank">📊 Excel</a>
                <a href="{{ route('reportes.inventario.general.pdf') }}" class="btn btn-danger btn-sm" target="_blank">📄 PDF</a>
            </div>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
                <thead style="background: #1e3a8a; color: white; position: sticky; top: 0; z-index: 10;">
                    <tr>
                        <th style="padding: 8px 4px; border: 1px solid #3b82f6; text-align: center; min-width: 40px; font-weight: 700;">Nº</th>
                        <th style="padding: 8px 4px; border: 1px solid #3b82f6; text-align: center; min-width: 200px; font-weight: 700;">CÓDIGO</th>
                        <th style="padding: 8px 4px; border: 1px solid #3b82f6; text-align: center; min-width: 120px; font-weight: 700;">DESCRIPCIÓN DEL EQUIPO</th>
                        <th style="padding: 8px 4px; border: 1px solid #3b82f6; text-align: center; min-width: 300px; font-weight: 700;">CARACTERÍSTICAS</th>
                        <th style="padding: 8px 4px; border: 1px solid #3b82f6; text-align: center; min-width: 60px; font-weight: 700;">CANTIDAD</th>
                        <th style="padding: 8px 4px; border: 1px solid #3b82f6; text-align: center; min-width: 100px; font-weight: 700;">COSTO (Bs.)</th>
                        <th style="padding: 8px 4px; border: 1px solid #3b82f6; text-align: center; min-width: 80px; font-weight: 700;">ESTADO</th>
                        <th style="padding: 8px 4px; border: 1px solid #3b82f6; text-align: center; min-width: 100px; font-weight: 700;">FUENTE</th>
                        <th style="padding: 8px 4px; border: 1px solid #3b82f6; text-align: center; min-width: 100px; font-weight: 700;">FECHA ENTREGA</th>
                        <th style="padding: 8px 4px; border: 1px solid #3b82f6; text-align: center; min-width: 60px; font-weight: 700;">DÍA</th>
                        <th style="padding: 8px 4px; border: 1px solid #3b82f6; text-align: center; min-width: 60px; font-weight: 700;">MES</th>
                        <th style="padding: 8px 4px; border: 1px solid #3b82f6; text-align: center; min-width: 60px; font-weight: 700;">AÑO</th>
                        <th style="padding: 8px 4px; border: 1px solid #3b82f6; text-align: center; min-width: 150px; font-weight: 700;">USO EN ÁREA/S (CARRERA)</th>
                        <th style="padding: 8px 4px; border: 1px solid #3b82f6; text-align: center; min-width: 150px; font-weight: 700;">OBSERVACIONES</th>
                        <th style="padding: 8px 4px; border: 1px solid #3b82f6; text-align: center; min-width: 80px; font-weight: 700;">IMAGEN</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reportes as $r)
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 6px 4px; border: 1px solid #e2e8f0; text-align: center; vertical-align: middle;">{{ $r->nro }}</td>
                        <td style="padding: 6px 4px; border: 1px solid #e2e8f0; text-align: center; vertical-align: middle; font-family: monospace; font-size: 10px; word-break: break-all;">{{ $r->codigos }}</td>
                        <td style="padding: 6px 4px; border: 1px solid #e2e8f0; text-align: center; vertical-align: middle; font-weight: 600;">{{ $r->descripcion }}</td>
                        <td style="padding: 6px 4px; border: 1px solid #e2e8f0; text-align: left; vertical-align: middle; font-size: 10px; max-width: 300px;">{{ $r->caracteristicas }}</td>
                        <td style="padding: 6px 4px; border: 1px solid #e2e8f0; text-align: center; vertical-align: middle; font-weight: 700; color: #1e3a8a;">{{ $r->cantidad }}</td>
                        <td style="padding: 6px 4px; border: 1px solid #e2e8f0; text-align: right; vertical-align: middle; padding-right: 8px; font-family: monospace;">{{ number_format($r->costo_total, 2) }}</td>
                        <td style="padding: 6px 4px; border: 1px solid #e2e8f0; text-align: center; vertical-align: middle;">
                            <span style="display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: 700;
                                @if($r->estado_fisico == 'B') background: #dcfce7; color: #166534;
                                @elseif($r->estado_fisico == 'R') background: #fef9c3; color: #854d0e;
                                @elseif($r->estado_fisico == 'M') background: #ffedd5; color: #9a3412;
                                @elseif($r->estado_fisico == 'FF') background: #fee2e2; color: #991b1b;
                                @else background: #f1f5f9; color: #475569; @endif">
                                {{ $r->estado_fisico_texto }}
                            </span>
                        </td>
                        <td style="padding: 6px 4px; border: 1px solid #e2e8f0; text-align: center; vertical-align: middle; font-size: 10px;">{{ $r->fuente ?? '—' }}</td>
                        <td style="padding: 6px 4px; border: 1px solid #e2e8f0; text-align: center; vertical-align: middle; font-family: monospace;">{{ $r->fecha_entrega ? $r->fecha_entrega->format('d/m/Y') : '—' }}</td>
                        <td style="padding: 6px 4px; border: 1px solid #e2e8f0; text-align: center; vertical-align: middle;">{{ $r->dia }}</td>
                        <td style="padding: 6px 4px; border: 1px solid #e2e8f0; text-align: center; vertical-align: middle;">{{ $r->mes }}</td>
                        <td style="padding: 6px 4px; border: 1px solid #e2e8f0; text-align: center; vertical-align: middle; font-weight: 700;">{{ $r->anio }}</td>
                        <td style="padding: 6px 4px; border: 1px solid #e2e8f0; text-align: center; vertical-align: middle; font-size: 11px;">{{ $r->carrera }}</td>
                        <td style="padding: 6px 4px; border: 1px solid #e2e8f0; text-align: center; vertical-align: middle; font-size: 10px;">{{ $r->observaciones ?? '—' }}</td>
                        <td style="padding: 6px 4px; border: 1px solid #e2e8f0; text-align: center; vertical-align: middle;">
                            @if($r->imagen)
                                <a href="{{ asset('storage/'.$r->imagen) }}" target="_blank" title="Ver imagen">
                                    <img src="{{ asset('storage/'.$r->imagen) }}" alt="Imagen" style="width: 40px; height: 30px; object-fit: cover; border-radius: 4px; border: 1px solid #e2e8f0;">
                                </a>
                            @else
                                <span style="color: #94a3b8; font-size: 10px;">Sin img</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot style="background: #f8fafc; font-weight: 700;">
                    <tr>
                        <td colspan="4" style="padding: 8px 4px; border: 1px solid #e2e8f0; text-align: right;">TOTALES:</td>
                        <td style="padding: 8px 4px; border: 1px solid #e2e8f0; text-align: center; color: #1e3a8a;">{{ $reportes->sum('cantidad') }}</td>
                        <td style="padding: 8px 4px; border: 1px solid #e2e8f0; text-align: right; padding-right: 8px;">{{ number_format($reportes->sum('costo_total'), 2) }}</td>
                        <td colspan="9" style="padding: 8px 4px; border: 1px solid #e2e8f0;"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        @if($reportes->isEmpty())
        <div style="padding: 60px 20px; text-align: center; color: #94a3b8;">
            <div style="font-size: 48px; margin-bottom: 16px;">📭</div>
            <p style="font-size: 16px; font-weight: 600;">No hay activos registrados con los filtros actuales</p>
        </div>
        @endif
    </div>

</div>
@endsection
