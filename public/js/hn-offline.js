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
 *
 * Modo sin conexión, fase 1 · signos vitales:
 *   Si al guardar signos no hay conexión (o el servidor está apagado), el registro se guarda en una COLA cifrada
 *   aparte (IndexedDB "hn-cola", con su propia clave no exportable) junto con un client_uuid, la hora real de la
 *   toma y la cuenta que lo capturó. La cola NO se borra al cerrar sesión: se envía cuando esa misma cuenta vuelve
 *   a tener sesión y conexión (POST /offline/signos, que no duplica un registro que llegue dos veces).
 */
(function (global) {
  'use strict';

  var DB = 'hn-offline';
  var STORE = 'kv';
  var URL_DATOS = '/offline/pacientes';
  var CADA_MS = 15 * 60 * 1000;
  var VIGENCIA_MS = 10 * 60 * 1000;
  var COLA_DB = 'hn-cola';
  var URL_SIGNOS = '/offline/signos';
  var URL_SALUD = '/up';
  var REINTENTO_MS = 60 * 1000;

  var soportado = !!(global.indexedDB && global.crypto && global.crypto.subtle);

  function abrir(nombre) {
    return new Promise(function (ok, mal) {
      var req = indexedDB.open(nombre || DB, 1);
      req.onupgradeneeded = function () { req.result.createObjectStore(STORE); };
      req.onsuccess = function () { ok(req.result); };
      req.onerror = function () { mal(req.error); };
    });
  }

  function op(modo, fn, nombre) {
    return abrir(nombre).then(function (db) {
      return new Promise(function (ok, mal) {
        var tx = db.transaction(STORE, modo);
        var r = fn(tx.objectStore(STORE));
        tx.oncomplete = function () { db.close(); ok(r && r.result); };
        tx.onerror = function () { db.close(); mal(tx.error); };
      });
    });
  }
  var leerKV = function (k, nombre) { return op('readonly', function (s) { return s.get(k); }, nombre); };
  var guardarKV = function (k, v, nombre) { return op('readwrite', function (s) { s.put(v, k); }, nombre); };

  // La clave se crea una sola vez: con add() (no put()), si dos llamadas la generan a la vez, la segunda falla y usa
  // la que quedó guardada. Así nunca se cifra con una clave que luego se reemplaza.
  function clave(nombre) {
    return leerKV('clave', nombre).then(function (k) {
      if (k) return k;
      return crypto.subtle.generateKey({ name: 'AES-GCM', length: 256 }, false, ['encrypt', 'decrypt'])
        .then(function (nueva) { return op('readwrite', function (s) { s.add(nueva, 'clave'); }, nombre); })
        .then(function () { return leerKV('clave', nombre); }, function () { return leerKV('clave', nombre); });
    });
  }

  function cifrar(obj, nombre) {
    var iv = crypto.getRandomValues(new Uint8Array(12));
    var bytes = new TextEncoder().encode(JSON.stringify(obj));
    return clave(nombre).then(function (k) { return crypto.subtle.encrypt({ name: 'AES-GCM', iv: iv }, k, bytes); })
      .then(function (c) { return { iv: iv, cifrado: c }; });
  }

  function descifrar(reg, nombre) {
    return clave(nombre).then(function (k) { return crypto.subtle.decrypt({ name: 'AES-GCM', iv: reg.iv }, k, reg.cifrado); })
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

  /* ====================== Cola de signos vitales sin conexión ====================== */

  var meta = function (n) { var m = global.document && document.querySelector('meta[name="' + n + '"]'); return m ? m.getAttribute('content') : null; };
  /** Cuenta con sesión en esta página (meta hn-usuario) o null. */
  var usuarioActual = function () { var u = meta('hn-usuario'); return u ? String(u) : null; };

  function nuevoUuid() {
    if (global.crypto && crypto.randomUUID) return crypto.randomUUID();
    var b = crypto.getRandomValues(new Uint8Array(16));
    b[6] = (b[6] & 0x0f) | 0x40; b[8] = (b[8] & 0x3f) | 0x80;
    var h = Array.prototype.map.call(b, function (x) { return (x + 256).toString(16).slice(1); }).join('');
    return h.slice(0, 8) + '-' + h.slice(8, 12) + '-' + h.slice(12, 16) + '-' + h.slice(16, 20) + '-' + h.slice(20);
  }

  // Cada registro se guarda por separado con la llave "item:<client_uuid>": agregar, marcar o quitar uno nunca
  // reescribe a los demás (dos pestañas a la vez no se pisan). Si un registro no se puede leer, es un error, no "cola vacía".
  var PREFIJO = 'item:';
  var rango = function () { return IDBKeyRange.bound(PREFIJO, PREFIJO + '\uffff'); };

  /** Todos los registros de la cola (de cualquier cuenta), ya descifrados y en orden de captura. */
  function leerCola() {
    if (!soportado) return Promise.resolve([]);
    return op('readonly', function (s) { return s.getAll(rango()); }, COLA_DB).then(function (regs) {
      return Promise.all((regs || []).map(function (reg) { return descifrar(reg, COLA_DB); }));
    }).then(function (items) {
      return items.sort(function (a, b) { return a.capturado_en < b.capturado_en ? -1 : a.capturado_en > b.capturado_en ? 1 : 0; });
    });
  }
  function guardarItem(item) {
    return cifrar(item, COLA_DB).then(function (reg) { return guardarKV(PREFIJO + item.client_uuid, reg, COLA_DB); }).then(avisar);
  }
  function quitarItem(uuid) {
    return op('readwrite', function (s) { s.delete(PREFIJO + uuid); }, COLA_DB).then(avisar);
  }

  var oyentes = [];
  function avisar() { oyentes.forEach(function (fn) { try { fn(); } catch (e) { /* sigue con los demás */ } }); }
  function alCambiar(fn) { oyentes.push(fn); }

  /**
   * Guarda signos vitales para enviarlos al reconectar.
   * datos: campos del formulario (paciente_id, temperatura, …) · info: { usuario, paciente }
   */
  function encolarSignos(datos, info) {
    if (!soportado) return Promise.reject(new Error('Este navegador no puede guardar datos sin conexión.'));
    var usuario = (info && info.usuario) || usuarioActual();
    if (!usuario) return Promise.reject(new Error('No hay una cuenta para guardar el registro.'));
    var item = {
      client_uuid: nuevoUuid(), capturado_en: new Date().toISOString(), usuario: String(usuario),
      paciente: (info && info.paciente) || '', datos: datos, estado: 'pendiente', error: null,
    };
    return guardarItem(item).then(function () { return item; });
  }

  /** Registros de una cuenta (por omisión, la de esta página). */
  function cola(usuario) {
    var u = String(usuario || usuarioActual() || '');
    return leerCola().then(function (items) { return items.filter(function (i) { return i.usuario === u; }); });
  }

  function descartarSigno(uuid) { return quitarItem(uuid); }

  function primerError(json) {
    if (json && json.errors) { for (var k in json.errors) { if (json.errors[k] && json.errors[k][0]) return json.errors[k][0]; } }
    return (json && json.message) || 'El servidor no aceptó el registro.';
  }

  var enviando = null;
  /**
   * Envía los pendientes de la cuenta con sesión, en orden.
   *   201 / 200 (ya recibido): sale de la cola · sin red, 401, 419 (sesión), 429 o 5xx: se detiene y reintenta después
   *   422 / 403 / 404 / 409: queda marcado con el motivo; no se reintenta solo (la persona lo revisa o lo descarta)
   */
  function enviarCola() {
    var usuario = usuarioActual();
    var token = meta('csrf-token');
    if (!soportado || !usuario || !token || (global.navigator && navigator.onLine === false)) return Promise.resolve({ enviados: 0 });
    if (enviando) return enviando;
    var r = { enviados: 0, rechazados: 0 };
    enviando = cola(usuario).then(function (mios) {
      var pendientes = mios.filter(function (i) { return i.estado === 'pendiente'; });
      var paso = function (n) {
        if (n >= pendientes.length) return r;
        var item = pendientes[n];
        var cuerpo = Object.assign({}, item.datos, { client_uuid: item.client_uuid, capturado_en: item.capturado_en });
        return fetch(URL_SIGNOS, {
          method: 'POST', credentials: 'same-origin', cache: 'no-store',
          headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': token },
          body: JSON.stringify(cuerpo),
        }).then(function (resp) {
          if (resp.status === 200 || resp.status === 201) {
            r.enviados++;
            return quitarItem(item.client_uuid).then(function () { return paso(n + 1); });
          }
          if ([403, 404, 409, 422].indexOf(resp.status) !== -1) {
            r.rechazados++;
            return resp.json().catch(function () { return null; }).then(function (json) {
              var motivo = resp.status === 403 ? 'Tu cuenta ya no puede registrar signos vitales.' : primerError(json);
              return guardarItem(Object.assign({}, item, { estado: 'error', error: motivo }));
            }).then(function () { return paso(n + 1); });
          }
          return r; // sesión vencida, demasiadas peticiones o servidor con error: se reintenta después
        }, function () { return r; }); // sin conexión
      };
      return paso(0);
    }).then(function (res) { enviando = null; return res; }, function () { enviando = null; return r; });
    return enviando;
  }

  /** ¿Hay conexión con el servidor? (no basta navigator.onLine: el servidor puede estar apagado) */
  function servidorDisponible() {
    if (global.navigator && navigator.onLine === false) return Promise.resolve(false);
    var ctrl = global.AbortController ? new AbortController() : null;
    var t = ctrl ? setTimeout(function () { ctrl.abort(); }, 4000) : null;
    return fetch(URL_SALUD, { method: 'GET', cache: 'no-store', credentials: 'same-origin', signal: ctrl ? ctrl.signal : undefined })
      .then(function (r) { return r.ok; }, function () { return false; })
      .then(function (ok) { if (t) clearTimeout(t); return ok; });
  }

  /* ---------- Formulario de la web: <form data-hn-offline="signos"> ---------- */
  function datosDelFormulario(form) {
    var datos = {};
    new FormData(form).forEach(function (v, k) {
      if (k === '_token' || k === 'cita_id' || typeof v !== 'string' || v.trim() === '') return;
      datos[k] = k === 'paciente_id' ? Number(v) : v.trim();
    });
    return datos;
  }

  function mensajeEn(form, texto, tipo) {
    var caja = form.querySelector('[data-hn-mensaje]');
    if (!caja) {
      caja = document.createElement('div');
      caja.setAttribute('data-hn-mensaje', '');
      caja.setAttribute('role', 'status');
      form.insertBefore(caja, form.firstChild);
    }
    caja.textContent = texto;
    caja.style.cssText = 'margin:16px 24px 0;padding:12px 14px;border-radius:12px;font-size:14px;line-height:1.5;' +
      (tipo === 'error' ? 'background:#FDECEC;color:#A61B1B;border:1px solid #F3C4C4' : 'background:#FEF3E2;color:#7A3E06;border:1px solid #F5D9A8');
  }

  function interceptarFormulario(form) {
    form.addEventListener('submit', function (e) {
      if (form.dataset.hnEnLinea === '1') return; // ya se comprobó la conexión: envío normal
      e.preventDefault();
      servidorDisponible().then(function (hay) {
        if (hay) { form.dataset.hnEnLinea = '1'; form.submit(); return; }
        var datos = datosDelFormulario(form);
        var sel = form.querySelector('[name="paciente_id"]');
        var paciente = sel && sel.options && sel.selectedIndex >= 0 ? sel.options[sel.selectedIndex].text.trim() : '';
        return encolarSignos(datos, { paciente: paciente }).then(function () {
          form.reset();
          mensajeEn(form, 'Sin conexión: los signos de ' + (paciente || 'este paciente') + ' se guardaron cifrados en este dispositivo y se enviarán solos al reconectar.');
          global.scrollTo && global.scrollTo(0, 0);
        }, function (err) { mensajeEn(form, 'No se pudo guardar sin conexión: ' + err.message, 'error'); });
      });
    });
  }

  /* ---------- Aviso de pendientes en las pantallas con sesión ---------- */
  var hora = function (iso) { try { return new Date(iso).toLocaleString('es-MX', { dateStyle: 'short', timeStyle: 'short' }); } catch (e) { return ''; } };
  function el(tag, estilo, texto) { var n = document.createElement(tag); if (estilo) n.style.cssText = estilo; if (texto != null) n.textContent = texto; return n; }

  function pintarAviso() {
    if (!global.document || !document.body) return Promise.resolve();
    return cola().catch(function () { return []; }).then(function (mios) {
      var viejo = document.getElementById('hn-cola-aviso');
      if (viejo) viejo.remove();
      if (!mios.length) return;
      var pend = mios.filter(function (i) { return i.estado === 'pendiente'; });
      var caja = el('div', 'position:fixed;right:16px;bottom:16px;z-index:80;max-width:360px;background:#FFFFFF;border:1px solid #F5D9A8;' +
        'border-left:6px solid #B45309;border-radius:14px;box-shadow:0 10px 30px rgba(17,26,58,.18);padding:14px 16px;font-size:14px;color:#111A3A');
      caja.id = 'hn-cola-aviso';
      caja.setAttribute('role', 'status');
      if (pend.length) {
        caja.appendChild(el('div', 'font-weight:700;color:#B45309', pend.length === 1 ? '1 registro de signos vitales sin enviar' : pend.length + ' registros de signos vitales sin enviar'));
        caja.appendChild(el('div', 'margin-top:4px;color:#4B5672', 'Se capturaron sin conexión y se enviarán solos.'));
        var b = el('button', 'margin-top:10px;height:36px;padding:0 14px;border:none;border-radius:10px;background:#4E2CCF;color:#fff;font-weight:600;cursor:pointer', 'Enviar ahora');
        b.type = 'button';
        b.onclick = function () { b.disabled = true; b.textContent = 'Enviando…'; enviarCola().then(pintarAviso); };
        caja.appendChild(b);
      }
      mios.filter(function (i) { return i.estado === 'error'; }).forEach(function (i) {
        var f = el('div', 'margin-top:10px;padding-top:10px;border-top:1px solid #F3C4C4;color:#A61B1B');
        f.appendChild(el('div', 'font-weight:700', 'No se pudo enviar: ' + (i.paciente || 'registro sin conexión') + ' (' + hora(i.capturado_en) + ')'));
        f.appendChild(el('div', null, i.error));
        var d = el('button', 'margin-top:6px;height:32px;padding:0 12px;border:1px solid #DC2626;border-radius:10px;background:#fff;color:#DC2626;font-weight:600;cursor:pointer', 'Descartar');
        d.type = 'button';
        d.onclick = function () { if (global.confirm('¿Descartar este registro? Se borrará de este dispositivo.')) descartarSigno(i.client_uuid).then(pintarAviso); };
        f.appendChild(d);
        caja.appendChild(f);
      });
      document.body.appendChild(caja);
    });
  }

  /** Para pantallas con sesión de médico o enfermería. */
  function activar() {
    if (!soportado) return;
    sincronizarSiHaceFalta();
    var enviarYPintar = function () { return enviarCola().then(pintarAviso); };
    global.addEventListener('online', sincronizar);
    global.addEventListener('online', enviarYPintar);
    global.setInterval(sincronizar, CADA_MS);
    global.setInterval(function () {
      cola().then(function (mios) { if (mios.some(function (i) { return i.estado === 'pendiente'; })) enviarYPintar(); }, function () {});
    }, REINTENTO_MS);
    var alCargar = function () {
      Array.prototype.forEach.call(document.querySelectorAll('form[data-hn-offline="signos"]'), interceptarFormulario);
      enviarYPintar();
    };
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', alCargar); else alCargar();
  }

  global.HNOffline = {
    soportado: soportado, sincronizar: sincronizar, sincronizarSiHaceFalta: sincronizarSiHaceFalta, leer: leer, borrar: borrar, activar: activar,
    encolarSignos: encolarSignos, cola: cola, enviarCola: enviarCola, descartarSigno: descartarSigno, alCambiar: alCambiar, servidorDisponible: servidorDisponible,
  };
})(typeof window !== 'undefined' ? window : globalThis);
