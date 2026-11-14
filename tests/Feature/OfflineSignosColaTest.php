<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

/**
 * Modo sin conexión, fase 1: las pantallas con sesión preparan la cola del navegador (public/js/hn-offline.js).
 */
class OfflineSignosColaTest extends TestCase
{
    use RefreshDatabase;

    private User $enfermera;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $this->seed(PermissionSeeder::class);
        $this->seed(RoleSeeder::class);
        $this->seed(RolePermissionSeeder::class);

        $this->enfermera = User::create([
            'name' => 'U '.Str::random(5), 'email' => Str::random(8).'@test.com', 'password' => bcrypt('password'),
            'nombre' => 'Ana', 'apellido_paterno' => 'Ruiz', 'activo' => true,
        ]);
        $this->enfermera->assignRole('enfermeria');
    }

    public function test_las_pantallas_con_sesion_preparan_la_cola_y_el_formulario_se_puede_guardar_sin_conexion(): void
    {
        $this->withoutVite();

        $this->actingAs($this->enfermera)->get(route('signos-vitales.create'))
            ->assertOk()
            ->assertSee('<meta name="hn-usuario" content="'.$this->enfermera->id.'">', false)
            ->assertSee('data-hn-offline="signos"', false)
            ->assertSee('HNOffline.activar();', false);

        $sw = file_get_contents(public_path('sw.js'));
        $this->assertStringContainsString("'/offline.html'", $sw, 'La página sin conexión sigue precargada');
        $js = file_get_contents(public_path('js/hn-offline.js'));
        $this->assertStringContainsString("var COLA_DB = 'hn-cola';", $js, 'La cola vive aparte: borrar() no la elimina');
    }

    public function test_sin_sesion_no_se_publica_ninguna_cuenta(): void
    {
        $this->withoutVite();

        $this->get(route('login'))->assertOk()->assertDontSee('name="hn-usuario"', false);
    }
}
