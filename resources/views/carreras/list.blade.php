@extends('layouts.app')

@section('content')
<section class="hero-card">
    <div>
        <div style="display:inline-block;padding:6px 10px;border-radius:999px;background:rgba(255,255,255,0.18);font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;">Carreras</div>
        <h1 style="margin:10px 0 8px;font-size:28px;">Carreras</h1>
        <p>Listado general de carreras y sus respectivos Jefes de Carrera.</p>
    </div>
    <div class="stat-grid" style="align-items:center;gap:12px;">
        <div><strong>{{ $carreras->count() }}</strong><span>Carreras</span></div>
        @if(auth()->check() && auth()->user()->esAdmin())
            <a href="{{ route('carreras.create') }}" class="btn" style="text-decoration:none;background:#2563eb;color:#fff;border-color:#1d4ed8;">+ Nueva Carrera</a>
        @endif
    </div>
</section>

<section class="panel">
    <h2>Listado de Carreras</h2>
    @if(session('status'))
        <div class="result-box">
            <h3>Éxito</h3>
            <p>{{ session('status') }}</p>
        </div>
    @endif
    @if(session('success'))
        <div style="background:#dcfce7;border:1px solid #bbf7d0;color:#166534;padding:12px 16px;border-radius:10px;margin-bottom:15px;font-weight:700;">
            ✓ {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="result-box" style="border-color:#fecaca;background:#fff1f2;">
            <h3>Error</h3>
            <ul>
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Tarjetas cuadradas estilo carpeta con doble clic usando id_carrera --}}
    <style>
        .folders-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 16px;
            margin-top: 20px;
        }
        .folder-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 20px;
            text-align: left;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .folder-card:hover {
            transform: translateY(-3px);
            border-color: #2563eb;
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.1);
        }
    </style>

    <div class="folders-grid">
        @foreach($carreras as $c)
            {{-- ⭐ Redirección precisa usando $c->id_carrera --}}
            <div class="folder-card" ondblclick="window.location.href='{{ route('carreras.show', $c->id_carrera) }}'">
                <div>
                    <div style="font-size: 32px; margin-bottom: 8px;">🎓</div>
                    <h3 style="font-size: 16px; font-weight: 800; color: #0f172a; margin: 0 0 6px 0;">{{ $c->nombre }}</h3>
                    <div style="font-size: 11px; color: #475569; background: #f8fafc; padding: 8px; border-radius: 8px; border: 1px solid #f1f5f9; margin-top: 10px;">
                        <strong style="color: #1e293b; display: block; margin-bottom: 2px;">👤 Jefe de Carrera:</strong>
                        <span style="color: #2563eb; font-weight: 700;">{{ $c->jefeCarrera?->nombre_completo ?? 'No asignado' }}</span>
                    </div>
                </div>

                <div style="display: flex; gap: 8px; margin-top: 16px; padding-top: 10px; border-top: 1px solid #f1f5f9;" onclick="event.stopPropagation();">
                    <a class="btn" href="{{ route('carreras.show', $c->id_carrera) }}" style="flex:1; text-align:center; font-size:12px; padding:6px; text-decoration:none;">Abrir</a>
                    <a class="btn light" href="{{ route('carreras.show', $c->id_carrera) }}?nuevo_ambiente=1" style="flex:1; text-align:center; font-size:12px; padding:6px; text-decoration:none;">+ Ambiente</a>
                    @if(auth()->check() && auth()->user()->esAdmin())
                        <a class="btn" href="{{ route('carreras.edit', $c->id_carrera) }}" style="flex:1; text-align:center; font-size:12px; padding:6px; text-decoration:none; background:#f59e0b; color:#fff; border-color:#d97706;">✏️ Editar</a>
                        <form action="{{ route('carreras.destroy', $c->id_carrera) }}" method="POST" style="flex:1;" onsubmit="return confirm('¿Eliminar la carrera {{ $c->nombre }}? Esta acción no se puede deshacer.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn" style="width:100%; font-size:12px; padding:6px; background:#dc2626; color:#fff; border-color:#b91c1c;">🗑 Eliminar</button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</section>
@endsection
