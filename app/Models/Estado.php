<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    protected $table = 'estados';
    protected $fillable = ['nombre', 'abreviatura'];

    public function municipios()
    {
        return $this->hasMany(Municipio::class);
    }

    public function pacientes()
    {
        return $this->hasMany(Paciente::class);
    }
}