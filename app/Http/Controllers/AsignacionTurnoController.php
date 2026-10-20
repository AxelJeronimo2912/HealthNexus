<?php

namespace App\Http\Controllers;

use App\Models\Turno;
use App\Models\User;
use Carbon\Carbon;
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

        $turnos = Turno::where('activo', true)
            ->orderBy('hora_inicio')
            ->get();

        return view('turnos.asignaciones.index', compact('user', 'asignaciones', 'turnos'));
    }

    /**
     * Formulario para asignar un turno a un usuario.
     */
    public function create(User $user): View
    {
        $turnos = Turno::where('activo', true)
            ->orderBy('hora_inicio')
            ->get();

        return view('turnos.asignaciones.create', compact('user', 'turnos'));
    }

    /**
     * Guarda una nueva asignación.
     */
    public function store(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'turno_id'     => ['required', 'exists:turnos,id'],
            'dia_semana'   => ['nullable', 'integer', 'between:0,6'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin'    => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'area'         => ['nullable', 'string', 'max:100'],
            'notas'        => ['nullable', 'string'],
        ]);

        // ── 1. Evitar duplicado exacto ──
        $existe = $user->turnos()
            ->wherePivot('turno_id', $data['turno_id'])
            ->wherePivot('dia_semana', $data['dia_semana'] ?? null)
            ->wherePivot('fecha_inicio', $data['fecha_inicio'])
            ->wherePivot('activo', true)
            ->exists();

        if ($existe) {
            return back()
                ->with('error', 'Ese turno ya está asignado al usuario en esa fecha.')
                ->withInput();
        }

        // ── 2. Evitar solapamiento de horario ──
        $conflicto = $this->buscarConflicto($user, $data);

        if ($conflicto) {
            return back()
                ->with('error', $conflicto)
                ->withInput();
        }

        $user->turnos()->attach($data['turno_id'], [
            'dia_semana'   => $data['dia_semana'] ?? null,
            'fecha_inicio' => $data['fecha_inicio'],
            'fecha_fin'    => $data['fecha_fin'] ?? null,
            'area'         => $data['area'] ?? null,
            'notas'        => $data['notas'] ?? null,
            'activo'       => true,
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

        if (! $pivote) {
            return back()->with('error', 'Asignación no encontrada.');
        }

        $user->turnos()->updateExistingPivot($pivote->id, [
            'activo' => ! $pivote->pivot->activo,
        ]);

        return back()->with('success', 'Estado actualizado.');
    }

    private function buscarConflicto(User $user, array $data): ?string
    {
        $nuevoTurno = Turno::find($data['turno_id']);
        if (! $nuevoTurno) {
            return null;
        }

        $nuevoInicio = $data['fecha_inicio'];
        $nuevoFin    = $data['fecha_fin']  ?? null;
        $nuevoDia    = $data['dia_semana'] ?? null;

        $turnosActuales = $user->turnos()
            ->wherePivot('activo', true)
            ->get();

        foreach ($turnosActuales as $actual) {
            $pivote       = $actual->pivot;
            $actualDia    = $pivote->dia_semana;
            $actualInicio = $pivote->fecha_inicio;
            $actualFin    = $pivote->fecha_fin;

            if ($nuevoDia !== null && $actualDia !== null && $nuevoDia != $actualDia) {
                continue;
            }

            $finA = $nuevoFin  ?? '9999-12-31';
            $finB = $actualFin ?? '9999-12-31';

            if (! ($nuevoInicio <= $finB && $finA >= $actualInicio)) {
                continue;
            }

            if (! $nuevoTurno->seSolapaConHorario($actual)) {
                continue;
            }

            $diaTexto = $this->textoDia($actualDia);
            $rangoActual = $this->textoRangoFechas($actualInicio, $actualFin);

            return sprintf(
                'Conflicto con el turno "%s" (%s · %s · %s). Ajusta las fechas, el día o el horario.',
                $actual->nombre,
                $actual->rango ?? $this->textoRangoHoras($actual),
                $diaTexto,
                $rangoActual
            );
        }

        return null;
    }

    private function textoDia(?int $dia): string
    {
        if ($dia === null) {
            return 'todos los días';
        }

        return ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'][$dia] ?? '—';
    }

    private function textoRangoFechas(?string $inicio, ?string $fin): string
    {
        if (! $inicio) {
            return 'sin vigencia';
        }

        $ini = Carbon::parse($inicio)->format('d/m/Y');

        if (! $fin) {
            return "desde {$ini} (indefinido)";
        }

        $f = Carbon::parse($fin)->format('d/m/Y');

        return "del {$ini} al {$f}";
    }

    private function textoRangoHoras(Turno $turno): string
    {
        $ini = substr($turno->hora_inicio, 0, 5);
        $fin = substr($turno->hora_fin, 0, 5);
        return "{$ini} — {$fin}";
    }
}
