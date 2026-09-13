<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Diagnostico extends Model
{
    protected $table = 'diagnosticos';

    protected $fillable = ['codigo', 'nombre', 'grupo', 'activo'];

    protected $casts = ['activo' => 'boolean'];

    public function getEtiquetaAttribute(): string
    {
        return "{$this->codigo} — {$this->nombre}";
    }
}