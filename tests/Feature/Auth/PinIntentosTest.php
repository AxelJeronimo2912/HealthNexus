<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

/**
 * El PIN del médico admite 5 intentos por cuenta; luego se bloquea 15 minutos y queda en la bitácora.
 */
class PinIntentosTest extends TestCase
{
    use RefreshDatabase;

    private User $medico;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $this->seed(PermissionSeeder::class);
        $this->seed(RoleSeeder::class);
        $this->seed(RolePermissionSeeder::class);

        $this->medico = User::create([
            'name' => 'Medico', 'email' => 'medico@test.com', 'password' => Hash::make('Clave-Segura-1'),
            'nombre' => 'Luis', 'apellido_paterno' => 'Mora', 'activo' => true, 'pin' => Hash::make('4321'),
        ]);
        $this->medico->assignRole('medico');
    }

    private function pasoDeContrasena(): void
    {
        $this->post('/login', ['email' => 'medico@test.com', 'password' => 'Clave-Segura-1'])
            ->assertRedirect(route('login.pin'));
    }

    private function enviarPin(string $pin)
    {
        return $this->from(route('login.pin'))->post(route('login.pin.verify'), ['pin' => $pin]);
    }

    public function test_cada_pin_incorrecto_avisa_los_intentos_restantes_y_queda_en_la_bitacora(): void
    {
        $this->pasoDeContrasena();

        $this->enviarPin('0000')->assertSessionHasErrors(['pin' => 'PIN incorrecto. Te quedan 4 intentos.']);
        $this->assertGuest();
        $this->assertDatabaseHas('audit_logs', [
            'evento' => 'pin_fallido', 'auditable_type' => User::class, 'auditable_id' => $this->medico->id,
        ]);
    }

    public function test_al_quinto_error_se_bloquea_y_ni_el_pin_correcto_entra(): void
    {
        $this->pasoDeContrasena();
        foreach (['0001', '0002', '0003', '0004'] as $pin) {
            $this->enviarPin($pin);
        }

        $this->enviarPin('0005')
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors(['email' => 'Demasiados intentos de PIN. Por seguridad, espera 15 minutos e inicia sesión de nuevo.']);
        $this->assertDatabaseHas('audit_logs', ['evento' => 'actividad_inusual', 'auditable_id' => $this->medico->id]);

        // Volver a escribir la contraseña no da más intentos: el bloqueo es por cuenta.
        $this->pasoDeContrasena();
        $this->enviarPin('4321')->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_el_bloqueo_termina_a_los_15_minutos(): void
    {
        $this->pasoDeContrasena();
        foreach (['0001', '0002', '0003', '0004', '0005'] as $pin) {
            $this->enviarPin($pin);
        }

        $this->travel(16)->minutes();
        $this->pasoDeContrasena();
        $this->enviarPin('4321')->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticatedAs($this->medico);
    }

    public function test_un_pin_correcto_reinicia_el_contador(): void
    {
        $this->pasoDeContrasena();
        $this->enviarPin('0001');
        $this->enviarPin('4321');
        $this->assertAuthenticatedAs($this->medico);

        $this->post('/logout');
        $this->pasoDeContrasena();
        $this->enviarPin('0002')->assertSessionHasErrors(['pin' => 'PIN incorrecto. Te quedan 4 intentos.']);
    }
}
