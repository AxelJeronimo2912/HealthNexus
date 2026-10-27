<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('partials.pwa')

    <title>{{ isset($title) ? $title . ' · ' : '' }}{{ config('app.name', 'HealthNexus') }}</title>
    <link rel="icon" href="{{ asset('images/favicon-64.png') }}" type="image/png">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    <style>[x-cloak] { display: none !important; }</style>

    {{-- Evita el "salto" del sidebar al cargar: aplica el estado guardado antes de pintar --}}
    <script>
        try {
            if (localStorage.getItem('hn.sidebar.pinned') === '0') document.documentElement.classList.add('hn-collapsed');
        } catch (e) {}
    </script>
    <style>
        @media (min-width: 1024px) {
            html:not(.hn-ready) .hn-main { padding-left: 256px; }
            html.hn-collapsed:not(.hn-ready) aside[aria-label] { width: 72px !important; }
            html.hn-collapsed:not(.hn-ready) .hn-main { padding-left: 72px !important; }
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-full font-sans antialiased bg-nexus-bg text-slate-800">
    <div x-data="hnLayout" class="min-h-full">

        <x-sidebar />

        <div class="hn-main flex min-h-screen flex-col transition-[padding] duration-300 ease-in-out"
            :class="pinned ? 'lg:pl-64' : 'lg:pl-[72px]'">

            @include('layouts.navigation')

            @isset($header)
                {{-- El encabezado vive dentro del contenido, sin banda blanca separada --}}
                <div class="hn-page-header">
                    <div class="mx-auto max-w-7xl px-4 pt-7 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </div>
            @endisset

            <main class="flex-1">
                {{ $slot }}
            </main>

            <footer class="mx-auto w-full max-w-7xl px-4 py-6 text-xs text-slate-400 sm:px-6 lg:px-8">
                © {{ date('Y') }} HealthNexus · Tecnología que cuida vidas
            </footer>
        </div>
    </div>

    <x-toasts />

    @stack('scripts')
</body>

</html>
