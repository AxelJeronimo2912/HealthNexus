<?php

namespace App\Models;

use App\Enums\AuditEvent;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'audit_logs';

    protected $fillable = [
        'user_id', 'user_nombre', 'user_rol',
        'evento', 'modulo', 'descripcion',
        'auditable_type', 'auditable_id',
        'datos_antes', 'datos_despues', 'metadata',
        'severidad', 'es_sensible',
    ];

    protected $casts = [
        'datos_antes' => 'array',
        'datos_despues' => 'array',
        'metadata' => 'array',
        'es_sensible' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function auditable()
    {
        return $this->morphTo();
    }

    public function getEventoEnumAttribute(): ?AuditEvent
    {
        return AuditEvent::tryFrom($this->evento);
    }

    public function getEventoLabelAttribute(): string
    {
        return $this->evento_enum?->etiqueta() ?? ucfirst($this->evento);
    }

    public function getEventoColorAttribute(): string
    {
        return match ($this->severidad) {
            'critical' => 'bg-red-100 text-red-800',
            'warning' => 'bg-yellow-100 text-yellow-800',
            default => 'bg-blue-100 text-blue-800',
        };
    }

    public function getModuloLabelAttribute(): string
    {
        return ucfirst(str_replace('_', ' ', $this->modulo));
    }
}