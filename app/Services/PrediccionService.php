<?php

namespace App\Services;

use App\Models\Medicamento;
use App\Models\MovimientoInventario;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PrediccionService
{
    /**
     * Obtiene el historial de consumo diario de un medicamento.
     * Devuelve un array indexado por fecha con la cantidad consumida.
     */
    public static function historialConsumo(Medicamento $med, int $dias = 90): array
    {
        $desde = now()->subDays($dias);

        $movimientos = MovimientoInventario::where('medicamento_id', $med->id)
            ->where('tipo', 'salida')
            ->where('created_at', '>=', $desde)
            ->select(
                DB::raw('DATE(created_at) as fecha'),
                DB::raw('SUM(ABS(cantidad)) as cantidad')
            )
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->pluck('cantidad', 'fecha')
            ->toArray();

        $historial = [];
        for ($i = $dias; $i >= 0; $i--) {
            $fecha = now()->subDays($i)->format('Y-m-d');
            $historial[$fecha] = (int) ($movimientos[$fecha] ?? 0);
        }

        return $historial;
    }

    /**
     * Promedio simple en los últimos N días.
     */
    public static function promedioSimple(Medicamento $med, int $dias): float
    {
        $total = MovimientoInventario::where('medicamento_id', $med->id)
            ->where('tipo', 'salida')
            ->where('created_at', '>=', now()->subDays($dias))
            ->sum(DB::raw('ABS(cantidad)'));

        return round($total / max($dias, 1), 2);
    }

    /**
     * Promedio móvil ponderado.
     * Más peso a los días recientes.
     */
    public static function promedioMovilPonderado(array $historial): float
    {
        $n = count($historial);
        if ($n === 0) return 0;

        $sumaPonderada = 0;
        $sumaPesos = 0;

        $i = 1;
        foreach (array_reverse($historial) as $cantidad) {
            $peso = $i;
            $sumaPonderada += $cantidad * $peso;
            $sumaPesos += $peso;
            $i++;
        }

        return round($sumaPonderada / $sumaPesos, 2);
    }

    /**
     * Suavizado exponencial (Simple Exponential Smoothing).
     * alpha entre 0 y 1; mayor alpha = más peso a lo reciente.
     */
    public static function suavizadoExponencial(array $historial, float $alpha = 0.3): float
    {
        if (empty($historial)) return 0;

        $valores = array_values($historial);
        $suavizado = $valores[0];

        for ($i = 1; $i < count($valores); $i++) {
            $suavizado = $alpha * $valores[$i] + (1 - $alpha) * $suavizado;
        }

        return round($suavizado, 2);
    }

    /**
     * Regresión lineal simple.
     * Devuelve [pendiente, intercepto, r2].
     */
    public static function regresionLineal(array $historial): array
    {
        $valores = array_values($historial);
        $n = count($valores);

        if ($n < 2) return [0, 0, 0];

        $sumX = 0; $sumY = 0; $sumXY = 0; $sumXX = 0; $sumYY = 0;

        for ($i = 0; $i < $n; $i++) {
            $x = $i;
            $y = $valores[$i];
            $sumX += $x;
            $sumY += $y;
            $sumXY += $x * $y;
            $sumXX += $x * $x;
            $sumYY += $y * $y;
        }

        $denominador = ($n * $sumXX) - ($sumX * $sumX);
        if ($denominador == 0) return [0, 0, 0];

        $pendiente = (($n * $sumXY) - ($sumX * $sumY)) / $denominador;
        $intercepto = ($sumY - ($pendiente * $sumX)) / $n;

        // Coeficiente de determinación R²
        $mediaY = $sumY / $n;
        $ssTotal = 0; $ssResiduo = 0;
        for ($i = 0; $i < $n; $i++) {
            $predicho = $pendiente * $i + $intercepto;
            $ssTotal += ($valores[$i] - $mediaY) ** 2;
            $ssResiduo += ($valores[$i] - $predicho) ** 2;
        }
        $r2 = $ssTotal > 0 ? round(1 - ($ssResiduo / $ssTotal), 3) : 0;

        return [round($pendiente, 4), round($intercepto, 2), $r2];
    }

    /**
     * Predicción combinada para los próximos N días.
     */
    public static function predecir(Medicamento $med, int $diasFuturo = 30): array
    {
        $historial = self::historialConsumo($med, 90);

        // Promedios
        $prom7 = self::promedioSimple($med, 7);
        $prom30 = self::promedioSimple($med, 30);
        $prom90 = self::promedioSimple($med, 90);

        // Modelos
        $movilPonderado = self::promedioMovilPonderado($historial);
        $suavizado = self::suavizadoExponencial($historial, 0.3);
        [$pendiente, $intercepto, $r2] = self::regresionLineal($historial);

        // Modelo combinado (pesos según desempeño del modelo)
        // - Si R² es alto, damos más peso a la regresión
        // - Si R² es bajo, damos más peso al suavizado y promedio móvil
        $pesoRegresion = max(0.2, min(0.6, $r2));
        $pesoSuavizado = (1 - $pesoRegresion) * 0.6;
        $pesoMovil = (1 - $pesoRegresion) * 0.4;

        $prediccionDiariaBase =
            $pesoRegresion * max(0, $pendiente * 90 + $intercepto) +
            $pesoSuavizado * $suavizado +
            $pesoMovil * $movilPonderado;

        // Factor día de la semana (sábado y domingo bajan)
        $detalle = [];
        $acumulado = 0;

        for ($i = 1; $i <= $diasFuturo; $i++) {
            $fecha = now()->addDays($i);
            $factorDia = in_array($fecha->dayOfWeek, [0, 6]) ? 0.6 : 1.0;

            $cantidad = round($prediccionDiariaBase * $factorDia, 1);
            $acumulado += $cantidad;

            $detalle[] = [
                'fecha' => $fecha->format('Y-m-d'),
                'dia_semana' => $fecha->dayOfWeek,
                'cantidad_predicha' => $cantidad,
                'acumulado' => round($acumulado, 1),
            ];
        }

        return [
            'promedio_7_dias' => $prom7,
            'promedio_30_dias' => $prom30,
            'promedio_90_dias' => $prom90,
            'movil_ponderado' => $movilPonderado,
            'suavizado_exponencial' => $suavizado,
            'regresion' => [
                'pendiente' => $pendiente,
                'intercepto' => $intercepto,
                'r2' => $r2,
                'interpretacion' => self::interpretarRegresion($pendiente, $r2),
            ],
            'prediccion_diaria_base' => round($prediccionDiariaBase, 2),
            'total_predicho' => round($acumulado, 0),
            'detalle' => $detalle,
        ];
    }

    /**
     * Interpretación textual del modelo de regresión.
     */
    private static function interpretarRegresion(float $pendiente, float $r2): string
    {
        $tendencia = match (true) {
            $pendiente > 0.1 => 'creciente',
            $pendiente < -0.1 => 'decreciente',
            default => 'estable',
        };

        $confianza = match (true) {
            $r2 >= 0.7 => 'alta',
            $r2 >= 0.4 => 'media',
            default => 'baja',
        };

        return "Tendencia {$tendencia} con confianza {$confianza} (R² = {$r2})";
    }

    /**
     * Predicción de agotamiento para un medicamento.
     */
    public static function prediccionAgotamiento(Medicamento $med): array
    {
        $historial = self::historialConsumo($med, 30);
        $prediccion = self::predecir($med, 30);

        $stock = $med->stock_total_calculado;
        $consumoDiario = $prediccion['prediccion_diaria_base'];

        if ($consumoDiario <= 0) {
            return [
                'dias_restantes' => null,
                'estado' => 'sin_consumo',
                'cantidad_sugerida' => 0,
                'fecha_agotamiento' => null,
            ];
        }

        $diasRestantes = round($stock / $consumoDiario, 1);
        $fechaAgotamiento = now()->addDays((int) $diasRestantes)->format('Y-m-d');

        $estado = match (true) {
            $diasRestantes <= 7 => 'critico',
            $diasRestantes <= 15 => 'bajo',
            $diasRestantes <= 30 => 'medio',
            default => 'ok',
        };

        return [
            'dias_restantes' => $diasRestantes,
            'estado' => $estado,
            'cantidad_sugerida' => max(0, (int) ceil($consumoDiario * 30 - $stock)),
            'fecha_agotamiento' => $fechaAgotamiento,
        ];
    }

    /**
     * Ranking de demanda por medicamento.
     */
    public static function rankingDemanda(int $dias = 30, int $limite = 10): array
    {
        $desde = now()->subDays($dias);

        return MovimientoInventario::select(
                'medicamento_id',
                DB::raw('SUM(ABS(cantidad)) as total_salidas'),
                DB::raw('COUNT(*) as num_salidas')
            )
            ->where('tipo', 'salida')
            ->where('created_at', '>=', $desde)
            ->groupBy('medicamento_id')
            ->orderByDesc('total_salidas')
            ->limit($limite)
            ->with('medicamento')
            ->get()
            ->map(fn($r) => [
                'medicamento' => $r->medicamento,
                'nombre' => $r->medicamento?->nombre . ' ' . $r->medicamento?->concentracion,
                'total_salidas' => (int) $r->total_salidas,
                'num_salidas' => (int) $r->num_salidas,
                'promedio_diario' => round($r->total_salidas / max($dias, 1), 2),
            ])
            ->toArray();
    }

    /**
     * Tendencia de consumo: compara periodo actual vs anterior.
     */
    public static function calcularTendencia(Medicamento $med, int $dias = 30): array
    {
        $actual = MovimientoInventario::where('medicamento_id', $med->id)
            ->where('tipo', 'salida')
            ->where('created_at', '>=', now()->subDays($dias))
            ->sum(DB::raw('ABS(cantidad)'));

        $anterior = MovimientoInventario::where('medicamento_id', $med->id)
            ->where('tipo', 'salida')
            ->whereBetween('created_at', [
                now()->subDays($dias * 2),
                now()->subDays($dias),
            ])
            ->sum(DB::raw('ABS(cantidad)'));

        $cambio = $anterior > 0
            ? round((($actual - $anterior) / $anterior) * 100, 1)
            : ($actual > 0 ? 100 : 0);

        return [
            'actual' => (int) $actual,
            'anterior' => (int) $anterior,
            'cambio_porcentaje' => $cambio,
            'direccion' => $cambio > 10 ? 'subiendo' : ($cambio < -10 ? 'bajando' : 'estable'),
        ];
    }
}