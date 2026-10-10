<?php

namespace App\Services;

use App\Enums\AuditEvent;
use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditoriaService
{
    /**
     * Registra un evento de auditoría.
     */
    public static function registrar(
        AuditEvent $evento,
        string $modulo,
        string $descripcion,
        ?Model $auditable = null,
        array $datosAntes = [],
        array $datosDespues = [],
        array $metadataExtra = []
    ): AuditLog {
        $user = Auth::user();

        return AuditLog::create([
            'user_id' => $user?->id,
            'user_nombre' => $user?->nombre_completo ?? $user?->name ?? 'Sistema',
            'user_rol' => $user?->getRoleNames()->first() ?? 'invitado',
            'evento' => $evento->value,
            'modulo' => $modulo,
            'descripcion' => $descripcion,
            'auditable_type' => $auditable ? get_class($auditable) : null,
            'auditable_id' => $auditable?->id,
            'datos_antes' => self::limpiarDatos($datosAntes),
            'datos_despues' => self::limpiarDatos($datosDespues),
            'metadata' => array_merge(self::metadataBase(), $metadataExtra),
            'severidad' => $evento->severidad(),
            'es_sensible' => $evento->esSensible(),
        ]);
    }

    /**
     * Atajo para created.
     */
    public static function creado(Model $modelo, string $modulo, ?string $descripcion = null): AuditLog
    {
        return self::registrar(
            AuditEvent::CREATED,
            $modulo,
            $descripcion ?? "Nuevo registro en {$modulo}",
            $modelo,
            [],
            $modelo->getAttributes()
        );
    }

    /**
     * Atajo para updated.
     */
    public static function actualizado(Model $modelo, string $modulo, array $antes, ?string $descripcion = null): AuditLog
    {
        $cambios = [];
        foreach ($modelo->getChanges() as $campo => $nuevo) {
            $cambios[$campo] = [
                'antes' => $antes[$campo] ?? null,
                'despues' => $nuevo,
            ];
        }

        return self::registrar(
            AuditEvent::UPDATED,
            $modulo,
            $descripcion ?? "Actualización en {$modulo}",
            $modelo,
            $antes,
            $cambios
        );
    }

    /**
     * Atajo para deleted.
     */
    public static function eliminado(Model $modelo, string $modulo, ?string $descripcion = null): AuditLog
    {
        return self::registrar(
            AuditEvent::DELETED,
            $modulo,
            $descripcion ?? "Eliminación en {$modulo}",
            $modelo,
            $modelo->getAttributes(),
            []
        );
    }

    /**
     * Atajo para login/logout.
     */
    public static function login(bool $exitoso = true, ?string $email = null): AuditLog
    {
        return self::registrar(
            $exitoso ? AuditEvent::LOGIN : AuditEvent::LOGIN_FALLIDO,
            'auth',
            $exitoso
                ? 'Inicio de sesión exitoso'
                : "Intento de login fallido" . ($email ? " para {$email}" : ''),
            null,
            [],
            [], 
            ['email_intentado' => $email]
        );
    }

    public static function logout(): AuditLog
    {
        return self::registrar(AuditEvent::LOGOUT, 'auth', 'Cierre de sesión');
    }

    /**
     * Limpia datos sensibles antes de guardar.
     */
    private static function limpiarDatos(array $datos): array
    {
        $sensibles = ['password', 'pin', 'remember_token', 'firma_canvas'];

        foreach ($sensibles as $campo) {
            if (isset($datos[$campo])) {
                $datos[$campo] = '[REDACTADO]';
            }
        }

        return $datos;
    }

    private static function metadataBase(): array
    {
        return [
            'ip' => Request::ip(),
            'user_agent' => substr(Request::userAgent() ?? '', 0, 500),
            'url' => Request::fullUrl(),
            'metodo' => Request::method(),
        ];
    }
}