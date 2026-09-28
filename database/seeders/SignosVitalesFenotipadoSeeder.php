<?php

namespace Database\Seeders;

use App\Models\Paciente;
use App\Models\SignoVital;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class SignosVitalesFenotipadoSeeder extends Seeder
{
    public function run(): void
    {
        // Buscar un usuario para asignar los signos (enfermero/medico)
        $user = User::first();
        if (!$user) {
            $this->command->error('No hay usuarios en la BD. Crea uno primero.');
            return;
        }

        // Traer pacientes activos que NO tengan signos vitales aún
        $pacientes = Paciente::where('activo', true)
            ->whereDoesntHave('signosVitales')
            ->inRandomOrder()
            ->limit(60)
            ->get();

        if ($pacientes->isEmpty()) {
            $this->command->warn('Todos los pacientes ya tienen signos vitales o no hay pacientes activos.');
            return;
        }

        // 4 perfiles clínicos: [fc_base, temp_base, spo2_base, var_fc, var_spo2, triage]
        $perfiles = [
            // 0. Respondedor Rápido (joven, estable, triaje leve)
            ['fc' => 78,  'temp' => 36.5, 'spo2' => 97, 'var_fc' => 4,  'var_spo2' => 0.8, 'triage' => 'verde'],
            // 1. Crónico Complejo (mayor, moderadamente inestable)
            ['fc' => 88,  'temp' => 36.8, 'spo2' => 94, 'var_fc' => 8,  'var_spo2' => 1.5, 'triage' => 'amarillo'],
            // 2. Inestable Oculto (triaje leve pero MUCHA variabilidad)
            ['fc' => 105, 'temp' => 37.4, 'spo2' => 91, 'var_fc' => 18, 'var_spo2' => 3.5, 'triage' => 'verde'],
            // 3. Monitoreo Intensivo (crítico evidente)
            ['fc' => 118, 'temp' => 38.2, 'spo2' => 88, 'var_fc' => 14, 'var_spo2' => 3.0, 'triage' => 'naranja'],
        ];

        $totalSignos = 0;

        // Repartir pacientes en los 4 perfiles de forma round-robin
        foreach ($pacientes as $i => $paciente) {
            $perfil = $perfiles[$i % 4];

            // 3 a 5 mediciones por paciente
            $numMediciones = random_int(3, 5);

            for ($m = 0; $m < $numMediciones; $m++) {
                $diasAtras = random_int(1, 60);

                $fc   = (int) round($this->gauss($perfil['fc'],  $perfil['var_fc']));
                $temp = round($this->gauss($perfil['temp'], 0.3), 1);
                $spo2 = (int) round($this->gauss($perfil['spo2'], $perfil['var_spo2']));

                // Límites clínicos razonables
                $fc   = max(40, min(200, $fc));
                $temp = max(35.0, min(42.0, $temp));
                $spo2 = max(70, min(100, $spo2));

                SignoVital::create([
                    'paciente_id'              => $paciente->id,
                    'user_id'                  => $user->id,
                    'temperatura'              => $temp,
                    'frecuencia_cardiaca'      => $fc,
                    'frecuencia_respiratoria'  => random_int(12, 28),
                    'presion_arterial'         => random_int(100, 140) . '/' . random_int(60, 90),
                    'saturacion_oxigeno'       => $spo2,
                    'glucosa'                  => random_int(80, 180),
                    'peso'                     => round(60 + random_int(-15, 25), 1),
                    'talla'                    => round(1.55 + random_int(-15, 20) / 100, 2),
                    'escala_dolor'             => random_int(0, 8),
                    'triage'                   => $perfil['triage'],
                    'triage_manual'            => true,
                    'motivo_consulta'          => 'Datos sintéticos para fenotipado',
                    'notas'                    => 'Generado por SignosVitalesFenotipadoSeeder',
                    'created_at'               => Carbon::now()->subDays($diasAtras)->subHours(random_int(0, 23)),
                    'updated_at'               => Carbon::now()->subDays($diasAtras)->subHours(random_int(0, 23)),
                ]);

                $totalSignos++;
            }
        }

        $this->command->info('✓ Signos vitales generados:');
        $this->command->line("   Pacientes procesados: {$pacientes->count()}");
        $this->command->line("   Signos vitales creados: {$totalSignos}");
    }

    /**
     * Distribución normal (Box-Muller) para dar realismo.
     */
    private function gauss(float $media, float $desviacion): float
    {
        $u1 = random_int(1, PHP_INT_MAX) / PHP_INT_MAX;
        $u2 = random_int(1, PHP_INT_MAX) / PHP_INT_MAX;
        $z = sqrt(-2 * log($u1)) * cos(2 * M_PI * $u2);
        return $media + $z * $desviacion;
    }
}