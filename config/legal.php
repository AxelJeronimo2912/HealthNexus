<?php

/*
|--------------------------------------------------------------------------
| Datos de las páginas legales (Aviso de privacidad y Términos de uso)
|--------------------------------------------------------------------------
| Llena estos valores en el .env de cada ambiente. Mientras estén vacíos,
| las páginas muestran el texto entre corchetes para que se note qué falta.
*/

return [
    'empresa'       => env('LEGAL_EMPRESA', 'HealthNexus'),
    'domicilio'     => env('LEGAL_DOMICILIO', 'Cerrada Simón Bolívar S/N, Santa María Atarasquillo, Lerma, Estado de México'),
    'telefono'      => env('LEGAL_TELEFONO', '722 364 8734'),
    'correo'        => env('LEGAL_CORREO', '[CORREO DE PRIVACIDAD]'),
    'ciudad'        => env('LEGAL_CIUDAD', 'Lerma, Estado de México'),
    'actualizacion' => env('LEGAL_ACTUALIZACION', '26 de octubre de 2026'),
];
