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
            AdminUserSeeder::class,
            EstadosMunicipiosSeeder::class,
            MedicamentoSeeder::class,
            PacienteSeeder::class,
            CamaSeeder::class,
            TurnoSeeder::class,
        
        ]);
    }
}