<?php

namespace App\Http\Controllers\Enfermeria;

use App\Http\Controllers\Controller;
use App\Models\Admision;
use App\Models\Alerta;
use App\Models\Cama;
use App\Models\CamaPaciente;
use App\Models\Paciente;
use App\Models\SignoVital;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $idsConCama = CamaPaciente::where('activa', true)
            ->pluck('paciente_id')
            ->unique();

        $seguimientoQuery = Paciente::where('activo', true)
            ->where(function ($query) use ($idsConCama) {
                $query->whereIn('id', $idsConCama)
                    ->orWhereHas('signosVitales', function ($q) {
                        $q->where('created_at', '>=', now()->subHours(48));
                    });
            });

        $pacientesEnSeguimientoTotal = (clone $seguimientoQuery)->count();

        $pacientesEnSeguimiento = $seguimientoQuery
            ->with('ultimoSignoVital')
            ->orderByDesc('updated_at')
            ->limit(10)
            ->get();

        $pacientesEnSeguimiento->each(function ($paciente) {
            $paciente->cama_actual = CamaPaciente::with('cama')
                ->where('paciente_id', $paciente->id)
                ->where('activa', true)
                ->first();
        });

        $camasOcupadas = Cama::where('activo', true)
            ->where('estado', 'ocupada')
            ->count();

        $camasDisponibles = Cama::where('estado', 'disponible')
            ->where('activo', true)
            ->count();

        $camasTotales = Cama::where('activo', true)->count();

        $signosHoy = SignoVital::whereDate('created_at', today())->count();

        $admisionesEnEsperaTotal = Admision::where('estado', 'en_espera')->count();

        $admisionesEnEspera = Admision::where('estado', 'en_espera')
            ->with('paciente')
            ->orderBy('fecha_hora_llegada')
            ->limit(10)
            ->get();

        $alertasActivasTotal = Alerta::whereIn('estado', ['activa', 'vista'])->count();

        $alertasActivas = Alerta::whereIn('estado', ['activa', 'vista'])
            ->orderByRaw("FIELD(nivel, 'critico', 'advertencia', 'info')")
            ->limit(5)
            ->get();

        return view('enfermeria.dashboard', compact(
            'pacientesEnSeguimiento',
            'pacientesEnSeguimientoTotal',
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
