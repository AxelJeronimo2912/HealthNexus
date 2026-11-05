{{--
    Layout de las páginas de error de HealthNexus.

    Es autosuficiente a propósito: estilos en línea, sin Vite, sin consultas a la
    base de datos y sin componentes que dependan de la sesión. Así la página se
    muestra aunque falle la compilación de assets, la base o la propia aplicación
    (que es justo cuando aparece un 500 o un 503).
--}}
@php
    $tone = trim($__env->yieldContent('tone', 'brand'));
    $tones = [
        'brand' => ['#EEF0FB', '#3A40A6'],
        'amber' => ['#FFFBEB', '#B45309'],
        'red'   => ['#FEF2F2', '#B91C1C'],
        'sky'   => ['#F0F9FF', '#0369A1'],
    ];
    [$toneBg, $toneFg] = $tones[$tone] ?? $tones['brand'];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <meta name="theme-color" content="#141739">
    <title>@yield('code') · @yield('title') · HealthNexus</title>
    <link rel="icon" href="{{ asset('images/favicon-64.png') }}" type="image/png">
    <style>
        :root {
            --brand-950: #141739; --brand-900: #1E2257; --brand-700: #2F3488; --brand-600: #3A40A6;
            --brand-200: #BCC3EF; --brand-50: #EEF0FB; --accent-400: #9A7DF0; --accent-300: #B9A6F7;
            --bg: #F7F8FC; --ink: #0F172A; --text: #334155; --muted: #64748B; --line: #E2E8F0;
            --tone-bg: {{ $toneBg }}; --tone-fg: {{ $toneFg }};
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; }
        body {
            font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: var(--bg); color: var(--text); -webkit-font-smoothing: antialiased;
            display: flex; min-height: 100vh;
        }
        a { color: inherit; }
        :focus-visible { outline: 2px solid var(--brand-600); outline-offset: 2px; border-radius: 8px; }

        /* Panel de marca (solo escritorio) */
        .brand-panel {
            position: relative; overflow: hidden; width: 42%; max-width: 560px; background: var(--brand-950); color: #fff;
            display: flex; flex-direction: column; justify-content: space-between; padding: 48px;
        }
        .brand-panel::before {
            content: ""; position: absolute; inset: 0; opacity: .9;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='240' height='80' viewBox='0 0 240 80'%3E%3Cpath d='M0 40h70l8-14 10 30 12-48 12 52 8-20h120' fill='none' stroke='%23ffffff' stroke-opacity='0.07' stroke-width='2' stroke-linejoin='round' stroke-linecap='round'/%3E%3C/svg%3E");
            background-size: 240px 80px;
        }
        .brand-panel::after {
            content: ""; position: absolute; width: 520px; height: 520px; right: -200px; top: -200px; border-radius: 50%;
            background: radial-gradient(closest-side, rgba(126, 90, 230, .38), transparent);
        }
        .brand-panel > * { position: relative; z-index: 1; }
        .logo { display: inline-flex; align-items: center; gap: 12px; text-decoration: none; font-weight: 700; font-size: 18px; letter-spacing: -.01em; }
        .logo span.mark { width: 40px; height: 40px; border-radius: 11px; background: #fff; display: grid; place-items: center; }
        .logo img { width: 30px; height: 30px; }
        .logo b { color: var(--accent-300); font-weight: 700; }
        .big-code { font-size: 168px; line-height: .9; font-weight: 800; letter-spacing: -.06em;
            background: linear-gradient(180deg, #fff, rgba(255,255,255,.25)); -webkit-background-clip: text; background-clip: text; color: transparent; }
        .brand-panel p { margin-top: 18px; color: var(--brand-200); font-size: 15px; line-height: 1.6; max-width: 340px; }
        .brand-foot { font-size: 12px; color: #8C93C9; }

        /* Contenido */
        .content { flex: 1; display: flex; flex-direction: column; padding: 32px 24px; }
        .content .mobile-logo { display: none; color: var(--brand-900); }
        .card { margin: auto; width: 100%; max-width: 520px; }
        .icon { width: 64px; height: 64px; border-radius: 18px; display: grid; place-items: center; background: var(--tone-bg); color: var(--tone-fg); }
        .icon svg { width: 32px; height: 32px; }
        .code-pill { display: inline-block; margin-top: 28px; padding: 4px 12px; border-radius: 999px; background: var(--tone-bg); color: var(--tone-fg);
            font: 600 12px ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; letter-spacing: .08em; }
        h1 { margin-top: 12px; font-size: 32px; line-height: 1.15; font-weight: 800; letter-spacing: -.03em; color: var(--ink); }
        .message { margin-top: 12px; font-size: 16px; line-height: 1.65; color: var(--text); }
        .detail { margin-top: 18px; padding: 14px 16px; border-radius: 12px; background: #fff; border: 1px solid var(--line); font-size: 14px; line-height: 1.55; color: var(--text); }
        .detail strong { color: var(--ink); }
        .hint { margin-top: 18px; display: flex; gap: 10px; align-items: flex-start; font-size: 14px; line-height: 1.55; color: var(--muted); }
        .hint svg { flex: none; width: 18px; height: 18px; margin-top: 2px; color: var(--brand-600); }
        .actions { margin-top: 28px; display: flex; flex-wrap: wrap; gap: 10px; }
        .btn { display: inline-flex; align-items: center; gap: 8px; padding: 11px 18px; border-radius: 12px; font-size: 14px; font-weight: 600;
            text-decoration: none; border: 0; cursor: pointer; font-family: inherit; transition: background .15s, color .15s; }
        .btn svg { width: 18px; height: 18px; }
        .btn-primary { background: var(--brand-600); color: #fff; box-shadow: 0 1px 2px rgba(15,23,42,.08); }
        .btn-primary:hover { background: var(--brand-700); }
        .btn-secondary { background: #fff; color: var(--text); box-shadow: inset 0 0 0 1px var(--line); }
        .btn-secondary:hover { background: #F8FAFC; color: var(--ink); }
        .meta { margin-top: 36px; padding-top: 18px; border-top: 1px solid var(--line); font-size: 12px; color: #94A3B8;
            display: flex; flex-wrap: wrap; gap: 6px 18px; }
        .meta code { font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; }

        @media (max-width: 900px) {
            .brand-panel { display: none; }
            .content { padding: 24px 20px; }
            .content .mobile-logo { display: inline-flex; margin-bottom: 24px; }
            .card { margin: 24px auto auto; }
            h1 { font-size: 27px; }
        }
        @media (prefers-color-scheme: dark) {
            :root { --bg: #0D0F24; --ink: #F1F5F9; --text: #CBD5E1; --muted: #94A3B8; --line: #262A52; }
            .detail, .btn-secondary { background: #151838; }
            .btn-secondary:hover { background: #1B1F45; color: #fff; }
            .content .mobile-logo { color: #fff; }
        }
    </style>
</head>
<body>
    <aside class="brand-panel" aria-hidden="true">
        <a class="logo" href="{{ url('/') }}" tabindex="-1">
            <span class="mark"><img src="{{ asset('images/logo-mark.png') }}" alt=""></span>
            <span>Health<b>Nexus</b></span>
        </a>
        <div>
            <div class="big-code">@yield('code')</div>
            <p>@yield('panel', 'Tecnología que cuida vidas.')</p>
        </div>
        <div class="brand-foot">© {{ date('Y') }} HealthNexus</div>
    </aside>

    <main class="content">
        <a class="logo mobile-logo" href="{{ url('/') }}">
            <span class="mark" style="background:var(--brand-950)"><img src="{{ asset('images/logo-mark.png') }}" alt=""></span>
            <span>Health<b>Nexus</b></span>
        </a>

        <div class="card">
            <div class="icon">@yield('icon')</div>
            <span class="code-pill">ERROR @yield('code')</span>
            <h1>@yield('title')</h1>
            <p class="message">@yield('message')</p>

            @hasSection('detail')
                <div class="detail">@yield('detail')</div>
            @endif

            @hasSection('hint')
                <p class="hint">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 16v-4M12 8h.01"/></svg>
                    <span>@yield('hint')</span>
                </p>
            @endif

            <div class="actions">@yield('actions')</div>

            <div class="meta">
                <span>{{ now()->format('d/m/Y H:i') }}</span>
                <span>Ruta: <code>/{{ ltrim(request()->path(), '/') }}</code></span>
                @hasSection('reference')<span>@yield('reference')</span>@endif
            </div>
        </div>
    </main>
</body>
</html>
