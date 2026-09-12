<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PacienteMedico extends Model
{
    protected $table = 'paciente_medico';

    protected $fillable = [
        'paciente_id', 'medico_id', 'asignado_por', 'asignado_en', 'activo', 'motivo',
    ];

    protected $casts = [
        'asignado_en' => 'datetime',
        'activo' => 'boolean',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function medico()
    {
        return $this->belongsTo(User::class, 'medico_id');
    }

    public function asignadoPor()
    {
        return $this->belongsTo(User::class, 'asignado_por');
    }
}