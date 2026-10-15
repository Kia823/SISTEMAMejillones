@extends('layouts.app')

@section('content')

@if(session('success'))
    <div style="background: #dcfce7; border: 1px solid #bbf7d0; color: #166534; padding: 14px 20px; border-radius: 12px; margin-bottom: 20px; font-weight: 700;">
        ✓ {{ session('success') }}
    </div>
@endif

<section class="hero-card" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); padding: 28px; border-radius: 18px; color: white; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; margin-bottom: 24px;">
    <div>
        <span style="display:inline-block;padding:5px 12px;border-radius:999px;background:rgba(255,255,255,0.2);font-size:11px;font-weight:800;text-transform:uppercase;">
            Ambientes
        </span>
        <h1 style="margin:10px 0 8px;font-size:26px;font-weight:800;">
            {{ isset($carrera) ? 'Ambientes de '.$carrera->nombre : 'Todos los ambientes' }}
        </h1>
        <p style="opacity:.9;font-size:14px;">Administra los espacios físicos donde se ubican los activos.</p>
    </div>
    <div style="display: flex; gap: 12px;">
        <div style="background: rgba(255,255,255,0.15); padding: 12px 20px; border-radius: 12px; text-align: center; min-width: 100px;">
            <div style="font-size: 22px; font-weight: 800;">{{ $ambientes->count() }}</div>
            <span style="font-size: 11px; font-weight: 700; opacity: .9; text-transform: uppercase;">Ambientes</span>
        </div>
        <div style="background: rgba(255,255,255,0.15); padding: 12px 20px; border-radius: 12px; text-align: center; min-width: 100px;">
            <div style="font-size: 22px; font-weight: 800;">{{ $ambientes->sum('activos_count') }}</div>
            <span style="font-size: 11px; font-weight: 700; opacity: .9; text-transform: uppercase;">Activos</span>
        </div>
    </div>
</section>

<section class="panel" style="background: white; border-radius: 16px; padding: 24px; border: 1px solid #e2e8f0;">
    <h2 style="margin-top: 0; font-size: 18px; font-weight: 800; color: #0f172a;">Listado de ambientes</h2>

    @if($ambientes->count())
        <div class="table-wrap" style="overflow-x: auto; margin-top: 15px;">
            <table style="width:100%; border-collapse: collapse;">
                <thead>
                    <tr style="background:#f8fafc; color:#64748b; font-size:11px; font-weight:800; text-transform:uppercase;">
                        <th style="padding:10px; text-align:left;">Código</th>
                        <th style="padding:10px; text-align:left;">Nombre</th>
                        <th style="padding:10px; text-align:left;">Bloque</th>
                        <th style="padding:10px; text-align:left;">Piso</th>
                        <th style="padding:10px; text-align:center;">Activos</th>
                        <th style="padding:10px; text-align:center;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ambientes as $amb)
                        <tr style="border-bottom:1px solid #e2e8f0;">
                            <td style="padding:10px;">{{ $amb->codigo }}</td>
                            <td style="padding:10px; font-weight:700;">{{ $amb->nombre }}</td>
                            <td style="padding:10px;">{{ $amb->bloque ?? '—' }}</td>
                            <td style="padding:10px;">{{ $amb->piso ?? '—' }}</td>
                            <td style="padding:10px; text-align:center;">{{ $amb->activos_count ?? 0 }}</td>
                            <td style="padding:10px; text-align:center;">
                                {{-- La ruta espera el id del ambiente --}}
                                <a class="btn light" href="{{ route('ambientes.show', ['id' => $amb->id_ambiente]) }}"
                                   style="padding:5px 12px;border-radius:8px;background:#eff6ff;color:#2563eb;text-decoration:none;font-weight:700;font-size:12px;">
                                    Ver detalle
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p style="text-align:center; color:#94a3b8; padding: 30px; font-weight:600;">No hay ambientes registrados.</p>
    @endif
</section>
@endsection
