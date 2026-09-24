<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Especialidad extends Model
{
    protected $table = 'especialidades';

    protected $fillable = [
        'codigo', 'nombre', 'descripcion', 'grupo',
        'duracion_consulta_default', 'color', 'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'duracion_consulta_default' => 'integer',
    ];

    // ---------------- Relaciones ----------------

    public function medicos()
    {
        return $this->belongsToMany(User::class, 'especialidad_user')
            ->withPivot(['id', 'es_principal', 'numero_cedula_especialidad', 'fecha_certificacion', 'activo'])
            ->withTimestamps();
    }

    public function servicios()
    {
        return $this->belongsToMany(Servicio::class, 'especialidad_servicio')
            ->withPivot(['id', 'activo'])
            ->withTimestamps();
    }

    public function citas()
    {
        return $this->hasMany(Cita::class);
    }

    public function consultas()
    {
        return $this->hasMany(Consulta::class);
    }

    // ---------------- Accessors ----------------

    public function getGrupoLabelAttribute(): string
    {
        return match ($this->grupo) {
            'clinica' => 'Clínica',
            'quirurgica' => 'Quirúrgica',
            'diagnostica' => 'Diagnóstica',
            'basica' => 'Básica',
            'otra' => 'Otra',
            default => ucfirst($this->grupo),
        };
    }

    public function getGrupoColorAttribute(): string
    {
        return match ($this->grupo) {
            'clinica' => 'bg-blue-100 text-blue-800',
            'quirurgica' => 'bg-red-100 text-red-800',
            'diagnostica' => 'bg-purple-100 text-purple-800',
            'basica' => 'bg-green-100 text-green-800',
            'otra' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getColorHexAttribute(): string
    {
        return $this->color ? '#' . ltrim($this->color, '#') : '#3B82F6';
    }
}