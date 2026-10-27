<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>HealthNexus — Gestión hospitalaria y control de accesos | NEXARA</title>
<meta name="description" content="HealthNexus, de NEXARA: pacientes, triage, signos vitales, medicación y control de accesos para hospitales, en web y app móvil.">
<link rel="canonical" href="{{ url('/') }}">
<meta property="og:type" content="website">
<meta property="og:title" content="HealthNexus · by NEXARA">
<meta property="og:description" content="Gestión hospitalaria y control de accesos en web y app móvil.">
<meta property="og:url" content="{{ url('/') }}">
<meta property="og:image" content="{{ asset('images/logo-healthnexus.png') }}">
<link rel="icon" type="image/png" href="{{ asset('images/favicon-64.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
html{scroll-behavior:smooth}
body{margin:0}
a{color:#3B3FC4}a:hover{color:#2A2D96}
</style>
</head>
<body>
<div style="font-family: 'Plus Jakarta Sans', system-ui, sans-serif; color: #141A33; background: #F5F7FB; min-height: 100vh">

<header style="background: #0D1230; color: #FFFFFF">
<div style="max-width: 1200px; margin: 0 auto; padding: 18px 24px; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 16px">
<a href="#inicio" style="display: flex; align-items: center; gap: 10px; color: #FFFFFF; text-decoration: none; font-weight: 800; font-size: 20px">
@include('partials.logo')
<span style="display: flex; flex-direction: column; line-height: 1.1"><span>Health<span style="color: #B9A6FF">Nexus</span></span><span style="font-size: 11px; font-weight: 600; letter-spacing: 0.12em; color: #9EA3F7">BY NEXARA</span></span>
</a>
<nav aria-label="Principal" style="display: flex; flex-wrap: wrap; align-items: center; gap: 24px; font-size: 15px">
<a href="#modulos" style="color: #C9CDF0; text-decoration: none">Módulos</a>
<a href="#movil" style="color: #C9CDF0; text-decoration: none">App móvil</a>
<a href="#plataforma" style="color: #C9CDF0; text-decoration: none">Plataforma</a>
<a href="#seguridad" style="color: #C9CDF0; text-decoration: none">Seguridad</a>
<a href="#normas" style="color: #C9CDF0; text-decoration: none">Normas</a>
<a href="#nexara" style="color: #C9CDF0; text-decoration: none">NEXARA</a>
<a href="{{ url('/login') }}" style="background: #5B5FEF; color: #FFFFFF; text-decoration: none; padding: 12px 20px; border-radius: 10px; font-weight: 600">Iniciar sesión</a>
</nav>
</div>
</header>

<section id="inicio" style="background: #0D1230; color: #FFFFFF; padding: 72px 24px 96px">
<div style="max-width: 1200px; margin: 0 auto; display: flex; flex-wrap: wrap; align-items: center; gap: 56px">
<div style="flex: 1 1 460px; min-width: 0; display: flex; flex-direction: column; gap: 24px">
<span style="align-self: flex-start; font-size: 13px; font-weight: 600; letter-spacing: 0.06em; text-transform: uppercase; color: #9EA3F7; border: 1px solid #343A73; padding: 8px 14px; border-radius: 999px">Gestión hospitalaria y control de accesos · por NEXARA</span>
<h1 style="margin: 0; font-size: 56px; line-height: 1.08; font-weight: 800; letter-spacing: -0.02em">Toda la operación clínica de tu hospital, en un solo lugar.</h1>
<p style="margin: 0; font-size: 19px; line-height: 1.6; color: #C9CDF0; max-width: 560px">HealthNexus conecta pacientes, triage, signos vitales, medicación, citas y camas para que administración, médicos y enfermería trabajen con la misma información, desde la web o el celular, con acceso solo para personal y dispositivos autorizados.</p>
<div style="display: flex; flex-wrap: wrap; gap: 14px; padding-top: 8px">
<a href="{{ url('/login') }}" style="background: #5B5FEF; color: #FFFFFF; text-decoration: none; padding: 16px 26px; border-radius: 12px; font-weight: 700; font-size: 16px">Acceder al sistema</a>
<a href="#modulos" style="color: #FFFFFF; text-decoration: none; padding: 16px 26px; border-radius: 12px; font-weight: 600; font-size: 16px; border: 1px solid #343A73">Conocer los módulos</a>
</div>
</div>
<div style="flex: 1 1 420px; min-width: 0">
<div style="background: #FFFFFF; color: #141A33; border-radius: 20px; padding: 24px; display: flex; flex-direction: column; gap: 18px; box-shadow: 0 30px 60px rgba(0,0,0,0.35)">
<div style="display: flex; align-items: center; justify-content: space-between; gap: 12px">
<div style="font-weight: 700; font-size: 17px">Resumen de hoy</div>
<div style="font-size: 13px; color: #5A6080">[Fecha]</div>
</div>
<div style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px">
<div style="background: #F0F1FE; border-radius: 12px; padding: 14px; display: flex; flex-direction: column; gap: 4px"><span style="font-size: 12px; color: #4A4F75">Pacientes activos</span><span style="font-size: 26px; font-weight: 800">[ — ]</span></div>
<div style="background: #FDECEC; border-radius: 12px; padding: 14px; display: flex; flex-direction: column; gap: 4px"><span style="font-size: 12px; color: #8E1F1F">Críticos</span><span style="font-size: 26px; font-weight: 800">[ — ]</span></div>
<div style="background: #E7F6F3; border-radius: 12px; padding: 14px; display: flex; flex-direction: column; gap: 4px"><span style="font-size: 12px; color: #2E5F57">Dispositivos activos</span><span style="font-size: 26px; font-weight: 800">[ — ]</span></div>
</div>
<div style="display: flex; flex-direction: column; gap: 10px">
<div style="font-size: 13px; font-weight: 600; color: #4A4F75">Pacientes por prioridad</div>
<div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 12px 14px; border: 1px solid #E3E6F2; border-radius: 12px"><div style="display: flex; flex-direction: column; gap: 2px"><span style="font-weight: 600; font-size: 14px">[Paciente]</span><span style="font-size: 12px; color: #5A6080">Urgencias · Cama [#]</span></div><span style="font-size: 12px; font-weight: 700; color: #8E1F1F; background: #FDECEC; padding: 4px 10px; border-radius: 999px">Nivel I</span></div>
<div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 12px 14px; border: 1px solid #E3E6F2; border-radius: 12px"><div style="display: flex; flex-direction: column; gap: 2px"><span style="font-weight: 600; font-size: 14px">[Paciente]</span><span style="font-size: 12px; color: #5A6080">Consultorio [#] · [Médico]</span></div><span style="font-size: 12px; font-weight: 700; color: #8A3F06; background: #FFEBD9; padding: 4px 10px; border-radius: 999px">Nivel II</span></div>
<div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 12px 14px; border: 1px solid #E3E6F2; border-radius: 12px"><div style="display: flex; flex-direction: column; gap: 2px"><span style="font-weight: 600; font-size: 14px">[Paciente]</span><span style="font-size: 12px; color: #5A6080">Pediatría · [Médico]</span></div><span style="font-size: 12px; font-weight: 700; color: #6E4A00; background: #FFF4C7; padding: 4px 10px; border-radius: 999px">Nivel III</span></div>
</div>
</div>
</div>
</div>
</section>

<section aria-label="Para quién es" style="padding: 0 24px">
<div style="max-width: 1200px; margin: -48px auto 0; background: #FFFFFF; border-radius: 18px; padding: 28px; display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 24px; box-shadow: 0 10px 30px rgba(13,18,48,0.08)">
<div style="display: flex; flex-direction: column; gap: 6px"><span style="font-weight: 700; font-size: 16px">Administración</span><span style="font-size: 14px; line-height: 1.5; color: #4A4F75">Usuarios, roles, dispositivos, auditoría y cuentas por cobrar.</span></div>
<div style="display: flex; flex-direction: column; gap: 6px"><span style="font-weight: 700; font-size: 16px">Médicos</span><span style="font-size: 14px; line-height: 1.5; color: #4A4F75">Triage, consultas y expediente, con PIN como segundo factor.</span></div>
<div style="display: flex; flex-direction: column; gap: 6px"><span style="font-weight: 700; font-size: 16px">Enfermería</span><span style="font-size: 14px; line-height: 1.5; color: #4A4F75">Signos vitales, medicación, notas y seguimiento.</span></div>
<div style="display: flex; flex-direction: column; gap: 6px"><span style="font-weight: 700; font-size: 16px">Recepción</span><span style="font-size: 14px; line-height: 1.5; color: #4A4F75">Registro de pacientes, citas y control de camas.</span></div>
</div>
</section>

<section id="modulos" style="padding: 96px 24px 40px">
<div style="max-width: 1200px; margin: 0 auto; display: flex; flex-direction: column; gap: 40px">
<div style="display: flex; flex-direction: column; gap: 12px; max-width: 640px">
<span style="font-size: 13px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: #3B3FC4">Módulos</span>
<h2 style="margin: 0; font-size: 40px; line-height: 1.15; font-weight: 800; letter-spacing: -0.01em">Todo lo que el hospital necesita, organizado por área</h2>
</div>
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px">

<div style="background: #FFFFFF; border: 1px solid #E3E6F2; border-radius: 18px; padding: 28px; display: flex; flex-direction: column; gap: 18px">
<div style="width: 48px; height: 48px; border-radius: 12px; background: #F0F1FE; display: flex; align-items: center; justify-content: center"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#3B3FC4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="3" width="16" height="18" rx="2"></rect><path d="M9 21v-4h6v4M9 7h1M14 7h1M9 11h1M14 11h1"></path></svg></div>
<h3 style="margin: 0; font-size: 21px; font-weight: 700">Gestión hospitalaria</h3>
<ul style="margin: 0; padding: 0; list-style: none; display: flex; flex-direction: column; gap: 10px; font-size: 15px; color: #33395C">
<li>Servicios y especialidades</li>
<li>Citas y turnos del personal</li>
<li>Control de camas</li>
<li>Cuentas por cobrar</li>
</ul>
</div>

<div style="background: #FFFFFF; border: 1px solid #E3E6F2; border-radius: 18px; padding: 28px; display: flex; flex-direction: column; gap: 18px">
<div style="width: 48px; height: 48px; border-radius: 12px; background: #E7F6F3; display: flex; align-items: center; justify-content: center"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#1F7A6C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"></rect><path d="M3 10h18M8 3v4M16 3v4"></path></svg></div>
<h3 style="margin: 0; font-size: 21px; font-weight: 700">Agenda</h3>
<ul style="margin: 0; padding: 0; list-style: none; display: flex; flex-direction: column; gap: 10px; font-size: 15px; color: #33395C">
<li>Calendario de citas por médico</li>
<li>Vista diaria, semanal y mensual</li>
<li>Reagendar y cancelar en un clic</li>
</ul>
</div>

<div style="background: #FFFFFF; border: 1px solid #E3E6F2; border-radius: 18px; padding: 28px; display: flex; flex-direction: column; gap: 18px">
<div style="width: 48px; height: 48px; border-radius: 12px; background: #FFF3E6; display: flex; align-items: center; justify-content: center"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#A35B0E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 3h7l5 5v13H7z"></path><path d="M14 3v5h5M10 13h6M10 17h4"></path></svg></div>
<h3 style="margin: 0; font-size: 21px; font-weight: 700">Atención clínica</h3>
<ul style="margin: 0; padding: 0; list-style: none; display: flex; flex-direction: column; gap: 10px; font-size: 15px; color: #33395C">
<li>Expediente clínico</li>
<li>Consultas</li>
<li>Notas de enfermería</li>
<li>Administración de medicamentos</li>
<li>Seguimiento del paciente</li>
</ul>
</div>

<div style="background: #FFFFFF; border: 1px solid #E3E6F2; border-radius: 18px; padding: 28px; display: flex; flex-direction: column; gap: 18px">
<div style="width: 48px; height: 48px; border-radius: 12px; background: #FDECEC; display: flex; align-items: center; justify-content: center"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#B42323" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 12h4l3-8 4 16 3-8h4"></path></svg></div>
<h3 style="margin: 0; font-size: 21px; font-weight: 700">Urgencias y triage</h3>
<ul style="margin: 0; padding: 0; list-style: none; display: flex; flex-direction: column; gap: 10px; font-size: 15px; color: #33395C">
<li>Clasificación por niveles de prioridad (I a V)</li>
<li>Registro de signos vitales con alertas fuera de rango</li>
<li>Pacientes externos con ubicación de atención</li>
<li>Filtros por nivel y búsqueda rápida</li>
</ul>
</div>

<div style="background: #FFFFFF; border: 1px solid #E3E6F2; border-radius: 18px; padding: 28px; display: flex; flex-direction: column; gap: 18px">
<div style="width: 48px; height: 48px; border-radius: 12px; background: #F0F1FE; display: flex; align-items: center; justify-content: center"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#3B3FC4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="7" y="2" width="10" height="20" rx="2"></rect><path d="M11 18h2"></path></svg></div>
<h3 style="margin: 0; font-size: 21px; font-weight: 700">Control de accesos</h3>
<ul style="margin: 0; padding: 0; list-style: none; display: flex; flex-direction: column; gap: 10px; font-size: 15px; color: #33395C">
<li>Registro automático de los dispositivos que inician sesión</li>
<li>Bloqueo de un dispositivo desde el panel web</li>
<li>Historial de accesos permitidos y denegados</li>
<li>Auditoría de consultas a información sensible</li>
</ul>
</div>

<div style="background: #FFFFFF; border: 1px solid #E3E6F2; border-radius: 18px; padding: 28px; display: flex; flex-direction: column; gap: 18px">
<div style="width: 48px; height: 48px; border-radius: 12px; background: #E7F6F3; display: flex; align-items: center; justify-content: center"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#1F7A6C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="8" r="3.5"></circle><path d="M3 20c0-3.3 2.7-6 6-6s6 2.7 6 6M17 8v6M20 11h-6"></path></svg></div>
<h3 style="margin: 0; font-size: 21px; font-weight: 700">Usuarios y roles</h3>
<ul style="margin: 0; padding: 0; list-style: none; display: flex; flex-direction: column; gap: 10px; font-size: 15px; color: #33395C">
<li>Alta del personal con su rol en el hospital</li>
<li>Permisos distintos para médico, enfermería, recepción y administración</li>
<li>PIN personal para el rol de médico</li>
<li>Cuentas activas, suspendidas o bloqueadas</li>
</ul>
</div>

</div>
</div>
</section>

<section id="movil" style="padding: 56px 24px">
<div style="max-width: 1200px; margin: 0 auto; background: #FFFFFF; border: 1px solid #E3E6F2; border-radius: 24px; padding: 48px; display: flex; flex-wrap: wrap; align-items: center; gap: 48px">
<div style="flex: 1 1 420px; min-width: 0; display: flex; flex-direction: column; gap: 18px">
<span style="font-size: 13px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: #3B3FC4">Web + móvil</span>
<h2 style="margin: 0; font-size: 36px; line-height: 1.2; font-weight: 800">El sistema acompaña a tu personal por todo el hospital</h2>
<p style="margin: 0; font-size: 17px; line-height: 1.6; color: #4A4F75">La app móvil para Android es de uso exclusivo para el personal del hospital. Médicos y enfermería registran triage, signos vitales y medicación desde el piso, y todo queda al instante en la plataforma web porque ambas usan la misma API.</p>
<ul style="margin: 0; padding: 0; list-style: none; display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 10px; font-size: 15px; color: #33395C">
<li style="display: flex; gap: 8px; align-items: flex-start"><span style="color: #1F7A6C; font-weight: 800">✓</span>Menú lateral con los módulos de cada rol</li>
<li style="display: flex; gap: 8px; align-items: flex-start"><span style="color: #1F7A6C; font-weight: 800">✓</span>Inicio de sesión con PIN para médicos</li>
<li style="display: flex; gap: 8px; align-items: flex-start"><span style="color: #1F7A6C; font-weight: 800">✓</span>Signos vitales con validación de rangos</li>
<li style="display: flex; gap: 8px; align-items: flex-start"><span style="color: #1F7A6C; font-weight: 800">✓</span>Sesión guardada de forma segura en el teléfono</li>
</ul>
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; padding-top: 8px">
<div style="display: flex; flex-direction: column; gap: 6px; padding: 18px; background: #F5F7FB; border-radius: 14px"><span style="font-size: 13px; font-weight: 700; color: #3B3FC4">1</span><span style="font-weight: 700">Panel web</span><span style="font-size: 14px; color: #4A4F75; line-height: 1.5">Administración, dispositivos y auditoría desde el escritorio.</span></div>
<div style="display: flex; flex-direction: column; gap: 6px; padding: 18px; background: #F5F7FB; border-radius: 14px"><span style="font-size: 13px; font-weight: 700; color: #3B3FC4">2</span><span style="font-weight: 700">API REST</span><span style="font-size: 14px; color: #4A4F75; line-height: 1.5">Un solo backend seguro para la web y la app.</span></div>
<div style="display: flex; flex-direction: column; gap: 6px; padding: 18px; background: #F5F7FB; border-radius: 14px"><span style="font-size: 13px; font-weight: 700; color: #3B3FC4">3</span><span style="font-weight: 700">App móvil</span><span style="font-size: 14px; color: #4A4F75; line-height: 1.5">Para el personal en piso, con su cuenta del hospital.</span></div>
</div>
<span style="align-self: flex-start; font-size: 13px; font-weight: 600; color: #33395C; background: #F0F1FE; padding: 8px 14px; border-radius: 999px">Android · Próximamente en Google Play</span>
</div>
<div style="flex: 0 1 280px; min-width: 0; display: flex; justify-content: center">
<div style="width: 240px; height: 480px; border-radius: 36px; background: #0D1230; padding: 12px; box-sizing: border-box">
<div style="width: 100%; height: 100%; border-radius: 26px; background: #F5F7FB; padding: 22px 16px; box-sizing: border-box; display: flex; flex-direction: column; gap: 12px">
<span style="font-weight: 800; font-size: 17px">Mi turno</span>
<div style="background: #FFFFFF; border-radius: 12px; padding: 12px; display: flex; flex-direction: column; gap: 4px"><span style="font-size: 12px; color: #B42323; font-weight: 700">Nivel I · Urgencias</span><span style="font-size: 14px; font-weight: 600">[Paciente]</span><span style="font-size: 12px; color: #5A6080">FC ▲ · SpO₂ ▼ · hace [#] min</span></div>
<div style="background: #FFFFFF; border-radius: 12px; padding: 12px; display: flex; flex-direction: column; gap: 4px"><span style="font-size: 12px; color: #1F7A6C; font-weight: 700">Cama [#] · Medicación</span><span style="font-size: 14px; font-weight: 600">[Paciente]</span><span style="font-size: 12px; color: #5A6080">Siguiente dosis a las 12:00</span></div>
<div style="margin-top: auto; background: #5B5FEF; color: #FFFFFF; border-radius: 12px; padding: 12px; text-align: center; font-size: 14px; font-weight: 700">Registrar signos vitales</div>
</div>
</div>
</div>
</div>
</section>

<section id="plataforma" style="padding: 56px 24px">
<div style="max-width: 1200px; margin: 0 auto; display: flex; flex-direction: column; gap: 32px">
<div style="display: flex; flex-direction: column; gap: 12px; max-width: 700px">
<span style="font-size: 13px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: #3B3FC4">Plataforma</span>
<h2 style="margin: 0; font-size: 36px; line-height: 1.2; font-weight: 800">Tres piezas, una sola información</h2>
<p style="margin: 0; font-size: 17px; line-height: 1.6; color: #4A4F75">HealthNexus ya funciona en producción en la nube de AWS, con la web, la API y la app conectadas a la misma base de datos.</p>
</div>
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px">
<div style="background: #FFFFFF; border: 1px solid #E3E6F2; border-radius: 18px; padding: 28px; display: flex; flex-direction: column; gap: 12px">
<span style="font-size: 13px; font-weight: 700; color: #3B3FC4">Web</span>
<span style="font-weight: 700; font-size: 19px">Panel de administración</span>
<span style="font-size: 15px; line-height: 1.55; color: #4A4F75">Hecho con Laravel. Gestión del hospital, usuarios, dispositivos y auditoría.</span>
<div style="display: flex; flex-wrap: wrap; gap: 8px; padding-top: 4px"><span style="font-size: 12px; font-weight: 600; color: #33395C; background: #F5F7FB; padding: 6px 10px; border-radius: 8px">Laravel</span><span style="font-size: 12px; font-weight: 600; color: #33395C; background: #F5F7FB; padding: 6px 10px; border-radius: 8px">Nginx</span><span style="font-size: 12px; font-weight: 600; color: #33395C; background: #F5F7FB; padding: 6px 10px; border-radius: 8px">HTTPS</span></div>
</div>
<div style="background: #FFFFFF; border: 1px solid #E3E6F2; border-radius: 18px; padding: 28px; display: flex; flex-direction: column; gap: 12px">
<span style="font-size: 13px; font-weight: 700; color: #3B3FC4">API</span>
<span style="font-weight: 700; font-size: 19px">Servicio central</span>
<span style="font-size: 15px; line-height: 1.55; color: #4A4F75">API REST en Node.js y Express, empaquetada en contenedores Docker y conectada a MySQL en Amazon RDS.</span>
<div style="display: flex; flex-wrap: wrap; gap: 8px; padding-top: 4px"><span style="font-size: 12px; font-weight: 600; color: #33395C; background: #F5F7FB; padding: 6px 10px; border-radius: 8px">Node.js</span><span style="font-size: 12px; font-weight: 600; color: #33395C; background: #F5F7FB; padding: 6px 10px; border-radius: 8px">Docker</span><span style="font-size: 12px; font-weight: 600; color: #33395C; background: #F5F7FB; padding: 6px 10px; border-radius: 8px">MySQL · RDS</span></div>
</div>
<div style="background: #FFFFFF; border: 1px solid #E3E6F2; border-radius: 18px; padding: 28px; display: flex; flex-direction: column; gap: 12px">
<span style="font-size: 13px; font-weight: 700; color: #3B3FC4">Móvil</span>
<span style="font-weight: 700; font-size: 19px">App para el personal</span>
<span style="font-size: 15px; line-height: 1.55; color: #4A4F75">Desarrollada con React Native para Android, con versiones separadas para pruebas y producción.</span>
<div style="display: flex; flex-wrap: wrap; gap: 8px; padding-top: 4px"><span style="font-size: 12px; font-weight: 600; color: #33395C; background: #F5F7FB; padding: 6px 10px; border-radius: 8px">React Native</span><span style="font-size: 12px; font-weight: 600; color: #33395C; background: #F5F7FB; padding: 6px 10px; border-radius: 8px">Android</span></div>
</div>
<div style="background: #FFFFFF; border: 1px solid #E3E6F2; border-radius: 18px; padding: 28px; display: flex; flex-direction: column; gap: 12px">
<span style="font-size: 13px; font-weight: 700; color: #3B3FC4">Nube</span>
<span style="font-weight: 700; font-size: 19px">Infraestructura en AWS</span>
<span style="font-size: 15px; line-height: 1.55; color: #4A4F75">Servidores en Amazon EC2, base de datos administrada y secretos separados por ambiente: desarrollo, pruebas y producción.</span>
<div style="display: flex; flex-wrap: wrap; gap: 8px; padding-top: 4px"><span style="font-size: 12px; font-weight: 600; color: #33395C; background: #F5F7FB; padding: 6px 10px; border-radius: 8px">EC2</span><span style="font-size: 12px; font-weight: 600; color: #33395C; background: #F5F7FB; padding: 6px 10px; border-radius: 8px">RDS</span><span style="font-size: 12px; font-weight: 600; color: #33395C; background: #F5F7FB; padding: 6px 10px; border-radius: 8px">Parameter Store</span></div>
</div>
</div>
</div>
</section>

<section id="seguridad" style="padding: 56px 24px">
<div style="max-width: 1200px; margin: 0 auto; display: flex; flex-direction: column; gap: 32px">
<div style="display: flex; flex-direction: column; gap: 12px; max-width: 680px">
<span style="font-size: 13px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: #3B3FC4">Seguridad y privacidad</span>
<h2 style="margin: 0; font-size: 36px; line-height: 1.2; font-weight: 800">Los datos de salud se tratan con cuidado</h2>
</div>
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px">
<div style="background: #FFFFFF; border: 1px solid #E3E6F2; border-radius: 16px; padding: 24px; display: flex; flex-direction: column; gap: 10px"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#3B3FC4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="5" y="11" width="14" height="10" rx="2"></rect><path d="M8 11V7a4 4 0 0 1 8 0v4"></path></svg><span style="font-weight: 700; font-size: 17px">Conexión cifrada</span><span style="font-size: 14px; line-height: 1.55; color: #4A4F75">Todo el tráfico viaja por HTTPS.</span></div>
<div style="background: #FFFFFF; border: 1px solid #E3E6F2; border-radius: 16px; padding: 24px; display: flex; flex-direction: column; gap: 10px"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#3B3FC4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="8" r="3.5"></circle><path d="M3 20c0-3.3 2.7-6 6-6s6 2.7 6 6M16 11l2 2 3-4"></path></svg><span style="font-weight: 700; font-size: 17px">Roles y permisos</span><span style="font-size: 14px; line-height: 1.55; color: #4A4F75">Cada usuario ve solo lo que le corresponde a su puesto.</span></div>
<div style="background: #FFFFFF; border: 1px solid #E3E6F2; border-radius: 16px; padding: 24px; display: flex; flex-direction: column; gap: 10px"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#3B3FC4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3l8 3v6c0 4.5-3.4 8.3-8 9-4.6-.7-8-4.5-8-9V6z"></path></svg><span style="font-weight: 700; font-size: 17px">Contraseñas protegidas</span><span style="font-size: 14px; line-height: 1.55; color: #4A4F75">Las contraseñas y los PIN se guardan cifrados; nadie puede leerlos.</span></div>
<div style="background: #FFFFFF; border: 1px solid #E3E6F2; border-radius: 16px; padding: 24px; display: flex; flex-direction: column; gap: 10px"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#3B3FC4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="6" width="18" height="12" rx="2"></rect><path d="M7 12h.01M11 12h.01M15 12h.01"></path></svg><span style="font-weight: 700; font-size: 17px">PIN para médicos</span><span style="font-size: 14px; line-height: 1.55; color: #4A4F75">Un segundo paso de verificación para el rol con acceso al expediente.</span></div>
<div style="background: #FFFFFF; border: 1px solid #E3E6F2; border-radius: 16px; padding: 24px; display: flex; flex-direction: column; gap: 10px"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#3B3FC4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="7" y="2" width="10" height="20" rx="2"></rect><path d="M11 18h2"></path></svg><span style="font-weight: 700; font-size: 17px">Dispositivos autorizados</span><span style="font-size: 14px; line-height: 1.55; color: #4A4F75">Cada equipo queda registrado y puede bloquearse si se pierde o no es reconocido.</span></div>
<div style="background: #FFFFFF; border: 1px solid #E3E6F2; border-radius: 16px; padding: 24px; display: flex; flex-direction: column; gap: 10px"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#3B3FC4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 2"></path></svg><span style="font-weight: 700; font-size: 17px">Límite de intentos</span><span style="font-size: 14px; line-height: 1.55; color: #4A4F75">Los intentos repetidos de acceso se frenan para evitar ataques de adivinación.</span></div>
<div style="background: #FFFFFF; border: 1px solid #E3E6F2; border-radius: 16px; padding: 24px; display: flex; flex-direction: column; gap: 10px"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#3B3FC4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 3h7l5 5v13H7z"></path><path d="M14 3v5h5M10 13h6M10 17h4"></path></svg><span style="font-weight: 700; font-size: 17px">Auditoría</span><span style="font-size: 14px; line-height: 1.55; color: #4A4F75">Queda registro de quién entró, desde qué equipo y qué información sensible consultó.</span></div>
<div style="background: #FFFFFF; border: 1px solid #E3E6F2; border-radius: 16px; padding: 24px; display: flex; flex-direction: column; gap: 10px"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#3B3FC4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 3h7l5 5v13H7z"></path><path d="M14 3v5h5M10 14l2 2 4-4"></path></svg><span style="font-weight: 700; font-size: 17px">Aviso de privacidad</span><span style="font-size: 14px; line-height: 1.55; color: #4A4F75">Explica qué datos se usan y para qué. <a href="{{ route('privacidad') }}">Leer aviso</a></span></div>
</div>
</div>
</section>

<section id="normas" style="padding: 56px 24px">
<div style="max-width: 1200px; margin: 0 auto; display: flex; flex-direction: column; gap: 32px">
<div style="display: flex; flex-direction: column; gap: 12px; max-width: 720px">
<span style="font-size: 13px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: #3B3FC4">Normas y estándares</span>
<h2 style="margin: 0; font-size: 36px; line-height: 1.2; font-weight: 800">Construido con base en normas reconocidas</h2>
<p style="margin: 0; font-size: 17px; line-height: 1.6; color: #4A4F75">Usamos estas normas como referencia para diseñar, desarrollar y proteger el sistema.</p>
</div>
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 20px">

<div style="background: #FFFFFF; border: 1px solid #E3E6F2; border-radius: 18px; padding: 28px; display: flex; flex-direction: column; gap: 20px">
<h3 style="margin: 0; font-size: 20px; font-weight: 700">Normativa mexicana en salud y datos</h3>
<div style="display: flex; flex-direction: column; gap: 4px"><span style="font-weight: 700; color: #3B3FC4">NOM-004-SSA3-2012</span><span style="font-size: 15px; line-height: 1.5; color: #33395C">Del expediente clínico: contenido mínimo, integridad y confidencialidad.</span></div>
<div style="display: flex; flex-direction: column; gap: 4px"><span style="font-weight: 700; color: #3B3FC4">NOM-024-SSA3-2012</span><span style="font-size: 15px; line-height: 1.5; color: #33395C">Sistemas de información de registro electrónico para la salud.</span></div>
<div style="display: flex; flex-direction: column; gap: 4px"><span style="font-weight: 700; color: #3B3FC4">LFPDPPP (2025)</span><span style="font-size: 15px; line-height: 1.5; color: #33395C">Ley Federal de Protección de Datos Personales en Posesión de los Particulares. Los datos de salud se tratan como datos sensibles.</span></div>
</div>

<div style="background: #FFFFFF; border: 1px solid #E3E6F2; border-radius: 18px; padding: 28px; display: flex; flex-direction: column; gap: 20px">
<h3 style="margin: 0; font-size: 20px; font-weight: 700">Estándares internacionales</h3>
<div style="display: flex; flex-direction: column; gap: 4px"><span style="font-weight: 700; color: #3B3FC4">ISO/IEC 25010</span><span style="font-size: 15px; line-height: 1.5; color: #33395C">Modelo de calidad del producto de software.</span></div>
<div style="display: flex; flex-direction: column; gap: 4px"><span style="font-weight: 700; color: #3B3FC4">ISO/IEC 27001</span><span style="font-size: 15px; line-height: 1.5; color: #33395C">Buenas prácticas de seguridad de la información.</span></div>
<div style="display: flex; flex-direction: column; gap: 4px"><span style="font-weight: 700; color: #3B3FC4">ISO/IEC/IEEE 12207</span><span style="font-size: 15px; line-height: 1.5; color: #33395C">Procesos del ciclo de vida del software.</span></div>
<div style="display: flex; flex-direction: column; gap: 4px"><span style="font-weight: 700; color: #3B3FC4">ISO 9241-210 y WCAG 2.1</span><span style="font-size: 15px; line-height: 1.5; color: #33395C">Diseño centrado en el usuario y accesibilidad web.</span></div>
</div>

</div>
<p style="margin: 0; font-size: 13px; line-height: 1.5; color: #5A6080">HealthNexus toma estas normas como referencia de diseño y desarrollo; no cuenta con una certificación ISO.</p>
</div>
</section>

<section id="nexara" style="padding: 56px 24px 96px">
<div style="max-width: 1200px; margin: 0 auto; background: #0D1230; color: #FFFFFF; border-radius: 24px; padding: 48px; display: flex; flex-wrap: wrap; gap: 48px">
<div style="flex: 1 1 420px; min-width: 0; display: flex; flex-direction: column; gap: 16px">
<span style="font-size: 13px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: #9EA3F7">Quiénes somos</span>
<h2 style="margin: 0; font-size: 34px; line-height: 1.2; font-weight: 800">Desarrollado por NEXARA</h2>
<p style="margin: 0; font-size: 17px; line-height: 1.6; color: #C9CDF0">NEXARA es la startup que diseña y desarrolla HealthNexus de punta a punta: la plataforma web, la app móvil y la API que las conecta. Nació en la Universidad Tecnológica del Valle de Toluca, en Ingeniería en Desarrollo y Gestión de Software, y trabaja con metodología Scrum en entregas por sprint.</p>
<p style="margin: 0; font-size: 15px; line-height: 1.6; color: #C9CDF0">El acceso al sistema es exclusivo para personal registrado del hospital.</p>
</div>
<div style="flex: 1 1 320px; min-width: 0; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 14px; align-content: start">
<div style="border: 1px solid #343A73; border-radius: 14px; padding: 16px; display: flex; flex-direction: column; gap: 4px"><span style="font-weight: 700">Jesus Felipe Aviles</span><span style="font-size: 13px; color: #C9CDF0">Scrum Master · API y app móvil</span></div>
<div style="border: 1px solid #343A73; border-radius: 14px; padding: 16px; display: flex; flex-direction: column; gap: 4px"><span style="font-weight: 700">[Nombre del integrante]</span><span style="font-size: 13px; color: #C9CDF0">Plataforma web</span></div>
<div style="border: 1px solid #343A73; border-radius: 14px; padding: 16px; display: flex; flex-direction: column; gap: 4px"><span style="font-weight: 700">[Nombre del integrante]</span><span style="font-size: 13px; color: #C9CDF0">[Rol en el equipo]</span></div>
<div style="border: 1px solid #343A73; border-radius: 14px; padding: 16px; display: flex; flex-direction: column; gap: 4px"><span style="font-weight: 700">[Nombre del asesor]</span><span style="font-size: 13px; color: #C9CDF0">Asesor académico</span></div>
</div>
</div>
</section>

<footer style="border-top: 1px solid #E3E6F2; background: #FFFFFF; padding: 32px 24px">
<div style="max-width: 1200px; margin: 0 auto; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 16px; font-size: 14px; color: #4A4F75">
<span>© 2026 NEXARA · HealthNexus es un producto de NEXARA</span>
<nav aria-label="Pie de página" style="display: flex; flex-wrap: wrap; gap: 20px">
<a href="{{ route('privacidad') }}">Aviso de privacidad</a>
<a href="{{ route('terminos') }}">Términos de uso</a>
<a href="mailto:{{ config('legal.correo') }}">Contacto</a>
</nav>
</div>
</footer>

</div>
</body>
</html>
