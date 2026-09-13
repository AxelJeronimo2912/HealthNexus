<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CitaController extends Controller
{
    /**
     * Listado de citas del día.
     * - Médico: solo las suyas.
     * - Admin: todas.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();

        $fecha = $request->filled('fecha')
            ? Carbon::parse($request->fecha)
            : Carbon::today();

        $query = Cita::with(['paciente', 'medico', 'turno', 'signoVital'])
            ->whereDate('fecha_hora', $fecha);

        // Médico solo ve sus citas
        if ($this->esMedico($user) && !$user->hasRole('administrador')) {
            $query->where('medico_id', $user->id);
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $citas = $query->orderBy('fecha_hora')->get();

        // Estadísticas del día
        $stats = [
            'total' => $citas->count(),
            'programadas' => $citas->where('estado', 'programada')->count(),
            'confirmadas' => $citas->where('estado', 'confirmada')->count(),
            'atendidas' => $citas->where('estado', 'atendida')->count(),
            'canceladas' => $citas->where('estado', 'cancelada')->count(),
        ];

        return view('citas.index', compact('fecha', 'citas', 'stats'));
    }

    /**
     * Detalle de una cita.
     */
    public function show(Cita $cita): View
    {
        $this->autorizarAcceso($cita);

        $cita->load(['paciente', 'medico', 'turno', 'signoVital']);

        return view('citas.show', compact('cita'));
    }

    /**
     * Cambia el estado de una cita.
     */
    public function cambiarEstado(Request $request, Cita $cita): RedirectResponse
    {
        $this->autorizarAcceso($cita);

        $request->validate([
            'estado' => ['required', 'in:programada,confirmada,en_curso,atendida,cancelada,no_asistio'],
        ]);

        $cita->update(['estado' => $request->estado]);

        return back()->with('success', 'Estado actualizado: ' . $cita->estado_label);
    }

    /**
     * Elimina una cita (solo admin).
     */
    public function destroy(Cita $cita): RedirectResponse
    {
        if (!auth()->user()->hasRole('administrador')) {
            abort(403, 'Solo el administrador puede eliminar citas.');
        }

        $cita->delete();

        return redirect()->route('citas.index')
            ->with('success', 'Cita eliminada correctamente.');
    }

    /**
     * Verifica que el médico solo acceda a sus propias citas.
     */
    private function autorizarAcceso(Cita $cita): void
    {
        $user = auth()->user();

        if ($this->esMedico($user) && !$user->hasRole('administrador')) {
            if ($cita->medico_id !== $user->id) {
                abort(403, 'No tienes permiso para ver esta cita.');
            }
        }
    }

    private function esMedico($user): bool
    {
        return $user->roles->contains(function ($rol) {
            $n = strtolower($rol->name);
            return str_contains($n, 'medic')
                || str_contains($n, 'doctor')
                || str_contains($n, 'médic');
        });
    }
}