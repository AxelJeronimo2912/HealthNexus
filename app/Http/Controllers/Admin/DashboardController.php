<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alerta;
use App\Models\AuditLog;
use App\Models\Cama;
use App\Models\Cita;
use App\Models\Consulta;
use App\Models\Medicamento;
use App\Models\Paciente;
use Illuminate\View\View;
use App\Services\SlaService;
use App\Services\DwhService;

class DashboardController extends Controller
{
    public function index(): View
    {

        // 1. Alertas activas
        $alertasActivas = Alerta::whereIn('estado', ['activa', 'vista'])->count();
        $alertasCriticas = Alerta::where('nivel', 'critico')
            ->whereIn('estado', ['activa', 'vista'])->count();

        // 2. Medicamentos con stock bajo
        $medicamentosBajos = Medicamento::where('activo', true)
            ->with('lotes')
            ->get()
            ->filter(fn($m) => $m->stock_total_calculado <= $m->stock_minimo)
            ->count();

        // 3. Camas disponibles
        $camasDisponibles = Cama::where('estado', 'disponible')->where('activo', true)->count();
        $camasTotales = Cama::where('activo', true)->count();

        // 4. Eventos de auditoría críticos hoy
        $auditoriaCritica = AuditLog::where('severidad', 'critical')
            ->whereDate('created_at', today())
            ->count();

        // ============ RESUMEN DE OPERACIÓN ============

        $resumen = [
            'pacientes_activos' => Paciente::where('activo', true)->count(),
            'citas_hoy' => Cita::whereDate('fecha_hora', today())->count(),
            'consultas_hoy' => Consulta::whereDate('created_at', today())->count(),
            'camas_ocupadas' => Cama::where('estado', 'ocupada')->count(),
        ];

        // ============ DATOS PARA MODALES (los usaremos en el PASO 2) ============

        // Últimas alertas
        $ultimasAlertas = Alerta::whereIn('estado', ['activa', 'vista'])
            ->orderByRaw("FIELD(nivel, 'critico', 'advertencia', 'info')")
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        // Últimos eventos de auditoría relevantes
        $ultimaAuditoria = AuditLog::with('user')
            ->whereIn('severidad', ['critical', 'warning'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        // Medicamentos en riesgo
        $medicamentosCriticos = Medicamento::where('activo', true)
            ->with('lotes')
            ->get()
            ->filter(fn($m) => $m->stock_total_calculado <= $m->stock_minimo)
            ->sortBy(fn($m) => $m->stock_total_calculado)
            ->take(10);

        // Camas por área
        $camasPorArea = Cama::select('area')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN estado = 'disponible' THEN 1 ELSE 0 END) as disponibles")
            ->selectRaw("SUM(CASE WHEN estado = 'ocupada' THEN 1 ELSE 0 END) as ocupadas")
            ->where('activo', true)
            ->groupBy('area')
            ->get();

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
            'camasPorArea'
        ));
    }
    public function slaData(): \Illuminate\Http\JsonResponse
{
    // Asegurar que existan eventos recientes
    if (\App\Models\SlaEvento::count() === 0) {
        SlaService::generarDesdeDatos(30);
    }

    $resumen = SlaService::resumen();

    // Top 10 outliers más recientes
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

    // Distribución por hora de outliers
    $outliersPorHora = \App\Models\SlaEvento::where('is_outlier', true)
        ->selectRaw('start_hour, COUNT(*) as total')
        ->groupBy('start_hour')
        ->orderBy('start_hour')
        ->pluck('total', 'start_hour')
        ->toArray();

    // Rellenar horas faltantes con 0
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