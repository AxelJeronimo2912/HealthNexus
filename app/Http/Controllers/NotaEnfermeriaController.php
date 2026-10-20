<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\Seguimiento;
use App\Models\CamaPaciente;
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

        if ($pacienteId) {
            $query->where('paciente_id', $pacienteId);
        }

        if ($desde) {
            $query->whereDate('created_at', '>=', $desde);
        }
        if ($hasta) {
            $query->whereDate('created_at', '<=', $hasta);
        }

        if ($buscar) {
            $query->whereHas('paciente', function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('apellido_paterno', 'like', "%{$buscar}%")
                  ->orWhere('apellido_materno', 'like', "%{$buscar}%");
            });
        }

        if ($esEnfermeria && !$esAdmin) {
            $query->where('user_id', $user->id);
        }

        $notas = $query->paginate(20)->withQueryString();

        $pacientes = Paciente::orderBy('apellido_paterno')->get(['id', 'nombre', 'apellido_paterno', 'apellido_materno']);

        $stats = [
            'total' => Seguimiento::where('tipo', 'nota_enfermeria')->count(),
            'hoy' => Seguimiento::where('tipo', 'nota_enfermeria')->whereDate('created_at', today())->count(),
            'esta_semana' => Seguimiento::where('tipo', 'nota_enfermeria')
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
        ];

        return view('enfermeria.notas.index', compact('notas', 'pacientes', 'pacienteId', 'desde', 'hasta', 'buscar', 'stats'));
    }

    /**
     * Formulario para crear una nota (Vía Modal AJAX).
     */
    public function create(Request $request) 
    {
        $pacienteId = $request->input('paciente_id');
        $paciente = $pacienteId ? Paciente::find($pacienteId) : null;

        $pacientes = Paciente::orderBy('apellido_paterno')->get();

        if ($request->ajax()) {
            return view('enfermeria.notas.create', compact('pacientes', 'paciente'))->render();
        }

        return view('enfermeria.notas.create', compact('pacientes', 'paciente'));
    }

    /**
     * Guarda la nota en la base de datos.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'paciente_id' => ['required', 'exists:pacientes,id'],
        ]);

        $paciente = Paciente::findOrFail($request->paciente_id);

        $cama = CamaPaciente::where('paciente_id', $paciente->id)
            ->where('activa', true)
            ->first();

        if (!$cama && !auth()->user()->hasRole('administrador')) {
            return back()->withErrors(['paciente_id' => 'El paciente no tiene una cama activa. No puedes registrar una nota de enfermería a un paciente que no está hospitalizado.'])->withInput();
        }

        $data = $request->validate([
            'contenido' => ['required', 'string', 'min:10', 'max:5000'],
            'estado_paciente' => ['nullable', 'in:estable,mejorando,grave,critico'],
            
            'temperatura' => ['required', 'numeric', 'min:34', 'max:42'],
            'frecuencia_cardiaca' => ['required', 'integer', 'min:30', 'max:200'],
            'frecuencia_respiratoria' => ['required', 'integer', 'min:8', 'max:60'],
            'presion_arterial' => ['required', 'string', 'regex:/^\d{2,3}\/\d{2,3}$/'],
            'saturacion_oxigeno' => ['required', 'integer', 'min:50', 'max:100'],
        ], [
            'contenido.required' => 'El contenido de la nota es obligatorio.',
            'contenido.min' => 'La nota debe ser más detallada (mínimo 10 caracteres).',
            
            'temperatura.required' => 'La temperatura es obligatoria.',
            'temperatura.min' => 'La temperatura no puede ser menor a 34°C.',
            'temperatura.max' => 'La temperatura no puede ser mayor a 42°C.',
            
            'frecuencia_cardiaca.required' => 'La frecuencia cardíaca es obligatoria.',
            'frecuencia_cardiaca.min' => 'La frecuencia cardíaca parece demasiado baja.',
            'frecuencia_cardiaca.max' => 'La frecuencia cardíaca parece demasiado alta.',
            
            'frecuencia_respiratoria.required' => 'La frecuencia respiratoria es obligatoria.',
            'frecuencia_respiratoria.min' => 'La frecuencia respiratoria parece demasiado baja.',
            'frecuencia_respiratoria.max' => 'La frecuencia respiratoria parece demasiado alta.',
            
            'presion_arterial.required' => 'La presión arterial es obligatoria.',
            'presion_arterial.regex' => 'El formato de la presión arterial debe ser similar a 120/80.',
            
            'saturacion_oxigeno.required' => 'La saturación de oxígeno es obligatoria.',
            'saturacion_oxigeno.max' => 'La saturación no puede superar el 100%.',
            'saturacion_oxigeno.min' => 'La saturación de oxígeno es peligrosamente baja (mínimo 50%).',
        ]);

        if (empty($data['estado_paciente'])) {
            $data['estado_paciente'] = $this->calcularEstado(
                $data['temperatura'],
                $data['frecuencia_cardiaca'],
                $data['frecuencia_respiratoria'],
                $data['saturacion_oxigeno']
            );
        }

        Seguimiento::create([
            'paciente_id' => $paciente->id,
            'user_id' => auth()->id(),
            'cama_id' => $cama?->cama_id,
            'tipo' => 'nota_enfermeria',
            'estado_paciente' => $data['estado_paciente'],
            'contenido' => $data['contenido'],
            'temperatura' => $data['temperatura'],
            'frecuencia_cardiaca' => $data['frecuencia_cardiaca'],
            'frecuencia_respiratoria' => $data['frecuencia_respiratoria'],
            'presion_arterial' => $data['presion_arterial'],
            'saturacion_oxigeno' => $data['saturacion_oxigeno'],
        ]);

        return redirect()->route('enfermeria.notas.index')
            ->with('success', 'Nota de enfermería registrada correctamente.');
    }

    /**
     * Ver una nota (Vía Modal AJAX).
     */
     public function show(Request $request, Seguimiento $nota)
    {
        if ($nota->tipo !== 'nota_enfermeria') {
            abort(404);
        }

        if ($this->esEnfermeria(auth()->user()) && $nota->user_id !== auth()->id() && !auth()->user()->hasRole('administrador')) {
            abort(403, 'No tienes permiso para ver esta nota.');
        }

        $nota->load(['paciente', 'user', 'cama']);

        if ($request->ajax()) {
            return view('enfermeria.notas.show', compact('nota'))->render();
        }

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

        if ($nota->user_id !== auth()->id() && !auth()->user()->hasRole('administrador')) {
            abort(403, 'No puedes eliminar notas de otros usuarios.');
        }

        $nota->delete();

        return redirect()->route('enfermeria.notas.index')
            ->with('success', 'Nota eliminada.');
    }

    /**
     * Calcula el estado del paciente basado en los signos vitales.
     */
    private function calcularEstado($temp, $fc, $fr, $spo2): string
    {
        if ($spo2 < 90 || $temp >= 39 || $fc > 120 || $fr > 30) {
            return 'critico';
        }
        
        if ($temp >= 37.5 || $fc > 100 || $fr > 20 || $spo2 < 94) {
            return 'grave';
        }
        
        if ($temp >= 37.2) {
            return 'mejorando';
        }

        return 'estable';
    }

    private function esEnfermeria($user): bool
    {
        return $user->roles->contains(function ($rol) {
            return str_contains(strtolower($rol->name), 'enfermer');
        });
    }
}
