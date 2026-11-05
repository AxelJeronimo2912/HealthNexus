<?php

namespace App\Http\Controllers\Medico;

use App\Http\Controllers\Controller;
use App\Models\Alerta;
use App\Models\Cita;
use App\Models\Consulta;
use App\Models\Paciente;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $citasHoy = Cita::where('medico_id', $user->id)
            ->whereDate('fecha_hora', today())
            ->with(['paciente', 'consulta'])
            ->orderBy('fecha_hora')
            ->get();

        $pacientesAsignadosIds = $user->pacientesAsignados()
            ->pluck('paciente_id')
            ->unique();

        $pacientesAsignados = $pacientesAsignadosIds->count();

        $consultasHoy = Consulta::where('medico_id', $user->id)
            ->whereDate('created_at', today())
            ->count();

        $citasAtendidasHoy = Cita::where('medico_id', $user->id)
            ->whereDate('fecha_hora', today())
            ->where('estado', 'atendida')
            ->count();

        $alertasActivas = Alerta::whereIn('estado', ['activa', 'vista'])
            ->whereIn('nivel', ['critico', 'advertencia'])
            ->where(function ($query) use ($user) {
                $query->whereNull('user_id')
                    ->orWhere('user_id', $user->id);
            })
            ->count();

        $proximasCitas = Cita::where('medico_id', $user->id)
            ->whereDate('fecha_hora', '>', today())
            ->whereDate('fecha_hora', '<=', today()->addDays(3))
            ->with('paciente')
            ->orderBy('fecha_hora')
            ->limit(5)
            ->get();

        $riesgoQuery = Paciente::whereIn('id', $pacientesAsignadosIds)
            ->whereHas('signosVitales', function ($q) {
                $q->where('created_at', '>=', now()->subHours(24))
                    ->where(function ($qq) {
                        $qq->whereIn('triage', ['rojo', 'naranja'])
                            ->orWhere('saturacion_oxigeno', '<', 92)
                            ->orWhere('frecuencia_cardiaca', '>', 120);
                    });
            });

        $pacientesRiesgoTotal = (clone $riesgoQuery)->count();

        $pacientesRiesgo = $riesgoQuery
            ->with('ultimoSignoVital')
            ->limit(5)
            ->get();

        return view('medico.dashboard', compact(
            'citasHoy',
            'pacientesAsignados',
            'consultasHoy',
            'citasAtendidasHoy',
            'alertasActivas',
            'proximasCitas',
            'pacientesRiesgo',
            'pacientesRiesgoTotal'
        ));
    }
}
