<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\Seguimiento;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotaEnfermeriaController extends Controller
{
    /**
     * Listado de notas de enfermería.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $esAdmin = $user->hasRole('administrador');
        $esEnfermeria = $this->esEnfermeria($user);

        $pacienteId = $request->input('paciente_id');
        $desde = $request->input('desde');
        $hasta = $request->input('hasta');
        $buscar = $request->input('buscar');

        $query = Seguimiento::with(['paciente', 'user', 'cama'])
            ->where('tipo', 'nota_enfermeria')
            ->orderByDesc('created_at');

        // Filtro por paciente
        if ($pacienteId) {
            $query->where('paciente_id', $pacienteId);
        }

        // Filtro por rango de fechas
        if ($desde) {
            $query->whereDate('created_at', '>=', $desde);
        }
        if ($hasta) {
            $query->whereDate('created_at', '<=', $hasta);
        }

        // Búsqueda por nombre de paciente
        if ($buscar) {
            $query->whereHas('paciente', function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('apellido_paterno', 'like', "%{$buscar}%")
                  ->orWhere('apellido_materno', 'like', "%{$buscar}%");
            });
        }

        // 🔒 Si es enfermería, solo ve sus propias notas
        if ($esEnfermeria && !$esAdmin) {
            $query->where('user_id', $user->id);
        }

        $notas = $query->paginate(20)->withQueryString();

        // Pacientes para el filtro
        $pacientes = Paciente::orderBy('apellido_paterno')->get(['id', 'nombre', 'apellido_paterno', 'apellido_materno']);

        // Stats
        $stats = [
            'total' => Seguimiento::where('tipo', 'nota_enfermeria')->count(),
            'hoy' => Seguimiento::where('tipo', 'nota_enfermeria')->whereDate('created_at', today())->count(),
            'esta_semana' => Seguimiento::where('tipo', 'nota_enfermeria')
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
        ];

        return view('enfermeria.notas.index', compact(
            'notas', 'pacientes', 'pacienteId', 'desde', 'hasta', 'buscar', 'stats'
        ));
    }

    /**
     * Formulario para crear una nota.
     */
    public function create(Request $request): View
    {
        $pacienteId = $request->input('paciente_id');
        $paciente = $pacienteId ? Paciente::find($pacienteId) : null;

        $pacientes = Paciente::orderBy('apellido_paterno')->get();

        return view('enfermeria.notas.create', compact('pacientes', 'paciente'));
    }

    /**
     * Guarda la nota.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'paciente_id' => ['required', 'exists:pacientes,id'],
            'contenido' => ['required', 'string'],
            'estado_paciente' => ['nullable', 'in:estable,mejorando,grave,critico'],
            'temperatura' => ['nullable', 'numeric', 'min:30', 'max:45'],
            'frecuencia_cardiaca' => ['nullable', 'integer', 'min:20', 'max:250'],
            'frecuencia_respiratoria' => ['nullable', 'integer', 'min:5', 'max:80'],
            'presion_arterial' => ['nullable', 'string', 'max:20'],
            'saturacion_oxigeno' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        // Cama actual si la tiene
        $cama = \App\Models\CamaPaciente::where('paciente_id', $data['paciente_id'])
            ->where('activa', true)
            ->first();

        Seguimiento::create([
            'paciente_id' => $data['paciente_id'],
            'user_id' => auth()->id(),
            'cama_id' => $cama?->cama_id,
            'tipo' => 'nota_enfermeria',
            'estado_paciente' => $data['estado_paciente'] ?? null,
            'contenido' => $data['contenido'],
            'temperatura' => $data['temperatura'] ?? null,
            'frecuencia_cardiaca' => $data['frecuencia_cardiaca'] ?? null,
            'frecuencia_respiratoria' => $data['frecuencia_respiratoria'] ?? null,
            'presion_arterial' => $data['presion_arterial'] ?? null,
            'saturacion_oxigeno' => $data['saturacion_oxigeno'] ?? null,
        ]);

        return redirect()->route('enfermeria.notas.index')
            ->with('success', 'Nota de enfermería registrada.');
    }

    /**
     * Ver una nota.
     */
    public function show(Seguimiento $nota): View
    {
        // Verificar que sea una nota de enfermería
        if ($nota->tipo !== 'nota_enfermeria') {
            abort(404);
        }

        $nota->load(['paciente', 'user', 'cama']);

        return view('enfermeria.notas.show', compact('nota'));
    }

    /**
     * Eliminar una nota.
     */
    public function destroy(Seguimiento $nota): RedirectResponse
    {
        if ($nota->tipo !== 'nota_enfermeria') {
            abort(404);
        }

        // Solo el autor o admin pueden eliminar
        if ($nota->user_id !== auth()->id() && !auth()->user()->hasRole('administrador')) {
            abort(403, 'No puedes eliminar notas de otros usuarios.');
        }

        $nota->delete();

        return redirect()->route('enfermeria.notas.index')
            ->with('success', 'Nota eliminada.');
    }

    private function esEnfermeria($user): bool
    {
        return $user->roles->contains(function ($rol) {
            return str_contains(strtolower($rol->name), 'enfermer');
        });
    }
}