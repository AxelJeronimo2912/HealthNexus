<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * El registro público está cerrado: las cuentas del personal las crea el administrador.
 */
class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_pantalla_de_registro_no_existe(): void
    {
        $this->get('/register')->assertNotFound();
    }

    public function test_nadie_puede_crear_una_cuenta_desde_afuera(): void
    {
        $this->post('/register', [
            'name' => 'Persona Externa',
            'email' => 'externa@correo.test',
            'password' => 'ClaveSegura#2026',
            'password_confirmation' => 'ClaveSegura#2026',
        ])->assertNotFound();

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'externa@correo.test']);
    }
}
