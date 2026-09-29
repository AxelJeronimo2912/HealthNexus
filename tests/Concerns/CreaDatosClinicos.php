<?php

namespace Tests\Concerns;

use App\Models\Cita;
use App\Models\Consulta;
use App\Models\Diagnostico;
use App\Models\Lote;
use App\Models\Medicamento;
use App\Models\Paciente;
use App\Models\SignoVital;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Str;
use Spatie\Permission\PermissionRegistrar;

/**
 * Utilidades para preparar datos clínicos en las pruebas de consultas y PDF.
 *
 * Los modelos del proyecto no usan HasFactory (salvo User), así que los
 * registros se crean directamente con Model::create().
 */
trait CreaDatosClinicos
{
    protected function prepararRolesYPermisos(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // El orden importa: permisos -> roles -> asignación
        $this->seed(PermissionSeeder::class);
        $this->seed(RoleSeeder::class);
        $this->seed(RolePermissionSeeder::class);
    }

    protected function crearUsuarioConRol(string $rol, array $atributos = []): User
    {
        $user = User::factory()->create($atributos);
        $user->assignRole($rol);

        return $user;
    }

    protected function crearMedico(array $atributos = []): User
    {
        return $this->crearUsuarioConRol('medico', array_merge([
            'nombre'             => 'Laura',
            'apellido_paterno'   => 'Ramírez',
            'apellido_materno'   => 'Soto',
            'cedula_profesional' => '12345678',
        ], $atributos));
    }

    protected function crearPaciente(array $atributos = []): Paciente
    {
        return Paciente::create(array_merge([
            'nombre'           => 'Juan',
            'apellido_paterno' => 'Pérez',
            'apellido_materno' => 'López',
            'fecha_nacimiento' => '1990-05-15',
            'sexo'             => 'hombre',
            'curp'             => Str::upper(Str::random(18)),
            'tipo_sanguineo'   => 'O+',
            'alergias'         => 'Penicilina',
        ], $atributos));
    }

    protected function crearCita(User $medico, ?Paciente $paciente = null, string $estado = 'confirmada'): Cita
    {
        return Cita::create([
            'paciente_id'      => ($paciente ?? $this->crearPaciente())->id,
            'medico_id'        => $medico->id,
            'fecha_hora'       => now()->addHour(),
            'duracion_minutos' => 30,
            'estado'           => $estado,
            'motivo'           => 'Dolor de cabeza persistente',
        ]);
    }

    protected function crearSignoVital(Paciente $paciente, ?Carbon $creadoEn = null): SignoVital
    {
        $signo = SignoVital::create([
            'paciente_id'         => $paciente->id,
            'temperatura'         => 36.8,
            'frecuencia_cardiaca' => 78,
            'presion_arterial'    => '120/80',
            'saturacion_oxigeno'  => 98,
            'peso'                => 70,
            'talla'               => 1.75,
            'triage'              => 'verde',
        ]);

        if ($creadoEn) {
            $signo->forceFill(['created_at' => $creadoEn])->save();
        }

        return $signo;
    }

    /**
     * Crea un medicamento activo. Si $stock > 0 también crea un lote vigente
     * con esa misma cantidad, porque el descuento real de inventario (FEFO)
     * se hace sobre los lotes y la validación previa sobre stock_actual.
     */
    protected function crearMedicamento(int $stock = 10, array $atributos = []): Medicamento
    {
        $medicamento = Medicamento::create(array_merge([
            'nombre'        => 'Paracetamol',
            'presentacion'  => 'Tableta',
            'concentracion' => '500 mg',
            'stock_actual'  => $stock,
            'activo'        => true,
        ], $atributos));

        if ($stock > 0) {
            Lote::create([
                'medicamento_id'      => $medicamento->id,
                'codigo_lote'         => 'L-' . Str::upper(Str::random(6)),
                'fecha_caducidad'     => now()->addYear(),
                'fecha_ingreso'       => now()->subDay(),
                'cantidad_inicial'    => $stock,
                'cantidad_disponible' => $stock,
            ]);
        }

        return $medicamento;
    }

    protected function crearDiagnostico(string $codigo = 'J00', string $nombre = 'Rinofaringitis aguda'): Diagnostico
    {
        return Diagnostico::create([
            'codigo' => $codigo,
            'nombre' => $nombre,
            'grupo'  => 'Respiratorias',
            'activo' => true,
        ]);
    }

    /**
     * Datos válidos para el formulario de consulta (POST/PUT).
     */
    protected function datosConsulta(array $sobrescribir = []): array
    {
        return array_merge([
            'subjetivo'           => 'Paciente refiere cefalea de 3 días de evolución.',
            'objetivo'            => 'Consciente, orientado, sin datos de focalización.',
            'analisis'            => 'Cefalea tensional.',
            'plan'                => 'Analgésico y control en 7 días.',
            'temperatura'         => 36.8,
            'frecuencia_cardiaca' => 78,
            'presion_arterial'    => '120/80',
            'saturacion_oxigeno'  => 98,
            'peso'                => 70,
            'talla'               => 1.75,
        ], $sobrescribir);
    }

    /**
     * Crea una consulta ya guardada (sin pasar por el controlador).
     */
    protected function crearConsulta(User $medico, array $atributos = [], ?Paciente $paciente = null): Consulta
    {
        $paciente ??= $this->crearPaciente();
        $cita = $this->crearCita($medico, $paciente, 'atendida');

        return Consulta::create(array_merge($this->datosConsulta(), [
            'cita_id'     => $cita->id,
            'paciente_id' => $paciente->id,
            'medico_id'   => $medico->id,
            'estado'      => 'finalizada',
        ], $atributos));
    }
}