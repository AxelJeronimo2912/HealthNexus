<?php

namespace App\Services;

class ClusteringService
{
    public array $centroides = [];
    public array $asignaciones = [];
    public int $iteraciones = 0;
    public float $inercia = 0.0;
    public float $silhouette = 0.0;
    public array $historialInercia = [];

    /**
     * Ejecuta K-Means++ sobre una matriz de vectores.
     *
     * @param  array  $vectores  Lista de arrays numéricos.
     * @param  int    $k         Número de clusters.
     * @param  int    $maxIter   Máximo de iteraciones.
     * @return array
     */
    public function kmeans(array $vectores, int $k = 4, int $maxIter = 100): array
    {
        $n = count($vectores);
        if ($n < $k) {
            throw new \InvalidArgumentException("Se necesitan al menos {$k} vectores.");
        }

        $dim = count($vectores[0]);

        // 1. Inicialización K-Means++
        $this->centroides = $this->kmeansPlusPlus($vectores, $k);

        $this->asignaciones = array_fill(0, $n, 0);
        $this->iteraciones = 0;
        $this->historialInercia = [];

        for ($iter = 0; $iter < $maxIter; $iter++) {
            $cambios = 0;

            // 2. Asignación
            foreach ($vectores as $i => $v) {
                $mejor = 0;
                $mejorDist = PHP_FLOAT_MAX;
                foreach ($this->centroides as $c => $centro) {
                    $d = $this->distanciaEuclidiana($v, $centro);
                    if ($d < $mejorDist) {
                        $mejorDist = $d;
                        $mejor = $c;
                    }
                }
                if ($this->asignaciones[$i] !== $mejor) {
                    $this->asignaciones[$i] = $mejor;
                    $cambios++;
                }
            }

            // 3. Recalcular centroides
            $sumas = array_fill(0, $k, array_fill(0, $dim, 0.0));
            $conteos = array_fill(0, $k, 0);
            foreach ($vectores as $i => $v) {
                $c = $this->asignaciones[$i];
                $conteos[$c]++;
                for ($d = 0; $d < $dim; $d++) {
                    $sumas[$c][$d] += $v[$d];
                }
            }
            for ($c = 0; $c < $k; $c++) {
                if ($conteos[$c] === 0) continue;
                for ($d = 0; $d < $dim; $d++) {
                    $this->centroides[$c][$d] = $sumas[$c][$d] / $conteos[$c];
                }
            }

            $this->iteraciones = $iter + 1;
            $this->historialInercia[] = $this->calcularInercia($vectores, $this->asignaciones);

            // 4. Convergencia
            if ($cambios === 0) break;
        }

        $this->inercia = $this->calcularInercia($vectores, $this->asignaciones);
        $this->silhouette = $this->silhouetteScore($vectores, $this->asignaciones);

        return [
            'centroides'   => $this->centroides,
            'asignaciones' => $this->asignaciones,
            'iteraciones'  => $this->iteraciones,
            'inercia'      => $this->inercia,
            'silhouette'   => $this->silhouette,
        ];
    }

    /**
     * K-Means++ : elige centroides iniciales dispersos.
     */
    public function kmeansPlusPlus(array $vectores, int $k): array
    {
        $n = count($vectores);
        $centroides = [];

        // Primer centroide: aleatorio
        $centroides[] = $vectores[random_int(0, $n - 1)];

        // Siguientes: probabilidad proporcional a d²
        while (count($centroides) < $k) {
            $distancias = [];
            $suma = 0.0;
            foreach ($vectores as $v) {
                $min = PHP_FLOAT_MAX;
                foreach ($centroides as $c) {
                    $d = $this->distanciaEuclidiana($v, $c);
                    if ($d < $min) $min = $d;
                }
                $d2 = $min * $min;
                $distancias[] = $d2;
                $suma += $d2;
            }
            if ($suma <= 0) {
                $centroides[] = $vectores[random_int(0, $n - 1)];
                continue;
            }
            $r = (random_int(0, PHP_INT_MAX) / PHP_INT_MAX) * $suma;
            $acum = 0.0;
            $elegido = $vectores[0];
            foreach ($distancias as $i => $d2) {
                $acum += $d2;
                if ($acum >= $r) {
                    $elegido = $vectores[$i];
                    break;
                }
            }
            $centroides[] = $elegido;
        }

        return $centroides;
    }

    public function distanciaEuclidiana(array $a, array $b): float
    {
        $suma = 0.0;
        foreach ($a as $i => $v) {
            $suma += ($v - $b[$i]) ** 2;
        }
        return sqrt($suma);
    }

    public function calcularInercia(array $vectores, array $asignaciones): float
    {
        $sse = 0.0;
        foreach ($vectores as $i => $v) {
            $c = $asignaciones[$i];
            $sse += ($this->distanciaEuclidiana($v, $this->centroides[$c])) ** 2;
        }
        return $sse;
    }

    /**
     * Silhouette Score: -1 (mal) ... +1 (excelente).
     * Limitado a los primeros N puntos para no reventar en datasets grandes.
     */
    public function silhouetteScore(array $vectores, array $asignaciones, int $maxPuntos = 500): float
    {
        $n = count($vectores);
        $indices = range(0, $n - 1);
        if ($n > $maxPuntos) {
            shuffle($indices);
            $indices = array_slice($indices, 0, $maxPuntos);
        }

        $total = 0.0;
        $count = 0;

        foreach ($indices as $i) {
            $propio = [];
            $otros = [];
            foreach ($vectores as $j => $v) {
                if ($i === $j) continue;
                $d = $this->distanciaEuclidiana($vectores[$i], $v);
                if ($asignaciones[$j] === $asignaciones[$i]) {
                    $propio[] = $d;
                } else {
                    $otros[$asignaciones[$j]][] = $d;
                }
            }

            $a = $propio ? array_sum($propio) / count($propio) : 0.0;

            $b = PHP_FLOAT_MAX;
            foreach ($otros as $dists) {
                $prom = array_sum($dists) / count($dists);
                if ($prom < $b) $b = $prom;
            }
            if ($b === PHP_FLOAT_MAX) $b = 0.0;

            $denom = max($a, $b);
            if ($denom > 0) {
                $total += ($b - $a) / $denom;
                $count++;
            }
        }

        return $count > 0 ? round($total / $count, 4) : 0.0;
    }

    /**
     * Método del codo: inercia para distintos K.
     */
    public function codo(array $vectores, int $kMax = 8): array
    {
        $resultado = [];
        for ($k = 2; $k <= $kMax; $k++) {
            $modelo = new self();
            $r = $modelo->kmeans($vectores, $k, 50);
            $resultado[$k] = round($r['inercia'], 2);
        }
        return $resultado;
    }
}