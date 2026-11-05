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
        $medicamentosBajos = Medicamento::where('activo', true)
            ->with('lotes')
            ->get()
            ->filter(fn($m) => $m->stock_total_calculado <= $m->stock_minimo)
            ->sortBy(fn($m) => $m->stock_total_calculado)
            ->values();

        $medicamentosCriticos = $medicamentosBajos->take(10);

        $medicamentosBajosTotal = $medicamentosBajos->count();

        $recetasPendientesQuery = Consulta::where('estado', 'finalizada')
            ->where('dispensada', false)
            ->where(function ($query) {
                $query->whereHas('medicamentos')
                    ->orWhereNotNull('receta_libre');
            });

        $recetasPendientesTotal = (clone $recetasPendientesQuery)->count();

        $recetasPendientes = $recetasPendientesQuery
            ->with(['paciente', 'medico', 'medicamentos'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $dispensacionesHoy = Consulta::where('dispensada', true)
            ->whereDate('dispensada_en', today())
            ->count();

        $alertasStockQuery = Alerta::whereIn('estado', ['activa', 'vista'])
            ->where(function ($q) {
                $q->where('titulo', 'LIKE', '%stock%')
                    ->orWhere('titulo', 'LIKE', '%medicamento%');
            });

        $alertasStockTotal = (clone $alertasStockQuery)->count();

        $alertasStock = $alertasStockQuery
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

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
