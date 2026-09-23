<?php

namespace App\Http\Controllers;

use App\Models\Dispositivo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DispositivoController extends Controller
{
    /**
     * Listado.
     * - Admin: ve TODOS los dispositivos del sistema.
     * - Usuario normal: solo los suyos.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $esAdmin = $user->hasRole('administrador');

        $busqueda = $request->input('buscar');
        $filtro = $request->input('filtro'); // confiable, pendiente, bloqueado, inactivo

        $query = Dispositivo::with(['user', 'aprobadoPor'])
            ->orderByDesc('ultimo_acceso');

        // 🔒 Filtro por rol
        if (!$esAdmin) {
            $query->where('user_id', $user->id);
        }

        // Búsqueda
        if ($busqueda) {
            $query->where(function ($q) use ($busqueda) {
                $q->where('nombre', 'like', "%{$busqueda}%")
                  ->orWhere('ip_registro', 'like', "%{$busqueda}%")
                  ->orWhereHas('user', function ($sub) use ($busqueda) {
                      $sub->where('nombre', 'like', "%{$busqueda}%")
                          ->orWhere('email', 'like', "%{$busqueda}%");
                  });
            });
        }

        // Filtros
        if ($filtro === 'confiable') {
            $query->where('confiable', true)->where('activo', true);
        } elseif ($filtro === 'pendiente') {
            $query->where('confiable', false)->where('activo', true);
        } elseif ($filtro === 'bloqueado') {
            $query->where('activo', false);
        }

        $dispositivos = $query->paginate(20)->withQueryString();

        // Estadísticas (respetando el filtro de rol)
        $baseStats = Dispositivo::query();
        if (!$esAdmin) {
            $baseStats->where('user_id', $user->id);
        }

        $stats = [
            'total' => (clone $baseStats)->count(),
            'confiables' => (clone $baseStats)->where('confiable', true)->where('activo', true)->count(),
            'pendientes' => (clone $baseStats)->where('confiable', false)->where('activo', true)->count(),
            'bloqueados' => (clone $baseStats)->where('activo', false)->count(),
        ];

        return view('dispositivos.index', compact(
            'dispositivos', 'busqueda', 'filtro', 'stats'
        ));
    }

    /**
     * Detalle de un dispositivo.
     */
    public function show(Dispositivo $dispositivo): View
    {
        $this->autorizarAcceso($dispositivo);

        $dispositivo->load(['user', 'aprobadoPor']);

        return view('dispositivos.show', compact('dispositivo'));
    }

    /**
     * Marcar como confiable (solo admin).
     */
    public function confiar(Dispositivo $dispositivo): RedirectResponse
    {
        if (!auth()->user()->hasRole('administrador')) {
            abort(403, 'Solo el administrador puede marcar dispositivos como confiables.');
        }

        $dispositivo->update([
            'confiable' => true,
            'activo' => true,
            'aprobado_en' => now(),
            'aprobado_por' => auth()->id(),
        ]);

        return back()->with('success', 'Dispositivo marcado como confiable.');
    }

    /**
     * Bloquear dispositivo.
     */
    public function bloquear(Dispositivo $dispositivo): RedirectResponse
    {
        $this->autorizarAcceso($dispositivo);

        // Un usuario no admin no puede bloquear su dispositivo actual
        if (!$this->esAdmin(auth()->user())) {
            $huellaActual = \App\Services\DispositivoService::generarHuella(request());
            if ($dispositivo->huella === $huellaActual) {
                return back()->with('error', 'No puedes bloquear el dispositivo con el que estás conectado.');
            }
        }

        $dispositivo->update(['activo' => false, 'confiable' => false]);

        return back()->with('success', 'Dispositivo bloqueado.');
    }

    /**
     * Reactivar.
     */
    public function reactivar(Dispositivo $dispositivo): RedirectResponse
    {
        if (!auth()->user()->hasRole('administrador')) {
            abort(403, 'Solo el administrador puede reactivar dispositivos.');
        }

        $dispositivo->update(['activo' => true]);

        return back()->with('success', 'Dispositivo reactivado.');
    }

    /**
     * Eliminar (solo admin).
     */
    public function destroy(Dispositivo $dispositivo): RedirectResponse
    {
        if (!auth()->user()->hasRole('administrador')) {
            abort(403, 'Solo el administrador puede eliminar dispositivos.');
        }

        $dispositivo->delete();

        return back()->with('success', 'Dispositivo eliminado.');
    }

    /**
     * Verificación: el usuario solo puede ver sus propios dispositivos
     * a menos que sea admin.
     */
    private function autorizarAcceso(Dispositivo $dispositivo): void
    {
        if ($this->esAdmin(auth()->user())) return;

        if ($dispositivo->user_id !== auth()->id()) {
            abort(403, 'No tienes permiso para ver este dispositivo.');
        }
    }

    private function esAdmin($user): bool
    {
        return $user->hasRole('administrador');
    }
}