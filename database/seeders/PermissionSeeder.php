<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permisos = [
            // Gestión Hospitalaria
            'pacientes.ver',
            'admision.ver',
            'servicios.ver',
            'especialidades.ver',
            'citas.ver',
            'turnos.ver',
            'camas.ver',
            'cuentas.ver',

            // Estudios (Laboratorio / Imagenología) ✅
            'estudios.ver',
            'estudios.crear',
            'estudios.editar',

            // Agenda
            'agenda.ver',

            // Atención Clínica
            'expediente.ver',
            'consultas.ver',
            'enfermeria.ver',
            'seguimiento.ver',
            'signos-vitales.ver',

            // Farmacia e Inventario
            'medicamentos.ver',
            'existencias.ver',
            'movimientos.ver',
            'prediccion.ver',
            'dispensaciones.ver',

            // Seguridad e Inteligencia
            'usuarios.ver',
            'roles.ver',
            'dispositivos.ver',
            'auditoria.ver',
            'asistente.ver',
            'alertas.ver',
        ];

        foreach ($permisos as $nombre) {
            Permission::firstOrCreate(['name' => $nombre, 'guard_name' => 'web']);
        }
    }
}