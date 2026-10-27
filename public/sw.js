/*
 * Service worker de HealthNexus.
 *
 * Regla de seguridad: aquí SOLO se guardan archivos estáticos (CSS/JS compilados,
 * imágenes, iconos y la página sin conexión). Nunca se guardan páginas con sesión
 * iniciada, respuestas con datos de pacientes ni peticiones que no sean GET:
 * esas siempre van a la red. Si no hay red, se muestra /offline.html.
 *
 * Al cambiar este archivo, sube la versión para que se limpie la caché anterior.
 */
const VERSION = 'hn-static-v1';
const PRECACHE = [
  '/offline.html',
  '/images/icons/icon-192.png',
  '/images/logo-mark.png',
];
// Rutas del mismo dominio que sí se pueden guardar (contenido público y estático).
const ESTATICOS = ['/build/', '/images/', '/fonts/'];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(VERSION).then((cache) => cache.addAll(PRECACHE)).then(() => self.skipWaiting())
  );
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys()
      .then((claves) => Promise.all(claves.filter((c) => c !== VERSION).map((c) => caches.delete(c))))
      .then(() => self.clients.claim())
  );
});

self.addEventListener('fetch', (event) => {
  const req = event.request;
  if (req.method !== 'GET') return;                 // POST, PUT, DELETE: siempre a la red

  const url = new URL(req.url);
  if (url.origin !== self.location.origin) return;  // otros dominios: sin intervenir

  // Navegación (abrir una página): siempre de la red, nunca se guarda.
  if (req.mode === 'navigate') {
    event.respondWith(fetch(req).catch(() => caches.match('/offline.html')));
    return;
  }

  // Archivos estáticos: primero la caché, si no la red (y se guarda la copia).
  if (ESTATICOS.some((p) => url.pathname.startsWith(p))) {
    event.respondWith(
      caches.match(req).then((guardado) => guardado || fetch(req).then((resp) => {
        if (resp.ok && resp.type === 'basic') {
          const copia = resp.clone();
          caches.open(VERSION).then((cache) => cache.put(req, copia));
        }
        return resp;
      }))
    );
  }
  // Todo lo demás (JSON, exportaciones, PDFs, etc.): sin intervenir, va a la red.
});

// Permite limpiar la caché a petición de la página (por ejemplo, al cerrar sesión).
self.addEventListener('message', (event) => {
  if (event.data === 'hn:limpiar-cache') {
    event.waitUntil(caches.keys().then((c) => Promise.all(c.map((k) => caches.delete(k)))));
  }
});
