
<?php

use App\Models\Cita;
use App\Models\Consulta;
use App\Models\Diagnostico;
use App\Models\Lote;
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

class RegistrarConsultaTest extends TestCase
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

        $this->admin = $this->crearUsuario('administrador');
        $this->medico = $this->crearUsuario('medico');
        $this->otroMedico = $this->crearUsuario('medico');

        $this->paciente = $this->crearPaciente();
        $this->crearSignoVital($this->paciente);
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
            'nombre' => 'Paciente',
            'apellido_paterno' => 'Test',
            'apellido_materno' => 'Demo',
            'fecha_nacimiento' => '1990-01-01',
            'sexo' => 'hombre',
            'estado_civil' => 'Soltero',
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

    private function crearSignoVital(
        Paciente $paciente,
        string $triage = 'verde',
        $createdAt = null
    ): SignoVital {
        $signo = SignoVital::create([
            'paciente_id' => $paciente->id,
            'user_id' => $this->medico->id,
            'temperatura' => 36.5,
            'frecuencia_cardiaca' => 80,
            'frecuencia_respiratoria' => 18,
            'presion_arterial' => '120/80',
            'saturacion_oxigeno' => 98,
            'triage' => $triage,
        ]);

        if ($createdAt) {
            $signo->forceFill([
                'created_at' => $createdAt,
            ])->save();
        }

        return $signo;
    }

    private function crearCita(
        User $medico,
        string $estado = 'confirmada',
        array $extra = []
    ): Cita {
        return Cita::create(array_merge([
            'paciente_id' => $this->paciente->id,
            'medico_id' => $medico->id,
            'creado_por' => $this->admin->id,
            'fecha_hora' => now()->addHour(),
            'duracion_minutos' => 30,
            'estado' => $estado,
            'triage_al_momento' => 'verde',
            'motivo' => 'Consulta general',
        ], $extra));
    }

    private function diagnostico(): Diagnostico
    {
        return Diagnostico::create([
            'codigo' => 'G44.2',
            'nombre' => 'Cefalea tensional',
            'activo' => true,
        ]);
    }

    private function crearMedicamentoConLote(
        int $stock = 10,
        array $extra = []
    ): Medicamento {
        $medicamento = Medicamento::create(array_merge([
            'nombre' => 'Paracetamol',
            'presentacion' => 'Tableta',
            'concentracion' => '500mg',
            'activo' => true,
            'stock_actual' => $stock,
            'stock_minimo' => 2,
        ], $extra));

        Lote::create([
            'medicamento_id' => $medicamento->id,
            'user_id' => $this->admin->id,
            'codigo_lote' => 'LOTE-' . Str::random(6),
            'fecha_caducidad' => now()->addYear(),
            'fecha_ingreso' => now(),
            'cantidad_inicial' => $stock,
            'cantidad_disponible' => $stock,
            'activo' => true,
        ]);

        return $medicamento;
    }

    private function payload(array $extra = []): array
    {
        return array_merge([
            'subjetivo' => 'Paciente refiere dolor de cabeza desde hace 3 días.',
            'objetivo' => 'TA 120/80, FC 78, T 36.5°C. Consciente y orientado.',
            'analisis' => 'Cefalea tensional.',
            'plan' => 'Paracetamol 500mg cada 8h por 3 días. Reposo.',
            'finalizar' => true,
        ], $extra);
    }

    /* =========================================================
     | INICIAR CONSULTA
     ========================================================= */

    public function test_medico_puede_iniciar_consulta_de_su_cita(): void
    {
        $cita = $this->crearCita($this->medico, 'confirmada');

        $this->actingAs($this->medico)
            ->get(route('consultas.iniciar', $cita))
            ->assertOk()
            ->assertViewIs('consultas.create');

        $this->assertEquals('en_curso', $cita->fresh()->estado);
    }

    public function test_admin_puede_iniciar_consulta_de_cualquier_medico(): void
    {
        $cita = $this->crearCita($this->medico, 'confirmada');

        $this->actingAs($this->admin)
            ->get(route('consultas.iniciar', $cita))
            ->assertOk();
    }

    public function test_medico_no_puede_iniciar_consulta_de_cita_ajena(): void
    {
        $cita = $this->crearCita($this->otroMedico, 'confirmada');

        $this->actingAs($this->medico)
            ->get(route('consultas.iniciar', $cita))
            ->assertForbidden();
    }

    public function test_no_se_puede_iniciar_consulta_si_la_cita_esta_programada(): void
    {
        $cita = $this->crearCita($this->medico, 'programada');

        $this->actingAs($this->medico)
            ->get(route('consultas.iniciar', $cita))
            ->assertRedirect(route('citas.show', $cita))
            ->assertSessionHas('error');
    }

    public function test_no_se_puede_iniciar_consulta_sin_signos_vitales(): void
    {
        SignoVital::where('paciente_id', $this->paciente->id)->delete();

        $cita = $this->crearCita($this->medico, 'confirmada');

        $this->actingAs($this->medico)
            ->get(route('consultas.iniciar', $cita))
            ->assertRedirect(route('signos-vitales.create', [
                'paciente_id' => $this->paciente->id,
                'cita_id' => $cita->id,
            ]))
            ->assertSessionHas('warning');
    }

    public function test_no_se_puede_iniciar_consulta_con_signos_vitales_de_mas_de_7_dias(): void
    {
        SignoVital::where('paciente_id', $this->paciente->id)->delete();

        $this->crearSignoVital(
            $this->paciente,
            'verde',
            now()->subDays(10)
        );

        $cita = $this->crearCita($this->medico, 'confirmada');

        $this->actingAs($this->medico)
            ->get(route('consultas.iniciar', $cita))
            ->assertRedirect()
            ->assertSessionHas('warning');
    }

    public function test_invitado_no_puede_iniciar_consulta(): void
    {
        $cita = $this->crearCita($this->medico, 'confirmada');

        $this->get(route('consultas.iniciar', $cita))
            ->assertRedirect(route('login'));
    }

    /* =========================================================
     | GUARDAR CONSULTA
     ========================================================= */

    public function test_medico_puede_registrar_consulta_finalizada(): void
    {
        $cita = $this->crearCita($this->medico, 'en_curso');
        $diagnostico = $this->diagnostico();

        $this->actingAs($this->medico)
            ->post(route('consultas.store', $cita), $this->payload([
                'diagnostico_principal_id' => $diagnostico->id,
            ]))
            ->assertRedirect();

        $this->assertDatabaseHas('consultas', [
            'cita_id' => $cita->id,
            'paciente_id' => $this->paciente->id,
            'medico_id' => $this->medico->id,
            'estado' => 'finalizada',
            'diagnostico_principal_id' => $diagnostico->id,
        ]);

        $this->assertEquals('atendida', $cita->fresh()->estado);
    }

    public function test_se_puede_guardar_como_borrador_sin_finalizar(): void
    {
        $cita = $this->crearCita($this->medico, 'en_curso');

        $this->actingAs($this->medico)
            ->post(route('consultas.store', $cita), $this->payload([
                'finalizar' => false,
            ]));

        $this->assertDatabaseHas('consultas', [
            'cita_id' => $cita->id,
            'estado' => 'borrador',
        ]);
    }

    public function test_no_se_puede_finalizar_consulta_sin_diagnostico_principal(): void
    {
        $cita = $this->crearCita($this->medico, 'en_curso');

        $this->actingAs($this->medico)
            ->post(route('consultas.store', $cita), $this->payload())
            ->assertSessionHasErrors('diagnostico_principal_id');

        $this->assertDatabaseCount('consultas', 0);
    }

    public function test_no_se_puede_finalizar_consulta_sin_plan(): void
    {
        $cita = $this->crearCita($this->medico, 'en_curso');
        $diagnostico = $this->diagnostico();

        $this->actingAs($this->medico)
            ->post(route('consultas.store', $cita), $this->payload([
                'diagnostico_principal_id' => $diagnostico->id,
                'plan' => null,
            ]))
            ->assertSessionHasErrors('plan');

        $this->assertDatabaseCount('consultas', 0);
    }

    public function test_no_se_puede_finalizar_consulta_sin_subjetivo_ni_objetivo(): void
    {
        $cita = $this->crearCita($this->medico, 'en_curso');
        $diagnostico = $this->diagnostico();

        $this->actingAs($this->medico)
            ->post(route('consultas.store', $cita), $this->payload([
                'diagnostico_principal_id' => $diagnostico->id,
                'subjetivo' => null,
                'objetivo' => null,
            ]))
            ->assertSessionHasErrors('subjetivo');

        $this->assertDatabaseCount('consultas', 0);
    }

    public function test_invitado_no_puede_registrar_consulta(): void
    {
        $cita = $this->crearCita($this->medico, 'en_curso');

        $this->post(route('consultas.store', $cita), $this->payload())
            ->assertRedirect(route('login'));

        $this->assertDatabaseCount('consultas', 0);
    }

    /* =========================================================
     | IMC
     ========================================================= */

    public function test_se_calcula_el_imc_al_guardar_consulta(): void
    {
        $cita = $this->crearCita($this->medico, 'en_curso');
        $diagnostico = $this->diagnostico();

        $this->actingAs($this->medico)
            ->post(route('consultas.store', $cita), $this->payload([
                'diagnostico_principal_id' => $diagnostico->id,
                'peso' => 70,
                'talla' => 1.75,
            ]));

        $consulta = Consulta::where('cita_id', $cita->id)->first();

        $this->assertNotNull($consulta);
        $this->assertEquals(22.9, (float) $consulta->imc);
    }

    /* =========================================================
     | INVENTARIO
     ========================================================= */

    public function test_al_recetar_un_medicamento_se_descuenta_del_stock(): void
    {
        $cita = $this->crearCita($this->medico, 'en_curso');
        $diagnostico = $this->diagnostico();

        $medicamento = $this->crearMedicamentoConLote(10);

        $this->actingAs($this->medico)
            ->post(route('consultas.store', $cita), $this->payload([
                'diagnostico_principal_id' => $diagnostico->id,
                'medicamentos' => [
                    [
                        'id' => $medicamento->id,
                        'dosis' => '500mg',
                        'via' => 'oral',
                        'frecuencia' => 'cada 8 horas',
                        'duracion' => '3 días',
                        'indicaciones' => 'Después de alimentos',
                    ],
                ],
            ]))
            ->assertRedirect();

        $this->assertEquals(9, $medicamento->fresh()->stock_actual);
    }

    public function test_no_se_puede_recetar_medicamento_sin_stock(): void
    {
        $cita = $this->crearCita($this->medico, 'en_curso');
        $diagnostico = $this->diagnostico();

        $medicamento = $this->crearMedicamentoConLote(0, [
            'nombre' => 'Sin Stock',
        ]);

        $this->actingAs($this->medico)
            ->post(route('consultas.store', $cita), $this->payload([
                'diagnostico_principal_id' => $diagnostico->id,
                'medicamentos' => [
                    [
                        'id' => $medicamento->id,
                        'dosis' => '500mg',
                        'via' => 'oral',
                        'frecuencia' => 'cada 8 horas',
                        'duracion' => '3 días',
                    ],
                ],
            ]))
            ->assertSessionHasErrors('medicamentos');

        $this->assertEquals(0, $medicamento->fresh()->stock_actual);
    }

    public function test_no_se_puede_recetar_dos_veces_el_mismo_medicamento(): void
    {
        $cita = $this->crearCita($this->medico, 'en_curso');
        $diagnostico = $this->diagnostico();
        $medicamento = $this->crearMedicamentoConLote(10);

        $this->actingAs($this->medico)
            ->post(route('consultas.store', $cita), $this->payload([
                'diagnostico_principal_id' => $diagnostico->id,
                'medicamentos' => [
                    [
                        'id' => $medicamento->id,
                        'dosis' => '500mg',
                        'via' => 'oral',
                        'frecuencia' => 'cada 8 horas',
                        'duracion' => '3 días',
                    ],
                    [
                        'id' => $medicamento->id,
                        'dosis' => '250mg',
                        'via' => 'oral',
                        'frecuencia' => 'cada 12 horas',
                        'duracion' => '3 días',
                    ],
                ],
            ]))
            ->assertSessionHasErrors('medicamentos');

        $this->assertDatabaseCount('consultas', 0);
    }

    /* =========================================================
     | AUTORIZACIÓN AL VER
     ========================================================= */

    public function test_medico_no_puede_ver_consulta_de_otro_medico(): void
    {
        $cita = $this->crearCita($this->otroMedico, 'atendida');

        $consulta = Consulta::create([
            'cita_id' => $cita->id,
            'paciente_id' => $this->paciente->id,
            'medico_id' => $this->otroMedico->id,
            'subjetivo' => 'Datos',
            'objetivo' => 'Datos',
            'analisis' => 'Datos',
            'plan' => 'Datos',
            'estado' => 'finalizada',
        ]);

        $this->actingAs($this->medico)
            ->get(route('consultas.show', $consulta))
            ->assertForbidden();
    }

    public function test_admin_puede_ver_cualquier_consulta(): void
    {
        $cita = $this->crearCita($this->otroMedico, 'atendida');

        $consulta = Consulta::create([
            'cita_id' => $cita->id,
            'paciente_id' => $this->paciente->id,
            'medico_id' => $this->otroMedico->id,
            'subjetivo' => 'Datos',
            'objetivo' => 'Datos',
            'analisis' => 'Datos',
            'plan' => 'Datos',
            'estado' => 'finalizada',
        ]);

        $this->actingAs($this->admin)
            ->get(route('consultas.show', $consulta))
            ->assertOk();
    }
}
