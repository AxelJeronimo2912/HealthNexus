<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Paciente;
use App\Services\ClusteringService;
use App\Services\PcaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PhenotypingController extends Controller
{
    /** Nombres de los 4 fenotipos */
    private array $nombres = [
        'Respondedor Rápido',
        'Crónico Complejo',
        'Inestable Oculto',
        'Monitoreo Intensivo',
    ];

    private array $colores = [
        '#ef4444', // rojo
        '#10b981', // verde
        '#f59e0b', // ámbar
        '#3b82f6', // azul
    ];

    private array $triageMap = [
        'rojo'     => 5,
        'naranja'  => 4,
        'amarillo' => 3,
        'verde'    => 2,
        'azul'     => 1,
    ];

    public function data(): JsonResponse
    {
        // 1. Traer pacientes con al menos 2 signos vitales en los últimos 90 días
        $pacientes = Paciente::where('activo', true)
            ->whereHas('signosVitales', function ($q) {
                $q->where('created_at', '>=', now()->subDays(90))
                  ->whereNotNull('triage');
            }, '>=', 2)
            ->with(['signosVitales' => function ($q) {
                $q->where('created_at', '>=', now()->subDays(90))
                  ->orderBy('created_at');
            }])
            ->limit(800) // tope para que no se eternice
            ->get();

        if ($pacientes->count() < 4) {
            return response()->json([
                'error' => 'Datos insuficientes para fenotipado (mínimo 4 pacientes con 2+ signos vitales).',
            ], 200);
        }

        // 2. Construir vectores clínicos (8 dimensiones)
        $vectores = [];
        $meta = [];

        foreach ($pacientes as $p) {
            $sv = $p->signosVitales;
            if ($sv->count() < 2) continue;

            $fc   = $sv->pluck('frecuencia_cardiaca')->filter()->values();
            $temp = $sv->pluck('temperatura')->filter()->values();
            $spo2 = $sv->pluck('saturacion_oxigeno')->filter()->values();

            if ($fc->count() < 2 || $temp->count() < 2 || $spo2->count() < 2) continue;

            $severidad = $this->triageMap[$sv->last()->triage] ?? 3;

            $vector = [
                (float) ($p->edad ?? 0),
                (float) $severidad,
                round($fc->avg(), 2),
                round($this->stddev($fc->all()), 2),
                round($temp->avg(), 2),
                round($this->stddev($temp->all()), 2),
                round($spo2->avg(), 2),
                round($this->stddev($spo2->all()), 2),
            ];

            $vectores[] = $vector;
            $meta[] = [
                'paciente_id' => $p->id,
                'nombre'      => $p->nombre_completo,
                'edad'        => $p->edad,
                'triage'      => $sv->last()->triage,
                'fc_prom'     => $vector[2],
                'spo2_prom'   => $vector[6],
                'var_fc'      => $vector[3],
                'var_spo2'    => $vector[7],
            ];
        }

        if (count($vectores) < 4) {
            return response()->json([
                'error' => 'No hay suficientes pacientes con datos completos.',
            ], 200);
        }

        // 3. K-Means (K=4)
        $clustering = new ClusteringService();
        $resultado = $clustering->kmeans($vectores, 4, 100);

        // 4. Codo (K=2..6) y silhouette por K
        $codo = [];
        $silhouettes = [];
        for ($k = 2; $k <= 6; $k++) {
            $m = new ClusteringService();
            $r = $m->kmeans($vectores, $k, 50);
            $codo[$k] = round($r['inercia'], 2);
            $silhouettes[$k] = round($r['silhouette'], 4);
        }

        // 5. Asignar fenotipo a cada paciente
        $pacientesPorCluster = [];
        foreach ($resultado['asignaciones'] as $i => $cluster) {
            $meta[$i]['cluster'] = $cluster;
            $meta[$i]['fenotipo'] = $this->nombres[$cluster] ?? "Cluster {$cluster}";
            $pacientesPorCluster[$cluster][] = $meta[$i];
        }

        // 6. Distribución por cluster
        $distribucion = [];
        foreach ($pacientesPorCluster as $c => $lista) {
            $distribucion[] = [
                'cluster'   => $c,
                'fenotipo'  => $this->nombres[$c] ?? "Cluster {$c}",
                'color'     => $this->colores[$c] ?? '#94a3b8',
                'total'     => count($lista),
                'edad_prom' => round(collect($lista)->avg('edad'), 1),
                'fc_prom'   => round(collect($lista)->avg('fc_prom'), 1),
                'spo2_prom' => round(collect($lista)->avg('spo2_prom'), 1),
            ];
        }

        // 7. PCA (3 componentes)
        $pca = new PcaService();
        $pcaResult = $pca->fit($vectores, 3);

        // Scatter PCA: [PC1, PC2, cluster]
        $scatterPca = [];
        foreach ($pcaResult['proyeccion'] as $i => $punto) {
            $scatterPca[] = [
                'x' => $punto[0],
                'y' => $punto[1],
                'cluster' => $resultado['asignaciones'][$i],
            ];
        }

        // Loadings (primeros 3 componentes x 8 variables)
        $loadings = [];
        $variables = ['Edad', 'Severidad', 'FC prom', 'Var FC', 'Temp prom', 'Var Temp', 'SpO2 prom', 'Var SpO2'];
        for ($c = 0; $c < 3; $c++) {
            $fila = [];
            for ($v = 0; $v < 8; $v++) {
                $fila[] = round($pcaResult['loadings'][$c][$v] ?? 0, 4);
            }
            $loadings[] = [
                'componente' => 'PC' . ($c + 1),
                'varianza'   => $pcaResult['varianza'][$c] ?? 0,
                'valores'    => $fila,
            ];
        }

        return response()->json([
            'kpis' => [
                'silhouette'  => $resultado['silhouette'],
                'inercia'     => round($resultado['inercia'], 2),
                'iteraciones' => $resultado['iteraciones'],
                'total'       => count($vectores),
                'k'           => 4,
            ],
            'codo'         => $codo,
            'silhouettes'  => $silhouettes,
            'distribucion' => $distribucion,
            'pacientes'    => array_slice($meta, 0, 50), // primeros 50 para tabla
            'pca' => [
                'varianza' => $pcaResult['varianza'],
                'loadings' => $loadings,
                'scatter'  => $scatterPca,
            ],
            'variables' => $variables,
        ]);
    }

    private function stddev(array $valores): float
    {
        $n = count($valores);
        if ($n < 2) return 0.0;
        $media = array_sum($valores) / $n;
        $suma = 0.0;
        foreach ($valores as $v) {
            $suma += ($v - $media) ** 2;
        }
        return sqrt($suma / ($n - 1));
    }
}