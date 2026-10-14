<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="SISActivos - Sistema de Control y Gestión de Activos Fijos">
    <meta name="theme-color" content="#2563eb">
    <link rel="manifest" href="/manifest.json">
    <title>@yield('title', 'SISActivos') - Sistema de Control y Gestión</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- Cola local: permite escanear y registrar sin conexión --}}
    @vite(['resources/js/offline.js'])

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --sidebar-bg: #111c44;
            --sidebar-hover: #1b285a;
            --sidebar-active: #2563eb;
            --body-bg: #f4f7fe;
            --card-bg: #ffffff;
            --text-dark: #1e293b;
            --text-muted: #8fa0b5;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        html,
        body {
            background-color: var(--body-bg);
            height: 100%;
            overflow-x: hidden;
        }

        body {
            display: flex;
        }

        /* ============ SIDEBAR ============ */
        .sidebar {
            width: 260px;
            background-color: var(--sidebar-bg);
            color: #ffffff;
            display: flex;
            flex-direction: column;
            padding: 24px 16px;
            flex-shrink: 0;
            overflow-y: auto;
            height: 100vh;
            position: sticky;
            top: 0;
        }

        .sidebar::-webkit-scrollbar {
            width: 5px;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 4px;
        }

        .brand {
            font-size: 20px;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 25px;
            padding-left: 12px;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .menu-category {
            font-size: 11px;
            text-transform: uppercase;
            color: var(--text-muted);
            font-weight: 700;
            padding: 12px 12px 6px;
            letter-spacing: 0.08em;
        }

        .nav-item {
            display: flex;
            align-items: center;
            padding: 10px 14px;
            color: #d1d5db;
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 600;
            border-radius: 10px;
            margin-bottom: 4px;
            transition: all 0.2s ease;
        }

        .nav-item:hover {
            background-color: var(--sidebar-hover);
            color: #ffffff;
        }

        .nav-item.active {
            background-color: var(--sidebar-active);
            color: #ffffff;
        }

        .logout-form {
            margin-top: auto;
            padding-top: 20px;
        }

        .logout-form button {
            width: 100%;
            background: #dc2626;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s;
        }

        .logout-form button:hover {
            background: #b91c1c;
        }

        /* ============ MAIN ============ */
        .main-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            min-height: 100vh;
        }

        .top-navbar {
            background: #ffffff;
            min-height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 32px;
            border-bottom: 1px solid #e2e8f0;
            position: sticky;
            top: 0;
            z-index: 40;
            gap: 16px;
            flex-wrap: wrap;
        }

        .content-area {
            padding: 24px 32px 32px;
        }

        /* ============ BOTN MOBILE MENU ============ */
        .mobile-menu-toggle {
            display: none;
            width: 42px;
            height: 42px;
            border-radius: 10px;
            background: #eff6ff;
            color: #2563eb;
            border: 1px solid #dbeafe;
            cursor: pointer;
            font-size: 20px;
            align-items: center;
            justify-content: center;
        }

        /* ============ OVERLAY MOBILE ============ */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 99;
        }

        .sidebar-overlay.active {
            display: block;
        }

        /* ============ RESPONSIVE ============ */
        @media (max-width: 980px) {
            .sidebar {
                position: fixed;
                left: -260px;
                top: 0;
                bottom: 0;
                z-index: 100;
                transition: left 0.3s;
            }

            .sidebar.open {
                left: 0;
            }

            .mobile-menu-toggle {
                display: inline-flex;
            }

            .top-navbar {
                padding: 12px 16px;
                flex-direction: row;
                align-items: center;
            }

            .content-area {
                padding: 16px;
            }
        }

        /* ============ LOADING BAR ============ */
        .loading-bar {
            position: fixed;
            top: 0;
            left: 0;
            height: 3px;
            background: linear-gradient(90deg, #2563eb, #7c3aed, #2563eb);
            background-size: 200% 100%;
            animation: loading 1.5s linear infinite;
            z-index: 9999;
            display: none;
        }

        .loading-bar.active {
            display: block;
        }

        @keyframes loading {
            0% {
                background-position: 0% 0;
                width: 0%;
            }

            50% {
                background-position: 100% 0;
                width: 70%;
            }

            100% {
                background-position: 0% 0;
                width: 100%;
            }
        }

        /* ============ ANIMACIN DEL ÃCONO DE BIENVENIDA ============ */
        @keyframes successPop {
            0% {
                transform: scale(0) rotate(-180deg);
                opacity: 0;
            }

            50% {
                transform: scale(1.2) rotate(10deg);
                opacity: 1;
            }

            100% {
                transform: scale(1) rotate(0deg);
                opacity: 1;
            }
        }

        .success-pop {
            animation: successPop 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
    </style>

    @stack('styles')
</head>

<body>

    {{-- Barra de carga global --}}
    <div class="loading-bar" id="loadingBar"></div>

    {{-- Overlay para menÃº móvil --}}
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    {{-- ============ SIDEBAR ============ --}}
    <aside class="sidebar" id="sidebar">
        <div class="brand">
            <span
                style="display:inline-flex;width:32px;height:32px;background:#2563eb;border-radius:8px;align-items:center;justify-content:center;font-size:16px;">ðŸ“¦</span>
            SISActivos
        </div>

        @auth
            @php $__user = auth()->user(); @endphp

            {{-- INICIO --}}
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard*') ? 'active' : '' }}">
                ðŸ  Inicio
            </a>

            {{-- CARRERAS --}}
            <div class="menu-category">CARRERAS</div>
            @php
                $__idsCarreras = $__user->idsCarrerasVisibles();
                $__carreras = \App\Models\Carrera::when(
                    $__idsCarreras === null,
                    fn($q) => $q,
                    fn($q) => $q->whereIn('id_carrera', $__idsCarreras),
                )
                    ->orderBy('nombre')
                    ->get();
            @endphp

            @forelse($__carreras as $car)
                <a href="{{ route('carreras.show', $car->id_carrera) }}"
                    class="nav-item {{ request()->routeIs('carreras.show') && request()->route('id_carrera') == $car->id_carrera ? 'active' : '' }}">
                    🎓 {{ $car->nombre }}
                </a>
            @empty
                <div style="padding: 8px 14px; font-size: 12px; color: #64748b; font-style: italic;">
                    Sin carreras a su alcance
                </div>
            @endforelse

            {{-- CONTROL Y OPERACIONES --}}
            <div class="menu-category">CONTROL Y OPERACIONES</div>

            @if ($__user->puedeRegistrarVerificacion())
                <a href="{{ route('verificaciones.index') }}"
                    class="nav-item {{ request()->routeIs('verificaciones.*') && !request()->routeIs('verificaciones-aprobacion.*') ? 'active' : '' }}">
                    🔍 Verificación Física
                    <span id="offline-pendientes"
                        style="display: none; background: #f59e0b; color: white; font-size: 10px; padding: 2px 6px; border-radius: 4px; margin-left: auto; font-weight: 800;"
                        title="Verificaciones guardadas en este equipo, pendientes de enviar">0</span>

            @endif

            @if ($__user->puedeAprobar())
                @php
                    $__pendientes =
                        \App\Models\ActaBaja::pendientes()->count() +
                        \App\Models\ActaTransferencia::pendientes()->count() +
                        \App\Models\VerificacionFisica::pendientesAprobacion()->count();
                @endphp
                <a href="{{ route('aprobaciones.index') }}"
                    class="nav-item {{ request()->routeIs('aprobaciones.*') ? 'active' : '' }}">
                    âœ… Aprobaciones
                    @if ($__pendientes > 0)
                        <span
                            style="background: #ef4444; color: white; font-size: 9px; padding: 2px 6px; border-radius: 4px; margin-left: auto; font-weight: 800;">{{ $__pendientes }}</span>
                    @endif

            @endif

            @if ($__user->puedeSolicitarBajaOTransferencia())
                <a href="{{ route('actas.baja.index') }}"
                    class="nav-item {{ request()->routeIs('actas.baja.*') ? 'active' : '' }}">
                    📄 Actas de Baja
                    @if ($__user->puedeAprobar())
                        <span
                            style="background: #ef4444; color: white; font-size: 9px; padding: 2px 6px; border-radius: 4px; margin-left: auto; font-weight: 800;">APRUEBA</span>
                    @endif


                <a href="{{ route('actas.transferencia.index') }}"
                    class="nav-item {{ request()->routeIs('actas.transferencia.*') ? 'active' : '' }}">
                    📄 Transferencias
                    @if ($__user->puedeAprobar())
                        <span
                            style="background: #ef4444; color: white; font-size: 9px; padding: 2px 6px; border-radius: 4px; margin-left: auto; font-weight: 800;">APRUEBA</span>
                    @endif

            @endif
            <a href="{{ route('categorias.index') }}"
                class="nav-item {{ request()->routeIs('categorias.*') ? 'active' : '' }}">
                ðŸ“¦ Categoras
            </a>
            <a href="{{ route('activos.index') }}"
                class="nav-item {{ request()->routeIs('activos.*') && !request()->routeIs('activos.no-activos') ? 'active' : '' }}">
                ðŸ“¦ Activos
            </a>

            {{--  NUEVO: No Activos --}}
            <a href="{{ route('activos.noActivos') }}"
                class="nav-item {{ request()->routeIs('activos.no-activos') ? 'active' : '' }}">
                🔍Å¡ No Activos
            </a>
            <a href="{{ route('activos.baja') }}"
                class="nav-item {{ request()->routeIs('activos.baja') ? 'active' : '' }}">
                🔍â€”â€˜Ã¯ Activos de Baja
            </a>
            <a href="{{ route('items.index') }}" class="nav-item {{ request()->routeIs('items.*') ? 'active' : '' }}">
                ðŸ·ï¸ Ãtems
            </a>
            @if ($__user->esAdmin() || $__user->esOperativo() || $__user->esJefeCarrera() || $__user->esCustodio())
            <a href="{{ route('reportes.index') }}" class="nav-item {{ request()->routeIs('reportes.*') ? 'active' : '' }}">
                ðŸ“Š Reportes
            </a>
            @endif

            @if ($__user->esAdmin() || $__user->esOperativo() || $__user->esJefeCarrera())
                <a href="{{ route('actas.liberacion.index') }}"
                    class="nav-item {{ request()->routeIs('actas.liberacion.*') ? 'active' : '' }}">
                    🔍Å¡ Liberación

            @endif

            {{-- ADMINISTRACIN --}}
            @if ($__user->esAdmin())
                <div class="menu-category">ADMINISTRACIN</div>



                <a href="{{ route('backup.index') }}"
                    class="nav-item {{ request()->routeIs('backup.*') ? 'active' : '' }}">
                    🔍â€™Â¾ Copias de Seguridad
                </a>

                <a href="{{ route('configuracion.index') }}"
                    class="nav-item {{ request()->routeIs('configuracion.*') ? 'active' : '' }}">
                    âš™ï¸ Configuración

            @endif

            <form action="{{ route('logout') }}" method="POST" class="logout-form">
                @csrf
                <button type="submit">Cerrar sesión</button>
            </form>
        @endauth
    </aside>

    {{-- ============ CONTENT WRAPPER ============ --}}
    <div class="main-wrapper">

        {{-- ============ TOP NAVBAR ============ --}}
        {{-- ============ TOP NAVBAR ============ --}}
        <header class="top-navbar">

            <div style="display: flex; align-items: center; gap: 12px; flex: 1; min-width: 280px; max-width: 800px;">

                <button type="button" class="mobile-menu-toggle" onclick="toggleSidebar()" title="Abrir menÃº">
                    
                </button>

                {{--  UN Å¡NICO BOTN "Volver" QUE REGRESA EXACTAMENTE DE DONDE SALISTE --}}
                @if (!request()->is('/') && !request()->is('dashboard'))
                    <button type="button" onclick="history.back()" title="Volver"
                        style="display: inline-flex; align-items: center; justify-content: center; gap: 6px; height: 42px; padding: 0 16px; border-radius: 10px; background: #eff6ff; color: #2563eb; border: 1px solid #dbeafe; font-weight: 700; font-size: 13px; cursor: pointer; white-space: nowrap; text-decoration: none; transition: all 0.2s ease;"
                        onmouseover="this.style.background='#dbeafe';" onmouseout="this.style.background='#eff6ff';">
                        <span style="font-size: 16px;">â€ </span>
                        <span>Volver</span>
                    </button>
                @endif

                <form method="GET" action="{{ route('activos.index') }}"
                    style="display: flex; align-items: center; gap: 8px; flex: 1; margin: 0;">
                    <div style="position: relative; flex: 1;">
                        <input type="text" name="buscar" value="{{ request('buscar') }}"
                            placeholder="Buscar activo por código, nombre, serie, marca..." autocomplete="off"
                            style="width: 100%; height: 42px; padding: 0 42px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; outline: none; background: #f8fafc; color: #0f172a; transition: all 0.2s ease;"
                            onfocus="this.style.background='#ffffff'; this.style.borderColor='#2563eb';"
                            onblur="this.style.background='#f8fafc'; this.style.borderColor='#cbd5e1';">
                        <span
                            style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #64748b; font-size: 14px; pointer-events: none;">🔍</span>
                    </div>

                    <button type="submit"
                        style="height: 42px; padding: 0 20px; border-radius: 10px; background: #2563eb; color: #ffffff; border: none; font-weight: 700; font-size: 13px; cursor: pointer; white-space: nowrap; transition: background 0.2s ease;"
                        onmouseover="this.style.background='#1d4ed8';" onmouseout="this.style.background='#2563eb';">
                        Buscar
                    </button>
                </form>
            </div>

            <div style="display: flex; align-items: center; gap: 16px;">
                @auth
                    @if (auth()->user()->esAdmin())
                        <a href="{{ route('usuarios.index') }}" title="Gestión de Usuarios"
                            style="width: 42px; height: 42px; background-color: #eff6ff; color: #2563eb; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; font-size: 18px; box-shadow: 0 2px 5px rgba(37, 99, 235, 0.15); transition: all 0.2s ease;"
                            onmouseover="this.style.backgroundColor='#2563eb'; this.style.color='#fff';"
                            onmouseout="this.style.backgroundColor='#eff6ff'; this.style.color='#2563eb';">
                            🔍â€˜

                    @endif

                    <div style="text-align: right; border-left: 1px solid #e2e8f0; padding-left: 16px;">
                        <span
                            style="display: block; font-size: 13px; font-weight: 700; color: #1e293b; white-space: nowrap;">
                            {{ auth()->user()->nombre_completo }}
                        </span>
                        <span style="font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase;">
                            {{ auth()->user()->rol }}
                        </span>
                    </div>
                @endauth
            </div>
        </header>

        <main class="content-area">
            @yield('content')
        </main>
    </div>

    {{-- ============================================================ --}}
    {{-- 🎓â€° MODAL DE BIENVENIDA (aparece solo tras login exitoso) --}}
    {{-- ============================================================ --}}
    @if (session('login_success'))
        <div id="welcome-modal"
            style="position: fixed; inset: 0; z-index: 99999; display: flex; align-items: center; justify-content: center; padding: 16px; background: rgba(15, 23, 42, 0.7); backdrop-filter: blur(4px); opacity: 0; transition: opacity 0.3s ease;">

            <div id="welcome-card"
                style="position: relative; background: white; border-radius: 24px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); max-width: 420px; width: 100%; overflow: hidden; transform: scale(0.9); transition: transform 0.5s ease;">

                {{-- Barra superior decorativa --}}
                <div
                    style="height: 8px; background: linear-gradient(90deg, #34d399, #14b8a6, #34d399); animation: pulse 2s ease-in-out infinite;">
                </div>

                {{-- Botón cerrar --}}
                <button type="button" onclick="cerrarModalBienvenida()"
                    style="position: absolute; top: 16px; right: 16px; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 50%; background: #f1f5f9; color: #475569; border: none; cursor: pointer; z-index: 10; transition: all 0.2s;"
                    onmouseover="this.style.background='#e2e8f0';" onmouseout="this.style.background='#f1f5f9';">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                {{-- Contenido principal --}}
                <div style="padding: 40px 32px 24px; text-align: center;">

                    {{-- Ãcono animado --}}
                    <div
                        style="position: relative; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                        <span
                            style="position: absolute; width: 96px; height: 96px; border-radius: 50%; background: #34d399; opacity: 0.3; animation: ping 1.5s cubic-bezier(0, 0, 0.2, 1) infinite;"></span>
                        <span class="success-pop"
                            style="position: relative; display: inline-flex; align-items: center; justify-content: center; width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, #34d399, #0d9488); color: white; box-shadow: 0 10px 25px -5px rgba(52, 211, 153, 0.5);">
                            <svg width="40" height="40" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </span>
                    </div>

                    {{-- Ttulo --}}
                    <h2 style="font-size: 26px; font-weight: 800; color: #0f172a; margin-bottom: 6px;">
                        Â¡Acceso Correcto!
                    </h2>
                    <p style="font-size: 14px; color: #64748b; margin-bottom: 24px;">
                        Has iniciado sesión exitosamente
                    </p>

                    {{-- Card del usuario --}}
                    <div
                        style="background: linear-gradient(135deg, #f8fafc, #f1f5f9); border-radius: 16px; padding: 20px; border: 1px solid #e2e8f0;">
                        <div style="display: flex; align-items: center; gap: 16px;">
                            {{-- Avatar --}}
                            <div
                                style="flex-shrink: 0; width: 56px; height: 56px; border-radius: 50%; background: linear-gradient(135deg, #3b82f6, #4f46e5); color: white; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 18px; box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3);">
                                {{ strtoupper(substr(Auth::user()->nombre_completo, 0, 2)) }}
                            </div>

                            <div style="flex: 1; text-align: left; min-width: 0;">
                                <p
                                    style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">
                                    Bienvenido
                                </p>
                                <p
                                    style="font-weight: 800; color: #0f172a; font-size: 16px; line-height: 1.2; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    {{ Auth::user()->nombre_completo }}
                                </p>
                                <div
                                    style="display: flex; align-items: center; gap: 8px; margin-top: 8px; flex-wrap: wrap;">
                                    <span
                                        style="display: inline-flex; align-items: center; padding: 3px 8px; background: #dbeafe; color: #1d4ed8; border-radius: 6px; font-size: 10px; font-weight: 800; text-transform: uppercase;">
                                        {{ Auth::user()->rol }}
                                    </span>
                                    @if (Auth::user()->cargo)
                                        <span
                                            style="font-size: 11px; color: #64748b; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            {{ Auth::user()->cargo }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Barra de progreso --}}
                <div style="height: 4px; background: #f1f5f9; overflow: hidden;">
                    <div id="welcome-progress"
                        style="height: 100%; background: linear-gradient(90deg, #34d399, #14b8a6); width: 100%;"></div>
                </div>

            </div>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- SCRIPTS --}}
    {{-- ============================================================ --}}
    @yield('scripts')
    @stack('scripts')

    <script>
        // ============================================================
        // ESTILOS DE ANIMACIONES
        // ============================================================
        (function() {
            const style = document.createElement('style');
            style.textContent = `
            @keyframes pulse {
                0%, 100% { opacity: 1; }
                50% { opacity: 0.7; }
            }
            @keyframes ping {
                75%, 100% { transform: scale(2); opacity: 0; }
            }
        `;
            document.head.appendChild(style);
        })();

        // ============================================================
        // MENÅ¡ MVIL
        // ============================================================
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('open');
            overlay.classList.toggle('active');
        }

        document.getElementById('sidebarOverlay')?.addEventListener('click', toggleSidebar);

        // ============================================================
        // SWEET ALERT  Mensajes de sesión
        // ============================================================
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('status'))
                Swal.fire({
                    icon: 'success',
                    title: 'Â¡â€°xito!',
                    text: @json(session('status')),
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Â¡â€°xito!',
                    text: @json(session('success')),
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif
            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: @json(session('error'))
                });
            @endif
            @if (session('warning'))
                Swal.fire({
                    icon: 'warning',
                    title: 'Atención',
                    text: @json(session('warning'))
                });
            @endif
            @if ($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Errores de validación',
                    html: `<ul style="text-align:left; font-size:13px;">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>`
                });
            @endif
        });

        // ============================================================
        // CONFIRMAR ELIMINACIN
        // ============================================================
        function confirmDelete(formId) {
            Swal.fire({
                title: 'Â¿Estás seguro de eliminar?',
                text: 'Â¡Esta acción no se puede deshacer!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'S, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById(formId);
                    if (form) form.submit();
                }
            });
        }

        // ============================================================
        // LOADING BAR
        // ============================================================
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                const bar = document.getElementById('loadingBar');
                if (bar) bar.classList.add('active');
            });
        });

        // ============================================================
        // 🎓â€° MODAL DE BIENVENIDA
        // ============================================================
        @if (session('login_success'))
            (function() {
                const modal = document.getElementById('welcome-modal');
                const card = document.getElementById('welcome-card');
                const progress = document.getElementById('welcome-progress');
                const DURACION_MS = 4000;

                if (!modal || !card || !progress) return;

                //  Audio de bienvenida
                try {
                    const audio = new Audio("{{ asset('audios/bienvenido.mp3') }}");
                    audio.volume = 0.7;
                    audio.play().catch(e => console.log('🔍â€¡ Audio bloqueado:', e.message));
                } catch (e) {
                    console.error('Error al reproducir audio:', e);
                }

                //  Mostrar modal con animación
                requestAnimationFrame(() => {
                    modal.style.opacity = '1';
                    card.style.transform = 'scale(1)';
                });

                //  Animar barra de progreso
                setTimeout(() => {
                    if (progress) {
                        progress.style.width = '0%';
                        progress.style.transition = `width ${DURACION_MS}ms linear`;
                    }
                }, 100);

                //  Auto-cerrar
                const timeoutId = setTimeout(() => {
                    cerrarModalBienvenida();
                }, DURACION_MS);

                //  Función global para cerrar
                window.cerrarModalBienvenida = function() {
                    clearTimeout(timeoutId);
                    modal.style.opacity = '0';
                    card.style.transform = 'scale(0.9)';

                    setTimeout(() => {
                        modal.remove();
                    }, 300);
                };

                //  Cerrar con clic fuera del card
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        cerrarModalBienvenida();
                    }
                });

                //  Cerrar con ESC
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        cerrarModalBienvenida();
                    }
                });

                //  LIMPIAR EL FLAG DESPUâ€°S DE MOSTRAR EL MODAL 
                setTimeout(() => {
                    fetch('{{ route('login.clear-welcome') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                    }).catch(() => {
                        // Silencioso
                    });
                }, 1500);
            })();
        @endif
    </script>
</body>

</html>














