<?php

/*
|--------------------------------------------------------------------------
| Datos de las páginas legales (Aviso de privacidad y Términos de uso)
|--------------------------------------------------------------------------
| Llena estos valores en el .env de cada ambiente. Mientras estén vacíos,
| las páginas muestran el texto entre corchetes para que se note qué falta.
*/

return [
    'empresa'       => env('LEGAL_EMPRESA', 'NEXARA'),
    'sitio'         => env('LEGAL_SITIO', 'https://jesusfelipea.github.io/Sitio_Web_Nexara/'),
    'domicilio'     => env('LEGAL_DOMICILIO', '[DOMICILIO DE NEXARA]'),
    'correo'        => env('LEGAL_CORREO', '[CORREO DE PRIVACIDAD]'),
    'ciudad'        => env('LEGAL_CIUDAD', 'Toluca, Estado de México'),
    'actualizacion' => env('LEGAL_ACTUALIZACION', '26 de octubre de 2026'),
];
