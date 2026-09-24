<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Especialidad;
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
    public function index(Request $request): View
{
    $user = auth()->user();
    $esAdmin = $user->hasRole('administrador');
    $esMedico = $this->esMedico($user);

    $fecha = $request->filled('fecha')
        ? Carbon::parse($request->fecha)
        : Carbon::today();

    $inicioSemana = $fecha->copy()->startOfWeek(Carbon::MONDAY);
    $finSemana = $fecha->copy()->endOfWeek(Carbon::SUNDAY);

    // Citas de la semana
    $query = Cita::with(['paciente', 'medico', 'turno', 'especialidad'])
        ->whereBetween('fecha_hora', [$inicioSemana, $finSemana]);

    if ($esMedico && !$esAdmin) {
        $query->where('medico_id', $user->id);
    }

    $citas = $query->orderBy('fecha_hora')->get();

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

    // 👇 PACIENTES DISPONIBLES PARA EL MODAL
    $pacientesQuery = Paciente::whereHas('signosVitales', function ($q) {
            $q->whereIn('id', function ($sub) {
                $sub->selectRaw('MAX(id)')
                    ->from('signos_vitales')
                    ->groupBy('paciente_id');
            });
        })
        // Excluir si tiene cita activa
        ->whereDoesntHave('citas', function ($q) {
            $q->whereIn('estado', ['programada', 'confirmada', 'en_curso']);
        })
        // 👇 Excluir si YA tiene una consulta finalizada (Opción C)
        ->whereDoesntHave('consultas', function ($q) {
            $q->where('estado', 'finalizada');
        })
        ->with([
            'medicoAsignado.medico',
            'signosVitales' => fn($q) => $q->orderByDesc('created_at')->limit(1),
        ])
        ->orderBy('apellido_paterno');

    if (!$esAdmin && $esMedico) {
        $pacientesQuery->where(function ($q) use ($user) {
            $q->whereHas('citas', fn($sub) => $sub->where('medico_id', $user->id))
              ->orWhereHas('consultas', fn($sub) => $sub->where('medico_id', $user->id))
              ->orWhereHas('medicoAsignado', fn($sub) => $sub->where('medico_id', $user->id));
        });
    }

    $pacientes = $pacientesQuery->get();

    // Especialidades
    $especialidades = Especialidad::where('activo', true)
        ->orderBy('nombre')
        ->get();

    return view('agenda.index', compact(
        'fecha', 'inicioSemana', 'finSemana', 'citasPorDia', 'stats',
        'pacientes', 'especialidades'
    ));
}
    public function dia(Request $request): View
    {
        $fecha = $request->filled('fecha')
            ? Carbon::parse($request->fecha)
            : Carbon::today();

        $user = auth()->user();

        $query = Cita::with(['paciente', 'medico', 'turno', 'especialidad'])
            ->whereDate('fecha_hora', $fecha);

        if ($this->esMedico($user) && !$user->hasRole('administrador')) {
            $query->where('medico_id', $user->id);
        }

        $citas = $query->orderBy('fecha_hora')->get();

        return view('agenda.dia', compact('fecha', 'citas'));
    }

  public function create(Request $request): View
{
    $user = auth()->user();
    $esAdmin = $user->hasRole('administrador');
    $esMedico = $this->esMedico($user);

    $query = Paciente::whereHas('signosVitales', function ($q) {
            // Solo pacientes con al menos un signo vital
            $q->whereIn('id', function ($sub) {
                $sub->selectRaw('MAX(id)')
                    ->from('signos_vitales')
                    ->groupBy('paciente_id');
            });
        })
        // ✅ Excluir si tiene cita activa
        ->whereDoesntHave('citas', function ($q) {
            $q->whereIn('estado', ['programada', 'confirmada', 'en_curso']);
        })
        // ✅ Excluir si YA fue atendido Y no hay signos vitales posteriores
        ->whereDoesntHave('citas', function ($q) {
            $q->where('estado', 'atendida')
              ->whereRaw('citas.updated_at >= (
                  SELECT MAX(signos_vitales.created_at)
                  FROM signos_vitales
                  WHERE signos_vitales.paciente_id = citas.paciente_id
              )');
        })
        // ✅ Excluir si YA tiene consulta finalizada Y no hay signos vitales posteriores
        ->whereDoesntHave('consultas', function ($q) {
            $q->where('estado', 'finalizada')
              ->whereRaw('consultas.created_at >= (
                  SELECT MAX(signos_vitales.created_at)
                  FROM signos_vitales
                  WHERE signos_vitales.paciente_id = consultas.paciente_id
              )');
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

    $especialidades = Especialidad::where('activo', true)
        ->orderBy('nombre')
        ->get();

    return view('agenda.create', [
        'pacientes' => $pacientes,
        'especialidades' => $especialidades,
        'fechaSeleccionada' => $request->input('fecha', now()->format('Y-m-d')),
        'horaSeleccionada' => $request->input('hora', '09:00'),
    ]);
}

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'paciente_id' => ['required', 'exists:pacientes,id'],
            'medico_id' => ['required', 'exists:users,id'],
            'especialidad_id' => ['nullable', 'exists:especialidades,id'],
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

        // 3. Conflicto de horario
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
                'medico_id' => 'Este médico ya tiene una cita en ese horario. Elige otra hora u otro médico.'
            ])->withInput();
        }

        // 4. El paciente no debe tener otra cita activa
        $pacienteConCita = Cita::where('paciente_id', $data['paciente_id'])
            ->whereIn('estado', ['programada', 'confirmada', 'en_curso'])
            ->exists();

        if ($pacienteConCita) {
            return back()->withErrors(['paciente_id' => 'Este paciente ya tiene una cita activa.'])->withInput();
        }

        // 5. Bloqueo paciente → médico
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

        // 7. Detectar turno
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
            'especialidad_id' => $data['especialidad_id'] ?? null,
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

    /**
     * Endpoint AJAX: médicos disponibles.
     */
    public function medicosDisponibles(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'fecha' => ['required', 'date'],
                'hora' => ['required', 'date_format:H:i'],
                'paciente_id' => ['nullable', 'exists:pacientes,id'],
                'especialidad_id' => ['nullable', 'exists:especialidades,id'],
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

            // 1. Query base: candidatos con rol médico
            $candidatosQuery = User::query()
                ->whereHas('roles', function ($q) {
                    $q->whereRaw('LOWER(name) LIKE ?', ['%medic%'])
                      ->orWhereRaw('LOWER(name) LIKE ?', ['%doctor%'])
                      ->orWhereRaw('LOWER(name) LIKE ?', ['%médic%']);
                })
                ->where('activo', true);

            // 2. Filtro por especialidad (usa whereHas porque es query, no colección)
            if ($request->filled('especialidad_id')) {
                $candidatosQuery->whereHas('especialidades', function ($q) use ($request) {
                    $q->where('especialidades.id', $request->especialidad_id)
                      ->where('especialidad_user.activo', true);
                });
            }

            // 3. Obtener la colección
            $candidatos = $candidatosQuery->get();

            // 4. Filtrar por turno
            $disponibles = $candidatos->filter(function ($m) use ($fecha, $hora) {
                try {
                    return $m->trabajaEn($fecha->toDateString(), $hora);
                } catch (\Throwable $e) {
                    \Log::warning('trabajaEn error médico ' . $m->id . ': ' . $e->getMessage());
                    return false;
                }
            });

            // 5. Marcar como ocupados
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
                    'especialidades' => $m->especialidades->pluck('nombre')->toArray(),
                    'ocupado' => $ocupado,
                ];
            });

            // 6. Bloqueo paciente → médico (solo futuro)
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
     * Endpoint AJAX: pacientes disponibles.
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