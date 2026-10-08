<?php

namespace Database\Seeders;

use App\Models\Cita;
use App\Models\Especialidad;
use App\Models\Paciente;
use App\Models\Servicio;
use App\Models\SignoVital;
use App\Models\Turno;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class AgendaDemoSeeder extends Seeder
{
    private const NOTA_SIGNOS = 'Datos ficticios de demostración creados por AgendaDemoSeeder.';

    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new RuntimeException('AgendaDemoSeeder solo se puede ejecutar en los entornos local o testing.');
        }

        DB::transaction(function (): void {
            $medico = User::role('medico')->where('activo', true)->orderBy('id')->first();
            $turno = Turno::where('codigo', 'MAT')->where('activo', true)->first();
            $especialidad = Especialidad::where('codigo', 'MED-INT')->where('activo', true)->first();
            $servicio = Servicio::where('codigo', 'CONS-EXT')->where('activo', true)->first();
            $pacientes = Paciente::where('activo', true)->orderBy('id')->limit(25)->get();

            if (! $medico || ! $turno || ! $especialidad || ! $servicio || $pacientes->isEmpty()) {
                throw new RuntimeException(
                    'Faltan datos base para la agenda. Ejecuta primero los seeders de usuarios, turnos, especialidades, servicios y pacientes.'
                );
            }

            $ahora = now();
            $fechaInicio = $ahora->copy()->subMonth()->toDateString();

            $medico->turnos()->syncWithoutDetaching([
                $turno->id => [
                    'dia_semana' => null,
                    'fecha_inicio' => $fechaInicio,
                    'fecha_fin' => null,
                    'area' => $servicio->nombre,
                    'activo' => true,
                    'notas' => 'Horario de demostración de AgendaDemoSeeder.',
                ],
            ]);

            DB::table('especialidad_user')->updateOrInsert(
                ['especialidad_id' => $especialidad->id, 'user_id' => $medico->id],
                ['activo' => true, 'updated_at' => $ahora]
            );

            DB::table('especialidad_servicio')->updateOrInsert(
                ['especialidad_id' => $especialidad->id, 'servicio_id' => $servicio->id],
                ['activo' => true, 'updated_at' => $ahora]
            );

            $medico->servicios()->syncWithoutDetaching([
                $servicio->id => [
                    'rol_en_servicio' => 'Médico de consulta externa',
                    'fecha_inicio' => $fechaInicio,
                    'activo' => true,
                ],
            ]);

            $triages = ['verde', 'azul', 'amarillo', 'verde', 'azul'];
            $signosPorPaciente = [];

            foreach ($pacientes as $indice => $paciente) {
                $triage = $triages[$indice % count($triages)];
                $signo = SignoVital::firstOrCreate(
                    [
                        'paciente_id' => $paciente->id,
                        'user_id' => $medico->id,
                        'notas' => self::NOTA_SIGNOS,
                    ],
                    [
                        'temperatura' => 36.5 + ($indice % 5) * 0.1,
                        'frecuencia_cardiaca' => 68 + ($indice % 5) * 4,
                        'frecuencia_respiratoria' => 14 + ($indice % 4),
                        'presion_arterial' => ['118/76', '120/80', '124/82', '116/74', '122/78'][$indice % 5],
                        'saturacion_oxigeno' => 96 + ($indice % 3),
                        'glucosa' => 88 + ($indice % 5) * 3,
                        'peso' => 55 + ($indice % 20),
                        'talla' => 1.50 + ($indice % 25) * 0.01,
                        'escala_dolor' => $indice % 3,
                        'triage' => $triage,
                        'triage_manual' => false,
                        'motivo_consulta' => 'Registro clínico ficticio para demostración.',
                    ]
                );

                $signosPorPaciente[$paciente->id] = $signo;
            }

            $adminId = User::role('administrador')->orderBy('id')->value('id');
            $fecha = Carbon::today()->startOfWeek(Carbon::MONDAY);
            $fechasCitas = [];

            for ($dia = 0; count($fechasCitas) < 10; $dia++) {
                $fechaCita = $fecha->copy()->addDays($dia);

                if ($fechaCita->isWeekday()) {
                    $fechasCitas[] = $fechaCita;
                }
            }

            foreach ($pacientes->take(10)->values() as $indice => $paciente) {
                $fechaHora = $fechasCitas[$indice]
                    ->copy()
                    ->setTime(9 + ($indice % 4), 0);
                $estado = $indice === 4
                    ? 'cancelada'
                    : ($fechaHora->isPast() ? 'atendida' : 'programada');
                $nota = sprintf(
                    'Dato ficticio de demostración creado por AgendaDemoSeeder (cita %02d).',
                    $indice + 1
                );

                Cita::withoutEvents(function () use (
                    $paciente,
                    $medico,
                    $turno,
                    $especialidad,
                    $servicio,
                    $adminId,
                    $fechaHora,
                    $estado,
                    $nota,
                    $signosPorPaciente,
                    $indice
                ): void {
                    Cita::updateOrCreate(
                        ['notas' => $nota],
                        [
                            'paciente_id' => $paciente->id,
                            'medico_id' => $medico->id,
                            'turno_id' => $turno->id,
                            'signo_vital_id' => $signosPorPaciente[$paciente->id]->id,
                            'creado_por' => $adminId,
                            'especialidad_id' => $especialidad->id,
                            'servicio_id' => $servicio->id,
                            'fecha_hora' => $fechaHora,
                            'duracion_minutos' => 30,
                            'estado' => $estado,
                            'triage_al_momento' => $signosPorPaciente[$paciente->id]->triage,
                            'motivo' => sprintf('Consulta de seguimiento ficticia %02d.', $indice + 1),
                        ]
                    );
                });
            }

            $this->command?->info(sprintf(
                'Agenda de demostración lista: %d signos vitales y %d citas.',
                count($signosPorPaciente),
                min(10, $pacientes->count())
            ));
        });
    }
}
