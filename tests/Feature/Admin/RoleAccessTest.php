<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->seed(PermissionSeeder::class);
        $this->seed(RoleSeeder::class);
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_medico_no_puede_listar_roles(): void
    {
        $user = User::factory()->create();
        $user->assignRole('medico');

        $this->actingAs($user)
            ->get(route('admin.roles.index'))
            ->assertForbidden();
    }

    public function test_medico_no_puede_crear_roles(): void
    {
        $user = User::factory()->create();
        $user->assignRole('medico');

        $this->actingAs($user)
            ->post(route('admin.roles.store'), ['name' => 'hack'])
            ->assertForbidden();

        $this->assertDatabaseMissing('roles', ['name' => 'hack']);
    }

    public function test_enfermeria_no_puede_eliminar_roles(): void
    {
        $user = User::factory()->create();
        $user->assignRole('enfermeria');

        $rol = Role::findByName('farmacia');

        $this->actingAs($user)
            ->delete(route('admin.roles.destroy', $rol->id))
            ->assertForbidden();

        $this->assertDatabaseHas('roles', ['name' => 'farmacia']);
    }

    public function test_administrador_puede_listar_roles(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('administrador');

        $this->actingAs($admin)
            ->get(route('admin.roles.index'))
            ->assertOk()
            ->assertViewHas('roles');
    }

    public function test_administrador_puede_crear_rol_con_permisos(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('administrador');

        $this->actingAs($admin)
            ->post(route('admin.roles.store'), [
                'name'        => 'recepcion',
                'permissions' => ['pacientes.ver'],
            ])
            ->assertRedirect(route('admin.roles.index'));

        $this->assertDatabaseHas('roles', ['name' => 'recepcion']);

        $rol = Role::where('name', 'recepcion')->first();
        $this->assertNotNull($rol);
        $this->assertTrue($rol->hasPermissionTo('pacientes.ver'));
    }

    public function test_administrador_puede_actualizar_rol(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('administrador');

        $rol = Role::create([
            'name' => 'auxiliar',
            'guard_name' => 'web'
        ]);

        $this->actingAs($admin)
            ->put(route('admin.roles.update', $rol->id), [
                'name'        => 'auxiliar2',
                'permissions' => [],
            ])
            ->assertRedirect(route('admin.roles.index'));

        $this->assertDatabaseHas('roles', ['name' => 'auxiliar2']);
    }

    public function test_no_puede_eliminar_rol_base(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('administrador');

        $rol = Role::findByName('medico');

        $this->actingAs($admin)
            ->delete(route('admin.roles.destroy', $rol->id))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('roles', ['name' => 'medico']);
    }

    public function test_no_puede_eliminar_rol_con_usuarios(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('administrador');

        $rol = Role::create([
            'name' => 'temporal',
            'guard_name' => 'web'
        ]);

        $user = User::factory()->create();
        $user->assignRole('temporal');

        $this->actingAs($admin)
            ->delete(route('admin.roles.destroy', $rol->id))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('roles', ['name' => 'temporal']);
    }

    public function test_rechaza_nombre_duplicado(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('administrador');

        $this->actingAs($admin)
            ->post(route('admin.roles.store'), ['name' => 'medico'])
            ->assertSessionHasErrors('name');
    }
}
