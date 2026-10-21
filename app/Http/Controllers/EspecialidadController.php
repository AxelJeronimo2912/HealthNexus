<?php

namespace App\Http\Controllers;

use App\Enums\AuditEvent;
use App\Http\Requests\EspecialidadRequest;
use App\Models\Especialidad;
use App\Models\Servicio;
use App\Models\User;
use App\Services\AuditoriaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EspecialidadController extends Controller
{
    public function index(Request $request): View
    {
        $busqueda = $request->get('buscar', '');
        $grupo    = $request->get('grupo', '');

        $especialidades = Especialidad::query()
            ->when($busqueda, function ($q) use ($busqueda) {
                $q->where(function ($qq) use ($busqueda) {
                    $qq->where('nombre', 'like', "%{$busqueda}%")
                       ->orWhere('codigo', 'like', "%{$busqueda}%");
                });
            })
            ->when($grupo, fn ($q) => $q->where('grupo', $grupo))
            ->withCount(['medicos', 'servicios', 'consultas'])
            ->with(['medicos', 'servicios']) // relaciones para los modales
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total'   => Especialidad::count(),
            'activas' => Especialidad::where('activo', true)->count(),
            'grupos'  => Especialidad::distinct('grupo')->count('grupo'),
        ];

        // Datos para los modales de médicos / servicios
        $medicosDisponibles   = User::role('medico')->orderBy('name')->get();
        $serviciosDisponibles = Servicio::orderBy('nombre')->get();

        return view('especialidades.index', compact(
            'especialidades',
            'stats',
            'busqueda',
            'grupo',
            'medicosDisponibles',
            'serviciosDisponibles'
        ));
    }

    public function create(): View
    {
        return view('especialidades.create');
    }

    public function store(EspecialidadRequest $request): RedirectResponse
    {
        Especialidad::create($request->validated());

        return redirect()->route('especialidades.index')
            ->with('success', 'Especialidad registrada correctamente.');
    }

    public function show(Especialidad $especialidad): View
    {
        $especialidad->load(['medicos', 'servicios']);

        $stats = [
            'citas'     => $especialidad->citas()->count(),
            'consultas' => $especialidad->consultas()->count(),
        ];

        return view('especialidades.show', compact('especialidad', 'stats'));
    }

    public function edit(Especialidad $especialidad): View
    {
        return view('especialidades.edit', compact('especialidad'));
    }

    public function update(EspecialidadRequest $request, Especialidad $especialidad): RedirectResponse
    {
        $especialidad->update($request->validated());

        return redirect()->route('especialidades.index')
            ->with('success', 'Especialidad actualizada correctamente.');
    }

    public function destroy(Especialidad $especialidad): RedirectResponse
    {
        if ($especialidad->medicos()->count() > 0) {
            AuditoriaService::registrar(
                AuditEvent::ACCESO_DENEGADO,
                'especialidades',
                "Intento de eliminar la especialidad {$especialidad->nombre} bloqueado por tener médicos asignados",
                $especialidad
            );

            return back()->with('error', 'No puedes eliminar una especialidad con médicos asignados.');
        }

        if ($especialidad->citas()->count() > 0 || $especialidad->consultas()->count() > 0) {
            AuditoriaService::registrar(
                AuditEvent::ACCESO_DENEGADO,
                'especialidades',
                "Intento de eliminar la especialidad {$especialidad->nombre} bloqueado por tener citas o consultas",
                $especialidad
            );

            return back()->with('error', 'No puedes eliminar una especialidad con citas o consultas registradas.');
        }

        $especialidad->delete(); // el trait registra 'deleted'

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
            'user_id'                    => ['required', 'exists:users,id'],
            'es_principal'               => ['boolean'],
            'numero_cedula_especialidad' => ['nullable', 'string', 'max:50'],
            'fecha_certificacion'        => ['nullable', 'date'],
        ]);

        if ($especialidad->medicos()->wherePivot('user_id', $data['user_id'])->exists()) {
            return back()->with('error', 'Ese médico ya está asignado a esta especialidad.');
        }

        if (!empty($data['es_principal'])) {
            DB::table('especialidad_user')
                ->where('user_id', $data['user_id'])
                ->update(['es_principal' => false]);
        }

        $especialidad->medicos()->attach($data['user_id'], [
            'es_principal'               => !empty($data['es_principal']),
            'numero_cedula_especialidad' => $data['numero_cedula_especialidad'] ?? null,
            'fecha_certificacion'        => $data['fecha_certificacion'] ?? null,
            'activo'                     => true,
        ]);

        $medico = User::find($data['user_id']);

        AuditoriaService::registrar(
            AuditEvent::ASIGNACION_MEDICO_ESPECIALIDAD,
            'especialidades',
            "Asignación del médico {$medico?->nombre_completo} a la especialidad {$especialidad->nombre}",
            $especialidad,
            [],
            [],
            [
                'user_id'                    => $data['user_id'],
                'user_nombre'                => $medico?->nombre_completo,
                'es_principal'               => !empty($data['es_principal']),
                'numero_cedula_especialidad' => $data['numero_cedula_especialidad'] ?? null,
                'fecha_certificacion'        => $data['fecha_certificacion'] ?? null,
            ]
        );

        return back()->with('success', 'Médico asignado correctamente.');
    }

    public function quitarMedico(Especialidad $especialidad, $pivotId): RedirectResponse
    {
        // Recuperar datos del pivote antes de borrar
        $pivot = $especialidad->medicos()->newPivotStatement()
            ->where('id', $pivotId)
            ->where('especialidad_id', $especialidad->id)
            ->first();

        $medico = $pivot ? User::find($pivot->user_id) : null;

        $especialidad->medicos()->newPivotStatement()
            ->where('id', $pivotId)
            ->where('especialidad_id', $especialidad->id)
            ->delete();

        AuditoriaService::registrar(
            AuditEvent::REMOCION_MEDICO_ESPECIALIDAD,
            'especialidades',
            "Remoción del médico {$medico?->nombre_completo} de la especialidad {$especialidad->nombre}",
            $especialidad,
            [],
            [],
            [
                'pivot_id'                   => $pivotId,
                'user_id'                    => $pivot?->user_id,
                'user_nombre'                => $medico?->nombre_completo,
                'es_principal'               => $pivot?->es_principal,
                'numero_cedula_especialidad' => $pivot?->numero_cedula_especialidad,
            ]
        );

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

        $servicio = Servicio::find($data['servicio_id']);

        AuditoriaService::registrar(
            AuditEvent::ASIGNACION_SERVICIO_ESPECIALIDAD,
            'especialidades',
            "Asignación del servicio {$servicio?->nombre} a la especialidad {$especialidad->nombre}",
            $especialidad,
            [],
            [],
            [
                'servicio_id'     => $data['servicio_id'],
                'servicio_nombre' => $servicio?->nombre,
            ]
        );

        return back()->with('success', 'Servicio asignado correctamente.');
    }

    public function quitarServicio(Especialidad $especialidad, $pivotId): RedirectResponse
    {
        $pivot = $especialidad->servicios()->newPivotStatement()
            ->where('id', $pivotId)
            ->where('especialidad_id', $especialidad->id)
            ->first();

        $servicio = $pivot ? Servicio::find($pivot->servicio_id) : null;

        $especialidad->servicios()->newPivotStatement()
            ->where('id', $pivotId)
            ->where('especialidad_id', $especialidad->id)
            ->delete();

        AuditoriaService::registrar(
            AuditEvent::REMOCION_SERVICIO_ESPECIALIDAD,
            'especialidades',
            "Remoción del servicio {$servicio?->nombre} de la especialidad {$especialidad->nombre}",
            $especialidad,
            [],
            [],
            [
                'pivot_id'        => $pivotId,
                'servicio_id'     => $pivot?->servicio_id,
                'servicio_nombre' => $servicio?->nombre,
            ]
        );

        return back()->with('success', 'Servicio removido de la especialidad.');
    }
}