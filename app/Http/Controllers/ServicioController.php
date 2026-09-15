<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

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
            'total' => Servicio::count(),
            'activos' => Servicio::where('activo', true)->count(),
            'tipos' => Servicio::distinct('tipo')->count('tipo'),
        ];

        return view('servicios.index', compact('servicios', 'busqueda', 'tipo', 'stats'));
    }

    public function create(): View
    {
        return view('servicios.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validar($request);

        $data['abierto_24h'] = $request->boolean('abierto_24h');
        $data['activo'] = $request->boolean('activo');

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
        $data = $this->validar($request, $servicio->id);

        $data['abierto_24h'] = $request->boolean('abierto_24h');
        $data['activo'] = $request->boolean('activo');

        $servicio->update($data);

        return redirect()->route('servicios.index')
            ->with('success', 'Servicio actualizado correctamente.');
    }

    public function destroy(Servicio $servicio): RedirectResponse
    {
        if ($servicio->users()->count() > 0) {
            return back()->with('error', 'No puedes eliminar un servicio con personal asignado.');
        }

        if ($servicio->camas()->count() > 0) {
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
            'user_id' => ['required', 'exists:users,id'],
            'rol_en_servicio' => ['nullable', 'string', 'max:50'],
            'fecha_inicio' => ['nullable', 'date'],
        ]);

        // Evitar duplicado activo
        $existe = $servicio->users()
            ->wherePivot('user_id', $data['user_id'])
            ->wherePivot('activo', true)
            ->exists();

        if ($existe) {
            return back()->with('error', 'Ese usuario ya está asignado al servicio.');
        }

        $servicio->users()->attach($data['user_id'], [
            'rol_en_servicio' => $data['rol_en_servicio'] ?? null,
            'fecha_inicio' => $data['fecha_inicio'] ?? now(),
            'activo' => true,
        ]);

        return back()->with('success', 'Personal asignado correctamente.');
    }

    /**
     * Elimina la asignación de personal.
     */
    public function quitarPersonal(Servicio $servicio, $pivotId): RedirectResponse
    {
        $servicio->users()->newPivotStatement()
            ->where('id', $pivotId)
            ->where('servicio_id', $servicio->id)
            ->delete();

        return back()->with('success', 'Personal removido del servicio.');
    }

    /**
     * Reglas de validación.
     */
    private function validar(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'codigo' => ['required', 'string', 'max:20', Rule::unique('servicios', 'codigo')->ignore($id)],
            'nombre' => ['required', 'string', 'max:150'],
            'tipo' => ['required', 'in:consulta_externa,urgencias,hospitalizacion,quirofano,farmacia,enfermeria,laboratorio,imagenologia,otro'],
            'ubicacion' => ['nullable', 'string', 'max:200'],
            'piso' => ['nullable', 'string', 'max:50'],
            'ala' => ['nullable', 'string', 'max:50'],
            'hora_apertura' => ['nullable', 'date_format:H:i'],
            'hora_cierre' => ['nullable', 'date_format:H:i'],
            'capacidad' => ['nullable', 'integer', 'min:0'],
            'extension_telefonica' => ['nullable', 'string', 'max:20'],
            'descripcion' => ['nullable', 'string'],
            'notas' => ['nullable', 'string'],
        ], [
            'codigo.unique' => 'Ya existe un servicio con ese código.',
            'tipo.required' => 'El tipo de servicio es obligatorio.',
        ]);
    }
}