<?php

namespace App\Listeners;

use App\Services\AuditoriaService;
use Illuminate\Auth\Events\Login;

class RegistrarLoginListener
{
    public function handle(Login $event): void
    {
        AuditoriaService::login(true);
    }
}