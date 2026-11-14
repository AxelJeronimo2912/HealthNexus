<?php

namespace App\Http\Requests;

/**
 * Signos vitales capturados sin conexión y enviados al reconectar.
 *
 * Aplica las mismas reglas que el registro en línea (rangos clínicos y reglas que cruzan campos) y además exige
 * el identificador del dispositivo (client_uuid) y la hora real de la toma, que no puede estar en el futuro ni
 * tener más de MAX_ATRASO_HORAS.
 */
class StoreSignoVitalOfflineRequest extends StoreSignoVitalRequest
{
    public const MAX_ATRASO_HORAS = 72;

    private const SIGNOS = [
        'temperatura', 'frecuencia_cardiaca', 'frecuencia_respiratoria', 'presion_arterial',
        'saturacion_oxigeno', 'glucosa', 'peso', 'talla', 'escala_dolor',
    ];

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $otros = fn (string $campo): string => implode(',', array_diff(self::SIGNOS, [$campo]));

        return array_merge(parent::rules(), [
            'client_uuid' => ['required', 'uuid'],
            'capturado_en' => [
                'required',
                'date',
                'after_or_equal:'.now()->subHours(self::MAX_ATRASO_HORAS)->toIso8601String(),
                'before_or_equal:'.now()->addMinutes(5)->toIso8601String(),
            ],
            'temperatura' => array_merge(parent::rules()['temperatura'], ['required_without_all:'.$otros('temperatura')]),
        ]);
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'client_uuid.required' => 'Falta el identificador del registro.',
            'client_uuid.uuid' => 'El identificador del registro no es válido.',
            'capturado_en.required' => 'Falta la hora de la toma.',
            'capturado_en.after_or_equal' => 'El registro tiene más de '.self::MAX_ATRASO_HORAS.' horas sin enviarse; captúralo de nuevo.',
            'capturado_en.before_or_equal' => 'La hora de la toma no puede estar en el futuro.',
            'temperatura.required_without_all' => 'Registra al menos un signo vital.',
        ]);
    }
}
