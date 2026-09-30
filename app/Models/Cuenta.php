<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cuenta extends Model
{
    protected $table = 'cuentas';

    protected $fillable = [
        'paciente_id',
        'folio',
        'estado',              // ← CLAVE: sin esto, update() no guarda el estado
        'subtotal',
        'descuento_global',
        'motivo_descuento',
        'total',
        'pagado',
        'saldo',
        'cerrada_en',
        'notas',
    ];

    protected $casts = [
        'subtotal'         => 'decimal:2',
        'descuento_global' => 'decimal:2',
        'total'            => 'decimal:2',
        'pagado'           => 'decimal:2',
        'saldo'            => 'decimal:2',
        'cerrada_en'       => 'datetime',
    ];

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CuentaItem::class);
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class);
    }

    /** Solo pagos aplicados (no cancelados) */
    public function pagosAplicados(): HasMany
    {
        return $this->hasMany(Pago::class)->where('estado', 'aplicado');
    }

    public static function generarFolio(): string
    {
        $hoy    = now()->format('Ymd');
        $conteo = static::whereDate('created_at', today())->count() + 1;
        return 'CTA-' . $hoy . '-' . str_pad($conteo, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Recalcula subtotal, total, pagado y saldo.
     * Cierra la cuenta automáticamente si el saldo queda en 0.
     */
    public function recalcular(): void
    {
        // 1. Suma de items (con descuentos por item ya aplicados en CuentaItem)
        $subtotal = (float) $this->items()->sum('importe');

        // 2. Descuento global
        $descuentoGlobal = (float) ($this->descuento_global ?? 0);
        $total = max(0, $subtotal - $descuentoGlobal);

        // 3. Solo sumar pagos APLICADOS (no cancelados)
        $pagado = (float) $this->pagosAplicados()->sum('monto');

        // 4. Actualizar
        $this->subtotal = $subtotal;
        $this->total    = $total;
        $this->pagado   = $pagado;
        $this->saldo    = $total - $pagado;

        // 5. 🎯 Cierre automático si el saldo llega a 0 (o menos)
        if ($this->estado === 'abierta' && $this->saldo <= 0) {
            $this->estado     = 'cerrada';
            $this->cerrada_en = now();
        }

        $this->save();
    }

    public function getTotalFormateadoAttribute(): string
    {
        return '$' . number_format((float) $this->total, 2);
    }

    public function getSaldoFormateadoAttribute(): string
    {
        return '$' . number_format((float) $this->saldo, 2);
    }

    public function getEstadoColorAttribute(): string
    {
        return match ($this->estado) {
            'abierta'  => 'bg-amber-100 text-amber-800',
            'cerrada'  => 'bg-emerald-100 text-emerald-800',
            'cancelada'=> 'bg-rose-100 text-rose-800',
            default    => 'bg-slate-100 text-slate-800',
        };
    }
}