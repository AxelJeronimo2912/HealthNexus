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
    }
</script>
@auth
    {{-- Cuenta con sesión: la cola de signos capturados sin conexión solo se envía con la misma cuenta --}}
    <meta name="hn-usuario" content="{{ auth()->id() }}">
@endauth
{{-- HAL-199/200/201: pacientes para consultar sin conexión, cifrados en el dispositivo (ver public/js/hn-offline.js) --}}
<script src="{{ asset('js/hn-offline.js') }}"></script>
<script>
    (function () {
        if (!window.HNOffline) { return; }
        @auth
            @if (\App\Support\RoleHelper::esMedico(auth()->user()) || \App\Support\RoleHelper::tieneRol(auth()->user(), 'enfermeria'))
                HNOffline.activar();
            @else
                HNOffline.borrar();
            @endif
        @else
            // Sin sesión (login, sesión vencida o la de otra persona): no debe quedar nada guardado.
            HNOffline.borrar();
        @endauth
        // Al cerrar sesión se borran los pacientes guardados y la caché del service worker.
        document.addEventListener('submit', function (e) {
            var accion = e.target && e.target.getAttribute('action');
            if (accion && /\/logout$/.test(accion)) { HNOffline.borrar({ cache: true }); }
        }, true);
    })();
</script>
