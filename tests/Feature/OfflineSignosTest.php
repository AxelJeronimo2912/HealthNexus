<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Cama;
use App\Models\CamaPaciente;
use App\Models\Paciente;
use App\Models\SignoVital;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

/**
 * Modo sin conexión, fase 1: signos vitales capturados sin conexión y enviados al reconectar (POST /offline/signos).
 */
class OfflineSignosTest extends TestCase
{
    use RefreshDatabase;

    private User $enfermera;

    private Paciente $paciente;

    private const UUID = '3f2b8c1e-4d5a-4b6c-9e7f-0a1b2c3d4e5f';

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $this->seed(PermissionSeeder::class);
        $this->seed(RoleSeeder::class);
        $this->seed(RolePermissionSeeder::class);

        $this->enfermera = $this->usuario('enfermeria');
        $this->paciente = Paciente::create([
            'nombre' => 'Laura', 'apellido_paterno' => 'Pérez', 'fecha_nacimiento' => '1980-05-10', 'sexo' => 'mujer',
            'curp' => strtoupper(Str::random(18)), 'activo' => true,
        ]);
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

    /**
     * @param  array<string, mixed>  $extra
     * @return array<string, mixed>
     */
    private function cuerpo(array $extra = []): array
    {
        return array_merge([
            'paciente_id' => $this->paciente->id,
            'frecuencia_cardiaca' => 88,
            'temperatura' => 37.1,
            'client_uuid' => self::UUID,
            'capturado_en' => now()->subMinutes(90)->toIso8601String(),
        ], $extra);
    }

    public function test_guarda_el_registro_con_la_hora_real_de_la_toma_y_lo_marca_como_sin_conexion(): void
    {
        $this->freezeTime();
        $capturado = now()->subMinutes(90)->startOfSecond();

        $this->actingAs($this->enfermera)->postJson(route('offline.signos'), $this->cuerpo(['capturado_en' => $capturado->toIso8601String()]))
            ->assertCreated()
            ->assertJsonStructure(['id', 'triage', 'revisar_cama']);

        $registro = SignoVital::sole();
        $this->assertSame(self::UUID, $registro->client_uuid);
        $this->assertTrue($registro->sin_conexion);
        $this->assertSame($this->enfermera->id, $registro->user_id);
        $this->assertTrue($registro->capturado_en->equalTo($capturado));
        $this->assertTrue($registro->created_at->equalTo($capturado), 'La fecha de registro es la de la toma, para ordenar bien');
        $this->assertNotNull($registro->triage, 'El triage se calcula igual que en línea');
        $this->assertSame(1, AuditLog::where('evento', 'created')->where('modulo', 'signos_vitales')
            ->where('descripcion', 'like', '%sin conexión%')->count());
    }

    public function test_si_el_mismo_registro_llega_dos_veces_no_se_duplica(): void
    {
        $primero = $this->actingAs($this->enfermera)->postJson(route('offline.signos'), $this->cuerpo())->assertCreated();

        $this->actingAs($this->enfermera)->postJson(route('offline.signos'), $this->cuerpo())
            ->assertOk()
            ->assertJson(['id' => $primero->json('id'), 'duplicado' => true]);

        $this->assertSame(1, SignoVital::count());
    }

    public function test_un_client_uuid_de_otra_cuenta_responde_409(): void
    {
        $this->actingAs($this->enfermera)->postJson(route('offline.signos'), $this->cuerpo())->assertCreated();

        $this->actingAs($this->usuario('enfermeria'))->postJson(route('offline.signos'), $this->cuerpo(['temperatura' => 39]))
            ->assertConflict();

        $this->assertSame(1, SignoVital::count());
    }

    public function test_aplica_las_mismas_reglas_que_en_linea_y_exige_identificador_y_hora_valida(): void
    {
        $casos = [
            'temperatura' => ['temperatura' => 52],
            'client_uuid' => ['client_uuid' => 'no-es-uuid'],
            'capturado_en' => ['capturado_en' => now()->addHour()->toIso8601String()],
        ];
        foreach ($casos as $campo => $extra) {
            $this->actingAs($this->enfermera)->postJson(route('offline.signos'), $this->cuerpo($extra))
                ->assertUnprocessable()->assertJsonValidationErrors($campo);
        }

        $this->actingAs($this->enfermera)->postJson(route('offline.signos'), $this->cuerpo(['capturado_en' => now()->subHours(73)->toIso8601String()]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['capturado_en' => 'más de 72 horas']);

        $this->actingAs($this->enfermera)->postJson(route('offline.signos'), [
            'paciente_id' => $this->paciente->id, 'client_uuid' => self::UUID, 'capturado_en' => now()->toIso8601String(), 'notas' => 'Solo nota',
        ])->assertUnprocessable()->assertJsonValidationErrors(['temperatura' => 'al menos un signo']);

        $this->assertSame(0, SignoVital::count());
    }

    public function test_sin_permiso_de_signos_vitales_o_sin_sesion_no_se_guarda(): void
    {
        $this->actingAs($this->usuario('farmacia'))->postJson(route('offline.signos'), $this->cuerpo())->assertForbidden();
        auth()->logout();
        $this->postJson(route('offline.signos'), $this->cuerpo())->assertUnauthorized();

        $this->assertSame(0, SignoVital::count());
    }

    public function test_no_libera_la_cama_aunque_el_paciente_salga_estable(): void
    {
        $cama = Cama::create(['codigo' => 'C-1', 'area' => 'Urgencias', 'estado' => 'ocupada', 'activo' => true]);
        CamaPaciente::create(['cama_id' => $cama->id, 'paciente_id' => $this->paciente->id, 'user_id' => $this->enfermera->id, 'fecha_ingreso' => now(), 'activa' => true]);

        $this->actingAs($this->enfermera)->postJson(route('offline.signos'), $this->cuerpo([
            'temperatura' => 36.6, 'frecuencia_cardiaca' => 75, 'saturacion_oxigeno' => 98,
        ]))->assertCreated();

        $this->assertTrue(CamaPaciente::where('paciente_id', $this->paciente->id)->value('activa'));
        $this->assertSame('ocupada', $cama->fresh()->estado);
    }

    public function test_el_ultimo_signo_es_el_de_la_toma_mas_reciente_aunque_el_sin_conexion_llegue_despues(): void
    {
        SignoVital::create(['paciente_id' => $this->paciente->id, 'user_id' => $this->enfermera->id, 'temperatura' => 36.5]);

        $this->actingAs($this->enfermera)->postJson(route('offline.signos'), $this->cuerpo([
            'temperatura' => 39.4, 'capturado_en' => now()->subHours(3)->toIso8601String(),
        ]))->assertCreated();

        $this->assertEquals(36.5, $this->paciente->fresh()->ultimoSignoVital->temperatura);
    }
}
