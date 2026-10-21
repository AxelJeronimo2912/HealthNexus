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
use Illuminate\Support\Facades\DB;

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

        $pacientesDadosDeAlta = Seguimiento::where('tipo', 'alta')
            ->where('created_at', '>=', now()->subHours(24))
            ->pluck('paciente_id')
            ->unique();

        // Unión
        $pacientesIds = $pacientesConCama->merge($pacientesConTriage)->merge($pacientesDadosDeAlta)->unique();

        if (!$esAdmin) {
            if ($esMedico) {
                $pacientesIds = $pacientesIds->filter(function ($pacienteId) use ($user) {
                    return Consulta::where('paciente_id', $pacienteId)->where('medico_id', $user->id)->exists()
                        || Cita::where('paciente_id', $pacienteId)->where('medico_id', $user->id)->exists();
                });
            } elseif ($esEnfermeria) {
                $pacientesIds = $pacientesConCama;
            } else {
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
        } elseif ($filtro === 'alta') {
            $query->whereIn('id', $pacientesDadosDeAlta);
        }

        if ($busqueda) {
            $query->where(function ($q) use ($busqueda) {
                $q->where('nombre', 'like', "%{$busqueda}%")
                  ->orWhere('apellido_paterno', 'like', "%{$busqueda}%")
                  ->orWhere('apellido_materno', 'like', "%{$busqueda}%");
            });
        }

        $pacientes = $query->orderBy('apellido_paterno')->paginate(15)->withQueryString();

        // Adjuntar cama actual y estado de alta
        $pacientes->getCollection()->transform(function ($paciente) {
            $paciente->cama_actual = CamaPaciente::with('cama')
                ->where('paciente_id', $paciente->id)
                ->where('activa', true)
                ->first();
                
            $paciente->alta_reciente = Seguimiento::where('paciente_id', $paciente->id)
                ->where('tipo', 'alta')
                ->where('created_at', '>=', now()->subHours(24))
                ->exists();

            return $paciente;
        });

        $stats = [
            'total' => $pacientesIds->count(),
            'con_cama' => $pacientesConCama->intersect($pacientesIds)->count(),
            'con_triage' => $pacientesConTriage->intersect($pacientesIds)->count(),
            'altas' => $pacientesDadosDeAlta->intersect($pacientesIds)->count(), // Nueva stat
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

        // Calcular la diferencia de signos vitales
        foreach ($seguimientos as $index => $seg) {
            $seg->anterior = $seguimientos->get($index + 1);

            if ($seg->anterior) {
                if ($seg->temperatura && $seg->anterior->temperatura) {
                    $seg->temp_diff = $seg->temperatura - $seg->anterior->temperatura;
                }
                if ($seg->frecuencia_cardiaca && $seg->anterior->frecuencia_cardiaca) {
                    $seg->fc_diff = $seg->frecuencia_cardiaca - $seg->anterior->frecuencia_cardiaca;
                }
                if ($seg->saturacion_oxigeno && $seg->anterior->saturacion_oxigeno) {
                    $seg->sat_diff = $seg->saturacion_oxigeno - $seg->anterior->saturacion_oxigeno;
                }
            }
        }

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

        if ($request->tipo === 'alta') {
            $tieneCama = CamaPaciente::where('paciente_id', $paciente->id)
                ->where('activa', true)
                ->exists();

            if (!$tieneCama) {
                return back()->withErrors(['tipo' => 'No puedes dar de alta a un paciente que no tiene una cama activa asignada.'])->withInput();
            }
        }

        $data = $request->validate([
            'tipo' => ['required', 'in:evolucion,nota_enfermeria,interconsulta,traslado,alta'],
            'estado_paciente' => ['nullable', 'in:estable,mejorando,grave,critico,fallecido'],
            'contenido' => ['required', 'string', 'min:10', 'max:5000'],

            'temperatura' => ['required', 'numeric', 'min:34', 'max:42'], 
            'frecuencia_cardiaca' => ['required', 'integer', 'min:30', 'max:200'],
            'frecuencia_respiratoria' => ['required', 'integer', 'min:8', 'max:60'],
            'presion_arterial' => ['required', 'string', 'regex:/^\d{2,3}\/\d{2,3}$/'], 
            'saturacion_oxigeno' => ['required', 'integer', 'min:50', 'max:100'],
        ], [
            'tipo.required' => 'Debes seleccionar el tipo de seguimiento.',
            'contenido.required' => 'El contenido de la nota es obligatorio.',
            'contenido.min' => 'La nota médica debe ser más detallada (mínimo 10 caracteres).',
            
            'temperatura.required' => 'La temperatura es obligatoria.',
            'temperatura.min' => 'La temperatura no puede ser menor a 34°C.',
            'temperatura.max' => 'La temperatura no puede ser mayor a 42°C.',
            
            'frecuencia_cardiaca.required' => 'La frecuencia cardíaca es obligatoria.',
            'frecuencia_cardiaca.min' => 'La frecuencia cardíaca parece demasiado baja.',
            'frecuencia_cardiaca.max' => 'La frecuencia cardíaca parece demasiado alta.',
            
            'frecuencia_respiratoria.required' => 'La frecuencia respiratoria es obligatoria.',
            'frecuencia_respiratoria.min' => 'La frecuencia respiratoria parece demasiado baja.',
            'frecuencia_respiratoria.max' => 'La frecuencia respiratoria parece demasiado alta.',
            
            'presion_arterial.required' => 'La presión arterial es obligatoria.',
            'presion_arterial.regex' => 'El formato de la presión arterial debe ser similar a 120/80.',
            
            'saturacion_oxigeno.required' => 'La saturación de oxígeno es obligatoria.',
            'saturacion_oxigeno.max' => 'La saturación no puede superar el 100%.',
            'saturacion_oxigeno.min' => 'La saturación de oxígeno es peligrosamente baja (mínimo 50%).',
        ]);

        $cama = CamaPaciente::where('paciente_id', $paciente->id)
            ->where('activa', true)
            ->first();

        $data['paciente_id'] = $paciente->id;
        $data['user_id'] = auth()->id();
        $data['cama_id'] = $cama?->cama_id;

        Seguimiento::create($data);

        // Liberar cama si es alta
        if ($data['tipo'] === 'alta' && $cama) {
            $cama->update(['activa' => false, 'fecha_salida' => now()]);
        }

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


        public function timeline(Paciente $paciente)
    {
        $this->autorizarAcceso($paciente);

        $eventos = collect();

        // 1. Cargar Citas
        $citas = Cita::where('paciente_id', $paciente->id)->with('medico')->get();
        foreach ($citas as $cita) {
            $eventos->push([
                'fecha' => $cita->created_at,
                'tipo' => 'cita',
                'titulo' => 'Cita Programada',
                'descripcion' => 'Motivo: ' . ($cita->motivo ?? 'No especificado'),
                'autor' => $cita->medico?->nombre_completo ?? 'Sistema',
                'icono' => 'calendar',
                'color' => 'indigo'
            ]);
        }

        // 2. Cargar Consultas
        $consultas = Consulta::where('paciente_id', $paciente->id)->with('medico')->get();
        foreach ($consultas as $consulta) {
            $eventos->push([
                'fecha' => $consulta->created_at,
                'tipo' => 'consulta',
                'titulo' => 'Consulta Médica',
                'descripcion' => 'Diagnóstico: ' . ($consulta->diagnosticoPrincipal?->etiqueta ?? 'Sin diagnóstico'),
                'autor' => $consulta->medico?->nombre_completo ?? 'Sistema',
                'icono' => 'document',
                'color' => 'emerald'
            ]);
        }

        // 3. Cargar Seguimientos y Calcular Triage
        $seguimientos = Seguimiento::where('paciente_id', $paciente->id)->with('user')->orderBy('created_at')->get();
        $seguimientosGrafica = collect();

        foreach ($seguimientos as $index => $seg) {
            $triageColor = 'bg-emerald-100 text-emerald-700';
            $triageLabel = 'Estable';
            
            if ($seg->saturacion_oxigeno && $seg->saturacion_oxigeno < 90) {
                $triageColor = 'bg-rose-100 text-rose-700'; $triageLabel = 'Crítico';
            } elseif ($seg->temperatura && $seg->temperatura >= 39) {
                $triageColor = 'bg-rose-100 text-rose-700'; $triageLabel = 'Crítico';
            } elseif ($seg->temperatura && $seg->temperatura >= 37.5) {
                $triageColor = 'bg-amber-100 text-amber-700'; $triageLabel = 'Fiebre';
            } elseif ($seg->frecuencia_cardiaca && $seg->frecuencia_cardiaca > 100) {
                $triageColor = 'bg-amber-100 text-amber-700'; $triageLabel = 'Taquicardia';
            }

            $eventos->push([
                'fecha' => $seg->created_at,
                'tipo' => 'seguimiento',
                'titulo' => $seg->tipo_label,
                'descripcion' => $seg->contenido,
                'autor' => $seg->user?->nombre_completo ?? 'Sistema',
                'icono' => 'heart',
                'color' => 'rose',
                'triage_color' => $triageColor,
                'triage_label' => $triageLabel,
                'signos' => [
                    'temp' => $seg->temperatura,
                    'fc' => $seg->frecuencia_cardiaca,
                    'ta' => $seg->presion_arterial,
                    'spo2' => $seg->saturacion_oxigeno,
                ]
            ]);

            // Datos para la gráfica
            if ($seg->temperatura || $seg->frecuencia_cardiaca) {
                $seguimientosGrafica->push([
                    'fecha' => $seg->created_at->format('d M'),
                    'temperatura' => (float) $seg->temperatura,
                    'frecuencia_cardiaca' => (int) $seg->frecuencia_cardiaca,
                ]);
            }
        }

        $eventos = $eventos->sortByDesc('fecha')->values();

        if (request()->ajax()) {
            return view('seguimientos.partials._timeline-modal', compact('paciente', 'eventos', 'seguimientosGrafica'))->render();
        }

        return view('seguimientos.timeline', compact('paciente', 'eventos', 'seguimientosGrafica'));
    }


        /**
     * Procesa el alta médica del paciente.
     */
    public function darDeAlta(Paciente $paciente): RedirectResponse
    {
        $this->autorizarAcceso($paciente);

        // 1. Buscar si tiene cama activa
        $cama = CamaPaciente::where('paciente_id', $paciente->id)
            ->where('activa', true)
            ->first();

        if (!$cama) {
            return back()->with('error', 'El paciente no tiene una cama activa asignada para dar de alta.');
        }

        DB::beginTransaction();

        try {
            // 2. Crear un seguimiento automático de "Alta"
            Seguimiento::create([
                'paciente_id' => $paciente->id,
                'user_id' => auth()->id(),
                'cama_id' => $cama->cama_id,
                'tipo' => 'alta',
                'contenido' => 'Paciente dado de alta médica desde el módulo de seguimiento. Se libera la cama ' . $cama->cama->codigo,
                'estado_paciente' => 'estable', // Asumimos que sale estable
            ]);

            // 3. Liberar la cama
            $cama->update(['activa' => false, 'fecha_salida' => now()]);

            DB::commit();

            return redirect()->route('seguimientos.index')
                ->with('success', 'Paciente dado de alta correctamente. La cama ha sido liberada.');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Error al dar de alta: ' . $e->getMessage());
        }
    }
}
