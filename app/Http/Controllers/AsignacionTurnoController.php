<?php

namespace App\Http\Controllers;

use App\Models\Turno;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AsignacionTurnoController extends Controller
{
    /**
     * Lista los turnos asignados a un usuario.
     */
    public function index(User $user): View
    {
        $asignaciones = $user->turnos()
            ->orderBy('hora_inicio')
            ->get();

        return view('turnos.asignaciones.index', compact('user', 'asignaciones'));
    }

    /**
     * Formulario para asignar un turno a un usuario.
     */
    public function create(User $user): View
    {
        $turnos = Turno::where('activo', true)->orderBy('hora_inicio')->get();

        return view('turnos.asignaciones.create', compact('user', 'turnos'));
    }

    /**
     * Guarda una nueva asignación.
     */
    public function store(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'turno_id' => ['required', 'exists:turnos,id'],
            'dia_semana' => ['nullable', 'integer', 'between:0,6'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'area' => ['nullable', 'string', 'max:100'],
            'notas' => ['nullable', 'string'],
        ]);

        // Evitar duplicado exacto
        $existe = $user->turnos()
            ->wherePivot('turno_id', $data['turno_id'])
            ->wherePivot('dia_semana', $data['dia_semana'] ?? null)
            ->wherePivot('fecha_inicio', $data['fecha_inicio'])
            ->wherePivot('activo', true)
            ->exists();

        if ($existe) {
            return back()->with('error', 'Ese turno ya está asignado al usuario en esa fecha.')
                ->withInput();
        }

        $user->turnos()->attach($data['turno_id'], [
            'dia_semana' => $data['dia_semana'] ?? null,
            'fecha_inicio' => $data['fecha_inicio'],
            'fecha_fin' => $data['fecha_fin'] ?? null,
            'area' => $data['area'] ?? null,
            'notas' => $data['notas'] ?? null,
            'activo' => true,
        ]);

        return redirect()->route('admin.users.turnos.index', $user)
            ->with('success', 'Turno asignado correctamente.');
    }

    /**
     * Elimina una asignación específica.
     */
   public function destroy(User $user, $pivotId): RedirectResponse
{
    $user->turnos()->newPivotStatement()
        ->where('id', $pivotId)
        ->where('user_id', $user->id)
        ->delete();

    return redirect()->route('admin.users.turnos.index', $user)
        ->with('success', 'Asignación eliminada correctamente.');
}

    /**
     * Alterna el estado activo/inactivo de una asignación.
     */
    public function toggle(User $user, $pivotId): RedirectResponse
    {
        $pivote = $user->turnos()->wherePivot('id', $pivotId)->first();

        if (!$pivote) {
            return back()->with('error', 'Asignación no encontrada.');
        }

        $user->turnos()->updateExistingPivot($pivote->id, [
            'activo' => !$pivote->pivot->activo,
        ]);

        return back()->with('success', 'Estado actualizado.');
    }
}