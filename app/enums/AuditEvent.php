<?php

namespace App\Enums;

enum AuditEvent: string
{
    // Autenticación
    case LOGIN = 'login';
    case LOGOUT = 'logout';
    case LOGIN_FALLIDO = 'login_fallido';
    case PIN_FALLIDO = 'pin_fallido';
    case PIN_EXITOSO = 'pin_exitoso';

    // CRUD genérico
    case CREATED = 'created';
    case UPDATED = 'updated';
    case DELETED = 'deleted';
    case RESTORED = 'restored';

    // Módulos específicos
    case DISPENSACION = 'dispensacion';
    case REVERSION_DISPENSACION = 'reversion_dispensacion';
    case DERIVACION = 'derivacion';
    case HOSPITALIZACION = 'hospitalizacion';
    case ASIGNACION_CAMA = 'asignacion_cama';
    case LIBERACION_CAMA = 'liberacion_cama';

    // Seguridad
    case ACCESO_DENEGADO = 'acceso_denegado';
    case DISPOSITIVO_NUEVO = 'dispositivo_nuevo';
    case DISPOSITIVO_BLOQUEADO = 'dispositivo_bloqueado';
    case ACTIVIDAD_INUSUAL = 'actividad_inusual';

    // Sistema
    case EXPORTACION = 'exportacion';
    case IMPORTACION = 'importacion';
    case CONSULTA_SENSIBLE = 'consulta_sensible';

    /**
     * Etiqueta legible en español.
     */
    public function etiqueta(): string
    {
        return match ($this) {
            self::LOGIN => 'Inicio de sesión',
            self::LOGOUT => 'Cierre de sesión',
            self::LOGIN_FALLIDO => 'Intento de login fallido',
            self::PIN_FALLIDO => 'PIN incorrecto',
            self::PIN_EXITOSO => 'PIN verificado',
            self::CREATED => 'Creación',
            self::UPDATED => 'Actualización',
            self::DELETED => 'Eliminación',
            self::RESTORED => 'Restauración',
            self::DISPENSACION => 'Dispensación',
            self::REVERSION_DISPENSACION => 'Reversión de dispensación',
            self::DERIVACION => 'Derivación',
            self::HOSPITALIZACION => 'Hospitalización',
            self::ASIGNACION_CAMA => 'Asignación de cama',
            self::LIBERACION_CAMA => 'Liberación de cama',
            self::ACCESO_DENEGADO => 'Acceso denegado',
            self::DISPOSITIVO_NUEVO => 'Dispositivo nuevo',
            self::DISPOSITIVO_BLOQUEADO => 'Dispositivo bloqueado',
            self::ACTIVIDAD_INUSUAL => 'Actividad inusual',
            self::EXPORTACION => 'Exportación de datos',
            self::IMPORTACION => 'Importación de datos',
            self::CONSULTA_SENSIBLE => 'Consulta de datos sensibles',
        };
    }

    /**
     * Severidad por defecto de este evento.
     */
    public function severidad(): string
    {
        return match ($this) {
            self::LOGIN_FALLIDO,
            self::PIN_FALLIDO,
            self::ACCESO_DENEGADO,
            self::ACTIVIDAD_INUSUAL,
            self::DISPOSITIVO_BLOQUEADO => 'critical',

            self::DELETED,
            self::DISPENSACION,
            self::REVERSION_DISPENSACION,
            self::DERIVACION,
            self::DISPOSITIVO_NUEVO,
            self::EXPORTACION,
            self::IMPORTACION => 'warning',

            default => 'info',
        };
    }

    /**
     * Color para mostrar en la UI.
     */
    public function color(): string
    {
        return match ($this->severidad()) {
            'critical' => 'bg-red-100 text-red-800',
            'warning' => 'bg-yellow-100 text-yellow-800',
            default => 'bg-blue-100 text-blue-800',
        };
    }

    /**
     * ¿Es un evento sensible que requiere retención extendida?
     */
    public function esSensible(): bool
    {
        return in_array($this, [
            self::LOGIN_FALLIDO,
            self::PIN_FALLIDO,
            self::ACCESO_DENEGADO,
            self::DELETED,
            self::REVERSION_DISPENSACION,
            self::ACTIVIDAD_INUSUAL,
        ]);
    }
}