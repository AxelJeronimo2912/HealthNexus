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
   public function store(Request $request): RedirectResponse
{
    $data = $request->validate([
        'paciente_id' => ['required', 'exists:pacientes,id'],
        'tipo' => ['required', 'in:urgencias,consulta_externa,hospitalizacion,traslado'],
        'triage' => ['nullable', 'in:rojo,naranja,amarillo,verde,azul'],
        'motivo' => ['required', 'string'],
        'diagnostico_presuntivo' => ['nullable', 'string'],
        'notas' => ['nullable', 'string'],
    ]);

    $paciente = Paciente::findOrFail($data['paciente_id']);
    $ultimoSigno = $paciente->signosVitales()->orderByDesc('created_at')->first();

    // Triage: si el usuario no eligió uno, usa el del último signo vital
    $triageFinal = $data['triage'] ?? $ultimoSigno?->triage;

    $folio = 'ADM-' . now()->format('Ymd') . '-' . str_pad(
        Admision::whereDate('created_at', today())->count() + 1,
        3, '0', STR_PAD_LEFT
    );

    $admision = Admision::create([
        'paciente_id' => $paciente->id,
        'user_id' => auth()->id(),
        'folio' => $folio,
        'fecha_hora_llegada' => now(),
        'tipo' => $data['tipo'],
        'triage' => $triageFinal,
        'motivo' => $data['motivo'],
        'diagnostico_presuntivo' => $data['diagnostico_presuntivo'] ?? null,
        'estado' => 'en_espera',
        'notas' => $data['notas'] ?? null,
    ]);

    return redirect()->route('admisiones.show', $admision)
        ->with('success', 'Paciente admitido. Folio: ' . $folio);
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