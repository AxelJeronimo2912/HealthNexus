<?php

/*
| Consulta de pacientes sin conexión (HAL-199).
| Qué se guarda en el dispositivo y por cuánto tiempo.
*/
return [
    // Horas que los datos siguen disponibles sin conexión (un turno). Después se borran solos.
    'ttl_horas' => (int) env('OFFLINE_TTL_HORAS', 12),

    // Máximo de pacientes que se guardan por usuario.
    'max_pacientes' => (int) env('OFFLINE_MAX_PACIENTES', 150),
];
