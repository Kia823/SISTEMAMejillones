@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Botón Volver --}}
    <div style="margin-bottom: 15px;">
        <button onclick="history.back()" style="background: #e2e8f0; color: #334155; padding: 8px 16px; border-radius: 10px; font-weight: 700; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; font-size: 14px;">
            ← Volver atrás
        </button>
    </div>

    {{-- ENCABEZADO --}}
    <div style="background:linear-gradient(135deg,#d97706 0%,#b45309 100%);padding:28px;border-radius:16px;color:white;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:20px;margin-bottom:24px;box-shadow:0 10px 15px -3px rgba(217,119,6,.2);">
        <div>
            <span style="background:rgba(255,255,255,.2);padding:5px 12px;border-radius:20px;font-size:11px;font-weight:800;text-transform:uppercase;">
                Clasificación secundaria
            </span>
            <h1 style="font-size:28px;font-weight:800;margin:8px 0 4px 0;">Artículos No Activos</h1>
            <p style="opacity:.9;font-size:14px;margin:0;">
                Listado de elementos menores (grapadoras, perforadoras, útiles, etc.) registrados en el sistema.
            </p>
        </div>

        <div style="background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.2);padding:18px 20px;border-radius:12px;text-align:center;min-width:130px;">
            <div style="font-size:26px;font-weight:800;">{{ $totalNoActivos ?? 0 }}</div>
            <div style="font-size:11px;font-weight:700;opacity:.9;text-transform:uppercase;margin-top:4px;">Total No Activos</div>
        </div>
    </div>

    {{-- BUSCADOR --}}
    <div style="background:white;border-radius:12px;border:1px solid #e2e8f0;padding:16px 20px;margin-bottom:20px;">
        <form method="GET" action="{{ route('activos.noActivos') }}" style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
            <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="🔍 Buscar por código, nombre, marca..."
                   style="flex:1;min-width:280px;padding:10px 14px;border-radius:8px;border:1px solid #cbd5e1;font-size:13px;">
            <button type="submit" style="background:#2563eb;color:white;border:none;padding:10px 20px;border-radius:8px;font-weight:700;cursor:pointer;">
                Buscar
            </button>
            @if(request('buscar'))
                <a href="{{ route('activos.noActivos') }}" style="background:#e2e8f0;color:#475569;padding:10px 16px;border-radius:8px;font-weight:700;text-decoration:none;font-size:13px;">
                    Limpiar
                </a>
            @endif
        </form>
    </div>

    {{-- TABLA DE RESULTADOS --}}
    <div style="background:white;border-radius:16px;border:1px solid #e2e8f0;padding:25px;box-shadow:0 4px 6px -1px rgba(0,0,0,.05);">
        @if(isset($activos) && $activos->count() > 0)
            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;font-size:13px;vertical-align:middle;">
                    <thead>
                        <tr style="background:#f1f5f9;color:#475569;text-transform:uppercase;font-size:11px;font-weight:800;">
                            <th style="padding:14px 10px;text-align:center;width:45px;">Nº</th>
                            <th style="padding:14px 10px;text-align:center;width:65px;">IMAGEN</th>
                            <th style="padding:14px 10px;text-align:left;">CÓDIGO</th>
                            <th style="padding:14px 10px;text-align:left;">NOMBRE / CARACTERÍSTICAS</th>
                            <th style="padding:14px 10px;text-align:left;">AMBIENTE / CATEGORÍA</th>
                            <th style="padding:14px 10px;text-align:left;">CUSTODIO</th>
                            <th style="padding:14px 10px;text-align:center;width:90px;">ESTADO</th>
                            <th style="padding:14px 10px;text-align:center;width:120px;">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($activos as $i => $a)
                            <tr style="border-bottom:1px solid #e2e8f0;">
                                <td style="padding:12px 10px;text-align:center;color:#64748b;font-weight:800;">{{ $i + 1 }}</td>
                                <td style="padding:12px 10px;text-align:center;">
                                    @if($a->imagen)
                                        <img src="{{ asset('storage/' . $a->imagen) }}" alt="Foto" style="width:42px;height:42px;object-fit:cover;border-radius:8px;border:1px solid #e2e8f0;display:block;margin:0 auto;">
                                    @else
                                        <span style="font-size:11px;color:#94a3b8;font-style:italic;">Sin foto</span>
                                    @endif
                                </td>
                                <td style="padding:12px 10px;font-weight:800;color:#0f172a;">
                                    {{ $a->codigo_activo }}
                                </td>
                                <td style="padding:12px 10px;">
                                    <div style="font-weight:700;color:#1e293b;">{{ $a->nombre }}</div>
                                    <div style="font-size:11px;color:#64748b;">{{ $a->descripcion }}</div>
                                </td>
                                <td style="padding:12px 10px;font-size:12px;color:#334155;">
                                    <div>🏢 {{ $a->ambiente->nombre ?? '—' }}</div>
                                    <div style="font-size:11px;color:#64748b;">📁 {{ $a->categoria->nombre ?? '—' }}</div>
                                </td>
                                <td style="padding:12px 10px;font-size:12px;color:#0f172a;font-weight:700;">
                                    {{ $a->custodio->nombre_completo ?? 'Sin asignar' }}
                                </td>
                                <td style="padding:12px 10px;text-align:center;">
                                    <span style="background:#fef3c7;color:#92400e;padding:4px 8px;border-radius:6px;font-weight:800;font-size:10px;display:inline-block;">
                                        NO ACTIVO
                                    </span>
                                </td>
                                <td style="padding:12px 10px;text-align:center;">
                                    <div style="display:flex;gap:6px;justify-content:center;align-items:center;">
                                        <a href="{{ route('activos.tarjeta', $a->id_activo) }}" target="_blank" title="Ver tarjeta"
                                           style="width:30px;height:30px;background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;text-decoration:none;font-size:13px;">👁️</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="text-align:center;padding:60px 20px;color:#94a3b8;font-weight:600;">
                <div style="font-size:48px;margin-bottom:12px;">📂</div>
                <h3 style="margin:0 0 6px 0;font-size:16px;color:#64748b;">No hay artículos no activos registrados</h3>
                <p style="margin:0;font-size:13px;">Los ítems clasificados como "No Activo" aparecerán aquí.</p>
            </div>
        @endif
    </div>
</div>
@endsection
