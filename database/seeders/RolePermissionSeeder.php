<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Administrador: ve TODO
        $admin = Role::findByName('administrador');
        $admin->syncPermissions(Permission::all());

        // Médico
        $medico = Role::findByName('medico');
        $medico->syncPermissions([
            'pacientes.ver',
            'citas.ver',
            'turnos.ver',
            'expediente.ver',
            'consultas.ver',
            'seguimiento.ver',
            'signos-vitales.ver',
            'camas.ver',
            'agenda.ver',
        ]);

        // Enfermería
        $enfermeria = Role::findByName('enfermeria');
        $enfermeria->syncPermissions([
            'pacientes.ver',
            'turnos.ver',
            'enfermeria.ver',
            'seguimiento.ver',
            'signos-vitales.ver',
            'camas.ver',
            'agenda.ver'
        ]);

        // Farmacia
        $farmacia = Role::findByName('farmacia');
        $farmacia->syncPermissions([
            'medicamentos.ver',
            'existencias.ver',
            'movimientos.ver',
            'prediccion.ver',
            
        ]);
    }
}