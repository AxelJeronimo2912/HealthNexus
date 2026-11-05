<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSignoVitalRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado a hacer esta petición.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Reglas de validación para actualizar signos vitales.
     */
    public function rules(): array
    {
        return [
            'paciente_id'             => ['required', 'exists:pacientes,id'],
            'temperatura'             => ['nullable', 'numeric'],
            'frecuencia_cardiaca'     => ['nullable', 'integer'],
            'frecuencia_respiratoria' => ['nullable', 'integer'],
            'presion_arterial'        => ['nullable', 'string', 'max:20'],
            'saturacion_oxigeno'      => ['nullable', 'numeric'],
            'glucosa'                 => ['nullable', 'numeric'],
            'peso'                    => ['nullable', 'numeric'],
            'talla'                   => ['nullable', 'numeric'],
            'escala_dolor'            => ['nullable', 'integer', 'min:0', 'max:10'],
            'triage_manual'           => ['nullable', 'boolean'],
            'triage'                  => ['nullable', 'in:rojo,naranja,amarillo,verde,azul'],
            'motivo_consulta'         => ['nullable', 'string'],
            'notas'                   => ['nullable', 'string'],
        ];
    }
}