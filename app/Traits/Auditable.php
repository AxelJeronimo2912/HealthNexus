<?php

namespace App\Traits;

use App\Services\AuditoriaService;

trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            AuditoriaService::creado($model, static::moduloAuditoria());
        });

        static::updated(function ($model) {
    $cambios = collect($model->getChanges())->except(['updated_at'])->toArray();

    if (empty($cambios)) {
        return;
    }

    // Silenciar cambios automáticos (ej. recalcular() de Cuenta)
    if (
        method_exists($model, 'silenciarCambiosAutomaticos')
        && $model->silenciarCambiosAutomaticos()
    ) {
        return;
    }

    // Silenciar solo cambio de estado
    if (
        method_exists($model, 'silenciarCambioEstadoAuditoria')
        && $model->silenciarCambioEstadoAuditoria()
        && count($cambios) === 1
        && array_key_exists('estado', $cambios)
    ) {
        return;
    }

    $antes = collect($model->getOriginal())->only(array_keys($cambios))->toArray();
    AuditoriaService::actualizado($model, static::moduloAuditoria(), $antes);
});

        static::deleted(function ($model) {
            AuditoriaService::eliminado($model, static::moduloAuditoria());
        });
    }

    public static function moduloAuditoria(): string
    {
        return strtolower(class_basename(static::class)) . 's';
    }
}