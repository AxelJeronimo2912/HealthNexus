<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Paciente;
use App\Models\PacienteMedico;
use App\Models\SignoVital;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AgendaController extends Controller
{
    /**
     * Vista principal del calendario semanal.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();

        $fecha = $request->filled('fecha')
            ? Carbon::parse($request->fecha)
            : Carbon::today();

        $inicioSemana = $fecha->copy()->startOfWeek(Carbon::MONDAY);
        $finSemana = $fecha->copy()->endOfWeek(Carbon::SUNDAY);

        $query = Cita::with(['paciente', 'medico', 'turno'])
            ->whereBetween('fecha_hora', [$inicioSemana, $finSemana]);

        if ($this->esMedico($user) && !$user->hasRole('administrador')) {
            $query->where('medico_id', $user->id);
        }

        $citas = $query->orderBy('fecha_hora')->get();

        // Agrupar por día
        $citasPorDia = [];
        for ($d = $inicioSemana->copy(); $d->lte($finSemana); $d->addDay()) {
            $key = $d->format('Y-m-d');
            $citasPorDia[$key] = $citas->filter(fn($c) => $c->fecha_hora->format('Y-m-d') === $key);
        }

        $stats = [
            'total_semana' => $citas->count(),
            'programadas' => $citas->where('estado', 'programada')->count(),
            'atendidas' => $citas->where('estado', 'atendida')->count(),
            'canceladas' => $citas->where('estado', 'cancelada')->count(),
        ];

        return view('agenda.index', compact(
            'fecha', 'inicioSemana', 'finSemana', 'citasPorDia', 'stats'
        ));
    }

    /**
     * Vista de un día específico.
     */
    public function dia(Request $request): View
    {
        $fecha = $request->filled('fecha')
            ? Carbon::parse($request->fecha)
            : Carbon::today();

        $user = auth()->user();

        $query = Cita::with(['paciente', 'medico', 'turno'])
            ->whereDate('fecha_hora', $fecha);

        if ($this->esMedico($user) && !$user->hasRole('administrador')) {
            $query->where('medico_id', $user->id);
        }

        $citas = $query->orderBy('fecha_hora')->get();

        return view('agenda.dia', compact('fecha', 'citas'));
    }

    /**
     * Formulario para nueva cita.
     * Incluye pacientes con CUALQUIER triage (rojo, naranja, amarillo, verde, azul)
     * que aún no tengan una cita activa.
     */
   public function create(Request $request): View
    {
        $pacientes = Paciente::whereHas('signosVitales', function ($q) {
                // No filtramos por triage → aparecen TODOS.
                $q->whereIn('id', function ($sub) {
                    $sub->selectRaw('MAX(id)')
                        ->from('signos_vitales')
                        ->groupBy('paciente_id');
                });
            })
            ->whereDoesntHave('citas', function ($q) {
                $q->whereIn('estado', ['programada', 'confirmada', 'en_curso']);
            })
            ->whereDoesntHave('consultas', function ($q) {
                $q->whereIn('estado', ['borrador', 'finalizada']);
            })
            ->with([
                'medicoAsignado.medico',
                'signosVitales' => fn($q) => $q->orderByDesc('created_at')->limit(1),
            ])
            ->orderBy('apellido_paterno')
            ->get();

        return view('agenda.create', [
            'pacientes' => $pacientes,
            'fechaSeleccionada' => $request->input('fecha', now()->format('Y-m-d')),
            'horaSeleccionada' => $request->input('hora', '09:00'),
        ]);
    }

    /**
     * Guarda la cita.
     */
   public function store(Request $request): RedirectResponse
{
    $data = $request->validate([
        'paciente_id' => ['required', 'exists:pacientes,id'],
        'medico_id' => ['required', 'exists:users,id'],
        'fecha' => ['required', 'date'],
        'hora' => ['required', 'date_format:H:i'],
        'duracion_minutos' => ['required', 'integer', 'min:15', 'max:180'],
        'motivo' => ['nullable', 'string'],
        'notas' => ['nullable', 'string'],
    ]);

    $duracion = (int) $data['duracion_minutos'];
    $fechaHora = Carbon::parse("{$data['fecha']} {$data['hora']}");

    // 1. No fechas pasadas
    if ($fechaHora->isBefore(today())) {
        return back()->withErrors(['fecha' => 'No puedes agendar citas en fechas pasadas.'])->withInput();
    }

    // 2. El médico debe trabajar en ese horario
    $medico = User::findOrFail($data['medico_id']);

    if (!$medico->trabajaEn($fechaHora->toDateString(), $fechaHora->format('H:i'))) {
        return back()->withErrors([
            'medico_id' => 'El médico no tiene turno asignado en ese horario.'
        ])->withInput();
    }

    // 3. CONFLICTO DE HORARIO → SIEMPRE se valida (hoy y futuro)
    $conflicto = Cita::where('medico_id', $medico->id)
        ->whereIn('estado', ['programada', 'confirmada', 'en_curso'])
        ->where(function ($q) use ($fechaHora, $duracion) {
            $fin = $fechaHora->copy()->addMinutes($duracion);
            $q->whereBetween('fecha_hora', [$fechaHora, $fin])
              ->orWhere(function ($sub) use ($fechaHora) {
                  $sub->where('fecha_hora', '<', $fechaHora)
                      ->whereRaw('DATE_ADD(fecha_hora, INTERVAL duracion_minutos MINUTE) > ?', [$fechaHora]);
              });
        })
        ->exists();

    if ($conflicto) {
        return back()->withErrors([
            'medico_id' => 'Este médico ya tiene una cita en ese horario. Elige otra hora o otro médico.'
        ])->withInput();
    }

    // 4. El paciente no debe tener otra cita activa
    $pacienteConCita = Cita::where('paciente_id', $data['paciente_id'])
        ->whereIn('estado', ['programada', 'confirmada', 'en_curso'])
        ->exists();

    if ($pacienteConCita) {
        return back()->withErrors(['paciente_id' => 'Este paciente ya tiene una cita activa.'])->withInput();
    }

    // 5. Bloqueo paciente → médico (SOLO para futuro, no para hoy)
    $paciente = Paciente::findOrFail($data['paciente_id']);
    $esHoy = $fechaHora->isToday();

    $asignacionActual = null;

    if (!$esHoy) {
        $asignacionActual = PacienteMedico::where('paciente_id', $paciente->id)
            ->where('activo', true)
            ->first();

        if ($asignacionActual && $asignacionActual->medico_id !== $medico->id) {
            return back()->withErrors([
                'medico_id' => 'Este paciente ya está asignado al Dr. ' .
                    ($asignacionActual->medico->nombre_completo ?? 'otro médico') .
                    '. Solo ese médico puede atenderlo.'
            ])->withInput();
        }
    }

    // 6. Último signo vital
    $ultimoSigno = SignoVital::where('paciente_id', $paciente->id)
        ->orderByDesc('created_at')
        ->first();

    // 7. Detectar turno del médico
    $horaCita = $fechaHora->format('H:i');
    $turno = $medico->turnosEn($fechaHora->toDateString())
        ->first(function ($t) use ($horaCita) {
            $horaInicio = substr($t->hora_inicio, 0, 5);
            $horaFin = substr($t->hora_fin, 0, 5);
            return $horaCita >= $horaInicio && $horaCita <= $horaFin;
        });

    // 8. Crear la cita
    Cita::create([
        'paciente_id' => $paciente->id,
        'medico_id' => $medico->id,
        'turno_id' => $turno?->id,
        'signo_vital_id' => $ultimoSigno?->id,
        'creado_por' => auth()->id(),
        'fecha_hora' => $fechaHora,
        'duracion_minutos' => $duracion,
        'estado' => 'programada',
        'triage_al_momento' => $ultimoSigno?->triage,
        'motivo' => $data['motivo'] ?? null,
        'notas' => $data['notas'] ?? null,
    ]);

    // 9. Bloqueo paciente→médico solo si NO es hoy
    if (!$esHoy && !$asignacionActual) {
        PacienteMedico::create([
            'paciente_id' => $paciente->id,
            'medico_id' => $medico->id,
            'asignado_por' => auth()->id(),
            'asignado_en' => now(),
            'activo' => true,
        ]);
    }

    return redirect()->route('agenda.index')
        ->with('success', 'Cita agendada correctamente.');
}

public function medicosDisponibles(Request $request): JsonResponse
{
    try {
        $request->validate([
            'fecha' => ['required', 'date'],
            'hora' => ['required', 'date_format:H:i'],
            'paciente_id' => ['nullable', 'exists:pacientes,id'],
        ]);

        $fecha = Carbon::parse($request->fecha);

        if ($fecha->isBefore(today())) {
            return response()->json([
                'error' => true,
                'message' => 'No se pueden agendar citas en fechas pasadas.',
            ], 422);
        }

        $hora = $request->hora;
        $esHoy = $fecha->isToday();

        $inicio = Carbon::parse("{$fecha->toDateString()} {$hora}");
        $fin = $inicio->copy()->addMinutes(30);

        // 1. Candidatos: cualquier rol con "medic", "doctor" o "médic"
        $candidatos = User::query()
            ->whereHas('roles', function ($q) {
                $q->whereRaw('LOWER(name) LIKE ?', ['%medic%'])
                  ->orWhereRaw('LOWER(name) LIKE ?', ['%doctor%'])
                  ->orWhereRaw('LOWER(name) LIKE ?', ['%médic%']);
            })
            ->where('activo', true)
            ->get();

        // 2. Filtrar por turno
        $disponibles = $candidatos->filter(function ($m) use ($fecha, $hora) {
            try {
                return $m->trabajaEn($fecha->toDateString(), $hora);
            } catch (\Throwable $e) {
                \Log::warning('trabajaEn error médico ' . $m->id . ': ' . $e->getMessage());
                return false;
            }
        });

        // 3. Marcar como ocupados a los médicos con cita en ese horario
        $conEstado = $disponibles->map(function ($m) use ($inicio, $fin) {
            $ocupado = Cita::where('medico_id', $m->id)
                ->whereIn('estado', ['programada', 'confirmada', 'en_curso'])
                ->where(function ($q) use ($inicio, $fin) {
                    $q->whereBetween('fecha_hora', [$inicio, $fin])
                      ->orWhere(function ($sub) use ($inicio) {
                          $sub->where('fecha_hora', '<', $inicio)
                              ->whereRaw('DATE_ADD(fecha_hora, INTERVAL duracion_minutos MINUTE) > ?', [$inicio]);
                      });
                })
                ->exists();

            return [
                'id' => $m->id,
                'nombre' => $m->nombre_completo ?: $m->name,
                'rol' => $m->getRoleNames()->first() ?? 'sin rol',
                'ocupado' => $ocupado,
            ];
        });

        // 4. Bloqueo paciente → médico (solo para futuro)
        $final = $conEstado;

        if (!$esHoy && $request->filled('paciente_id')) {
            $asignacion = PacienteMedico::where('paciente_id', $request->paciente_id)
                ->where('activo', true)
                ->first();

            if ($asignacion) {
                $final = $final->where('id', $asignacion->medico_id);
            }
        }

        return response()->json($final->values());
    } catch (\Throwable $e) {
        \Log::error('Error en medicosDisponibles: ' . $e->getMessage(), [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);

        return response()->json([
            'error' => true,
            'message' => $e->getMessage(),
        ], 500);
    }
}
    /**
     * Endpoint AJAX: pacientes con signos vitales sin cita activa (cualquier triage).
     */
     
    public function pacientesDisponibles(): JsonResponse
    {
        $user = auth()->user();
        $esAdmin = $user->hasRole('administrador');
        $esMedico = $this->esMedico($user);

        $query = Paciente::whereHas('signosVitales', function ($q) {
                $q->whereIn('id', function ($sub) {
                    $sub->selectRaw('MAX(id)')
                        ->from('signos_vitales')
                        ->groupBy('paciente_id');
                });
            })
            ->whereDoesntHave('citas', function ($q) {
                $q->whereIn('estado', ['programada', 'confirmada', 'en_curso']);
            })
            ->whereDoesntHave('consultas', function ($q) {
                $q->whereIn('estado', ['borrador', 'finalizada']);
            })
            ->with([
                'medicoAsignado.medico',
                'signosVitales' => fn($q) => $q->orderByDesc('created_at')->limit(1),
            ])
            ->orderBy('apellido_paterno');

        if (!$esAdmin && $esMedico) {
            $query->where(function ($q) use ($user) {
                $q->whereHas('citas', fn($sub) => $sub->where('medico_id', $user->id))
                  ->orWhereHas('consultas', fn($sub) => $sub->where('medico_id', $user->id))
                  ->orWhereHas('medicoAsignado', fn($sub) => $sub->where('medico_id', $user->id));
            });
        }

        $pacientes = $query->get();

        return response()->json(
            $pacientes->map(fn($p) => [
                'id' => $p->id,
                'nombre' => $p->nombre_completo,
                'triage' => $p->signosVitales->first()?->triage,
                'medico_asignado' => $p->medicoAsignado?->medico?->nombre_completo,
                'medico_asignado_id' => $p->medicoAsignado?->medico_id,
            ])
        );
    }

    /**
     * Helper: ¿el usuario es médico? (cualquier rol que contenga medic/doctor)
     */
    private function esMedico(User $user): bool
    {
        return $user->roles->contains(function ($rol) {
            $n = strtolower($rol->name);
            return str_contains($n, 'medic')
                || str_contains($n, 'doctor')
                || str_contains($n, 'médic');
        });
    }
}