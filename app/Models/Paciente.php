<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    protected $table = 'pacientes';
    protected $fillable = [
        'nombre', 'apellido_paterno', 'apellido_materno', 'fecha_nacimiento',
        'sexo', 'estado_civil', 'nacionalidad', 'pais_nacimiento',
        'estado_nacimiento', 'curp', 'pasaporte',
        'telefono_principal', 'correo_electronico', 'ocupacion', 'responsable_nombre',
        'tipo_sanguineo', 'alergias', 'enfermedades_cronicas',
        'estado_id', 'municipio_id', 'colonia', 'calle',
        'numero_exterior', 'numero_interior', 'activo',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'activo' => 'boolean',
    ];

    public function getNombreCompletoAttribute()
    {
        return trim("{$this->nombre} {$this->apellido_paterno} {$this->apellido_materno}");
    }

    public function getEdadAttribute()
    {
        return $this->fecha_nacimiento
            ? $this->fecha_nacimiento->age
            : null;
    }
public function citas()
{
    return $this->hasMany(Cita::class);
}

public function medicoAsignado()
{
    return $this->hasOne(PacienteMedico::class)->where('activo', true);
}

public function historialMedicos()
{
    return $this->hasMany(PacienteMedico::class);
}
    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }

    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }

    public function signosVitales()
{
    return $this->hasMany(SignoVital::class);
}

public function asignacionesCama()
{
    return $this->hasMany(CamaPaciente::class);
}

public function ultimoSignoVital()
{
    return $this->hasOne(SignoVital::class)->latestOfMany();
}

/**
 * Accessor directo al objeto para usar en vistas.
 */
public function getUltimoSignoAttribute(): ?SignoVital
{
    return $this->relationLoaded('ultimoSignoVital')
        ? $this->ultimoSignoVital
        : $this->ultimoSignoVital()->first();
}

public function consultas()
{
    return $this->hasMany(Consulta::class);
}

public function seguimientos()
{
    return $this->hasMany(Seguimiento::class)->orderByDesc('created_at');
}

public function seguimientoActual()
{
    return $this->hasOne(Seguimiento::class)->latestOfMany();
}

/**
 * ¿Este paciente debe estar en seguimiento?
 * Criterio: tiene cama activa O triage grave reciente (últimas 24h).
 */
public function getEnSeguimientoAttribute(): bool
{
    // 1. ¿Tiene cama asignada activa?
    $tieneCama = \App\Models\CamaPaciente::where('paciente_id', $this->id)
        ->where('activa', true)
        ->exists();

    if ($tieneCama) return true;

    // 2. ¿Triage grave en las últimas 24 horas?
    $triageGrave = $this->signosVitales()
        ->whereIn('triage', ['rojo', 'naranja', 'amarillo'])
        ->where('created_at', '>=', now()->subHours(24))
        ->exists();

    return $triageGrave;
}

}