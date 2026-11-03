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
    /**
     * Mostrar el listado del historial de movimientos con filtros avanzados.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $esAdmin = $user->hasRole('administrador');

        // Captura de filtros
        $medicamentoId = $request->input('medicamento_id');
        $tipo = $request->input('tipo');
        $userId = $request->input('user_id');
        $loteId = $request->input('lote_id');
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

        if ($loteId) {
            $query->where('lote_id', $loteId);
        }

        if ($desde) {
            $query->whereDate('created_at', '>=', $desde);
        }

        if ($hasta) {
            $query->whereDate('created_at', '<=', $hasta);
        }

        $movimientos = $query->paginate(25)->withQueryString();

        // Datos para alimentar los selects de los filtros
        $medicamentos = Medicamento::orderBy('nombre')->get(['id', 'nombre', 'concentracion']);
        $usuarios = User::orderBy('nombre')->get(['id', 'nombre', 'apellido_paterno']);
        $lotes = Lote::orderBy('codigo_lote')->get(['id', 'codigo_lote']);

        // Estadísticas globales de inventario
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
            'movimientos', 'medicamentos', 'usuarios', 'lotes', 'stats',
            'medicamentoId', 'tipo', 'userId', 'loteId', 'desde', 'hasta'
        ));
    }

    /**
     * Detalle de un movimiento individual para el modal por AJAX.
     */
    public function show(Request $request, MovimientoInventario $movimiento)
    {
        $movimiento->load(['medicamento', 'lote', 'user']);

        // Si es una petición asíncrona (AJAX), devolvemos solo el render del componente del modal
        if ($request->ajax()) {
            return view('movimientos.show', compact('movimiento'))->render();
        }

        return view('movimientos.show', compact('movimiento'));
    }
}