<?php

namespace App\Http\Controllers;

use App\Models\AdministracionMedicamento;
use App\Models\Medicamento;
use App\Models\Paciente;
use App\Services\InventarioService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdministracionMedicamentoController extends Controller
{
    /**
     * Listado de administraciones.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $esAdmin = $user->hasRole('administrador');
        $esEnfermeria = $this->esEnfermeria($user);

        $pacienteId = $request->input('paciente_id');
        $buscar = $request->input('buscar');
        $desde = $request->input('desde');
        $hasta = $request->input('hasta');

        $query = AdministracionMedicamento::with(['paciente', 'medicamento', 'user'])
            ->orderByDesc('administrado_en');

        if ($pacienteId) {
            $query->where('paciente_id', $pacienteId);
        }

        if ($desde) {
            $query->whereDate('administrado_en', '>=', $desde);
        }
        if ($hasta) {
            $query->whereDate('administrado_en', '<=', $hasta);
        }

        if ($buscar) {
            $query->whereHas('paciente', function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('apellido_paterno', 'like', "%{$buscar}%");
            });
        }

        if ($esEnfermeria && !$esAdmin) {
            $query->where('user_id', $user->id);
        }

        $administraciones = $query->paginate(20)->withQueryString();

        $pacientes = Paciente::orderBy('apellido_paterno')->get(['id', 'nombre', 'apellido_paterno', 'apellido_materno']);

        $stats = [
            'total' => AdministracionMedicamento::count(),
            'hoy' => AdministracionMedicamento::whereDate('administrado_en', today())->count(),
            'reacciones' => AdministracionMedicamento::where('reaccion_adversa', true)->count(),
        ];

        return view('enfermeria.administraciones.index', compact(
            'administraciones', 'pacientes', 'pacienteId', 'buscar', 'desde', 'hasta', 'stats'
        ));
    }

    /**
     * Formulario para registrar administración.
     */
    public function create(Request $request): View
    {
        $pacienteId = $request->input('paciente_id');

        $pacientes = Paciente::orderBy('apellido_paterno')->get();
        $medicamentos = Medicamento::where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view('enfermeria.administraciones.create', compact(
            'pacientes', 'medicamentos', 'pacienteId'
        ));
    }

    /**
     * Guarda la administración y descuenta stock.
     */
    public function store(Request $request)    {
        // 1. Validamos los campos de Enfermería
        $request->validate([
            'paciente_id' => ['required', 'exists:pacientes,id'],
            'medicamento_id' => ['required', 'exists:medicamentos,id'],
            'dosis' => ['required', 'string', 'max:50'],
            'via' => ['required', 'string', 'max:50'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
        ]);

        $paciente = Paciente::findOrFail($request->paciente_id);
        $medicamento = Medicamento::findOrFail($request->medicamento_id);

        $cama = \App\Models\CamaPaciente::where('paciente_id', $paciente->id)->where('activa', true)->first();
        if (!$cama && !auth()->user()->hasRole('administrador')) {
            return response()->json(['success' => false, 'message' => 'El paciente no está hospitalizado.'], 400);
        }

        if (!InventarioService::tieneStock($medicamento, 1)) {
            return response()->json(['success' => false, 'message' => "Sin stock suficiente de {$medicamento->nombre}."], 400);
        }

        DB::beginTransaction();

        try {
            // 2. Descontar Stock
            InventarioService::salida(
                $medicamento,
                1,
                'Administración de enfermería: ' . $request->dosis . ' vía ' . $request->via,
                ['tipo' => 'nota_enfermeria', 'id' => $paciente->id]
            );

            // 3. Crear la Nota Clínica (El famoso Seguimiento)
            $nota = \App\Models\Seguimiento::create([
                'paciente_id' => $paciente->id,
                'user_id' => auth()->id(),
                'cama_id' => $cama?->cama_id,
                'tipo' => 'nota_enfermeria', 
                'contenido' => "Se administró {$medicamento->nombre} ({$medicamento->concentracion}). Dosis: {$request->dosis}. Vía: {$request->via}. " . ($request->observaciones ?? ''),
                'estado_paciente' => 'estable',
                'temperatura' => null,
                'frecuencia_cardiaca' => null,
                'frecuencia_respiratoria' => null,
                'presion_arterial' => null,
                'saturacion_oxigeno' => null,
            ]);

            // 4. Guardar en la tabla de administraciones
            \App\Models\AdministracionMedicamento::create([
                'paciente_id' => $paciente->id,
                'medicamento_id' => $medicamento->id,
                'user_id' => auth()->id(),
                'consulta_id' => null,
                'seguimiento_id' => $nota->id,
                'dosis' => $request->dosis,
                'via' => $request->via,
                'administrado_en' => now(),
                'reaccion_adversa' => false,
                'observaciones' => $request->observaciones,
            ]);

            DB::commit();

            // 5. Si es una petición AJAX, devolvemos JSON para que el JS sepa qué hacer
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Medicamento administrado.',
                    'redirect' => route('enfermeria.notas.show', $nota) 
                ]);
            }

            return redirect()->route('enfermeria.notas.show', $nota)->with('success', 'Medicamento administrado.');

        } catch (\Throwable $e) {
            DB::rollBack();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
    /**
     * Detalle.
     */
    public function show(AdministracionMedicamento $administracion): View
    {
        $administracion->load(['paciente', 'medicamento', 'user']);

        return view('enfermeria.administraciones.show', compact('administracion'));
    }

    /**
     * Eliminar (devuelve stock).
     */
    public function destroy(AdministracionMedicamento $administracion): RedirectResponse
    {
        if (!auth()->user()->hasRole('administrador')) {
            abort(403, 'Solo el administrador puede eliminar administraciones.');
        }

        DB::beginTransaction();

        try {
            // Devolver stock
            $med = $administracion->medicamento;
            if ($med) {
                InventarioService::entrada(
                    $med,
                    1,
                    'Reversión de administración #' . $administracion->id,
                    ['tipo' => 'administracion', 'id' => $administracion->id]
                );
            }

            $administracion->delete();

            DB::commit();

            return redirect()->route('enfermeria.administraciones.index')
                ->with('success', 'Administración eliminada. Stock devuelto.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    private function esEnfermeria($user): bool
    {
        return $user->roles->contains(fn($rol) => str_contains(strtolower($rol->name), 'enfermer'));
    }
}


