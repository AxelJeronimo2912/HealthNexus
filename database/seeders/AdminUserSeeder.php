<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear (o recuperar) el rol administrador
        $rolAdmin = Role::firstOrCreate(['name' => 'administrador']);

        // 2. Crear el usuario administrador
        $admin = User::firstOrCreate(
            ['email' => 'admin@healthnexus.com'],
            [
                'name' => 'Administrador HealthNexus',
                'password' => bcrypt('Admin1234'),

                // Datos personales
                'nombre' => 'Administrador',
                'apellido_paterno' => 'HealthNexus',
                'apellido_materno' => 'Sistema',
                'curp' => 'HEXA000101HDFRRN01',
                'fecha_nacimiento' => '1990-01-01',
                'cedula_profesional' => '12345678',

                // Contacto
                'telefono' => '5555555555',
                'telefono_contacto' => '5555555556',

                // Rol y servicio
                'tipo_servicio' => 'presencial',

                // Estado
                'activo' => true,
            ]
        );

        // 3. Asignar el rol administrador
        if (!$admin->hasRole($rolAdmin->name)) {
            $admin->assignRole($rolAdmin);
        }

        // 4. Mensaje en consola
        $this->command->info('✅ Usuario administrador creado:');
        $this->command->line('   Correo:     admin@healthnexus.com');
        $this->command->line('   Contraseña: Admin1234');
    }
}