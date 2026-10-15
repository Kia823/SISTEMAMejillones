@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- ============ MENSAJES ============ --}}
    @if (session('success'))
        <div style="background:#dcfce7;border:1px solid #bbf7d0;color:#166534;padding:14px 20px;border-radius:12px;margin-bottom:20px;font-weight:700;">
            ✓ {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div style="background:#fee2e2;border:1px solid #fecaca;color:#991b1b;padding:14px 20px;border-radius:12px;margin-bottom:20px;font-weight:700;">
            ✗ {{ session('error') }}
        </div>
    @endif

    {{-- ============ ENCABEZADO ============ --}}
    <div style="background:linear-gradient(135deg,#0f172a 0%,#1e3a8a 100%);padding:28px;border-radius:16px;color:white;margin-bottom:24px;box-shadow:0 10px 15px -3px rgba(15,23,42,.2);">
        <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:20px;">
            <div>
                <div style="display:inline-block;background:rgba(255,255,255,.15);padding:5px 12px;border-radius:20px;font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.08em;">
                    💾 Sistema
                </div>
                <h1 style="font-size:28px;font-weight:800;margin:10px 0 6px 0;">Copias de Seguridad (Backup)</h1>
                <p style="opacity:.9;font-size:14px;margin:0;">
                    Gestión, generación y restauración de la base de datos del sistema.
                </p>
            </div>

            <div style="display:flex;gap:10px;flex-wrap:wrap;">
                <button type="button" onclick="openImportarModal()"
                    style="background:#10b981;color:white;border:none;padding:12px 20px;border-radius:10px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:6px;font-size:14px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="17 8 12 3 7 8"></polyline>
                        <line x1="12" y1="3" x2="12" y2="15"></line>
                    </svg>
                    Subir Copia Externa
                </button>

                <button type="button" onclick="crearBackup()"
                    style="background:#2563eb;color:white;border:none;padding:12px 20px;border-radius:10px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:6px;font-size:14px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    Crear Copia de Seguridad
                </button>
            </div>
        </div>

        <div style="margin-top:16px;padding:12px 16px;background:rgba(255,255,255,.1);border-radius:10px;font-size:12px;">
            <strong>ℹ️ Protección de Datos:</strong>
            Los respaldos se generan y almacenan de forma segura y privada en el servidor local.
            Se recomienda realizar copias periódicas y descargarlas a un almacenamiento externo.
        </div>
    </div>

    {{-- ============ TARJETAS DE ESTADÍSTICAS ============ --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;margin-bottom:24px;">

        <div style="background:white;border-radius:14px;padding:20px;box-shadow:0 2px 4px rgba(0,0,0,.04);border:1px solid #e2e8f0;">
            <div style="display:flex;justify-content:space-between;align-items:center;">
                <div>
                    <div style="font-size:12px;color:#64748b;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">Copias Creadas</div>
                    <div style="font-size:32px;font-weight:800;color:#0f172a;margin-top:6px;">{{ $totalCopias }}</div>
                    <div style="font-size:11px;color:#64748b;margin-top:2px;">archivos almacenados</div>
                </div>
                <div style="width:48px;height:48px;border-radius:12px;background:#eff6ff;color:#2563eb;display:flex;align-items:center;justify-content:center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="21 8 21 21 3 21 3 8"></polyline>
                        <rect x="1" y="3" width="22" height="5"></rect>
                        <line x1="10" y1="12" x2="14" y2="12"></line>
                    </svg>
                </div>
            </div>
        </div>

        <div style="background:white;border-radius:14px;padding:20px;box-shadow:0 2px 4px rgba(0,0,0,.04);border:1px solid #e2e8f0;">
            <div style="display:flex;justify-content:space-between;align-items:center;">
                <div>
                    <div style="font-size:12px;color:#64748b;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">Espacio Ocupado</div>
                    <div style="font-size:26px;font-weight:800;color:#10b981;margin-top:6px;">{{ number_format($espacioTotal / 1024, 2) }} KB</div>
                    <div style="font-size:11px;color:#64748b;margin-top:2px;">en backups</div>
                </div>
                <div style="width:48px;height:48px;border-radius:12px;background:#dcfce7;color:#10b981;display:flex;align-items:center;justify-content:center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                    </svg>
                </div>
            </div>
        </div>

        <div style="background:white;border-radius:14px;padding:20px;box-shadow:0 2px 4px rgba(0,0,0,.04);border:1px solid #e2e8f0;">
            <div style="display:flex;justify-content:space-between;align-items:center;">
                <div>
                    <div style="font-size:12px;color:#64748b;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">Base de Datos</div>
                    <div style="font-size:26px;font-weight:800;color:#2563eb;margin-top:6px;">{{ number_format($dbSize / 1024 / 1024, 2) }} MB</div>
                    <div style="font-size:11px;color:#64748b;margin-top:2px;">{{ $dbName }}</div>
                </div>
                <div style="width:48px;height:48px;border-radius:12px;background:#dbeafe;color:#2563eb;display:flex;align-items:center;justify-content:center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                        <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                        <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div style="background:white;border-radius:14px;padding:20px;box-shadow:0 2px 4px rgba(0,0,0,.04);border:1px solid #e2e8f0;">
            <div style="display:flex;justify-content:space-between;align-items:center;">
                <div>
                    <div style="font-size:12px;color:#64748b;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">Último Respaldo</div>
                    <div style="font-size:18px;font-weight:800;color:#f59e0b;margin-top:6px;">
                        @if($ultimaCopia)
                            {{ $ultimaCopia['fecha']->diffForHumans() }}
                        @else
                            Sin copias
                        @endif
                    </div>
                    <div style="font-size:11px;color:#64748b;margin-top:2px;">
                        @if($ultimaCopia)
                            {{ $ultimaCopia['fecha']->format('d/m/Y H:i:s') }}
                        @endif
                    </div>
                </div>
                <div style="width:48px;height:48px;border-radius:12px;background:#fef3c7;color:#f59e0b;display:flex;align-items:center;justify-content:center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
            </div>
        </div>

    </div>

    {{-- ============ LISTADO DE COPIAS ============ --}}
    <div style="background:white;border-radius:16px;border:1px solid #e2e8f0;padding:20px;box-shadow:0 4px 6px -1px rgba(0,0,0,.05);">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
            <h3 style="font-size:16px;font-weight:800;color:#0f172a;margin:0;">📋 Listado de Copias de Seguridad</h3>
            <span style="background:#eff6ff;color:#2563eb;font-size:11px;font-weight:700;padding:4px 12px;border-radius:20px;">
                {{ $totalCopias }} archivo(s) disponible(s)
            </span>
        </div>

        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                <thead>
                    <tr style="background:#f8fafc;color:#64748b;text-transform:uppercase;font-size:11px;">
                        <th style="padding:12px 10px;text-align:left;border-bottom:1px solid #e2e8f0;">#</th>
                        <th style="padding:12px 10px;text-align:left;border-bottom:1px solid #e2e8f0;">Archivo de Respaldo</th>
                        <th style="padding:12px 10px;text-align:left;border-bottom:1px solid #e2e8f0;">Formato</th>
                        <th style="padding:12px 10px;text-align:left;border-bottom:1px solid #e2e8f0;">Tamaño</th>
                        <th style="padding:12px 10px;text-align:left;border-bottom:1px solid #e2e8f0;">Fecha y Hora</th>
                        <th style="padding:12px 10px;text-align:left;border-bottom:1px solid #e2e8f0;">Tipo</th>
                        <th style="padding:12px 10px;text-align:left;border-bottom:1px solid #e2e8f0;">Creado Por</th>
                        <th style="padding:12px 10px;text-align:center;border-bottom:1px solid #e2e8f0;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($copias as $i => $c)
                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:12px 10px;color:#64748b;font-weight:700;">{{ $i + 1 }}</td>

                            <td style="padding:12px 10px;">
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <span style="width:26px;height:26px;background:#dcfce7;color:#16a34a;border-radius:6px;display:inline-flex;align-items:center;justify-content:center;font-size:14px;">
                                        💾
                                    </span>
                                    <span style="font-weight:700;color:#0f172a;font-size:12px;">{{ $c['nombre'] }}</span>
                                </div>
                            </td>

                            <td style="padding:12px 10px;">
                                <span style="background:#dcfce7;color:#166534;font-size:11px;font-weight:700;padding:3px 8px;border-radius:6px;">SQL</span>
                            </td>

                            <td style="padding:12px 10px;color:#475569;font-weight:600;">
                                {{ number_format($c['tamano'] / 1024, 2) }} KB
                            </td>

                            <td style="padding:12px 10px;">
                                <div style="font-weight:700;color:#0f172a;font-size:12px;">{{ $c['fecha']->format('d/m/Y') }}</div>
                                <div style="color:#64748b;font-size:11px;">{{ $c['fecha']->format('H:i:s A') }}</div>
                            </td>

                            <td style="padding:12px 10px;">
                                @if($c['tipo'] === 'Auto')
                                    <span style="background:#f3e8ff;color:#7c3aed;font-size:11px;font-weight:700;padding:3px 8px;border-radius:6px;">🤖 Auto</span>
                                @elseif($c['tipo'] === 'Manual')
                                    <span style="background:#dbeafe;color:#2563eb;font-size:11px;font-weight:700;padding:3px 8px;border-radius:6px;">👤 Manual</span>
                                @else
                                    <span style="background:#f1f5f9;color:#475569;font-size:11px;font-weight:700;padding:3px 8px;border-radius:6px;">⚙️ Sistema</span>
                                @endif
                            </td>

                            <td style="padding:12px 10px;color:#475569;font-size:12px;">
                                {{ $c['tipo'] === 'Auto' ? 'Sistema' : Auth::user()->nombre_completo }}
                            </td>

                            <td style="padding:12px 10px;text-align:center;">
                                <div style="display:flex;gap:6px;justify-content:center;flex-wrap:wrap;">

                                    {{-- Descargar --}}
                                    <a href="{{ route('backup.descargar', $c['nombre']) }}"
                                       title="Descargar copia"
                                       style="width:34px;height:34px;background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;text-decoration:none;transition:all .18s ease;"
                                       onmouseover="this.style.background='#2563eb'; this.style.color='white';"
                                       onmouseout="this.style.background='#eff6ff'; this.style.color='#2563eb';">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                            <polyline points="7 10 12 15 17 10"></polyline>
                                            <line x1="12" y1="15" x2="12" y2="3"></line>
                                        </svg>
                                    </a>

                                    {{-- Restaurar --}}
                                    <button type="button"
                                            onclick="restaurarBackup('{{ $c['nombre'] }}')"
                                            title="Restaurar esta copia"
                                            style="width:34px;height:34px;background:#fef3c7;color:#f59e0b;border:1px solid #fde68a;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;transition:all .18s ease;"
                                            onmouseover="this.style.background='#f59e0b'; this.style.color='white';"
                                            onmouseout="this.style.background='#fef3c7'; this.style.color='#f59e0b';">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"></path>
                                            <path d="M21 3v5h-5"></path>
                                            <path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"></path>
                                            <path d="M8 16H3v5"></path>
                                        </svg>
                                    </button>

                                    {{-- Eliminar --}}
                                    <button type="button"
                                            onclick="eliminarBackup('{{ $c['nombre'] }}')"
                                            title="Eliminar copia"
                                            style="width:34px;height:34px;background:#fee2e2;color:#dc2626;border:1px solid #fecaca;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;transition:all .18s ease;"
                                            onmouseover="this.style.background='#dc2626'; this.style.color='white';"
                                            onmouseout="this.style.background='#fee2e2'; this.style.color='#dc2626';">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                            <line x1="10" y1="11" x2="10" y2="17"></line>
                                            <line x1="14" y1="11" x2="14" y2="17"></line>
                                        </svg>
                                    </button>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="padding:40px;text-align:center;color:#94a3b8;font-weight:600;">
                                No hay copias de seguridad registradas. Crea una nueva usando el botón superior.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ============ MODAL: IMPORTAR COPIA EXTERNA ============ --}}
<div id="importar-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;justify-content:center;align-items:center;padding:20px;">
    <div style="background:white;border-radius:20px;padding:30px;width:100%;max-width:520px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;border-bottom:1px solid #e2e8f0;padding-bottom:10px;">
            <h3 style="margin:0;font-size:18px;font-weight:800;color:#0f172a;">📤 Subir Copia Externa</h3>
            <button type="button" onclick="closeImportarModal()" style="background:#f1f5f9;border:none;border-radius:50%;width:32px;height:32px;cursor:pointer;font-size:18px;">×</button>
        </div>

        <div style="background:#fef3c7;border:1px solid #fde68a;color:#78350f;padding:12px 16px;border-radius:10px;margin-bottom:16px;font-size:12px;">
            <strong>⚠️ Advertencia:</strong> Restaurar una copia reemplazará <strong>todos los datos actuales</strong> de la base de datos. Asegúrate de tener un backup reciente.
        </div>

        <form method="POST" action="{{ route('backup.importar') }}" enctype="multipart/form-data">
            @csrf

            <div style="margin-bottom:20px;">
                <label style="font-size:12px;font-weight:700;display:block;margin-bottom:6px;color:#334155;">Archivo SQL *</label>
                <input type="file" name="archivo_sql" accept=".sql,.txt" required
                       style="width:100%;padding:10px;border-radius:8px;border:1px solid #cbd5e1;background:#fff;">
                <small style="color:#64748b;font-size:11px;margin-top:4px;display:block;">
                    Solo archivos .sql o .txt. Máximo 50 MB.
                </small>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:10px;border-top:1px solid #e2e8f0;padding-top:15px;">
                <button type="button" onclick="closeImportarModal()"
                    style="background:#e2e8f0;color:#475569;border:none;padding:10px 18px;border-radius:8px;font-weight:700;cursor:pointer;">
                    Cancelar
                </button>
                <button type="submit"
                    style="background:#10b981;color:white;border:none;padding:10px 18px;border-radius:8px;font-weight:700;cursor:pointer;">
                    ⚠️ Restaurar Base de Datos
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ============ MODAL: RESTAURAR COPIA LOCAL ============ --}}
<div id="restaurar-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;justify-content:center;align-items:center;padding:20px;">
    <div style="background:white;border-radius:20px;padding:30px;width:100%;max-width:520px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;border-bottom:1px solid #e2e8f0;padding-bottom:10px;">
            <h3 style="margin:0;font-size:18px;font-weight:800;color:#0f172a;">♻️ Restaurar Copia</h3>
            <button type="button" onclick="closeRestaurarModal()" style="background:#f1f5f9;border:none;border-radius:50%;width:32px;height:32px;cursor:pointer;font-size:18px;">×</button>
        </div>

        <div style="background:#fee2e2;border:1px solid #fecaca;color:#991b1b;padding:14px 16px;border-radius:10px;margin-bottom:16px;font-size:13px;">
            <strong>⚠️ ¡Atención!</strong><br>
            Vas a restaurar la base de datos desde la copia:
            <div style="background:white;padding:8px 12px;border-radius:6px;margin-top:8px;font-family:monospace;font-size:11px;color:#0f172a;word-break:break-all;">
                <strong id="restaurar-nombre"></strong>
            </div>
            <div style="margin-top:10px;">
                Todos los datos actuales serán <strong>reemplazados</strong>. Esta acción no se puede deshacer.
            </div>
        </div>

        <div style="display:flex;justify-content:flex-end;gap:10px;border-top:1px solid #e2e8f0;padding-top:15px;">
            <button type="button" onclick="closeRestaurarModal()"
                style="background:#e2e8f0;color:#475569;border:none;padding:10px 18px;border-radius:8px;font-weight:700;cursor:pointer;">
                Cancelar
            </button>
            <button type="button" id="btn-confirmar-restaurar"
                style="background:#dc2626;color:white;border:none;padding:10px 18px;border-radius:8px;font-weight:700;cursor:pointer;">
                ⚠️ Restaurar Ahora
            </button>
        </div>
    </div>
</div>

{{-- ============ SCRIPT ============ --}}
<script>
    // ============================================
    // CREAR BACKUP
    // ============================================
    async function crearBackup() {
        const result = await Swal.fire({
            title: '¿Crear copia de seguridad?',
            html: `
                <div style="text-align:left;background:#eff6ff;padding:12px;border-radius:8px;border:1px solid #bfdbfe;font-size:13px;">
                    Se generará un archivo <strong>.sql</strong> con toda la estructura y datos de la base de datos.
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#64748b',
            confirmButtonText: '💾 Sí, crear',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        });

        if (!result.isConfirmed) return;

        Swal.fire({
            title: 'Generando copia...',
            html: 'Por favor espere',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        try {
            const res = await fetch('{{ route("backup.crear") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ tipo: 'Manual' }),
            });

            const json = await res.json();

            if (json.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Copia creada!',
                    html: `
                        <div style="text-align:left;font-size:13px;">
                            <div><strong>Archivo:</strong> ${json.archivo}</div>
                            <div><strong>Tamaño:</strong> ${(json.tamano / 1024).toFixed(2)} KB</div>
                        </div>
                    `,
                    confirmButtonColor: '#2563eb',
                }).then(() => window.location.reload());
            } else {
                Swal.fire('Error', json.mensaje, 'error');
            }
        } catch (err) {
            console.error(err);
            Swal.fire('Error', 'No se pudo crear la copia.', 'error');
        }
    }

    // ============================================
    // IMPORTAR
    // ============================================
    function openImportarModal() {
        document.getElementById('importar-modal').style.setProperty('display', 'flex', 'important');
    }
    function closeImportarModal() {
        document.getElementById('importar-modal').style.setProperty('display', 'none', 'important');
    }

    // ============================================
    // RESTAURAR COPIA LOCAL
    // ============================================
    let archivoSeleccionado = null;

    function restaurarBackup(nombre) {
        archivoSeleccionado = nombre;
        document.getElementById('restaurar-nombre').textContent = nombre;
        document.getElementById('restaurar-modal').style.setProperty('display', 'flex', 'important');
    }

    function closeRestaurarModal() {
        document.getElementById('restaurar-modal').style.setProperty('display', 'none', 'important');
        archivoSeleccionado = null;
    }

    document.getElementById('btn-confirmar-restaurar')?.addEventListener('click', async function() {
        if (!archivoSeleccionado) return;

        Swal.fire({
            title: 'Confirmar restauración',
            text: 'Escribe CONFIRMAR para restaurar la base de datos',
            input: 'text',
            inputPlaceholder: 'CONFIRMAR',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            confirmButtonText: '⚠️ Restaurar',
            cancelButtonText: 'Cancelar',
            inputValidator: (value) => {
                if (value !== 'CONFIRMAR') {
                    return 'Debes escribir exactamente CONFIRMAR';
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Restaurando...',
                    html: 'Por favor espere',
                    allowOutsideClick: false,
                    didOnOpen: () => Swal.showLoading(),
                    didOpen: () => Swal.showLoading(),
                });
                // Aquí iría la petición AJAX. Por simplicidad, un alert.
                setTimeout(() => {
                    Swal.fire('Info', 'Función de restauración disponible próximamente.', 'info');
                }, 1000);
            }
        });
    });

    // ============================================
    // ELIMINAR COPIA
    // ============================================
    function eliminarBackup(nombre) {
        Swal.fire({
            title: '¿Eliminar copia de seguridad?',
            html: `
                <div style="text-align:left;background:#fef2f2;padding:12px;border-radius:8px;border:1px solid #fecaca;font-size:13px;">
                    <div><strong>Archivo:</strong></div>
                    <div style="font-family:monospace;font-size:11px;color:#0f172a;word-break:break-all;margin-top:4px;">${nombre}</div>
                </div>
                <p style="margin-top:12px;color:#dc2626;font-size:12px;font-weight:600;">
                    ⚠️ Esta acción no se puede deshacer.
                </p>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: '🗑️ Sí, eliminar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then(result => {
            if (result.isConfirmed) {
                // Crear form dinámico
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/backup/eliminar/' + encodeURIComponent(nombre);

                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = '{{ csrf_token() }}';

                const method = document.createElement('input');
                method.type = 'hidden';
                method.name = '_method';
                method.value = 'DELETE';

                form.appendChild(csrf);
                form.appendChild(method);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>
@endsection
