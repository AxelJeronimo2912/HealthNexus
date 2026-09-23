<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            RolePermissionSeeder::class,       
            AdminUserSeeder::class,
            EstadosMunicipiosSeeder::class,
            DiagnosticoSeeder::class,
            TurnoSeeder::class,
            ServicioSeeder::class,
            CamaSeeder::class,
            MedicamentoSeeder::class,
            LoteSeeder::class,
            PacienteSeeder::class,
             EspecialidadSeeder::class,
        ]);
    }
}