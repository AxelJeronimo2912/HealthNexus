<?php

namespace App\Services;

class PrediccionCostosService
{
    /**
     * Regresión Lineal Simple (y = mx + b)
     * Útil para ver la tendencia general a largo plazo.
     */
    public function regresionLineal(array $valores, int $periodosPredecir = 3): array
    {
        $n = count($valores);
        if ($n < 2) return [];

        $sumX = 0;
        $sumY = 0;
        $sumXY = 0;
        $sumX2 = 0;

        // X representa el mes/periodo (1, 2, 3...), Y representa el costo
        foreach ($valores as $i => $y) {
            $x = $i + 1;
            $sumX += $x;
            $sumY += $y;
            $sumXY += ($x * $y);
            $sumX2 += ($x * $x);
        }

        // Fórmulas de la recta
        $m = (($n * $sumXY) - ($sumX * $sumY)) / (($n * $sumX2) - ($sumX * $sumX));
        $b = ($sumY - ($m * $sumX)) / $n;

        $predicciones = [];
        for ($i = 1; $i <= $periodosPredecir; $i++) {
            $x_futuro = $n + $i;
            $y_predicho = ($m * $x_futuro) + $b;
            // Evitar costos negativos
            $predicciones[] = max(0, round($y_predicho, 2)); 
        }

        return $predicciones;
    }

    /**
     * Suavizado Exponencial Simple
     * Útil para predecir el siguiente periodo basado en la tendencia reciente.
     */
    public function suavizadoExponencial(array $valores, float $alpha = 0.5, int $periodosPredecir = 3): array
    {
        $n = count($valores);
        if ($n === 0) return [];

        // El primer valor suavizado es igual al primer valor real
        $suavizados = [$valores[0]];

        for ($i = 1; $i < $n; $i++) {
            // Fórmula: S_t = (alpha * Y_t) + ((1 - alpha) * S_t-1)
            $suavizados[] = ($alpha * $valores[$i]) + ((1 - $alpha) * end($suavizados));
        }

        // Para predecir el futuro, asumimos que el último suavizado se mantiene
        $ultimoSuavizado = end($suavizados);
        $predicciones = array_fill(0, $periodosPredecir, round($ultimoSuavizado, 2));

        return $predicciones;
    }
}