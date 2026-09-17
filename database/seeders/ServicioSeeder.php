<?php

namespace Database\Seeders;

use App\Models\Servicio;
use Illuminate\Database\Seeder;

class ServicioSeeder extends Seeder
{
    public function run(): void
    {
        $servicios = [
            [
                'codigo' => 'CONS-EXT',
                'nombre' => 'Consulta Externa',
                'tipo' => 'consulta_externa',
                'ubicacion' => 'Edificio A, planta baja',
                'piso' => 'Piso 1',
                'ala' => 'Ala Norte',
                'hora_apertura' => '08:00',
                'hora_cierre' => '20:00',
                'capacidad' => 10,
                'extension_telefonica' => '1001',
                'descripcion' => 'Atención médica ambulatoria programada.',
            ],
            [
                'codigo' => 'URG',
                'nombre' => 'Urgencias',
                'tipo' => 'urgencias',
                'ubicacion' => 'Edificio principal, planta baja',
                'piso' => 'Piso 1',
                'abierto_24h' => true,
                'capacidad' => 15,
                'extension_telefonica' => '2000',
                'descripcion' => 'Atención médica de urgencias 24 horas.',
            ],
            [
                'codigo' => 'HOSP',
                'nombre' => 'Hospitalización',
                'tipo' => 'hospitalizacion',
                'ubicacion' => 'Edificio B',
                'piso' => 'Piso 2',
                'abierto_24h' => true,
                'capacidad' => 50,
                'extension_telefonica' => '3000',
                'descripcion' => 'Área de internamiento de pacientes.',
            ],
            [
                'codigo' => 'QUIR',
                'nombre' => 'Quirófano',
                'tipo' => 'quirofano',
                'ubicacion' => 'Edificio B, planta alta',
                'piso' => 'Piso 3',
                'hora_apertura' => '07:00',
                'hora_cierre' => '19:00',
                'capacidad' => 4,
                'extension_telefonica' => '4000',
                'descripcion' => 'Salas de cirugía.',
            ],
            [
                'codigo' => 'FARM',
                'nombre' => 'Farmacia',
                'tipo' => 'farmacia',
                'ubicacion' => 'Edificio principal, planta baja',
                'piso' => 'Piso 1',
                'hora_apertura' => '07:00',
                'hora_cierre' => '22:00',
                'extension_telefonica' => '5000',
                'descripcion' => 'Dispensación de medicamentos.',
            ],
            [
                'codigo' => 'ENF',
                'nombre' => 'Enfermería',
                'tipo' => 'enfermeria',
                'abierto_24h' => true,
                'extension_telefonica' => '6000',
                'descripcion' => 'Atención de enfermería.',
            ],
            [
                'codigo' => 'LAB',
                'nombre' => 'Laboratorio Clínico',
                'tipo' => 'laboratorio',
                'piso' => 'Piso 2',
                'hora_apertura' => '06:00',
                'hora_cierre' => '18:00',
                'extension_telefonica' => '7000',
                'descripcion' => 'Análisis clínicos.',
            ],
            [
                'codigo' => 'IMG',
                'nombre' => 'Imagenología',
                'tipo' => 'imagenologia',
                'piso' => 'Piso 1',
                'hora_apertura' => '08:00',
                'hora_cierre' => '20:00',
                'extension_telefonica' => '8000',
                'descripcion' => 'Rayos X, ultrasonido, TAC, RM.',
            ],
        ];

        foreach ($servicios as $s) {
            Servicio::firstOrCreate(['codigo' => $s['codigo']], $s);
        }

        $this->command->info(' ' . count($servicios) . ' servicios creados.');
    }
}