<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Cuenta;      
use App\Models\Servicio;
use App\Enums\AuditEvent;
use App\Services\AuditoriaService;
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
            ->whereDate('fecha_hora', $fecha)
            ->whereIn('estado', ['programada', 'confirmada', 'en_curso']);


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

    $estadoAnterior = $cita->estado;
    $nuevoEstado    = $request->estado;

    // Si no cambió nada, no auditamos doble
    if ($estadoAnterior === $nuevoEstado) {
        return back()->with('info', 'La cita ya estaba en ese estado.');
    }

    $cita->update(['estado' => $nuevoEstado]);

    // ── Evento semántico según el nuevo estado ──
    $evento = match ($nuevoEstado) {
        'confirmada' => AuditEvent::CITA_CONFIRMADA,
        'en_curso'   => AuditEvent::CITA_EN_CURSO,
        'atendida'   => AuditEvent::CITA_ATENDIDA,
        'cancelada'  => AuditEvent::CITA_CANCELADA,
        'no_asistio' => AuditEvent::CITA_NO_ASISTIO,
        default      => AuditEvent::UPDATED,
    };

    AuditoriaService::registrar(
        $evento,
        'citas',
        "Cita #{$cita->id} ({$cita->paciente?->nombre_completo}) cambió de '{$estadoAnterior}' a '{$nuevoEstado}'",
        $cita,
        ['estado' => $estadoAnterior],
        ['estado' => $nuevoEstado],
        [
            'cita_id'     => $cita->id,
            'paciente_id' => $cita->paciente_id,
            'medico_id'   => $cita->medico_id,
            'fecha_hora'  => $cita->fecha_hora?->toDateTimeString(),
        ]
    );

    if ($nuevoEstado === 'atendida' && $estadoAnterior !== 'atendida') {
        $this->cobrarServicioDeCita($cita);
    }

    return back()->with('success', 'Estado actualizado: ' . $cita->estado_label);
}
   protected function cobrarServicioDeCita(Cita $cita): void
{
    $servicio = $cita->servicio;

    if (! $servicio || (float) $servicio->precio <= 0) {
        return;
    }

    $cuenta = $cita->paciente->obtenerCuentaAbierta();

    $yaCobrado = $cuenta->items()->where('cita_id', $cita->id)->exists();
    if ($yaCobrado) {
        return;
    }

    $item = $cuenta->items()->create([
        'servicio_id'     => $servicio->id,
        'cita_id'         => $cita->id,
        'user_id'         => auth()->id(),
        'concepto'        => $servicio->nombre,
        'cantidad'        => 1,
        'precio_unitario' => $servicio->precio,
        'notas'           => 'Generado automáticamente al atender la cita #' . $cita->id,
    ]);

    AuditoriaService::registrar(
        AuditEvent::CITA_COBRO,
        'citas',
        "Cobro generado por cita #{$cita->id}: {$servicio->nombre} (\${$servicio->precio})",
        $cita,
        [],
        [],
        [
            'cuenta_id'       => $cuenta->id,
            'item_id'         => $item->id,
            'servicio_id'     => $servicio->id,
            'servicio_nombre' => $servicio->nombre,
            'monto'           => (float) $servicio->precio,
        ]
    );
}
    /**
     * Elimina una cita (solo admin).
     */
    public function destroy(Cita $cita): RedirectResponse
{
    if (!auth()->user()->hasRole('administrador')) {
        AuditoriaService::registrar(
            AuditEvent::ACCESO_DENEGADO,
            'citas',
            "Intento no autorizado de eliminar la cita #{$cita->id} ({$cita->paciente?->nombre_completo})",
            $cita,
            [],
            [],
            ['motivo' => 'usuario sin rol administrador']
        );

        abort(403, 'Solo el administrador puede eliminar citas.');
    }

    // El trait Auditable registra el 'deleted' automáticamente
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

    /**
 * Historial completo de citas (para el modal).
 * Médico: solo las suyas. Admin: todas.
 */
public function historial(Request $request): View
{
    $user = auth()->user();

    $query = Cita::with(['paciente', 'medico'])
        ->whereIn('estado', ['atendida', 'cancelada', 'no_asistio'])
        ->orderByDesc('fecha_hora');

    if ($this->esMedico($user) && !$user->hasRole('administrador')) {
        $query->where('medico_id', $user->id);
    }

    if ($request->filled('paciente_id')) {
        $query->where('paciente_id', $request->paciente_id);
    }

    $historial = $query->paginate(15);

    return view('citas.partials.historial-modal', compact('historial'));
}
}