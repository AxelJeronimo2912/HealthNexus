<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cuenta extends Model
{
    protected $table = 'cuentas';

    protected $fillable = [
        'paciente_id', 'folio', 'estado',
        'subtotal', 'total', 'pagado', 'saldo',
        'cerrada_en', 'notas',
    ];

    protected $casts = [
        'subtotal'   => 'decimal:2',
        'total'      => 'decimal:2',
        'pagado'     => 'decimal:2',
        'saldo'      => 'decimal:2',
        'cerrada_en' => 'datetime',
    ];

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CuentaItem::class);
    }

    /**
     * Genera un folio único del tipo CTA-YYYYMMDD-NNN.
     */
    public static function generarFolio(): string
    {
        $hoy = now()->format('Ymd');
        $conteo = static::whereDate('created_at', today())->count() + 1;
        return 'CTA-' . $hoy . '-' . str_pad($conteo, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Recalcula subtotal, total y saldo a partir de los items.
     */
    public function recalcular(): void
    {
        $subtotal = (float) $this->items()->sum('importe');

        $this->subtotal = $subtotal;
        $this->total    = $subtotal;
        $this->saldo    = $subtotal - (float) $this->pagado;
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