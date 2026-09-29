<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alerta extends Model
{
    protected $table = 'alertas';

    protected $fillable = [
        'tipo', 'categoria', 'nivel', 'titulo', 'mensaje', 'datos',
        'referencia_tipo', 'referencia_id',
        'estado', 'resuelta_por', 'resuelta_en',
        'user_id', 'expira_en',
    ];

    protected $casts = [
        'datos' => 'array',
        'resuelta_en' => 'datetime',
        'expira_en' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function resueltaPor()
    {
        return $this->belongsTo(User::class, 'resuelta_por');
    }

    public function getNivelLabelAttribute(): string
    {
        return match ($this->nivel) {
            'info' => 'Información',
            'advertencia' => 'Advertencia',
            'critico' => 'Crítico',
            default => ucfirst($this->nivel),
        };
    }

    public function getNivelColorAttribute(): string
    {
        return match ($this->nivel) {
            'info' => 'bg-blue-100 text-blue-800 border-blue-300',
            'advertencia' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
            'critico' => 'bg-red-100 text-red-800 border-red-300',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getNivelIconoAttribute(): string
    {
        return match ($this->nivel) {
            'info' => 'information-circle',
            'advertencia' => 'exclamation-triangle',
            'critico' => 'exclamation-circle',
            default => 'bell',
        };
    }

    public function getEstadoColorAttribute(): string
    {
        return match ($this->estado) {
            'activa' => 'bg-red-50 border-red-200',
            'vista' => 'bg-yellow-50 border-yellow-200',
            'resuelta' => 'bg-green-50 border-green-200',
            'descartada' => 'bg-gray-50 border-gray-200',
            default => 'bg-white border-gray-200',
        };
    }

    public function scopeActivas($query)
    {
        return $query->whereIn('estado', ['activa', 'vista']);
    }

    public function scopeCriticas($query)
    {
        return $query->where('nivel', 'critico')->activas();
    }
}