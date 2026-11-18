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

        $suavizados = [$valores[0]];

        for ($i = 1; $i < $n; $i++) {
            $suavizados[] = ($alpha * $valores[$i]) + ((1 - $alpha) * end($suavizados));
        }

        $ultimoSuavizado = end($suavizados);
        $predicciones = array_fill(0, $periodosPredecir, round($ultimoSuavizado, 2));

        return $predicciones;
    }


        /**
     * Calcula la precisión del modelo usando Backtesting (MAPE)
     */
    public function calcularPrecision(array $valores): array
    {
        $n = count($valores);
        
        if ($n < 3) {
            return [
                'mape' => null,
                'precision' => null,
                'mensaje' => 'Datos insuficientes para calcular precisión (se requieren al menos 3 meses).'
            ];
        }

        $entrenamiento = array_slice($valores, 0, $n - 1);
        $valorRealUltimoMes = end($valores);

        $prediccion = $this->regresionLineal($entrenamiento, 1);
        $valorPredichoUltimoMes = $prediccion[0] ?? 0;

        $errorAbsoluto = 0;
        if ($valorRealUltimoMes > 0) {
            $errorAbsoluto = abs(($valorRealUltimoMes - $valorPredichoUltimoMes) / $valorRealUltimoMes) * 100;
        }

        $precision = max(0, 100 - $errorAbsoluto);

        return [
            'mape' => round($errorAbsoluto, 2), // Margen de error
            'precision' => round($precision, 2), // Precisión del modelo (0-100%)
            'mensaje' => 'Calculado comparando la predicción del último mes con el valor real.'
        ];
    }
}