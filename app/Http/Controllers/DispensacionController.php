<?php

namespace App\Http\Controllers;

use App\Models\Consulta;
use App\Services\InventarioService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DispensacionController extends Controller
{
    /**
     * Lista de recetas pendientes de dispensar.
     */
    public function index(Request $request): View
    {
        $busqueda = $request->input('buscar');
        $filtro = $request->input('filtro', 'pendientes'); // pendientes, dispensadas, todas

        $query = Consulta::with(['paciente', 'medico', 'medicamentos', 'dispensadaPor'])
            ->where('estado', 'finalizada')   // solo consultas finalizadas
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
    public function show(Consulta $consulta): View
    {
        $consulta->load(['paciente', 'medico', 'medicamentos', 'dispensadaPor', 'cita']);

        return view('dispensaciones.show', compact('consulta'));
    }

    /**
     * Dispensar la receta: descuenta stock de cada medicamento.
     */
    public function dispensar(Request $request, Consulta $consulta): RedirectResponse
    {
        if ($consulta->dispensada) {
            return back()->with('error', 'Esta receta ya fue dispensada.');
        }

        if ($consulta->medicamentos()->count() === 0 && empty($consulta->receta_libre)) {
            return back()->with('error', 'Esta consulta no tiene receta.');
        }

        $request->validate([
            'notas_dispensacion' => ['nullable', 'string', 'max:500'],
        ]);

        DB::beginTransaction();

        try {
            // Descontar stock de cada medicamento recetado
            foreach ($consulta->medicamentos as $med) {
                $cantidad = 1; // o el campo cantidad si lo agregas después

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

            // Marcar la consulta como dispensada
            $consulta->update([
                'dispensada' => true,
                'dispensada_en' => now(),
                'dispensada_por' => auth()->id(),
                'notas_dispensacion' => $request->notas_dispensacion,
            ]);

            DB::commit();

            return redirect()->route('dispensaciones.show', $consulta)
                ->with('success', 'Receta dispensada correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Error al dispensar: ' . $e->getMessage());
        }
    }

    /**
     * Revertir una dispensación (solo admin).
     */
    public function revertir(Consulta $consulta): RedirectResponse
    {
        if (!auth()->user()->hasRole('administrador')) {
            abort(403, 'Solo el administrador puede revertir dispensaciones.');
        }

        if (!$consulta->dispensada) {
            return back()->with('error', 'Esta receta no ha sido dispensada.');
        }

        DB::beginTransaction();

        try {
            // Devolver stock
            foreach ($consulta->medicamentos as $med) {
                InventarioService::entrada(
                    $med,
                    1,
                    'Reversión de dispensación',
                    ['tipo' => 'consulta', 'id' => $consulta->id]
                );
            }

            $consulta->update([
                'dispensada' => false,
                'dispensada_en' => null,
                'dispensada_por' => null,
            ]);

            DB::commit();

            return back()->with('success', 'Dispensación revertida. El stock fue devuelto.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Error al revertir: ' . $e->getMessage());
        }
    }
}