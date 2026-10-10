
<?php

use App\Models\Cita;
use App\Models\Consulta;
use App\Models\Diagnostico;
use App\Models\Medicamento;
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

class RecetaPdfTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $medico;
    protected User $otroMedico;
    protected Paciente $paciente;
    protected Consulta $consulta;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->seed(PermissionSeeder::class);
        $this->seed(RoleSeeder::class);
        $this->seed(RolePermissionSeeder::class);

        $this->admin = $this->crearUsuario('administrador');
        $this->medico = $this->crearUsuario('medico');
        $this->otroMedico = $this->crearUsuario('medico');

        $this->paciente = $this->crearPaciente();
        $this->crearSignoVital($this->paciente);

        $this->consulta = $this->crearConsultaCompleta();
    }

    /* =========================================================
     | HELPERS
     ========================================================= */

    private function crearUsuario(string $rol): User
    {
        $user = User::create([
            'name' => 'User ' . Str::random(6),
            'email' => 'user' . Str::random(8) . '@test.com',
            'password' => bcrypt('password'),
            'nombre' => 'Juan',
            'apellido_paterno' => 'Pérez',
            'apellido_materno' => 'Gómez',
            'activo' => true,
        ]);

        $user->assignRole($rol);

        return $user;
    }

    private function crearPaciente(array $extra = []): Paciente
    {
        return Paciente::create(array_merge([
            'nombre' => 'María',
            'apellido_paterno' => 'García',
            'apellido_materno' => 'López',
            'fecha_nacimiento' => '1985-05-20',
            'sexo' => 'mujer',
            'estado_civil' => 'Casado',
            'nacionalidad' => 'MEXICANA',
            'estado_nacimiento' => 'Jalisco',
            'curp' => strtoupper(Str::random(18)),
            'telefono_principal' => '3312345678',
            'correo_electronico' => 'paciente' . Str::random(5) . '@test.com',
            'ocupacion' => 'Empleado',
            'responsable_nombre' => 'Familiar',
            'tipo_sanguineo' => 'O+',
            'alergias' => 'Ninguna',
            'enfermedades_cronicas' => 'Ninguna',
            'colonia' => 'Centro',
            'calle' => 'Av. Juárez',
            'numero_exterior' => '123',
            'numero_interior' => 'A',
            'activo' => true,
        ], $extra));
    }

    private function crearSignoVital(Paciente $paciente): SignoVital
    {
        return SignoVital::create([
            'paciente_id' => $paciente->id,
            'user_id' => $this->medico->id,
            'temperatura' => 36.5,
            'frecuencia_cardiaca' => 80,
            'frecuencia_respiratoria' => 18,
            'presion_arterial' => '120/80',
            'saturacion_oxigeno' => 98,
            'triage' => 'verde',
        ]);
    }

    private function crearConsultaCompleta(): Consulta
    {
        // Crear cita atendida.
        $cita = Cita::create([
            'paciente_id' => $this->paciente->id,
            'medico_id' => $this->medico->id,
            'creado_por' => $this->admin->id,
            'fecha_hora' => now()->subHour(),
            'duracion_minutos' => 30,
            'estado' => 'atendida',
            'triage_al_momento' => 'verde',
            'motivo' => 'Consulta general',
        ]);

        // Crear diagnóstico.
        $diagnostico = Diagnostico::create([
            'codigo' => 'G44.2',
            'nombre' => 'Cefalea tensional',
            'activo' => true,
        ]);

        // Crear consulta finalizada.
        $consulta = Consulta::create([
            'cita_id' => $cita->id,
            'paciente_id' => $this->paciente->id,
            'medico_id' => $this->medico->id,
            'subjetivo' => 'Dolor de cabeza',
            'objetivo' => 'TA 120/80',
            'analisis' => 'Cefalea tensional',
            'plan' => 'Paracetamol cada 8h',
            'receta_libre' => 'Paracetamol 500mg cada 8 horas por 3 días.',
            'diagnostico_principal_id' => $diagnostico->id,
            'estado' => 'finalizada',
            'finalizada_en' => now(),
        ]);

        // Crear medicamento.
        $medicamento = Medicamento::create([
            'nombre' => 'Paracetamol',
            'presentacion' => 'Tableta',
            'concentracion' => '500mg',
            'activo' => true,
            'stock_actual' => 50,
            'stock_minimo' => 5,
        ]);

        // Asociar medicamento a la consulta.
        $consulta->medicamentos()->attach($medicamento->id, [
            'dosis' => '500mg',
            'via' => 'oral',
            'frecuencia' => 'cada 8 horas',
            'duracion' => '3 días',
            'indicaciones' => 'Después de alimentos',
        ]);

        return $consulta->fresh();
    }

    /* =========================================================
     | PDF DE CONSULTA
     ========================================================= */

    public function test_medico_puede_generar_pdf_de_su_consulta(): void
    {
        $response = $this->actingAs($this->medico)
            ->get(route('consultas.pdf', $this->consulta));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_pdf_de_consulta_tiene_nombre_correcto(): void
    {
        $response = $this->actingAs($this->medico)
            ->get(route('consultas.pdf', $this->consulta));

        $disposition = $response->headers->get('Content-Disposition');

        $this->assertNotNull($disposition);

        $this->assertStringContainsString(
            'consulta-' . $this->consulta->id . '.pdf',
            $disposition
        );
    }

    public function test_admin_puede_generar_cualquier_pdf_de_consulta(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('consultas.pdf', $this->consulta));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_medico_no_puede_generar_pdf_de_consulta_ajena(): void
    {
        $this->actingAs($this->otroMedico)
            ->get(route('consultas.pdf', $this->consulta))
            ->assertForbidden();
    }

    public function test_invitado_no_puede_generar_pdf_de_consulta(): void
    {
        $this->get(route('consultas.pdf', $this->consulta))
            ->assertRedirect(route('login'));
    }

    /* =========================================================
     | PDF DE RECETA
     ========================================================= */

    public function test_medico_puede_generar_pdf_de_receta(): void
    {
        $response = $this->actingAs($this->medico)
            ->get(route('consultas.receta.pdf', $this->consulta));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_receta_pdf_tiene_nombre_correcto(): void
    {
        $response = $this->actingAs($this->medico)
            ->get(route('consultas.receta.pdf', $this->consulta));

        $disposition = $response->headers->get('Content-Disposition');

        $this->assertNotNull($disposition);

        $this->assertStringContainsString(
            'receta-' . $this->consulta->id . '.pdf',
            $disposition
        );
    }

    public function test_admin_puede_generar_cualquier_receta_pdf(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('consultas.receta.pdf', $this->consulta));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_medico_no_puede_generar_receta_de_consulta_ajena(): void
    {
        $this->actingAs($this->otroMedico)
            ->get(route('consultas.receta.pdf', $this->consulta))
            ->assertForbidden();
    }

    public function test_invitado_no_puede_generar_receta_pdf(): void
    {
        $this->get(route('consultas.receta.pdf', $this->consulta))
            ->assertRedirect(route('login'));
    }

    public function test_no_se_puede_generar_receta_sin_medicamentos_ni_receta_libre(): void
    {
        // Crear una consulta sin medicamentos ni receta libre.
        $consultaVacia = Consulta::create([
            'cita_id' => $this->consulta->cita_id,
            'paciente_id' => $this->paciente->id,
            'medico_id' => $this->medico->id,
            'subjetivo' => 'X',
            'objetivo' => 'X',
            'analisis' => 'X',
            'plan' => 'X',
            'receta_libre' => null,
            'estado' => 'finalizada',
            'finalizada_en' => now(),
        ]);

        $response = $this->actingAs($this->medico)
            ->get(route('consultas.receta.pdf', $consultaVacia));

        $this->assertTrue(
            $response->isForbidden()
                || $response->isRedirect()
                || $response->isServerError(),
            'Se esperaba un error al generar receta sin medicamentos.'
        );
    }
}
