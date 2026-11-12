<?php

namespace App\Http\Controllers\Laboratorio;

use App\Http\Controllers\Controller;
use App\Models\SolicitudEstudio;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $pendientes = SolicitudEstudio::whereIn('estado', ['solicitado', 'en_proceso'])->count();
        
        $resultadosHoy = SolicitudEstudio::where('estado', 'resultado_listo')
            ->whereDate('resultado_en', today())
            ->count();

        $entregadosHoy = SolicitudEstudio::where('estado', 'entregado')
            ->whereDate('entregado_en', today())
            ->count();

        $solicitudesPendientes = SolicitudEstudio::with(['paciente', 'servicio', 'medicoSolicita'])
            ->whereIn('estado', ['solicitado', 'en_proceso'])
            ->orderBy('created_at', 'asc') 
            ->limit(8)
            ->get();

        $ultimosResultados = SolicitudEstudio::with(['paciente', 'servicio'])
            ->where('estado', 'resultado_listo')
            ->orderByDesc('resultado_en')
            ->limit(5)
            ->get();

        return view('laboratorio.dashboard', compact(
            'pendientes',
            'resultadosHoy',
            'entregadosHoy',
            'solicitudesPendientes',
            'ultimosResultados'
        ));
    }
}