<?php

namespace Database\Seeders;

use App\Models\Especialidad;
use Illuminate\Database\Seeder;

class EspecialidadSeeder extends Seeder
{
    public function run(): void
    {
        $especialidades = [
            ['MED-INT', 'Medicina Interna', 'basica', 'Atención integral del adulto hospitalizado.', 30, '0284C7'],
            ['PED', 'Pediatría', 'clinica', 'Atención médica de niños y adolescentes.', 20, 'EC4899'],
            ['GIN', 'Ginecología y Obstetricia', 'clinica', 'Salud de la mujer y embarazo.', 30, '8B5CF6'],
            ['CARD', 'Cardiología', 'clinica', 'Enfermedades del corazón.', 30, 'EF4444'],
            ['CIR-GEN', 'Cirugía General', 'quirurgica', 'Cirugía del abdomen y tejidos.', 30, 'DC2626'],
            ['DERM', 'Dermatología', 'clinica', 'Enfermedades de la piel.', 20, 'F59E0B'],
        ];

        foreach ($especialidades as $e) {
            Especialidad::firstOrCreate(
                ['codigo' => $e[0]],
                [
                    'nombre' => $e[1],
                    'grupo' => $e[2],
                    'descripcion' => $e[3],
                    'duracion_consulta_default' => $e[4],
                    'color' => $e[5],
                    'activo' => true,
                ]
            );
        }

        $this->command->info(' ' . count($especialidades) . ' especialidades creadas.');
    }
}