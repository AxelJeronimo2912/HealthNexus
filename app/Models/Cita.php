<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    protected $table = 'citas';

    protected $fillable = [
        'paciente_id', 'medico_id', 'turno_id', 'signo_vital_id', 'creado_por',
        'fecha_hora', 'duracion_minutos', 'estado', 'triage_al_momento',
        'motivo', 'notas',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'duracion_minutos' => 'integer',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function medico()
    {
        return $this->belongsTo(User::class, 'medico_id');
    }

    public function turno()
    {
        return $this->belongsTo(Turno::class);
    }

    public function signoVital()
    {
        return $this->belongsTo(SignoVital::class);
    }

    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function getFechaHoraFinAttribute(): Carbon
    {
        return $this->fecha_hora->copy()->addMinutes($this->duracion_minutos);
    }

    public function getEstadoLabelAttribute(): string
    {
        return match ($this->estado) {
            'programada' => 'Programada',
            'confirmada' => 'Confirmada',
            'en_curso' => 'En curso',
            'atendida' => 'Atendida',
            'cancelada' => 'Cancelada',
            'no_asistio' => 'No asistió',
            default => ucfirst($this->estado),
        };
    }

    public function getEstadoColorAttribute(): string
    {
        return match ($this->estado) {
            'programada' => 'bg-blue-100 text-blue-800',
            'confirmada' => 'bg-indigo-100 text-indigo-800',
            'en_curso' => 'bg-yellow-100 text-yellow-800',
            'atendida' => 'bg-green-100 text-green-800',
            'cancelada' => 'bg-red-100 text-red-800',
            'no_asistio' => 'bg-gray-200 text-gray-700',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}