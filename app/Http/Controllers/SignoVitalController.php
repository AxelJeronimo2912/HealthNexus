<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSignoVitalRequest;
use App\Http\Requests\UpdateSignoVitalRequest;
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
            'pacientes'            => Paciente::orderBy('apellido_paterno')->get(),
            'pacienteSeleccionado' => $pacienteSeleccionado,
        ]);
    }

    public function store(StoreSignoVitalRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Calcular triage automático si no se marcó manual
        if (!$request->boolean('triage_manual') || empty($data['triage'])) {
            $data['triage']        = TriageService::calcular($data);
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
        $signoVital->load('paciente');

        abort_if(
            !$signoVital->paciente,
            404,
            'El paciente asociado a este registro ya no existe.'
        );

        return view('signos-vitales.edit', [
            'registro'  => $signoVital,
            'pacientes' => Paciente::orderBy('apellido_paterno')->get(),
        ]);
    }

    public function update(UpdateSignoVitalRequest $request, SignoVital $signoVital): RedirectResponse
    {
        $data = $request->validated();

        if (!$request->boolean('triage_manual') || empty($data['triage'])) {
            $data['triage']        = TriageService::calcular($data);
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

        CamaPaciente::create([
            'cama_id'         => $cama->id,
            'paciente_id'     => $signoVital->paciente_id,
            'signo_vital_id'  => $signoVital->id,
            'user_id'         => auth()->id(),
            'fecha_ingreso'   => now(),
            'motivo'          => 'Triage ' . $signoVital->triage,
            'activa'          => true,
        ]);

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
            'activa'       => false,
            'fecha_egreso' => now(),
        ]);

        $asignacion->cama->update(['estado' => 'disponible']);

        return redirect()->route('signos-vitales.show', $signoVital)
            ->with('success', 'Cama liberada correctamente.');
    }
}