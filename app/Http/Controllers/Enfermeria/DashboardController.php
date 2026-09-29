<?php

namespace App\Http\Controllers\Enfermeria;

use App\Http\Controllers\Controller;
use App\Models\Admision;
use App\Models\Alerta;
use App\Models\Cama;
use App\Models\Paciente;
use App\Models\SignoVital;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // 1. Pacientes en seguimiento (con signos vitales en las últimas 24h)
        $pacientesEnSeguimiento = Paciente::where('activo', true)
            ->whereHas('signosVitales', function ($q) {
                $q->where('created_at', '>=', now()->subHours(24));
            })
            ->with('ultimoSignoVital')
            ->limit(10)
            ->get();

        // 2. Camas
        $camasOcupadas    = Cama::where('estado', 'ocupada')->count();
        $camasDisponibles = Cama::where('estado', 'disponible')->where('activo', true)->count();
        $camasTotales     = Cama::where('activo', true)->count();

        // 3. Signos vitales registrados hoy
        $signosHoy = SignoVital::whereDate('created_at', today())->count();

        // 4. Admisiones en espera
        $admisionesEnEspera = Admision::where('estado', 'en_espera')
            ->with('paciente')
            ->orderBy('fecha_hora_llegada')
            ->limit(10)
            ->get();
        $admisionesEnEsperaTotal = $admisionesEnEspera->count();

        // 5. Alertas activas
        $alertasActivas = Alerta::whereIn('estado', ['activa', 'vista'])
            ->orderByRaw("FIELD(nivel, 'critico', 'advertencia', 'info')")
            ->limit(5)
            ->get();
        $alertasActivasTotal = $alertasActivas->count();

        return view('enfermeria.dashboard', compact(
            'pacientesEnSeguimiento',
            'camasOcupadas',
            'camasDisponibles',
            'camasTotales',
            'signosHoy',
            'admisionesEnEspera',
            'admisionesEnEsperaTotal',
            'alertasActivas',
            'alertasActivasTotal'
        ));
    }
}