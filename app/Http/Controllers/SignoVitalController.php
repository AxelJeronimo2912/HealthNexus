<?php

namespace App\Http\Controllers;

use App\Models\Cama;
use App\Models\CamaPaciente;
use App\Models\Paciente;
use App\Models\SignoVital;
use App\Services\TriageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SignoVitalController extends Controller
{
    public function index(Request $request): View
    {
        $busqueda = $request->input('buscar');
        $filtroTriage = $request->input('triage');

        $registros = SignoVital::with(['paciente', 'user'])
            ->when($busqueda, function ($q) use ($busqueda) {
                $q->whereHas('paciente', function ($sub) use ($busqueda) {
                    $sub->where('nombre', 'like', "%{$busqueda}%")
                        ->orWhere('apellido_paterno', 'like', "%{$busqueda}%")
                        ->orWhere('apellido_materno', 'like', "%{$busqueda}%");
                });
            })
            ->when($filtroTriage, fn($q) => $q->where('triage', $filtroTriage))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('signos-vitales.index', compact('registros', 'busqueda', 'filtroTriage'));
    }

    public function create(Request $request): View
    {
        $pacienteSeleccionado = null;
        if ($request->filled('paciente_id')) {
            $pacienteSeleccionado = Paciente::find($request->paciente_id);
        }

        return view('signos-vitales.create', [
            'pacientes' => Paciente::orderBy('apellido_paterno')->get(),
            'pacienteSeleccionado' => $pacienteSeleccionado,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validar($request);

        // Calcular triage automático si no se marcó manual
        if (!$request->boolean('triage_manual') || empty($data['triage'])) {
            $data['triage'] = TriageService::calcular($data);
            $data['triage_manual'] = false;
        }

        $data['user_id'] = auth()->id();

        $registro = SignoVital::create($data);

        // Si el triage es rojo o naranja → ofrecer asignar cama
        if (in_array($registro->triage, ['rojo', 'naranja'])) {
            return redirect()->route('signos-vitales.show', $registro)
                ->with('sugerir_cama', true)
                ->with('success', 'Signos vitales registrados. Se recomienda asignar cama.');
        }

        return redirect()->route('signos-vitales.show', $registro)
            ->with('success', 'Signos vitales registrados correctamente.');
    }

    public function show($id): View
{
    $signoVital = SignoVital::with(['paciente', 'user', 'asignaciones.cama'])->findOrFail($id);

    $camasDisponibles = Cama::where('estado', 'disponible')
        ->where('activo', true)
        ->orderBy('area')
        ->orderBy('codigo')
        ->get();

    return view('signos-vitales.show', compact('signoVital', 'camasDisponibles'));
}

    public function edit(SignoVital $signoVital): View
    {
        return view('signos-vitales.edit', [
            'registro' => $signoVital,
            'pacientes' => Paciente::orderBy('apellido_paterno')->get(),
        ]);
    }

    public function update(Request $request, SignoVital $signoVital): RedirectResponse
    {
        $data = $this->validar($request);

        if (!$request->boolean('triage_manual') || empty($data['triage'])) {
            $data['triage'] = TriageService::calcular($data);
            $data['triage_manual'] = false;
        }

        $signoVital->update($data);

        return redirect()->route('signos-vitales.show', $signoVital)
            ->with('success', 'Registro actualizado correctamente.');
    }

    public function destroy(SignoVital $signoVital): RedirectResponse
    {
        $signoVital->delete();

        return redirect()->route('signos-vitales.index')
            ->with('success', 'Registro eliminado correctamente.');
    }

    /**
     * Asigna una cama a un paciente desde el módulo de signos vitales.
     */
    public function asignarCama(Request $request, SignoVital $signoVital): RedirectResponse
    {
        $request->validate([
            'cama_id' => ['required', 'exists:camas,id'],
        ]);

        $cama = Cama::findOrFail($request->cama_id);

        if ($cama->estado !== 'disponible') {
            return back()->with('error', 'La cama seleccionada ya no está disponible.');
        }

        // Crear asignación
        CamaPaciente::create([
            'cama_id' => $cama->id,
            'paciente_id' => $signoVital->paciente_id,
            'signo_vital_id' => $signoVital->id,
            'user_id' => auth()->id(),
            'fecha_ingreso' => now(),
            'motivo' => 'Triage ' . $signoVital->triage,
            'activa' => true,
        ]);

        // Actualizar estado de la cama
        $cama->update(['estado' => 'ocupada']);

        return redirect()->route('signos-vitales.show', $signoVital)
            ->with('success', 'Paciente asignado a la cama ' . $cama->codigo);
    }

    /**
     * Libera la cama asociada al signo vital.
     */
    public function liberarCama(SignoVital $signoVital): RedirectResponse
    {
        $asignacion = $signoVital->asignaciones()->where('activa', true)->first();

        if (!$asignacion) {
            return back()->with('error', 'No hay cama asignada.');
        }

        $asignacion->update([
            'activa' => false,
            'fecha_egreso' => now(),
        ]);

        $asignacion->cama->update(['estado' => 'disponible']);

        return redirect()->route('signos-vitales.show', $signoVital)
            ->with('success', 'Cama liberada correctamente.');
    }

    private function validar(Request $request): array
    {
        return $request->validate([
            'paciente_id' => ['required', 'exists:pacientes,id'],
            'temperatura' => ['nullable', 'numeric', 'min:30', 'max:45'],
            'frecuencia_cardiaca' => ['nullable', 'integer', 'min:20', 'max:250'],
            'frecuencia_respiratoria' => ['nullable', 'integer', 'min:5', 'max:80'],
            'presion_arterial' => ['nullable', 'string', 'max:20'],
            'saturacion_oxigeno' => ['nullable', 'integer', 'min:0', 'max:100'],
            'glucosa' => ['nullable', 'integer', 'min:0', 'max:1000'],
            'peso' => ['nullable', 'numeric', 'min:0.5', 'max:400'],
            'talla' => ['nullable', 'numeric', 'min:0.3', 'max:2.5'],
            'escala_dolor' => ['nullable', 'integer', 'min:0', 'max:10'],
            'triage' => ['nullable', 'in:rojo,naranja,amarillo,verde,azul'],
            'triage_manual' => ['boolean'],
            'motivo_consulta' => ['nullable', 'string'],
            'notas' => ['nullable', 'string'],
        ], [
            'paciente_id.required' => 'Debes seleccionar un paciente.',
            'temperatura.min' => 'La temperatura debe ser al menos 30 °C.',
            'temperatura.max' => 'La temperatura no puede superar 45 °C.',
            'saturacion_oxigeno.max' => 'La saturación no puede superar 100%.',
        ]);
    }
}