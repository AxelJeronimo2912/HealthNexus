<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medicamento extends Model
{
    protected $table = 'medicamentos';

    protected $fillable = [
        'nombre', 'sustancia_activa', 'presentacion', 'concentracion',
        'via_administracion', 'laboratorio', 'codigo_barras', 'registro_sanitario',
        'grupo_terapeutico', 'psicotropico', 'antibiotico', 'controlado',
        'unidad_medida', 'stock_minimo', 'stock_maximo',
        'precio_compra', 'precio_venta', 'activo',
    ];

    protected $casts = [
        'psicotropico' => 'boolean',
        'antibiotico' => 'boolean',
        'controlado' => 'boolean',
        'activo' => 'boolean',
        'precio_compra' => 'decimal:2',
        'precio_venta' => 'decimal:2',
    ];

    public function getNombreCompletoAttribute()
    {
        $partes = array_filter([
            $this->nombre,
            $this->concentracion,
            $this->presentacion,
        ]);
        return implode(' ', $partes);
    }
}