<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $rolAdmin = Role::firstOrCreate(['name' => 'administrador']);

        $rolAdmin->syncPermissions(Permission::all());   

        $admin = User::firstOrCreate(
            ['email' => 'admin@healthnexus.com'],
            [
                'name' => 'Administrador HealthNexus',
                'password' => bcrypt('Admin1234'),
                'nombre' => 'Administrador',
                'apellido_paterno' => 'HealthNexus',
                'apellido_materno' => 'Sistema',
                'curp' => 'HEXA000101HDFRRN01',
                'fecha_nacimiento' => '1990-01-01',
                'cedula_profesional' => '12345678',
                'telefono' => '5555555555',
                'telefono_contacto' => '5555555556',
                'tipo_servicio' => 'presencial',
                'activo' => true,
            ]
        );

        if (!$admin->hasRole($rolAdmin->name)) {
            $admin->assignRole($rolAdmin);
        }

        $this->command->info(' Usuario administrador creado:');
        $this->command->line('   Correo:     admin@healthnexus.com');
        $this->command->line('   Contraseña: Admin1234');
        $this->command->line('   Permisos:   ' . $rolAdmin->permissions()->count());
    }
}