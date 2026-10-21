<?php

namespace App\Http\Controllers;

use App\Models\Consulta;
use App\Services\InventarioService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use App\Models\MovimientoInventario; 
use App\Models\Lote;  
class DispensacionController extends Controller
{
    /**
     * Lista de recetas pendientes de dispensar.
     */
    public function index(Request $request): View
    {
        $busqueda = $request->input('buscar');
        $filtro = $request->input('filtro', 'pendientes');

        $query = Consulta::with(['paciente', 'medico', 'medicamentos', 'dispensadaPor'])
            ->where('estado', 'finalizada')  
            ->where(function ($q) {
                $q->whereHas('medicamentos')
                  ->orWhereNotNull('receta_libre');
            });

        // Filtro por estado de dispensación
        if ($filtro === 'pendientes') {
            $query->where('dispensada', false);
        } elseif ($filtro === 'dispensadas') {
            $query->where('dispensada', true);
        }

        // Búsqueda por paciente
        if ($busqueda) {
            $query->whereHas('paciente', function ($q) use ($busqueda) {
                $q->where('nombre', 'like', "%{$busqueda}%")
                  ->orWhere('apellido_paterno', 'like', "%{$busqueda}%")
                  ->orWhere('apellido_materno', 'like', "%{$busqueda}%")
                  ->orWhere('curp', 'like', "%{$busqueda}%");
            });
        }

        $recetas = $query->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        // Estadísticas
        $stats = [
            'pendientes' => Consulta::where('estado', 'finalizada')
                ->where('dispensada', false)
                ->where(function ($q) {
                    $q->whereHas('medicamentos')->orWhereNotNull('receta_libre');
                })->count(),
            'dispensadas_hoy' => Consulta::where('dispensada', true)
                ->whereDate('dispensada_en', today())
                ->count(),
            'dispensadas_mes' => Consulta::where('dispensada', true)
                ->whereMonth('dispensada_en', now()->month)
                ->whereYear('dispensada_en', now()->year)
                ->count(),
        ];

        return view('dispensaciones.index', compact('recetas', 'busqueda', 'filtro', 'stats'));
    }

    /**
     * Ver detalle de una receta para dispensar.
     */
    public function show(Request $request, Consulta $consulta) 
{
    $consulta->load(['paciente', 'medico', 'medicamentos', 'dispensadaPor', 'cita']);

    if ($request->ajax()) {
        return view('dispensaciones.show', compact('consulta'))->render();
    }

    return view('dispensaciones.show', compact('consulta'));
}
    /**
     * Dispensar la receta: descuenta stock de cada medicamento.
     */
       public function dispensar(Request $request, Consulta $consulta) 
    {
        if ($consulta->dispensada) {
            if ($request->ajax()) return response()->json(['success' => false, 'message' => 'Esta receta ya fue dispensada.'], 400);
            return back()->with('error', 'Esta receta ya fue dispensada.');
        }

        if ($consulta->medicamentos()->count() === 0 && empty($consulta->receta_libre)) {
            if ($request->ajax()) return response()->json(['success' => false, 'message' => 'Esta consulta no tiene receta.'], 400);
            return back()->with('error', 'Esta consulta no tiene receta.');
        }

        $request->validate([
            'notas_dispensacion' => ['nullable', 'string', 'max:500'],
        ]);

        DB::beginTransaction();

        try {
            foreach ($consulta->medicamentos as $med) {
                $cantidad = 1; 

                if (!InventarioService::tieneStock($med, $cantidad)) {
                    throw new \RuntimeException("Sin stock suficiente de {$med->nombre}. Disponible: {$med->stock_total_calculado}");
                }

                InventarioService::salida(
                    $med,
                    $cantidad,
                    'Dispensación de receta',
                    ['tipo' => 'consulta', 'id' => $consulta->id]
                );
            }

            $consulta->update([
                'dispensada' => true,
                'dispensada_en' => now(),
                'dispensada_por' => auth()->id(),
                'notas_dispensacion' => $request->notas_dispensacion,
            ]);

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true, 
                    'message' => 'Receta dispensada correctamente.'
                ]);
            }

            return redirect()->route('dispensaciones.show', $consulta)
                ->with('success', 'Receta dispensada correctamente.');
                
        } catch (\Throwable $e) {
            DB::rollBack();
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Error al dispensar: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Error al dispensar: ' . $e->getMessage());
        }
    }

    /**
     * Revertir una dispensación (solo admin).
     */
    public function revertir(Request $request, Consulta $consulta) 
    {
        if (!auth()->user()->hasRole('administrador')) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Solo el administrador puede revertir dispensaciones.'], 403);
            }
            abort(403, 'Solo el administrador puede revertir dispensaciones.');
        }

        if (!$consulta->dispensada) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Esta receta no ha sido dispensada.'], 400);
            }
            return back()->with('error', 'Esta receta no ha sido dispensada.');
        }

        DB::beginTransaction();

        try {
            $movimientosSalida = MovimientoInventario::where('referencia_tipo', 'consulta')
                ->where('referencia_id', $consulta->id)
                ->where('tipo', 'salida')
                ->get();

            if ($movimientosSalida->count() > 0) {
                foreach ($movimientosSalida as $mov) {
                    MovimientoInventario::create([
                        'medicamento_id' => $mov->medicamento_id,
                        'lote_id'        => $mov->lote_id, 
                        'user_id'        => auth()->id(),
                        'tipo'           => 'entrada',
                        'cantidad'       => abs($mov->cantidad), 
                        'stock_anterior' => $mov->medicamento->stock_total_calculado,
                        'stock_nuevo'    => $mov->medicamento->stock_total_calculado + abs($mov->cantidad),
                        'motivo'         => 'Reversión de dispensación (Receta #' . $consulta->id . ')',
                        'referencia_tipo'=> 'consulta',
                        'referencia_id'  => $consulta->id,
                    ]);

                    if ($mov->lote_id) {
                        Lote::where('id', $mov->lote_id)->increment('cantidad_disponible', abs($mov->cantidad));
                    }
                }
            } else {
                foreach ($consulta->medicamentos as $med) {
                    $cantidadARevertir = $med->pivot->cantidad ?? 1; 
                    
                    InventarioService::entrada(
                        $med,
                        $cantidadARevertir,
                        'Reversión de dispensación (Ajuste manual)',
                        ['tipo' => 'consulta', 'id' => $consulta->id]
                    );
                }
            }

            // 4. Actualizar la receta a "no dispensada"
            $consulta->update([
                'dispensada' => false,
                'dispensada_en' => null,
                'dispensada_por' => null,
            ]);

            DB::commit();

        
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true, 
                    'message' => 'Dispensación revertida. El stock fue devuelto a sus lotes correspondientes.'
                ]);
            }

            return back()->with('success', 'Dispensación revertida. El stock fue devuelto a sus lotes correspondientes.');
            
        } catch (\Throwable $e) {
            DB::rollBack();
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Error al revertir: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Error al revertir: ' . $e->getMessage());
        }
    }

    /**
 * Historial de dispensaciones (HTML para el modal).
 */
public function historial(Request $request)
{
    $busqueda = $request->input('buscar');

    $query = Consulta::with(['paciente', 'medico', 'dispensadaPor'])
        ->where('dispensada', true)
        ->orderByDesc('dispensada_en');

    if ($busqueda) {
        $query->whereHas('paciente', function ($q) use ($busqueda) {
            $q->where('nombre', 'like', "%{$busqueda}%")
              ->orWhere('apellido_paterno', 'like', "%{$busqueda}%")
              ->orWhere('apellido_materno', 'like', "%{$busqueda}%")
              ->orWhere('curp', 'like', "%{$busqueda}%");
        });
    }

    $historial = $query->limit(50)->get();

    return view('dispensaciones.partials.historial-modal', compact('historial', 'busqueda'));
}
}
