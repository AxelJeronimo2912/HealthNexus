<?php

namespace App\Http\Controllers;

use App\Enums\AuditEvent;
use App\Http\Requests\StoreSignoVitalOfflineRequest;
use App\Models\SignoVital;
use App\Models\User;
use App\Services\AuditoriaService;
use App\Services\TriageService;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

/**
 * Modo sin conexión, fase 1: recibe los signos vitales que el navegador guardó en su cola mientras no había
 * conexión (o el servidor estaba apagado) y los envía al reconectar.
 *
 * Es idempotente: si el mismo client_uuid llega dos veces (un reintento después de un corte), responde 200 con el
 * mismo registro y no lo duplica. Si ese client_uuid lo envió otra cuenta, responde 409.
 *
 * A diferencia del registro en línea, NO libera la cama automáticamente aunque el triage salga verde o azul:
 * la toma puede tener horas y el paciente pudo cambiar. La respuesta indica si conviene revisar la cama.
 */
class OfflineSignosController extends Controller
{
    public function __invoke(StoreSignoVitalOfflineRequest $request): JsonResponse
    {
        $user = $request->user();
        $uuid = strtolower($request->validated('client_uuid'));

        if ($existente = SignoVital::where('client_uuid', $uuid)->first()) {
            return $this->yaRecibido($existente, $user);
        }

        $datos = collect($request->validated())->except(['client_uuid', 'capturado_en'])->all();
        if (! $request->boolean('triage_manual') || empty($datos['triage'])) {
            $datos['triage'] = TriageService::calcular($datos);
            $datos['triage_manual'] = false;
        }
        $capturadoEn = Carbon::parse($request->validated('capturado_en'))->setTimezone(config('app.timezone'));

        // La fecha de registro es la hora real de la toma, para que quede en orden en los listados y en "último signo";
        // updated_at y la bitácora guardan cuándo llegó.
        $registro = new SignoVital([
            ...$datos,
            'user_id' => $user->id,
            'client_uuid' => $uuid,
            'capturado_en' => $capturadoEn,
            'sin_conexion' => true,
        ]);
        $registro->created_at = $capturadoEn;

        try {
            $registro->save();
        } catch (UniqueConstraintViolationException) {
            // Dos envíos simultáneos del mismo registro: el segundo responde como reintento
            return $this->yaRecibido(SignoVital::where('client_uuid', $uuid)->firstOrFail(), $user);
        }

        AuditoriaService::registrar(
            AuditEvent::CREATED,
            'signos_vitales',
            'Signos vitales capturados sin conexión el '.$capturadoEn->format('d/m/Y H:i').' y sincronizados',
            $registro,
        );

        return response()->json([
            'id' => $registro->id,
            'triage' => $registro->triage,
            'revisar_cama' => in_array($registro->triage, ['rojo', 'naranja'], true),
        ], 201);
    }

    private function yaRecibido(SignoVital $registro, User $user): JsonResponse
    {
        if ((int) $registro->user_id !== (int) $user->id) {
            return response()->json(['message' => 'Ese registro ya fue enviado desde otra cuenta.'], 409);
        }

        return response()->json(['id' => $registro->id, 'triage' => $registro->triage, 'duplicado' => true]);
    }
}
