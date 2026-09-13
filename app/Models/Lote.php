<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Lote extends Model
{
    protected $table = 'lotes';

    protected $fillable = [
        'medicamento_id', 'user_id', 'codigo_lote',
        'fecha_caducidad', 'fecha_ingreso',
        'cantidad_inicial', 'cantidad_disponible',
        'precio_compra', 'proveedor', 'notas', 'activo',
    ];

    protected $casts = [
        'fecha_caducidad' => 'date',
        'fecha_ingreso' => 'date',
        'cantidad_inicial' => 'integer',
        'cantidad_disponible' => 'integer',
        'precio_compra' => 'decimal:2',
        'activo' => 'boolean',
    ];

    public function medicamento()
    {
        return $this->belongsTo(Medicamento::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoInventario::class);
    }

    // ---------------- Accessors ----------------

    public function getDiasParaCaducarAttribute(): int
    {
        return now()->diffInDays($this->fecha_caducidad, false);
    }

    public function getEstaCaducadoAttribute(): bool
    {
        return $this->fecha_caducidad->isPast();
    }

    public function getProximoACaducarAttribute(): bool
    {
        $dias = $this->dias_para_caducar;
        return !$this->esta_caducado && $dias <= 30;
    }

    public function getEstadoLabelAttribute(): string
    {
        if ($this->esta_caducado) return 'Caducado';
        if ($this->proximo_a_caducar) return 'Próximo a caducar';
        if ($this->cantidad_disponible <= 0) return 'Agotado';
        return 'Disponible';
    }

    public function getEstadoColorAttribute(): string
    {
        if ($this->esta_caducado) return 'bg-red-100 text-red-800';
        if ($this->proximo_a_caducar) return 'bg-yellow-100 text-yellow-800';
        if ($this->cantidad_disponible <= 0) return 'bg-gray-200 text-gray-700';
        return 'bg-green-100 text-green-800';
    }
}