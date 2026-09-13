<?php

namespace App\Http\Controllers;

use App\Models\Lote;
use App\Models\Medicamento;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExistenciaController extends Controller
{
    /**
     * Vista general de existencias.
     * Muestra todos los medicamentos con su stock y alertas.
     */
    public function index(Request $request): View
    {
        $busqueda = $request->input('buscar');
        $filtro = $request->input('filtro'); // bajo, caducado, proximo

        $query = Medicamento::query()
            ->with(['lotes' => function ($q) {
                $q->where('cantidad_disponible', '>', 0)
                  ->orderBy('fecha_caducidad');
            }])
            ->when($busqueda, function ($q) use ($busqueda) {
                $q->where('nombre', 'like', "%{$busqueda}%")
                  ->orWhere('sustancia_activa', 'like', "%{$busqueda}%");
            });

        // Filtros
        if ($filtro === 'bajo') {
            $query->whereRaw('(SELECT COALESCE(SUM(cantidad_disponible), 0) FROM lotes WHERE lotes.medicamento_id = medicamentos.id AND cantidad_disponible > 0 AND fecha_caducidad >= CURDATE()) <= medicamentos.stock_minimo');
        } elseif ($filtro === 'caducado') {
            $query->whereHas('lotes', function ($q) {
                $q->where('fecha_caducidad', '<', now())
                  ->where('cantidad_disponible', '>', 0);
            });
        } elseif ($filtro === 'proximo') {
            $query->whereHas('lotes', function ($q) {
                $q->whereBetween('fecha_caducidad', [now(), now()->addDays(30)])
                  ->where('cantidad_disponible', '>', 0);
            });
        }

        $medicamentos = $query->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        // Estadísticas globales
        $stats = [
            'total_medicamentos' => Medicamento::count(),
            'stock_bajo' => Medicamento::whereRaw('(SELECT COALESCE(SUM(cantidad_disponible), 0) FROM lotes WHERE lotes.medicamento_id = medicamentos.id AND cantidad_disponible > 0 AND fecha_caducidad >= CURDATE()) <= medicamentos.stock_minimo')->count(),
            'proximos_caducar' => Lote::whereBetween('fecha_caducidad', [now(), now()->addDays(30)])
                ->where('cantidad_disponible', '>', 0)->count(),
            'caducados' => Lote::where('fecha_caducidad', '<', now())
                ->where('cantidad_disponible', '>', 0)->count(),
        ];

        return view('existencias.index', compact('medicamentos', 'busqueda', 'filtro', 'stats'));
    }

    /**
     * Detalle de un medicamento: sus lotes y movimientos.
     */
    public function show(Medicamento $medicamento): View
    {
        $lotes = $medicamento->lotes()
            ->orderBy('fecha_caducidad')
            ->get();

        $movimientos = $medicamento->movimientos()
            ->with(['user', 'lote'])
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        return view('existencias.show', compact('medicamento', 'lotes', 'movimientos'));
    }

    /**
     * Listado de lotes por caducidad.
     */
    public function lotes(Request $request): View
    {
        $filtro = $request->input('filtro');

        $query = Lote::with('medicamento')
            ->where('cantidad_disponible', '>', 0);

        if ($filtro === 'caducados') {
            $query->where('fecha_caducidad', '<', now());
        } elseif ($filtro === 'proximos') {
            $query->whereBetween('fecha_caducidad', [now(), now()->addDays(30)]);
        } elseif ($filtro === 'vigentes') {
            $query->where('fecha_caducidad', '>=', now());
        }

        $lotes = $query->orderBy('fecha_caducidad')
            ->paginate(20)
            ->withQueryString();

        return view('existencias.lotes', compact('lotes', 'filtro'));
    }
}