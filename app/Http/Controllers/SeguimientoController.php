<?php

namespace App\Http\Controllers;

use App\Models\CamaPaciente;
use App\Models\Paciente;
use App\Models\Seguimiento;
use App\Models\SignoVital;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Cita;
use App\Models\Consulta;

class SeguimientoController extends Controller
{
    /**
     * Lista de pacientes en seguimiento.
     * Criterio: cama activa o triage grave en últimas 24h.
     */
    public function index(Request $request): View
{
    $user = auth()->user();
    $esAdmin = $user->hasRole('administrador');
    $esMedico = $this->esMedico($user);
    $esEnfermeria = $this->esEnfermeria($user);

    $busqueda = $request->input('buscar');
    $filtro = $request->input('filtro', 'todos');

    // Pacientes con cama activa
    $pacientesConCama = CamaPaciente::where('activa', true)
        ->pluck('paciente_id')
        ->unique();

    // Pacientes con triage grave reciente
    $pacientesConTriage = SignoVital::whereIn('triage', ['rojo', 'naranja', 'amarillo'])
        ->where('created_at', '>=', now()->subHours(24))
        ->pluck('paciente_id')
        ->unique();

    // Unión
    $pacientesIds = $pacientesConCama->merge($pacientesConTriage)->unique();

    if (!$esAdmin) {
        if ($esMedico) {
            // Médico: solo pacientes que ha atendido (citas o consultas)
            $pacientesIds = $pacientesIds->filter(function ($pacienteId) use ($user) {
                return Consulta::where('paciente_id', $pacienteId)
                    ->where('medico_id', $user->id)
                    ->exists()
                    || Cita::where('paciente_id', $pacienteId)
                        ->where('medico_id', $user->id)
                        ->exists();
            });
        } elseif ($esEnfermeria) {
            // Enfermería: solo pacientes con cama (los que cuida)
            $pacientesIds = $pacientesConCama;
        } else {
            // Otros roles (farmacia, etc.): sin acceso
            $pacientesIds = collect();
        }
    }

    // Query base
    $query = Paciente::whereIn('id', $pacientesIds)
        ->with([
            'ultimoSignoVital',
            'seguimientoActual.user',
        ]);

    // Filtro por tipo
    if ($filtro === 'cama') {
        $query->whereIn('id', $pacientesConCama);
    } elseif ($filtro === 'triage') {
        $query->whereIn('id', $pacientesConTriage);
    }

    // Búsqueda
    if ($busqueda) {
        $query->where(function ($q) use ($busqueda) {
            $q->where('nombre', 'like', "%{$busqueda}%")
              ->orWhere('apellido_paterno', 'like', "%{$busqueda}%")
              ->orWhere('apellido_materno', 'like', "%{$busqueda}%");
        });
    }

    $pacientes = $query->orderBy('apellido_paterno')->paginate(15)->withQueryString();

    // Adjuntar cama actual
    $pacientes->getCollection()->transform(function ($paciente) {
        $paciente->cama_actual = CamaPaciente::with('cama')
            ->where('paciente_id', $paciente->id)
            ->where('activa', true)
            ->first();
        return $paciente;
    });

    // Estadísticas (filtradas por rol también)
    $stats = [
        'total' => $pacientesIds->count(),
        'con_cama' => $pacientesConCama->intersect($pacientesIds)->count(),
        'con_triage' => $pacientesConTriage->intersect($pacientesIds)->count(),
    ];

    return view('seguimientos.index', compact('pacientes', 'busqueda', 'filtro', 'stats'));
}
    /**
     * Detalle del seguimiento de un paciente.
     */
    public function show(Paciente $paciente): View
{
    $this->autorizarAcceso($paciente);

    $paciente->load([
        'ultimoSignoVital',
        'signosVitales' => fn($q) => $q->orderByDesc('created_at')->limit(20),
    ]);

    $camaActual = CamaPaciente::with('cama')
        ->where('paciente_id', $paciente->id)
        ->where('activa', true)
        ->first();

    $seguimientos = $paciente->seguimientos()
        ->with(['user', 'cama'])
        ->orderByDesc('created_at')
        ->get();

    return view('seguimientos.show', compact('paciente', 'camaActual', 'seguimientos'));
}

    /**
     * Formulario para crear un seguimiento.
     */
    public function create(Paciente $paciente): View
    {
        return view('seguimientos.create', compact('paciente'));
    }

    /**
     * Guarda un nuevo seguimiento.
     */
   public function store(Request $request, Paciente $paciente): RedirectResponse
{
    $this->autorizarAcceso($paciente);

    $data = $request->validate([
        'tipo' => ['required', 'in:evolucion,nota_enfermeria,interconsulta,traslado,alta'],
        'estado_paciente' => ['nullable', 'in:estable,mejorando,grave,critico,fallecido'],
        'contenido' => ['required', 'string'],
        'temperatura' => ['nullable', 'numeric', 'min:30', 'max:45'],
        'frecuencia_cardiaca' => ['nullable', 'integer', 'min:20', 'max:250'],
        'frecuencia_respiratoria' => ['nullable', 'integer', 'min:5', 'max:80'],
        'presion_arterial' => ['nullable', 'string', 'max:20'],
        'saturacion_oxigeno' => ['nullable', 'integer', 'min:0', 'max:100'],
    ]);

    $cama = CamaPaciente::where('paciente_id', $paciente->id)
        ->where('activa', true)
        ->first();

    $data['paciente_id'] = $paciente->id;
    $data['user_id'] = auth()->id();
    $data['cama_id'] = $cama?->cama_id;

    Seguimiento::create($data);

    return redirect()->route('seguimientos.show', $paciente)
        ->with('success', 'Seguimiento registrado correctamente.');
}

    /**
     * Elimina un seguimiento.
     */
    public function destroy(Seguimiento $seguimiento): RedirectResponse
    {
        $pacienteId = $seguimiento->paciente_id;
        $seguimiento->delete();

        return redirect()->route('seguimientos.show', $pacienteId)
            ->with('success', 'Seguimiento eliminado.');
    }

    /**
 * Verifica que el usuario tenga acceso al seguimiento de este paciente.
 */
private function autorizarAcceso(Paciente $paciente): void
{
    $user = auth()->user();

    if ($user->hasRole('administrador')) return;

    if ($this->esMedico($user)) {
        $tieneAcceso = Consulta::where('paciente_id', $paciente->id)
            ->where('medico_id', $user->id)
            ->exists()
            || Cita::where('paciente_id', $paciente->id)
                ->where('medico_id', $user->id)
                ->exists();

        if (!$tieneAcceso) {
            abort(403, 'No tienes acceso al seguimiento de este paciente.');
        }

        return;
    }

    if ($this->esEnfermeria($user)) {
        // ¿El paciente tiene cama activa?
        $tieneCama = CamaPaciente::where('paciente_id', $paciente->id)
            ->where('activa', true)
            ->exists();

        if (!$tieneCama) {
            abort(403, 'No tienes acceso al seguimiento de este paciente.');
        }

        return;
    }

    // Otros roles: sin acceso
    abort(403, 'No tienes permiso para acceder a seguimientos.');
}

private function esMedico($user): bool
{
    return $user->roles->contains(function ($rol) {
        $n = strtolower($rol->name);
        return str_contains($n, 'medic') || str_contains($n, 'doctor') || str_contains($n, 'médic');
    });
}

private function esEnfermeria($user): bool
{
    return $user->roles->contains(function ($rol) {
        return str_contains(strtolower($rol->name), 'enfermer');
    });
}
}