<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dispositivo extends Model
{
    protected $table = 'dispositivos';

    protected $fillable = [
        'user_id', 'huella', 'nombre', 'tipo', 'sistema_operativo',
        'navegador', 'user_agent', 'ip_registro', 'ip_ultimo_acceso',
        'ciudad', 'pais', 'confiable', 'activo',
        'aprobado_en', 'aprobado_por', 'ultimo_acceso', 'total_accesos',
    ];

    protected $casts = [
        'confiable' => 'boolean',
        'activo' => 'boolean',
        'aprobado_en' => 'datetime',
        'ultimo_acceso' => 'datetime',
        'total_accesos' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function aprobadoPor()
    {
        return $this->belongsTo(User::class, 'aprobado_por');
    }

    public function getTipoIconoAttribute(): string
    {
        return match ($this->tipo) {
            'mobile' => 'device-phone-mobile',
            'tablet' => 'device-tablet',
            default => 'computer-desktop',
        };
    }

    public function getEstadoColorAttribute(): string
    {
        if (!$this->activo) return 'bg-red-100 text-red-800';
        if ($this->confiable) return 'bg-green-100 text-green-800';
        return 'bg-yellow-100 text-yellow-800';
    }

    public function getEstadoLabelAttribute(): string
    {
        if (!$this->activo) return 'Bloqueado';
        if ($this->confiable) return 'Confiable';
        return 'Pendiente';
    }
}