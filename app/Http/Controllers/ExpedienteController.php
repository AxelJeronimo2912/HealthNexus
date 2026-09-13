<?php

namespace App\Http\Controllers;

use App\Models\Consulta;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpedienteController extends Controller
{
    /**
     * Listado de pacientes con expediente.
     * - Admin: todos los pacientes con al menos una consulta.
     * - Médico: solo pacientes que él ha atendido.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $esAdmin = $user->hasRole('administrador');
        $esMedico = $this->esMedico($user);

        $busqueda = $request->input('buscar');

        // Query base: pacientes con consultas
        $query = Paciente::query()
            ->whereHas('consultas', function ($q) use ($esAdmin, $esMedico, $user) {
                if (!$esAdmin && $esMedico) {
                    $q->where('medico_id', $user->id);
                }
            })
            ->withCount(['consultas' => function ($q) use ($esAdmin, $esMedico, $user) {
                if (!$esAdmin && $esMedico) {
                    $q->where('medico_id', $user->id);
                }
            }])
            ->with(['ultimoSignoVital']);

        // Búsqueda por nombre o CURP
        if ($busqueda) {
            $query->where(function ($q) use ($busqueda) {
                $q->where('nombre', 'like', "%{$busqueda}%")
                  ->orWhere('apellido_paterno', 'like', "%{$busqueda}%")
                  ->orWhere('apellido_materno', 'like', "%{$busqueda}%")
                  ->orWhere('curp', 'like', "%{$busqueda}%");
            });
        }

        $pacientes = $query->orderBy('apellido_paterno')
            ->paginate(15)
            ->withQueryString();

        return view('expedientes.index', compact('pacientes', 'busqueda'));
    }

    /**
     * Historial completo de un paciente.
     * - Admin: ve todas las consultas.
     * - Médico: solo las que él hizo.
     */
    public function show(Paciente $paciente): View
    {
        $user = auth()->user();
        $esAdmin = $user->hasRole('administrador');
        $esMedico = $this->esMedico($user);

        // Cargar consultas filtradas
        $consultasQuery = Consulta::where('paciente_id', $paciente->id)
            ->with(['medico', 'medicamentos', 'diagnosticoPrincipal', 'diagnosticoSecundario'])
            ->orderByDesc('created_at');

        // Filtro por rol
        if (!$esAdmin && $esMedico) {
            $consultasQuery->where('medico_id', $user->id);
        }

        $consultas = $consultasQuery->get();

        // Signos vitales del paciente
        $signosVitales = $paciente->signosVitales()
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        // Citas del paciente (opcional)
        $citas = $paciente->citas()
            ->with('medico')
            ->orderByDesc('fecha_hora')
            ->limit(20)
            ->get();

        return view('expedientes.show', compact('paciente', 'consultas', 'signosVitales', 'citas'));
    }

    /**
     * Helper: ¿el usuario es médico?
     */
    private function esMedico($user): bool
    {
        return $user->roles->contains(function ($rol) {
            $n = strtolower($rol->name);
            return str_contains($n, 'medic') || str_contains($n, 'doctor') || str_contains($n, 'médic');
        });
    }
}