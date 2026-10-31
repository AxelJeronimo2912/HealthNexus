<?php

namespace Tests\Feature\Cuenta;

use App\Http\Controllers\CuentaController;
use App\Models\Cita;
use App\Models\CuentaItem;
use App\Models\Paciente;
use App\Models\Servicio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

class CobroAutomaticoCitaTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Paciente $paciente;
    protected Servicio $servicio;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name'             => 'User ' . Str::random(6),
            'email'            => 'user' . Str::random(8) . '@test.com',
            'password'         => bcrypt('password'),
            'nombre'           => 'Juan',
            'apellido_paterno' => 'Pérez',
            'apellido_materno' => 'Gómez',
            'activo'           => true,
        ]);

        $this->actingAs($this->user);

        $this->paciente = Paciente::create([
            'nombre'             => 'Paciente',
            'apellido_paterno'   => 'Test',
            'apellido_materno'   => 'Demo',
            'fecha_nacimiento'   => '1990-01-01',
            'sexo'               => 'hombre',
            'curp'               => strtoupper(Str::random(18)),
            'telefono_principal' => '1234567890',
            'activo'             => true,
        ]);

        $this->servicio = Servicio::create([
            'codigo'      => 'SRV-' . Str::random(4),
            'nombre'      => 'Consulta general',
            'tipo'        => 'consulta_externa',
            'precio'      => 500,
            'activo'      => true,
            'abierto_24h' => false,
            'tiene_costo' => true,
        ]);
    }

    /* =========================================================
     |  HELPERS
     ========================================================= */

    private function crearCita(array $extra = []): Cita
    {
        $base = [
            'paciente_id'      => $this->paciente->id,
            'medico_id'        => $this->user->id,
            'creado_por'       => $this->user->id,
            'fecha_hora'       => now()->addDay()->setTime(10, 0),
            'duracion_minutos' => 30,
            'estado'           => 'programada',
            'motivo'           => 'Consulta general',
        ];

        if (Schema::hasColumn('citas', 'servicio_id')) {
            $base['servicio_id'] = $this->servicio->id;
        }
        if (Schema::hasColumn('citas', 'precio')) {
            $base['precio'] = 500;
        }

        return Cita::create(array_merge($base, $extra));
    }

    private function crearServicioGratis(): Servicio
    {
        return Servicio::create([
            'codigo'      => 'SRV-GRATIS',
            'nombre'      => 'Consulta gratuita',
            'tipo'        => 'consulta_externa',
            'precio'      => 0,
            'activo'      => true,
            'abierto_24h' => false,
            'tiene_costo' => false,
        ]);
    }

    /* =========================================================
     |  CA-01
     ========================================================= */

    public function test_ca_01_agrega_el_cargo_de_la_cita_a_la_cuenta_abierta(): void
    {
        $cita = $this->crearCita();

        $item = app(CuentaController::class)->cobrarCita($cita);

        $this->assertNotNull($item);
        $this->assertEquals($cita->id, $item->cita_id);
        $this->assertEquals(500.0, (float) $item->precio_unitario);
        $this->assertEquals($this->paciente->id, $item->cuenta->paciente_id);
        $this->assertEquals('abierta', $item->cuenta->estado);
    }

    /* =========================================================
     |  CA-02 — idempotencia
     ========================================================= */

    public function test_ca_02_es_idempotente_al_cobrar_la_misma_cita_dos_veces(): void
    {
        $cita = $this->crearCita();

        $primero = app(CuentaController::class)->cobrarCita($cita);

        $cita->refresh();
        $cita->load('paciente');

        $segundo = app(CuentaController::class)->cobrarCita($cita);

        $this->assertNotNull($primero);
        $this->assertNotNull($segundo);
        $this->assertEquals($primero->id, $segundo->id);
        $this->assertEquals(1, CuentaItem::where('cita_id', $cita->id)->count());
    }

    /* =========================================================
     |  CA-03
     ========================================================= */

    public function test_ca_03_crea_la_cuenta_abierta_si_el_paciente_no_tiene(): void
    {
        $this->assertNull($this->paciente->cuentaAbierta);

        $cita = $this->crearCita();
        app(CuentaController::class)->cobrarCita($cita);

        $this->assertNotNull($this->paciente->fresh()->cuentaAbierta);
    }

    /* =========================================================
     |  CA-04 — sin precio no se cobra
     ========================================================= */

    public function test_ca_04_no_genera_cargo_si_la_cita_no_tiene_precio(): void
    {
        if (Schema::hasColumn('citas', 'precio')) {
            $cita = $this->crearCita(['precio' => 0]);
        } else {
            $servicioGratis = $this->crearServicioGratis();
            $cita = $this->crearCita(['servicio_id' => $servicioGratis->id]);
        }

        $item = app(CuentaController::class)->cobrarCita($cita);

        $this->assertNull($item);
        $this->assertEquals(0, CuentaItem::where('cita_id', $cita->id)->count());
    }

    /* =========================================================
     |  CA-05 — reverso
     ========================================================= */

    public function test_ca_05_revierte_el_cargo_al_cancelar_la_cita(): void
    {
        $cita = $this->crearCita();
        app(CuentaController::class)->cobrarCita($cita);

        $ok = app(CuentaController::class)->revertirCargoCita($cita);

        $this->assertTrue($ok);
        $this->assertEquals(0, CuentaItem::where('cita_id', $cita->id)->count());
    }

    /* =========================================================
     |  CA-06 — no revertir si cuenta cerrada
     ========================================================= */

    public function test_ca_06_no_revierte_el_cargo_si_la_cuenta_esta_cerrada(): void
    {
        $cita = $this->crearCita();
        $item = app(CuentaController::class)->cobrarCita($cita);

        $item->cuenta->update(['estado' => 'cerrada', 'cerrada_en' => now()]);

        $ok = app(CuentaController::class)->revertirCargoCita($cita);

        $this->assertFalse($ok);
        $this->assertNotNull(CuentaItem::find($item->id));
    }
}