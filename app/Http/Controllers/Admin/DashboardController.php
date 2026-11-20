<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alerta;
use App\Models\AuditLog;
use App\Models\Cama;
use App\Models\Cita;
use App\Models\Consulta;
use App\Models\Medicamento;
use App\Models\MovimientoInventario;
use App\Models\Paciente;
use App\Services\DwhService;
use App\Services\PrediccionCostosService;
use App\Services\SlaService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request, PrediccionCostosService $predictor): View
    {
        $alertasActivas = Alerta::whereIn('estado', ['activa', 'vista'])->count();

        $alertasCriticas = Alerta::where('nivel', 'critico')
            ->whereIn('estado', ['activa', 'vista'])
            ->count();

        $medicamentosConStock = Medicamento::query()
            ->where('activo', true)
            ->withSum([
                'lotes as stock_lotes_vigentes' => function ($query) {
                    $query->where('cantidad_disponible', '>', 0)
                        ->where('fecha_caducidad', '>=', now())
                        ->where('activo', true);
                },
            ], 'cantidad_disponible')
            ->get();

        $medicamentosBajos = $medicamentosConStock
            ->filter(fn($m) => (int) ($m->stock_lotes_vigentes ?? 0) <= $m->stock_minimo)
            ->count();

        $camasDisponibles = Cama::where('estado', 'disponible')
            ->where('activo', true)
            ->count();

        $camasTotales = Cama::where('activo', true)->count();

        $auditoriaCritica = AuditLog::where('severidad', 'critical')
            ->whereDate('created_at', today())
            ->count();

        $resumen = [
            'pacientes_activos' => Paciente::where('activo', true)->count(),
            'citas_hoy' => Cita::whereDate('fecha_hora', today())->count(),
            'consultas_hoy' => Consulta::whereDate('created_at', today())->count(),
            'camas_ocupadas' => Cama::where('activo', true)
                ->where('estado', 'ocupada')
                ->count(),
        ];

        $ultimasAlertas = Alerta::whereIn('estado', ['activa', 'vista'])
            ->orderByRaw("FIELD(nivel, 'critico', 'advertencia', 'info')")
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $ultimaAuditoria = AuditLog::with('user')
            ->whereIn('severidad', ['critical', 'warning'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $medicamentosCriticos = $medicamentosConStock
            ->filter(fn($m) => (int) ($m->stock_lotes_vigentes ?? 0) <= $m->stock_minimo)
            ->sortBy(fn($m) => (int) ($m->stock_lotes_vigentes ?? 0))
            ->take(10)
            ->values();

        $medicamentosCriticos->load('lotes');

        $camasPorArea = Cama::select('area')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN estado = 'disponible' THEN 1 ELSE 0 END) as disponibles")
            ->selectRaw("SUM(CASE WHEN estado = 'ocupada' THEN 1 ELSE 0 END) as ocupadas")
            ->where('activo', true)
            ->groupBy('area')
            ->get();

        // ============ FILTRO POR PERIODO, INDICADORES Y GRÁFICAS ============
        $periodo = $request->get('periodo', '30d');
        $hasta = $request->filled('hasta')
            ? Carbon::parse($request->input('hasta'))->endOfDay()
            : now()->endOfDay();

        $desde = match ($periodo) {
            '7d'        => now()->subDays(6)->startOfDay(),
            'mes'       => now()->startOfMonth()->startOfDay(),
            'trimestre' => now()->subMonths(3)->startOfDay(),
            default     => now()->subDays(29)->startOfDay(),
        };

        if ($request->filled('desde') && $request->input('periodo') === 'personalizado') {
            $desde = Carbon::parse($request->input('desde'))->startOfDay();
        } elseif ($request->filled('desde') && !$request->has('periodo')) {
            $desde = Carbon::parse($request->input('desde'))->startOfDay();
            $periodo = 'personalizado';
        }

        if ($desde->gt($hasta)) {
            [$desde, $hasta] = [$hasta->copy()->startOfDay(), $desde->copy()->endOfDay()];
        }

        $diasRango = max(1, $desde->copy()->startOfDay()->diffInDays($hasta->copy()->startOfDay()) + 1);

        $citasPeriodo = Cita::whereBetween('fecha_hora', [$desde, $hasta])->count();
        $movimientosPeriodo = MovimientoInventario::whereBetween('created_at', [$desde, $hasta])->count();

        $precioUnitario = fn($m) => (float) ($m?->precio_compra ?: ($m?->precio_venta ?: 0));

        // Costo proyectado de reposición para medicamentos en o bajo stock mínimo
        $costoReposicionCritica = $medicamentosConStock
            ->filter(fn($m) => (int) ($m->stock_lotes_vigentes ?? 0) <= $m->stock_minimo)
            ->sum(function ($m) use ($precioUnitario) {
                $actual = (int) ($m->stock_lotes_vigentes ?? 0);
                $objetivo = max((int) $m->stock_minimo * 2, (int) $m->stock_minimo + 1);
                $unidadesFaltantes = max(0, $objetivo - $actual);
                return $unidadesFaltantes * $precioUnitario($m);
            });

        // Costo de salidas/consumo en el periodo seleccionado
        $costoConsumoPeriodo = MovimientoInventario::with('medicamento')
            ->where('tipo', 'salida')
            ->whereBetween('created_at', [$desde, $hasta])
            ->get()
            ->sum(fn($mov) => abs((int) $mov->cantidad) * $precioUnitario($mov->medicamento));

        // Serie histórica y proyección financiera mediante PrediccionCostosService
        $tipoCosto = $request->get('tipo_costo', 'atencion');
        $valoresHistoricos = [];
        $etiquetasHistoricas = [];

        try {
            if ($tipoCosto === 'insumos') {
                $filas = DB::table('movimientos_inventario')
                    ->select(
                        DB::raw('COALESCE(SUM(total), 0) as costo'),
                        DB::raw("DATE_FORMAT(created_at, '%Y-%m') as mes")
                    )
                    ->where('tipo', 'entrada')
                    ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
                    ->groupBy('mes')
                    ->orderBy('mes')
                    ->get();
            } else {
                $filas = DB::table('cuenta_items')
                    ->join('cuentas', 'cuentas.id', '=', 'cuenta_items.cuenta_id')
                    ->select(
                        DB::raw('COALESCE(SUM(cuenta_items.importe), 0) as costo'),
                        DB::raw("DATE_FORMAT(cuentas.created_at, '%Y-%m') as mes")
                    )
                    ->where('cuentas.created_at', '>=', now()->subMonths(6)->startOfMonth())
                    ->groupBy('mes')
                    ->orderBy('mes')
                    ->get();
            }

            foreach ($filas as $fila) {
                $valoresHistoricos[] = (float) $fila->costo;
                $etiquetasHistoricas[] = $fila->mes;
            }
        } catch (\Throwable $e) {
            $valoresHistoricos = [];
            $etiquetasHistoricas = [];
        }

        if (empty($valoresHistoricos) || array_sum($valoresHistoricos) <= 0) {
            $valoresHistoricos = [15000, 18000, 16500, 22000, 21000, 25000];
            $etiquetasHistoricas = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'];
        }

        $lineal = $predictor->regresionLineal($valoresHistoricos, 3);
        $suavizado = $predictor->suavizadoExponencial($valoresHistoricos, 0.5, 3);

        $proyeccionMes = round((float) ($lineal[0] ?? end($valoresHistoricos)), 2);
        $proyeccionSuavizada = round((float) ($suavizado[0] ?? end($valoresHistoricos)), 2);

        $kpisPeriodo = [
            'citas_periodo'        => $citasPeriodo,
            'movimientos_periodo'  => $movimientosPeriodo,
            'costo_reposicion'     => round($costoReposicionCritica, 2),
            'costo_proyectado'     => round($proyeccionMes + $costoReposicionCritica, 2),
            'proyeccion_suavizada' => $proyeccionSuavizada,
            'consumo_periodo'      => round($costoConsumoPeriodo, 2),
            'dias_rango'           => $diasRango,
        ];

        // Tendencia diaria dentro del rango (limitada a 60 puntos si el rango es muy amplio)
        $tendenciaLabels = [];
        $tendenciaCitas = [];
        $tendenciaMovimientos = [];

        $citasPorDia = Cita::whereBetween('fecha_hora', [$desde, $hasta])
            ->selectRaw('DATE(fecha_hora) as dia, COUNT(*) as total')
            ->groupBy('dia')
            ->pluck('total', 'dia');

        $movsPorDia = MovimientoInventario::whereBetween('created_at', [$desde, $hasta])
            ->selectRaw('DATE(created_at) as dia, COUNT(*) as total')
            ->groupBy('dia')
            ->pluck('total', 'dia');

        $cursor = $desde->copy()->startOfDay();
        $limiteDias = $hasta->copy()->startOfDay();
        $paso = $diasRango > 60 ? (int) ceil($diasRango / 60) : 1;

        while ($cursor->lte($limiteDias)) {
            $claveDia = $cursor->toDateString();
            $tendenciaLabels[] = $cursor->format('d/m');
            $tendenciaCitas[] = (int) ($citasPorDia[$claveDia] ?? 0);
            $tendenciaMovimientos[] = (int) ($movsPorDia[$claveDia] ?? 0);
            $cursor->addDays($paso);
        }

        // Serie de costos proyectados (histórico + próximos 3 meses por regresión lineal)
        $costosLabels = array_merge($etiquetasHistoricas, ['Mes +1', 'Mes +2', 'Mes +3']);
        $costosValores = array_merge(
            array_map(fn($v) => round((float) $v, 2), $valoresHistoricos),
            array_map(fn($v) => round((float) $v, 2), $lineal)
        );

        return view('admin.dashboard', compact(
            'alertasActivas',
            'alertasCriticas',
            'medicamentosBajos',
            'camasDisponibles',
            'camasTotales',
            'auditoriaCritica',
            'resumen',
            'ultimasAlertas',
            'ultimaAuditoria',
            'medicamentosCriticos',
            'camasPorArea',
            'periodo',
            'desde',
            'hasta',
            'tipoCosto',
            'kpisPeriodo',
            'tendenciaLabels',
            'tendenciaCitas',
            'tendenciaMovimientos',
            'costosLabels',
            'costosValores'
        ));
    }

    public function slaData(): \Illuminate\Http\JsonResponse
    {
        if (\App\Models\SlaEvento::count() === 0) {
            SlaService::generarDesdeDatos(30);
        }

        $resumen = SlaService::resumen();

        $outliers = \App\Models\SlaEvento::where('is_outlier', true)
            ->with('user')
            ->orderByDesc('evento_en')
            ->limit(10)
            ->get()
            ->map(fn($e) => [
                'id' => $e->id,
                'modulo' => $e->modulo_label,
                'modulo_color' => $e->modulo_color,
                'fecha' => $e->evento_en->format('d/m/Y H:i'),
                'duracion' => round($e->duration_minutes, 1),
                'z_score' => round($e->outlier_z_score, 2),
                'severidad' => $e->severidad_z,
            ]);

        $outliersPorHora = \App\Models\SlaEvento::where('is_outlier', true)
            ->selectRaw('start_hour, COUNT(*) as total')
            ->groupBy('start_hour')
            ->orderBy('start_hour')
            ->pluck('total', 'start_hour')
            ->toArray();

        $distribucionHoraria = [];

        for ($h = 0; $h < 24; $h++) {
            $distribucionHoraria[$h] = $outliersPorHora[$h] ?? 0;
        }

        return response()->json([
            'resumen' => $resumen,
            'outliers' => $outliers,
            'distribucion_horaria' => $distribucionHoraria,
        ]);
    }

    public function dwhData(DwhService $dwh): \Illuminate\Http\JsonResponse
    {
        return response()->json($dwh->metricas());
    }
}