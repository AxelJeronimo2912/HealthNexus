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
}