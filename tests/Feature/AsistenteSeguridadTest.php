<?php

namespace Tests\Feature;

use App\Models\Paciente;
use App\Models\User;
use App\Services\AsistenteTools;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

/**
 * El asistente IA solo entrega lo que el usuario podría ver en sus pantallas.
 */
class AsistenteSeguridadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $this->seed(PermissionSeeder::class);
        $this->seed(RoleSeeder::class);
        $this->seed(RolePermissionSeeder::class);

        Paciente::create([
            'nombre' => 'Laura', 'apellido_paterno' => 'Secreta', 'fecha_nacimiento' => '1980-01-01', 'sexo' => 'mujer',
            'curp' => 'SECL800101MDFRRR09', 'telefono_principal' => '5511112222', 'activo' => true,
        ]);
    }

    private function usuario(?string $rol): User
    {
        $user = User::create([
            'name' => 'U '.Str::random(5), 'email' => Str::random(8).'@test.com', 'password' => bcrypt('password'),
            'nombre' => 'Ana', 'apellido_paterno' => 'Ruiz', 'activo' => true,
        ]);
        if ($rol) {
            $user->assignRole($rol);
        }

        return $user;
    }

    private function nombresDeHerramientas(): array
    {
        return array_map(fn ($t) => $t['function']['name'], AsistenteTools::definiciones());
    }

    public function test_una_cuenta_sin_rol_no_puede_usar_el_asistente(): void
    {
        $sinRol = $this->usuario(null);

        $this->actingAs($sinRol)->get(route('asistente.index'))->assertForbidden();
        $this->actingAs($sinRol)->postJson(route('asistente.enviar'), ['message' => 'lista los pacientes'])->assertForbidden();
    }

    public function test_farmacia_no_recibe_herramientas_de_pacientes_ni_puede_ejecutarlas(): void
    {
        $farmacia = $this->usuario('farmacia');
        $this->actingAs($farmacia);

        $herramientas = $this->nombresDeHerramientas();
        $this->assertContains('medicamentos_stock_bajo', $herramientas);
        $this->assertNotContains('listar_pacientes', $herramientas);
        $this->assertNotContains('listar_personal', $herramientas);

        $this->assertSame(['error' => 'No tienes permiso para consultar esa información.'], AsistenteTools::ejecutar('listar_pacientes'));
        $this->assertDatabaseHas('audit_logs', ['user_id' => $farmacia->id, 'evento' => 'acceso_denegado', 'modulo' => 'asistente']);
    }

    public function test_enfermeria_consulta_pacientes_sin_que_la_curp_llegue_a_la_ia(): void
    {
        $this->actingAs($this->usuario('enfermeria'));

        $resultado = AsistenteTools::ejecutar('listar_pacientes', ['limite' => 5]);

        $this->assertSame('Laura Secreta', $resultado['pacientes'][0]['nombre']);
        $this->assertStringNotContainsString('SECL800101MDFRRR09', json_encode($resultado));
        $this->assertStringNotContainsString('SECL800101MDFRRR09', json_encode(AsistenteTools::ejecutar('buscar_paciente', ['termino' => 'Laura'])));
    }

    public function test_solo_el_administrador_lista_al_personal(): void
    {
        $this->actingAs($this->usuario('medico'));
        $this->assertArrayHasKey('error', AsistenteTools::ejecutar('listar_personal'));

        $this->actingAs($this->usuario('administrador'));
        $this->assertArrayHasKey('personal', AsistenteTools::ejecutar('listar_personal'));
    }

    public function test_si_la_ia_pide_una_herramienta_prohibida_no_se_le_envian_datos(): void
    {
        config(['services.groq.key' => 'clave-de-prueba', 'services.groq.url' => 'https://ia.test/v1']);
        Http::fake([
            'ia.test/*' => Http::sequence()
                ->push(['choices' => [['message' => ['content' => null, 'tool_calls' => [[
                    'id' => 'call_1', 'type' => 'function', 'function' => ['name' => 'listar_pacientes', 'arguments' => '{}'],
                ]]]]]])
                ->push(['choices' => [['message' => ['content' => 'No tienes acceso a esa información.']]]]),
        ]);

        $this->actingAs($this->usuario('farmacia'))
            ->postJson(route('asistente.enviar'), ['message' => 'dame la lista de pacientes'])
            ->assertOk()
            ->assertJsonPath('response', 'No tienes acceso a esa información.');

        Http::assertSent(fn (Request $r) => ! array_key_exists('tools', $r->data())
            || ! in_array('listar_pacientes', array_map(fn ($t) => $t['function']['name'], $r->data()['tools']), true));
        Http::assertNotSent(fn (Request $r) => str_contains($r->body(), 'Laura Secreta'));
    }
}
