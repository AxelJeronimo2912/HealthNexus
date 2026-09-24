<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admision extends Model
{
    protected $table = 'admisiones';

    protected $fillable = [
        'paciente_id', 'user_id', 'medico_id', 'cama_id', 'hospital_derivado_id',
        'folio', 'fecha_hora_llegada', 'tipo', 'triage',
        'motivo', 'diagnostico_presuntivo', 'estado',
        'fecha_derivacion', 'motivo_derivacion', 'pase_salida_pdf', 'notas',
    ];

    protected $casts = [
        'fecha_hora_llegada' => 'datetime',
        'fecha_derivacion' => 'datetime',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function medico()
    {
        return $this->belongsTo(User::class, 'medico_id');
    }

    public function cama()
    {
        return $this->belongsTo(Cama::class);
    }

    public function hospitalDerivado()
    {
        return $this->belongsTo(Hospital::class, 'hospital_derivado_id');
    }

    public function getEstadoLabelAttribute(): string
    {
        return match ($this->estado) {
            'en_espera' => 'En espera',
            'atendido' => 'Atendido',
            'hospitalizado' => 'Hospitalizado',
            'derivado' => 'Derivado',
            'alta' => 'Alta',
            'fallecido' => 'Fallecido',
            default => ucfirst($this->estado),
        };
    }

    public function getEstadoColorAttribute(): string
    {
        return match ($this->estado) {
            'en_espera' => 'bg-yellow-100 text-yellow-800',
            'atendido' => 'bg-blue-100 text-blue-800',
            'hospitalizado' => 'bg-indigo-100 text-indigo-800',
            'derivado' => 'bg-orange-100 text-orange-800',
            'alta' => 'bg-green-100 text-green-800',
            'fallecido' => 'bg-gray-800 text-white',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getTipoLabelAttribute(): string
    {
        return match ($this->tipo) {
            'urgencias' => 'Urgencias',
            'consulta_externa' => 'Consulta Externa',
            'hospitalizacion' => 'Hospitalización',
            'traslado' => 'Traslado',
            default => ucfirst($this->tipo),
        };
    }
}