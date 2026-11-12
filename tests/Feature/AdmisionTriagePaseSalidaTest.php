<?php

namespace Tests\Feature;

use App\Models\Admision;
use App\Models\Cama;
use App\Models\Hospital;
use App\Models\Paciente;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AdmisionTriagePaseSalidaTest extends TestCase
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
        $folio = $extra['folio'] ?? 'ADM-' . now()->format('Ymd') . '-' . str_pad(
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

    public function test_crear_admision_con_triage_rojo_genera_estado_en_espera(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admisiones.store'), [
            'paciente_id' => $this->paciente->id,
            'tipo'        => 'urgencias',
            'triage'      => 'rojo',
            'motivo'      => 'Dolor torácico',
        ]);

        $admision = Admision::first();

        $response->assertRedirect(route('admisiones.show', $admision));
        $response->assertSessionHas('success');

        $this->assertEquals('en_espera', $admision->estado);
        $this->assertEquals('rojo', $admision->triage);
        $this->assertEquals('urgencias', $admision->tipo);
        $this->assertMatchesRegularExpression(
            '/^ADM-\d{8}-\d{3}$/',
            $admision->folio
        );
    }

    public function test_crear_admision_sin_triage_es_valida(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('admisiones.store'), [
            'paciente_id' => $this->paciente->id,
            'tipo'        => 'consulta_externa',
            'motivo'      => 'Revisión anual',
        ])->assertRedirect();

        $admision = Admision::first();
        $this->assertNull($admision->triage);
        $this->assertEquals('en_espera', $admision->estado);
    }

    public function test_admision_rechaza_triage_invalido(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('admisiones.store'), [
            'paciente_id' => $this->paciente->id,
            'tipo'        => 'urgencias',
            'triage'      => 'morado', // inválido
            'motivo'      => 'X',
        ])->assertSessionHasErrors('triage');

        $this->assertDatabaseCount('admisiones', 0);
    }

    public function test_admision_rechaza_tipo_invalido(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('admisiones.store'), [
            'paciente_id' => $this->paciente->id,
            'tipo'        => 'desconocido',
            'motivo'      => 'X',
        ])->assertSessionHasErrors('tipo');

        $this->assertDatabaseCount('admisiones', 0);
    }

    /* =========================================================
     |  2. ASIGNACIÓN (BASE PARA DERIVACIÓN)
     ========================================================= */

    public function test_asignar_cama_y_medico_marca_hospitalizado(): void
    {
        $this->actingAs($this->admin);

        $admision = $this->crearAdmision();
        $cama     = $this->crearCama(['estado' => 'disponible']);

        $this->post(route('admisiones.asignar', $admision), [
            'cama_id'   => $cama->id,
            'medico_id' => $this->medico->id,
        ])->assertRedirect();

        $admision->refresh();
        $cama->refresh();

        $this->assertEquals('hospitalizado', $admision->estado);
        $this->assertEquals($cama->id, $admision->cama_id);
        $this->assertEquals($this->medico->id, $admision->medico_id);
        $this->assertEquals('ocupada', $cama->estado);
    }

    /* =========================================================
     |  3. DERIVACIÓN
     ========================================================= */

    public function test_derivar_paciente_actualiza_estado_y_hospital(): void
    {
        $this->actingAs($this->admin);

        $admision = $this->crearAdmision(['estado' => 'en_espera']);
        $hospital = $this->crearHospital();

        $this->postJson(route('admisiones.derivar', $admision), [
            'hospital_derivado_id' => $hospital->id,
            'motivo_derivacion'    => 'Falta de especialista',
        ])->assertOk()
          ->assertJsonPath('ok', true)
          ->assertJsonStructure(['ok', 'mensaje', 'pase_url']);

        $admision->refresh();

        $this->assertEquals('derivado', $admision->estado);
        $this->assertEquals($hospital->id, $admision->hospital_derivado_id);
        $this->assertNotNull($admision->fecha_derivacion);
        $this->assertEquals('Falta de especialista', $admision->motivo_derivacion);
    }

    public function test_derivar_requiere_hospital_existente_y_motivo(): void
    {
        $this->actingAs($this->admin);

        $admision = $this->crearAdmision();

        // Hospital inexistente
        $this->postJson(route('admisiones.derivar', $admision), [
            'hospital_derivado_id' => 999999,
            'motivo_derivacion'    => 'X',
        ])->assertStatus(422)
          ->assertJsonValidationErrors('hospital_derivado_id');

        // Sin motivo
        $hospital = $this->crearHospital();
        $this->postJson(route('admisiones.derivar', $admision), [
            'hospital_derivado_id' => $hospital->id,
        ])->assertStatus(422)
          ->assertJsonValidationErrors('motivo_derivacion');
    }

    /* =========================================================
     |  4. PASE DE SALIDA EN PDF
     ========================================================= */

    public function test_pase_salida_genera_pdf_cuando_esta_derivado(): void
    {
        Storage::fake('public');
        Storage::fake('local');

        $this->actingAs($this->admin);

        $hospital = $this->crearHospital();
        $admision = $this->crearAdmision([
            'estado'               => 'derivado',
            'hospital_derivado_id' => $hospital->id,
            'fecha_derivacion'     => now(),
            'motivo_derivacion'    => 'Traslado',
        ]);

        // Evitamos depender de la vista PDF real: mockeamos DomPDF
        $fakePdf = \Mockery::mock(\Barryvdh\DomPDF\PDF::class);
        $fakePdf->shouldReceive('setOption')->andReturnSelf();
        $fakePdf->shouldReceive('output')->andReturn('%PDF-1.4 fake content');
        Pdf::shouldReceive('loadView')->andReturn($fakePdf);

        $response = $this->get(route('admisiones.pase-salida', $admision));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');

        $admision->refresh();
        $this->assertNotNull($admision->pase_salida_pdf);
        $this->assertStringContainsString('pases-salida/pase-', $admision->pase_salida_pdf);

        Storage::disk('local')->assertExists($admision->pase_salida_pdf);
        Storage::disk('public')->assertMissing($admision->pase_salida_pdf);
    }

    public function test_pase_salida_rechaza_si_no_esta_derivado(): void
    {
        $this->actingAs($this->admin);

        $admision = $this->crearAdmision(['estado' => 'en_espera']);

        $response = $this->get(route('admisiones.pase-salida', $admision));

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->assertNull($admision->fresh()->pase_salida_pdf);
    }
}