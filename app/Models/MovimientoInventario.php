<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoInventario extends Model
{
    protected $table = 'movimientos_inventario';

    protected $fillable = [
        'medicamento_id', 'user_id', 'tipo',
        'cantidad', 'stock_anterior', 'stock_nuevo',
        'referencia_tipo', 'referencia_id', 'motivo',
    ];

    public function medicamento()
    {
        return $this->belongsTo(Medicamento::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getTipoLabelAttribute(): string
    {
        return match ($this->tipo) {
            'entrada' => 'Entrada',
            'salida' => 'Salida',
            'ajuste' => 'Ajuste',
            'devolucion' => 'Devolución',
            default => ucfirst($this->tipo),
        };
    }

    public function getTipoColorAttribute(): string
    {
        return match ($this->tipo) {
            'entrada', 'devolucion' => 'bg-green-100 text-green-800',
            'salida' => 'bg-red-100 text-red-800',
            'ajuste' => 'bg-yellow-100 text-yellow-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function lote()
{
    return $this->belongsTo(Lote::class);
}
}