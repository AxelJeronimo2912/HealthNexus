<?php

namespace App\Listeners;

use App\Services\AuditoriaService;
use Illuminate\Auth\Events\Logout;

class RegistrarLogoutListener
{
    public function handle(Logout $event): void
    {
        if ($event->user) {
            AuditoriaService::logout();
        }
    }
}