<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    protected $table = 'municipios';
    protected $fillable = ['estado_id', 'nombre'];

    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }

    public function pacientes()
    {
        return $this->hasMany(Paciente::class);
    }
}