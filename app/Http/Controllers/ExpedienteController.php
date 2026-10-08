<?php

namespace App\Http\Controllers;

use App\Models\Consulta;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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
    $user     = auth()->user();
    $esAdmin  = $user->hasRole('administrador');
    $esMedico = $this->esMedico($user);

    $busqueda = $request->input('buscar');

    $query = Paciente::query()
        ->select([
            'id',
            'nombre',
            'apellido_paterno',
            'apellido_materno',
            'curp',
            'fecha_nacimiento',
            'sexo',
        ])
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
        ->with(['ultimoSignoVital' => function ($q) {
            
            $q->select([
                'signos_vitales.id',
                'signos_vitales.paciente_id',
                'signos_vitales.temperatura',
                'signos_vitales.triage',
                'signos_vitales.created_at',
            ]);
        }]);

    if ($busqueda) {
        $query->where(function ($q) use ($busqueda) {
            $q->where('nombre', 'like', "%{$busqueda}%")
              ->orWhere('apellido_paterno', 'like', "%{$busqueda}%")
              ->orWhere('apellido_materno', 'like', "%{$busqueda}%")
              ->orWhere('curp', 'like', "%{$busqueda}%");
        });
    }

    $pacientes = $query->orderBy('apellido_paterno')
        ->orderBy('nombre')
        ->paginate(15)
        ->withQueryString();

    if ($request->boolean('_partial') || $request->ajax()) {
        return view('expedientes._tabla', compact('pacientes', 'busqueda'));
    }

    return view('expedientes.index', compact('pacientes', 'busqueda'));
}
    /**
     * Historial completo de un paciente.
     * - Admin: ve todas las consultas.
     * - Médico: solo las que él hizo.
     */
    public function show(Request $request, Paciente $paciente): View
    {
        $user     = auth()->user();
        $esAdmin  = $user->hasRole('administrador');
        $esMedico = $this->esMedico($user);

      
        $cacheKey = "expediente.resumen.{$paciente->id}." . ($esAdmin ? 'admin' : 'medico_' . $user->id);

        $resumen = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($paciente, $esAdmin, $esMedico, $user) {
            $q = $paciente->consultas();

            if (!$esAdmin && $esMedico) {
                $q->where('medico_id', $user->id);
            }

            return [
                'total_consultas' => $q->count(),
                'ultima_consulta' => (clone $q)->latest()->select('id', 'created_at')->first(),
                'total_signos'    => $paciente->signosVitales()->count(),
                'total_citas'     => $paciente->citas()->count(),
            ];
        });

       
        $consultasQuery = Consulta::query()
            ->where('paciente_id', $paciente->id)
            
            ->select([
                'id',
                'paciente_id',
                'medico_id',
                'cita_id',
                'diagnostico_principal_id',
                'diagnostico_secundario_id',
                'estado',
                'finalizada_en',
                'created_at',
            ])
            ->with([
                'medico:id,nombre,apellido_paterno,apellido_materno',
                'diagnosticoPrincipal:id,codigo,nombre',
                'diagnosticoSecundario:id,codigo,nombre',
            ])
            ->orderByDesc('created_at');

        // Filtro por rol
        if (!$esAdmin && $esMedico) {
            $consultasQuery->where('medico_id', $user->id);
        }

        // Filtro opcional por estado (borrador/finalizada)
        if ($request->filled('estado')) {
            $consultasQuery->where('estado', $request->input('estado'));
        }

        // Filtro opcional por rango de fechas
        if ($request->filled('desde')) {
            $consultasQuery->whereDate('created_at', '>=', $request->input('desde'));
        }
        if ($request->filled('hasta')) {
            $consultasQuery->whereDate('created_at', '<=', $request->input('hasta'));
        }

        $consultas = $consultasQuery
            ->paginate(15)
            ->withQueryString();

        
        $signosVitales = $paciente->signosVitales()
            ->select([
                'id',
                'paciente_id',
                'temperatura',
                'frecuencia_cardiaca',
                'frecuencia_respiratoria',
                'presion_arterial',
                'saturacion_oxigeno',
                'peso',
                'talla',
                'triage',
                'created_at',
            ])
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

       
        $citas = $paciente->citas()
            ->select([
                'id',
                'paciente_id',
                'medico_id',
                'fecha_hora',
                'estado',
            ])
            ->with('medico:id,nombre,apellido_paterno,apellido_materno')
            ->orderByDesc('fecha_hora')
            ->limit(20)
            ->get();

        return view('expedientes.show', compact(
            'paciente',
            'resumen',
            'consultas',
            'signosVitales',
            'citas',
            'esAdmin',
            'esMedico'
        ));
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
