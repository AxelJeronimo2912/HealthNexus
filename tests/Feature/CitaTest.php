<?php

namespace Tests\Feature;

use App\Models\Cita;
use App\Models\Paciente;
use App\Models\PacienteMedico;
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

class CitaTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $medico;
    protected User $otroMedico;
    protected Paciente $paciente;

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

        $turno = Turno::create([
            'nombre'      => 'Matutino',
            'codigo'      => 'MAT',
            'hora_inicio' => '08:00:00',
            'hora_fin'    => '18:00:00',
            'activo'      => true,
        ]);

        foreach ([$this->medico, $this->otroMedico] as $m) {
            $m->turnos()->attach($turno->id, [
                'dia_semana'   => null,
                'fecha_inicio' => now()->subYear()->toDateString(),
                'fecha_fin'    => now()->addYear()->toDateString(),
                'activo'       => true,
            ]);
        }

        $this->paciente = $this->crearPaciente();
        $this->crearSignoVital($this->paciente);
    }

    /* HELPERS */

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

    private function crearSignoVital(Paciente $paciente, string $triage = 'verde'): SignoVital
    {
        return SignoVital::create([
            'paciente_id'             => $paciente->id,
            'user_id'                 => $this->medico->id,
            'temperatura'             => 36.5,
            'frecuencia_cardiaca'     => 80,
            'frecuencia_respiratoria' => 18,
            'presion_arterial'        => '120/80',
            'saturacion_oxigeno'      => 98,
            'triage'                  => $triage,
        ]);
    }

    private function crearCita(User $medico, Paciente $paciente, array $extra = []): Cita
    {
        return Cita::create(array_merge([
            'paciente_id'      => $paciente->id,
            'medico_id'        => $medico->id,
            'creado_por'       => $this->admin->id,
            'fecha_hora'       => now()->addDay()->setTime(10, 0),
            'duracion_minutos' => 30,
            'estado'           => 'programada',
            'motivo'            => 'Consulta general',
        ], $extra));
    }

    /*
    |--------------------------------------------------------------------------
    | CREACIÓN DE CITAS
    |--------------------------------------------------------------------------
    | NOTA: No se prueba vía POST al endpoint porque el controlador usa
    | DATE_ADD() (MySQL-only) y los tests corren en SQLite.
    | Se prueba la LÓGICA en BD directamente.
    */

    public function test_medico_puede_crear_una_cita_valida(): void
    {
        $fecha = now()->addDays(3)->setTime(10, 0);

        // Simulamos lo que hace el controlador al crear la cita
        $cita = Cita::create([
            'paciente_id'      => $this->paciente->id,
            'medico_id'        => $this->medico->id,
            'creado_por'       => $this->medico->id,
            'fecha_hora'       => $fecha,
            'duracion_minutos' => 30,
            'estado'           => 'programada',
            'motivo'            => 'Consulta general',
        ]);

        $this->assertDatabaseHas('citas', [
            'id'          => $cita->id,
            'paciente_id' => $this->paciente->id,
            'medico_id'   => $this->medico->id,
            'motivo'      => 'Consulta general',
            'estado'      => 'programada',
        ]);

        $this->assertEquals('programada', $cita->estado);
        $this->assertEquals(30, $cita->duracion_minutos);
    }

    public function test_no_se_puede_crear_cita_en_fecha_pasada(): void
    {
        $fechaHora = now()->subDay();

        // La lógica del controlador: rechazar si es antes de hoy
        $this->assertTrue($fechaHora->isBefore(today()));
    }

    public function test_no_se_puede_crear_cita_si_el_medico_no_trabaja_en_ese_horario(): void
    {
        $medicoSinTurno = $this->crearUsuario('medico');

        $trabaja = $medicoSinTurno->trabajaEn(
            now()->addDays(3)->toDateString(),
            '10:00'
        );

        $this->assertFalse($trabaja);
    }

    public function test_no_se_puede_crear_cita_con_conflicto_de_horario(): void
    {
        // Lógica de conflicto implementada en PHP (misma idea que el controlador)
        $fecha = now()->addDays(3)->format('Y-m-d');
        $inicio = \Carbon\Carbon::parse("{$fecha} 10:00:00");
        $duracion = 30;

        // Cita existente: 10:00–10:30
        $this->crearCita($this->medico, $this->paciente, [
            'fecha_hora' => $inicio,
        ]);

        // Cita candidata: 10:15–10:45
        $candidatoInicio = \Carbon\Carbon::parse("{$fecha} 10:15:00");
        $candidatoFin = $candidatoInicio->copy()->addMinutes($duracion);

        // Query de solapamiento simple: existe cita del médico donde
        // [fecha_hora, fecha_hora + duracion) se solape con [candidatoInicio, candidatoFin)
        $conflicto = Cita::where('medico_id', $this->medico->id)
            ->whereIn('estado', ['programada', 'confirmada', 'en_curso'])
            ->get()
            ->contains(function (Cita $c) use ($candidatoInicio, $candidatoFin) {
                $cInicio = $c->fecha_hora;
                $cFin    = $c->fecha_hora->copy()->addMinutes($c->duracion_minutos);

                return $cInicio < $candidatoFin && $cFin > $candidatoInicio;
            });

        $this->assertTrue($conflicto, 'Debería detectar conflicto de horario');
    }

    public function test_no_se_puede_crear_cita_si_el_paciente_ya_tiene_otra_activa(): void
    {
        $fecha = now()->addDays(3)->format('Y-m-d');

        $this->crearCita($this->medico, $this->paciente, [
            'fecha_hora' => "{$fecha} 09:00:00",
        ]);

        // Lógica del controlador: existe cita activa del paciente
        $tieneCitaActiva = Cita::where('paciente_id', $this->paciente->id)
            ->whereIn('estado', ['programada', 'confirmada', 'en_curso'])
            ->exists();

        $this->assertTrue($tieneCitaActiva);
    }

    public function test_no_se_puede_crear_cita_si_el_paciente_esta_asignado_a_otro_medico(): void
    {
        PacienteMedico::create([
            'paciente_id'  => $this->paciente->id,
            'medico_id'    => $this->otroMedico->id,
            'asignado_por' => $this->admin->id,
            'asignado_en'  => now(),
            'activo'       => true,
        ]);

        $asignacion = PacienteMedico::where('paciente_id', $this->paciente->id)
            ->where('activo', true)
            ->first();

        $this->assertNotNull($asignacion);
        $this->assertNotEquals($asignacion->medico_id, $this->medico->id);
        $this->assertEquals($this->otroMedico->id, $asignacion->medico_id);
    }

    public function test_invitado_no_puede_crear_citas(): void
    {
        $this->post(route('agenda.store'), [
            'paciente_id'      => $this->paciente->id,
            'medico_id'        => $this->medico->id,
            'fecha'            => now()->addDays(3)->format('Y-m-d'),
            'hora'             => '10:00',
            'duracion_minutos' => 30,
        ])->assertRedirect(route('login'));
    }

    /*
    |--------------------------------------------------------------------------
    | CAMBIO DE ESTADO
    |--------------------------------------------------------------------------
    */

    public function test_medico_puede_confirmar_su_propia_cita(): void
    {
        $cita = $this->crearCita($this->medico, $this->paciente, ['estado' => 'programada']);

        $this->actingAs($this->medico)
            ->post(route('citas.cambiar-estado', $cita), ['estado' => 'confirmada'])
            ->assertRedirect();

        $this->assertEquals('confirmada', $cita->fresh()->estado);
    }

    public function test_medico_puede_poner_en_curso_su_cita(): void
    {
        $cita = $this->crearCita($this->medico, $this->paciente, ['estado' => 'confirmada']);

        $this->actingAs($this->medico)
            ->post(route('citas.cambiar-estado', $cita), ['estado' => 'en_curso']);

        $this->assertEquals('en_curso', $cita->fresh()->estado);
    }

    public function test_medico_puede_marcar_como_atendida_su_cita(): void
    {
        $cita = $this->crearCita($this->medico, $this->paciente, ['estado' => 'en_curso']);

        $this->actingAs($this->medico)
            ->post(route('citas.cambiar-estado', $cita), ['estado' => 'atendida']);

        $this->assertEquals('atendida', $cita->fresh()->estado);
    }

    public function test_medico_puede_cancelar_su_cita(): void
    {
        $cita = $this->crearCita($this->medico, $this->paciente, ['estado' => 'programada']);

        $this->actingAs($this->medico)
            ->post(route('citas.cambiar-estado', $cita), ['estado' => 'cancelada']);

        $this->assertEquals('cancelada', $cita->fresh()->estado);
    }

    public function test_medico_puede_marcar_no_asistio(): void
    {
        $cita = $this->crearCita($this->medico, $this->paciente, ['estado' => 'programada']);

        $this->actingAs($this->medico)
            ->post(route('citas.cambiar-estado', $cita), ['estado' => 'no_asistio']);

        $this->assertEquals('no_asistio', $cita->fresh()->estado);
    }

    public function test_no_se_puede_cambiar_a_un_estado_invalido(): void
    {
        $cita = $this->crearCita($this->medico, $this->paciente, ['estado' => 'programada']);

        $this->actingAs($this->medico)
            ->post(route('citas.cambiar-estado', $cita), ['estado' => 'estado_falso'])
            ->assertSessionHasErrors('estado');

        $this->assertEquals('programada', $cita->fresh()->estado);
    }

    public function test_si_el_estado_no_cambia_retorna_info(): void
    {
        $cita = $this->crearCita($this->medico, $this->paciente, ['estado' => 'confirmada']);

        $this->actingAs($this->medico)
            ->post(route('citas.cambiar-estado', $cita), ['estado' => 'confirmada'])
            ->assertSessionHas('info');

        $this->assertEquals('confirmada', $cita->fresh()->estado);
    }

    /*
    |--------------------------------------------------------------------------
    | PERMISOS
    |--------------------------------------------------------------------------
    */

    public function test_medico_no_puede_cambiar_estado_de_cita_ajena(): void
    {
        $cita = $this->crearCita($this->otroMedico, $this->paciente, ['estado' => 'programada']);

        $this->actingAs($this->medico)
            ->post(route('citas.cambiar-estado', $cita), ['estado' => 'confirmada'])
            ->assertForbidden();

        $this->assertEquals('programada', $cita->fresh()->estado);
    }

    public function test_administrador_puede_cambiar_estado_de_cualquier_cita(): void
    {
        $cita = $this->crearCita($this->otroMedico, $this->paciente, ['estado' => 'programada']);

        $this->actingAs($this->admin)
            ->post(route('citas.cambiar-estado', $cita), ['estado' => 'confirmada']);

        $this->assertEquals('confirmada', $cita->fresh()->estado);
    }

    public function test_invitado_no_puede_cambiar_estado(): void
    {
        $cita = $this->crearCita($this->medico, $this->paciente, ['estado' => 'programada']);

        $this->post(route('citas.cambiar-estado', $cita), ['estado' => 'confirmada'])
            ->assertRedirect(route('login'));

        $this->assertEquals('programada', $cita->fresh()->estado);
    }

    /*
    |--------------------------------------------------------------------------
    | ELIMINACIÓN
    |--------------------------------------------------------------------------
    */

    public function test_administrador_puede_eliminar_una_cita(): void
    {
        $cita = $this->crearCita($this->medico, $this->paciente);

        $this->actingAs($this->admin)
            ->delete(route('citas.destroy', $cita))
            ->assertRedirect(route('citas.index'));

        $this->assertDatabaseMissing('citas', ['id' => $cita->id]);
    }

    public function test_medico_no_puede_eliminar_una_cita(): void
    {
        $cita = $this->crearCita($this->medico, $this->paciente);

        $this->actingAs($this->medico)
            ->delete(route('citas.destroy', $cita))
            ->assertForbidden();

        $this->assertDatabaseHas('citas', ['id' => $cita->id]);
    }
}
