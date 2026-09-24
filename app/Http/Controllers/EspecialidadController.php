<?php

namespace App\Http\Controllers;

use App\Models\Especialidad;
use App\Models\Servicio;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EspecialidadController extends Controller
{
    public function index(Request $request): View
    {
        $busqueda = $request->input('buscar');
        $grupo = $request->input('grupo');

        $especialidades = Especialidad::query()
            ->withCount(['medicos', 'servicios', 'citas', 'consultas'])
            ->when($busqueda, function ($q) use ($busqueda) {
                $q->where('nombre', 'like', "%{$busqueda}%")
                  ->orWhere('codigo', 'like', "%{$busqueda}%");
            })
            ->when($grupo, fn($q) => $q->where('grupo', $grupo))
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => Especialidad::count(),
            'activas' => Especialidad::where('activo', true)->count(),
            'grupos' => Especialidad::distinct('grupo')->count('grupo'),
        ];

        return view('especialidades.index', compact('especialidades', 'busqueda', 'grupo', 'stats'));
    }

    public function create(): View
    {
        return view('especialidades.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validar($request);
        $data['activo'] = $request->boolean('activo');

        Especialidad::create($data);

        return redirect()->route('especialidades.index')
            ->with('success', 'Especialidad registrada correctamente.');
    }

    public function show(Especialidad $especialidad): View
    {
        $especialidad->load(['medicos', 'servicios']);

        $stats = [
            'citas' => $especialidad->citas()->count(),
            'consultas' => $especialidad->consultas()->count(),
        ];

        return view('especialidades.show', compact('especialidad', 'stats'));
    }

    public function edit(Especialidad $especialidad): View
    {
        return view('especialidades.edit', compact('especialidad'));
    }

    public function update(Request $request, Especialidad $especialidad): RedirectResponse
    {
        $data = $this->validar($request, $especialidad->id);
        $data['activo'] = $request->boolean('activo');

        $especialidad->update($data);

        return redirect()->route('especialidades.index')
            ->with('success', 'Especialidad actualizada correctamente.');
    }

    public function destroy(Especialidad $especialidad): RedirectResponse
    {
        if ($especialidad->medicos()->count() > 0) {
            return back()->with('error', 'No puedes eliminar una especialidad con médicos asignados.');
        }

        if ($especialidad->citas()->count() > 0 || $especialidad->consultas()->count() > 0) {
            return back()->with('error', 'No puedes eliminar una especialidad con citas o consultas registradas.');
        }

        $especialidad->delete();

        return redirect()->route('especialidades.index')
            ->with('success', 'Especialidad eliminada correctamente.');
    }

    /**
     * Asignar / quitar médicos de la especialidad.
     */
    public function medicos(Especialidad $especialidad): View
    {
        $especialidad->load('medicos');

        $asignados = $especialidad->medicos->pluck('id')->toArray();

        $disponibles = User::whereNotIn('id', $asignados)
            ->where('activo', true)
            ->whereHas('roles', function ($q) {
                $q->whereRaw('LOWER(name) LIKE ?', ['%medic%'])
                  ->orWhereRaw('LOWER(name) LIKE ?', ['%doctor%']);
            })
            ->orderBy('nombre')
            ->get();

        return view('especialidades.medicos', compact('especialidad', 'disponibles'));
    }

    public function asignarMedico(Request $request, Especialidad $especialidad): RedirectResponse
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'es_principal' => ['boolean'],
            'numero_cedula_especialidad' => ['nullable', 'string', 'max:50'],
            'fecha_certificacion' => ['nullable', 'date'],
        ]);

        // Evitar duplicado
        if ($especialidad->medicos()->wherePivot('user_id', $data['user_id'])->exists()) {
            return back()->with('error', 'Ese médico ya está asignado a esta especialidad.');
        }

        // Si es principal, quitar el "principal" de otras del mismo médico
        if (!empty($data['es_principal'])) {
            \DB::table('especialidad_user')
                ->where('user_id', $data['user_id'])
                ->update(['es_principal' => false]);
        }

        $especialidad->medicos()->attach($data['user_id'], [
            'es_principal' => !empty($data['es_principal']),
            'numero_cedula_especialidad' => $data['numero_cedula_especialidad'] ?? null,
            'fecha_certificacion' => $data['fecha_certificacion'] ?? null,
            'activo' => true,
        ]);

        return back()->with('success', 'Médico asignado correctamente.');
    }

    public function quitarMedico(Especialidad $especialidad, $pivotId): RedirectResponse
    {
        $especialidad->medicos()->newPivotStatement()
            ->where('id', $pivotId)
            ->where('especialidad_id', $especialidad->id)
            ->delete();

        return back()->with('success', 'Médico removido de la especialidad.');
    }

    /**
     * Asignar / quitar servicios de la especialidad.
     */
    public function servicios(Especialidad $especialidad): View
    {
        $especialidad->load('servicios');

        $asignados = $especialidad->servicios->pluck('id')->toArray();

        $disponibles = Servicio::whereNotIn('id', $asignados)
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view('especialidades.servicios', compact('especialidad', 'disponibles'));
    }

    public function asignarServicio(Request $request, Especialidad $especialidad): RedirectResponse
    {
        $data = $request->validate([
            'servicio_id' => ['required', 'exists:servicios,id'],
        ]);

        if ($especialidad->servicios()->wherePivot('servicio_id', $data['servicio_id'])->exists()) {
            return back()->with('error', 'Ese servicio ya está asignado.');
        }

        $especialidad->servicios()->attach($data['servicio_id'], ['activo' => true]);

        return back()->with('success', 'Servicio asignado correctamente.');
    }

    public function quitarServicio(Especialidad $especialidad, $pivotId): RedirectResponse
    {
        $especialidad->servicios()->newPivotStatement()
            ->where('id', $pivotId)
            ->where('especialidad_id', $especialidad->id)
            ->delete();

        return back()->with('success', 'Servicio removido de la especialidad.');
    }

    private function validar(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'codigo' => ['required', 'string', 'max:20', Rule::unique('especialidades', 'codigo')->ignore($id)],
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string'],
            'grupo' => ['required', 'in:clinica,quirurgica,diagnostica,basica,otra'],
            'duracion_consulta_default' => ['required', 'integer', 'min:5', 'max:240'],
            'color' => ['nullable', 'string', 'max:7'],
        ], [
            'codigo.unique' => 'Ya existe una especialidad con ese código.',
        ]);
    }
}