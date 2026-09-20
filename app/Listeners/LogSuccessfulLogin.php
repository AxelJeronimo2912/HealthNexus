<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\LoginLog; // <-- Importa tu modelo

class LogSuccessfulLogin
{
    public function handle(Login $event): void
    {
        // Guardar el registro de login exitoso
        LoginLog::create([
            'email' => $event->user->email,
            'ip_address' => request()->ip(),
            'success' => true,
            'motivo' => 'Inicio de sesión exitoso',
        ]);
    }
}