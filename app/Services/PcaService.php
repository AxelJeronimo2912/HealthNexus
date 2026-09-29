<?php

namespace App\Services;

class PcaService
{
    public array $medias = [];
    public array $desviaciones = [];
    public array $eigenvectors = [];
    public array $eigenvalues = [];
    public array $varianzaExplicada = [];

    /**
     * Ajusta PCA sobre una matriz de vectores.
     */
    public function fit(array $vectores, int $nComponentes = 3): array
    {
        $n = count($vectores);
        if ($n === 0) {
            throw new \InvalidArgumentException('No hay vectores para PCA.');
        }
        $dim = count($vectores[0]);

        // 1. Estandarizar
        $Z = $this->standardize($vectores);

        // 2. Matriz de covarianza
        $cov = $this->covarianceMatrix($Z);

        // 3. Eigen descomposición por QR iterativo
        [$eigenvalues, $eigenvectors] = $this->eigenDecomposition($cov);

        // 4. Ordenar por eigenvalor descendente
        $orden = array_keys($eigenvalues);
        usort($orden, fn($a, $b) => $eigenvalues[$b] <=> $eigenvalues[$a]);

        $eigenvaluesOrdenados = [];
        $eigenvectorsOrdenados = [];
        foreach ($orden as $i) {
            $eigenvaluesOrdenados[] = $eigenvalues[$i];
            $eigenvectorsOrdenados[] = $eigenvectors[$i];
        }

        $total = array_sum($eigenvaluesOrdenados);
        $this->varianzaExplicada = array_map(
            fn($v) => $total > 0 ? round($v / $total * 100, 2) : 0.0,
            $eigenvaluesOrdenados
        );

        $this->eigenvalues = $eigenvaluesOrdenados;
        $this->eigenvectors = $eigenvectorsOrdenados;

        // 5. Proyección
        $componentes = array_slice($eigenvectorsOrdenados, 0, $nComponentes);
        $proyeccion = $this->project($Z, $componentes);

        return [
            'proyeccion' => $proyeccion,
            'varianza'   => array_slice($this->varianzaExplicada, 0, $nComponentes),
            'loadings'   => $componentes,
        ];
    }

    public function standardize(array $vectores): array
    {
        $n = count($vectores);
        $dim = count($vectores[0]);
        $this->medias = array_fill(0, $dim, 0.0);
        $this->desviaciones = array_fill(0, $dim, 0.0);

        foreach ($vectores as $v) {
            for ($d = 0; $d < $dim; $d++) {
                $this->medias[$d] += $v[$d];
            }
        }
        for ($d = 0; $d < $dim; $d++) {
            $this->medias[$d] /= $n;
        }

        foreach ($vectores as $v) {
            for ($d = 0; $d < $dim; $d++) {
                $this->desviaciones[$d] += ($v[$d] - $this->medias[$d]) ** 2;
            }
        }
        for ($d = 0; $d < $dim; $d++) {
            $this->desviaciones[$d] = sqrt($this->desviaciones[$d] / $n) ?: 1.0;
        }

        $Z = [];
        foreach ($vectores as $v) {
            $fila = [];
            for ($d = 0; $d < $dim; $d++) {
                $fila[] = ($v[$d] - $this->medias[$d]) / $this->desviaciones[$d];
            }
            $Z[] = $fila;
        }
        return $Z;
    }

    public function covarianceMatrix(array $Z): array
    {
        $n = count($Z);
        $dim = count($Z[0]);
        $cov = array_fill(0, $dim, array_fill(0, $dim, 0.0));

        foreach ($Z as $fila) {
            for ($i = 0; $i < $dim; $i++) {
                for ($j = $i; $j < $dim; $j++) {
                    $cov[$i][$j] += $fila[$i] * $fila[$j];
                }
            }
        }
        $denom = max($n - 1, 1);
        for ($i = 0; $i < $dim; $i++) {
            for ($j = $i; $j < $dim; $j++) {
                $cov[$i][$j] /= $denom;
                $cov[$j][$i] = $cov[$i][$j];
            }
        }
        return $cov;
    }

    /**
     * Descomposición QR iterativa para eigenvalores/vectores.
     * Funciona bien para matrices simétricas pequeñas (8x8).
     */
    public function eigenDecomposition(array $A, int $maxIter = 200): array
    {
        $n = count($A);
        $Ak = $A;
        $Qtotal = $this->identidad($n);

        for ($iter = 0; $iter < $maxIter; $iter++) {
            [$Q, $R] = $this->qrDecompose($Ak);
            $Ak = $this->multiplicar($R, $Q);
            $Qtotal = $this->multiplicar($Qtotal, $Q);
        }

        $eigenvalues = [];
        for ($i = 0; $i < $n; $i++) {
            $eigenvalues[] = $Ak[$i][$i];
        }

        // Eigenvectors = columnas de Qtotal
        $eigenvectors = [];
        for ($j = 0; $j < $n; $j++) {
            $vec = [];
            for ($i = 0; $i < $n; $i++) {
                $vec[] = $Qtotal[$i][$j];
            }
            $eigenvectors[] = $vec;
        }

        return [$eigenvalues, $eigenvectors];
    }

    public function qrDecompose(array $A): array
    {
        $n = count($A);
        $m = count($A[0]);
        $Q = array_fill(0, $n, array_fill(0, $m, 0.0));
        $R = array_fill(0, $m, array_fill(0, $m, 0.0));

        for ($j = 0; $j < $m; $j++) {
            $v = array_column($A, $j);
            for ($i = 0; $i < $j; $i++) {
                $q = array_column($Q, $i);
                $r = 0.0;
                for ($k = 0; $k < $n; $k++) {
                    $r += $q[$k] * $v[$k];
                }
                $R[$i][$j] = $r;
                for ($k = 0; $k < $n; $k++) {
                    $v[$k] -= $r * $q[$k];
                }
            }
            $norma = 0.0;
            foreach ($v as $x) $norma += $x * $x;
            $norma = sqrt($norma) ?: 1.0;
            for ($k = 0; $k < $n; $k++) {
                $Q[$k][$j] = $v[$k] / $norma;
            }
            $R[$j][$j] = $norma;
        }

        return [$Q, $R];
    }

    public function project(array $Z, array $componentes): array
    {
        $proyeccion = [];
        foreach ($Z as $fila) {
            $nuevo = [];
            foreach ($componentes as $comp) {
                $suma = 0.0;
                foreach ($fila as $i => $v) {
                    $suma += $v * $comp[$i];
                }
                $nuevo[] = round($suma, 4);
            }
            $proyeccion[] = $nuevo;
        }
        return $proyeccion;
    }

    public function calcularLoadings(): array
    {
        return $this->eigenvectors;
    }

    private function identidad(int $n): array
    {
        $I = [];
        for ($i = 0; $i < $n; $i++) {
            $I[$i] = array_fill(0, $n, 0.0);
            $I[$i][$i] = 1.0;
        }
        return $I;
    }

    private function multiplicar(array $A, array $B): array
    {
        $n = count($A);
        $m = count($B[0]);
        $p = count($B);
        $C = array_fill(0, $n, array_fill(0, $m, 0.0));
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $m; $j++) {
                $s = 0.0;
                for ($k = 0; $k < $p; $k++) {
                    $s += $A[$i][$k] * $B[$k][$j];
                }
                $C[$i][$j] = $s;
            }
        }
        return $C;
    }
}