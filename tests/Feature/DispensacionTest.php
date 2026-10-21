<?php

namespace Tests\Feature;

use App\Models\Cita;
use App\Models\Consulta;
use App\Models\Lote;
use App\Models\Medicamento;
use App\Models\Paciente;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class DispensacionTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $medico;
    protected Paciente $paciente;
    protected Medicamento $medicamento;
    protected Lote $lote;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Configurar roles y permisos
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $this->seed(PermissionSeeder::class);
        $this->seed(RoleSeeder::class);
        $this->seed(RolePermissionSeeder::class);

        // 2. Crear usuarios
        $this->admin  = $this->crearUsuario('administrador');
        $this->medico = $this->crearUsuario('medico');

        $this->medico->givePermissionTo('dispensaciones.ver');

        // 3. Crear paciente
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

        // 4. Crear medicamento y lote con stock
        $this->medicamento = Medicamento::create([
            'nombre' => 'Paracetamol',
            'sustancia_activa' => 'Paracetamol',
            'presentacion' => 'Tableta',
            'concentracion' => '500 mg',
            'unidad_medida' => 'pieza',
            'stock_minimo' => 10,
            'stock_maximo' => 100,
            'activo' => true,
        ]);

        $this->lote = Lote::create([
            'medicamento_id' => $this->medicamento->id,
            'codigo_lote' => 'LOTE-TEST-01',
            'fecha_caducidad' => now()->addYear(),
            'fecha_ingreso' => now(),
            'cantidad_inicial' => 50,
            'cantidad_disponible' => 50,
            'activo' => true,
        ]);
    }

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

    private function crearConsultaConReceta(): Consulta
    {
        // 1. Crear la cita (obligatoria)
        $cita = Cita::create([
            'paciente_id' => $this->paciente->id,
            'medico_id' => $this->medico->id,
            'creado_por' => $this->admin->id,
            'fecha_hora' => now(),
            'duracion_minutos' => 30,
            'estado' => 'atendida',
            'motivo' => 'Consulta de prueba',
        ]);

        // 2. Crear la consulta
        $consulta = Consulta::create([
            'cita_id' => $cita->id,
            'paciente_id' => $this->paciente->id,
            'medico_id' => $this->medico->id,
            'estado' => 'finalizada',
            'finalizada_en' => now(),
            'dispensada' => false,
        ]);

        // 3. Recetar 1 unidad
        $consulta->medicamentos()->attach($this->medicamento->id, [
            'dosis' => '1 tableta',
            'via' => 'Oral',
            'frecuencia' => 'Cada 8 horas',
            'duracion' => '3 días',
            'indicaciones' => 'Tomar con alimentos',
        ]);

        $this->lote->decrement('cantidad_disponible', 1); 

        return $consulta;
    }

    /*
    |--------------------------------------------------------------------------
    | PRUEBAS DE DISPENSACIÓN
    |--------------------------------------------------------------------------
    */

    public function test_medico_puede_dispensar_una_receta_y_descuenta_stock(): void
    {
        $consulta = $this->crearConsultaConReceta();
        
        // El stock ya está en 49 por el paso anterior
        $this->assertEquals(49, $this->lote->fresh()->cantidad_disponible);

        $this->actingAs($this->medico)
            ->post(route('dispensaciones.dispensar', $consulta), [
                'notas_dispensacion' => 'Prueba de dispensación'
            ])
            ->assertRedirect();

        // 1. Verificar que la consulta se marcó como dispensada
        $this->assertTrue($consulta->fresh()->dispensada);
        $this->assertEquals($this->medico->id, $consulta->fresh()->dispensada_por);

        // 2. El stock baja otra vez (a 48) porque la lógica del controlador resta al dispensar
        $this->assertEquals(48, $this->lote->fresh()->cantidad_disponible);
    }

        public function test_no_se_puede_dispensar_una_receta_sin_stock(): void
    {
        $consulta = $this->crearConsultaConReceta();

        // Agotamos el stock del lote
        $this->lote->update(['cantidad_disponible' => 0]);

        $this->actingAs($this->medico)
            ->post(route('dispensaciones.dispensar', $consulta))
            ->assertRedirect() 
            ->assertSessionHas('error'); // Y que contenga un mensaje de error

        $this->assertFalse($consulta->fresh()->dispensada);
    }

    /*
    |--------------------------------------------------------------------------
    | PRUEBAS DE REVERSIÓN
    |--------------------------------------------------------------------------
    */

    public function test_administrador_puede_revertir_dispensacion_y_devuelve_stock(): void
    {
        $consulta = $this->crearConsultaConReceta();
        
        // 1. Primero dispensamos la receta (baja el stock a 48)
        $this->actingAs($this->medico)
            ->post(route('dispensaciones.dispensar', $consulta));

        // Verificamos que SÍ se dispensó
        $this->assertTrue($consulta->fresh()->dispensada);
        $this->assertEquals(48, $this->lote->fresh()->cantidad_disponible);

        // 2. Ahora el admin revierte
        $this->actingAs($this->admin)
            ->post(route('dispensaciones.revertir', $consulta))
            ->assertRedirect();

        // 3. Verificar que la consulta ya no está dispensada
        $this->assertFalse($consulta->fresh()->dispensada);

        // 4. Verificar que el stock volvió a subir (de 48 a 49)
        $this->assertEquals(49, $this->lote->fresh()->cantidad_disponible);
    }

    public function test_medico_no_puede_revertir_una_dispensacion(): void
    {
        $consulta = $this->crearConsultaConReceta();

        // Dispensamos
        $this->actingAs($this->medico)
            ->post(route('dispensaciones.dispensar', $consulta));

        // Intentamos revertir con el médico
        $this->actingAs($this->medico)
            ->post(route('dispensaciones.revertir', $consulta))
            ->assertForbidden(); // El middleware o controlador devuelve 403
    }

    public function test_no_se_puede_revertir_una_receta_que_no_ha_sido_dispensada(): void
    {
        $consulta = $this->crearConsultaConReceta();
        
        // La receta nace en false (no dispensada)
        $this->actingAs($this->admin)
            ->post(route('dispensaciones.revertir', $consulta))
            ->assertSessionHas('error');
    }
}