<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SeriesHistoricasRequest;
use App\Services\PrediccionCostosService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SeriesHistoricasController extends Controller
{
    public function index(SeriesHistoricasRequest $request, PrediccionCostosService $predictor)
    {
        $indicador = $request->input('indicador');
        
        $fechaInicio = $request->filled('fecha_inicio') 
            ? Carbon::parse($request->input('fecha_inicio'))->startOfMonth() 
            : now()->subMonths(12)->startOfMonth();
            
        $fechaFin = $request->filled('fecha_fin') 
            ? Carbon::parse($request->input('fecha_fin'))->endOfMonth() 
            : now()->endOfMonth();

        try {
            if ($indicador === 'insumos') {
                $datos = DB::table('movimientos_inventario')
                    ->join('medicamentos', 'medicamentos.id', '=', 'movimientos_inventario.medicamento_id')
                    ->select(
                        DB::raw('SUM(movimientos_inventario.cantidad * medicamentos.precio_compra) as valor'), 
                        DB::raw("DATE_FORMAT(movimientos_inventario.created_at, '%Y-%m') as periodo")
                    )
                    ->whereBetween('movimientos_inventario.created_at', [$fechaInicio, $fechaFin])
                    ->where('movimientos_inventario.tipo', 'entrada')
                    ->groupBy('periodo')
                    ->orderBy('periodo', 'asc')
                    ->get();
            } else {
                $query = DB::table('cuenta_items')
                    ->join('cuentas', 'cuentas.id', '=', 'cuenta_items.cuenta_id')
                    ->select(
                        DB::raw('SUM(cuenta_items.importe) as valor'), 
                        DB::raw("DATE_FORMAT(cuentas.created_at, '%Y-%m') as periodo")
                    )
                    ->whereBetween('cuentas.created_at', [$fechaInicio, $fechaFin]);

                if ($request->filled('servicio_id')) {
                    $query->where('cuenta_items.servicio_id', $request->input('servicio_id'));
                }

                $datos = $query->groupBy('periodo')->orderBy('periodo', 'asc')->get();
            }

            // Formatear los datos para la gráfica
            $series = $datos->map(function ($item) {
                return [
                    'periodo' => $item->periodo,
                    'valor' => (float) $item->valor
                ];
            });

            $valoresParaMetricas = $series->pluck('valor')->toArray();

            $metricasBasicas = $this->calcularMetricas($series);
            $metricasPrecision = $predictor->calcularPrecision($valoresParaMetricas);

            return response()->json([
                'success' => true,
                'meta' => [
                    'indicador' => $indicador,
                    'periodo_inicio' => $fechaInicio->format('Y-m-d'),
                    'periodo_fin' => $fechaFin->format('Y-m-d'),
                    'total_registros' => $series->count(),
                    'modelo_confiabilidad' => array_merge($metricasBasicas, $metricasPrecision) 
                ],
                'data' => $series
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las series históricas.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calcula métricas básicas de los datos históricos
     */
    private function calcularMetricas($series)
    {
        if ($series->isEmpty()) {
            return [
                'promedio' => 0,
                'desviacion_estandar' => 0,
                'tendencia' => 'Sin datos'
            ];
        }

        $valores = $series->pluck('valor')->toArray();
        $promedio = array_sum($valores) / count($valores);
        
        $sumaCuadrados = 0;
        foreach ($valores as $valor) {
            $sumaCuadrados += pow($valor - $promedio, 2);
        }
        $desviacion = sqrt($sumaCuadrados / count($valores));

        $tendencia = 'Estable';
        if (count($valores) > 1) {
            $diferencia = end($valores) - $valores[0];
            if ($diferencia > $promedio * 0.1) $tendencia = 'Alza';
            if ($diferencia < -$promedio * 0.1) $tendencia = 'Baja';
        }

        return [
            'promedio' => round($promedio, 2),
            'desviacion_estandar' => round($desviacion, 2),
            'tendencia' => $tendencia
        ];
    }
}