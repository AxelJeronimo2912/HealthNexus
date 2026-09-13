<?php

namespace App\Services;

use App\Models\Lote;
use App\Models\Medicamento;
use App\Models\MovimientoInventario;

class InventarioService
{
    /**
     * Entrada: crea un lote nuevo o suma al existente.
     */
    public static function entrada(
        Medicamento $med,
        int $cantidad,
        ?string $motivo = null,
        ?array $referencia = null,
        ?array $loteData = null
    ): MovimientoInventario {
        // Crear lote nuevo
        $lote = Lote::create([
            'medicamento_id' => $med->id,
            'user_id' => auth()->id(),
            'codigo_lote' => $loteData['codigo_lote'] ?? null,
            'fecha_caducidad' => $loteData['fecha_caducidad'] ?? now()->addYear(),
            'fecha_ingreso' => now(),
            'cantidad_inicial' => $cantidad,
            'cantidad_disponible' => $cantidad,
            'precio_compra' => $loteData['precio_compra'] ?? null,
            'proveedor' => $loteData['proveedor'] ?? null,
        ]);

        // Actualizar stock global del medicamento
        $med->increment('stock_actual', $cantidad);

        return MovimientoInventario::create([
            'medicamento_id' => $med->id,
            'lote_id' => $lote->id,
            'user_id' => auth()->id(),
            'tipo' => 'entrada',
            'cantidad' => $cantidad,
            'stock_anterior' => $med->stock_actual - $cantidad,
            'stock_nuevo' => $med->stock_actual,
            'referencia_tipo' => $referencia['tipo'] ?? null,
            'referencia_id' => $referencia['id'] ?? null,
            'motivo' => $motivo ?? 'Entrada de lote',
        ]);
    }

    /**
     * Salida: descuenta del lote más próximo a caducar (FEFO).
     */
    public static function salida(
        Medicamento $med,
        int $cantidad,
        ?string $motivo = null,
        ?array $referencia = null
    ): array {
        $restante = $cantidad;
        $movimientos = [];

        // Lotes ordenados por caducidad ascendente (primero el que vence antes)
        $lotes = Lote::where('medicamento_id', $med->id)
            ->where('cantidad_disponible', '>', 0)
            ->where('fecha_caducidad', '>=', now())
            ->where('activo', true)
            ->orderBy('fecha_caducidad')
            ->get();

        if ($lotes->sum('cantidad_disponible') < $cantidad) {
            throw new \RuntimeException("Stock insuficiente de {$med->nombre}. Disponible: " . $lotes->sum('cantidad_disponible'));
        }

        foreach ($lotes as $lote) {
            if ($restante <= 0) break;

            $descontar = min($lote->cantidad_disponible, $restante);

            $lote->decrement('cantidad_disponible', $descontar);
            $med->decrement('stock_actual', $descontar);

            $mov = MovimientoInventario::create([
                'medicamento_id' => $med->id,
                'lote_id' => $lote->id,
                'user_id' => auth()->id(),
                'tipo' => 'salida',
                'cantidad' => -$descontar,
                'stock_anterior' => $med->stock_actual + $descontar,
                'stock_nuevo' => $med->stock_actual,
                'referencia_tipo' => $referencia['tipo'] ?? null,
                'referencia_id' => $referencia['id'] ?? null,
                'motivo' => $motivo ?? 'Salida de lote',
            ]);

            $movimientos[] = $mov;
            $restante -= $descontar;
        }

        return $movimientos;
    }

    /**
     * Ajuste manual.
     */
    public static function ajuste(
        Medicamento $med,
        int $nuevoStock,
        ?string $motivo = null
    ): MovimientoInventario {
        $diferencia = $nuevoStock - $med->stock_actual;

        if ($diferencia === 0) {
            return new MovimientoInventario(); // no-op
        }

        $tipo = $diferencia > 0 ? 'entrada' : 'salida';

        // Para el ajuste, actualizamos el stock global y creamos un movimiento sin lote específico
        $med->update(['stock_actual' => $nuevoStock]);

        return MovimientoInventario::create([
            'medicamento_id' => $med->id,
            'user_id' => auth()->id(),
            'tipo' => 'ajuste',
            'cantidad' => $diferencia,
            'stock_anterior' => $nuevoStock - $diferencia,
            'stock_nuevo' => $nuevoStock,
            'motivo' => $motivo ?? 'Ajuste manual',
        ]);
    }

    /**
     * Verifica si hay stock suficiente.
     */
    public static function tieneStock(Medicamento $med, int $cantidad): bool
    {
        $disponible = Lote::where('medicamento_id', $med->id)
            ->where('cantidad_disponible', '>', 0)
            ->where('fecha_caducidad', '>=', now())
            ->where('activo', true)
            ->sum('cantidad_disponible');

        return $disponible >= $cantidad;
    }
}