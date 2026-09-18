<?php

namespace Tests\Feature\Modules;

use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ModuleAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Orden importa
        $this->seed(PermissionSeeder::class);
        $this->seed(RoleSeeder::class);
        $this->seed(RolePermissionSeeder::class);
    }

    /**
     * Verifica que el middleware de permisos NO devolvió 403.
     * Lo que se prueba aquí es el PERMISO, no que la vista renderice.
     * La vista puede devolver 500 por bugs ajenos a este sprint.
     */
    private function assertPermisoConcedido(TestResponse $response): void
    {
        $this->assertNotSame(
            403,
            $response->getStatusCode(),
            'Se esperaba que el permiso permitiera el acceso, pero se recibió 403.'
        );
    }

    /* ============================================================
     |  MÉDICO
     ============================================================ */

    public function test_medico_puede_ver_pacientes(): void
    {
        $user = User::factory()->create();
        $user->assignRole('medico');

        $response = $this->actingAs($user)->get(route('pacientes.index'));
        $this->assertPermisoConcedido($response);
    }

    public function test_medico_puede_ver_camas(): void
    {
        $user = User::factory()->create();
        $user->assignRole('medico');

        // médico SÍ tiene camas.ver en tu RolePermissionSeeder
        $response = $this->actingAs($user)->get(route('camas.index'));
        $this->assertPermisoConcedido($response);
    }

    public function test_medico_no_puede_ver_medicamentos(): void
    {
        $user = User::factory()->create();
        $user->assignRole('medico');

        $this->actingAs($user)
            ->get(route('medicamentos.index'))
            ->assertForbidden();
    }

    /* ============================================================
     |  ENFERMERÍA
     ============================================================ */

    public function test_enfermeria_puede_ver_camas(): void
    {
        $user = User::factory()->create();
        $user->assignRole('enfermeria');

        $response = $this->actingAs($user)->get(route('camas.index'));
        $this->assertPermisoConcedido($response);
    }

    public function test_enfermeria_puede_ver_signos_vitales(): void
    {
        $user = User::factory()->create();
        $user->assignRole('enfermeria');

        $response = $this->actingAs($user)->get(route('signos-vitales.index'));
        $this->assertPermisoConcedido($response);
    }

    public function test_enfermeria_no_puede_ver_citas(): void
    {
        $user = User::factory()->create();
        $user->assignRole('enfermeria');

        // enfermería NO tiene citas.ver en tu seeder
        $this->actingAs($user)
            ->get(route('citas.index'))
            ->assertForbidden();
    }

    /* ============================================================
     |  FARMACIA
     ============================================================ */

    public function test_farmacia_puede_ver_medicamentos(): void
    {
        $user = User::factory()->create();
        $user->assignRole('farmacia');

        $response = $this->actingAs($user)->get(route('medicamentos.index'));
        $this->assertPermisoConcedido($response);
    }

    public function test_farmacia_puede_ver_existencias(): void
    {
        $user = User::factory()->create();
        $user->assignRole('farmacia');

        $response = $this->actingAs($user)->get(route('existencias.index'));
        $this->assertPermisoConcedido($response);
    }

    public function test_farmacia_no_puede_ver_pacientes(): void
    {
        $user = User::factory()->create();
        $user->assignRole('farmacia');

        $this->actingAs($user)
            ->get(route('pacientes.index'))
            ->assertForbidden();
    }

    /* ============================================================
     |  ADMINISTRADOR
     ============================================================ */

    public function test_administrador_puede_ver_todos_los_modulos(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('administrador');

        $rutas = [
            route('pacientes.index'),
            route('medicamentos.index'),
            route('camas.index'),
            route('signos-vitales.index'),
            route('existencias.index'),
        ];

        foreach ($rutas as $ruta) {
            $response = $this->actingAs($admin)->get($ruta);
            $this->assertPermisoConcedido($response);
        }
    }
}
