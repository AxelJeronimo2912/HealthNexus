<?php

namespace App\Http\Controllers\Farmacia;

use App\Http\Controllers\Controller;
use App\Models\Alerta;
use App\Models\Consulta;
use App\Models\Medicamento;
use App\Services\PrediccionService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // 1. Medicamentos con stock bajo
        $medicamentosBajos = Medicamento::where('activo', true)
            ->with('lotes')
            ->get()
            ->filter(fn($m) => $m->stock_total_calculado <= $m->stock_minimo)
            ->sortBy(fn($m) => $m->stock_total_calculado)
            ->values();

        // 2. Medicamentos críticos (top 10 de los bajos)
        $medicamentosCriticos = $medicamentosBajos->take(10);

        // Contador precalculado
        $medicamentosBajosTotal = $medicamentosBajos->count();

        // 3. Recetas pendientes de dispensar
        $recetasPendientes = Consulta::where('dispensada', false)
            ->whereNotNull('diagnostico_principal')
            ->whereHas('medicamentos')
            ->with(['paciente', 'medico'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $recetasPendientesTotal = $recetasPendientes->count();

        // 4. Dispensaciones de hoy
        $dispensacionesHoy = Consulta::where('dispensada', true)
            ->whereDate('dispensada_en', today())
            ->count();

        // 5. Alertas de stock
        $alertasStock = Alerta::whereIn('estado', ['activa', 'vista'])
            ->where(function ($q) {
                $q->where('titulo', 'LIKE', '%stock%')
                  ->orWhere('titulo', 'LIKE', '%medicamento%');
            })
            ->limit(5)
            ->get();

        $alertasStockTotal = $alertasStock->count();

        // 6. Top 5 medicamentos con mayor consumo (predicción)
        $topDemanda = PrediccionService::rankingDemanda(30, 5);
        $topDemandaTotal = count($topDemanda);

        return view('farmacia.dashboard', compact(
            'medicamentosBajos',
            'medicamentosBajosTotal',
            'medicamentosCriticos',
            'recetasPendientes',
            'recetasPendientesTotal',
            'dispensacionesHoy',
            'alertasStock',
            'alertasStockTotal',
            'topDemanda',
            'topDemandaTotal'
        ));
    }
}