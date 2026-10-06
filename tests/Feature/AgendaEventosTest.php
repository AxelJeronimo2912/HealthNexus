<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\Concerns\CreaDatosClinicos;
use Tests\TestCase;

class AgendaEventosTest extends TestCase
{
    use CreaDatosClinicos;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $this->prepararRolesYPermisos();
    }

    public function test_administrador_recibe_los_eventos_del_rango_con_datos_visuales(): void
    {
        $administrador = $this->crearUsuarioConRol('administrador');
        $this->actingAs($administrador);
        $medico = $this->crearMedico();
        $paciente = $this->crearPaciente();
        $cita = $this->crearCita($medico, $paciente);
        $cita->update(['triage_al_momento' => 'rojo']);

        $response = $this->getJson(route('agenda.eventos', [
            'start' => now()->subDay()->startOfDay()->format('Y-m-d H:i:s'),
            'end' => now()->addDays(2)->startOfDay()->format('Y-m-d H:i:s'),
        ]));

        $response->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.id', $cita->id)
            ->assertJsonPath('0.title', $paciente->nombre_completo . ' · Dr. ' . $medico->nombre_completo)
            ->assertJsonPath('0.color', '#f43f5e')
            ->assertJsonPath('0.extendedProps.medico', $medico->nombre_completo);
    }

    public function test_medico_solo_recibe_sus_citas_en_el_calendario(): void
    {
        $medico = $this->crearMedico();
        $otroMedico = $this->crearMedico();
        $this->actingAs($medico);
        $citaPropia = $this->crearCita($medico);
        $this->crearCita($otroMedico);

        $response = $this->getJson(route('agenda.eventos', [
            'start' => now()->subDay()->startOfDay()->format('Y-m-d H:i:s'),
            'end' => now()->addDays(2)->startOfDay()->format('Y-m-d H:i:s'),
        ]));

        $response->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.id', $citaPropia->id);
    }

    public function test_eventos_requiere_un_rango_de_fechas_valido(): void
    {
        $administrador = $this->crearUsuarioConRol('administrador');

        $this->actingAs($administrador)
            ->getJson(route('agenda.eventos', ['start' => now()->toDateString()]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['end']);
    }
}
