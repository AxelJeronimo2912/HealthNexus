<?php

use App\Models\Cita;
use App\Models\Paciente;
use App\Models\SignoVital;
use App\Models\Turno;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class CitaCreacionTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $medico;
    protected Paciente $paciente;
    protected Turno $turno;

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
        $this->turno    = $this->crearTurno();

        $this->asignarTurnoAlMedico($this->medico, $this->turno);
        $this->crearSignoVital($this->paciente);
    }

    /* =========================================================
     | HELPERS
     ========================================================= */

    private function crearUsuario(string $rol): User
    {
        $user = User::create([
            'name'              => 'User ' . Str::random(6),
            'email'             => 'user' . Str::random(8) . '@test.com',
            'password'          => bcrypt('password'),
            'nombre'            => 'Juan',
            'apellido_paterno'  => 'Pérez',
            'apellido_materno'  => 'Gómez',
            'activo'            => true,
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
            'nombre'            => 'Matutino',
            'codigo'            => 'MAT',
            'hora_inicio'       => '08:00:00',
            'hora_fin'          => '14:00:00',
            'cruza_medianoche'  => false,
            'duracion_horas'    => 6,
            'descripcion'       => 'Turno matutino de prueba',
            'activo'            => true,
        ]);
    }

    private function asignarTurnoAlMedico(User $medico, Turno $turno): void
    {
        $medico->turnos()->attach($turno->id, [
            'dia_semana'   => null,
            'fecha_inicio' => now()->subDay()->toDateString(),
            'fecha_fin'    => now()->addMonth()->toDateString(),
            'activo'       => true,
        ]);
    }

    private function crearSignoVital(Paciente $paciente): SignoVital
    {
        return SignoVital::create([
            'paciente_id'             => $paciente->id,
            'user_id'                 => $this->medico->id,
            'temperatura'             => 36.5,
            'frecuencia_cardiaca'     => 80,
            'frecuencia_respiratoria' => 18,
            'presion_arterial'        => '120/80',
            'saturacion_oxigeno'      => 98,
            'triage'                  => 'verde',
        ]);
    }

    private function datosCitaValidos(array $override = []): array
    {
        $fechaHora = now()->addDay()->setTime(10, 0);

        return array_merge([
            'paciente_id'      => $this->paciente->id,
            'medico_id'        => $this->medico->id,
            'fecha'            => $fechaHora->format('Y-m-d'),
            'hora'             => $fechaHora->format('H:i'),
            'duracion_minutos' => 30,
            'motivo'           => 'Consulta general',
        ], $override);
    }

    /* =========================================================
     | CREACIÓN DE CITA (vía agenda.store)
     | Solo validaciones — los escenarios "happy path" se omiten
     | porque AgendaController@store usa DATE_ADD(...INTERVAL...)
     | que SQLite no soporta.
     ========================================================= */

    public function test_no_se_puede_crear_cita_sin_paciente(): void
    {
        $datos = $this->datosCitaValidos(['paciente_id' => null]);

        $this->actingAs($this->admin)
            ->post(route('agenda.store'), $datos)
            ->assertSessionHasErrors('paciente_id');

        $this->assertDatabaseCount('citas', 0);
    }

    public function test_no_se_puede_crear_cita_sin_medico(): void
    {
        $datos = $this->datosCitaValidos(['medico_id' => null]);

        $this->actingAs($this->admin)
            ->post(route('agenda.store'), $datos)
            ->assertSessionHasErrors('medico_id');

        $this->assertDatabaseCount('citas', 0);
    }

    public function test_no_se_puede_crear_cita_con_fecha_invalida(): void
    {
        $datos = $this->datosCitaValidos(['fecha' => 'no-es-fecha']);

        $this->actingAs($this->admin)
            ->post(route('agenda.store'), $datos)
            ->assertSessionHasErrors('fecha');

        $this->assertDatabaseCount('citas', 0);
    }

    public function test_no_se_puede_crear_cita_con_hora_invalida(): void
    {
        $datos = $this->datosCitaValidos(['hora' => '25:99']);

        $this->actingAs($this->admin)
            ->post(route('agenda.store'), $datos)
            ->assertSessionHasErrors('hora');

        $this->assertDatabaseCount('citas', 0);
    }

    public function test_no_se_puede_crear_cita_con_duracion_cero(): void
    {
        $datos = $this->datosCitaValidos(['duracion_minutos' => 0]);

        $this->actingAs($this->admin)
            ->post(route('agenda.store'), $datos)
            ->assertSessionHasErrors('duracion_minutos');
    }

    public function test_no_se_puede_crear_cita_con_duracion_excesiva(): void
    {
        $datos = $this->datosCitaValidos(['duracion_minutos' => 500]);

        $this->actingAs($this->admin)
            ->post(route('agenda.store'), $datos)
            ->assertSessionHasErrors('duracion_minutos');
    }

    public function test_no_se_puede_crear_cita_en_fecha_pasada(): void
    {
        $datos = $this->datosCitaValidos([
            'fecha' => now()->subDay()->format('Y-m-d'),
        ]);

        $this->actingAs($this->admin)
            ->post(route('agenda.store'), $datos)
            ->assertSessionHasErrors('fecha');

        $this->assertDatabaseCount('citas', 0);
    }

    public function test_invitado_no_puede_crear_cita(): void
    {
        $this->post(route('agenda.store'), $this->datosCitaValidos())
            ->assertRedirect(route('login'));

        $this->assertDatabaseCount('citas', 0);
    }
}
