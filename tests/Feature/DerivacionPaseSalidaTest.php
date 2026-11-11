<?php

namespace Tests\Feature;

use App\Models\Admision;
use App\Models\Cama;
use App\Models\Hospital;
use App\Models\Paciente;
use App\Models\User;
use App\Enums\AuditEvent;
use Barryvdh\DomPDF\Facade\Pdf;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mockery;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class DerivacionPaseSalidaTest extends TestCase
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

        $this->admin    = $this->crearUsuario('administrador');
        $this->medico   = $this->crearUsuario('medico');
        $this->paciente = $this->crearPaciente();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
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
            'triage'             => 'amarillo',
            'motivo'             => 'Dolor abdominal',
            'estado'             => 'en_espera',
        ], $extra));
    }

    private function admisionDerivada(?Hospital $hospital = null): Admision
    {
        $hospital ??= $this->crearHospital();

        return $this->crearAdmision([
            'estado'               => 'derivado',
            'hospital_derivado_id' => $hospital->id,
            'fecha_derivacion'     => now(),
            'motivo_derivacion'    => 'Traslado por falta de especialista',
        ]);
    }

    private function mockPdf(string $contenido = '%PDF-1.4 contenido-falso'): void
    {
        $fakePdf = Mockery::mock(\Barryvdh\DomPDF\PDF::class);
        $fakePdf->shouldReceive('setOption')->andReturnSelf();
        $fakePdf->shouldReceive('output')->andReturn($contenido);

        Pdf::shouldReceive('loadView')->andReturn($fakePdf);
    }

    /* =========================================================
     |  CA-D1: DERIVACIÓN EXITOSA
     ========================================================= */

    public function test_derivar_paciente_actualiza_estado_hospital_fecha_y_motivo(): void
    {
        $this->actingAs($this->admin);

        $admision = $this->crearAdmision(['estado' => 'en_espera']);
        $hospital = $this->crearHospital();

        $response = $this->postJson(route('admisiones.derivar', $admision), [
            'hospital_derivado_id' => $hospital->id,
            'motivo_derivacion'    => 'Requiere UCI',
        ]);

        $response->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonStructure(['ok', 'mensaje', 'pase_url']);

        $admision->refresh();

        $this->assertSame('derivado', $admision->estado);
        $this->assertSame($hospital->id, $admision->hospital_derivado_id);
        $this->assertNotNull($admision->fecha_derivacion);
        $this->assertSame('Requiere UCI', $admision->motivo_derivacion);
    }

    /* =========================================================
     |  CA-D2: DERIVACIÓN POR FORMULARIO (NO JSON)
     ========================================================= */

    public function test_derivar_via_formulario_redirige_al_pase_de_salida(): void
    {
        $this->actingAs($this->admin);

        $admision = $this->crearAdmision();
        $hospital = $this->crearHospital();

        $response = $this->post(route('admisiones.derivar', $admision), [
            'hospital_derivado_id' => $hospital->id,
            'motivo_derivacion'    => 'Traslado',
        ]);

        $response->assertRedirect(route('admisiones.pase-salida', $admision));
        $response->assertSessionHas('success');
        $this->assertSame('derivado', $admision->fresh()->estado);
    }

    /* =========================================================
     |  CA-D3: VALIDACIONES DE ENTRADA
     ========================================================= */

    public function test_derivar_falla_sin_hospital(): void
    {
        $this->actingAs($this->admin);
        $admision = $this->crearAdmision();

        $this->postJson(route('admisiones.derivar', $admision), [
            'motivo_derivacion' => 'X',
        ])->assertStatus(422)
          ->assertJsonValidationErrors('hospital_derivado_id');

        $this->assertSame('en_espera', $admision->fresh()->estado);
    }

    public function test_derivar_falla_con_hospital_inexistente(): void
    {
        $this->actingAs($this->admin);
        $admision = $this->crearAdmision();

        $this->postJson(route('admisiones.derivar', $admision), [
            'hospital_derivado_id' => 999999,
            'motivo_derivacion'    => 'X',
        ])->assertStatus(422)
          ->assertJsonValidationErrors('hospital_derivado_id');
    }

    public function test_derivar_falla_sin_motivo(): void
    {
        $this->actingAs($this->admin);
        $admision = $this->crearAdmision();
        $hospital = $this->crearHospital();

        $this->postJson(route('admisiones.derivar', $admision), [
            'hospital_derivado_id' => $hospital->id,
        ])->assertStatus(422)
          ->assertJsonValidationErrors('motivo_derivacion');

        $this->assertSame('en_espera', $admision->fresh()->estado);
    }

    /* =========================================================
     |  CA-D4: SEGURIDAD / ACCESO
     ========================================================= */

    public function test_invitado_no_puede_derivar(): void
    {
        $admision = $this->crearAdmision();
        $hospital = $this->crearHospital();

        $this->post(route('admisiones.derivar', $admision), [
            'hospital_derivado_id' => $hospital->id,
            'motivo_derivacion'    => 'X',
        ])->assertRedirect(route('login'));

        $this->assertSame('en_espera', $admision->fresh()->estado);
    }

    /* =========================================================
     |  CA-P1: PASE DE SALIDA – CASO FELIZ
     ========================================================= */

    public function test_pase_salida_genera_pdf_y_guarda_archivo(): void
    {
        Storage::fake('public');
        Storage::fake('local');
        $this->actingAs($this->admin);

        $admision = $this->admisionDerivada();
        $this->mockPdf('%PDF-1.4 contenido-falso');

        $response = $this->get(route('admisiones.pase-salida', $admision));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $response->getContent());

        $admision->refresh();
        $esperado = 'pases-salida/pase-' . $admision->folio . '.pdf';

        $this->assertSame($esperado, $admision->pase_salida_pdf);
        Storage::disk('local')->assertExists($esperado);
        Storage::disk('public')->assertMissing($esperado);
    }

    /* =========================================================
     |  CA-P2: PASE DE SALIDA – NOMBRE DE ARCHIVO
     ========================================================= */

    public function test_pase_salida_usa_folio_en_el_nombre_del_archivo(): void
    {
        Storage::fake('public');
        Storage::fake('local');
        $this->actingAs($this->admin);

        $admision = $this->admisionDerivada();
        $this->mockPdf('%PDF-1.4');

        $this->get(route('admisiones.pase-salida', $admision))->assertOk();

        $archivo = $admision->fresh()->pase_salida_pdf;

        $this->assertMatchesRegularExpression(
            '/^pases-salida\/pase-ADM-\d{8}-\d{3}\.pdf$/',
            $archivo
        );
        $this->assertStringContainsString($admision->folio, $archivo);
    }

    /* =========================================================
     |  CA-P3: PASE DE SALIDA – BLOQUEO SI NO ESTÁ DERIVADO
     ========================================================= */

    public function test_pase_salida_rechaza_si_no_esta_derivado(): void
    {
        Storage::fake('public');
        Storage::fake('local');
        $this->actingAs($this->admin);

        $admision = $this->crearAdmision(['estado' => 'en_espera']);

        $response = $this->get(route('admisiones.pase-salida', $admision));

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->assertNull($admision->fresh()->pase_salida_pdf);
        $this->assertCount(0, Storage::disk('public')->allFiles('pases-salida'));
    }

    /* =========================================================
     |  CA-P4: IDEMPOTENCIA
     ========================================================= */

    public function test_pase_salida_puede_generarse_dos_veces_sobrescribiendo_archivo(): void
    {
        Storage::fake('public');
        Storage::fake('local');
        $this->actingAs($this->admin);

        $admision = $this->admisionDerivada();
        $this->mockPdf('%PDF-1.4');

        $this->get(route('admisiones.pase-salida', $admision))->assertOk();
        $this->get(route('admisiones.pase-salida', $admision))->assertOk();

        $archivo = $admision->fresh()->pase_salida_pdf;

        $this->assertNotNull($archivo);
        Storage::disk('local')->assertExists($archivo);
        Storage::disk('public')->assertMissing($archivo);
        // Solo debe existir un archivo para ese folio
        $this->assertCount(1, Storage::disk('local')->files('pases-salida'));
    }
}