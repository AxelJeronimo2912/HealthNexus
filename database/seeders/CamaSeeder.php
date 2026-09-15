<?php

namespace Database\Seeders;

use App\Models\Cama;
use App\Models\Servicio;
use Illuminate\Database\Seeder;

class CamaSeeder extends Seeder
{
    public function run(): void
    {
        // Mapeo de área de cama → tipo de servicio
        $areas = [
            'Hospitalización' => ['pisos' => ['Piso 1', 'Piso 2', 'Piso 3'], 'tipo_servicio' => 'hospitalizacion'],
            'Urgencias'       => ['pisos' => ['Piso 1'],                     'tipo_servicio' => 'urgencias'],
            'UCI'             => ['pisos' => ['Piso 2'],                     'tipo_servicio' => 'hospitalizacion'],
            'Pediatría'       => ['pisos' => ['Piso 3'],                     'tipo_servicio' => 'hospitalizacion'],
            'Ginecología'     => ['pisos' => ['Piso 4'],                     'tipo_servicio' => 'hospitalizacion'],
            'Cirugía'         => ['pisos' => ['Piso 2'],                     'tipo_servicio' => 'quirofano'],
        ];

        $estados = [
            'disponible', 'disponible', 'disponible',
            'ocupada', 'ocupada',
            'mantenimiento', 'limpieza',
        ];

        $total = 0;

        foreach ($areas as $area => $config) {
            // Buscar el servicio que corresponde al tipo
            $servicio = Servicio::where('tipo', $config['tipo_servicio'])->first();

            $tipoCama = match ($area) {
                'UCI'         => 'uci',
                'Pediatría'   => 'pediatrica',
                'Urgencias'   => 'urgencias',
                'Cirugía'     => 'recuperacion',
                'Aislamiento' => 'aislamiento',
                default       => 'general',
            };

            foreach ($config['pisos'] as $piso) {
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
                                'servicio_id' => $servicio?->id,   
                                'nombre'      => null,
                                'piso'        => $piso,
                                'ala'         => fake()->randomElement(['Ala Norte', 'Ala Sur']),
                                'habitacion'  => (string) $numHab,
                                'area'        => $area,
                                'tipo'        => $tipoCama,
                                'estado'      => fake()->randomElement($estados),
                                'oxigeno'     => in_array($area, ['UCI', 'Urgencias', 'Hospitalización'], true),
                                'monitor'     => in_array($area, ['UCI', 'Urgencias'], true),
                                'ventilador'  => $area === 'UCI',
                                'activo'      => true,
                            ]
                        );

                        $total++;
                    }
                }
            }
        }

        $this->command->info(" {$total} camas procesadas (creadas o actualizadas).");
    }
}