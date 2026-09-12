<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable, HasRoles;

    protected $fillable = [
        'name', 'email', 'password',
        'nombre', 'apellido_paterno', 'apellido_materno', 'curp',
        'fecha_nacimiento', 'cedula_profesional', 'telefono',
        'telefono_contacto', 'tipo_servicio',
        'foto_perfil', 'firma_archivo', 'firma_canvas', 'activo','pin',

    ];

    protected $hidden = [
        'password',
        'remember_token',
        'pin',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'fecha_nacimiento' => 'date',
            'activo' => 'boolean',
            'password' => 'hashed',
             'pin' => 'hashed',
        ];
    }

    public function getNombreCompletoAttribute()
    {
        return trim("{$this->nombre} {$this->apellido_paterno} {$this->apellido_materno}");
    }


  public function turnos()
{
    return $this->belongsToMany(Turno::class, 'turno_user')
        ->withPivot(['id', 'dia_semana', 'fecha_inicio', 'fecha_fin', 'area', 'activo', 'notas'])
        ->withTimestamps();
}


public function citasComoMedico()
{
    return $this->hasMany(Cita::class, 'medico_id');
}

public function pacientesAsignados()
{
    return $this->hasMany(PacienteMedico::class, 'medico_id')->where('activo', true);
}

/**
 * Devuelve los turnos activos del usuario en una fecha dada.
 */
public function turnosEn($fecha = null)
{
    $fecha = $fecha ? \Carbon\Carbon::parse($fecha) : now();

    return $this->turnos()
        ->wherePivot('activo', true)
        ->wherePivot('fecha_inicio', '<=', $fecha->toDateString())
        ->where(function ($q) use ($fecha) {
            $q->whereNull('turno_user.fecha_fin')
              ->orWhere('turno_user.fecha_fin', '>=', $fecha->toDateString());
        })
        ->where(function ($q) use ($fecha) {
            $q->whereNull('turno_user.dia_semana')
              ->orWhere('turno_user.dia_semana', $fecha->dayOfWeek);
        })
        ->get();
}

public function trabajaEn($fecha, $hora = null): bool
{
    $fecha = \Carbon\Carbon::parse($fecha);
    $horaStr = $hora ? (is_string($hora) ? $hora : $hora->format('H:i')) : null;

    foreach ($this->turnosEn($fecha) as $turno) {
        if (!$horaStr) return true;

        $horaInicio = substr($turno->hora_inicio, 0, 5);
        $horaFin = substr($turno->hora_fin, 0, 5);

        // Construir las 3 fechas con la MISMA fecha
        $inicio = \Carbon\Carbon::parse($fecha->toDateString() . ' ' . $horaInicio);
        $fin = \Carbon\Carbon::parse($fecha->toDateString() . ' ' . $horaFin);
        $horaCita = \Carbon\Carbon::parse($fecha->toDateString() . ' ' . $horaStr);

        if ($fin->lessThanOrEqualTo($inicio)) {
            $fin->addDay();
        }

        if ($horaCita->between($inicio, $fin)) {
            return true;
        }
    }

    


    
    return false;
}
}