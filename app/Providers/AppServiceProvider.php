<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event; // <-- 1. Importa la fachada Event
use Illuminate\Auth\Events\Login;     // <-- 2. Importa el evento Login
use App\Listeners\LogSuccessfulLogin; // <-- 3. Importa tu listener

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 4. Registra el evento y el listener aquí
        Event::listen(
            Login::class,
            LogSuccessfulLogin::class,
        );
    }
}