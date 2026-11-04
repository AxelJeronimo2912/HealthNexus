
<?php


use App\Models\User;
use App\Models\Paciente;
use App\Models\Cuenta;
use App\Models\CuentaItem;
use App\Models\Pago;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class GestionPagosCuentaTest extends TestCase
{
    use RefreshDatabase;

    protected User $usuario;
    protected Paciente $paciente;
    protected Cuenta $cuenta;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::findOrCreate('cuentas.ver', 'web');

        $this->usuario = User::create([
            'name' => 'Administrador',
            'email' => 'admin' . Str::random(8) . '@test.com',
            'password' => bcrypt('password'),
            'nombre' => 'Administrador',
            'apellido_paterno' => 'Test',
            'apellido_materno' => 'Demo',
            'activo' => true,
        ]);

        $this->usuario->givePermissionTo('cuentas.ver');

        $this->actingAs($this->usuario);

        $this->paciente = Paciente::create([
            'nombre' => 'Paciente',
            'apellido_paterno' => 'Prueba',
            'apellido_materno' => 'Demo',
            'fecha_nacimiento' => '1990-01-01',
            'sexo' => 'hombre',
            'curp' => strtoupper(Str::random(18)),
            'telefono_principal' => '1234567890',
            'activo' => true,
        ]);

        $this->cuenta = Cuenta::create([
            'paciente_id' => $this->paciente->id,
            'folio' => 'CTA-' . Str::upper(Str::random(10)),
            'estado' => 'abierta',
            'subtotal' => 1000,
            'total' => 1000,
            'pagado' => 0,
            'saldo' => 1000,
        ]);

        CuentaItem::create([
            'cuenta_id' => $this->cuenta->id,
            'user_id' => $this->usuario->id,
            'concepto' => 'Consulta general',
            'cantidad' => 1,
            'precio_unitario' => 1000,
            'importe' => 1000,
        ]);

        $this->cuenta->refresh();
    }

    // 1. Registrar un pago correctamente
    public function test_registra_un_pago_correctamente(): void
    {
        $response = $this->post(
            route('cuentas.pagos.store', $this->cuenta),
            [
                'monto' => 300,
                'metodo' => 'efectivo',
            ]
        );

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('pagos', [
            'cuenta_id' => $this->cuenta->id,
            'monto' => 300,
            'metodo' => 'efectivo',
            'estado' => 'aplicado',
        ]);

        $this->assertEquals(
            700,
            (float) $this->cuenta->fresh()->saldo
        );
    }

    // 2. Rechazar pagos superiores al saldo
    public function test_rechaza_un_pago_superior_al_saldo(): void
    {
        $response = $this->from('/cuentas')->post(
            route('cuentas.pagos.store', $this->cuenta),
            [
                'monto' => 1500,
                'metodo' => 'efectivo',
            ]
        );

        $response->assertRedirect('/cuentas');
        $response->assertSessionHas('error');

        $this->assertDatabaseCount('pagos', 0);

        $this->assertEquals(
            1000,
            (float) $this->cuenta->fresh()->saldo
        );
    }

    // 3. Validar los datos obligatorios del pago
    public function test_valida_los_datos_obligatorios_del_pago(): void
    {
        $response = $this->post(
            route('cuentas.pagos.store', $this->cuenta),
            [
                'monto' => '',
                'metodo' => 'invalido',
            ]
        );

        $response->assertSessionHasErrors([
            'monto',
            'metodo',
        ]);

        $this->assertDatabaseCount('pagos', 0);
    }

    // 4. Cancelar un pago y recalcular el saldo
    public function test_cancela_un_pago_y_recalcula_el_saldo(): void
    {
        $pago = $this->crearPago(300);

        $response = $this->from('/cuentas')->post(
            route('cuentas.pagos.cancelar', $pago),
            [
                'motivo_cancelacion' => 'Pago registrado incorrectamente',
            ]
        );

        $response->assertRedirect('/cuentas');

        $this->assertDatabaseHas('pagos', [
            'id' => $pago->id,
            'estado' => 'cancelado',
            'motivo_cancelacion' => 'Pago registrado incorrectamente',
        ]);

        $this->assertEquals(
            1000,
            (float) $this->cuenta->fresh()->saldo
        );

        $this->assertEquals(
            0,
            (float) $this->cuenta->fresh()->pagado
        );
    }

    // 5. No permitir cancelar un pago dos veces
    public function test_no_permite_cancelar_un_pago_dos_veces(): void
    {
        $pago = $this->crearPago(300);

        $this->post(
            route('cuentas.pagos.cancelar', $pago),
            [
                'motivo_cancelacion' => 'Error de registro',
            ]
        );

        $response = $this->from('/cuentas')->post(
            route('cuentas.pagos.cancelar', $pago),
            [
                'motivo_cancelacion' => 'Segundo intento',
            ]
        );

        $response->assertRedirect('/cuentas');
        $response->assertSessionHas('error');

        $this->assertEquals(
            'cancelado',
            $pago->fresh()->estado
        );
    }

    // 6. Generar recibo PDF
    public function test_genera_el_recibo_pdf_de_un_pago(): void
    {
        $pago = $this->crearPago(300);

        $response = $this->get(
            route('cuentas.pagos.recibo', $pago)
        );

        $response->assertOk();

        $response->assertHeader(
            'Content-Type',
            'application/pdf'
        );
    }

    // 7. Cerrar automáticamente la cuenta al liquidar el saldo
    public function test_cierra_la_cuenta_cuando_el_saldo_es_cero(): void
    {
        $response = $this->post(
            route('cuentas.pagos.store', $this->cuenta),
            [
                'monto' => 1000,
                'metodo' => 'efectivo',
            ]
        );

        $response->assertSessionHasNoErrors();

        $cuenta = $this->cuenta->fresh();

        $this->assertEquals(0, (float) $cuenta->saldo);
        $this->assertEquals(1000, (float) $cuenta->pagado);
        $this->assertEquals('cerrada', $cuenta->estado);
        $this->assertNotNull($cuenta->cerrada_en);
    }

    // 8. No permitir cerrar una cuenta con saldo pendiente
    public function test_no_permite_cerrar_una_cuenta_con_saldo_pendiente(): void
    {
        $response = $this->from('/cuentas')->post(
            route('cuentas.cerrar', $this->cuenta)
        );

        $response->assertRedirect('/cuentas');
        $response->assertSessionHas('error');

        $this->assertEquals(
            'abierta',
            $this->cuenta->fresh()->estado
        );
    }

    // 9. No permitir registrar pagos en cuentas cerradas
    public function test_no_permite_registrar_pagos_en_cuentas_cerradas(): void
    {
        $this->cuenta->update([
            'estado' => 'cerrada',
            'cerrada_en' => now(),
        ]);

        $response = $this->from('/cuentas')->post(
            route('cuentas.pagos.store', $this->cuenta),
            [
                'monto' => 100,
                'metodo' => 'efectivo',
            ]
        );

        $response->assertRedirect('/cuentas');
        $response->assertSessionHas('error');

        $this->assertDatabaseCount('pagos', 0);
    }

    // 10. Validar motivo obligatorio de cancelación
    public function test_exige_motivo_para_cancelar_un_pago(): void
    {
        $pago = $this->crearPago(300);

        $response = $this->post(
            route('cuentas.pagos.cancelar', $pago),
            [
                'motivo_cancelacion' => '',
            ]
        );

        $response->assertSessionHasErrors([
            'motivo_cancelacion',
        ]);

        $this->assertEquals(
            'aplicado',
            $pago->fresh()->estado
        );
    }

    // Método auxiliar para crear pagos
    private function crearPago(float $monto): Pago
    {
        return $this->cuenta->pagos()->create([
            'user_id' => $this->usuario->id,
            'folio' => Pago::generarFolio(),
            'monto' => $monto,
            'metodo' => 'efectivo',
            'pagado_en' => now(),
            'estado' => 'aplicado',
        ]);
    }
}
