<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use App\Enums\AuditEvent;
use App\Services\AuditoriaService;

class ServicioController extends Controller
{
    public function index(Request $request): View
    {
        $busqueda = $request->input('buscar');
        $tipo = $request->input('tipo');

        $servicios = Servicio::query()
            ->withCount(['users', 'camas'])
            ->when($busqueda, function ($q) use ($busqueda) {
                $q->where('nombre', 'like', "%{$busqueda}%")
                  ->orWhere('codigo', 'like', "%{$busqueda}%")
                  ->orWhere('ubicacion', 'like', "%{$busqueda}%");
            })
            ->when($tipo, fn($q) => $q->where('tipo', $tipo))
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        // Estadísticas
        $stats = [
            'total'   => Servicio::count(),
            'activos' => Servicio::where('activo', true)->count(),
            'tipos'   => Servicio::distinct('tipo')->count('tipo'),
        ];

        return view('servicios.index', compact('servicios', 'busqueda', 'tipo', 'stats'));
    }

    public function create(): View
    {
        return view('servicios.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->prepararDatos($this->validar($request), $request);

        Servicio::create($data);

        return redirect()->route('servicios.index')
            ->with('success', 'Servicio registrado correctamente.');
    }

    public function show(Servicio $servicio): View
    {
        $servicio->load(['users', 'camas']);

        return view('servicios.show', compact('servicio'));
    }

    public function edit(Servicio $servicio): View
    {
        return view('servicios.edit', compact('servicio'));
    }

    public function update(Request $request, Servicio $servicio): RedirectResponse
    {
        $data = $this->prepararDatos($this->validar($request, $servicio->id), $request);

        $servicio->update($data);

        return redirect()->route('servicios.index')
            ->with('success', 'Servicio actualizado correctamente.');
    }

    public function destroy(Servicio $servicio): RedirectResponse
    {
        if ($servicio->users()->count() > 0) {
            AuditoriaService::registrar(
                AuditEvent::ACCESO_DENEGADO,
                'servicios',
                "Intento de eliminar servicio {$servicio->nombre} bloqueado por tener personal asignado",
                $servicio
            );

            return back()->with('error', 'No puedes eliminar un servicio con personal asignado.');
        }

        if ($servicio->camas()->count() > 0) {
            AuditoriaService::registrar(
                AuditEvent::ACCESO_DENEGADO,
                'servicios',
                "Intento de eliminar servicio {$servicio->nombre} bloqueado por tener camas asignadas",
                $servicio
            );

            return back()->with('error', 'No puedes eliminar un servicio con camas asignadas.');
        }

        $servicio->delete(); 

        return redirect()->route('servicios.index')
            ->with('success', 'Servicio eliminado correctamente.');
    }

    /**
     * Formulario para asignar personal al servicio.
     */
    public function personal(Servicio $servicio): View
    {
        $servicio->load('users');

        // Usuarios no asignados al servicio
        $asignados = $servicio->users()->pluck('users.id')->toArray();
        $disponibles = User::whereNotIn('id', $asignados)
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view('servicios.personal', compact('servicio', 'disponibles'));
    }

    /**
     * Asigna un usuario al servicio.
     */
    public function asignarPersonal(Request $request, Servicio $servicio): RedirectResponse
    {
        $data = $request->validate([
            'user_id'         => ['required', 'exists:users,id'],
            'rol_en_servicio' => ['nullable', 'string', 'max:50'],
            'fecha_inicio'    => ['nullable', 'date'],
        ]);

        $existe = $servicio->users()
            ->wherePivot('user_id', $data['user_id'])
            ->wherePivot('activo', true)
            ->exists();

        if ($existe) {
            return back()->with('error', 'Ese usuario ya está asignado al servicio.');
        }

        $servicio->users()->attach($data['user_id'], [
            'rol_en_servicio' => $data['rol_en_servicio'] ?? null,
            'fecha_inicio'    => $data['fecha_inicio'] ?? now(),
            'activo'          => true,
        ]);

        $userAsignado = \App\Models\User::find($data['user_id']);

        AuditoriaService::registrar(
            AuditEvent::UPDATED,
            'servicios',
            "Asignación de personal al servicio {$servicio->nombre}: {$userAsignado?->nombre_completo}",
            $servicio,
            [],
            [],
            [
                'accion'          => 'asignar_personal',
                'user_id'         => $data['user_id'],
                'user_nombre'     => $userAsignado?->nombre_completo,
                'rol_en_servicio' => $data['rol_en_servicio'] ?? null,
                'fecha_inicio'    => $data['fecha_inicio'] ?? now()->toDateString(),
            ]
        );

        return back()->with('success', 'Personal asignado correctamente.');
    }

    /**
     * Elimina la asignación de personal.
     */
    public function quitarPersonal(Servicio $servicio, $pivotId): RedirectResponse
    {
        // Recuperar datos del pivote antes de borrar, para poder auditar
        $pivot = $servicio->users()->newPivotStatement()
            ->where('id', $pivotId)
            ->where('servicio_id', $servicio->id)
            ->first();

        $userAfectado = $pivot ? \App\Models\User::find($pivot->user_id) : null;

        $servicio->users()->newPivotStatement()
            ->where('id', $pivotId)
            ->where('servicio_id', $servicio->id)
            ->delete();

        AuditoriaService::registrar(
            AuditEvent::UPDATED,
            'servicios',
            "Remoción de personal del servicio {$servicio->nombre}: {$userAfectado?->nombre_completo}",
            $servicio,
            [],
            [],
            [
                'accion'          => 'quitar_personal',
                'pivot_id'        => $pivotId,
                'user_id'         => $pivot?->user_id,
                'user_nombre'     => $userAfectado?->nombre_completo,
                'rol_en_servicio' => $pivot?->rol_en_servicio,
            ]
        );

        return back()->with('success', 'Personal removido del servicio.');
    }

    /**
     * Reglas de validación.
     *
     * Nota: aceptamos H:i y H:i:s porque los <input type="time"> modernos
     * envían "HH:MM:SS". La normalización a "H:i" se hace en prepararDatos().
     */
    private function validar(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'codigo'               => ['required', 'string', 'max:20', Rule::unique('servicios', 'codigo')->ignore($id)],
            'nombre'               => ['required', 'string', 'max:150'],
            'tipo'                 => ['required', 'in:consulta_externa,urgencias,hospitalizacion,quirofano,farmacia,enfermeria,laboratorio,imagenologia,otro'],
            'ubicacion'            => ['nullable', 'string', 'max:200'],
            'piso'                 => ['nullable', 'string', 'max:50'],
            'ala'                  => ['nullable', 'string', 'max:50'],
            'hora_apertura'        => ['nullable', 'date_format:H:i,H:i:s'],
            'hora_cierre'          => ['nullable', 'date_format:H:i,H:i:s'],
            'capacidad'            => ['nullable', 'integer', 'min:0'],
            'tiene_costo'          => ['nullable', 'boolean'],
            'precio'               => ['nullable', 'numeric', 'min:0'],
            'precio_descripcion'   => ['nullable', 'string', 'max:100'],
            'extension_telefonica' => ['nullable', 'string', 'max:20'],
            'descripcion'          => ['nullable', 'string'],
            'notas'                => ['nullable', 'string'],
        ], [
            'codigo.unique' => 'Ya existe un servicio con ese código.',
            'tipo.required' => 'El tipo de servicio es obligatorio.',
        ]);
    }

    /**
     * Normaliza y completa los datos antes de guardar.
     */
    private function prepararDatos(array $data, Request $request): array
    {
        $data['abierto_24h'] = $request->boolean('abierto_24h');
        $data['activo']      = $request->boolean('activo');

        // Normalizar horas a "H:i" (ej. "08:00:00" -> "08:00")
        foreach (['hora_apertura', 'hora_cierre'] as $campo) {
            if (!empty($data[$campo])) {
                $data[$campo] = substr($data[$campo], 0, 5);
            }
        }

        // Si no tiene costo, forzar precio 0 y limpiar descripción
        if (! $request->boolean('tiene_costo')) {
            $data['precio'] = 0;
            $data['precio_descripcion'] = null;
        } else {
            $data['precio'] = $data['precio'] ?? 0;
        }

        return $data;
    }
}