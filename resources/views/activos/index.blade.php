@extends('layouts.app')

@section('content')
    <style>
        .actions { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 12px; }
        .btn.secondary { background: #0f766e; }
        .btn.danger { background: #b91c1c; }
        .btn.light { background: #e8f0ff; color: #1d4ed8; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 10px; border-bottom: 1px solid var(--border); text-align: left; vertical-align: middle; }
        th { font-size: 12px; text-transform: uppercase; color: var(--muted); }
        .qr-image { width: 52px; height: 52px; object-fit: contain; }
        .modal {
            position: fixed; inset: 0; z-index: 999; display: none;
            align-items: center; justify-content: center; padding: 20px;
            background: rgba(15, 23, 42, .65);
        }
        .modal.open { display: flex; }
        .modal-box { width: min(900px, 100%); max-height: 92vh; overflow-y: auto; background: white; border-radius: 18px; padding: 22px; }
        .modal-head { display: flex; justify-content: space-between; align-items: center; gap: 14px; margin-bottom: 14px; }
        .close { border: 0; background: #e2e8f0; border-radius: 999px; width: 34px; height: 34px; cursor: pointer; font-size: 20px; }
        #qr-reader { width: 100%; max-width: 500px; margin: 0 auto; border-radius: 12px; overflow: hidden; }
        #scanner-status { text-align: center; color: var(--muted); min-height: 24px; margin-top: 10px; }

        .btn-icon {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 15px;
            border: 1px solid transparent;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .btn-icon:hover { transform: scale(1.08); }
        .btn-icon-ver { background: #eff6ff; color: #2563eb; border-color: #bfdbfe; }
        .btn-icon-ver:hover { background: #2563eb; color: white; }
        .btn-icon-editar { background: #fef9c3; color: #ca8a04; border-color: #fde047; }
        .btn-icon-editar:hover { background: #ca8a04; color: white; }
        .btn-icon-qr { background: #f3e8ff; color: #7c3aed; border-color: #ddd6fe; }
        .btn-icon-qr:hover { background: #7c3aed; color: white; }
        .btn-icon-pdf { background: #fee2e2; color: #dc2626; border-color: #fecaca; }
        .btn-icon-pdf:hover { background: #dc2626; color: white; }
        .btn-icon-excel { background: #dcfce7; color: #16a34a; border-color: #bbf7d0; }
        .btn-icon-excel:hover { background: #16a34a; color: white; }
        .btn-icon-eliminar { background: #dc2626; color: white; border-color: #b91c1c; }
        .btn-icon-eliminar:hover { background: #b91c1c; }

        .numero-col {
            width: 45px;
            text-align: center;
            font-weight: 800;
            color: #64748b;
        }
    </style>

    <section class="hero-card">
        <div>
            <div style="display:inline-block;padding:6px 10px;border-radius:999px;background:rgba(255,255,255,.18);font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;">Activos fijos</div>
            <h1 style="margin:10px 0 8px;font-size:28px;">Gestión de activos fijos</h1>
            <p>Registra activos, genera códigos QR automáticamente y consulta el inventario.</p>
        </div>
        <div class="stat-grid">
            <div><strong>{{ isset($activos) ?$activos->count() : 0 }}</strong><span>Activos registrados</span></div>
            <div><strong>QR</strong><span>Generación automática</span></div>
            <div><strong>PDF</strong><span>Reporte imprimible</span></div>
            <div><strong>Cámara</strong><span>Escaneo QR</span></div>
        </div>
    </section>

    <section class="panel" style="margin-top: 20px;">
        <div class="modal-head">
            <div>
                <h2>Inventario registrado</h2>
                <p>Administra cada activo y su código QR.</p>
            </div>
            <div class="actions" style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center; margin-top: 0;">
                @if (auth()->user()->esAdmin())
                    <a href="{{ route('activos.create') }}"
                        style="background: transparent; color: #2563eb; border: 1.5px solid #2563eb; padding: 8px 16px; border-radius: 10px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; font-size: 14px;">
                        ➕ Nuevo activo
                    </a>
                @endif

                <button type="button" onclick="openScannerModal()"
                    style="background: transparent; color: #0d9488; border: 1.5px solid #0d9488; padding: 8px 16px; border-radius: 10px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; font-size: 14px;">
                    📷 Escanear con cámara
                </button>
            </div>
        </div>

        @php
            $opcionesTipo = [
                'TODOS' => 'Todos los bienes',
                'ACTIVO_FIJO' => 'Solo activos fijos',
                'NO_ACTIVO' => 'Solo no activos',
            ];

            $paramsBase = array_filter([
                'id_ambiente' => $ambiente->id_ambiente ?? null,
                'buscar' => request('buscar'),
                'id_categoria' => request('id_categoria'),
            ], fn ($v) => $v !== null &&$v !== '');

            $linkFiltro = fn ($tipo) => route('activos.index', array_merge($paramsBase, ['tipo_bien' =>$tipo]));
            $linkExport = fn ($ruta, $tipo) => route($ruta, array_merge($paramsBase, ['tipo_bien' =>$tipo]));

            $totalActivos = $totales['ACTIVO_FIJO'];$totalNoActivos = $totales['NO_ACTIVO'];$totalGeneral = $totalActivos +$totalNoActivos;
        @endphp

        {{-- Filtro y exportación por tipo de bien --}}
        <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:12px;margin-bottom:14px;">
            <div style="display:flex;gap:16px;align-items:center;flex-wrap:wrap;justify-content:space-between;">

                <div style="display:flex;gap:6px;flex-wrap:wrap;">
                    @foreach($opcionesTipo as $valor =>$texto)
                        <a href="{{ $linkFiltro($valor) }}"
                           style="padding:8px 14px;border-radius:9px;font-size:13px;font-weight:800;text-decoration:none;border:1.5px solid {{ $tipoBien ===$valor ? '#2563eb' : '#cbd5e1' }};background:{{ $tipoBien ===$valor ? '#2563eb' : '#fff' }};color:{{ $tipoBien ===$valor ? '#fff' : '#475569' }};">
                            {{ $texto }}
                            <span style="opacity:.75;font-weight:700;">
                                ({{ $valor === 'ACTIVO_FIJO' ? $totalActivos : ($valor === 'NO_ACTIVO' ? $totalNoActivos :$totalGeneral) }})
                            </span>
                        </a>
                    @endforeach
                </div>

                <div style="display:flex;gap:6px;flex-wrap:wrap;align-items:center;">
                    <span style="font-size:11px;font-weight:800;color:#64748b;text-transform:uppercase;margin-right:2px;">
                        Exportar
                    </span>
                    @foreach(['pdf' => 'PDF', 'excel' => 'Excel'] as $ruta =>$texto)
                        @foreach($opcionesTipo as $valor =>$etiqueta)
                            <a href="{{ $linkExport('activos.' . $ruta,$valor) }}" target="_blank"
                               style="border:1.5px solid {{ $ruta === 'pdf' ? '#dc2626' : '#16a34a' }};color:{{ $ruta === 'pdf' ? '#dc2626' : '#16a34a' }};padding:6px 10px;border-radius:8px;font-size:11px;font-weight:800;text-decoration:none;white-space:nowrap;">
                                {{ $texto }} · {{$etiqueta }}
                            </a>
                        @endforeach
                    @endforeach
                </div>
            </div>
        </div>

        <div class="table-wrap" style="margin-top: 15px;">
            <table>
                <thead>
                    <tr style="background-color: #f8fafc; color: #64748b; font-size: 11px; font-weight: 800; text-transform: uppercase;">
                        <th class="numero-col" style="padding: 14px 10px;">Nº</th>
                        <th style="padding: 14px 10px; width: 75px; text-align: center;">QR</th>
                        <th style="padding: 14px 10px; width: 75px; text-align: center;">IMAGEN</th>
                        <th style="padding: 14px 10px;">CÓDIGO</th>
                        <th style="padding: 14px 10px; width: 105px;">TIPO</th>
                        <th style="padding: 14px 10px;">ACTIVO</th>
                        <th style="padding: 14px 10px;">UBICACIÓN / CUSTODIO</th>
                        <th style="padding: 14px 10px;">DESCRIPCIÓN</th>
                        <th style="padding: 14px 10px; width: 110px;">FECHA INGRESO</th>
                        <th style="padding: 14px 10px;">ESTADO</th>
                        <th style="padding: 14px 10px; text-align: center;">ACCIONES</th>
                    </tr>
                </thead>
                <tbody id="assets-table">
                    @forelse ($activos as $i =>$activo)
                        <tr id="asset-{{ $activo->id_activo }}" data-code="{{ $activo->codigo_activo }}">

                            <td class="numero-col">{{ $i + 1 }}</td>

                            <td style="text-align: center;">
                                @if($activo->ruta_qr)
                                    <img class="qr-image" src="{{ asset('storage/' . $activo->ruta_qr) }}" alt="QR {{ $activo->codigo_activo }}">
                                @else
                                    <span style="font-size:11px; color:var(--muted);">Sin QR</span>
                                @endif
                            </td>

                            <td style="text-align: center;">
                                @if ($activo->imagen)
                                    <img src="{{ asset('storage/' . $activo->imagen) }}" alt="Foto"
                                        style="width: 52px; height: 52px; object-fit: cover; border-radius: 8px; border: 1px solid #e2e8f0;">
                                @else
                                    <span style="font-size: 11px; color: #94a3b8; font-style: italic;">Sin foto</span>
                                @endif
                            </td>

                            <td>
                                <strong style="font-size: 14px; color: #0f172a; display: block;">{{ $activo->codigo_activo }}</strong>
                                <small style="color: #64748b;">{{ $activo->numero_serie ?? 'S/N' }}</small>
                            </td>

                            <td>
                                <span style="display:inline-block;padding:4px 9px;border-radius:8px;font-size:10px;font-weight:800;text-transform:uppercase;background:{{ $activo->esNoActivo() ? '#fef3c7' : '#eff6ff' }};color:{{ $activo->esNoActivo() ? '#b45309' : '#1d4ed8' }};">
                                    {{ $activo->tipo_bien_texto }}
                                </span>
                            </td>

                            {{-- COLUMNA ACTIVO --}}
                            <td>
                                <div style="font-weight: 700; color: #1e293b;">{{ $activo->nombre }}</div>
                                <div style="color: #64748b; font-size: 11px; margin-top: 2px;">{{ $activo->marca }} {{$activo->modelo }}</div>
                            </td>

                            {{-- COLUMNA UBICACIÓN / CUSTODIO --}}
                            <td>
                                <div style="font-weight: 700; color: #0f172a; text-transform: uppercase;">{{ $activo->ambiente?->nombre ?? 'Sin ambiente' }}</div>
                                <small style="color:var(--muted);">Custodio: {{ $activo->custodio?->nombre_completo ?? 'Sin asignar' }}</small>
                            </td>

                            {{-- ⭐ NUEVA COLUMNA DESCRIPCIÓN INDEPENDIENTE --}}
                            <td>
                                @if (!empty($activo->descripcion))
                                    <div style="color: #475569; font-size: 11px; font-style: italic; background: #f8fafc; padding: 4px 8px; border-radius: 6px; border: 1px solid #e2e8f0; line-height: 1.35; max-width: 220px;">
                                        {{ \Illuminate\Support\Str::limit($activo->descripcion, 120) }}
                                    </div>
                                @else
                                    <span style="color: #94a3b8; font-size: 11px; font-style: italic;">Sin descripción</span>
                                @endif
                            </td>

                            <td style="font-size: 13px; color: #334155; font-weight: 600;">
                                {{ $activo->fecha_adquisicion ? \Carbon\Carbon::parse($activo->fecha_adquisicion)->format('d/m/Y') : ($activo->created_at ? $activo->created_at->format('d/m/Y') : '—') }}
                            </td>

                            <td style="font-weight: 800; font-size: 13px; color: #1d4ed8;">
                                {{ $activo->estado_registro_texto ?? $activo->estado_registro }}
                            </td>

                            {{-- ACCIONES CON ICONOS --}}
                            <td style="text-align: center;">
                                <div style="display:flex; gap:6px; justify-content:center; flex-wrap:wrap;">

                                    {{-- 👁️ Ver --}}
                                    <a href="{{ route('activos.tarjeta', $activo->id_activo) }}" target="_blank"
                                       class="btn-icon btn-icon-ver"
                                       title="Ver tarjeta del activo">
                                        👁️
                                    </a>

                                    {{-- ✏️ Editar (solo admin) --}}
                                    @if (auth()->user()->esAdmin())
                                        <a href="{{ route('activos.edit', $activo->id_activo) }}"
                                           class="btn-icon btn-icon-editar"
                                           title="Editar activo">
                                            ✏️
                                        </a>
                                    @endif
                                        <a href="{{ route('activos.descargar_qr', $activo->id_activo) }}" title="Descargar QR"
                                           style="width:30px;height:30px;background:#f3e8ff;color:#7c3aed;border:1px solid #ddd6fe;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;text-decoration:none;font-size:13px;">⬇️</a>

                                    {{-- 🏷️ Etiqueta con QR --}}
                                    <a href="{{ route('activos.etiqueta', $activo->id_activo) }}" target="_blank"
                                       class="btn-icon"
                                       style="background:#fef3c7;color:#f59e0b;border-color:#fde68a;"
                                       title="Ver etiqueta imprimible"
                                       onmouseover="this.style.background='#f59e0b'; this.style.color='white';"
                                       onmouseout="this.style.background='#fef3c7'; this.style.color='#f59e0b';">
                                        🏷️
                                    </a>

                                    {{-- 📋 Acta de Alta --}}
                                    <a href="{{ route('actas.alta.pdf', $activo->id_activo) }}" target="_blank"
                                       class="btn-icon"
                                       style="background:#d1fae5;color:#059669;border-color:#a7f3d0;"
                                       title="Ver Acta de Alta en PDF"
                                       onmouseover="this.style.background='#059669'; this.style.color='white';"
                                       onmouseout="this.style.background='#d1fae5'; this.style.color='#059669';">
                                        📋
                                    </a>

                                    {{-- 🗑️ Eliminar --}}
                                    @if (auth()->user()->esAdmin())
                                        <form method="POST"
                                              action="{{ route('activos.destroy', $activo->id_activo) }}"
                                              class="form-eliminar-activo"
                                              data-nombre="{{ $activo->nombre }}"
                                              data-codigo="{{ $activo->codigo_activo }}"
                                              style="display:inline;margin:0;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-icon btn-icon-eliminar" title="Eliminar activo">
                                                🗑️
                                            </button>
                                        </form>
                                    @endif

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" style="text-align:center; padding: 30px; color:var(--muted); font-weight: 600;">
                                Aún no hay activos registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    {{-- Modal Escáner QR --}}
    <div class="modal" id="scanner-modal">
        <div class="modal-box" style="max-width: 550px;">
            <div class="modal-head">
                <div>
                    <h2>Escanear código QR</h2>
                    <p>Autorice el uso de la cámara y enfoque el código.</p>
                </div>
                <button class="close" type="button" onclick="closeScannerModal()">×</button>
            </div>
            <div id="qr-reader"></div>
            <p id="scanner-status">Iniciando escáner...</p>
            <div class="actions" style="justify-content: flex-end; margin-top: 15px;">
                <button class="btn light" type="button" onclick="closeScannerModal()">Cerrar</button>
            </div>
        </div>
    </div>

    {{-- Modal Detalle de Activo --}}
    <div class="modal" id="activo-detalle-modal">
        <div class="modal-box">
            <div class="modal-head">
                <div>
                    <h2 id="detalle-titulo">Detalle del activo</h2>
                    <p id="detalle-sub"></p>
                </div>
                <button class="close" type="button" onclick="closeModal('activo-detalle-modal')">×</button>
            </div>
            <div id="detalle-contenido" style="display:grid;grid-template-columns:120px 1fr;gap:12px;align-items:start;">
                <div id="detalle-imagen">
                    <img id="detalle-imagen-img" src="" alt="Imagen activo"
                         style="width:120px;height:160px;object-fit:cover;border-radius:6px;">
                </div>
                <div id="detalle-datos"></div>
            </div>
            <div style="margin-top:12px;display:flex;gap:8px;justify-content:flex-end;">
                <a id="detalle-editar" class="btn" href="#"
                   style="text-decoration:none; display:inline-block; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 700; background: #2563eb; color: #fff;">
                    Editar
                </a>
                <a id="detalle-ver" class="btn light" href="#"
                   style="padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 700; text-decoration: none;">
                    Ver en lista
                </a>
                <button class="btn" type="button" onclick="closeModal('activo-detalle-modal')"
                        style="background: #64748b; color: #fff; border: none; padding: 8px 16px; border-radius: 8px; cursor: pointer; font-size: 13px; font-weight: 700;">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        let html5QrCode = null;

        const audioEncontrado = new Audio("{{ asset('audios/activo_correcto.mp3') }}");
        const audioError      = new Audio("{{ asset('audios/activo_no_existente.mp3') }}");
        const audioUbicacion  = new Audio("{{ asset('audios/activo_encontrado_otro_ambiente.mp3') }}");

        function precargarAudios() {
            [audioEncontrado, audioError, audioUbicacion].forEach(audio => {
                audio.play().then(() => {
                    audio.pause();
                    audio.currentTime = 0;
                }).catch(() => {});
            });
        }

        function reproducirSonido(audio) {
            try {
                audio.currentTime = 0;
                audio.play().catch(e => console.log("Audio play bloqueado:", e));
            } catch (err) {
                console.error(err);
            }
        }

        function modal(id, show) {
            const el = document.getElementById(id);
            if (!el) return;
            if (show) el.classList.add('open');
            else el.classList.remove('open');
        }

        function closeModal(id) {
            modal(id, false);
        }

        function openScannerModal() {
            precargarAudios();
            modal('scanner-modal', true);
            const statusEl = document.getElementById('scanner-status');
            if (statusEl) statusEl.textContent = 'Iniciando cámara...';

            if (!html5QrCode) {
                html5QrCode = new Html5Qrcode("qr-reader");
            }

            html5QrCode.start(
                { facingMode: "environment" },
                { fps: 10, qrbox: { width: 250, height: 250 } },
                (decodedText) => {
                    closeScannerModal();
                    validarYMostrarActivo(decodedText);
                },
                (errorMessage) => {}
            ).then(() => {
                if (statusEl) statusEl.textContent = 'Apunta con la cámara al código QR.';
            }).catch(err => {
                console.error("Error al encender cámara: ", err);
                if (statusEl) statusEl.textContent = 'No se pudo acceder a la cámara. Revisa los permisos.';
            });
        }

        function closeScannerModal() {
            if (html5QrCode && html5QrCode.isScanning) {
                html5QrCode.stop().then(() => {
                    modal('scanner-modal', false);
                }).catch(err => {
                    console.error(err);
                    modal('scanner-modal', false);
                });
            } else {
                modal('scanner-modal', false);
            }
        }

        async function validarYMostrarActivo(code) {
            try {
                const res = await fetch('{{ url('/activos/codigo') }}/' + encodeURIComponent(code), {
                    headers: { 'Accept': 'application/json' }
                });

                const json = await res.json();

                if (!res.ok || !json.success) {
                    reproducirSonido(audioError);
                    alert('❌ CÓDIGO INVÁLIDO: El QR no pertenece a ningún activo registrado.');
                    return;
                }

                const activo = json.activo;
                reproducirSonido(audioEncontrado);

                document.getElementById('detalle-titulo').textContent = `${activo.nombre} — ${activo.codigo_activo}`;
                document.getElementById('detalle-sub').textContent = `${activo.marca || ''} ${activo.modelo || ''}`;
                document.getElementById('detalle-imagen-img').src = json.imagen_url || '/images/no-image.png';

                const datos = [
                    '<strong>Código:</strong> ' + activo.codigo_activo,
                    '<strong>Serie:</strong> ' + (activo.numero_serie || '—'),
                    '<strong>Categoría:</strong> ' + (activo.categoria?.nombre || '—'),
                    '<strong>Ambiente:</strong> ' + (activo.ambiente?.nombre || '—'),
                    '<strong>Custodio:</strong> ' + (activo.custodio?.nombre_completo || 'Sin custodio'),
                    '<strong>Estado físico:</strong> ' + (activo.estado_fisico || '—'),
                    '<p style="margin-top:8px;">' + (activo.descripcion || '') + '</p>'
                ];

                document.getElementById('detalle-datos').innerHTML = datos.join('<br>');
                document.getElementById('detalle-ver').href = '{{ url('/activos') }}?buscar=' + encodeURIComponent(activo.codigo_activo);
                document.getElementById('detalle-editar').href = '{{ url('/activos') }}/' + activo.id_activo + '/edit';

                modal('activo-detalle-modal', true);
            } catch (err) {
                console.error(err);
                reproducirSonido(audioError);
                alert('Error al consultar el activo.');
            }
        }

        // ============================================
        // SWEETALERT2 PARA ELIMINACIÓN
        // ============================================
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.form-eliminar-activo').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const nombre = form.dataset.nombre || '';
                    const codigo = form.dataset.codigo || '';

                    Swal.fire({
                        title: '¿Eliminar este activo?',
                        html: `
                            <div style="text-align:left;background:#fef2f2;padding:12px;border-radius:8px;border:1px solid #fecaca;font-size:13px;">
                                <div><strong>Nombre:</strong> ${nombre}</div>
                                <div><strong>Código:</strong> ${codigo}</div>
                            </div>
                            <p style="margin-top:12px;color:#64748b;font-size:12px;">Esta acción no se puede deshacer.</p>
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
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endsection
