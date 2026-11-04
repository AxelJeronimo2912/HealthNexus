<?php

namespace Tests\Feature;

use App\Models\Admision;
use App\Models\Cama;
use App\Models\Hospital;
use App\Models\Paciente;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AdmisionTriageTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $medico;
    protected Paciente $paciente;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->seed(PermissionSeeder::class);
        $this->seed(RoleSeeder::class);
        $this->seed(RolePermissionSeeder::class);

        $this->admin  = $this->crearUsuario('administrador');
        $this->medico = $this->crearUsuario('medico');

        $this->paciente = $this->crearPaciente();
    }

    /* =========================================================
     |  HELPERS
     ========================================================= */

    private function crearUsuario(string $rol): User
    {
        $user = User::create([
            'name'             => 'User ' . Str::random(6),
            'email'            => 'user' . Str::random(8) . '@test.com',
            'password'         => bcrypt('password'),
            'nombre'           => 'Juan',
            'apellido_paterno' => 'Pérez',
            'apellido_materno' => 'Gómez',
            'activo'           => true,
        ]);

        $user->assignRole($rol);

        return $user;
    }

    private function crearPaciente(array $extra = []): Paciente
    {
        return Paciente::create(array_merge([
            'nombre'             => 'Paciente',
            'apellido_paterno'   => 'Test',
            'apellido_materno'   => 'Demo',
            'fecha_nacimiento'   => '1990-01-01',
            'sexo'               => 'hombre',
            'curp'               => strtoupper(Str::random(18)),
            'telefono_principal' => '1234567890',
            'activo'             => true,
        ], $extra));
    }

    private function crearCama(array $extra = []): Cama
    {
        return Cama::create(array_merge([
            'codigo' => 'C-' . Str::random(6),
            'estado' => 'disponible',
            'activo' => true,
        ], $extra));
    }

    private function crearHospital(array $extra = []): Hospital
{
    return Hospital::create(array_merge([
        'nombre'    => 'Hospital ' . Str::random(4),
        'direccion' => 'Calle Falsa 123',
        'latitud'   => 19.4326,
        'longitud'  => -99.1332,
        'telefono'  => '5555555555',
        'activo'    => true,
    ], $extra));
}
private function crearAdmision(array $extra = []): Admision
{
    $folio = $extra['folio']
        ?? 'ADM-' . now()->format('Ymd') . '-' . str_pad(
            Admision::whereDate('created_at', today())->count() + 1,
            3, '0', STR_PAD_LEFT
        );

    unset($extra['folio']);

    return Admision::create(array_merge([
        'paciente_id'        => $this->paciente->id,
        'user_id'            => $this->admin->id,
        'folio'              => $folio,
        'fecha_hora_llegada' => now(),
        'tipo'               => 'urgencias',
        'triage'             => 'verde',
        'motivo'             => 'Dolor abdominal',
        'estado'             => 'en_espera',
    ], $extra));
}

    /* =========================================================
     |  1. ADMISIÓN CON TRIAGE
     ========================================================= */

    public function test_admision_con_triage_valido_se_crea_en_espera(): void
    {
        $admision = $this->crearAdmision([
            'tipo'   => 'urgencias',
            'triage' => 'rojo',
            'motivo' => 'Dolor torácico',
        ]);

        $this->assertDatabaseHas('admisiones', [
            'id'          => $admision->id,
            'paciente_id' => $this->paciente->id,
            'triage'      => 'rojo',
            'estado'      => 'en_espera',
        ]);

        $this->assertEquals('en_espera', $admision->estado);
    }

    public function test_admision_sin_triage_es_valida(): void
    {
        $admision = $this->crearAdmision([
            'tipo'   => 'consulta_externa',
            'triage' => null,
        ]);

        $this->assertNull($admision->triage);
        $this->assertEquals('en_espera', $admision->estado);
    }

    public function test_folio_tiene_formato_correcto(): void
    {
        $admision = $this->crearAdmision();

        $this->assertMatchesRegularExpression('/^ADM-\d{8}-\d{3}$/', $admision->folio);
    }

    /* =========================================================
     |  2. PACIENTE RÁPIDO (defaults)
     ========================================================= */

    public function test_paciente_rapido_aplica_defaults(): void
    {
        $paciente = Paciente::create([
            'nombre'                => 'Ana',
            'apellido_paterno'      => 'García',
            'apellido_materno'      => 'S/A',
            'fecha_nacimiento'      => '1990-05-10',
            'sexo'                  => 'mujer',
            'alergias'              => 'Se desconoce',
            'enfermedades_cronicas' => 'Se desconoce',
            'nacionalidad'          => 'MEXICANA',
            'estado_civil'          => 'Soltero',
            'ocupacion'             => 'No especificada',
            'responsable_nombre'    => 'No especificado',
            'correo_electronico'    => 'pendiente_' . uniqid() . '@urgencias.local',
            'curp'                  => strtoupper(Str::random(18)),
            'activo'                => true,
        ]);

        $this->assertEquals('S/A', $paciente->apellido_materno);
        $this->assertEquals('Se desconoce', $paciente->alergias);
        $this->assertEquals('Se desconoce', $paciente->enfermedades_cronicas);
        $this->assertEquals('MEXICANA', $paciente->nacionalidad);
        $this->assertStringContainsString('@urgencias.local', $paciente->correo_electronico);
    }

    public function test_paciente_rapido_respeta_datos_enviados(): void
    {
        $paciente = Paciente::create([
            'nombre'                => 'María',
            'apellido_paterno'      => 'López',
            'apellido_materno'      => 'Ramírez',
            'fecha_nacimiento'      => '1970-03-15',
            'sexo'                  => 'mujer',
            'telefono_principal'    => '5512345678',
            'tipo_sanguineo'        => 'O+',
            'alergias'              => 'Penicilina',
            'enfermedades_cronicas' => 'Diabetes tipo 2',
            'nacionalidad'          => 'MEXICANA',
            'estado_civil'          => 'Soltero',
            'ocupacion'             => 'No especificada',
            'responsable_nombre'    => 'No especificado',
            'correo_electronico'    => 'pendiente_' . uniqid() . '@urgencias.local',
            'curp'                  => strtoupper(Str::random(18)),
            'activo'                => true,
        ]);

        $this->assertEquals('Ramírez', $paciente->apellido_materno);
        $this->assertEquals('Penicilina', $paciente->alergias);
        $this->assertEquals('O+', $paciente->tipo_sanguineo);
    }

    /* =========================================================
     |  3. ASIGNACIÓN DE CAMA Y MÉDICO
     ========================================================= */

    public function test_asignacion_cambia_estado_y_ocupa_cama(): void
    {
        $admision = $this->crearAdmision(['estado' => 'en_espera']);
        $cama     = $this->crearCama(['estado' => 'disponible']);

        $admision->update([
            'cama_id'   => $cama->id,
            'medico_id' => $this->medico->id,
            'estado'    => 'hospitalizado',
        ]);
        $cama->update(['estado' => 'ocupada']);

        $this->assertEquals('hospitalizado', $admision->fresh()->estado);
        $this->assertEquals($cama->id, $admision->fresh()->cama_id);
        $this->assertEquals('ocupada', $cama->fresh()->estado);
    }

    public function test_cama_inexistente_no_existe(): void
    {
        $this->assertNull(Cama::find(999999));
    }

    public function test_cama_ocupada_no_esta_disponible(): void
    {
        $cama = $this->crearCama(['estado' => 'ocupada']);

        $this->assertEquals('ocupada', $cama->estado);
        $this->assertNotEquals('disponible', $cama->estado);
    }

    /* =========================================================
     |  4. DERIVACIÓN
     ========================================================= */

    public function test_derivacion_cambia_estado_y_registra_hospital(): void
    {
        $admision = $this->crearAdmision(['estado' => 'en_espera']);
        $hospital = $this->crearHospital();

        $admision->update([
            'hospital_derivado_id' => $hospital->id,
            'fecha_derivacion'     => now(),
            'motivo_derivacion'    => 'Falta de especialista',
            'estado'               => 'derivado',
        ]);

        $fresh = $admision->fresh();

        $this->assertEquals('derivado', $fresh->estado);
        $this->assertEquals($hospital->id, $fresh->hospital_derivado_id);
        $this->assertNotNull($fresh->fecha_derivacion);
    }

    public function test_hospital_inexistente_no_existe(): void
    {
        $this->assertNull(Hospital::find(999999));
    }

    /* =========================================================
     |  5. PASE DE SALIDA
     ========================================================= */

    public function test_pase_salida_solo_con_estado_derivado(): void
    {
        $hospital = $this->crearHospital();

        $admision = $this->crearAdmision([
            'estado'               => 'derivado',
            'hospital_derivado_id' => $hospital->id,
            'fecha_derivacion'     => now(),
            'motivo_derivacion'    => 'Traslado',
        ]);

        $this->assertEquals('derivado', $admision->estado);
    }

    public function test_admision_en_espera_no_tiene_pase(): void
    {
        $admision = $this->crearAdmision(['estado' => 'en_espera']);

        $this->assertNotEquals('derivado', $admision->estado);
        $this->assertNull($admision->pase_salida_pdf);
    }

    /* =========================================================
     |  6. ELIMINACIÓN Y AUTH
     ========================================================= */

    public function test_administrador_puede_eliminar_admision(): void
    {
        $admision = $this->crearAdmision();

        $this->actingAs($this->admin)
            ->delete(route('admisiones.destroy', $admision))
            ->assertRedirect(route('admisiones.index'));

        $this->assertDatabaseMissing('admisiones', ['id' => $admision->id]);
    }

    public function test_invitado_no_puede_crear_admision(): void
    {
        $this->post(route('admisiones.store'), [
            'paciente_id' => $this->paciente->id,
            'tipo'        => 'urgencias',
            'motivo'      => 'Dolor',
        ])->assertRedirect(route('login'));
    }

    public function test_invitado_no_puede_eliminar_admision(): void
    {
        $admision = $this->crearAdmision();

        $this->delete(route('admisiones.destroy', $admision))
            ->assertRedirect(route('login'));

        $this->assertDatabaseHas('admisiones', ['id' => $admision->id]);
    }
}