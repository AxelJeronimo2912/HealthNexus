<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seguimiento extends Model
{
    protected $table = 'seguimientos';

    protected $fillable = [
        'paciente_id', 'user_id', 'cama_id',
        'tipo', 'estado_paciente', 'contenido',
        'temperatura', 'frecuencia_cardiaca', 'frecuencia_respiratoria',
        'presion_arterial', 'saturacion_oxigeno',
    ];

    protected $casts = [
        'temperatura' => 'decimal:1',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cama()
    {
        return $this->belongsTo(Cama::class);
    }

    public function getTipoLabelAttribute(): string
    {
        return match ($this->tipo) {
            'evolucion' => 'Evolución médica',
            'nota_enfermeria' => 'Nota de enfermería',
            'interconsulta' => 'Interconsulta',
            'traslado' => 'Traslado',
            'alta' => 'Alta',
            default => ucfirst($this->tipo),
        };
    }

    public function getEstadoColorAttribute(): string
    {
        return match ($this->estado_paciente) {
            'estable' => 'bg-green-100 text-green-800',
            'mejorando' => 'bg-blue-100 text-blue-800',
            'grave' => 'bg-orange-100 text-orange-800',
            'critico' => 'bg-red-100 text-red-800',
            'fallecido' => 'bg-gray-800 text-white',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}