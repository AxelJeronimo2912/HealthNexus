<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('titulo') · HealthNexus</title>
<meta name="description" content="@yield('descripcion')">
<link rel="canonical" href="{{ url()->current() }}">
<link rel="icon" type="image/png" href="{{ asset('images/favicon-64.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    body { margin: 0; font-family: 'Plus Jakarta Sans', system-ui, sans-serif; color: #141A33; background: #F5F7FB; }
    a { color: #3B3FC4; } a:hover { color: #2A2D96; }
    .legal { max-width: 820px; margin: 0 auto; padding: 48px 24px 80px; }
    .legal-card { background: #FFFFFF; border: 1px solid #E3E6F2; border-radius: 20px; padding: 40px; }
    .legal h1 { margin: 0 0 8px; font-size: 36px; line-height: 1.15; font-weight: 800; letter-spacing: -0.01em; }
    .legal .fecha { margin: 0 0 32px; font-size: 14px; color: #5A6080; }
    .legal h2 { margin: 36px 0 12px; font-size: 21px; font-weight: 700; }
    .legal h3 { margin: 20px 0 8px; font-size: 16px; font-weight: 700; }
    .legal p, .legal li { font-size: 16px; line-height: 1.7; color: #33395C; }
    .legal ul { padding-left: 22px; margin: 8px 0 16px; }
    .legal li { margin-bottom: 6px; }
    .legal .nota { background: #F0F1FE; border-radius: 12px; padding: 16px 18px; font-size: 15px; }
    .legal table { width: 100%; border-collapse: collapse; margin: 12px 0 20px; font-size: 15px; }
    .legal th, .legal td { text-align: left; padding: 10px 12px; border-bottom: 1px solid #E3E6F2; vertical-align: top; color: #33395C; }
    .legal th { background: #F5F7FB; font-weight: 700; color: #141A33; }
    .tabla { overflow-x: auto; }
    @media (max-width: 640px) { .legal-card { padding: 24px; } .legal h1 { font-size: 28px; } }
</style>
</head>
<body>

<header style="background: #0D1230; color: #FFFFFF">
    <div style="max-width: 1200px; margin: 0 auto; padding: 18px 24px; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 16px">
        <a href="{{ url('/') }}" style="display: flex; align-items: center; gap: 10px; color: #FFFFFF; text-decoration: none; font-weight: 800; font-size: 20px">
            @include('partials.logo')
            <span style="display: flex; flex-direction: column; line-height: 1.1"><span>Health<span style="color: #B9A6FF">Nexus</span></span><span style="font-size: 11px; font-weight: 600; letter-spacing: 0.12em; color: #9EA3F7">BY NEXARA</span></span>
        </a>
        <nav aria-label="Principal" style="display: flex; flex-wrap: wrap; align-items: center; gap: 24px; font-size: 15px">
            <a href="{{ url('/') }}" style="color: #C9CDF0; text-decoration: none">Inicio</a>
            <a href="{{ route('privacidad') }}" style="color: #C9CDF0; text-decoration: none">Privacidad</a>
            <a href="{{ route('terminos') }}" style="color: #C9CDF0; text-decoration: none">Términos</a>
            <a href="{{ url('/login') }}" style="background: #5B5FEF; color: #FFFFFF; text-decoration: none; padding: 12px 20px; border-radius: 10px; font-weight: 600">Iniciar sesión</a>
        </nav>
    </div>
</header>

<main class="legal">
    <article class="legal-card">
        @yield('contenido')
    </article>
</main>

<footer style="border-top: 1px solid #E3E6F2; background: #FFFFFF; padding: 32px 24px">
    <div style="max-width: 1200px; margin: 0 auto; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 16px; font-size: 14px; color: #4A4F75">
        <span>© {{ date('Y') }} <a href="{{ config('legal.sitio') }}" target="_blank" rel="noopener">NEXARA</a> · HealthNexus es un producto de NEXARA</span>
        <nav aria-label="Pie de página" style="display: flex; flex-wrap: wrap; gap: 20px">
            <a href="{{ route('privacidad') }}">Aviso de privacidad</a>
            <a href="{{ route('terminos') }}">Términos de uso</a>
            <a href="mailto:{{ config('legal.correo') }}">Contacto</a>
        </nav>
    </div>
</footer>

</body>
</html>
