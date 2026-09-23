<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdministracionMedicamento extends Model
{
    protected $table = 'administraciones_medicamento';

    protected $fillable = [
        'paciente_id', 'medicamento_id', 'user_id', 'consulta_id',
        'dosis', 'via', 'administrado_en', 'reaccion_adversa', 'observaciones',
    ];

    protected $casts = [
        'administrado_en' => 'datetime',
        'reaccion_adversa' => 'boolean',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function medicamento()
    {
        return $this->belongsTo(Medicamento::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function consulta()
    {
        return $this->belongsTo(Consulta::class);
    }
}