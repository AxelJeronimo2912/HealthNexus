<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Estructura del navbar agrupada por sección
    |--------------------------------------------------------------------------
    | Cada sección tiene un título y una lista de items.
    | Cada item tiene:
    |   - permiso: nombre del permiso (lo que se guarda en BD)
    |   - label:   nombre amigable que ve el admin
    |   - icono:   nombre de Heroicon (opcional, sin el prefijo heroicon-o-)
    |   - ruta:    nombre de la ruta de Laravel (o '#' si aún no existe el módulo)
    */
    'secciones' => [

        /*
        |--------------------------------------------------------------------------
        | Gestión Hospitalaria
        |--------------------------------------------------------------------------
        */
        [
            'titulo' => 'Gestión Hospitalaria',
            'items' => [
                ['permiso' => 'pacientes.ver',      'label' => 'Pacientes',      'icono' => 'user-group',               'ruta' => 'pacientes.index'],
                ['permiso' => 'admision.ver',       'label' => 'Admisión',       'icono' => 'clipboard-document-check', 'ruta' => '#'],
                ['permiso' => 'servicios.ver', 'label' => 'Servicios', 'icono' => 'building-office-2', 'ruta' => 'servicios.index'],
                ['permiso' => 'especialidades.ver', 'label' => 'Especialidades', 'icono' => 'academic-cap', 'ruta' => 'especialidades.index'],
                ['permiso' => 'citas.ver', 'label' => 'Citas', 'icono' => 'calendar-days', 'ruta' => 'citas.index'],
                ['permiso' => 'turnos.ver',         'label' => 'Turnos',         'icono' => 'clock',                    'ruta' => 'admin.turnos.index'],
                ['permiso' => 'camas.ver',          'label' => 'Camas',          'icono' => 'home-modern',              'ruta' => 'camas.index'],
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        | Atención Clínica
        |--------------------------------------------------------------------------
        */
       [
    'titulo' => 'Atención Clínica',
    'items' => [
        ['permiso' => 'agenda.ver',           'label' => 'Agenda',                 'icono' => 'calendar-days',           'ruta' => 'agenda.index'],
        ['permiso' => 'expediente.ver',       'label' => 'Expediente',             'icono' => 'document-text',           'ruta' => 'expedientes.index'],
        ['permiso' => 'consultas.ver',        'label' => 'Consultas',              'icono' => 'clipboard-document-list', 'ruta' => 'citas.index'],
        ['permiso' => 'enfermeria.ver',       'label' => 'Notas de Enfermería',    'icono' => 'heart',                   'ruta' => 'enfermeria.notas.index'],
        ['permiso' => 'enfermeria.ver',       'label' => 'Administrar Medicamentos','icono' => 'beaker',                  'ruta' => 'enfermeria.administraciones.index'],
        ['permiso' => 'seguimiento.ver',      'label' => 'Seguimiento',            'icono' => 'chart-bar',               'ruta' => 'seguimientos.index'],
        ['permiso' => 'signos-vitales.ver',   'label' => 'Signos Vitales',         'icono' => 'heart',                   'ruta' => 'signos-vitales.index'],
    ],
],

        /*
        |--------------------------------------------------------------------------
        | Farmacia e Inventario
        |--------------------------------------------------------------------------
        */
       [
       'titulo' => 'Farmacia e Inventario',
       'items' => [
        ['permiso' => 'dispensaciones.ver', 'label' => 'Recetas',       'icono' => 'clipboard-document-list', 'ruta' => 'dispensaciones.index'],
        ['permiso' => 'medicamentos.ver',   'label' => 'Medicamentos',  'icono' => 'beaker',                 'ruta' => 'medicamentos.index'],
        ['permiso' => 'existencias.ver',    'label' => 'Existencias',   'icono' => 'archive-box',            'ruta' => 'existencias.index'],
        ['permiso' => 'movimientos.ver', 'label' => 'Movimientos', 'icono' => 'arrow-path', 'ruta' => 'movimientos.index'],
        ['permiso' => 'prediccion.ver', 'label' => 'Predicción IA', 'icono' => 'cpu-chip', 'ruta' => 'prediccion.index'],  
          ],
],

        /*
        |--------------------------------------------------------------------------
        | Seguridad e Inteligencia
        |--------------------------------------------------------------------------
        */
        [
            'titulo' => 'Seguridad e Inteligencia',
            'items' => [
                ['permiso' => 'usuarios.ver',     'label' => 'Usuarios',             'icono' => 'users',                   'ruta' => 'admin.users.index'],
                ['permiso' => 'roles.ver',        'label' => 'Roles y Permisos',     'icono' => 'shield-check',            'ruta' => 'admin.roles.index'],
['permiso' => 'dispositivos.ver', 'label' => 'Dispositivos', 'icono' => 'device-phone-mobile', 'ruta' => 'dispositivos.index'],                ['permiso' => 'auditoria.ver',    'label' => 'Auditoría',            'icono' => 'clipboard-document-list', 'ruta' => '#'],
                ['permiso' => 'asistente.ver',    'label' => 'Asistente IA',         'icono' => 'sparkles',                'ruta' => '#'],
                ['permiso' => 'alertas.ver',      'label' => 'Alertas Inteligentes', 'icono' => 'bell-alert',              'ruta' => '#'],
            ],
        ],
    ],
];