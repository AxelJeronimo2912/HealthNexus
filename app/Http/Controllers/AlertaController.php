<?php

namespace App\Http\Controllers;

use App\Models\Alerta;
use App\Services\AlertaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AlertaController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $esAdmin = $user->hasRole('administrador');

        $filtroEstado = $request->input('estado', 'activas');
        $filtroNivel = $request->input('nivel');
        $filtroTipo = $request->input('tipo');

        $query = Alerta::with(['user', 'resueltaPor'])
            ->orderByRaw("FIELD(nivel, 'critico', 'advertencia', 'info')")
            ->orderByDesc('created_at');

        // Filtro por rol: admin ve todas, otros solo las suyas o generales
        if (!$esAdmin) {
            $query->where(function ($q) use ($user) {
                $q->whereNull('user_id')
                  ->orWhere('user_id', $user->id);
            });
        }

        // Filtro por estado
        if ($filtroEstado === 'activas') {
            $query->whereIn('estado', ['activa', 'vista']);
        } elseif ($filtroEstado) {
            $query->where('estado', $filtroEstado);
        }

        if ($filtroNivel) {
            $query->where('nivel', $filtroNivel);
        }

        if ($filtroTipo) {
            $query->where('tipo', $filtroTipo);
        }

        $alertas = $query->paginate(20)->withQueryString();

        $stats = [
            'total_activas' => Alerta::whereIn('estado', ['activa', 'vista'])->count(),
            'criticas' => Alerta::where('nivel', 'critico')->whereIn('estado', ['activa', 'vista'])->count(),
            'advertencias' => Alerta::where('nivel', 'advertencia')->whereIn('estado', ['activa', 'vista'])->count(),
            'resueltas_hoy' => Alerta::where('estado', 'resuelta')->whereDate('resuelta_en', today())->count(),
        ];

        return view('alertas.index', compact('alertas', 'stats', 'filtroEstado', 'filtroNivel', 'filtroTipo'));
    }

    public function marcarVista(Alerta $alerta): RedirectResponse
    {
        $alerta->update(['estado' => 'vista']);
        return back()->with('success', 'Alerta marcada como vista.');
    }

    public function resolver(Alerta $alerta): RedirectResponse
    {
        $alerta->update([
            'estado' => 'resuelta',
            'resuelta_por' => auth()->id(),
            'resuelta_en' => now(),
        ]);

        return back()->with('success', 'Alerta resuelta.');
    }

    public function descartar(Alerta $alerta): RedirectResponse
    {
        $alerta->update(['estado' => 'descartada']);
        return back()->with('success', 'Alerta descartada.');
    }

    public function generar(): RedirectResponse
    {
        $resultados = AlertaService::generarTodas();
        $total = array_sum($resultados);

        return back()->with('success', "Se generaron {$total} alertas nuevas.");
    }

    /**
     * Endpoint AJAX: cuenta de alertas activas (para el badge del sidebar).
     */
    public function contar(): JsonResponse
    {
        $user = auth()->user();
        $esAdmin = $user->hasRole('administrador');

        $query = Alerta::whereIn('estado', ['activa', 'vista']);

        if (!$esAdmin) {
            $query->where(function ($q) use ($user) {
                $q->whereNull('user_id')->orWhere('user_id', $user->id);
            });
        }

        return response()->json([
            'total' => $query->count(),
            'criticas' => (clone $query)->where('nivel', 'critico')->count(),
        ]);
    }
}