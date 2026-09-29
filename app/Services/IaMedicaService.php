<?php

namespace App\Services;

use App\Models\SimuladorPredictivoCaso;

class IaMedicaService
{
    /**
     * Ejecuta los 5 modelos con los 4 signos vitales.
     * Los coeficientes están pre-calculados según el pitch (nexus.pptx).
     */
    public function predecir(int $fc, int $spo2, float $temp, int $edad): array
    {
        return [
            'input' => compact('fc', 'spo2', 'temp', 'edad'),
            'logistica' => $this->regresionLogistica($fc, $spo2, $temp, $edad),
            'svm'       => $this->svm($fc, $spo2, $temp, $edad),
            'arbol'     => $this->arbolDecision($fc, $spo2, $temp, $edad),
            'rf'        => $this->randomForest($fc, $spo2, $temp, $edad),
            'lineal'    => $this->regresionLineal($fc, $spo2, $temp, $edad),
            'costo'     => $this->prediccionCosto($fc, $spo2, $temp, $edad),
            'dias'      => $this->prediccionDias($fc, $spo2, $temp, $edad),
        ];
    }

    // ==================== 1. Regresión Logística ====================
    // P(critico) = 1 / (1 + e^(-z))
    // z = 0.6·FC - 0.8·SpO2 + 30 (según la presentación)
    private function regresionLogistica(int $fc, int $spo2, float $temp, int $edad): array
    {
        $z = 0.6 * $fc - 0.8 * $spo2 + 30;
        $p = 1 / (1 + exp(-$z));

        // Normalizar a un rango más clínico usando edad y temp
        $p = min(0.99, max(0.01, $p * 0.5 + ($edad > 60 ? 0.1 : 0) + ($temp > 38 ? 0.15 : 0)));

        return [
            'probabilidad' => round($p, 4),
            'porcentaje'   => round($p * 100, 1),
            'nivel'        => $p > 0.7 ? 'crítico' : ($p > 0.4 ? 'moderado' : 'bajo'),
            'formula'      => 'P = 1 / (1 + e^(-z)) · z = 0.6·FC − 0.8·SpO₂ + 30',
        ];
    }

    // ==================== 2. SVM ====================
    // Hiperplano: 0.6·FC - 0.8·SpO2 + 30
    private function svm(int $fc, int $spo2, float $temp, int $edad): array
    {
        $hiperplano = 0.6 * $fc - 0.8 * $spo2 + 30;
        $riesgo = $hiperplano > 0 ? 'Alto' : 'Bajo';

        return [
            'hiperplano' => round($hiperplano, 2),
            'riesgo'     => $riesgo,
            'margen'     => round(abs($hiperplano), 2),
            'formula'    => '0.6·FC − 0.8·SpO₂ + 30',
        ];
    }

    // ==================== 3. Árbol de Decisión ====================
    // Reglas directas según la presentación
    private function arbolDecision(int $fc, int $spo2, float $temp, int $edad): array
    {
        if ($spo2 < 90) {
            return ['recomendacion' => 'UCI Inmediata', 'color' => 'red', 'razon' => 'SpO₂ < 90%'];
        }
        if ($temp > 39 || $fc > 130) {
            return ['recomendacion' => 'UCI Inmediata', 'color' => 'red', 'razon' => 'Fiebre alta o taquicardia severa'];
        }
        if ($temp > 38 || $fc > 110) {
            return ['recomendacion' => 'Observación', 'color' => 'orange', 'razon' => 'Fiebre o taquicardia moderada'];
        }
        if ($temp > 37.5) {
            return ['recomendacion' => 'Control Febril', 'color' => 'yellow', 'razon' => 'Febrícula'];
        }
        return ['recomendacion' => 'Estable', 'color' => 'green', 'razon' => 'Signos dentro de rango'];
    }

    // ==================== 4. Random Forest ====================
    // Simula 3 árboles que votan; mayoría gana
    private function randomForest(int $fc, int $spo2, float $temp, int $edad): array
    {
        $votos = [];

        // Árbol 1: SpO2
        $votos[] = $spo2 < 92 ? 'Crítico' : 'No crítico';

        // Árbol 2: Edad + Temp
        $votos[] = ($edad > 70 && $temp > 37.5) ? 'Crítico' : 'No crítico';

        // Árbol 3: FC
        $votos[] = $fc > 120 ? 'Crítico' : 'No crítico';

        $criticos = count(array_filter($votos, fn($v) => $v === 'Crítico'));
        $votoFinal = $criticos >= 2 ? 'Crítico' : 'No crítico';

        return [
            'votos'      => $votos,
            'criticos'   => $criticos,
            'voto_final' => $votoFinal,
            'confianza'  => round(($criticos >= 2 ? $criticos : 3 - $criticos) / 3 * 100, 1),
        ];
    }

    // ==================== 5. Regresión Lineal ====================
    // SpO2 esperado = b0 + b1·edad + b2·temp
    private function regresionLineal(int $fc, int $spo2, float $temp, int $edad): array
    {
        // Coeficientes ilustrativos
        $spo2Esperado = 99 - 0.05 * $edad - 0.8 * max(0, $temp - 37);

        $diferencia = $spo2 - $spo2Esperado;

        return [
            'spo2_esperado' => round($spo2Esperado, 2),
            'spo2_actual'   => $spo2,
            'diferencia'    => round($diferencia, 2),
            'estado'        => $diferencia < -3 ? 'Por debajo' : ($diferencia > 3 ? 'Por encima' : 'Normal'),
            'formula'       => 'SpO₂ = 99 − 0.05·Edad − 0.8·(Temp − 37)',
        ];
    }

    // ==================== 6. Predicción de costos ====================
    // Regresión lineal múltiple entrenada (coeficientes de presentación)
    private function prediccionCosto(int $fc, int $spo2, float $temp, int $edad): array
    {
        // Modelo ilustrativo: costo = base + pesos por gravedad
        $base = 8000;
        $costo = $base
            + ($fc > 100 ? 4000 : 0)
            + ($spo2 < 92 ? 6000 : 0)
            + ($temp > 38 ? 2500 : 0)
            + ($edad > 60 ? 3500 : 0);

        return [
            'costo_estimado' => round($costo, 2),
            'moneda'         => 'MXN',
        ];
    }

    // ==================== 7. Predicción de días de estancia ====================
    private function prediccionDias(int $fc, int $spo2, float $temp, int $edad): array
    {
        $dias = 2;
        if ($fc > 100)   $dias += 1;
        if ($spo2 < 92)  $dias += 2;
        if ($temp > 38)  $dias += 1;
        if ($edad > 60)  $dias += 1;

        return [
            'dias_estimados' => $dias,
            'rango'          => $dias . ' – ' . ($dias + 2) . ' días',
        ];
    }

    // ==================== MÉTRICAS DEL MODELO ====================
    // Si hay casos cerrados, calcula en vivo; si no, usa los valores de la presentación
    public function metricasModelo(): array
    {
        $casos = SimuladorPredictivoCaso::where('cerrado', true)
            ->whereNotNull('diagnostico_final')
            ->get();

        if ($casos->count() >= 5) {
            return $this->metricasEnVivo($casos);
        }

        // Fallback: valores del pitch
        return [
            'casos_cerrados' => 16,
            'accuracy'       => 75.0,
            'recall'         => 75.0,
            'precision'      => 50.0,
            'f1'             => 60.0,
            'matriz' => [
                'vn' => 9, 'fp' => 3,
                'fn' => 1, 'vp' => 3,
            ],
            'fuente' => 'demo',
        ];
    }

    private function metricasEnVivo($casos): array
    {
        $vn = $fp = $fn = $vp = 0;

        foreach ($casos as $c) {
            $pred = $c->voto_rf === 'Crítico';
            $real = $c->diagnostico_final === 'fallecio';

            if ($pred && $real)       $vp++;
            elseif ($pred && !$real)  $fp++;
            elseif (!$pred && $real)  $fn++;
            else                      $vn++;
        }

        $total = $vp + $vn + $fp + $fn;
        $accuracy  = $total > 0 ? round(($vp + $vn) / $total * 100, 1) : 0;
        $precision = ($vp + $fp) > 0 ? round($vp / ($vp + $fp) * 100, 1) : 0;
        $recall    = ($vp + $fn) > 0 ? round($vp / ($vp + $fn) * 100, 1) : 0;
        $f1        = ($precision + $recall) > 0 ? round(2 * $precision * $recall / ($precision + $recall), 1) : 0;

        return [
            'casos_cerrados' => $total,
            'accuracy'       => $accuracy,
            'recall'         => $recall,
            'precision'      => $precision,
            'f1'             => $f1,
            'matriz'         => compact('vn', 'fp', 'fn', 'vp'),
            'fuente'         => 'vivo',
        ];
    }

    /**
     * Feature importance del Random Forest (del pitch)
     */
    public function featureImportance(): array
    {
        return [
            ['variable' => 'SpO₂',    'peso' => 33.6],
            ['variable' => 'Edad',    'peso' => 21.5],
            ['variable' => 'Temp',    'peso' => 14.3],
            ['variable' => 'FC',      'peso' => 11.2],
            ['variable' => 'Diabetes','peso' => 9.0],
            ['variable' => 'TA',      'peso' => 8.0],
            ['variable' => 'HTA',     'peso' => 4.5],
        ];
    }

    /**
     * Accuracy por modelo (del pitch)
     */
    public function accuracyPorModelo(): array
    {
        return [
            ['modelo' => 'Reg. Logística', 'accuracy' => 85, 'color' => '#dc2626'],
            ['modelo' => 'SVM',            'accuracy' => 30, 'color' => '#ea580c'],
            ['modelo' => 'Árbol Decisión', 'accuracy' => 73, 'color' => '#eab308'],
            ['modelo' => 'Random Forest',  'accuracy' => 65, 'color' => '#16a34a'],
        ];
    }

    /**
     * Guarda un caso del simulador.
     */
    public function guardarCaso(array $input, array $predicciones, ?int $userId = null): SimuladorPredictivoCaso
    {
        return SimuladorPredictivoCaso::create([
            'user_id'             => $userId,
            'fc'                  => $input['fc'],
            'spo2'                => $input['spo2'],
            'temp'                => $input['temp'],
            'edad'                => $input['edad'],
            'prob_critico'        => $predicciones['logistica']['probabilidad'],
            'riesgo_svm'          => $predicciones['svm']['riesgo'],
            'recomendacion_arbol' => $predicciones['arbol']['recomendacion'],
            'voto_rf'             => $predicciones['rf']['voto_final'],
            'spo2_esperado'       => $predicciones['lineal']['spo2_esperado'],
            'costo_predicho'      => $predicciones['costo']['costo_estimado'],
            'dias_predicho'       => $predicciones['dias']['dias_estimados'],
        ]);
    }
}