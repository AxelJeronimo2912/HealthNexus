<?php

namespace Database\Seeders;

use App\Models\Servicio;
use Illuminate\Database\Seeder;

class ServiciosEstudioSeeder extends Seeder
{
    public function run(): void
    {
        // ================= IMAGENOLOGÍA =================
        $imagenes = [
            ['codigo' => 'IMG-RX-TORAX',    'nombre' => 'Radiografía de Tórax PA',        'precio' => 450.00,  'precio_descripcion' => 'Incluye interpretación'],
            ['codigo' => 'IMG-RX-COLUMNA',  'nombre' => 'Radiografía de Columna Lumbar',  'precio' => 600.00,  'precio_descripcion' => '2 proyecciones'],
            ['codigo' => 'IMG-RX-EXTREM',   'nombre' => 'Radiografía de Extremidades',    'precio' => 400.00,  'precio_descripcion' => 'Por región'],
            ['codigo' => 'IMG-USG-ABD',     'nombre' => 'Ultrasonido Abdominal',          'precio' => 850.00,  'precio_descripcion' => 'Ayuno de 8 horas'],
            ['codigo' => 'IMG-USG-PELV',    'nombre' => 'Ultrasonido Pélvico',            'precio' => 800.00,  'precio_descripcion' => 'Incluye transvaginal'],
            ['codigo' => 'IMG-USG-OBST',    'nombre' => 'Ultrasonido Obstétrico',         'precio' => 900.00,  'precio_descripcion' => 'Incluye reporte'],
            ['codigo' => 'IMG-TAC-CRANEO',  'nombre' => 'TAC de Cráneo Simple',           'precio' => 2500.00, 'precio_descripcion' => 'Sin contraste'],
            ['codigo' => 'IMG-TAC-ABD',     'nombre' => 'TAC Abdominal Contrastada',      'precio' => 3800.00, 'precio_descripcion' => 'Incluye contraste'],
            ['codigo' => 'IMG-RM-CRANEO',   'nombre' => 'Resonancia Magnética de Cráneo', 'precio' => 4500.00, 'precio_descripcion' => 'Sin contraste'],
            ['codigo' => 'IMG-RM-COLUMNA',  'nombre' => 'Resonancia Magnética de Columna', 'precio' => 5000.00, 'precio_descripcion' => 'Región lumbar'],
            ['codigo' => 'IMG-MAMOGRAFIA',  'nombre' => 'Mamografía Bilateral',           'precio' => 950.00,  'precio_descripcion' => 'Incluye interpretación'],
            ['codigo' => 'IMG-DENSITO',     'nombre' => 'Densitometría Ósea',             'precio' => 1100.00, 'precio_descripcion' => 'Columna y cadera'],
        ];

        // ================= LABORATORIO =================
        $laboratorio = [
            ['codigo' => 'LAB-BH',          'nombre' => 'Biometría Hemática Completa',    'precio' => 180.00,  'precio_descripcion' => 'Ayuno no requerido'],
            ['codigo' => 'LAB-QS',          'nombre' => 'Química Sanguínea 6 Elementos',  'precio' => 250.00,  'precio_descripcion' => 'Ayuno de 8 horas'],
            ['codigo' => 'LAB-QS12',        'nombre' => 'Química Sanguínea 12 Elementos', 'precio' => 380.00,  'precio_descripcion' => 'Ayuno de 8 horas'],
            ['codigo' => 'LAB-EGO',         'nombre' => 'Examen General de Orina',        'precio' => 120.00,  'precio_descripcion' => 'Primera orina del día'],
            ['codigo' => 'LAB-PERFIL-LIP',  'nombre' => 'Perfil de Lípidos',              'precio' => 320.00,  'precio_descripcion' => 'Ayuno de 12 horas'],
            ['codigo' => 'LAB-PERFIL-TIR',  'nombre' => 'Perfil Tiroideo (TSH, T3, T4)',  'precio' => 450.00,  'precio_descripcion' => 'Ayuno no requerido'],
            ['codigo' => 'LAB-GLUCOSA',     'nombre' => 'Glucosa en Ayuno',               'precio' => 80.00,   'precio_descripcion' => 'Ayuno de 8 horas'],
            ['codigo' => 'LAB-HBA1C',       'nombre' => 'Hemoglobina Glicosilada',        'precio' => 350.00,  'precio_descripcion' => 'Control diabetes'],
            ['codigo' => 'LAB-TP-TPT',      'nombre' => 'Tiempos de Coagulación',         'precio' => 280.00,  'precio_descripcion' => 'TP y TPT'],
            ['codigo' => 'LAB-GRUPO-SANG',  'nombre' => 'Grupo Sanguíneo y Rh',           'precio' => 100.00,  'precio_descripcion' => ''],
            ['codigo' => 'LAB-VIH',         'nombre' => 'Prueba de VIH',                  'precio' => 300.00,  'precio_descripcion' => 'Confidencial'],
            ['codigo' => 'LAB-HEP-B',       'nombre' => 'Antígeno de Superficie Hepatitis B', 'precio' => 350.00, 'precio_descripcion' => ''],
            ['codigo' => 'LAB-CULTIVO',     'nombre' => 'Cultivo de Orina',               'precio' => 400.00,  'precio_descripcion' => 'Con antibiograma'],
            ['codigo' => 'LAB-PERFIL-HEP',  'nombre' => 'Perfil Hepático',                'precio' => 480.00,  'precio_descripcion' => 'Ayuno de 8 horas'],
            ['codigo' => 'LAB-PERFIL-REN',  'nombre' => 'Perfil Renal',                   'precio' => 420.00,  'precio_descripcion' => 'Ayuno de 8 horas'],
            ['codigo' => 'LAB-PCR',         'nombre' => 'PCR (Proteína C Reactiva)',       'precio' => 250.00,  'precio_descripcion' => ''],
            ['codigo' => 'LAB-ELECTRO',     'nombre' => 'Electrolitos Séricos',           'precio' => 350.00,  'precio_descripcion' => 'Na, K, Cl, Ca'],
            ['codigo' => 'LAB-FERRITINA',   'nombre' => 'Ferritina',                      'precio' => 380.00,  'precio_descripcion' => ''],
            ['codigo' => 'LAB-VIT-D',       'nombre' => 'Vitamina D',                     'precio' => 550.00,  'precio_descripcion' => ''],
            ['codigo' => 'LAB-COPRO',       'nombre' => 'Coproparasitoscópico',           'precio' => 150.00,  'precio_descripcion' => '3 muestras'],
        ];

        // Guardar imagenología
        foreach ($imagenes as $item) {
            Servicio::firstOrCreate(
                ['codigo' => $item['codigo']],
                array_merge($item, [
                    'tipo'         => 'imagenes',
                    'ubicacion'    => 'Edificio de Imagenología',
                    'abierto_24h'  => false,
                    'activo'       => true,
                ])
            );
        }

        // Guardar laboratorio
        foreach ($laboratorio as $item) {
            Servicio::firstOrCreate(
                ['codigo' => $item['codigo']],
                array_merge($item, [
                    'tipo'         => 'laboratorio',
                    'ubicacion'    => 'Laboratorio Clínico',
                    'abierto_24h'  => false,
                    'activo'       => true,
                ])
            );
        }

        $this->command->info(' Servicios de Imagenología y Laboratorio creados.');
    }
}