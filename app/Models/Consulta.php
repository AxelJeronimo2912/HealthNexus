<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consulta extends Model
{
    protected $table = 'consultas';

    protected $fillable = [
        'cita_id', 'paciente_id', 'medico_id',
        'subjetivo', 'objetivo', 'analisis', 'plan',
        'diagnostico_principal', 'diagnostico_secundario',
        'temperatura', 'frecuencia_cardiaca', 'frecuencia_respiratoria',
        'presion_arterial', 'saturacion_oxigeno', 'glucosa',
        'peso', 'talla', 'imc', 'perimetro_abdominal',
        'receta_libre', 'notas', 'estado', 'finalizada_en',
        'diagnostico_principal_id', 'diagnostico_secundario_id',
        'dispensada', 'dispensada_en', 'dispensada_por', 'notas_dispensacion',
        'especialidad_id'
        ];

    protected $casts = [
    
        'subjetivo'    => 'encrypted',
        'objetivo'     => 'encrypted',
        'analisis'     => 'encrypted',
        'plan'         => 'encrypted',
        'receta_libre' => 'encrypted',
        'notas'        => 'encrypted',
        'finalizada_en'       => 'datetime',
        'temperatura'         => 'decimal:1',
        'peso'                => 'decimal:2',
        'talla'               => 'decimal:2',
        'imc'                 => 'decimal:1',
        'perimetro_abdominal' => 'decimal:2',
        'dispensada'          => 'boolean',
        'dispensada_en'       => 'datetime',
    ];

    public function cita()
    {
        return $this->belongsTo(Cita::class);
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function medico()
    {
        return $this->belongsTo(User::class, 'medico_id');
    }

    public function medicamentos()
    {
        return $this->belongsToMany(Medicamento::class, 'consulta_medicamento')
            ->withPivot(['dosis', 'via', 'frecuencia', 'duracion', 'indicaciones'])
            ->withTimestamps();
    }

    public function getTieneRecetaAttribute(): bool
    {
        return $this->medicamentos()->count() > 0
            || !empty($this->receta_libre);
    }

    protected static function booted(): void
    {
        static::saving(function (Consulta $c) {
            if ($c->peso && $c->talla && $c->talla > 0) {
                $c->imc = round($c->peso / ($c->talla ** 2), 1);
            }
        });
    }

    public function diagnosticoPrincipal()
    {
        return $this->belongsTo(Diagnostico::class, 'diagnostico_principal_id');
    }

    public function diagnosticoSecundario()
    {
        return $this->belongsTo(Diagnostico::class, 'diagnostico_secundario_id');
    }

    public function dispensadaPor()
    {
        return $this->belongsTo(User::class, 'dispensada_por');
    }


    public function especialidad()
{
    return $this->belongsTo(Especialidad::class);
}
}