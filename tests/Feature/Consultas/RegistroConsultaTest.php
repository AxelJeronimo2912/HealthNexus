<?php

namespace Tests\Feature\Consultas;

use App\Models\Consulta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Concerns\CreaDatosClinicos;
use Tests\TestCase;

/**
 * Pruebas automatizadas del registro de consultas médicas:
 * inicio de consulta, guardado (borrador / finalizada), validaciones,
 * receta con descuento de inventario, edición y control de acceso.
 */
class RegistroConsultaTest extends TestCase
{
    use RefreshDatabase, CreaDatosClinicos;

    protected function setUp(): void
    {
        parent::setUp();

        // Las vistas usan @vite; en pruebas no se necesita el build de assets.
        $this->withoutVite();

        $this->prepararRolesYPermisos();
    }

    /* ============================================================
     |  INICIAR CONSULTA
     ============================================================ */

    public function test_medico_puede_iniciar_consulta_de_su_cita_confirmada(): void
    {
        $medico = $this->crearMedico();
        $cita = $this->crearCita($medico);
        $this->crearSignoVital($cita->paciente);

        $this->actingAs($medico)
            ->get(route('consultas.iniciar', $cita))
            ->assertOk()
            ->assertViewIs('consultas.create');

        // Al iniciar, la cita pasa de "confirmada" a "en_curso"
        $this->assertSame('en_curso', $cita->fresh()->estado);
    }

    public function test_medico_no_puede_iniciar_consulta_de_la_cita_de_otro_medico(): void
    {
        $duenio = $this->crearMedico();
        $otro = $this->crearMedico();
        $cita = $this->crearCita($duenio);
        $this->crearSignoVital($cita->paciente);

        $this->actingAs($otro)
            ->get(route('consultas.iniciar', $cita))
            ->assertForbidden();

        $this->assertSame('confirmada', $cita->fresh()->estado);
    }

    public function test_administrador_puede_iniciar_consulta_de_cualquier_medico(): void
    {
        $admin = $this->crearUsuarioConRol('administrador');
        $cita = $this->crearCita($this->crearMedico());
        $this->crearSignoVital($cita->paciente);

        $this->actingAs($admin)
            ->get(route('consultas.iniciar', $cita))
            ->assertOk();
    }

    #[DataProvider('estadosDeCitaNoIniciables')]
    public function test_no_se_inicia_consulta_si_la_cita_no_esta_confirmada_ni_en_curso(string $estado): void
    {
        $medico = $this->crearMedico();
        $cita = $this->crearCita($medico, null, $estado);
        $this->crearSignoVital($cita->paciente);

        $this->actingAs($medico)
            ->get(route('consultas.iniciar', $cita))
            ->assertRedirect(route('citas.show', $cita))
            ->assertSessionHas('error');

        $this->assertSame($estado, $cita->fresh()->estado);
    }

    public static function estadosDeCitaNoIniciables(): array
    {
        return [
            'programada' => ['programada'],
            'cancelada'  => ['cancelada'],
            'atendida'   => ['atendida'],
            'no asistió' => ['no_asistio'],
        ];
    }

    public function test_pide_registrar_signos_vitales_si_el_paciente_no_tiene_ninguno(): void
    {
        $medico = $this->crearMedico();
        $cita = $this->crearCita($medico);

        $this->actingAs($medico)
            ->get(route('consultas.iniciar', $cita))
            ->assertRedirect(route('signos-vitales.create', [
                'paciente_id' => $cita->paciente_id,
                'cita_id'     => $cita->id,
            ]))
            ->assertSessionHas('warning');
    }

    public function test_pide_nuevos_signos_vitales_si_tienen_mas_de_7_dias(): void
    {
        $medico = $this->crearMedico();
        $cita = $this->crearCita($medico);
        $this->crearSignoVital($cita->paciente, now()->subDays(8));

        $this->actingAs($medico)
            ->get(route('consultas.iniciar', $cita))
            ->assertRedirect(route('signos-vitales.create', [
                'paciente_id' => $cita->paciente_id,
                'cita_id'     => $cita->id,
            ]))
            ->assertSessionHas('warning');
    }

    public function test_acepta_signos_vitales_de_hace_menos_de_7_dias(): void
    {
        $medico = $this->crearMedico();
        $cita = $this->crearCita($medico);
        $this->crearSignoVital($cita->paciente, now()->subDays(6));

        $this->actingAs($medico)
            ->get(route('consultas.iniciar', $cita))
            ->assertOk();
    }

    public function test_si_la_cita_ya_tiene_consulta_redirige_a_edicion(): void
    {
        $medico = $this->crearMedico();
        $consulta = $this->crearConsulta($medico);
        $consulta->cita->update(['estado' => 'en_curso']);

        $this->actingAs($medico)
            ->get(route('consultas.iniciar', $consulta->cita))
            ->assertRedirect(route('consultas.edit', $consulta));
    }

    /* ============================================================
     |  GUARDAR CONSULTA
     ============================================================ */

    public function test_medico_guarda_consulta_como_borrador(): void
    {
        $medico = $this->crearMedico();
        $cita = $this->crearCita($medico);

        $response = $this->actingAs($medico)
            ->post(route('consultas.store', $cita), $this->datosConsulta());

        $consulta = Consulta::firstOrFail();

        $response->assertRedirect(route('consultas.show', $consulta))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('consultas', [
            'id'          => $consulta->id,
            'cita_id'     => $cita->id,
            'paciente_id' => $cita->paciente_id,
            'medico_id'   => $medico->id,
            'estado'      => 'borrador',
        ]);
        $this->assertNull($consulta->finalizada_en);
    }

    public function test_medico_finaliza_consulta_y_la_cita_queda_atendida(): void
    {
        $medico = $this->crearMedico();
        $cita = $this->crearCita($medico);

        $this->actingAs($medico)
            ->post(route('consultas.store', $cita), $this->datosConsulta(['finalizar' => 1]))
            ->assertSessionHasNoErrors();

        $consulta = Consulta::firstOrFail();

        $this->assertSame('finalizada', $consulta->estado);
        $this->assertNotNull($consulta->finalizada_en);
        $this->assertSame('atendida', $cita->fresh()->estado);
    }

    public function test_guarda_los_datos_clinicos_enviados(): void
    {
        $medico = $this->crearMedico();
        $cita = $this->crearCita($medico);

        $this->actingAs($medico)
            ->post(route('consultas.store', $cita), $this->datosConsulta([
                'presion_arterial'        => '130/85',
                'frecuencia_respiratoria' => 18,
                'glucosa'                 => 95,
                'perimetro_abdominal'     => 88.5,
                'receta_libre'            => 'Reposo relativo e hidratación.',
                'notas'                   => 'Regresar si empeora.',
            ]));

        $consulta = Consulta::firstOrFail();

        $this->assertSame('130/85', $consulta->presion_arterial);
        $this->assertSame(18, (int) $consulta->frecuencia_respiratoria);
        $this->assertSame(95, (int) $consulta->glucosa);
        $this->assertSame('Reposo relativo e hidratación.', $consulta->receta_libre);
        $this->assertSame('Regresar si empeora.', $consulta->notas);
    }

    public function test_el_soap_se_guarda_cifrado_en_base_de_datos(): void
    {
        $medico = $this->crearMedico();
        $cita = $this->crearCita($medico);
        $datos = $this->datosConsulta();

        $this->actingAs($medico)->post(route('consultas.store', $cita), $datos);

        $fila = DB::table('consultas')->first();

        foreach (['subjetivo', 'objetivo', 'analisis', 'plan'] as $campo) {
            // En la base NO debe estar el texto plano...
            $this->assertNotSame($datos[$campo], $fila->{$campo}, "El campo {$campo} se guardó sin cifrar.");
            $this->assertStringNotContainsString($datos[$campo], $fila->{$campo});

            // ...pero el modelo lo descifra correctamente.
            $this->assertSame($datos[$campo], Consulta::first()->{$campo});
        }
    }

    public function test_calcula_el_imc_automaticamente(): void
    {
        $medico = $this->crearMedico();
        $cita = $this->crearCita($medico);

        $this->actingAs($medico)
            ->post(route('consultas.store', $cita), $this->datosConsulta([
                'peso'  => 70,
                'talla' => 1.75,
            ]));

        // 70 / 1.75² = 22.857... -> 22.9
        $this->assertEquals(22.9, Consulta::firstOrFail()->imc);
    }

    public function test_paciente_y_medico_se_toman_de_la_cita_y_no_del_formulario(): void
    {
        $medico = $this->crearMedico();
        $intruso = $this->crearMedico();
        $otroPaciente = $this->crearPaciente(['nombre' => 'Otro']);
        $cita = $this->crearCita($medico);

        $this->actingAs($medico)
            ->post(route('consultas.store', $cita), $this->datosConsulta([
                'paciente_id' => $otroPaciente->id,
                'medico_id'   => $intruso->id,
                'cita_id'     => 999,
            ]));

        $consulta = Consulta::firstOrFail();

        $this->assertSame($cita->paciente_id, $consulta->paciente_id);
        $this->assertSame($medico->id, $consulta->medico_id);
        $this->assertSame($cita->id, $consulta->cita_id);
    }

    public function test_asocia_diagnostico_principal_y_secundario(): void
    {
        $medico = $this->crearMedico();
        $cita = $this->crearCita($medico);
        $principal = $this->crearDiagnostico('J00', 'Rinofaringitis aguda');
        $secundario = $this->crearDiagnostico('R51', 'Cefalea');

        $this->actingAs($medico)
            ->post(route('consultas.store', $cita), $this->datosConsulta([
                'diagnostico_principal_id'  => $principal->id,
                'diagnostico_secundario_id' => $secundario->id,
            ]));

        $consulta = Consulta::firstOrFail();

        $this->assertTrue($consulta->diagnosticoPrincipal->is($principal));
        $this->assertTrue($consulta->diagnosticoSecundario->is($secundario));
    }

    /* ============================================================
     |  VALIDACIONES
     ============================================================ */

    #[DataProvider('datosInvalidos')]
    public function test_rechaza_datos_invalidos_y_no_crea_la_consulta(string $errorEn, array $payload): void
    {
        $medico = $this->crearMedico();
        $cita = $this->crearCita($medico);

        $this->actingAs($medico)
            ->post(route('consultas.store', $cita), $this->datosConsulta($payload))
            ->assertSessionHasErrors($errorEn);

        $this->assertDatabaseCount('consultas', 0);
        $this->assertSame('confirmada', $cita->fresh()->estado);
    }

    public static function datosInvalidos(): array
    {
        return [
            'temperatura menor a 30'        => ['temperatura', ['temperatura' => 29.9]],
            'temperatura mayor a 45'        => ['temperatura', ['temperatura' => 45.1]],
            'temperatura no numérica'       => ['temperatura', ['temperatura' => 'caliente']],
            'FC menor a 20'                 => ['frecuencia_cardiaca', ['frecuencia_cardiaca' => 19]],
            'FC mayor a 250'                => ['frecuencia_cardiaca', ['frecuencia_cardiaca' => 251]],
            'FR menor a 5'                  => ['frecuencia_respiratoria', ['frecuencia_respiratoria' => 4]],
            'FR mayor a 80'                 => ['frecuencia_respiratoria', ['frecuencia_respiratoria' => 81]],
            'saturación mayor a 100'        => ['saturacion_oxigeno', ['saturacion_oxigeno' => 101]],
            'saturación negativa'           => ['saturacion_oxigeno', ['saturacion_oxigeno' => -1]],
            'glucosa mayor a 1000'          => ['glucosa', ['glucosa' => 1001]],
            'peso menor a 0.5'              => ['peso', ['peso' => 0.4]],
            'peso mayor a 400'              => ['peso', ['peso' => 401]],
            'talla menor a 0.3'             => ['talla', ['talla' => 0.29]],
            'talla mayor a 2.5'             => ['talla', ['talla' => 2.6]],
            'presión arterial muy larga'    => ['presion_arterial', ['presion_arterial' => str_repeat('1', 21)]],
            'diagnóstico principal inexistente'  => ['diagnostico_principal_id', ['diagnostico_principal_id' => 99999]],
            'diagnóstico secundario inexistente' => ['diagnostico_secundario_id', ['diagnostico_secundario_id' => 99999]],
            'medicamento inexistente'       => ['medicamentos.0.id', ['medicamentos' => [['id' => 99999]]]],
            'medicamentos no es arreglo'    => ['medicamentos', ['medicamentos' => 'paracetamol']],
        ];
    }

    public function test_acepta_los_valores_limite_permitidos(): void
    {
        $medico = $this->crearMedico();
        $cita = $this->crearCita($medico);

        $this->actingAs($medico)
            ->post(route('consultas.store', $cita), $this->datosConsulta([
                'temperatura'             => 30,
                'frecuencia_cardiaca'     => 250,
                'frecuencia_respiratoria' => 5,
                'saturacion_oxigeno'      => 100,
                'glucosa'                 => 0,
            ]))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount('consultas', 1);
    }

    /* ============================================================
     |  RECETA E INVENTARIO
     ============================================================ */

    public function test_recetar_un_medicamento_lo_asocia_y_descuenta_inventario(): void
    {
        $medico = $this->crearMedico();
        $cita = $this->crearCita($medico);
        $medicamento = $this->crearMedicamento(10);

        $this->actingAs($medico)
            ->post(route('consultas.store', $cita), $this->datosConsulta([
                'medicamentos' => [[
                    'id'           => $medicamento->id,
                    'dosis'        => '1 tableta',
                    'via'          => 'Oral',
                    'frecuencia'   => 'Cada 8 horas',
                    'duracion'     => '5 días',
                    'indicaciones' => 'Después de los alimentos',
                ]],
            ]))
            ->assertSessionHasNoErrors();

        $consulta = Consulta::firstOrFail();

        // Receta guardada con sus datos de pivote
        $this->assertDatabaseHas('consulta_medicamento', [
            'consulta_id'    => $consulta->id,
            'medicamento_id' => $medicamento->id,
            'dosis'          => '1 tableta',
            'via'            => 'Oral',
            'frecuencia'     => 'Cada 8 horas',
            'duracion'       => '5 días',
            'indicaciones'   => 'Después de los alimentos',
        ]);

        // Inventario: stock global y lote
        $this->assertSame(9, $medicamento->fresh()->stock_actual);
        $this->assertDatabaseHas('lotes', [
            'medicamento_id'      => $medicamento->id,
            'cantidad_disponible' => 9,
        ]);

        // Movimiento de salida trazable a la consulta
        $this->assertDatabaseHas('movimientos_inventario', [
            'medicamento_id'  => $medicamento->id,
            'tipo'            => 'salida',
            'cantidad'        => -1,
            'stock_anterior'  => 10,
            'stock_nuevo'     => 9,
            'referencia_tipo' => 'consulta',
            'referencia_id'   => $consulta->id,
            'user_id'         => $medico->id,
        ]);
    }

    public function test_recetar_varios_medicamentos_descuenta_cada_uno(): void
    {
        $medico = $this->crearMedico();
        $cita = $this->crearCita($medico);
        $paracetamol = $this->crearMedicamento(5);
        $ibuprofeno = $this->crearMedicamento(3, ['nombre' => 'Ibuprofeno', 'concentracion' => '400 mg']);

        $this->actingAs($medico)
            ->post(route('consultas.store', $cita), $this->datosConsulta([
                'medicamentos' => [
                    ['id' => $paracetamol->id, 'dosis' => '1 tableta'],
                    ['id' => $ibuprofeno->id, 'dosis' => '1 tableta'],
                ],
            ]))
            ->assertSessionHasNoErrors();

        $this->assertCount(2, Consulta::firstOrFail()->medicamentos);
        $this->assertSame(4, $paracetamol->fresh()->stock_actual);
        $this->assertSame(2, $ibuprofeno->fresh()->stock_actual);
    }

    public function test_no_permite_recetar_medicamento_sin_stock(): void
    {
        $medico = $this->crearMedico();
        $cita = $this->crearCita($medico);
        $agotado = $this->crearMedicamento(0, ['nombre' => 'Amoxicilina']);

        $this->actingAs($medico)
            ->post(route('consultas.store', $cita), $this->datosConsulta([
                'medicamentos' => [['id' => $agotado->id, 'dosis' => '1 cápsula']],
            ]))
            ->assertSessionHasErrors('medicamentos');

        // No se crea la consulta ni se toca la cita ni el inventario
        $this->assertDatabaseCount('consultas', 0);
        $this->assertDatabaseCount('movimientos_inventario', 0);
        $this->assertSame('confirmada', $cita->fresh()->estado);
    }

    /* ============================================================
     |  ACTUALIZAR CONSULTA
     ============================================================ */

    public function test_medico_actualiza_y_finaliza_su_consulta(): void
    {
        $medico = $this->crearMedico();
        $consulta = $this->crearConsulta($medico, ['estado' => 'borrador']);

        $this->actingAs($medico)
            ->put(route('consultas.update', $consulta), $this->datosConsulta([
                'analisis'  => 'Migraña sin aura.',
                'finalizar' => 1,
            ]))
            ->assertRedirect(route('consultas.show', $consulta))
            ->assertSessionHas('success');

        $consulta->refresh();

        $this->assertSame('Migraña sin aura.', $consulta->analisis);
        $this->assertSame('finalizada', $consulta->estado);
        $this->assertNotNull($consulta->finalizada_en);
    }

    public function test_actualizar_sin_finalizar_conserva_el_estado_borrador(): void
    {
        $medico = $this->crearMedico();
        $consulta = $this->crearConsulta($medico, ['estado' => 'borrador']);

        $this->actingAs($medico)
            ->put(route('consultas.update', $consulta), $this->datosConsulta(['plan' => 'Nuevo plan']))
            ->assertSessionHasNoErrors();

        $consulta->refresh();

        $this->assertSame('borrador', $consulta->estado);
        $this->assertNull($consulta->finalizada_en);
        $this->assertSame('Nuevo plan', $consulta->plan);
    }

    public function test_medico_no_puede_actualizar_la_consulta_de_otro_medico(): void
    {
        $duenio = $this->crearMedico();
        $otro = $this->crearMedico();
        $consulta = $this->crearConsulta($duenio);

        $this->actingAs($otro)
            ->put(route('consultas.update', $consulta), $this->datosConsulta(['analisis' => 'Alterado']))
            ->assertForbidden();

        $this->assertNotSame('Alterado', $consulta->fresh()->analisis);
    }

    public function test_actualizar_con_datos_invalidos_no_modifica_la_consulta(): void
    {
        $medico = $this->crearMedico();
        $consulta = $this->crearConsulta($medico);

        $this->actingAs($medico)
            ->put(route('consultas.update', $consulta), $this->datosConsulta(['temperatura' => 60]))
            ->assertSessionHasErrors('temperatura');

        $this->assertEquals(36.8, $consulta->fresh()->temperatura);
    }

    public function test_quitar_un_medicamento_al_editar_devuelve_el_stock(): void
    {
        $medico = $this->crearMedico();
        $cita = $this->crearCita($medico);
        $medicamento = $this->crearMedicamento(10);

        $this->actingAs($medico)->post(route('consultas.store', $cita), $this->datosConsulta([
            'medicamentos' => [['id' => $medicamento->id, 'dosis' => '1 tableta']],
        ]));

        $consulta = Consulta::firstOrFail();
        $this->assertSame(9, $medicamento->fresh()->stock_actual);

        // Se edita la consulta sin el medicamento
        $this->actingAs($medico)
            ->put(route('consultas.update', $consulta), $this->datosConsulta())
            ->assertSessionHasNoErrors();

        $this->assertCount(0, $consulta->fresh()->medicamentos);
        $this->assertSame(10, $medicamento->fresh()->stock_actual);
        $this->assertDatabaseHas('movimientos_inventario', [
            'medicamento_id'  => $medicamento->id,
            'tipo'            => 'entrada',
            'cantidad'        => 1,
            'referencia_tipo' => 'consulta',
            'referencia_id'   => $consulta->id,
            'motivo'          => 'Devolución por edición de consulta',
        ]);
    }

    public function test_conservar_un_medicamento_al_editar_no_lo_descuenta_dos_veces(): void
    {
        $medico = $this->crearMedico();
        $cita = $this->crearCita($medico);
        $medicamento = $this->crearMedicamento(10);
        $receta = [['id' => $medicamento->id, 'dosis' => '1 tableta']];

        $this->actingAs($medico)->post(route('consultas.store', $cita), $this->datosConsulta([
            'medicamentos' => $receta,
        ]));

        $consulta = Consulta::firstOrFail();

        $this->actingAs($medico)
            ->put(route('consultas.update', $consulta), $this->datosConsulta([
                'medicamentos' => [['id' => $medicamento->id, 'dosis' => '2 tabletas']],
            ]))
            ->assertSessionHasNoErrors();

        $this->assertSame(9, $medicamento->fresh()->stock_actual);
        $this->assertSame('2 tabletas', $consulta->fresh()->medicamentos->first()->pivot->dosis);
    }

    public function test_agregar_medicamento_sin_stock_al_editar_falla_y_no_modifica_la_consulta(): void
    {
        $medico = $this->crearMedico();
        $consulta = $this->crearConsulta($medico);
        $agotado = $this->crearMedicamento(0, ['nombre' => 'Amoxicilina']);

        $this->actingAs($medico)
            ->put(route('consultas.update', $consulta), $this->datosConsulta([
                'analisis'     => 'No debería guardarse',
                'medicamentos' => [['id' => $agotado->id]],
            ]))
            ->assertSessionHasErrors('medicamentos');

        $this->assertNotSame('No debería guardarse', $consulta->fresh()->analisis);
        $this->assertCount(0, $consulta->fresh()->medicamentos);
    }

    /* ============================================================
     |  MOSTRAR CONSULTA
     ============================================================ */

    public function test_medico_puede_ver_su_consulta(): void
    {
        $medico = $this->crearMedico();
        $consulta = $this->crearConsulta($medico);

        $this->actingAs($medico)
            ->get(route('consultas.show', $consulta))
            ->assertOk()
            ->assertViewIs('consultas.show')
            ->assertViewHas('consulta', fn ($c) => $c->is($consulta));
    }

    public function test_medico_no_puede_ver_la_consulta_de_otro_medico(): void
    {
        $consulta = $this->crearConsulta($this->crearMedico());

        $this->actingAs($this->crearMedico())
            ->get(route('consultas.show', $consulta))
            ->assertForbidden();
    }

    public function test_administrador_puede_ver_cualquier_consulta(): void
    {
        $consulta = $this->crearConsulta($this->crearMedico());

        $this->actingAs($this->crearUsuarioConRol('administrador'))
            ->get(route('consultas.show', $consulta))
            ->assertOk();
    }

    /* ============================================================
     |  CONTROL DE ACCESO (PERMISOS)
     ============================================================ */

    #[DataProvider('rolesSinPermisoDeConsultas')]
    public function test_roles_sin_permiso_de_consultas_no_pueden_registrar(string $rol): void
    {
        $medico = $this->crearMedico();
        $cita = $this->crearCita($medico);
        $usuario = $this->crearUsuarioConRol($rol);

        $this->actingAs($usuario)
            ->post(route('consultas.store', $cita), $this->datosConsulta())
            ->assertForbidden();

        $this->assertDatabaseCount('consultas', 0);
    }

    public static function rolesSinPermisoDeConsultas(): array
    {
        return [
            'enfermería' => ['enfermeria'],
            'farmacia'   => ['farmacia'],
        ];
    }

    public function test_invitado_es_redirigido_al_login_al_registrar_consulta(): void
    {
        $cita = $this->crearCita($this->crearMedico());

        $this->post(route('consultas.store', $cita), $this->datosConsulta())
            ->assertRedirect(route('login'));

        $this->assertDatabaseCount('consultas', 0);
    }

    public function test_invitado_es_redirigido_al_login_al_iniciar_consulta(): void
    {
        $cita = $this->crearCita($this->crearMedico());

        $this->get(route('consultas.iniciar', $cita))
            ->assertRedirect(route('login'));
    }
}