<?php

/*
|--------------------------------------------------------------------------
| Navegación principal de HealthNexus
|--------------------------------------------------------------------------
|
| Fuente única para el sidebar, el breadcrumb del topbar y la paleta de
| comandos (Ctrl/⌘ + K). Agregar un módulo aquí lo hace aparecer en los
| tres lugares, respetando el permiso indicado.
|
| Cada item:
|   permiso → permiso de spatie/laravel-permission (null = visible para todos)
|   ruta    → nombre de la ruta a la que enlaza
|   activo  → patrón(es) de routeIs() que marcan el item como activo
|   icono   → nombre de Heroicon (outline)
|
*/

return [

    [
        'titulo' => 'Gestión hospitalaria',
        'items' => [
            ['permiso' => 'pacientes.ver',      'ruta' => 'pacientes.index',       'activo' => 'pacientes.*',       'icono' => 'user-group',               'label' => 'Pacientes'],
            ['permiso' => 'admision.ver',       'ruta' => 'admisiones.index',      'activo' => 'admisiones.*',      'icono' => 'clipboard-document-check', 'label' => 'Admisión'],
            ['permiso' => 'servicios.ver',      'ruta' => 'servicios.index',       'activo' => 'servicios.*',       'icono' => 'building-office-2',        'label' => 'Servicios'],
            ['permiso' => 'especialidades.ver', 'ruta' => 'especialidades.index',  'activo' => 'especialidades.*',  'icono' => 'academic-cap',             'label' => 'Especialidades'],
            ['permiso' => 'citas.ver',          'ruta' => 'citas.index',           'activo' => 'citas.*',           'icono' => 'calendar-days',            'label' => 'Citas'],
            ['permiso' => 'turnos.ver',         'ruta' => 'admin.turnos.index',    'activo' => 'admin.turnos.*',    'icono' => 'clock',                    'label' => 'Turnos'],
            ['permiso' => 'camas.ver',          'ruta' => 'camas.index',           'activo' => 'camas.*',           'icono' => 'home-modern',              'label' => 'Camas'],
            ['permiso' => 'cuentas.ver',        'ruta' => 'cuentas.index',         'activo' => 'cuentas.*',         'icono' => 'banknotes',                'label' => 'Cuentas por cobrar'],
        ],
    ],

    [
        'titulo' => 'Agenda',
        'items' => [
            ['permiso' => 'agenda.ver', 'ruta' => 'agenda.index', 'activo' => 'agenda.*', 'icono' => 'calendar', 'label' => 'Calendario de citas'],
        ],
    ],

    [
        'titulo' => 'Atención clínica',
        'items' => [
            ['permiso' => 'expediente.ver',     'ruta' => 'expedientes.index',                'activo' => 'expedientes.*',                'icono' => 'document-text',           'label' => 'Expediente'],
            // Consultas comparte la pantalla de citas: no se marca activo para no duplicar la selección
            ['permiso' => 'consultas.ver',      'ruta' => 'citas.index',                      'activo' => 'consultas.*',                  'icono' => 'clipboard-document-list', 'label' => 'Consultas'],
            ['permiso' => 'enfermeria.ver',     'ruta' => 'enfermeria.notas.index',           'activo' => 'enfermeria.notas.*',           'icono' => 'pencil-square',           'label' => 'Notas de enfermería'],
            ['permiso' => 'enfermeria.ver',     'ruta' => 'enfermeria.administraciones.index', 'activo' => 'enfermeria.administraciones.*', 'icono' => 'eye-dropper',            'label' => 'Administrar medicamentos'],
            ['permiso' => 'seguimiento.ver',    'ruta' => 'seguimientos.index',               'activo' => 'seguimientos.*',               'icono' => 'chart-bar',               'label' => 'Seguimiento'],
            ['permiso' => 'signos-vitales.ver', 'ruta' => 'signos-vitales.index',             'activo' => 'signos-vitales.*',             'icono' => 'heart',                   'label' => 'Signos vitales'],
        ],
    ],

    [
        'titulo' => 'Farmacia e inventario',
        'items' => [
            ['permiso' => 'medicamentos.ver',   'ruta' => 'medicamentos.index',   'activo' => 'medicamentos.*',   'icono' => 'beaker',          'label' => 'Medicamentos'],
            ['permiso' => 'dispensaciones.ver', 'ruta' => 'dispensaciones.index', 'activo' => 'dispensaciones.*', 'icono' => 'document-check',  'label' => 'Recetas'],
            ['permiso' => 'existencias.ver',    'ruta' => 'existencias.index',    'activo' => 'existencias.*',    'icono' => 'archive-box',     'label' => 'Existencias'],
            ['permiso' => 'movimientos.ver',    'ruta' => 'movimientos.index',    'activo' => 'movimientos.*',    'icono' => 'arrows-right-left', 'label' => 'Movimientos'],
            ['permiso' => 'prediccion.ver',     'ruta' => 'prediccion.index',     'activo' => 'prediccion.*',     'icono' => 'cpu-chip',        'label' => 'Predicción IA'],
        ],
    ],

    [
        'titulo' => 'Seguridad e inteligencia',
        'items' => [
            ['permiso' => 'usuarios.ver',    'ruta' => 'admin.users.index', 'activo' => 'admin.users.*', 'icono' => 'users',               'label' => 'Usuarios'],
            ['permiso' => 'roles.ver',       'ruta' => 'admin.roles.index', 'activo' => 'admin.roles.*', 'icono' => 'shield-check',        'label' => 'Roles y permisos'],
            ['permiso' => 'dispositivos.ver', 'ruta' => 'dispositivos.index', 'activo' => 'dispositivos.*', 'icono' => 'device-phone-mobile', 'label' => 'Dispositivos'],
            ['permiso' => 'auditoria.ver',   'ruta' => 'auditoria.index',   'activo' => 'auditoria.*',   'icono' => 'finger-print',        'label' => 'Auditoría'],
            ['permiso' => 'asistente.ver',   'ruta' => 'asistente.index',   'activo' => 'asistente.*',   'icono' => 'sparkles',            'label' => 'Asistente IA'],
            ['permiso' => 'alertas.ver',     'ruta' => 'alertas.index',     'activo' => 'alertas.*',     'icono' => 'bell-alert',          'label' => 'Alertas inteligentes', 'contador' => 'alertas.contar'],
        ],
    ],

];
