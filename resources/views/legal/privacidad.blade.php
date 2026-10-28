@extends('layouts.legal')

@section('titulo', 'Aviso de privacidad')
@section('descripcion', 'Aviso de privacidad integral de la plataforma HealthNexus.')

@section('contenido')
<h1>Aviso de privacidad integral</h1>
<p class="fecha">Última actualización: {{ config('legal.actualizacion') }}</p>

<p>Este aviso explica qué datos personales se tratan en la plataforma HealthNexus (sitio web, aplicación móvil y API), para qué se usan, con quién se comparten y cómo puedes ejercer tus derechos. Se emite con base en la Ley Federal de Protección de Datos Personales en Posesión de los Particulares (LFPDPPP) vigente.</p>

<h2>1. Quién es responsable de tus datos</h2>
<p>HealthNexus es un sistema que usan los hospitales para su operación. Por eso hay dos papeles distintos:</p>
<ul>
    <li><strong>El hospital o institución de salud</strong> que contrata y opera HealthNexus es el <strong>responsable</strong> de los datos de sus pacientes y de su personal. Para ejercer tus derechos sobre esos datos, también puedes acudir directamente al hospital.</li>
    <li><strong>{{ config('legal.empresa') }}</strong>, con domicilio en {{ config('legal.domicilio') }} y teléfono {{ config('legal.telefono') }}, desarrolla y mantiene la plataforma. Trata los datos <strong>por cuenta del hospital</strong> (como encargado), solo para prestar el servicio y nunca para fines propios.</li>
</ul>
<p>Contacto para temas de privacidad: <a href="mailto:{{ config('legal.correo') }}">{{ config('legal.correo') }}</a></p>

<h2>2. Datos que se tratan</h2>

<h3>Del personal del hospital (usuarios de la plataforma)</h3>
<ul>
    <li>Identificación y contacto: nombre, correo institucional y rol (médico, enfermería, recepción, administración, farmacia).</li>
    <li>Credenciales: contraseña y, en el caso del rol de médico, un PIN personal. Ambos se guardan cifrados; nadie, ni el personal de {{ config('legal.empresa') }}, puede leerlos.</li>
    <li>Datos del dispositivo: un identificador cifrado del equipo, sistema operativo, modelo, dirección IP, y fecha y hora de cada acceso.</li>
    <li>Registros de actividad: accesos permitidos o denegados y consultas a información sensible, para fines de seguridad y auditoría.</li>
</ul>

<h3>De los pacientes</h3>
<ul>
    <li>Identificación: nombre, fecha de nacimiento, sexo, estado civil, nacionalidad, lugar de nacimiento, CURP o pasaporte y ocupación.</li>
    <li>Contacto: domicilio, teléfono, correo electrónico y nombre de una persona responsable.</li>
    <li>Ubicación del lugar de atención, solo cuando se trata de un paciente atendido fuera del hospital.</li>
    <li>Información administrativa: citas, cama asignada, servicio y cuentas por cobrar.</li>
</ul>

<div class="nota">
    <strong>Datos personales sensibles.</strong> HealthNexus trata datos de salud: tipo sanguíneo, alergias, enfermedades crónicas, nivel de triage, signos vitales, consultas, diagnósticos, expediente clínico, notas de enfermería y medicamentos. Estos datos se consideran sensibles y requieren el consentimiento expreso de su titular, salvo en los casos de excepción que marca la ley, como la atención médica de urgencia o el cumplimiento de obligaciones legales. El hospital es quien recaba ese consentimiento.
</div>

<h2>3. Para qué se usan</h2>
<h3>Finalidades necesarias para el servicio</h3>
<ul>
    <li>Prestar atención médica y dar seguimiento a cada paciente.</li>
    <li>Integrar y resguardar el expediente clínico conforme a la NOM-004-SSA3-2012.</li>
    <li>Administrar citas, camas, servicios, medicamentos y cobranza del hospital.</li>
    <li>Identificar al personal, controlar su acceso según su rol y verificar que use dispositivos autorizados.</li>
    <li>Proteger la información: detectar accesos indebidos, bloquear dispositivos perdidos y mantener registros de auditoría.</li>
    <li>Cumplir obligaciones legales y atender requerimientos de autoridades competentes.</li>
</ul>
<h3>Finalidades adicionales</h3>
<p>HealthNexus <strong>no</strong> usa tus datos para publicidad, mercadotecnia ni para elaborar perfiles comerciales, y no los vende.</p>

<h2>4. Con quién se comparten</h2>
<ul>
    <li><strong>Proveedores de infraestructura.</strong> Los datos se alojan en servidores y bases de datos de Amazon Web Services (AWS). Este proveedor actúa por cuenta de {{ config('legal.empresa') }} y del hospital, y no puede usar los datos para fines propios.</li>
    <li><strong>Autoridades sanitarias, judiciales o administrativas</strong>, solo cuando la ley lo exija o lo ordenen en un procedimiento.</li>
    <li><strong>Otras instituciones de salud</strong>, solo cuando sea necesario para la atención, el diagnóstico o la referencia de un paciente y lo autorice el hospital.</li>
</ul>
<p>Fuera de estos casos, tus datos no se transfieren a terceros sin tu consentimiento.</p>

<h2>5. Cómo se protegen</h2>
<ul>
    <li>Toda la comunicación viaja cifrada por HTTPS.</li>
    <li>Contraseñas y PIN cifrados; sesiones con vencimiento.</li>
    <li>Acceso por roles: cada usuario ve solo lo que corresponde a su puesto.</li>
    <li>Registro de dispositivos, bloqueo remoto y límite de intentos de acceso.</li>
    <li>Bitácora de auditoría de accesos y consultas a información sensible.</li>
    <li>En la app móvil, la sesión se guarda en el almacenamiento seguro del teléfono.</li>
</ul>

<h2>6. Cuánto tiempo se conservan</h2>
<p>El expediente clínico se conserva por al menos cinco años contados a partir del último acto médico, como lo marca la NOM-004-SSA3-2012. Los demás datos se conservan mientras exista la relación con el hospital y durante los plazos que exijan las leyes aplicables; después se bloquean y se eliminan de forma segura.</p>

<h2>7. Tus derechos (ARCO) y la revocación del consentimiento</h2>
<p>Tienes derecho a <strong>Acceder</strong> a tus datos, <strong>Rectificarlos</strong> si son inexactos, pedir su <strong>Cancelación</strong> u <strong>Oponerte</strong> a su uso para fines específicos. También puedes revocar tu consentimiento o pedir que se limite el uso de tus datos.</p>
<p>Para hacerlo, envía tu solicitud al hospital que te atiende o al correo <a href="mailto:{{ config('legal.correo') }}">{{ config('legal.correo') }}</a> con:</p>
<ul>
    <li>Tu nombre y un medio para responderte.</li>
    <li>Un documento que acredite tu identidad o, en su caso, la de tu representante.</li>
    <li>La descripción clara de los datos y del derecho que quieres ejercer.</li>
</ul>
<p>Te responderemos en un plazo máximo de 20 días hábiles. Si la solicitud procede, se hará efectiva dentro de los 15 días hábiles siguientes.</p>
<p>Ten en cuenta que algunos datos del expediente clínico no se pueden cancelar mientras exista la obligación legal de conservarlos.</p>

<h2>8. Cookies</h2>
<p>El sitio web usa únicamente cookies técnicas necesarias para mantener tu sesión iniciada y proteger los formularios contra solicitudes falsificadas. No se usan cookies de publicidad ni de rastreo de terceros.</p>

<h2>9. Cambios a este aviso</h2>
<p>Este aviso puede actualizarse por cambios en la ley, en el servicio o en las prácticas de privacidad. La versión vigente siempre estará publicada en esta página, con su fecha de actualización.</p>

<h2>10. Autoridad</h2>
<p>Si consideras que tu derecho a la protección de datos personales ha sido vulnerado, puedes acudir a la autoridad competente en materia de protección de datos personales en posesión de los particulares.</p>
@endsection
