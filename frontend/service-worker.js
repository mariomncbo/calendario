/* ============================================================
   CALENDARIO · Service Worker
   Necesario para que el navegador permita instalar la app como PWA.
   ============================================================ */

// Nombre del caché de la app (se usa para versionar y limpiar cachés antiguas)
const CACHE_NOMBRE = 'calendario-v1';

// Al instalar, activamos el service worker sin esperar a cerrar todos los clientes
self.addEventListener('install', (event) => {
  self.skipWaiting();
});

// Al activar, borramos cachés de versiones anteriores y reclamamos los clientes
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches
      .keys()
      .then((nombres) =>
        Promise.all(
          nombres
            .filter((nombre) => nombre !== CACHE_NOMBRE)
            .map((nombre) => caches.delete(nombre))
        )
      )
      .then(() => self.clients.claim())
  );
});

// Interceptamos solo peticiones GET del mismo origen.
// No cacheamos datos dinámicos ni la API para no romper la sesión ni el login.
self.addEventListener('fetch', (event) => {
  if (event.request.method !== 'GET' || !event.request.url.startsWith(self.location.origin)) {
    return;
  }

  event.respondWith(fetch(event.request));
});