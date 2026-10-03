<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pago extends Model
{
    protected $table = 'pagos';

    protected $fillable = [
        'cuenta_id', 'user_id', 'folio', 'monto', 'metodo', 'referencia',
        'estado', 'motivo_cancelacion', 'cancelado_en', 'cancelado_por',
        'pagado_en', 'notas',
    ];

    protected $casts = [
        'monto'        => 'decimal:2',
        'pagado_en'    => 'datetime',
        'cancelado_en' => 'datetime',
    ];

    public function cuenta(): BelongsTo
    {
        return $this->belongsTo(Cuenta::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function canceladoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelado_por');
    }

    public static function generarFolio(): string
    {
        $hoy = now()->format('Ymd');
        $conteo = static::whereDate('created_at', today())->count() + 1;
        return 'PAG-' . $hoy . '-' . str_pad($conteo, 3, '0', STR_PAD_LEFT);
    }

    public function getMetodoLabelAttribute(): string
    {
        return match ($this->metodo) {
            'efectivo'      => 'Efectivo',
            'tarjeta'       => 'Tarjeta',
            'transferencia' => 'Transferencia',
            'otro'          => 'Otro',
            default         => ucfirst($this->metodo),
        };
    }

    // 👇 NUEVO: recalcula la cuenta al guardar o borrar un pago
    protected static function booted(): void
    {
        static::saved(function (Pago $pago) {
            $pago->cuenta?->recalcular();
        });

        static::deleted(function (Pago $pago) {
            $pago->cuenta?->recalcular();
        });
    }
}