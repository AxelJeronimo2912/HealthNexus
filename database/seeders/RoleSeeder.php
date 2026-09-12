<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::create(['name' => 'administrador']);
        Role::create(['name' => 'medico']);
        Role::create(['name' => 'enfermeria']);
        Role::create(['name' => 'farmacia']);
    }
}