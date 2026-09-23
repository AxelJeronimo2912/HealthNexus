<?php

namespace App\Http\Controllers;

use App\Models\Medicamento;
use App\Services\PrediccionService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PrediccionController extends Controller
{
  public function index(Request $request): View
{
    $dias = (int) $request->input('dias', 30);

    // Top demanda
    $topDemanda = PrediccionService::rankingDemanda($dias, 10);

    // Todos los medicamentos activos
    $medicamentos = Medicamento::where('activo', true)
        ->with('lotes')
        ->get();

    // Predicción de agotamiento
    $agotamiento = $medicamentos->map(function ($med) {
        $pred = PrediccionService::prediccionAgotamiento($med);

        // 👇 Calcular el consumo diario (promedio ponderado)
        $consumoDiario = PrediccionService::predecir($med, 30)['prediccion_diaria_base'];

        return [
            'medicamento' => $med,
            'nombre' => $med->nombre . ' ' . $med->concentracion,
            'stock_actual' => $med->stock_total_calculado,
            'stock_minimo' => $med->stock_minimo,
            'stock_maximo' => $med->stock_maximo,
            'promedio_diario' => round($consumoDiario, 2),   // 👈 ESTA FALTABA
            'dias_restantes' => $pred['dias_restantes'],
            'estado' => $pred['estado'],
            'cantidad_sugerida' => $pred['cantidad_sugerida'],
            'fecha_agotamiento' => $pred['fecha_agotamiento'],
        ];
    })->toArray();

    // Ordenar por criticidad
    usort($agotamiento, function ($a, $b) {
        $orden = ['critico' => 1, 'bajo' => 2, 'medio' => 3, 'ok' => 4, 'sin_consumo' => 5];
        return ($orden[$a['estado']] ?? 99) <=> ($orden[$b['estado']] ?? 99);
    });

    // Tendencias
    $tendencias = [];
    foreach ($medicamentos as $med) {
        $t = PrediccionService::calcularTendencia($med, $dias);
        if ($t['actual'] > 0 || $t['anterior'] > 0) {
            $tendencias[] = [
                'medicamento' => $med,
                'nombre' => $med->nombre . ' ' . $med->concentracion,
                'consumo_actual' => $t['actual'],
                'consumo_anterior' => $t['anterior'],
                'cambio_porcentaje' => $t['cambio_porcentaje'],
                'direccion' => $t['direccion'],
            ];
        }
    }
    usort($tendencias, fn($a, $b) => abs($b['cambio_porcentaje']) <=> abs($a['cambio_porcentaje']));
    $tendencias = array_slice($tendencias, 0, 10);

    // Alertas críticas
    $alertas = collect($agotamiento)
        ->where('estado', 'critico')
        ->take(5)
        ->map(function ($item) {
            return [
                'titulo' => 'Stock crítico',
                'mensaje' => "{$item['nombre']} se agotará en {$item['dias_restantes']} días. Sugerido pedir: {$item['cantidad_sugerida']} unidades.",
                'medicamento_id' => $item['medicamento']->id,
            ];
        })
        ->values()
        ->toArray();

    // Stats
    $stats = [
        'total_medicamentos' => $medicamentos->count(),
        'medicamentos_criticos' => collect($agotamiento)->where('estado', 'critico')->count(),
        'medicamentos_bajos' => collect($agotamiento)->where('estado', 'bajo')->count(),
        'medicamentos_ok' => collect($agotamiento)->where('estado', 'ok')->count(),
    ];

    return view('prediccion.index', compact(
        'topDemanda', 'agotamiento', 'tendencias', 'alertas', 'stats', 'dias'
    ));
}

    public function show(Medicamento $medicamento): View
    {
        $prediccion = PrediccionService::predecir($medicamento, 30);
        $historial = PrediccionService::historialConsumo($medicamento, 90);

        $stockActual = $medicamento->stock_total_calculado;
        $consumoPromedio = $prediccion['prediccion_diaria_base'];
        $consumoTotal = array_sum($historial);

        $diasRestantes = $consumoPromedio > 0
            ? round($stockActual / $consumoPromedio, 1)
            : null;

        // Preparar datos para Chart.js
        $chartHistorial = [
            'labels' => array_keys($historial),
            'data' => array_values($historial),
        ];

        $chartPrediccion = [
            'labels' => array_column($prediccion['detalle'], 'fecha'),
            'data' => array_column($prediccion['detalle'], 'cantidad_predicha'),
            'acumulado' => array_column($prediccion['detalle'], 'acumulado'),
        ];

        return view('prediccion.show', compact(
            'medicamento', 'historial', 'prediccion',
            'stockActual', 'consumoPromedio', 'consumoTotal', 'diasRestantes',
            'chartHistorial', 'chartPrediccion'
        ));
    }
}