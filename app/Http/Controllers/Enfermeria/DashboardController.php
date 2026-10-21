<?php

namespace App\Http\Controllers\Enfermeria;

use App\Http\Controllers\Controller;
use App\Models\Admision;
use App\Models\Alerta;
use App\Models\Cama;
use App\Models\CamaPaciente; // <-- Asegúrate de importar este modelo
use App\Models\Paciente;
use App\Models\SignoVital;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // 1. Pacientes en seguimiento (Cama activa O signos vitales en las últimas 48h)
        
        // Primero obtenemos los IDs de pacientes que tienen cama activa
        $idsConCama = CamaPaciente::where('activa', true)->pluck('paciente_id')->unique();

        $pacientesEnSeguimiento = Paciente::where('activo', true)
            ->where(function ($query) use ($idsConCama) {
                // Condición A: Que su ID esté en la lista de los que tienen cama
                $query->whereIn('id', $idsConCama)
                // Condición B: O que tenga signos vitales recientes
                ->orWhereHas('signosVitales', function ($q) {
                    $q->where('created_at', '>=', now()->subHours(48));
                });
            })
            ->with('ultimoSignoVital') // Solo cargamos el último signo vital
            ->limit(10)
            ->get();

        // Ahora, adjuntamos manualmente la cama a cada paciente para usarla en la vista
        $pacientesEnSeguimiento->map(function ($paciente) {
            $paciente->cama_actual = CamaPaciente::with('cama')
                ->where('paciente_id', $paciente->id)
                ->where('activa', true)
                ->first();
            return $paciente;
        });

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