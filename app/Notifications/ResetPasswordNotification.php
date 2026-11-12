<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPassword
{
   public function toMail($notifiable)
{
    $url = url(config('app.url').route('password.reset', [
        'token' => $this->token,
        'email' => $notifiable->getEmailForPasswordReset(),
    ], false));

    // Ruta física de la imagen en tu proyecto
    $logoPath = public_path('images/logo-mark.png');

    return (new MailMessage)
        ->subject('Restablece tu contraseña en HealthNexus')
        ->view('emails.reset-password', [
            'url' => $url,
            'logo' => $logoPath // Pasamos la ruta al correo
        ])
        ->attach($logoPath, [
            'as' => 'logo-mark.png',
            'mime' => 'image/png',
        ]);
}
}