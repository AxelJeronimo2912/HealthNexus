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
        'unidad_medida', 'stock_minimo', 'stock_maximo', 'stock_actual',
        'precio_compra', 'precio_venta', 'activo',
    ];

    protected $casts = [
        'psicotropico' => 'boolean',
        'antibiotico' => 'boolean',
        'controlado' => 'boolean',
        'activo' => 'boolean',
        'precio_compra' => 'decimal:2',
        'precio_venta' => 'decimal:2',
        'stock_actual' => 'integer',
        'stock_minimo' => 'integer',
        'stock_maximo' => 'integer',
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

    public function consultas()
    {
        return $this->belongsToMany(Consulta::class, 'consulta_medicamento')
            ->withPivot(['dosis', 'via', 'frecuencia', 'duracion', 'indicaciones'])
            ->withTimestamps();
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoInventario::class)->orderByDesc('created_at');
    }

    /**
     * ¿Tiene stock suficiente?
     */
    public function tieneStock(int $cantidad = 1): bool
    {
        return $this->stock_actual >= $cantidad;
    }

    /**
     * ¿Está por debajo del mínimo?
     */
    public function getStockBajoAttribute(): bool
    {
        return $this->stock_actual <= $this->stock_minimo;
    }

    public function lotes()
{
    return $this->hasMany(Lote::class)->orderBy('fecha_caducidad');
}

public function lotesDisponibles()
{
    return $this->hasMany(Lote::class)
        ->where('cantidad_disponible', '>', 0)
        ->where('fecha_caducidad', '>=', now())
        ->where('activo', true)
        ->orderBy('fecha_caducidad');
}

/**
 * Stock total calculado sumando los lotes vigentes.
 */
public function getStockTotalCalculadoAttribute(): int
{
    return $this->lotes()
        ->where('cantidad_disponible', '>', 0)
        ->where('fecha_caducidad', '>=', now())
        ->where('activo', true)
        ->sum('cantidad_disponible');
}
}