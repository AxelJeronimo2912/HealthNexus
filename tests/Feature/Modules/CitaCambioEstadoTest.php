<?php

use App\Models\Cita;
use App\Models\Paciente;
use App\Models\Turno;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class CitaCambioEstadoTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $medico;
    protected User $otroMedico;
    protected Paciente $paciente;
    protected Turno $turno;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->seed(PermissionSeeder::class);
        $this->seed(RoleSeeder::class);
        $this->seed(RolePermissionSeeder::class);

        $this->admin      = $this->crearUsuario('administrador');
        $this->medico     = $this->crearUsuario('medico');
        $this->otroMedico = $this->crearUsuario('medico');

        $this->paciente = $this->crearPaciente();
        $this->turno    = $this->crearTurno();
    }

    /* =========================================================
     | HELPERS
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
            'nombre'                => 'María',
            'apellido_paterno'      => 'García',
            'apellido_materno'      => 'López',
            'fecha_nacimiento'      => '1985-05-20',
            'sexo'                  => 'mujer',
            'estado_civil'          => 'Casado',
            'nacionalidad'          => 'MEXICANA',
            'estado_nacimiento'     => 'Jalisco',
            'curp'                  => strtoupper(Str::random(18)),
            'telefono_principal'    => '3312345678',
            'correo_electronico'    => 'paciente' . Str::random(5) . '@test.com',
            'ocupacion'             => 'Empleado',
            'responsable_nombre'    => 'Familiar',
            'tipo_sanguineo'        => 'O+',
            'alergias'              => 'Ninguna',
            'enfermedades_cronicas' => 'Ninguna',
            'colonia'               => 'Centro',
            'calle'                 => 'Av. Juárez',
            'numero_exterior'       => '123',
            'numero_interior'       => 'A',
            'activo'                => true,
        ], $extra));
    }

    private function crearTurno(): Turno
    {
        return Turno::create([
            'nombre'           => 'Matutino',
            'codigo'           => 'MAT',
            'hora_inicio'      => '08:00:00',
            'hora_fin'         => '14:00:00',
            'cruza_medianoche' => false,
            'duracion_horas'   => 6,
            'descripcion'      => 'Turno matutino de prueba',
            'activo'           => true,
        ]);
    }

    private function crearCita(string $estado = 'programada', ?User $medico = null): Cita
    {
        return Cita::create([
            'paciente_id'       => $this->paciente->id,
            'medico_id'         => ($medico ?? $this->medico)->id,
            'turno_id'          => $this->turno->id,
            'creado_por'        => $this->admin->id,
            'fecha_hora'        => now()->addDay()->setTime(10, 0),
            'duracion_minutos'  => 30,
            'estado'            => $estado,
            'triage_al_momento' => 'verde',
            'motivo'            => 'Consulta general',
        ]);
    }

    /* =========================================================
     | TRANSICIONES VÁLIDAS
     ========================================================= */

    public function test_admin_puede_confirmar_cita(): void
    {
        $cita = $this->crearCita('programada');

        $this->actingAs($this->admin)
            ->post(route('citas.cambiar-estado', $cita), ['estado' => 'confirmada'])
            ->assertRedirect();

        $this->assertEquals('confirmada', $cita->fresh()->estado);
    }

    public function test_medico_puede_pasar_su_cita_a_en_curso(): void
    {
        $cita = $this->crearCita('confirmada', $this->medico);

        $this->actingAs($this->medico)
            ->post(route('citas.cambiar-estado', $cita), ['estado' => 'en_curso'])
            ->assertRedirect();

        $this->assertEquals('en_curso', $cita->fresh()->estado);
    }

    public function test_medico_puede_marcar_su_cita_como_atendida(): void
    {
        $cita = $this->crearCita('en_curso', $this->medico);

        $this->actingAs($this->medico)
            ->post(route('citas.cambiar-estado', $cita), ['estado' => 'atendida'])
            ->assertRedirect();

        $this->assertEquals('atendida', $cita->fresh()->estado);
    }

    public function test_admin_puede_cancelar_cita(): void
    {
        $cita = $this->crearCita('confirmada');

        $this->actingAs($this->admin)
            ->post(route('citas.cambiar-estado', $cita), ['estado' => 'cancelada'])
            ->assertRedirect();

        $this->assertEquals('cancelada', $cita->fresh()->estado);
    }

    public function test_admin_puede_marcar_no_asistio(): void
    {
        $cita = $this->crearCita('confirmada');

        $this->actingAs($this->admin)
            ->post(route('citas.cambiar-estado', $cita), ['estado' => 'no_asistio'])
            ->assertRedirect();

        $this->assertEquals('no_asistio', $cita->fresh()->estado);
    }

    /* =========================================================
     | TRANSICIONES INVÁLIDAS / PROTEGIDAS
     ========================================================= */

    public function test_medico_no_puede_cambiar_estado_de_cita_ajena(): void
    {
        $cita = $this->crearCita('confirmada', $this->medico);

        $this->actingAs($this->otroMedico)
            ->post(route('citas.cambiar-estado', $cita), ['estado' => 'en_curso'])
            ->assertForbidden();

        $this->assertEquals('confirmada', $cita->fresh()->estado);
    }

    public function test_no_se_puede_cambiar_a_estado_invalido(): void
    {
        $cita = $this->crearCita('programada');

        $this->actingAs($this->admin)
            ->post(route('citas.cambiar-estado', $cita), ['estado' => 'inventado'])
            ->assertSessionHasErrors('estado');

        $this->assertEquals('programada', $cita->fresh()->estado);
    }

    public function test_no_se_puede_cambiar_estado_sin_enviarlo(): void
    {
        $cita = $this->crearCita('programada');

        $this->actingAs($this->admin)
            ->post(route('citas.cambiar-estado', $cita), [])
            ->assertSessionHasErrors('estado');
    }

    public function test_cambiar_al_mismo_estado_no_modifica_nada(): void
    {
        $cita = $this->crearCita('confirmada');

        $this->actingAs($this->admin)
            ->post(route('citas.cambiar-estado', $cita), ['estado' => 'confirmada'])
            ->assertRedirect()
            ->assertSessionHas('info');

        $this->assertEquals('confirmada', $cita->fresh()->estado);
    }

    public function test_invitado_no_puede_cambiar_estado_de_cita(): void
    {
        $cita = $this->crearCita('programada');

        $this->post(route('citas.cambiar-estado', $cita), ['estado' => 'confirmada'])
            ->assertRedirect(route('login'));

        $this->assertEquals('programada', $cita->fresh()->estado);
    }

    /* =========================================================
     | AUTORIZACIÓN POR ROL (ACCESO AL SHOW)
     ========================================================= */

    public function test_medico_puede_ver_su_propia_cita(): void
    {
        $cita = $this->crearCita('confirmada', $this->medico);

        $this->actingAs($this->medico)
            ->get(route('citas.show', $cita))
            ->assertOk();
    }

    public function test_medico_no_puede_ver_cita_ajena(): void
    {
        $cita = $this->crearCita('confirmada', $this->medico);

        $this->actingAs($this->otroMedico)
            ->get(route('citas.show', $cita))
            ->assertForbidden();
    }
}
