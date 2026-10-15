/**
 * Cola local del inventario.
 *
 * Cuando el inventariador escanea un activo sin señal, el registro no se
 * manda: se guarda aquí y se envía solo. Al volver la señal se sincroniza
 * y cada elemento se borra únicamente cuando el servidor confirma que lo
 * registró.
 */
(function () {
    'use strict';

    const DB_NOMBRE = 'sisactivos';
    const DB_VERSION = 1;
    const ALMACEN = 'cola';
    const CLAVES = {
        cola: 'pendientes',
        catalogo: 'catalogo',
    };

    const CATALOGO_URL = '/sincronizacion/catalogo';
    const SINCRONIZAR_URL = '/sincronizacion/verificaciones';

    /* ---------------------------------------------------------------- */
    /* BASE DE DATOS LOCAL                                              */
    /* ---------------------------------------------------------------- */

    function abrirBase() {
        return new Promise(function (resolve, reject) {
            const solicitud = indexedDB.open(DB_NOMBRE, DB_VERSION);

            solicitud.onupgradeneeded = function () {
                const base = solicitud.result;

                if (!base.objectStoreNames.contains(ALMACEN)) {
                    base.createObjectStore(ALMACEN, { keyPath: 'id_local' });
                }
            };

            solicitud.onsuccess = function () {
                resolve(solicitud.result);
            };

            solicitud.onerror = function () {
                reject(solicitud.error);
            };
        });
    }

    function transaccion(modo, accion) {
        return abrirBase().then(function (base) {
            return new Promise(function (resolve, reject) {
                const tx = base.transaction(ALMACEN, modo);
                const almacen = tx.objectStore(ALMACEN);
                const resultado = accion(almacen);

                tx.oncomplete = function () {
                    resolve(resultado && resultado.result !== undefined ? resultado.result : resultado);
                };
                tx.onerror = function () {
                    reject(tx.error);
                };
            });
        });
    }

    /* ---------------------------------------------------------------- */
    /* COLA                                                             */
    /* ---------------------------------------------------------------- */

    function guardarEnCola(registro) {
        return transaccion('readwrite', function (almacen) {
            return almacen.put(registro);
        });
    }

    function leerCola() {
        return transaccion('readonly', function (almacen) {
            return almacen.getAll(CLAVES.cola);
        }).then(function (filas) {
            return (filas || []).filter(function (fila) {
                return fila.clave === CLAVES.cola;
            });
        });
    }

    function quitarDeCola(idsLocales) {
        if (!idsLocales || !idsLocales.length) {
            return Promise.resolve();
        }

        return transaccion('readwrite', function (almacen) {
            idsLocales.forEach(function (id) {
                almacen.delete(id);
            });
        });
    }

    function contarPendientes() {
        return leerCola().then(function (filas) {
            return filas.length;
        });
    }

    /* ---------------------------------------------------------------- */
    /* CATÁLOGO                                                         */
    /* ---------------------------------------------------------------- */

    function guardarCatalogo(ambienteId) {
        const url = ambienteId
            ? CATALOGO_URL + '?ambiente_id=' + encodeURIComponent(ambienteId)
            : CATALOGO_URL;

        return fetch(url, { credentials: 'same-origin' })
            .then(function (respuesta) {
                if (!respuesta.ok) {
                    throw new Error('No se pudo descargar el catálogo');
                }
                return respuesta.json();
            })
            .then(function (datos) {
                return transaccion('readwrite', function (almacen) {
                    almacen.put({
                        clave: CLAVES.catalogo,
                        id_local: CLAVES.catalogo,
                        ambiente_id: ambienteId || null,
                        descargado_en: datos.descargado_en,
                        activos: datos.activos,
                    });
                }).then(function () {
                    return datos.activos;
                });
            });
    }

    function leerCatalogo() {
        return transaccion('readonly', function (almacen) {
            return almacen.get(CLAVES.catalogo);
        });
    }

    function buscarEnCatalogo(codigo) {
        return leerCatalogo().then(function (registro) {
            if (!registro || !registro.activos) {
                return null;
            }

            const limpio = String(codigo || '').trim();
            const activos = registro.activos;

            return activos.find(function (activo) {
                return activo.codigo_activo === limpio;
            }) || activos.find(function (activo) {
                return String(activo.codigo_activo || '').indexOf(limpio) !== -1;
            }) || null;
        });
    }

    /* ---------------------------------------------------------------- */
    /* SINCRONIZACIÓN                                                   */
    /* ---------------------------------------------------------------- */

    let sincronizando = false;

    function sincronizar() {
        if (sincronizando || !navigator.onLine) {
            return Promise.resolve({ sincronizado: 0, pendientes: 0 });
        }

        return leerCola().then(function (filas) {
            if (!filas.length) {
                return { sincronizado: 0, pendientes: 0 };
            }

            sincronizando = true;

            const lote = filas.map(function (fila) {
                return {
                    id_local: fila.id_local,
                    codigo_activo: fila.codigo_activo,
                    ambiente_escaneo_id: fila.ambiente_escaneo_id,
                    fecha_verificacion: fila.fecha_verificacion,
                    estado_fisico: fila.estado_fisico || null,
                    observaciones: fila.observaciones || null,
                    solicitar_baja: !!fila.solicitar_baja,
                    es_sustitucion: !!fila.es_sustitucion,
                    sustituto: fila.sustituto || null,
                };
            });

            return fetch(SINCRONIZAR_URL, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    Accept: 'application/json',
                },
                body: JSON.stringify({ verificaciones: lote }),
            })
                .then(function (respuesta) {
                    return respuesta.json().then(function (datos) {
                        return { respuesta: respuesta, datos: datos };
                    });
                })
                .then(function (resultado) {
                    const datos = resultado.datos || {};

                    if (!resultado.respuesta.ok || !datos.success) {
                        throw new Error(datos.error || 'Error al sincronizar');
                    }

                    const aceptados = (datos.registradas || []).map(function (item) {
                        return item.id_local;
                    });

                    // Solo se borra lo que el servidor confirmó.
                    return quitarDeCola(aceptados).then(function () {
                        return { sincronizado: aceptados.length, rechazadas: datos.rechazadas || [] };
                    });
                })
                .finally(function () {
                    sincronizando = false;
                });
        }).catch(function (error) {
            sincronizando = false;
            console.warn('[offline] No se pudo sincronizar:', error.message);
            return { sincronizado: 0, error: error.message };
        });
    }

    /* ---------------------------------------------------------------- */
    /* API PÚBLICA                                                      */
    /* ---------------------------------------------------------------- */

    const InventarioOffline = {
        guardarVerificacion: guardarEnCola,
        cola: leerCola,
        pendientes: contarPendientes,
        catalogo: guardarCatalogo,
        leerCatalogo: leerCatalogo,
        buscar: buscarEnCatalogo,
        sincronizar: sincronizar,

        estaConectado: function () {
            return navigator.onLine;
        },

        /** Registra el escaneo: va al servidor si hay señal, si no, a la cola. */
        registrar: function (datos) {
            const registro = Object.assign(
                {
                    clave: CLAVES.cola,
                    id_local: 'v-' + Date.now() + '-' + Math.random().toString(36).slice(2, 9),
                    fecha_verificacion: new Date().toISOString(),
                },
                datos
            );

            return guardarEnCola(registro)
                .then(function () {
                    if (navigator.onLine) {
                        return sincronizar().then(function () {
                            return { encolado: false, id_local: registro.id_local };
                        });
                    }

                    return { encolado: true, id_local: registro.id_local };
                })
                .then(function (resultado) {
                    actualizarContador();
                    return resultado;
                });
        },
    };

    /* ---------------------------------------------------------------- */
    /* INTERFAZ                                                         */
    /* ---------------------------------------------------------------- */

    function actualizarContador() {
        const insignia = document.getElementById('offline-pendientes');
        if (!insignia) {
            return;
        }

        contarPendientes().then(function (total) {
            insignia.textContent = total;
            insignia.style.display = total > 0 ? 'inline-flex' : 'none';
        });
    }

    window.InventarioOffline = InventarioOffline;

    document.addEventListener('DOMContentLoaded', function () {
        // Botón de sincronización manual
        const boton = document.getElementById('btn-sincronizar');

        if (boton) {
            boton.addEventListener('click', function () {
                boton.disabled = true;
                boton.textContent = '⏳ Enviando…';

                sincronizar().then(function () {
                    boton.disabled = false;
                    boton.textContent = '🔄 Sincronizar ahora';
                    actualizarContador();

                    if (boton.dataset.reload === '1') {
                        window.location.reload();
                    }
                });
            });
        }

        // El service worker avisa cuando hay algo que enviar
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.addEventListener('message', function (evento) {
                if (evento.data && evento.data.tipo === 'SINCRONIZAR') {
                    sincronizar().then(actualizarContador);
                }
            });

            navigator.serviceWorker.register('/sw.js').then(function () {
                return sincronizar();
            }).then(actualizarContador).catch(function () {
                /* sin service worker la cola sigue funcionando */
            });
        }

        // Al volver la señal se envía solo
        window.addEventListener('online', function () {
            sincronizar().then(actualizarContador);
        });

        actualizarContador();
    });

    window.addEventListener('load', actualizarContador);
})();