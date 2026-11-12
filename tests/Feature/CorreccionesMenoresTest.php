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

class CorreccionesMenoresTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $this->seed(PermissionSeeder::class);
        $this->seed(RoleSeeder::class);
        $this->seed(RolePermissionSeeder::class);
        $this->withoutVite();
    }

    private function usuario(string $rol): User
    {
        $user = User::create([
            'name' => 'U '.Str::random(5), 'email' => Str::random(8).'@test.com', 'password' => bcrypt('password'),
            'nombre' => 'Ana', 'apellido_paterno' => 'Ruiz', 'activo' => true,
        ]);
        $user->assignRole($rol);

        return $user;
    }

    public function test_el_contador_de_estudios_del_menu_responde(): void
    {
        $this->actingAs($this->usuario('laboratorio'))
            ->getJson(route('estudios.contador'))
            ->assertOk()
            ->assertExactJson(['total' => 0]);
    }

    public function test_turnos_solo_aparece_en_el_menu_de_quien_puede_abrirlo(): void
    {
        $turnos = route('admin.turnos.index');

        $this->actingAs($this->usuario('medico'))->get(route('profile.edit'))->assertOk()->assertDontSee($turnos, false);
        $this->actingAs($this->usuario('enfermeria'))->get(route('profile.edit'))->assertOk()->assertDontSee($turnos, false);
        $this->actingAs($this->usuario('administrador'))->get(route('profile.edit'))->assertOk()->assertSee($turnos, false);
    }

    public function test_los_mensajes_de_validacion_salen_en_espanol(): void
    {
        app()->setLocale('es');

        $errores = validator(['frecuencia_cardiaca' => 'abc'], ['frecuencia_cardiaca' => 'integer'])->errors();

        $this->assertSame('La frecuencia cardíaca debe ser un número entero.', $errores->first('frecuencia_cardiaca'));
    }
}
