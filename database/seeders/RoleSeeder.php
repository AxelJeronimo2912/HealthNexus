<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'administrador',
            'medico',
            'enfermeria',
            'farmacia',
            'laboratorio', 
        ];

        foreach ($roles as $nombre) {
            Role::firstOrCreate([
                'name' => $nombre,
                'guard_name' => 'web',
            ]);
        }
    }
}
