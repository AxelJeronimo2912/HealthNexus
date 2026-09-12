<?php

namespace Database\Seeders;

use App\Models\Cama;
use Illuminate\Database\Seeder;

class CamaSeeder extends Seeder
{
    public function run(): void
    {
        $areas = [
            'Hospitalización' => ['Piso 1', 'Piso 2', 'Piso 3'],
            'Urgencias'       => ['Piso 1'],
            'UCI'             => ['Piso 2'],
            'Pediatría'       => ['Piso 3'],
            'Ginecología'     => ['Piso 4'],
            'Cirugía'         => ['Piso 2'],
        ];

        $estados = [
            'disponible', 'disponible', 'disponible',
            'ocupada', 'ocupada',
            'mantenimiento', 'limpieza',
        ];

        $total = 0;

        foreach ($areas as $area => $pisos) {
            $tipo = match ($area) {
                'UCI'         => 'uci',
                'Pediatría'   => 'pediatrica',
                'Urgencias'   => 'urgencias',
                'Cirugía'     => 'recuperacion',
                'Aislamiento' => 'aislamiento',
                default       => 'general',
            };

            foreach ($pisos as $piso) {
                $habitaciones = 5;
                $camasPorHab  = 2;
                $numPiso      = (int) filter_var($piso, FILTER_SANITIZE_NUMBER_INT);

                for ($h = 1; $h <= $habitaciones; $h++) {
                    $numHab = $numPiso * 100 + $h;

                    for ($c = 1; $c <= $camasPorHab; $c++) {
                        $codigo = 'CAMA-' . $numHab . chr(64 + $c);

                        Cama::updateOrCreate(
                            ['codigo' => $codigo],
                            [
                                'nombre'     => null,
                                'piso'       => $piso,
                                'ala'        => fake()->randomElement(['Ala Norte', 'Ala Sur']),
                                'habitacion' => (string) $numHab,
                                'area'       => $area,
                                'tipo'       => $tipo,
                                'estado'     => fake()->randomElement($estados),
                                'oxigeno'    => in_array($area, ['UCI', 'Urgencias', 'Hospitalización'], true),
                                'monitor'    => in_array($area, ['UCI', 'Urgencias'], true),
                                'ventilador' => $area === 'UCI',
                                'activo'     => true,
                            ]
                        );

                        $total++;
                    }
                }
            }
        }

        $this->command->info("{$total} camas procesadas (creadas o actualizadas).");
    }
}