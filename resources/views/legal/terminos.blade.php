@extends('layouts.legal')

@section('titulo', 'Términos de uso')
@section('descripcion', 'Términos y condiciones de uso de la plataforma HealthNexus, desarrollada por NEXARA.')

@section('contenido')
<h1>Términos de uso</h1>
<p class="fecha">Última actualización: {{ config('legal.actualizacion') }}</p>

<p>Estos términos regulan el uso de HealthNexus, la plataforma de gestión hospitalaria y control de accesos desarrollada por {{ config('legal.empresa') }}, que incluye el sitio web, la aplicación móvil y la API. Al iniciar sesión aceptas estos términos y el <a href="{{ route('privacidad') }}">Aviso de privacidad</a>.</p>

<h2>1. Quién puede usar HealthNexus</h2>
<ul>
    <li>El acceso es exclusivo para personal registrado por un hospital o institución de salud que opera la plataforma.</li>
    <li>Cada cuenta es personal e intransferible. El rol asignado (médico, enfermería, recepción, farmacia o administración) define a qué información y funciones tienes acceso.</li>
    <li>La página principal y estas páginas legales son públicas; el resto del sistema requiere iniciar sesión.</li>
</ul>

<h2>2. Tu cuenta y tus credenciales</h2>
<ul>
    <li>Eres responsable de mantener en secreto tu contraseña y, si eres médico, tu PIN. No los compartas con nadie, ni siquiera con compañeros o con personal de soporte.</li>
    <li>Todo lo que se haga con tu cuenta queda registrado a tu nombre.</li>
    <li>Si sospechas que alguien conoce tus credenciales, o pierdes un teléfono con la app instalada, avisa de inmediato al administrador del hospital para que bloquee el dispositivo o la cuenta.</li>
    <li>Cierra sesión al terminar, sobre todo en equipos compartidos.</li>
</ul>

<h2>3. Dispositivos</h2>
<p>Al iniciar sesión, el equipo queda registrado en la plataforma. El administrador del hospital puede bloquear cualquier dispositivo; un dispositivo bloqueado no podrá volver a entrar hasta que se autorice de nuevo.</p>

<h2>4. Uso permitido</h2>
<p>HealthNexus debe usarse solo para las actividades propias de tu puesto en el hospital. Queda prohibido:</p>
<ul>
    <li>Consultar información de pacientes que no estén a tu cargo o sin una razón relacionada con su atención.</li>
    <li>Copiar, exportar, fotografiar o difundir información clínica fuera de los casos autorizados por el hospital.</li>
    <li>Intentar entrar con cuentas ajenas, saltarse los controles de acceso o usar dispositivos no autorizados.</li>
    <li>Realizar pruebas de seguridad, escaneos, extracción masiva de datos o ingeniería inversa de la app o la API sin autorización por escrito de {{ config('legal.empresa') }}.</li>
    <li>Introducir información falsa o alterar registros clínicos de forma indebida.</li>
</ul>

<h2>5. Confidencialidad y auditoría</h2>
<p>La información de los pacientes es confidencial y está protegida por la ley y por el secreto profesional. La plataforma registra los accesos y las consultas a información sensible; esos registros pueden revisarse para verificar el uso correcto del sistema.</p>

<h2>6. Responsabilidad clínica</h2>
<p>HealthNexus es una herramienta de apoyo para registrar y consultar información. Las alertas de signos vitales y la clasificación de triage ayudan a priorizar, pero <strong>no sustituyen el criterio del profesional de la salud</strong>. Las decisiones clínicas son responsabilidad del personal que atiende al paciente.</p>

<h2>7. Disponibilidad del servicio</h2>
<p>{{ config('legal.empresa') }} procura que la plataforma esté disponible de forma continua, pero puede haber interrupciones por mantenimiento, actualizaciones o causas ajenas, como fallas de internet o del proveedor de infraestructura. Siempre que sea posible, el mantenimiento programado se avisará con anticipación. El hospital debe contar con un procedimiento alterno para seguir atendiendo si el sistema no está disponible.</p>

<h2>8. Suspensión de cuentas</h2>
<p>El hospital o {{ config('legal.empresa') }} pueden suspender o bloquear una cuenta o un dispositivo, sin previo aviso, cuando detecten un uso contrario a estos términos, un riesgo para la seguridad de la información o a solicitud del propio hospital.</p>

<h2>9. Propiedad intelectual</h2>
<p>El software, el diseño, la marca HealthNexus y su logotipo pertenecen a {{ config('legal.empresa') }}. El uso de la plataforma no te da ningún derecho sobre ellos. La información clínica registrada pertenece al hospital y a sus pacientes, conforme a la ley.</p>

<h2>10. Cambios a estos términos</h2>
<p>Estos términos pueden actualizarse. La versión vigente siempre estará publicada en esta página con su fecha de actualización. Si los cambios son importantes, se avisará dentro de la plataforma.</p>

<h2>11. Ley aplicable</h2>
<p>Estos términos se rigen por las leyes de los Estados Unidos Mexicanos. Cualquier controversia se resolverá ante los tribunales competentes de {{ config('legal.ciudad') }}.</p>

<h2>12. Contacto</h2>
<p>Dudas sobre estos términos: <a href="mailto:{{ config('legal.correo') }}">{{ config('legal.correo') }}</a></p>
@endsection
