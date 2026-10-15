/*
 * Service worker del inventario.
 *
 * El inventario entra a locales sin señal, así que el catálogo de activos
 * y el módulo de verificación tienen que quedar disponibles aunque no haya
 * red. Lo que el usuario registra no se pierde: se guarda en una cola
 * local (IndexedDB) y el cliente la envía apenas vuelve la señal.
 */

const CACHE = 'sisactivos-v1';
const CATALOGO = '/sincronizacion/catalogo';
const APP_SHELL = [
    '/offline',
    '/manifest.json',
];

/* ------------------------------------------------------------------ */
/* INSTALACIÓN                                                        */
/* ------------------------------------------------------------------ */

self.addEventListener('install', (evento) => {
    evento.waitUntil(
        caches.open(CACHE).then((cache) => cache.addAll(APP_SHELL)).then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (evento) => {
    evento.waitUntil(
        caches.keys()
            .then((claves) => Promise.all(
                claves.filter((clave) => clave !== CACHE).map((clave) => caches.delete(clave))
            ))
            .then(() => self.clients.claim())
    );
});

/* ------------------------------------------------------------------ */
/* ESTRATEGIA DE RED                                                  */
/* ------------------------------------------------------------------ */

self.addEventListener('fetch', (evento) => {
    const url = new URL(evento.request.url);

    if (evento.request.method !== 'GET' || url.origin !== self.location.origin) {
        return;
    }

    // El catálogo se guarda siempre: es lo que permite escanear sin señal.
    if (url.pathname === CATALOGO) {
        evento.respondWith(cacheFirstThenNetwork(evento.request));
        return;
    }

    // Navegación: red primero, y si no hay, la copia guardada u offline.
    if (evento.request.mode === 'navigate') {
        evento.respondWith(
            fetch(evento.request)
                .then((respuesta) => {
                    const copia = respuesta.clone();
                    caches.open(CACHE).then((cache) => cache.put(evento.request, copia));
                    return respuesta;
                })
                .catch(async () => {
                    const guardada = await caches.match(evento.request);
                    if (guardada) {
                        return guardada;
                    }
                    const Offline = await caches.match('/offline');
                    return Offline || new Response('Sin conexión', {
                        status: 503,
                        headers: { 'Content-Type': 'text/plain; charset=utf-8' },
                    });
                })
        );
        return;
    }

    // Activos, JS y CSS: caché primero para que la pantalla abra rápido.
    evento.respondWith(cacheFirstThenNetwork(evento.request));
});

async function cacheFirstThenNetwork(peticion) {
    const guardada = await caches.match(peticion);

    if (guardada) {
        // Se refresca en segundo plano, sin bloquear la respuesta.
        fetch(peticion)
            .then((respuesta) => {
                if (respuesta && respuesta.ok) {
                    const copia = respuesta.clone();
                    caches.open(CACHE).then((cache) => cache.put(peticion, copia));
                }
            })
            .catch(() => {});

        return guardada;
    }

    try {
        const respuesta = await fetch(peticion);

        if (respuesta && respuesta.ok) {
            const copia = respuesta.clone();
            caches.open(CACHE).then((cache) => cache.put(peticion, copia));
        }

        return respuesta;
    } catch (error) {
        return new Response('', { status: 503, statusText: 'Sin conexión' });
    }
}

/* ------------------------------------------------------------------ */
/* SINCRONIZACIÓN                                                     */
/* ------------------------------------------------------------------ */

self.addEventListener('sync', (evento) => {
    if (evento.tag === 'sisactivos-verificaciones') {
        evento.waitUntil(
            self.clients.matchAll({ includeUncontrolled: true }).then((clientes) => {
                clientes.forEach((cliente) => cliente.postMessage({ tipo: 'SINCRONIZAR' }));
            })
        );
    }
});

self.addEventListener('message', (evento) => {
    if (evento.data && evento.data.tipo === 'SYNC_AHORA') {
        self.registration.sync
            ? self.registration.sync.register('sisactivos-verificaciones')
            : self.clients.matchAll().then((clientes) => {
                  clientes.forEach((cliente) => cliente.postMessage({ tipo: 'SINCRONIZAR' }));
              });
    }
});