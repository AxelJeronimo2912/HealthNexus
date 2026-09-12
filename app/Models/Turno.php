<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Turno extends Model
{
    protected $table = 'turnos';

    protected $fillable = [
        'nombre', 'codigo', 'hora_inicio', 'hora_fin',
        'cruza_medianoche', 'duracion_horas', 'descripcion', 'activo',
    ];

    protected $casts = [
    'cruza_medianoche' => 'boolean',
    'activo' => 'boolean',
];

    public function users()
{
    return $this->belongsToMany(User::class, 'turno_user')
        ->withPivot(['id', 'dia_semana', 'fecha_inicio', 'fecha_fin', 'area', 'activo', 'notas'])
        ->withTimestamps();
}

    /**
     * Calcula la duración en horas al guardar.
     */
  protected static function booted(): void
{
    static::saving(function (Turno $turno) {
        if ($turno->hora_inicio && $turno->hora_fin) {
            $inicio = \Carbon\Carbon::parse('2000-01-01 ' . substr($turno->hora_inicio, 0, 5));
            $fin = \Carbon\Carbon::parse('2000-01-01 ' . substr($turno->hora_fin, 0, 5));

            if ($fin->lessThanOrEqualTo($inicio)) {
                $fin->addDay();
                $turno->cruza_medianoche = true;
            } else {
                $turno->cruza_medianoche = false;
            }

            $turno->duracion_horas = $inicio->diffInHours($fin);
        }
    });
}
public function getRangoAttribute(): string
{
    $inicio = substr($this->hora_inicio, 0, 5);
    $fin = substr($this->hora_fin, 0, 5);
    return "{$inicio} — {$fin}";
}
}