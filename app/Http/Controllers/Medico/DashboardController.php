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

        // 1. Citas de hoy asignadas al médico
        $citasHoy = Cita::where('medico_id', $user->id)
            ->whereDate('fecha_hora', today())
            ->with(['paciente', 'consulta'])
            ->orderBy('fecha_hora')
            ->get();

        // 2. Pacientes asignados al médico
        $pacientesAsignados = $user->pacientesAsignados()->count();

        // 3. Consultas finalizadas hoy por este médico
        //    (cuenta solo consultas reales, no citas)
        $consultasHoy = Consulta::where('medico_id', $user->id)
            ->whereDate('created_at', today())
            ->count();

        // 4. Citas atendidas hoy
        $citasAtendidasHoy = Cita::where('medico_id', $user->id)
            ->whereDate('fecha_hora', today())
            ->where('estado', 'atendida')
            ->count();

        // 5. Alertas activas relacionadas
        $alertasActivas = Alerta::whereIn('estado', ['activa', 'vista'])
            ->whereIn('nivel', ['critico', 'advertencia'])
            ->count();

        // 6. Próximas citas (siguiente 3 días, sin contar hoy)
        $proximasCitas = Cita::where('medico_id', $user->id)
            ->whereDate('fecha_hora', '>', today())
            ->whereDate('fecha_hora', '<=', today()->addDays(3))
            ->with('paciente')
            ->orderBy('fecha_hora')
            ->limit(5)
            ->get();

        // 7. Pacientes con signos vitales anormales recientes
        $pacientesRiesgo = Paciente::whereHas('signosVitales', function ($q) {
                $q->where('created_at', '>=', now()->subHours(24))
                  ->where(function ($qq) {
                      $qq->whereIn('triage', ['rojo', 'naranja'])
                         ->orWhere('saturacion_oxigeno', '<', 92)
                         ->orWhere('frecuencia_cardiaca', '>', 120);
                  });
            })
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
            'pacientesRiesgo'
        ));
    }
}