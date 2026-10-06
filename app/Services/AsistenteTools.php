<?php

namespace App\Services;

use App\Models\Admision;
use App\Models\Cama;
use App\Models\Cita;
use App\Models\Consulta;
use App\Models\Medicamento;
use App\Models\Paciente;
use App\Models\Turno;
use App\Models\User;
use Carbon\Carbon;

class AsistenteTools
{

private static function usuarioActual()
{
    return auth()->user();
}

private static function esAdmin(): bool
{
    return self::usuarioActual()?->hasRole('administrador') ?? false;
}

private static function esMedico(): bool
{
    $user = self::usuarioActual();
    if (!$user) return false;

    return $user->roles->contains(function ($rol) {
        $n = strtolower($rol->name);
        return str_contains($n, 'medic') || str_contains($n, 'doctor') || str_contains($n, 'médic');
    });
}


private static function esEnfermeria(): bool
{
    return self::usuarioActual()?->roles->contains(fn($r) => str_contains(strtolower($r->name), 'enfermer')) ?? false;
}
    public static function definiciones(): array
    {
        return [
            // ===== PACIENTES =====
            [
                'type' => 'function',
                'function' => [
                    'name' => 'contar_pacientes',
                    'description' => 'Cuenta el total de pacientes activos registrados.',
                    'parameters' => ['type' => 'object', 'properties' => new \stdClass(), 'required' => []],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'buscar_paciente',
                    'description' => 'Busca un paciente por nombre, apellido o CURP. Devuelve sus datos básicos, alergias, enfermedades crónicas y médico asignado.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'termino' => ['type' => 'string', 'description' => 'Nombre, apellido o CURP del paciente'],
                        ],
                        'required' => ['termino'],
                    ],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'historial_consultas_paciente',
                    'description' => 'Devuelve las últimas consultas médicas de un paciente específico.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'paciente_id' => ['type' => 'integer', 'description' => 'ID del paciente'],
                            'limite' => ['type' => 'integer', 'description' => 'Número máximo de consultas a devolver', 'default' => 5],
                        ],
                        'required' => ['paciente_id'],
                    ],
                ],
            ],

            // ===== CITAS =====
            [
                'type' => 'function',
                'function' => [
                    'name' => 'citas_hoy',
                    'description' => 'Cuenta las citas programadas para hoy.',
                    'parameters' => ['type' => 'object', 'properties' => new \stdClass(), 'required' => []],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'citas_por_fecha',
                    'description' => 'Devuelve las citas de una fecha específica.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'fecha' => ['type' => 'string', 'description' => 'Fecha en formato YYYY-MM-DD'],
                        ],
                        'required' => ['fecha'],
                    ],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'citas_medico',
                    'description' => 'Devuelve las citas de un médico específico por nombre.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'nombre_medico' => ['type' => 'string', 'description' => 'Nombre o apellido del médico'],
                            'fecha' => ['type' => 'string', 'description' => 'Fecha opcional YYYY-MM-DD'],
                        ],
                        'required' => ['nombre_medico'],
                    ],
                ],
            ],

            // ===== CAMAS =====
            [
                'type' => 'function',
                'function' => [
                    'name' => 'camas_disponibles',
                    'description' => 'Cuenta las camas disponibles, ocupadas y en mantenimiento.',
                    'parameters' => ['type' => 'object', 'properties' => new \stdClass(), 'required' => []],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'camas_por_area',
                    'description' => 'Muestra la disponibilidad de camas por área (Hospitalización, Urgencias, UCI, etc.).',
                    'parameters' => ['type' => 'object', 'properties' => new \stdClass(), 'required' => []],
                ],
            ],

            // ===== MEDICAMENTOS =====
            [
                'type' => 'function',
                'function' => [
                    'name' => 'medicamentos_stock_bajo',
                    'description' => 'Lista los medicamentos con stock por debajo del mínimo.',
                    'parameters' => ['type' => 'object', 'properties' => new \stdClass(), 'required' => []],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'buscar_medicamento',
                    'description' => 'Busca un medicamento por nombre y devuelve stock, presentación, caducidad próxima.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'nombre' => ['type' => 'string', 'description' => 'Nombre del medicamento'],
                        ],
                        'required' => ['nombre'],
                    ],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'medicamentos_proximos_caducar',
                    'description' => 'Lista los medicamentos cuyo lote más próximo vence en menos de 30 días.',
                    'parameters' => ['type' => 'object', 'properties' => new \stdClass(), 'required' => []],
                ],
            ],

            // ===== MEDICAMENTOS: STOCK ALTO =====
[
    'type' => 'function',
    'function' => [
        'name' => 'medicamentos_stock_alto',
        'description' => 'Lista los medicamentos con mayor stock disponible (por encima del promedio o con exceso).',
        'parameters' => [
            'type' => 'object',
            'properties' => [
                'limite' => ['type' => 'integer', 'description' => 'Número máximo de resultados', 'default' => 10],
            ],
            'required' => [],
        ],
    ],
],

// ===== LISTADO COMPLETO DE MEDICAMENTOS =====
[
    'type' => 'function',
    'function' => [
        'name' => 'listar_medicamentos',
        'description' => 'Devuelve el catálogo completo de medicamentos activos con su stock actual.',
        'parameters' => [
            'type' => 'object',
            'properties' => [
                'limite' => ['type' => 'integer', 'description' => 'Número máximo de resultados', 'default' => 20],
            ],
            'required' => [],
        ],
    ],
],

// ===== PACIENTES: LISTADO =====
[
    'type' => 'function',
    'function' => [
        'name' => 'listar_pacientes',
        'description' => 'Devuelve el listado de pacientes activos. Si el usuario es médico, solo los suyos.',
        'parameters' => [
            'type' => 'object',
            'properties' => [
                'limite' => ['type' => 'integer', 'description' => 'Número máximo de resultados', 'default' => 20],
            ],
            'required' => [],
        ],
    ],
],

// ===== CAMAS: LISTADO DETALLADO =====
[
    'type' => 'function',
    'function' => [
        'name' => 'listar_camas',
        'description' => 'Lista las camas del hospital con su estado y área. Filtrable por estado.',
        'parameters' => [
            'type' => 'object',
            'properties' => [
                'estado' => ['type' => 'string', 'description' => 'Filtrar por estado: disponible, ocupada, mantenimiento'],
                'limite' => ['type' => 'integer', 'description' => 'Número máximo de resultados', 'default' => 20],
            ],
            'required' => [],
        ],
    ],
],

// ===== CITAS: LISTADO GENERAL =====
[
    'type' => 'function',
    'function' => [
        'name' => 'listar_citas',
        'description' => 'Lista citas filtradas por estado o rango de fechas.',
        'parameters' => [
            'type' => 'object',
            'properties' => [
                'estado' => ['type' => 'string', 'description' => 'Estado: programada, confirmada, atendida, cancelada'],
                'desde' => ['type' => 'string', 'description' => 'Fecha inicio YYYY-MM-DD'],
                'hasta' => ['type' => 'string', 'description' => 'Fecha fin YYYY-MM-DD'],
                'limite' => ['type' => 'integer', 'description' => 'Número máximo de resultados', 'default' => 20],
            ],
            'required' => [],
        ],
    ],
],

// ===== PERSONAL: LISTADO COMPLETO =====
[
    'type' => 'function',
    'function' => [
        'name' => 'listar_personal',
        'description' => 'Lista todo el personal activo del hospital, filtrable por rol.',
        'parameters' => [
            'type' => 'object',
            'properties' => [
                'rol' => ['type' => 'string', 'description' => 'Filtrar por rol: médico, enfermero, administrativo, etc.'],
                'limite' => ['type' => 'integer', 'description' => 'Número máximo de resultados', 'default' => 20],
            ],
            'required' => [],
        ],
    ],
],

// ===== ADMISIONES: LISTADO =====
[
    'type' => 'function',
    'function' => [
        'name' => 'listar_admisiones',
        'description' => 'Lista admisiones filtradas por estado o fecha.',
        'parameters' => [
            'type' => 'object',
            'properties' => [
                'estado' => ['type' => 'string', 'description' => 'Estado: en_espera, hospitalizado, derivado, alta'],
                'fecha' => ['type' => 'string', 'description' => 'Fecha YYYY-MM-DD'],
                'limite' => ['type' => 'integer', 'description' => 'Número máximo de resultados', 'default' => 20],
            ],
            'required' => [],
        ],
    ],
],

// ===== ESTADÍSTICAS GENERALES =====
[
    'type' => 'function',
    'function' => [
        'name' => 'estadisticas_generales',
        'description' => 'Devuelve estadísticas agregadas: total de pacientes, citas del mes, ocupación promedio, medicamentos totales, personal activo.',
        'parameters' => ['type' => 'object', 'properties' => new \stdClass(), 'required' => []],
    ],
],

// ===== PDFs ADICIONALES =====
[
    'type' => 'function',
    'function' => [
        'name' => 'generar_pdf_medicamentos_stock_alto',
        'description' => 'Genera un PDF con el reporte de medicamentos con stock alto.',
        'parameters' => ['type' => 'object', 'properties' => new \stdClass(), 'required' => []],
    ],
],
[
    'type' => 'function',
    'function' => [
        'name' => 'generar_pdf_pacientes',
        'description' => 'Genera un PDF con el listado completo de pacientes activos.',
        'parameters' => ['type' => 'object', 'properties' => new \stdClass(), 'required' => []],
    ],
],
[
    'type' => 'function',
    'function' => [
        'name' => 'generar_pdf_personal',
        'description' => 'Genera un PDF con el listado de todo el personal activo.',
        'parameters' => ['type' => 'object', 'properties' => new \stdClass(), 'required' => []],
    ],
],
[
    'type' => 'function',
    'function' => [
        'name' => 'generar_pdf_admisiones',
        'description' => 'Genera un PDF con las admisiones de una fecha específica.',
        'parameters' => [
            'type' => 'object',
            'properties' => [
                'fecha' => ['type' => 'string', 'description' => 'Fecha YYYY-MM-DD'],
            ],
            'required' => [],
        ],
    ],
],

            // ===== PERSONAL / TURNOS =====
            [
                'type' => 'function',
                'function' => [
                    'name' => 'medicos_activos',
                    'description' => 'Cuenta y lista los médicos activos del hospital.',
                    'parameters' => ['type' => 'object', 'properties' => new \stdClass(), 'required' => []],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'turnos_de_hoy',
                    'description' => 'Lista el personal (médicos y enfermería) que tiene turno hoy.',
                    'parameters' => ['type' => 'object', 'properties' => new \stdClass(), 'required' => []],
                ],
            ],



            // ===== ADMISIONES =====
            [
                'type' => 'function',
                'function' => [
                    'name' => 'admisiones_de_hoy',
                    'description' => 'Cuenta las admisiones registradas hoy y su estado.',
                    'parameters' => ['type' => 'object', 'properties' => new \stdClass(), 'required' => []],
                ],
            ],

            // ===== RESUMEN GENERAL =====
            [
                'type' => 'function',
                'function' => [
                    'name' => 'resumen_hospital',
                    'description' => 'Devuelve un resumen general del estado del hospital: pacientes, camas, citas del día, medicamentos críticos.',
                    'parameters' => ['type' => 'object', 'properties' => new \stdClass(), 'required' => []],
                ],
            ],

            [
    'type' => 'function',
    'function' => [
        'name' => 'prediccion_agotamiento',
        'description' => 'Analiza qué medicamentos se van a agotar pronto según su consumo histórico. Devuelve una lista con días estimados y cantidad sugerida de reabastecimiento.',
        'parameters' => ['type' => 'object', 'properties' => new \stdClass(), 'required' => []],
    ],
],
[
    'type' => 'function',
    'function' => [
        'name' => 'medicamentos_mayor_demanda',
        'description' => 'Devuelve los medicamentos con mayor demanda (más dispensados) en el último mes.',
        'parameters' => [
            'type' => 'object',
            'properties' => [
                'dias' => ['type' => 'integer', 'description' => 'Días hacia atrás para analizar', 'default' => 30],
            ],
            'required' => [],
        ],
    ],
],


[
    'type' => 'function',
    'function' => [
        'name' => 'generar_pdf_pacientes_medico',
        'description' => 'Genera un PDF con el reporte de pacientes de un médico específico.',
        'parameters' => [
            'type' => 'object',
            'properties' => [
                'nombre_medico' => ['type' => 'string', 'description' => 'Nombre o apellido del médico'],
            ],
            'required' => ['nombre_medico'],
        ],
    ],
],
[
    'type' => 'function',
    'function' => [
        'name' => 'generar_pdf_citas_dia',
        'description' => 'Genera un PDF con las citas de una fecha específica.',
        'parameters' => [
            'type' => 'object',
            'properties' => [
                'fecha' => ['type' => 'string', 'description' => 'Fecha en formato YYYY-MM-DD'],
            ],
            'required' => ['fecha'],
        ],
    ],
],
[
    'type' => 'function',
    'function' => [
        'name' => 'generar_pdf_inventario_bajo',
        'description' => 'Genera un PDF con el reporte de medicamentos con stock bajo.',
        'parameters' => ['type' => 'object', 'properties' => new \stdClass(), 'required' => []],
    ],
],

        ];
    }

   public static function ejecutar(string $nombre, array $args = []): array
{
    return match ($nombre) {
        // Pacientes
        'contar_pacientes' => self::contarPacientes(),
        'buscar_paciente' => self::buscarPaciente($args['termino'] ?? ''),
        'historial_consultas_paciente' => self::historialConsultasPaciente($args['paciente_id'] ?? 0, $args['limite'] ?? 5),
        'listar_pacientes' => self::listarPacientes($args['limite'] ?? 20),

        // Citas
        'citas_hoy' => self::citasHoy(),
        'citas_por_fecha' => self::citasPorFecha($args['fecha'] ?? today()->format('Y-m-d')),
        'citas_medico' => self::citasMedico($args['nombre_medico'] ?? '', $args['fecha'] ?? null),
        'listar_citas' => self::listarCitas($args['estado'] ?? null, $args['desde'] ?? null, $args['hasta'] ?? null, $args['limite'] ?? 20),

        // Camas
        'camas_disponibles' => self::camasDisponibles(),
        'camas_por_area' => self::camasPorArea(),
        'listar_camas' => self::listarCamas($args['estado'] ?? null, $args['limite'] ?? 20),

        // Medicamentos
        'medicamentos_stock_bajo' => self::medicamentosStockBajo(),
        'medicamentos_stock_alto' => self::medicamentosStockAlto($args['limite'] ?? 10),
        'buscar_medicamento' => self::buscarMedicamento($args['nombre'] ?? ''),
        'medicamentos_proximos_caducar' => self::medicamentosProximosCaducar(),
        'listar_medicamentos' => self::listarMedicamentos($args['limite'] ?? 20),
        'prediccion_agotamiento' => self::prediccionAgotamiento(),
        'medicamentos_mayor_demanda' => self::medicamentosMayorDemanda($args['dias'] ?? 30),

        // Personal
        'medicos_activos' => self::medicosActivos(),
        'turnos_de_hoy' => self::turnosDeHoy(),
        'listar_personal' => self::listarPersonal($args['rol'] ?? null, $args['limite'] ?? 20),

        // Admisiones
        'admisiones_de_hoy' => self::admisionesDeHoy(),
        'listar_admisiones' => self::listarAdmisiones($args['estado'] ?? null, $args['fecha'] ?? null, $args['limite'] ?? 20),

        // Resumen y estadísticas
        'resumen_hospital' => self::resumenHospital(),
        'estadisticas_generales' => self::estadisticasGenerales(),

        // PDFs
        'generar_pdf_pacientes_medico' => self::generarPdfPacientesMedico($args['nombre_medico'] ?? ''),
        'generar_pdf_citas_dia' => self::generarPdfCitasDia($args['fecha'] ?? today()->format('Y-m-d')),
        'generar_pdf_inventario_bajo' => self::generarPdfInventarioBajo(),
        'generar_pdf_medicamentos_stock_alto' => self::generarPdfMedicamentosStockAlto(),
        'generar_pdf_pacientes' => self::generarPdfPacientes(),
        'generar_pdf_personal' => self::generarPdfPersonal(),
        'generar_pdf_admisiones' => self::generarPdfAdmisiones($args['fecha'] ?? today()->format('Y-m-d')),

        default => ['error' => 'Herramienta desconocida: ' . $nombre],
    };
}

    // ============ PACIENTES ============

    private static function contarPacientes(): array
{
    $query = Paciente::where('activo', true);

    if (self::esMedico() && !self::esAdmin()) {
        $userId = self::usuarioActual()->id;
        $query->where(function ($q) use ($userId) {
            $q->whereHas('citas', fn($sub) => $sub->where('medico_id', $userId))
              ->orWhereHas('consultas', fn($sub) => $sub->where('medico_id', $userId))
              ->orWhereHas('medicoAsignado', fn($sub) => $sub->where('medico_id', $userId));
        });
    }

    $total = $query->count();
    $contexto = self::esMedico() && !self::esAdmin() ? 'asignados a ti' : 'en total';

    return [
        'total_pacientes_activos' => $total,
        'contexto' => $contexto,
        'interpretacion' => "Actualmente hay {$total} pacientes activos {$contexto}.",
    ];
}


    private static function buscarPaciente(string $termino): array
{
    if (strlen($termino) < 2) {
        return ['error' => 'El término de búsqueda debe tener al menos 2 caracteres.'];
    }

    $query = Paciente::where(function ($q) use ($termino) {
        $q->where('nombre', 'like', "%{$termino}%")
          ->orWhere('apellido_paterno', 'like', "%{$termino}%")
          ->orWhere('apellido_materno', 'like', "%{$termino}%")
          ->orWhere('curp', 'like', "%{$termino}%");
    })->with(['medicoAsignado.medico']);

    if (self::esMedico() && !self::esAdmin()) {
        $userId = self::usuarioActual()->id;
        $query->where(function ($q) use ($userId) {
            $q->whereHas('citas', fn($sub) => $sub->where('medico_id', $userId))
              ->orWhereHas('consultas', fn($sub) => $sub->where('medico_id', $userId))
              ->orWhereHas('medicoAsignado', fn($sub) => $sub->where('medico_id', $userId));
        });
    }

    $pacientes = $query->limit(5)->get();

    if ($pacientes->isEmpty()) {
        return ['encontrados' => 0, 'interpretacion' => "No se encontraron pacientes con '{$termino}' que tengas permiso de ver."];
    }

    return [
        'encontrados' => $pacientes->count(),
        'pacientes' => $pacientes->map(fn($p) => [
            'id' => $p->id,
            'nombre_completo' => $p->nombre_completo,
            'curp' => $p->curp,
            'edad' => $p->edad,
            'alergias' => $p->alergias,
            'medico_asignado' => $p->medicoAsignado?->medico?->nombre_completo,
        ])->toArray(),
    ];
}
   private static function historialConsultasPaciente(int $pacienteId, int $limite = 5): array
{
    $paciente = Paciente::find($pacienteId);
    if (!$paciente) {
        return ['error' => 'Paciente no encontrado.'];
    }

    $query = Consulta::where('paciente_id', $pacienteId);

    if (self::esMedico() && !self::esAdmin()) {
        $query->where('medico_id', self::usuarioActual()->id);
    }

    $consultas = $query->with(['medico', 'diagnosticoPrincipal'])
        ->orderByDesc('created_at')
        ->limit($limite)
        ->get();

    return [
        'paciente' => $paciente->nombre_completo,
        'total_consultas' => $query->count(),
        'ultimas' => $consultas->map(fn($c) => [
            'fecha' => $c->created_at->format('d/m/Y'),
            'medico' => $c->medico?->nombre_completo,
            'diagnostico' => $c->diagnosticoPrincipal?->etiqueta ?? $c->analisis,
        ])->toArray(),
    ];
}

    // ============ CITAS ============

    private static function citasHoy(): array
    {
        $total = Cita::whereDate('fecha_hora', today())->count();
        $pendientes = Cita::whereDate('fecha_hora', today())
            ->whereIn('estado', ['programada', 'confirmada', 'en_curso'])->count();
        $atendidas = Cita::whereDate('fecha_hora', today())
            ->where('estado', 'atendida')->count();

        return [
            'fecha' => today()->format('d/m/Y'),
            'total' => $total,
            'pendientes' => $pendientes,
            'atendidas' => $atendidas,
            'interpretacion' => "Hoy hay {$total} citas: {$pendientes} pendientes y {$atendidas} atendidas.",
        ];
    }

    private static function citasPorFecha(string $fecha): array
    {
        try {
            $fechaCarbon = Carbon::parse($fecha);
        } catch (\Throwable $e) {
            return ['error' => 'Formato de fecha inválido. Usa YYYY-MM-DD.'];
        }

        $citas = Cita::whereDate('fecha_hora', $fechaCarbon)
            ->with(['paciente', 'medico'])
            ->orderBy('fecha_hora')
            ->get();

        return [
            'fecha' => $fechaCarbon->format('d/m/Y'),
            'total' => $citas->count(),
            'citas' => $citas->map(fn($c) => [
                'hora' => $c->fecha_hora->format('H:i'),
                'paciente' => $c->paciente?->nombre_completo,
                'medico' => $c->medico?->nombre_completo,
                'estado' => $c->estado_label,
            ])->toArray(),
        ];
    }

 private static function citasMedico(string $nombreMedico, ?string $fecha = null): array
{
    if (self::esMedico() && !self::esAdmin()) {
        $nombreMedico = self::usuarioActual()->nombre;
    }

    // Buscar al médico por nombre o apellido y que tenga rol de médico
    $medico = User::where(function ($q) use ($nombreMedico) {
            $q->where('nombre', 'like', "%{$nombreMedico}%")
              ->orWhere('apellido_paterno', 'like', "%{$nombreMedico}%");
        })
        ->whereHas('roles', fn($q) => $q->whereRaw('LOWER(name) LIKE ?', ['%medic%']))
        ->first();

    if (!$medico) {
        return ['error' => "No se encontró un médico con '{$nombreMedico}'."];
    }

    $query = Cita::where('medico_id', $medico->id)->with('paciente');

    if ($fecha) {
        $query->whereDate('fecha_hora', $fecha);
    } else {
        $query->whereDate('fecha_hora', today());
    }

    $citas = $query->orderBy('fecha_hora')->get();

    return [
        'medico' => $medico->nombre_completo,
        'fecha'  => $fecha ?? today()->format('d/m/Y'),
        'total'  => $citas->count(),
        'citas'  => $citas->map(fn($c) => [
            'hora'     => $c->fecha_hora->format('H:i'),
            'paciente' => $c->paciente?->nombre_completo,
            'estado'   => $c->estado_label,
        ])->toArray(),
    ];
}

    // ============ CAMAS ============

    private static function camasDisponibles(): array
    {
        $disponibles = Cama::where('estado', 'disponible')->where('activo', true)->count();
        $ocupadas = Cama::where('estado', 'ocupada')->count();
        $mantenimiento = Cama::whereIn('estado', ['mantenimiento', 'limpieza', 'fuera_servicio'])->count();
        $total = $disponibles + $ocupadas + $mantenimiento;

        return [
            'disponibles' => $disponibles,
            'ocupadas' => $ocupadas,
            'no_disponibles' => $mantenimiento,
            'total' => $total,
            'ocupacion_porcentaje' => $total > 0 ? round(($ocupadas / $total) * 100, 1) : 0,
            'interpretacion' => "Hay {$disponibles} camas disponibles de {$total} totales ({$ocupadas} ocupadas).",
        ];
    }

    private static function camasPorArea(): array
    {
        $areas = Cama::select('area')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN estado = 'disponible' THEN 1 ELSE 0 END) as disponibles")
            ->selectRaw("SUM(CASE WHEN estado = 'ocupada' THEN 1 ELSE 0 END) as ocupadas")
            ->where('activo', true)
            ->groupBy('area')
            ->get();

        return [
            'por_area' => $areas->map(fn($a) => [
                'area' => $a->area,
                'total' => $a->total,
                'disponibles' => $a->disponibles,
                'ocupadas' => $a->ocupadas,
            ])->toArray(),
        ];
    }

    // ============ MEDICAMENTOS ============

    private static function medicamentosStockBajo(): array
    {
        $medicamentos = Medicamento::where('activo', true)
            ->with('lotes')
            ->get()
            ->filter(fn($m) => $m->stock_total_calculado <= $m->stock_minimo)
            ->map(fn($m) => [
                'nombre' => $m->nombre . ' ' . $m->concentracion,
                'stock_actual' => $m->stock_total_calculado,
                'stock_minimo' => $m->stock_minimo,
            ])
            ->values()
            ->toArray();

        return [
            'total' => count($medicamentos),
            'medicamentos' => $medicamentos,
            'interpretacion' => count($medicamentos) . ' medicamentos tienen stock bajo.',
        ];
    }

    private static function buscarMedicamento(string $nombre): array
    {
        $medicamentos = Medicamento::where('nombre', 'like', "%{$nombre}%")
            ->orWhere('sustancia_activa', 'like', "%{$nombre}%")
            ->with(['lotes' => fn($q) => $q->where('cantidad_disponible', '>', 0)->orderBy('fecha_caducidad')])
            ->limit(5)
            ->get();

        if ($medicamentos->isEmpty()) {
            return ['encontrados' => 0, 'interpretacion' => "No se encontró '{$nombre}'."];
        }

        return [
            'encontrados' => $medicamentos->count(),
            'medicamentos' => $medicamentos->map(function ($m) {
                $loteProximo = $m->lotes->first();
                return [
                    'nombre' => $m->nombre . ' ' . $m->concentracion,
                    'sustancia' => $m->sustancia_activa,
                    'stock_total' => $m->stock_total_calculado,
                    'stock_minimo' => $m->stock_minimo,
                    'caducidad_proxima' => $loteProximo?->fecha_caducidad->format('d/m/Y'),
                    'dias_para_caducar' => $loteProximo?->dias_para_caducar,
                ];
            })->toArray(),
        ];
    }

    private static function medicamentosProximosCaducar(): array
    {
        $medicamentos = Medicamento::whereHas('lotes', function ($q) {
                $q->whereBetween('fecha_caducidad', [now(), now()->addDays(30)])
                  ->where('cantidad_disponible', '>', 0);
            })
            ->with(['lotes' => function ($q) {
                $q->whereBetween('fecha_caducidad', [now(), now()->addDays(30)])
                  ->where('cantidad_disponible', '>', 0)
                  ->orderBy('fecha_caducidad');
            }])
            ->get();

        return [
            'total' => $medicamentos->count(),
            'medicamentos' => $medicamentos->map(function ($m) {
                $lote = $m->lotes->first();
                return [
                    'nombre' => $m->nombre . ' ' . $m->concentracion,
                    'lote' => $lote?->codigo_lote,
                    'caducidad' => $lote?->fecha_caducidad->format('d/m/Y'),
                    'dias_restantes' => $lote?->dias_para_caducar,
                    'cantidad' => $lote?->cantidad_disponible,
                ];
            })->toArray(),
        ];
    }

    // ============ PERSONAL ============

    private static function medicosActivos(): array
    {
        $medicos = User::whereHas('roles', fn($q) => $q->whereRaw('LOWER(name) LIKE ?', ['%medic%']))
            ->where('activo', true)
            ->with('especialidades')
            ->get();

        return [
            'total' => $medicos->count(),
            'medicos' => $medicos->map(fn($m) => [
                'nombre' => $m->nombre_completo,
                'especialidades' => $m->especialidades->pluck('nombre')->toArray(),
            ])->toArray(),
        ];
    }

    private static function turnosDeHoy(): array
    {
        $hoy = today();
        $usuarios = User::where('activo', true)
            ->whereHas('turnos', function ($q) use ($hoy) {
                $q->wherePivot('activo', true)
                  ->wherePivot('fecha_inicio', '<=', $hoy->toDateString())
                  ->where(function ($sub) use ($hoy) {
                      $sub->whereNull('turno_user.fecha_fin')
                          ->orWhere('turno_user.fecha_fin', '>=', $hoy->toDateString());
                  })
                  ->where(function ($sub) use ($hoy) {
                      $sub->whereNull('turno_user.dia_semana')
                          ->orWhere('turno_user.dia_semana', $hoy->dayOfWeek);
                  });
            })
            ->with(['turnos' => function ($q) use ($hoy) {
                $q->wherePivot('activo', true)
                  ->wherePivot('fecha_inicio', '<=', $hoy->toDateString());
            }, 'roles'])
            ->get();

        return [
            'fecha' => $hoy->format('d/m/Y'),
            'total' => $usuarios->count(),
            'personal' => $usuarios->map(fn($u) => [
                'nombre' => $u->nombre_completo,
                'rol' => $u->getRoleNames()->first(),
                'turnos' => $u->turnos->map(fn($t) => $t->nombre . ' (' . $t->rango . ')')->toArray(),
            ])->toArray(),
        ];
    }

    // ============ ADMISIONES ============

    private static function admisionesDeHoy(): array
    {
        $total = Admision::whereDate('fecha_hora_llegada', today())->count();
        $enEspera = Admision::whereDate('fecha_hora_llegada', today())->where('estado', 'en_espera')->count();
        $hospitalizados = Admision::whereDate('fecha_hora_llegada', today())->where('estado', 'hospitalizado')->count();
        $derivados = Admision::whereDate('fecha_hora_llegada', today())->where('estado', 'derivado')->count();

        return [
            'fecha' => today()->format('d/m/Y'),
            'total' => $total,
            'en_espera' => $enEspera,
            'hospitalizados' => $hospitalizados,
            'derivados' => $derivados,
            'interpretacion' => "Hoy hubo {$total} admisiones: {$enEspera} en espera, {$hospitalizados} hospitalizados, {$derivados} derivados.",
        ];
    }

    // ============ RESUMEN GENERAL ============

    private static function resumenHospital(): array
    {
        return [
            'fecha' => now()->format('d/m/Y H:i'),
            'pacientes_activos' => Paciente::where('activo', true)->count(),
            'citas_hoy' => Cita::whereDate('fecha_hora', today())->count(),
            'camas_disponibles' => Cama::where('estado', 'disponible')->where('activo', true)->count(),
            'camas_ocupadas' => Cama::where('estado', 'ocupada')->count(),
            'medicamentos_stock_bajo' => Medicamento::where('activo', true)->with('lotes')->get()
                ->filter(fn($m) => $m->stock_total_calculado <= $m->stock_minimo)->count(),
            'admisiones_hoy' => Admision::whereDate('fecha_hora_llegada', today())->count(),
        ];
    }

    private static function prediccionAgotamiento(): array
{
    $medicamentos = Medicamento::where('activo', true)->with('lotes')->get();

    $criticos = [];
    foreach ($medicamentos as $med) {
        $pred = \App\Services\PrediccionService::prediccionAgotamiento($med);

        if (in_array($pred['estado'], ['critico', 'bajo'])) {
            $criticos[] = [
                'nombre' => $med->nombre . ' ' . $med->concentracion,
                'stock_actual' => $med->stock_total_calculado,
                'dias_restantes' => $pred['dias_restantes'],
                'fecha_agotamiento' => $pred['fecha_agotamiento'],
                'estado' => $pred['estado'],
                'cantidad_sugerida' => $pred['cantidad_sugerida'],
            ];
        }
    }

    // Ordenar por días restantes (más críticos primero)
    usort($criticos, fn($a, $b) => ($a['dias_restantes'] ?? 999) <=> ($b['dias_restantes'] ?? 999));

    return [
        'total_criticos' => count($criticos),
        'medicamentos' => array_slice($criticos, 0, 10),
        'interpretacion' => count($criticos) . ' medicamentos se van a agotar en los próximos días.',
    ];
}

private static function medicamentosMayorDemanda(int $dias = 30): array
{
    $desde = now()->subDays($dias);

    $resultados = \App\Models\MovimientoInventario::select(
            'medicamento_id',
            \DB::raw('SUM(ABS(cantidad)) as total_salidas'),
            \DB::raw('COUNT(*) as num_salidas')
        )
        ->where('tipo', 'salida')
        ->where('created_at', '>=', $desde)
        ->groupBy('medicamento_id')
        ->orderByDesc('total_salidas')
        ->limit(10)
        ->with('medicamento')
        ->get();

    return [
        'periodo' => "últimos {$dias} días",
        'total_analizados' => $resultados->count(),
        'top' => $resultados->map(fn($r) => [
            'medicamento' => $r->medicamento?->nombre . ' ' . $r->medicamento?->concentracion,
            'total_salidas' => (int) $r->total_salidas,
            'promedio_diario' => round($r->total_salidas / max($dias, 1), 2),
        ])->toArray(),
    ];
}

private static function generarPdfPacientesMedico(string $nombreMedico): array
{
    $medico = User::where(function ($q) use ($nombreMedico) {
            $q->where('nombre', 'like', "%{$nombreMedico}%")
              ->orWhere('apellido_paterno', 'like', "%{$nombreMedico}%");
        })
        ->whereHas('roles', fn($q) => $q->whereRaw('LOWER(name) LIKE ?', ['%medic%']))
        ->first();

    if (!$medico) {
        return ['error' => "No se encontró un médico con '{$nombreMedico}'."];
    }

    return \App\Services\ReporteService::pacientesDeMedico($medico->id);
}

private static function generarPdfCitasDia(string $fecha): array
{
    return \App\Services\ReporteService::citasDelDia($fecha);
}
// ============ MEDICAMENTOS: STOCK ALTO ============

private static function medicamentosStockAlto(int $limite = 10): array
{
    $medicamentos = Medicamento::where('activo', true)
        ->with('lotes')
        ->get()
        ->map(fn($m) => [
            'nombre' => $m->nombre . ' ' . $m->concentracion,
            'stock_actual' => $m->stock_total_calculado,
            'stock_minimo' => $m->stock_minimo,
        ])
        ->sortByDesc('stock_actual')
        ->take($limite)
        ->values()
        ->toArray();

    return [
        'total' => count($medicamentos),
        'medicamentos' => $medicamentos,
        'interpretacion' => 'Estos son los medicamentos con mayor stock disponible.',
    ];
}

private static function listarMedicamentos(int $limite = 20): array
{
    $medicamentos = Medicamento::where('activo', true)
        ->with('lotes')
        ->limit($limite)
        ->get()
        ->map(fn($m) => [
            'id' => $m->id,
            'nombre' => $m->nombre . ' ' . $m->concentracion,
            'sustancia' => $m->sustancia_activa,
            'stock_actual' => $m->stock_total_calculado,
            'stock_minimo' => $m->stock_minimo,
            'estado' => $m->stock_total_calculado <= $m->stock_minimo ? 'bajo' : 'ok',
        ])
        ->toArray();

    return [
        'total' => count($medicamentos),
        'medicamentos' => $medicamentos,
    ];
}

// ============ PACIENTES: LISTADO ============

private static function listarPacientes(int $limite = 20): array
{
    $query = Paciente::where('activo', true)->with('medicoAsignado.medico');

    if (self::esMedico() && !self::esAdmin()) {
        $userId = self::usuarioActual()->id;
        $query->where(function ($q) use ($userId) {
            $q->whereHas('citas', fn($sub) => $sub->where('medico_id', $userId))
              ->orWhereHas('consultas', fn($sub) => $sub->where('medico_id', $userId))
              ->orWhereHas('medicoAsignado', fn($sub) => $sub->where('medico_id', $userId));
        });
    }

    $pacientes = $query->limit($limite)->get()->map(fn($p) => [
        'id' => $p->id,
        'nombre' => $p->nombre_completo,
        'edad' => $p->edad,
        'curp' => $p->curp,
        'medico_asignado' => $p->medicoAsignado?->medico?->nombre_completo,
    ])->toArray();

    return [
        'total' => count($pacientes),
        'pacientes' => $pacientes,
    ];
}

// ============ CAMAS: LISTADO ============

private static function listarCamas(?string $estado = null, int $limite = 20): array
{
    $query = Cama::where('activo', true)->with('pacienteActual.paciente');

    if ($estado) {
        $query->where('estado', $estado);
    }

    $camas = $query->limit($limite)->get()->map(fn($c) => [
        'codigo' => $c->codigo,
        'area' => $c->area,
        'estado' => $c->estado,
        'paciente' => $c->pacienteActual?->paciente?->nombre_completo,
    ])->toArray();

    return [
        'total' => count($camas),
        'camas' => $camas,
    ];
}

// ============ CITAS: LISTADO ============

private static function listarCitas(?string $estado = null, ?string $desde = null, ?string $hasta = null, int $limite = 20): array
{
    $query = Cita::with(['paciente', 'medico']);

    if ($estado) {
        $query->where('estado', $estado);
    }
    if ($desde) {
        $query->whereDate('fecha_hora', '>=', $desde);
    }
    if ($hasta) {
        $query->whereDate('fecha_hora', '<=', $hasta);
    }

    if (self::esMedico() && !self::esAdmin()) {
        $query->where('medico_id', self::usuarioActual()->id);
    }

    $citas = $query->orderByDesc('fecha_hora')->limit($limite)->get()->map(fn($c) => [
        'fecha' => $c->fecha_hora->format('d/m/Y H:i'),
        'paciente' => $c->paciente?->nombre_completo,
        'medico' => $c->medico?->nombre_completo,
        'estado' => $c->estado_label,
    ])->toArray();

    return [
        'total' => count($citas),
        'citas' => $citas,
    ];
}

// ============ PERSONAL: LISTADO ============

private static function listarPersonal(?string $rol = null, int $limite = 20): array
{
    $query = User::where('activo', true)->with('roles');

    if ($rol) {
        $query->whereHas('roles', fn($q) => $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($rol) . '%']));
    }

    $personal = $query->limit($limite)->get()->map(fn($u) => [
        'nombre' => $u->nombre_completo,
        'rol' => $u->getRoleNames()->first(),
        'email' => $u->email,
    ])->toArray();

    return [
        'total' => count($personal),
        'personal' => $personal,
    ];
}

// ============ ADMISIONES: LISTADO ============

private static function listarAdmisiones(?string $estado = null, ?string $fecha = null, int $limite = 20): array
{
    $query = Admision::with('paciente');

    if ($estado) {
        $query->where('estado', $estado);
    }
    if ($fecha) {
        $query->whereDate('fecha_hora_llegada', $fecha);
    }

    $admisiones = $query->orderByDesc('fecha_hora_llegada')->limit($limite)->get()->map(fn($a) => [
        'fecha' => $a->fecha_hora_llegada?->format('d/m/Y H:i'),
        'paciente' => $a->paciente?->nombre_completo,
        'estado' => $a->estado,
    ])->toArray();

    return [
        'total' => count($admisiones),
        'admisiones' => $admisiones,
    ];
}

// ============ ESTADÍSTICAS GENERALES ============

private static function estadisticasGenerales(): array
{
    $totalPacientes = Paciente::where('activo', true)->count();
    $citasMes = Cita::whereMonth('fecha_hora', now()->month)->count();
    $totalCamas = Cama::where('activo', true)->count();
    $camasOcupadas = Cama::where('estado', 'ocupada')->count();
    $totalMedicamentos = Medicamento::where('activo', true)->count();
    $personalActivo = User::where('activo', true)->count();

    return [
        'pacientes_activos' => $totalPacientes,
        'citas_mes_actual' => $citasMes,
        'ocupacion_camas' => $totalCamas > 0 ? round(($camasOcupadas / $totalCamas) * 100, 1) . '%' : '0%',
        'total_medicamentos' => $totalMedicamentos,
        'personal_activo' => $personalActivo,
        'interpretacion' => "El hospital tiene {$totalPacientes} pacientes activos, " .
            "{$citasMes} citas este mes, " .
            "y una ocupación de camas del " . ($totalCamas > 0 ? round(($camasOcupadas / $totalCamas) * 100, 1) : 0) . "%.",
    ];
}

// ============ PDFs ADICIONALES ============

private static function generarPdfMedicamentosStockAlto(): array
{
    return \App\Services\ReporteService::medicamentosStockAlto();
}

private static function generarPdfPacientes(): array
{
    return \App\Services\ReporteService::pacientesActivos();
}

private static function generarPdfPersonal(): array
{
    return \App\Services\ReporteService::personalActivo();
}

private static function generarPdfAdmisiones(string $fecha): array
{
    return \App\Services\ReporteService::admisionesDelDia($fecha);
}
private static function generarPdfInventarioBajo(): array
{
    return \App\Services\ReporteService::inventarioBajo();
}
}