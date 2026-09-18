<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Administrador: todos los permisos
        Role::findByName('administrador')->syncPermissions(Permission::all());

        // Médico: atención clínica
        Role::findByName('medico')->syncPermissions([
            'pacientes.ver',
            'citas.ver',
            'turnos.ver',
            'expediente.ver',
            'consultas.ver',
            'seguimiento.ver',
            'signos-vitales.ver',
            'camas.ver',
            'agenda.ver',
            'existencias.ver',
            'servicios.ver',
        ]);

        // Enfermería: cuidados y monitoreo
        Role::findByName('enfermeria')->syncPermissions([
            'pacientes.ver',
            'turnos.ver',
            'enfermeria.ver',
            'seguimiento.ver',
            'signos-vitales.ver',
            'camas.ver',
            'agenda.ver',
            'expediente.ver',
            'existencias.ver',
            'movimientos.ver',
            'servicios.ver',
        ]);

        // Farmacia: inventario y dispensación
        Role::findByName('farmacia')->syncPermissions([
            'medicamentos.ver',
            'existencias.ver',
            'movimientos.ver',
            'prediccion.ver',
            'dispensaciones.ver',
        ]);
    }
}
