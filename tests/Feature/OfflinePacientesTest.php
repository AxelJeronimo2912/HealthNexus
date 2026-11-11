<?php

namespace Tests\Feature;

use App\Models\Admision;
use App\Models\AuditLog;
use App\Models\Cama;
use App\Models\CamaPaciente;
use App\Models\Paciente;
use App\Models\PacienteMedico;
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
 * HAL-199 / HAL-201: datos mínimos de pacientes para consultar sin conexión.
 */
class OfflinePacientesTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $medico;

    protected User $enfermera;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $this->seed(PermissionSeeder::class);
        $this->seed(RoleSeeder::class);
        $this->seed(RolePermissionSeeder::class);

        $this->admin = $this->crearUsuario('administrador');
        $this->medico = $this->crearUsuario('medico');
        $this->enfermera = $this->crearUsuario('enfermeria');
    }

    private function crearUsuario(string $rol): User
    {
        $user = User::create([
            'name' => 'User '.Str::random(6),
            'email' => 'user'.Str::random(8).'@test.com',
            'password' => bcrypt('password'),
            'nombre' => 'Ana',
            'apellido_paterno' => 'López',
            'activo' => true,
        ]);
        $user->assignRole($rol);

        return $user;
    }

    private function crearPaciente(string $nombre, array $extra = []): Paciente
    {
        return Paciente::create(array_merge([
            'nombre' => $nombre,
            'apellido_paterno' => 'Prueba',
            'fecha_nacimiento' => '1980-05-10',
            'sexo' => 'mujer',
            'curp' => strtoupper(Str::random(18)),
            'telefono_principal' => '5512345678',
            'correo_electronico' => 'paciente@correo.test',
            'calle' => 'Calle Privada 10',
            'tipo_sanguineo' => 'O+',
            'alergias' => 'Penicilina',
            'activo' => true,
        ], $extra));
    }

    private function asignarMedico(Paciente $paciente, User $medico): void
    {
        PacienteMedico::create([
            'paciente_id' => $paciente->id,
            'medico_id' => $medico->id,
            'asignado_por' => $this->admin->id,
            'asignado_en' => now(),
            'activo' => true,
        ]);
    }

    private function hospitalizar(Paciente $paciente, string $codigo, bool $activa = true): void
    {
        $cama = Cama::create(['codigo' => $codigo, 'area' => 'Urgencias', 'estado' => 'ocupada', 'activo' => true]);
        CamaPaciente::create([
            'cama_id' => $cama->id,
            'paciente_id' => $paciente->id,
            'user_id' => $this->admin->id,
            'fecha_ingreso' => now(),
            'activa' => $activa,
        ]);
    }

    private function signos(Paciente $paciente, string $triage): void
    {
        SignoVital::create([
            'paciente_id' => $paciente->id,
            'user_id' => $this->enfermera->id,
            'temperatura' => 38.4,
            'frecuencia_cardiaca' => 112,
            'frecuencia_respiratoria' => 22,
            'presion_arterial' => '100/60',
            'saturacion_oxigeno' => 93,
            'triage' => $triage,
        ]);
    }

    public function test_sin_sesion_redirige_al_login(): void
    {
        $this->get(route('offline.pacientes'))->assertRedirect(route('login'));
    }

    public function test_farmacia_recibe_403_y_queda_en_la_bitacora(): void
    {
        $farmacia = $this->crearUsuario('farmacia');

        $this->actingAs($farmacia)->getJson(route('offline.pacientes'))
            ->assertForbidden()
            ->assertJson(['error' => 'Solo médico y enfermería pueden consultar pacientes sin conexión.']);

        $this->assertDatabaseHas('audit_logs', ['user_id' => $farmacia->id, 'evento' => 'acceso_denegado', 'modulo' => 'pacientes']);
    }

    public function test_el_medico_recibe_solo_sus_pacientes_con_datos_minimos_y_caducidad_de_un_turno(): void
    {
        $this->freezeTime();
        $suyo = $this->crearPaciente('Laura');
        $deOtro = $this->crearPaciente('Pedro');
        $this->asignarMedico($suyo, $this->medico);
        $this->asignarMedico($deOtro, $this->crearUsuario('medico'));
        $this->hospitalizar($suyo, 'C-101');
        $this->signos($suyo, 'naranja');
        Admision::create([
            'paciente_id' => $suyo->id, 'user_id' => $this->admin->id, 'folio' => 'ADM-1', 'fecha_hora_llegada' => now(),
            'tipo' => 'urgencias', 'triage' => 'naranja', 'motivo' => 'Fiebre', 'diagnostico_presuntivo' => 'Neumonía', 'estado' => 'hospitalizado',
        ]);

        $respuesta = $this->actingAs($this->medico)->getJson(route('offline.pacientes'))
            ->assertOk()
            ->assertHeader('Cache-Control', 'no-store, private')
            ->assertJsonPath('rol', 'medico')
            ->assertJsonPath('caduca_en', now()->addHours(12)->toIso8601String())
            ->assertJsonCount(1, 'pacientes')
            ->assertJsonPath('pacientes.0.nombre', 'Laura Prueba')
            ->assertJsonPath('pacientes.0.alergias', 'Penicilina')
            ->assertJsonPath('pacientes.0.tipo_sanguineo', 'O+')
            ->assertJsonPath('pacientes.0.cama', 'C-101 · Urgencias')
            ->assertJsonPath('pacientes.0.triage', 'naranja')
            ->assertJsonPath('pacientes.0.diagnostico', 'Neumonía')
            ->assertJsonPath('pacientes.0.signos.spo2', 93);

        $paciente = $respuesta->json('pacientes.0');
        foreach (['curp', 'telefono_principal', 'correo_electronico', 'calle', 'pasaporte'] as $campo) {
            $this->assertArrayNotHasKey($campo, $paciente, "No debe salir {$campo}");
        }

        $bitacora = AuditLog::where('user_id', $this->medico->id)->where('evento', 'exportacion')->firstOrFail();
        $this->assertSame('Descarga de 1 paciente(s) para consulta sin conexión', $bitacora->descripcion);
    }

    public function test_enfermeria_recibe_los_hospitalizados_ordenados_por_gravedad(): void
    {
        $verde = $this->crearPaciente('Beto');
        $rojo = $this->crearPaciente('Ana');
        $dadoDeAlta = $this->crearPaciente('Carla');
        $inactivo = $this->crearPaciente('Diego', ['activo' => false]);
        $this->hospitalizar($verde, 'C-1');
        $this->hospitalizar($rojo, 'C-2');
        $this->hospitalizar($dadoDeAlta, 'C-3', false);
        $this->hospitalizar($inactivo, 'C-4');
        $this->signos($verde, 'verde');
        $this->signos($rojo, 'rojo');

        $this->actingAs($this->enfermera)->getJson(route('offline.pacientes'))
            ->assertOk()
            ->assertJsonPath('rol', 'enfermeria')
            ->assertJsonCount(2, 'pacientes')
            ->assertJsonPath('pacientes.0.nombre', 'Ana Prueba')
            ->assertJsonPath('pacientes.1.nombre', 'Beto Prueba');
    }

    public function test_la_caducidad_se_configura_en_horas(): void
    {
        $this->freezeTime();
        config(['offline.ttl_horas' => 8]);

        $this->actingAs($this->enfermera)->getJson(route('offline.pacientes'))
            ->assertOk()
            ->assertJsonPath('caduca_en', now()->addHours(8)->toIso8601String());
    }

    public function test_el_service_worker_no_guarda_la_ruta_de_pacientes(): void
    {
        $sw = file_get_contents(public_path('sw.js'));

        preg_match("/const ESTATICOS = \[(.*?)\];/", $sw, $m);
        $this->assertNotEmpty($m, 'sw.js debe declarar ESTATICOS');
        $this->assertStringNotContainsString('/offline/', $m[1], 'Los datos de pacientes no pueden ir a la caché del service worker');
        $this->assertStringContainsString("'/js/hn-offline.js'", $sw, 'El script de consulta sin conexión debe estar precargado');
    }

    public function test_la_pagina_con_sesion_activa_la_sincronizacion_y_la_de_invitado_borra_los_datos(): void
    {
        $this->withoutVite();

        $this->actingAs($this->enfermera)->get(route('profile.edit'))
            ->assertOk()
            ->assertSee('js/hn-offline.js', false)
            ->assertSee('HNOffline.activar();', false);

        auth()->logout();
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('HNOffline.borrar();', false)
            ->assertDontSee('HNOffline.activar();', false);
    }
}
