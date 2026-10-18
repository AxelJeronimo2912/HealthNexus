<?php

namespace App\Http\Controllers;

use App\Models\Lote;
use App\Models\Medicamento;
use App\Models\MovimientoInventario; 
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse; 

class ExistenciaController extends Controller
{
    /**
     * Vista general de existencias.
     * Muestra todos los medicamentos con su stock y alertas.
     */
    public function index(Request $request): View
    {
        $busqueda = $request->input('buscar');
        $filtro = $request->input('filtro');

        $query = Medicamento::query()
            ->with(['lotes' => function ($q) {
                $q->where('cantidad_disponible', '>', 0)
                  ->orderBy('fecha_caducidad');
            }])
            // Optimización: Sumamos el stock en la misma consulta SQL
            ->withSum(['lotes as stock_calculado_sql' => function ($q) {
                $q->where('cantidad_disponible', '>', 0)
                  ->where('fecha_caducidad', '>=', now())
                  ->where('activo', true);
            }], 'cantidad_disponible')
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

        // Lotes detallados para el banner de alerta
        $lotesPorCaducar = Lote::with('medicamento')
            ->whereBetween('fecha_caducidad', [now(), now()->addDays(30)])
            ->where('cantidad_disponible', '>', 0)
            ->orderBy('fecha_caducidad', 'asc')
            ->get();

        return view('existencias.index', compact('medicamentos', 'busqueda', 'filtro', 'stats', 'lotesPorCaducar'));
    }

    /**
     * Detalle de un medicamento: sus lotes y movimientos.
     */
    public function show(Request $request, Medicamento $medicamento) 
    {
        $lotes = $medicamento->lotes()
            ->orderBy('fecha_caducidad')
            ->get();

        $movimientos = $medicamento->movimientos()
            ->with(['user', 'lote'])
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        // Si es AJAX, devolvemos solo el HTML (String)
        if ($request->ajax()) {
            return view('existencias.show', compact('medicamento', 'lotes', 'movimientos'))->render();
        }

        // Si es normal, devolvemos la vista (View)
        return view('existencias.show', compact('medicamento', 'lotes', 'movimientos'));
    }

    /**
     * Listado de lotes por caducidad.
     */
    public function lotes(Request $request) 
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

        if ($request->filled('medicamento')) {
            $query->where('medicamento_id', $request->medicamento);
        }

        $lotes = $query->orderBy('fecha_caducidad')
            ->paginate(20)
            ->withQueryString();

        // Si es AJAX, devolvemos solo el HTML (String)
        if ($request->ajax()) {
            return view('existencias.lotes', compact('lotes', 'filtro'))->render();
        }

        // Si es normal, devolvemos la vista (View)
        return view('existencias.lotes', compact('lotes', 'filtro'));
    }

   
    public function storeMovimiento(Request $request): RedirectResponse
    {
        // Validar los datos que vienen del formulario del modal
        $request->validate([
            'medicamento_id' => 'required|exists:medicamentos,id',
            'tipo' => 'required|in:entrada,salida,ajuste',
            'cantidad' => 'required|integer|min:1',
            'motivo' => 'nullable|string|max:255',
            'codigo_lote' => 'nullable|string',
            'codigo_lote_nuevo' => 'nullable|string',
            'fecha_caducidad' => 'nullable|date',
        ]);

        $medicamento = Medicamento::findOrFail($request->medicamento_id);
        
        $stockAnterior = $medicamento->stock_total_calculado;
        $cantidad = (int) $request->cantidad;
        
        // Lógica para determinar si suma o resta
        $esSalida = in_array($request->tipo, ['salida']);
        $cantidadMovimiento = $esSalida ? -$cantidad : $cantidad;
        $stockNuevo = $stockAnterior + $cantidadMovimiento;

        // 1. Guardamos el movimiento en la base de datos
        MovimientoInventario::create([
            'medicamento_id' => $medicamento->id,
            'user_id'        => auth()->id(),
            'tipo'           => $request->tipo,
            'cantidad'       => $cantidadMovimiento,
            'stock_anterior' => $stockAnterior,
            'stock_nuevo'    => $stockNuevo,
            'motivo'         => $request->motivo ?? 'Movimiento manual',
            // 'referencia_tipo' => 'manual',
            // 'referencia_id' => null,
        ]);

        // 2. Lógica extra para LOTES
        if ($request->tipo === 'entrada' && $request->filled('codigo_lote_nuevo')) {
            // Si es una entrada, creamos el lote nuevo
            Lote::create([
                'medicamento_id' => $medicamento->id,
                'codigo_lote' => $request->codigo_lote_nuevo,
                'fecha_caducidad' => $request->fecha_caducidad ?? now()->addYear(),
                'fecha_ingreso' => now(),
                'cantidad_inicial' => $cantidad,
                'cantidad_disponible' => $cantidad,
                'precio_compra' => $medicamento->precio_compra ?? 0,
                'activo' => true,
            ]);
        } elseif ($request->tipo === 'salida' && $request->filled('codigo_lote')) {
            // Si es salida, buscamos el lote y le restamos
            $lote = Lote::where('medicamento_id', $medicamento->id)
                        ->where('codigo_lote', $request->codigo_lote)
                        ->first();
            
            if ($lote) {
                $lote->decrement('cantidad_disponible', $cantidad);
            }
        }

        return back()->with('success', 'Movimiento registrado correctamente.');
    }
}