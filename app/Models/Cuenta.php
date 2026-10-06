<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cuenta extends Model
{
    use Auditable;

    protected $table = 'cuentas';

    protected $fillable = [
        'paciente_id', 'folio', 'estado',
        'subtotal', 'descuento_global', 'motivo_descuento',
        'total', 'pagado', 'saldo', 'cerrada_en', 'notas',
    ];

    protected $casts = [
        'subtotal'         => 'decimal:2',
        'descuento_global' => 'decimal:2',
        'total'            => 'decimal:2',
        'pagado'           => 'decimal:2',
        'saldo'            => 'decimal:2',
        'cerrada_en'       => 'datetime',
    ];

    /* ---------- Auditoría ---------- */
    public static function moduloAuditoria(): string
    {
        return 'cuentas';
    }

    /**
     * `recalcular()` guarda la cuenta muchas veces (subtotal, total, pagado,
     * saldo cambian). No queremos inundar auditoría con cada recálculo.
     * Solo dejamos que el controlador registre los eventos semánticos.
     */
    public function silenciarCambiosAutomaticos(): bool
    {
        return true;
    }

    /* ---------- Relaciones ---------- */
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

    public function pagosAplicados(): HasMany
    {
        return $this->hasMany(Pago::class)->where('estado', 'aplicado');
    }

    /* ---------- Lógica ---------- */
    public static function generarFolio(): string
    {
        $hoy    = now()->format('Ymd');
        $conteo = static::whereDate('created_at', today())->count() + 1;
        return 'CTA-' . $hoy . '-' . str_pad($conteo, 3, '0', STR_PAD_LEFT);
    }

    public function recalcular(): void
    {
        $subtotal        = (float) $this->items()->sum('importe');
        $descuentoGlobal = (float) ($this->descuento_global ?? 0);
        $total           = max(0, $subtotal - $descuentoGlobal);
        $pagado          = (float) $this->pagosAplicados()->sum('monto');

        $this->subtotal = $subtotal;
        $this->total    = $total;
        $this->pagado   = $pagado;
        $this->saldo    = $total - $pagado;

        if ($this->estado === 'abierta' && $this->saldo <= 0) {
            $this->estado     = 'cerrada';
            $this->cerrada_en = now();
        }

        $this->save();
    }

    /* ---------- Accessors ---------- */
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
            'abierta'   => 'bg-amber-100 text-amber-800',
            'cerrada'   => 'bg-emerald-100 text-emerald-800',
            'cancelada' => 'bg-rose-100 text-rose-800',
            default     => 'bg-slate-100 text-slate-800',
        };
    }
}