<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    protected $table = 'servicios';

    protected $fillable = [
        'codigo', 'nombre', 'tipo', 'ubicacion', 'piso', 'ala',
        'hora_apertura', 'hora_cierre', 'abierto_24h', 'capacidad',
        'extension_telefonica', 'descripcion', 'notas', 'activo',
    ];

    protected $casts = [
        'abierto_24h' => 'boolean',
        'activo' => 'boolean',
        'capacidad' => 'integer',
    ];

    // ---------------- Relaciones ----------------

      public function users()
{
    return $this->belongsToMany(User::class, 'servicio_user')
        ->withPivot(['id', 'rol_en_servicio', 'fecha_inicio', 'fecha_fin', 'activo'])
        ->withTimestamps();
}

    public function camas()
    {
        return $this->hasMany(Cama::class);
    }

    // ---------------- Accessors ----------------

    public function getTipoLabelAttribute(): string
    {
        return match ($this->tipo) {
            'consulta_externa' => 'Consulta Externa',
            'urgencias' => 'Urgencias',
            'hospitalizacion' => 'Hospitalización',
            'quirofano' => 'Quirófano',
            'farmacia' => 'Farmacia',
            'enfermeria' => 'Enfermería',
            'laboratorio' => 'Laboratorio',
            'imagenologia' => 'Imagenología',
            'otro' => 'Otro',
            default => ucfirst($this->tipo),
        };
    }

    public function getTipoColorAttribute(): string
    {
        return match ($this->tipo) {
            'consulta_externa' => 'bg-blue-100 text-blue-800',
            'urgencias' => 'bg-red-100 text-red-800',
            'hospitalizacion' => 'bg-indigo-100 text-indigo-800',
            'quirofano' => 'bg-purple-100 text-purple-800',
            'farmacia' => 'bg-green-100 text-green-800',
            'enfermeria' => 'bg-pink-100 text-pink-800',
            'laboratorio' => 'bg-yellow-100 text-yellow-800',
            'imagenologia' => 'bg-orange-100 text-orange-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getHorarioAttribute(): string
    {
        if ($this->abierto_24h) return 'Abierto 24 horas';
        if (!$this->hora_apertura || !$this->hora_cierre) return 'Sin horario definido';

        $ini = substr($this->hora_apertura, 0, 5);
        $fin = substr($this->hora_cierre, 0, 5);
        return "{$ini} — {$fin}";
    }

    public function especialidades()
{
    return $this->belongsToMany(Especialidad::class, 'especialidad_servicio')
        ->withPivot(['id', 'activo'])
        ->withTimestamps();
}
}