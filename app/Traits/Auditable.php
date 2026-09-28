<?php

namespace App\Traits;

use App\Services\AuditoriaService;

trait Auditable
{
    /**
     * Boot del trait: registra los eventos del modelo.
     */
    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            AuditoriaService::creado($model, static::moduloAuditoria());
        });

        static::updated(function ($model) {
            $antes = $model->getOriginal();
            AuditoriaService::actualizado($model, static::moduloAuditoria(), $antes);
        });

        static::deleted(function ($model) {
            AuditoriaService::eliminado($model, static::moduloAuditoria());
        });
    }

    /**
     * Nombre del módulo para la auditoría.
     * Sobrescribe este método en el modelo si quieres un nombre específico.
     */
    public static function moduloAuditoria(): string
    {
        return strtolower(class_basename(static::class)) . 's';
    }
}