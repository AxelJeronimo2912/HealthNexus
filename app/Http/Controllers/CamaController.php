<?php

namespace App\Http\Controllers;

use App\Models\Cama;
use App\Models\Servicio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use App\Enums\AuditEvent;
use App\Services\AuditoriaService;

class CamaController extends Controller
{
    public function index(Request $request): View
    {
        $busqueda = $request->input('buscar');
        $filtroEstado = $request->input('estado');

        $camas = Cama::query()
            ->when($busqueda, function ($q) use ($busqueda) {
                $q->where(function ($query) use ($busqueda) {
                    $query->where('codigo', 'like', "%{$busqueda}%")
                        ->orWhere('habitacion', 'like', "%{$busqueda}%")
                        ->orWhere('area', 'like', "%{$busqueda}%");
                });
            })
            ->when(
                $filtroEstado,
                fn ($q) => $q->where('estado', $filtroEstado)
            )
            ->orderBy('area')
            ->orderBy('piso')
            ->orderBy('codigo')
            ->paginate(15)
            ->withQueryString();

        // Estadísticas rápidas
        $stats = [
            'total' => Cama::count(),
            'disponibles' => Cama::where('estado', 'disponible')->count(),
            'ocupadas' => Cama::where('estado', 'ocupada')->count(),
            'mantenimiento' => Cama::whereIn(
                'estado',
                ['mantenimiento', 'fuera_servicio']
            )->count(),
        ];

        // Servicios activos para el formulario de camas
        $servicios = Servicio::where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view('camas.index', compact(
            'camas',
            'busqueda',
            'filtroEstado',
            'stats',
            'servicios'
        ));
    }

    public function create(): View
    {
        $servicios = Servicio::where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view('camas.create', compact('servicios'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validar($request);

        $data['oxigeno'] = $request->boolean('oxigeno');
        $data['monitor'] = $request->boolean('monitor');
        $data['ventilador'] = $request->boolean('ventilador');
        $data['activo'] = $request->boolean('activo');

        Cama::create($data);

        return redirect()
            ->route('camas.index')
            ->with('success', 'Cama registrada correctamente.');
    }

    public function show(Cama $cama): View
    {
        return view('camas.show', compact('cama'));
    }

    public function edit(Cama $cama): View
    {
        $servicios = Servicio::where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view('camas.edit', [
            'cama' => $cama,
            'servicios' => $servicios,
        ]);
    }

    public function update(Request $request, Cama $cama): RedirectResponse
    {
        $data = $this->validar($request, $cama->id);

        $data['oxigeno'] = $request->boolean('oxigeno');
        $data['monitor'] = $request->boolean('monitor');
        $data['ventilador'] = $request->boolean('ventilador');
        $data['activo'] = $request->boolean('activo');

        $cama->update($data);

        return redirect()
            ->route('camas.index')
            ->with('success', 'Cama actualizada correctamente.');
    }

    public function destroy(Cama $cama): RedirectResponse
{
    if ($cama->pacienteActual()->exists()) {
        AuditoriaService::registrar(
            AuditEvent::ACCESO_DENEGADO,
            'camas',
            "Intento de eliminar la cama {$cama->etiqueta} bloqueado por tener un paciente asignado",
            $cama
        );

        return back()->with('error', 'No puedes eliminar una cama con un paciente asignado.');
    }

    if ($cama->estado === 'ocupada') {
        AuditoriaService::registrar(
            AuditEvent::ACCESO_DENEGADO,
            'camas',
            "Intento de eliminar la cama {$cama->etiqueta} bloqueado porque está marcada como ocupada",
            $cama
        );

        return back()->with('error', 'No puedes eliminar una cama marcada como ocupada.');
    }

    $cama->delete(); // el trait registra 'deleted'

    return redirect()
        ->route('camas.index')
        ->with('success', 'Cama eliminada correctamente.');
}

    /**
     * Cambia el estado de una cama desde el listado.
     */
    public function cambiarEstado(Request $request, Cama $cama): RedirectResponse
{
    $request->validate([
        'estado' => [
            'required',
            Rule::in(['disponible', 'ocupada', 'mantenimiento', 'limpieza', 'fuera_servicio']),
        ],
    ]);

    $estadoAnterior = $cama->estado;
    $nuevoEstado    = $request->estado;

    if ($estadoAnterior === $nuevoEstado) {
        return back()->with('info', 'La cama ya estaba en ese estado.');
    }

    $cama->update(['estado' => $nuevoEstado]);

    // ── Evento semántico según el estado destino ──
    $evento = match ($nuevoEstado) {
        'ocupada'        => AuditEvent::ASIGNACION_CAMA,
        'disponible'     => AuditEvent::LIBERACION_CAMA,
        'mantenimiento',
        'limpieza',
        'fuera_servicio' => AuditEvent::HOSPITALIZACION,
        default          => AuditEvent::UPDATED,
    };

    AuditoriaService::registrar(
        $evento,
        'camas',
        "Cama {$cama->etiqueta} cambió de '{$estadoAnterior}' a '{$nuevoEstado}'",
        $cama,
        ['estado' => $estadoAnterior],
        ['estado' => $nuevoEstado],
        [
            'cama_id'      => $cama->id,
            'cama_codigo'  => $cama->codigo,
            'servicio_id'  => $cama->servicio_id,
        ]
    );

    return back()->with('success', 'Estado actualizado a: ' . $cama->estado_label);
}
    private function validar(
        Request $request,
        ?int $id = null
    ): array {
        return $request->validate([
            'codigo' => [
                'required',
                'string',
                'max:50',
                Rule::unique('camas', 'codigo')->ignore($id),
            ],

            'nombre' => [
                'nullable',
                'string',
                'max:100',
            ],

            'piso' => [
                'nullable',
                'string',
                'max:50',
            ],

            'ala' => [
                'nullable',
                'string',
                'max:50',
            ],

            'habitacion' => [
                'nullable',
                'string',
                'max:50',
            ],

            'area' => [
                'nullable',
                'string',
                'max:100',
            ],

            'tipo' => [
                'required',
                Rule::in([
                    'general',
                    'pediatrica',
                    'uci',
                    'aislamiento',
                    'recuperacion',
                    'urgencias',
                ]),
            ],

            'estado' => [
                'required',
                Rule::in([
                    'disponible',
                    'ocupada',
                    'mantenimiento',
                    'limpieza',
                    'fuera_servicio',
                ]),
            ],

            'notas' => [
                'nullable',
                'string',
            ],

            'servicio_id' => [
                'nullable',
                'exists:servicios,id',
            ],
        ], [
            'codigo.required' => 'El código de la cama es obligatorio.',
            'codigo.unique' => 'Ya existe una cama con ese código.',
        ]);
    }
}
