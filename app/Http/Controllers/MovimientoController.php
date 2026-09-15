<?php

namespace App\Http\Controllers;

use App\Models\Lote;
use App\Models\Medicamento;
use App\Models\MovimientoInventario;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MovimientoController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $esAdmin = $user->hasRole('administrador');

        // Filtros
        $medicamentoId = $request->input('medicamento_id');
        $tipo = $request->input('tipo');
        $userId = $request->input('user_id');
        $desde = $request->input('desde');
        $hasta = $request->input('hasta');

        $query = MovimientoInventario::with(['medicamento', 'lote', 'user'])
            ->orderByDesc('created_at');

        if ($medicamentoId) {
            $query->where('medicamento_id', $medicamentoId);
        }

        if ($tipo) {
            $query->where('tipo', $tipo);
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($desde) {
            $query->whereDate('created_at', '>=', $desde);
        }

        if ($hasta) {
            $query->whereDate('created_at', '<=', $hasta);
        }

        $movimientos = $query->paginate(25)->withQueryString();

        // Datos para filtros
        $medicamentos = Medicamento::orderBy('nombre')->get(['id', 'nombre', 'concentracion']);
        $usuarios = User::orderBy('nombre')->get(['id', 'nombre', 'apellido_paterno']);

        // Estadísticas globales
        $stats = [
            'total_movimientos' => MovimientoInventario::count(),
            'entradas_hoy' => MovimientoInventario::where('tipo', 'entrada')->whereDate('created_at', today())->count(),
            'salidas_hoy' => MovimientoInventario::where('tipo', 'salida')->whereDate('created_at', today())->count(),
            'ajustes_mes' => MovimientoInventario::where('tipo', 'ajuste')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        return view('movimientos.index', compact(
            'movimientos', 'medicamentos', 'usuarios', 'stats',
            'medicamentoId', 'tipo', 'userId', 'desde', 'hasta'
        ));
    }

    /**
     * Detalle de un movimiento individual.
     */
    public function show(MovimientoInventario $movimiento): View
    {
        $movimiento->load(['medicamento', 'lote', 'user']);

        return view('movimientos.show', compact('movimiento'));
    }
}