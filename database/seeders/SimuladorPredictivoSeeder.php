<?php

namespace Database\Seeders;

use App\Models\SimuladorPredictivoCaso;
use App\Models\User;
use Illuminate\Database\Seeder;

class SimuladorPredictivoSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        if (!$user) {
            $this->command->error('No hay usuarios.');
            return;
        }

        if (SimuladorPredictivoCaso::where('cerrado', true)->count() >= 16) {
            $this->command->info('Ya hay casos cerrados suficientes.');
            return;
        }

        // 16 casos: 9 vivos correctos, 3 falsos positivos, 1 falso negativo, 3 verdaderos positivos
        $patrones = [
            // [fc, spo2, temp, edad, pred_rf, real]
            [78,  98, 36.5, 35, 'No crítico', 'vivo'],
            [82,  97, 36.6, 42, 'No crítico', 'vivo'],
            [75,  99, 36.4, 28, 'No crítico', 'vivo'],
            [80,  98, 36.7, 50, 'No crítico', 'vivo'],
            [88,  96, 36.8, 62, 'No crítico', 'vivo'],
            [85,  97, 36.5, 45, 'No crítico', 'vivo'],
            [79,  98, 36.6, 33, 'No crítico', 'vivo'],
            [83,  97, 36.4, 55, 'No crítico', 'vivo'],
            [81,  98, 36.6, 40, 'No crítico', 'vivo'],

            [110, 93, 37.5, 55, 'Crítico', 'vivo'],
            [105, 94, 37.2, 48, 'Crítico', 'vivo'],
            [115, 92, 37.8, 60, 'Crítico', 'vivo'],

            [90,  95, 37.0, 70, 'No crítico', 'fallecio'],

            [125, 88, 38.5, 72, 'Crítico', 'fallecio'],
            [130, 87, 38.9, 68, 'Crítico', 'fallecio'],
            [128, 89, 38.3, 75, 'Crítico', 'fallecio'],
        ];

        foreach ($patrones as $i => [$fc, $spo2, $temp, $edad, $pred, $real]) {
            SimuladorPredictivoCaso::create([
                'user_id'             => $user->id,
                'fc'                  => $fc,
                'spo2'                => $spo2,
                'temp'                => $temp,
                'edad'                => $edad,
                'prob_critico'        => $pred === 'Crítico' ? 0.75 : 0.25,
                'riesgo_svm'          => $pred,
                'recomendacion_arbol' => $pred,
                'voto_rf'             => $pred,
                'spo2_esperado'       => 97,
                'diagnostico_final'   => $real,
                'costo_real'          => 15000 + $i * 200,
                'costo_predicho'      => 14500 + $i * 210,
                'dias_real'           => 3 + ($i % 4),
                'dias_predicho'       => 3 + ($i % 5),
                'cerrado'             => true,
            ]);
        }

        $this->command->info('✓ 16 casos cerrados del simulador predictivo.');
    }
}