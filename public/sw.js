/*
 * Service worker de HealthNexus.
 *
 * Regla de seguridad: aquí SOLO se guardan archivos estáticos (CSS/JS compilados,
 * imágenes, iconos y la página sin conexión). Nunca se guardan páginas con sesión
 * iniciada, respuestas con datos de pacientes ni peticiones que no sean GET:
 * esas siempre van a la red. Si no hay red, se muestra /offline.html.
 *
 * Los datos mínimos de pacientes para consultar sin conexión NO pasan por esta caché:
 * los guarda cifrados public/js/hn-offline.js en IndexedDB, con caducidad (HAL-199).
 *
 * Al cambiar este archivo, sube la versión para que se limpie la caché anterior.
 */
const VERSION = 'hn-static-v6';
const PRECACHE = [
  '/offline.html',
  '/js/hn-offline.js',
  '/images/icons/icon-192.png',
  '/images/logo-mark.png',
];
// Rutas del mismo dominio que sí se pueden guardar (contenido público y estático).
const ESTATICOS = ['/build/', '/images/', '/fonts/', '/js/'];

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
  // Sin red se muestra /offline.html; si por algo no estuviera guardada, se vuelve a pedir
  // y, como último recurso, se responde una página mínima (nunca ERR_FAILED).
  if (req.mode === 'navigate') {
    event.respondWith(fetch(req).catch(paginaSinConexion));
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

function precargar() {
  return caches.open(VERSION).then((cache) => cache.addAll(PRECACHE));
}

function paginaSinConexion() {
  return caches.match('/offline.html')
    .then((guardada) => guardada || precargar().then(() => caches.match('/offline.html')).catch(() => null))
    .then((pagina) => pagina || new Response(
      '<!doctype html><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">'
      + '<title>Sin conexión · HealthNexus</title><body style="font-family:system-ui;background:#0D1230;color:#fff;'
      + 'display:grid;place-items:center;min-height:100vh;margin:0;text-align:center"><main><h1>Sin conexión</h1>'
      + '<p>Revisa tu conexión a internet; la página se recargará sola.</p></main>'
      + '<script>addEventListener("online",function(){location.reload()})</script></body>',
      { status: 503, headers: { 'Content-Type': 'text/html; charset=utf-8' } }
    ));
}

// Al cerrar sesión la página pide limpiar la caché: se borra todo lo guardado durante la sesión y se vuelve a
// precargar la página sin conexión (sin ella, quedarse sin red mostraría el error del navegador).
self.addEventListener('message', (event) => {
  if (event.data === 'hn:limpiar-cache') {
    event.waitUntil(
      caches.keys()
        .then((c) => Promise.all(c.map((k) => caches.delete(k))))
        .then(precargar)
        .catch(() => {})
    );
  }
});
