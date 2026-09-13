<?php

namespace Database\Seeders;

use App\Models\Turno;
use Illuminate\Database\Seeder;

class TurnoSeeder extends Seeder
{
    public function run(): void
    {
        $turnos = [
            ['nombre' => 'Mañana', 'codigo' => 'MAT', 'hora_inicio' => '07:00', 'hora_fin' => '15:00', 'descripcion' => 'Turno matutino'],
            ['nombre' => 'Tarde', 'codigo' => 'TAR', 'hora_inicio' => '15:00', 'hora_fin' => '23:00', 'descripcion' => 'Turno vespertino'],
            ['nombre' => 'Noche', 'codigo' => 'NOC', 'hora_inicio' => '23:00', 'hora_fin' => '07:00', 'descripcion' => 'Turno nocturno'],
            ['nombre' => 'Guardia 24h', 'codigo' => 'G24', 'hora_inicio' => '08:00', 'hora_fin' => '08:00', 'descripcion' => 'Guardia completa'],
            ['nombre' => 'Medio turno mañana', 'codigo' => 'MTM', 'hora_inicio' => '08:00', 'hora_fin' => '13:00', 'descripcion' => 'Medio turno matutino'],
        ];

        foreach ($turnos as $t) {
            Turno::firstOrCreate(['codigo' => $t['codigo']], $t);
        }

        $this->command->info(' Turnos creados.');
    }
}