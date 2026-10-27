{{-- PWA: manifiesto, iconos y registro del service worker (ver public/sw.js) --}}
<link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
<meta name="theme-color" content="#141739">
<meta name="application-name" content="HealthNexus">
<meta name="apple-mobile-web-app-title" content="HealthNexus">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<link rel="apple-touch-icon" href="{{ asset('images/icons/apple-touch-icon.png') }}">
<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('/sw.js').catch(function () {});
        });
        // Al cerrar sesión, borra también la caché del service worker.
        document.addEventListener('submit', function (e) {
            var accion = e.target && e.target.getAttribute('action');
            if (accion && /\/logout$/.test(accion) && navigator.serviceWorker.controller) {
                navigator.serviceWorker.controller.postMessage('hn:limpiar-cache');
            }
        }, true);
    }
</script>
