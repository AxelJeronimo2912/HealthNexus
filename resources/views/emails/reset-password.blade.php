<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer contraseña - HealthNexus</title>
</head>

<body
    style="margin: 0; padding: 0; background-color: #f8fafc; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; width: 100% !important;">

    <!-- Contenedor principal con fondo claro -->
    <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#f8fafc" style="padding: 40px 20px;">
        <tr>
            <td align="center">
                <!-- Tarjeta del correo -->
                <table width="100%" max-width="600" border="0" cellpadding="0" cellspacing="0" bgcolor="#ffffff"
                    style="max-width: 600px; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01); border: 1px solid #e2e8f0;">

                    <!-- Encabezado oscuro (estilo panel de marca) -->
                    <tr>
                        <td align="center" bgcolor="#0f172a"
                            style="padding: 40px 30px; background-color: #0f172a; background-image: radial-gradient(circle at top right, rgba(16, 185, 129, 0.2), transparent 40%);">

                            <!-- Logo incrustado en el correo (Recomendado) -->
                            <img src="{{ $message->embed($logo) }}" alt="HealthNexus" width="64" height="64"
                                style="display: block; margin-bottom: 20px;">

                            <h1
                                style="color: #ffffff; font-size: 24px; font-weight: bold; margin: 0; letter-spacing: -0.5px;">
                                HealthNexus</h1>
                            <p style="color: #94a3b8; font-size: 14px; margin-top: 8px;">Plataforma clínica integral</p>
                        </td>
                    </tr>

                    <!-- Cuerpo del mensaje -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <h2
                                style="color: #0f172a; font-size: 20px; font-weight: bold; margin-top: 0; margin-bottom: 16px;">
                                Hola,</h2>

                            <p style="color: #475569; font-size: 16px; line-height: 24px; margin-bottom: 24px;">
                                Recibimos una solicitud para restablecer la contraseña asociada a tu cuenta en
                                <strong>HealthNexus</strong>.
                            </p>

                            <!-- Botón de acción -->
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="padding: 20px 0 30px 0;">
                                        <a href="{{ $url }}"
                                            style="background-color: #0f172a; color: #ffffff; text-decoration: none; font-size: 16px; font-weight: 600; padding: 14px 32px; border-radius: 8px; display: inline-block; mso-padding-alt: 0; text-align: center;">
                                            Restablecer mi contraseña
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p
                                style="color: #475569; font-size: 14px; line-height: 22px; margin-bottom: 24px; background-color: #f1f5f9; padding: 16px; border-radius: 8px; border-left: 4px solid #10b981;">
                                <strong>Nota:</strong> Este enlace expirará en 60 minutos por motivos de seguridad.
                            </p>

                            <p style="color: #475569; font-size: 14px; line-height: 22px; margin-bottom: 0;">
                                Si no solicitaste un cambio de contraseña, puedes ignorar este correo. Tu cuenta
                                permanece segura.
                            </p>
                        </td>
                    </tr>

                    <!-- Pie de página -->
                    <tr>
                        <td bgcolor="#f8fafc"
                            style="padding: 24px 30px; border-top: 1px solid #e2e8f0; text-align: center;">
                            <p style="color: #94a3b8; font-size: 12px; line-height: 18px; margin: 0;">
                                © {{ date('Y') }} HealthNexus. Todos los derechos reservados.
                            </p>
                            <p style="color: #94a3b8; font-size: 12px; line-height: 18px; margin: 8px 0 0 0;">
                                Si el botón no funciona, copia y pega este enlace en tu navegador:<br>
                                <a href="{{ $url }}"
                                    style="color: #10b981; text-decoration: underline; word-break: break-all;">{{ $url }}</a>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
