<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cama extends Model
{
    protected $table = 'camas';

    protected $fillable = [
        'codigo', 'nombre', 'piso', 'ala', 'habitacion', 'area',
        'tipo', 'estado', 'oxigeno', 'monitor', 'ventilador',
        'notas', 'activo','servicio_id'
    ];

    protected $casts = [
        'oxigeno' => 'boolean',
        'monitor' => 'boolean',
        'ventilador' => 'boolean',
        'activo' => 'boolean',
    ];

    public function servicio()
{
    return $this->belongsTo(Servicio::class);
}
    public function getEtiquetaAttribute(): string
    {
        return $this->codigo . ($this->habitacion ? ' — Hab. ' . $this->habitacion : '');
    }

    public function getTipoLabelAttribute(): string
    {
        return match ($this->tipo) {
            'general' => 'General',
            'pediatrica' => 'Pediátrica',
            'uci' => 'UCI',
            'aislamiento' => 'Aislamiento',
            'recuperacion' => 'Recuperación',
            'urgencias' => 'Urgencias',
            default => ucfirst($this->tipo),
        };
    }

    public function getEstadoLabelAttribute(): string
    {
        return match ($this->estado) {
            'disponible' => 'Disponible',
            'ocupada' => 'Ocupada',
            'mantenimiento' => 'Mantenimiento',
            'limpieza' => 'Limpieza',
            'fuera_servicio' => 'Fuera de servicio',
            default => ucfirst($this->estado),
        };
    }

    public function getEstadoColorAttribute(): string
    {
        return match ($this->estado) {
            'disponible' => 'bg-green-100 text-green-800',
            'ocupada' => 'bg-red-100 text-red-800',
            'mantenimiento' => 'bg-yellow-100 text-yellow-800',
            'limpieza' => 'bg-blue-100 text-blue-800',
            'fuera_servicio' => 'bg-gray-200 text-gray-700',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function asignaciones()
{
    return $this->hasMany(CamaPaciente::class);
}

public function pacienteActual()
{
    return $this->hasOne(CamaPaciente::class)->where('activa', true)->latestOfMany();
}
}