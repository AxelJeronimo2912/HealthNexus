/*
 * HealthNexus · Consulta de pacientes sin conexión (HAL-199, HAL-200, HAL-201).
 *
 * Qué se guarda: solo los datos clínicos básicos de LOS pacientes del usuario (ver OfflinePacientesController),
 * nunca CURP, teléfono, dirección ni cuentas.
 * Dónde: IndexedDB "hn-offline", CIFRADO con AES-GCM 256. La clave se crea en el navegador como NO exportable:
 *   el código no puede leerla ni copiarla, y los archivos de IndexedDB copiados a otro equipo no se pueden descifrar.
 * Cuánto tiempo: hasta `caduca_en` (12 h por defecto, un turno). Al leerse vencido, se borra.
 * Cuándo se borra todo: al cerrar sesión, al abrir una pantalla sin sesión (login), si el servidor responde
 *   401/403/419, o al caducar.
 * Sincronización: al cargar una pantalla con sesión SOLO si los datos tienen más de 10 minutos (así navegar por
 *   la web no descarga la lista en cada página ni llena la bitácora), al recuperar la conexión y cada 15 minutos.
 */
(function (global) {
  'use strict';

  var DB = 'hn-offline';
  var STORE = 'kv';
  var URL_DATOS = '/offline/pacientes';
  var CADA_MS = 15 * 60 * 1000;
  var VIGENCIA_MS = 10 * 60 * 1000;

  var soportado = !!(global.indexedDB && global.crypto && global.crypto.subtle);

  function abrir() {
    return new Promise(function (ok, mal) {
      var req = indexedDB.open(DB, 1);
      req.onupgradeneeded = function () { req.result.createObjectStore(STORE); };
      req.onsuccess = function () { ok(req.result); };
      req.onerror = function () { mal(req.error); };
    });
  }

  function op(modo, fn) {
    return abrir().then(function (db) {
      return new Promise(function (ok, mal) {
        var tx = db.transaction(STORE, modo);
        var r = fn(tx.objectStore(STORE));
        tx.oncomplete = function () { db.close(); ok(r && r.result); };
        tx.onerror = function () { db.close(); mal(tx.error); };
      });
    });
  }
  var leerKV = function (k) { return op('readonly', function (s) { return s.get(k); }); };
  var guardarKV = function (k, v) { return op('readwrite', function (s) { s.put(v, k); }); };

  function clave() {
    return leerKV('clave').then(function (k) {
      if (k) return k;
      return crypto.subtle.generateKey({ name: 'AES-GCM', length: 256 }, false, ['encrypt', 'decrypt'])
        .then(function (nueva) { return guardarKV('clave', nueva).then(function () { return nueva; }); });
    });
  }

  function cifrar(obj) {
    var iv = crypto.getRandomValues(new Uint8Array(12));
    var bytes = new TextEncoder().encode(JSON.stringify(obj));
    return clave().then(function (k) { return crypto.subtle.encrypt({ name: 'AES-GCM', iv: iv }, k, bytes); })
      .then(function (c) { return { iv: iv, cifrado: c }; });
  }

  function descifrar(reg) {
    return clave().then(function (k) { return crypto.subtle.decrypt({ name: 'AES-GCM', iv: reg.iv }, k, reg.cifrado); })
      .then(function (b) { return JSON.parse(new TextDecoder().decode(b)); });
  }

  /**
   * Borra los pacientes guardados y su clave. Con `{ cache: true }` (al cerrar sesión) también pide al service
   * worker limpiar su caché; él vuelve a precargar la página sin conexión.
   */
  function borrar(opciones) {
    if (opciones && opciones.cache && global.navigator && navigator.serviceWorker && navigator.serviceWorker.controller) {
      navigator.serviceWorker.controller.postMessage('hn:limpiar-cache');
    }
    if (!global.indexedDB) return Promise.resolve();
    return new Promise(function (ok) {
      var req = indexedDB.deleteDatabase(DB);
      req.onsuccess = req.onerror = req.onblocked = function () { ok(); };
    });
  }

  /** Descarga los pacientes del usuario y los guarda cifrados. */
  function sincronizar() {
    if (!soportado || (global.navigator && navigator.onLine === false)) return Promise.resolve(null);
    return fetch(URL_DATOS, { credentials: 'same-origin', cache: 'no-store', headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
      .then(function (r) {
        if (r.status === 401 || r.status === 403 || r.status === 419) { return borrar().then(function () { return null; }); }
        if (!r.ok) return null;
        return r.json().then(function (datos) {
          return cifrar(datos).then(function (reg) {
            reg.caduca_en = datos.caduca_en;
            reg.generado_en = datos.generado_en;
            reg.total = datos.pacientes.length;
            return guardarKV('datos', reg).then(function () { return reg; });
          });
        });
      })
      .catch(function () { return null; });
  }

  /** Devuelve { generado_en, caduca_en, pacientes } o null si no hay datos o ya caducaron (y entonces los borra). */
  function leer() {
    if (!soportado) return Promise.resolve(null);
    return leerKV('datos').then(function (reg) {
      if (!reg) return null;
      if (!reg.caduca_en || new Date(reg.caduca_en).getTime() <= Date.now()) { return borrar().then(function () { return null; }); }
      return descifrar(reg);
    }).catch(function () { return borrar().then(function () { return null; }); });
  }

  /** Descarga de nuevo solo si no hay datos o tienen más de 10 minutos. */
  function sincronizarSiHaceFalta() {
    return leerKV('datos').then(function (reg) {
      var edad = reg && reg.generado_en ? Date.now() - new Date(reg.generado_en).getTime() : Infinity;
      return edad > VIGENCIA_MS ? sincronizar() : reg;
    }).catch(function () { return sincronizar(); });
  }

  /** Para pantallas con sesión de médico o enfermería. */
  function activar() {
    if (!soportado) return;
    sincronizarSiHaceFalta();
    global.addEventListener('online', sincronizar);
    global.setInterval(sincronizar, CADA_MS);
  }

  global.HNOffline = { soportado: soportado, sincronizar: sincronizar, sincronizarSiHaceFalta: sincronizarSiHaceFalta, leer: leer, borrar: borrar, activar: activar };
})(typeof window !== 'undefined' ? window : globalThis);
