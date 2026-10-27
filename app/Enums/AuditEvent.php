<?php

namespace App\Enums;

enum AuditEvent: string
{
    // ==================== Autenticación ====================
    case LOGIN = 'login';
    case LOGOUT = 'logout';
    case LOGIN_FALLIDO = 'login_fallido';
    case PIN_FALLIDO = 'pin_fallido';
    case PIN_EXITOSO = 'pin_exitoso';

    // ==================== CRUD genérico ====================
    case CREATED = 'created';
    case UPDATED = 'updated';
    case DELETED = 'deleted';
    case RESTORED = 'restored';

    // ==================== Módulos específicos ====================
    case DISPENSACION = 'dispensacion';
    case REVERSION_DISPENSACION = 'reversion_dispensacion';
    case DERIVACION = 'derivacion';
    case HOSPITALIZACION = 'hospitalizacion';
    case ASIGNACION_CAMA = 'asignacion_cama';
    case LIBERACION_CAMA = 'liberacion_cama';

    // ==================== Personal / Servicios ====================
    case ASIGNACION_PERSONAL = 'asignacion_personal';
    case REMOCION_PERSONAL  = 'remocion_personal';

    // ==================== Seguridad ====================
    case ACCESO_DENEGADO = 'acceso_denegado';
    case DISPOSITIVO_NUEVO = 'dispositivo_nuevo';
    case DISPOSITIVO_BLOQUEADO = 'dispositivo_bloqueado';
    case ACTIVIDAD_INUSUAL = 'actividad_inusual';

    // ==================== Sistema ====================
    case EXPORTACION = 'exportacion';
    case IMPORTACION = 'importacion';
    case CONSULTA_SENSIBLE = 'consulta_sensible';

    // ==================== Especialidades ====================
case ASIGNACION_MEDICO_ESPECIALIDAD   = 'asignacion_medico_especialidad';
case REMOCION_MEDICO_ESPECIALIDAD     = 'remocion_medico_especialidad';
case ASIGNACION_SERVICIO_ESPECIALIDAD = 'asignacion_servicio_especialidad';
case REMOCION_SERVICIO_ESPECIALIDAD   = 'remocion_servicio_especialidad';

// ==================== Citas ====================
case CITA_CONFIRMADA = 'cita_confirmada';
case CITA_CANCELADA  = 'cita_cancelada';
case CITA_EN_CURSO   = 'cita_en_curso';
case CITA_ATENDIDA   = 'cita_atendida';
case CITA_NO_ASISTIO = 'cita_no_asistio';
case CITA_COBRO      = 'cita_cobro';
case CAMBIO_ESTADO_CAMA = 'cambio_estado_cama';
// ==================== Cuentas / Financiero ====================
case CUENTA_CARGO_AGREGADO   = 'cuenta_cargo_agregado';
case CUENTA_CARGO_ELIMINADO  = 'cuenta_cargo_eliminado';
case CUENTA_DESCUENTO        = 'cuenta_descuento';
case CUENTA_PAGO             = 'cuenta_pago';
case CUENTA_PAGO_CANCELADO   = 'cuenta_pago_cancelado';
case CUENTA_CERRADA          = 'cuenta_cerrada';
case CUENTA_PDF_GENERADO     = 'cuenta_pdf_generado';
case RECIBO_PDF_GENERADO     = 'recibo_pdf_generado';

    /**
     * Etiqueta legible en español.
     */
    public function etiqueta(): string
    {
        return match ($this) {
            // Autenticación
            self::LOGIN                  => 'Inicio de sesión',
            self::LOGOUT                 => 'Cierre de sesión',
            self::LOGIN_FALLIDO          => 'Intento de login fallido',
            self::PIN_FALLIDO            => 'PIN incorrecto',
            self::PIN_EXITOSO            => 'PIN verificado',

            // CRUD
            self::CREATED                => 'Creación',
            self::UPDATED                => 'Actualización',
            self::DELETED                => 'Eliminación',
            self::RESTORED               => 'Restauración',

            // Módulos
            self::DISPENSACION           => 'Dispensación',
            self::REVERSION_DISPENSACION => 'Reversión de dispensación',
            self::DERIVACION             => 'Derivación',
            self::HOSPITALIZACION        => 'Hospitalización',
            self::ASIGNACION_CAMA        => 'Asignación de cama',
            self::LIBERACION_CAMA        => 'Liberación de cama',

            // Personal
            self::ASIGNACION_PERSONAL    => 'Asignación de personal',
            self::REMOCION_PERSONAL      => 'Remoción de personal',

            // Seguridad
            self::ACCESO_DENEGADO        => 'Acceso denegado',
            self::DISPOSITIVO_NUEVO      => 'Dispositivo nuevo',
            self::DISPOSITIVO_BLOQUEADO  => 'Dispositivo bloqueado',
            self::ACTIVIDAD_INUSUAL      => 'Actividad inusual',

            // Sistema
            self::EXPORTACION            => 'Exportación de datos',
            self::IMPORTACION            => 'Importación de datos',
            self::CONSULTA_SENSIBLE      => 'Consulta de datos sensibles',

            self::ASIGNACION_MEDICO_ESPECIALIDAD   => 'Asignación de médico a especialidad',
            self::REMOCION_MEDICO_ESPECIALIDAD     => 'Remoción de médico de especialidad',
            self::ASIGNACION_SERVICIO_ESPECIALIDAD => 'Asignación de servicio a especialidad',
            self::REMOCION_SERVICIO_ESPECIALIDAD   => 'Remoción de servicio de especialidad',
            self::CITA_CONFIRMADA => 'Cita confirmada',
self::CITA_CANCELADA  => 'Cita cancelada',
self::CITA_EN_CURSO   => 'Cita en curso',
self::CITA_ATENDIDA   => 'Cita atendida',
self::CITA_NO_ASISTIO => 'Paciente no asistió',
self::CITA_COBRO      => 'Cobro generado por cita',
self::CUENTA_CARGO_AGREGADO  => 'Cargo agregado a cuenta',
self::CUENTA_CARGO_ELIMINADO => 'Cargo eliminado de cuenta',
self::CUENTA_DESCUENTO       => 'Descuento aplicado a cuenta',
self::CUENTA_PAGO            => 'Pago registrado',
self::CUENTA_PAGO_CANCELADO  => 'Pago cancelado',
self::CUENTA_CERRADA         => 'Cuenta cerrada',
self::CUENTA_PDF_GENERADO    => 'Estado de cuenta PDF generado',
self::RECIBO_PDF_GENERADO    => 'Recibo PDF generado',


            // Red de seguridad: nunca debería entrar aquí, pero evita errores
            default => ucfirst(str_replace('_', ' ', $this->value)),
        };
    }

    /**
     * Severidad por defecto de este evento.
     */
    public function severidad(): string
    {
        return match ($this) {
            // Críticos
            self::LOGIN_FALLIDO,
            self::PIN_FALLIDO,
            self::ACCESO_DENEGADO,
            self::ACTIVIDAD_INUSUAL,
            self::DISPOSITIVO_BLOQUEADO => 'critical',
self::CITA_CANCELADA,
self::CITA_NO_ASISTIO => 'warning',
            // Advertencia
            self::DELETED,
            self::DISPENSACION,
            self::REVERSION_DISPENSACION,
            self::DERIVACION,
            self::DISPOSITIVO_NUEVO,
            self::EXPORTACION,
            self::IMPORTACION,
            self::REMOCION_PERSONAL,
            self::LIBERACION_CAMA => 'warning',

self::CUENTA_PAGO_CANCELADO,
self::CUENTA_CARGO_ELIMINADO,
self::CUENTA_DESCUENTO => 'warning',
            self::REMOCION_MEDICO_ESPECIALIDAD,
            self::REMOCION_SERVICIO_ESPECIALIDAD => 'warning',

            // Todo lo demás = info
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
            'warning'  => 'bg-yellow-100 text-yellow-800',
            default    => 'bg-blue-100 text-blue-800',
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
        ], true);
    }
}