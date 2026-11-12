<?php

namespace App\Http\Controllers;

use App\Enums\AuditEvent;
use App\Models\Admision;
use App\Models\CamaPaciente;
use App\Models\Paciente;
use App\Models\PacienteMedico;
use App\Services\AuditoriaService;
use App\Support\RoleHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * HAL-199 · Datos mínimos de pacientes para consultar sin conexión.
 *
 * Solo médico y enfermería, y solo SUS pacientes:
 *   - médico: pacientes que tiene asignados (paciente_medicos activo);
 *   - enfermería: pacientes hospitalizados (cama activa).
 *
 * Se entregan únicamente datos clínicos básicos para la atención (nombre, edad, sexo, cama, tipo de sangre,
 * alergias, enfermedades crónicas, triage, diagnóstico presuntivo y últimos signos). NUNCA CURP, teléfono,
 * dirección, correo ni datos de cuentas. El navegador los guarda cifrados y los borra al cerrar sesión o a las
 * `offline.ttl_horas` horas (ver public/js/hn-offline.js).
 */
class OfflinePacientesController extends Controller
{
    private const ORDEN_TRIAGE = ['rojo' => 0, 'naranja' => 1, 'amarillo' => 2, 'verde' => 3, 'azul' => 4];

    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();
        $esMedico = RoleHelper::esMedico($user);
        $esEnfermeria = RoleHelper::tieneRol($user, 'enfermeria');

        if (! $esMedico && ! $esEnfermeria) {
            AuditoriaService::registrar(AuditEvent::ACCESO_DENEGADO, 'pacientes', 'Intento de descargar pacientes para consulta sin conexión sin rol clínico');

            return response()->json(['error' => 'Solo médico y enfermería pueden consultar pacientes sin conexión.'], 403)
                ->header('Cache-Control', 'no-store, private');
        }

        $ids = $esMedico
            ? PacienteMedico::where('medico_id', $user->id)->where('activo', true)->pluck('paciente_id')
            : CamaPaciente::where('activa', true)->pluck('paciente_id');

        $pacientes = Paciente::query()
            ->whereIn('id', $ids->unique())
            ->where('activo', true)
            ->with([
                'ultimoSignoVital',
                'medicoAsignado.medico:id,name,nombre,apellido_paterno',
                'camas' => fn ($q) => $q->where('activa', true)->with('cama:id,codigo,nombre,area,piso'),
            ])
            ->limit(config('offline.max_pacientes'))
            ->get();

        $diagnosticos = Admision::whereIn('paciente_id', $pacientes->pluck('id'))
            ->whereNotIn('estado', ['alta', 'derivado', 'fallecido'])
            ->orderByDesc('fecha_hora_llegada')
            ->get(['paciente_id', 'diagnostico_presuntivo', 'motivo'])
            ->unique('paciente_id')
            ->keyBy('paciente_id');

        $lista = $pacientes->map(function (Paciente $p) use ($diagnosticos) {
            $signo = $p->ultimoSignoVital;
            $cama = optional($p->camas->first())->cama;
            $medico = optional($p->medicoAsignado)->medico;
            $adm = $diagnosticos->get($p->id);

            return [
                'id' => $p->id,
                'nombre' => $p->nombre_completo,
                'edad' => $p->edad,
                'sexo' => $p->sexo,
                'tipo_sanguineo' => $p->tipo_sanguineo,
                'alergias' => $p->alergias,
                'enfermedades_cronicas' => $p->enfermedades_cronicas,
                'cama' => $cama ? trim(($cama->codigo ?: $cama->nombre).($cama->area ? " · {$cama->area}" : '')) : null,
                'medico' => $medico ? trim($medico->nombre ? "{$medico->nombre} {$medico->apellido_paterno}" : $medico->name) : null,
                'triage' => $signo?->triage,
                'diagnostico' => $adm?->diagnostico_presuntivo ?: $adm?->motivo,
                'signos' => $signo ? [
                    'tomados_en' => optional($signo->created_at)->toIso8601String(),
                    'temperatura' => $signo->temperatura,
                    'fc' => $signo->frecuencia_cardiaca,
                    'fr' => $signo->frecuencia_respiratoria,
                    'ta' => $signo->presion_arterial,
                    'spo2' => $signo->saturacion_oxigeno,
                ] : null,
            ];
        })
            ->sortBy(fn ($p) => [self::ORDEN_TRIAGE[$p['triage']] ?? 9, $p['nombre']])
            ->values();

        AuditoriaService::registrar(
            AuditEvent::EXPORTACION,
            'pacientes',
            "Descarga de {$lista->count()} paciente(s) para consulta sin conexión",
            null, [], [],
            ['pacientes' => $lista->pluck('id')->all(), 'ttl_horas' => config('offline.ttl_horas')]
        );

        $ahora = now();

        return response()->json([
            'usuario_id' => $user->id,
            'rol' => $esMedico ? 'medico' : 'enfermeria',
            'generado_en' => $ahora->toIso8601String(),
            'caduca_en' => $ahora->copy()->addHours(config('offline.ttl_horas'))->toIso8601String(),
            'pacientes' => $lista,
        ])->header('Cache-Control', 'no-store, private');
    }
}
