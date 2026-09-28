<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SimuladorPredictivoCaso extends Model
{
    protected $table = 'simulador_predictivo_casos';

    protected $fillable = [
        'user_id', 'paciente_id',
        'fc', 'spo2', 'temp', 'edad',
        'prob_critico', 'riesgo_svm', 'recomendacion_arbol', 'voto_rf', 'spo2_esperado',
        'diagnostico_final', 'costo_real', 'costo_predicho',
        'dias_real', 'dias_predicho', 'cerrado',
    ];

    protected $casts = [
        'temp'            => 'decimal:1',
        'prob_critico'    => 'decimal:4',
        'spo2_esperado'   => 'decimal:2',
        'costo_real'      => 'decimal:2',
        'costo_predicho'  => 'decimal:2',
        'cerrado'         => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }
}