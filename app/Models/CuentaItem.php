<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CuentaItem extends Model
{
    protected $table = 'cuenta_items';

    protected $fillable = [
        'cuenta_id', 'servicio_id', 'cita_id', 'user_id',
        'concepto', 'cantidad', 'precio_unitario', 'importe', 'notas','descuento', 'motivo_descuento',
    ];

    protected $casts = [
        'cantidad'        => 'integer',
        'precio_unitario' => 'decimal:2',
        'importe'         => 'decimal:2',
        'descuento' => 'decimal:2',
    ];

    public function cuenta(): BelongsTo
    {
        return $this->belongsTo(Cuenta::class);
    }

    public function servicio(): BelongsTo
    {
        return $this->belongsTo(Servicio::class);
    }

    public function cita(): BelongsTo
    {
        return $this->belongsTo(Cita::class);
    }

    protected static function booted(): void
{
    static::saving(function (CuentaItem $item) {
        $bruto = $item->cantidad * (float) $item->precio_unitario;
        $item->importe = max(0, $bruto - (float) ($item->descuento ?? 0));
    });

    static::saved(function (CuentaItem $item) {
        $item->cuenta?->recalcular();
    });

    static::deleted(function (CuentaItem $item) {
        $item->cuenta?->recalcular();
    });
}
}