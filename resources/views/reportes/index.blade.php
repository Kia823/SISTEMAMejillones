@extends('layouts.app')

@section('title', 'Centro de Reportes e Inventarios')

@section('content')
<div class="container-fluid px-4 py-6">

    {{-- ENCABEZADO CORPORATIVO --}}
    <div style="background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%); padding: 30px; border-radius: 18px; color: white; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; margin-bottom: 25px; box-shadow: 0 10px 20px -3px rgba(37, 99, 235, 0.25);">
        <div>
            <span style="background: rgba(255, 255, 255, 0.2); padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">Módulo Institucional</span>
            <h1 style="font-size: 26px; font-weight: 800; margin: 8px 0 4px 0;">Centro de Reportes e Inventarios</h1>
            <p style="opacity: 0.9; font-size: 14px; margin: 0;">Genera, visualiza y exporta inventarios generales, por ambiente, carrera, categoría y actas oficiales.</p>
        </div>
        <div>
            <a href="{{ route('reportes.pdf') }}" target="_blank"
               style="background: white; color: #1e40af; padding: 12px 20px; border-radius: 12px; font-weight: 800; font-size: 13px; text-decoration: none; box-shadow: 0 4px 6px rgba(0,0,0,0.1); display: inline-flex; align-items: center; gap: 8px;">
                <span>📊</span> Inventario General PDF
            </a>
        </div>
    </div>

    {{-- TARJETAS DE MÉTRICAS (Actualizadas sin incluir bajas) --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 30px;">
        <div style="background: white; border-radius: 16px; padding: 20px; border: 1px solid #e2e8f0; border-left: 5px solid #10b981; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);">
            <div style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase;">Buenos</div>
            <div style="font-size: 24px; font-weight: 800; color: #0f172a; margin-top: 4px;">{{ $totalBuenos ?? 0 }}</div>
        </div>
        <div style="background: white; border-radius: 16px; padding: 20px; border: 1px solid #e2e8f0; border-left: 5px solid #f59e0b; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);">
            <div style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase;">Regulares</div>
            <div style="font-size: 24px; font-weight: 800; color: #0f172a; margin-top: 4px;">{{ $totalRegulares ?? 0 }}</div>
        </div>
        <div style="background: white; border-radius: 16px; padding: 20px; border: 1px solid #e2e8f0; border-left: 5px solid #f97316; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);">
            <div style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase;">Malos</div>
            <div style="font-size: 24px; font-weight: 800; color: #0f172a; margin-top: 4px;">{{ $totalMalos ?? 0 }}</div>
        </div>
        <div style="background: white; border-radius: 16px; padding: 20px; border: 1px solid #e2e8f0; border-left: 5px solid #dc2626; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);">
            <div style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase;">Fuera de Servicio</div>
            <div style="font-size: 24px; font-weight: 800; color: #0f172a; margin-top: 4px;">{{ $totalFueraServ ?? 0 }}</div>
        </div>
    </div>

    {{-- SECCIÓN DE REPORTES ESPECÍFICOS --}}
    <h3 style="font-size: 18px; font-weight: 800; color: #0f172a; margin-bottom: 16px;">Inventarios y Reportes Específicos</h3>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; margin-bottom: 30px;">
        {{-- 1. INVENTARIO POR AMBIENTE --}}
        <div style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
                <h4 style="font-size: 16px; font-weight: 800; color: #0f172a; margin: 0;">Inventario por Ambiente</h4>
                <span style="font-size: 10px; font-weight: 800; background: #e0e7ff; color: #3730a3; padding: 3px 8px; border-radius: 6px; text-transform: uppercase;">Ubicación</span>
            </div>
            <p style="font-size: 13px; color: #64748b; margin-bottom: 16px;">Selecciona primero la carrera y luego el ambiente para exportar su reporte.</p>

            <form method="GET" action="{{ route('reportes.pdf') }}" target="_blank" style="display: flex; flex-direction: column; gap: 12px;">
                <div style="display: flex; flex-direction: column; gap: 6px;">
                    <label style="font-size: 12px; font-weight: 700; color: #334155;">Carrera</label>
                    <select name="id_carrera" id="filtro-carrera-reporte" style="width: 100%; padding: 10px 14px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; color: #1e293b; outline: none;" onchange="filtrarAmbientesPorCarrera(this.value)">
                        <option value="">— Seleccionar carrera —</option>
                        @foreach($carreras as $car)
                            <option value="{{ $car->id_carrera }}">{{ $car->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="display: flex; flex-direction: column; gap: 6px;">
                    <label style="font-size: 12px; font-weight: 700; color: #334155;">Ambiente</label>
                    <select name="id_ambiente" id="filtro-ambiente-reporte" required style="width: 100%; padding: 10px 14px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; color: #1e293b; outline: none;">
                        <option value="">— Primero selecciona una carrera —</option>
                        @foreach($ambientes as $amb)
                            <option value="{{ $amb->id_ambiente }}" data-carrera="{{ optional($amb->carrera)->id_carrera }}">{{ $amb->nombre }} ({{ optional($amb->carrera)->nombre }})</option>
                        @endforeach
                    </select>
                </div>
                <div style="display: flex; flex-direction: column; gap: 6px;">
                    <label style="font-size: 12px; font-weight: 700; color: #334155;">Tipo de Bien</label>
                    <select name="tipo_bien" style="width: 100%; padding: 10px 14px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; color: #1e293b; outline: none;">
                        <option value="TODOS">Todos (Activos + No Activos)</option>
                        <option value="ACTIVO_FIJO">Solo Activos Fijos</option>
                        <option value="NO_ACTIVO">Solo No Activos</option>
                    </select>
                </div>
                <div style="display: flex; gap: 8px;">
                    <button type="submit" style="flex: 1; padding: 10px; background: #dc2626; color: white; border: none; border-radius: 10px; font-weight: 700; font-size: 12px; cursor: pointer;">
                        📄 PDF Ambiente
                    </button>
                    <button type="submit" formaction="{{ route('reportes.excel') }}" style="flex: 1; padding: 10px; background: #16a34a; color: white; border: none; border-radius: 10px; font-weight: 700; font-size: 12px; cursor: pointer;">
                        📊 Excel Ambiente
                    </button>
                </div>
            </form>
        </div>



        {{-- 3. INVENTARIO POR CATEGORÍA --}}
        <div style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
                <h4 style="font-size: 16px; font-weight: 800; color: #0f172a; margin: 0;">Inventario por Categoría</h4>
                <span style="font-size: 10px; font-weight: 800; background: #fdf2f8; color: #be185d; padding: 3px 8px; border-radius: 6px; text-transform: uppercase;">Clasificación</span>
            </div>
            <p style="font-size: 13px; color: #64748b; margin-bottom: 16px;">Selecciona carrera, ambiente, categoría y tipo de bien para exportar su reporte.</p>

            <form method="GET" action="{{ route('reportes.pdf') }}" target="_blank" style="display: flex; flex-direction: column; gap: 12px;">
                <div style="display: flex; flex-direction: column; gap: 6px;">
                    <label style="font-size: 12px; font-weight: 700; color: #334155;">Carrera</label>
                    <select name="id_carrera" id="filtro-carrera-cat" style="width: 100%; padding: 10px 14px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; color: #1e293b; outline: none;" onchange="filtrarAmbientesPorCarreraCat(this.value)">
                        <option value="">— Todas las carreras —</option>
                        @foreach($carreras as $car)
                            <option value="{{ $car->id_carrera }}">{{ $car->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="display: flex; flex-direction: column; gap: 6px;">
                    <label style="font-size: 12px; font-weight: 700; color: #334155;">Ambiente</label>
                    <select name="id_ambiente" id="filtro-ambiente-cat" style="width: 100%; padding: 10px 14px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; color: #1e293b; outline: none;">
                        <option value="">— Todos los ambientes —</option>
                        @foreach($ambientes as $amb)
                            <option value="{{ $amb->id_ambiente }}" data-carrera="{{ optional($amb->carrera)->id_carrera }}">{{ $amb->nombre }} ({{ optional($amb->carrera)->nombre }})</option>
                        @endforeach
                    </select>
                </div>
                <div style="display: flex; flex-direction: column; gap: 6px;">
                    <label style="font-size: 12px; font-weight: 700; color: #334155;">Categoría</label>
                    <select name="id_categoria" required style="width: 100%; padding: 10px 14px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; color: #1e293b; outline: none;">
                        <option value="">— Seleccionar categoría —</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id_categoria }}">{{ $cat->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="display: flex; gap: 8px;">
                    <button type="submit" style="flex: 1; padding: 10px; background: #dc2626; color: white; border: none; border-radius: 10px; font-weight: 700; font-size: 12px; cursor: pointer;">
                        📄 PDF Categoría
                    </button>
                    <button type="submit" formaction="{{ route('reportes.excel') }}" style="flex: 1; padding: 10px; background: #16a34a; color: white; border: none; border-radius: 10px; font-weight: 700; font-size: 12px; cursor: pointer;">
                        📊 Excel Categoría
                    </button>
                </div>
            </form>
        </div>

    </div>

    {{-- SECCIÓN INVENTARIO GENERAL Y ACTAS --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px;">

        {{-- ACTAS Y DOCUMENTOS --}}
        <div style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
                <h4 style="font-size: 16px; font-weight: 800; color: #0f172a; margin: 0;">Actas y Documentos</h4>
                <span style="font-size: 10px; font-weight: 800; background: #f1f5f9; color: #475569; padding: 3px 8px; border-radius: 6px; text-transform: uppercase;">Legal</span>
            </div>
            <p style="font-size: 13px; color: #64748b; margin-bottom: 20px;">Consulta y genera actas oficiales de recepción, baja y transferencia institucional.</p>

            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px;">
                <a href="{{ route('actas.baja.index') }}" style="padding: 10px 14px; background: #fff7ed; color: #9a3412; border: 1px solid #ffedd5; border-radius: 10px; font-size: 12px; font-weight: 700; text-decoration: none; text-align: center;">
                    📄 Actas de Baja
                </a>
                <a href="{{ route('actas.transferencia.index') }}" style="padding: 10px 14px; background: #eff6ff; color: #1d4ed8; border: 1px solid #dbeafe; border-radius: 10px; font-size: 12px; font-weight: 700; text-decoration: none; text-align: center;">
                    🔄 Transferencias
                </a>
            </div>
        </div>

        {{-- INVENTARIO GENERAL CARD --}}
        <div style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
                <h4 style="font-size: 16px; font-weight: 800; color: #0f172a; margin: 0;">Inventario General</h4>
                <span style="font-size: 10px; font-weight: 800; background: #eff6ff; color: #1d4ed8; padding: 3px 8px; border-radius: 6px; text-transform: uppercase;">Principal</span>
            </div>
            <p style="font-size: 13px; color: #64748b; margin-bottom: 20px;">Todos los activos vigentes registrados en el sistema institucional.</p>

            <div style="display: flex; gap: 10px;">
                <a href="{{ route('activos.index') }}" style="flex: 1; padding: 10px; background: #2563eb; color: white; border-radius: 10px; font-size: 12px; font-weight: 700; text-decoration: none; text-align: center;">
                    Ver Activos
                </a>
                <a href="{{ route('reportes.pdf') }}" target="_blank" style="flex: 1; padding: 10px; background: #ef4444; color: white; border-radius: 10px; font-size: 12px; font-weight: 700; text-decoration: none; text-align: center;">
                    PDF General
                </a>
            </div>
        </div>

    </div>

    <script>
        function filtrarAmbientesPorCarrera(idCarrera) {
            const selectAmbiente = document.getElementById('filtro-ambiente-reporte');
            const opciones = selectAmbiente.querySelectorAll('option');

            selectAmbiente.value = '';
            opciones.forEach(opt => {
                if (opt.value === '') {
                    opt.textContent = !idCarrera ? '— Primero selecciona una carrera —' : '— Sin ambientes en esta carrera —';
                    opt.style.display = 'block';
                    return;
                }
                const carreraOpt = opt.getAttribute('data-carrera');
                if (!idCarrera || carreraOpt === idCarrera) {
                    opt.style.display = 'block';
                } else {
                    opt.style.display = 'none';
                }
            });
        }

        function filtrarAmbientesPorCarreraCat(idCarrera) {
            const selectAmbiente = document.getElementById('filtro-ambiente-cat');
            const opciones = selectAmbiente.querySelectorAll('option');

            selectAmbiente.value = '';
            opciones.forEach(opt => {
                if (opt.value === '') {
                    opt.textContent = !idCarrera ? '— Todos los ambientes —' : '— Sin ambientes en esta carrera —';
                    opt.style.display = 'block';
                    return;
                }
                const carreraOpt = opt.getAttribute('data-carrera');
                if (!idCarrera || carreraOpt === idCarrera) {
                    opt.style.display = 'block';
                } else {
                    opt.style.display = 'none';
                }
            });
        }
    </script>
</div>
@endsection
