<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SlaEvento extends Model
{
    protected $table = 'sla_eventos';

    protected $fillable = [
        'modulo', 'event_type', 'referencia_tipo', 'referencia_id',
        'duration_minutes', 'start_hour', 'is_outlier', 'outlier_z_score',
        'user_id', 'evento_en',
    ];

    protected $casts = [
        'duration_minutes' => 'decimal:2',
        'outlier_z_score' => 'decimal:4',
        'is_outlier' => 'boolean',
        'evento_en' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getModuloLabelAttribute(): string
    {
        return match ($this->modulo) {
            'quirofano' => 'Quirófano',
            'urgencias' => 'Urgencias',
            'farmacia' => 'Farmacia',
            'hospitalizacion' => 'Hospitalización',
            default => ucfirst($this->modulo),
        };
    }

    public function getModuloColorAttribute(): string
    {
        return match ($this->modulo) {
            'quirofano' => 'bg-amber-100 text-amber-800',
            'urgencias' => 'bg-blue-100 text-blue-800',
            'farmacia' => 'bg-emerald-100 text-emerald-800',
            'hospitalizacion' => 'bg-purple-100 text-purple-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getSeveridadZAttribute(): string
    {
        $z = abs($this->outlier_z_score ?? 0);

        return match (true) {
            $z >= 4.5 => 'critico',
            $z >= 3.5 => 'director',
            $z >= 2.5 => 'jefe',
            default => 'normal',
        };
    }
}