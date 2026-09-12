<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CamaPaciente extends Model
{
    protected $table = 'cama_paciente';

    protected $fillable = [
        'cama_id', 'paciente_id', 'signo_vital_id', 'user_id',
        'fecha_ingreso', 'fecha_egreso', 'motivo', 'activa',
    ];

    protected $casts = [
        'fecha_ingreso' => 'datetime',
        'fecha_egreso' => 'datetime',
        'activa' => 'boolean',
    ];

    public function cama()
    {
        return $this->belongsTo(Cama::class);
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function signoVital()
    {
        return $this->belongsTo(SignoVital::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}