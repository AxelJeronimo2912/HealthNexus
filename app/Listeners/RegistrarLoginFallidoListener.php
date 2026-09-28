<?php

namespace App\Listeners;

use App\Services\AuditoriaService;
use Illuminate\Auth\Events\Failed;

class RegistrarLoginFallidoListener
{
    public function handle(Failed $event): void
    {
        AuditoriaService::login(false, $event->credentials['email'] ?? null);
    }
}