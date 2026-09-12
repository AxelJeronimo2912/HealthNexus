<?php

namespace App\Http\Controllers;

use App\Models\Turno;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TurnoController extends Controller
{
    public function index(): View
    {
        $turnos = Turno::withCount('users')
            ->orderBy('hora_inicio')
            ->paginate(15);

        return view('turnos.index', compact('turnos'));
    }

    public function create(): View
    {
        return view('turnos.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validar($request);

        $data['activo'] = $request->boolean('activo');

        Turno::create($data);

        return redirect()->route('turnos.index')
            ->with('success', 'Turno creado correctamente.');
    }

    public function show(Turno $turno): View
    {
        $turno->load(['users.roles']);

        return view('turnos.show', compact('turno'));
    }

    public function edit(Turno $turno): View
    {
        return view('turnos.edit', compact('turno'));
    }

    public function update(Request $request, Turno $turno): RedirectResponse
    {
        $data = $this->validar($request, $turno->id);

        $data['activo'] = $request->boolean('activo');

        $turno->update($data);

        return redirect()->route('turnos.index')
            ->with('success', 'Turno actualizado correctamente.');
    }

    public function destroy(Turno $turno): RedirectResponse
    {
        if ($turno->users()->count() > 0) {
            return back()->with('error', 'No puedes eliminar un turno con usuarios asignados.');
        }

        $turno->delete();

        return redirect()->route('turnos.index')
            ->with('success', 'Turno eliminado correctamente.');
    }

    private function validar(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:50', Rule::unique('turnos', 'nombre')->ignore($id)],
            'codigo' => ['required', 'string', 'max:20', Rule::unique('turnos', 'codigo')->ignore($id)],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fin' => ['required', 'date_format:H:i'],
            'descripcion' => ['nullable', 'string'],
        ], [
            'nombre.unique' => 'Ya existe un turno con ese nombre.',
            'codigo.unique' => 'Ya existe un turno con ese código.',
        ]);
    }
}