<?php

namespace App\Http\Requests;

use App\Models\Paciente;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreSignoVitalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            // ===== Paciente =====
            'paciente_id' => ['required', 'exists:pacientes,id'],

            // ===== Signos vitales con rangos clínicos =====
            'temperatura'             => ['nullable', 'numeric', 'between:30,45'],
            'frecuencia_cardiaca'     => ['nullable', 'integer', 'between:20,250'],
            'frecuencia_respiratoria' => ['nullable', 'integer', 'between:5,80'],
            'presion_arterial'        => ['nullable', 'string', 'regex:/^\d{2,3}\/\d{2,3}$/'],
            'saturacion_oxigeno'      => ['nullable', 'integer', 'between:50,100'],
            'glucosa'                 => ['nullable', 'numeric', 'between:20,800'],

            // ===== Somatometría =====
            'peso'  => ['nullable', 'numeric', 'between:0.5,500'],
            'talla' => ['nullable', 'numeric', 'between:0.3,2.5'],

            // ===== Dolor y triage =====
            'escala_dolor'   => ['nullable', 'integer', 'between:0,10'],
            'triage'         => ['nullable', 'in:rojo,naranja,amarillo,verde,azul'],
            'triage_manual'  => ['boolean'],

            // ===== Texto =====
            'motivo_consulta' => ['nullable', 'string', 'max:2000'],
            'notas'           => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'paciente_id.required' => 'Debes seleccionar un paciente.',
            'paciente_id.exists'   => 'El paciente seleccionado no existe.',

            'temperatura.between' => 'La temperatura debe estar entre 30 y 45 °C.',
            'frecuencia_cardiaca.between' => 'La frecuencia cardíaca debe estar entre 20 y 250 lpm.',
            'frecuencia_respiratoria.between' => 'La frecuencia respiratoria debe estar entre 5 y 80 rpm.',
            'presion_arterial.regex' => 'La presión debe tener el formato 120/80.',
            'saturacion_oxigeno.between' => 'La saturación debe estar entre 50 y 100%.',
            'glucosa.between' => 'La glucosa debe estar entre 20 y 800 mg/dL.',

            'peso.between'  => 'El peso debe estar entre 0.5 y 500 kg.',
            'talla.between' => 'La talla debe estar entre 0.3 y 2.5 m.',

            'escala_dolor.between' => 'La escala de dolor va de 0 a 10.',
            'triage.in' => 'El triage debe ser rojo, naranja, amarillo, verde o azul.',
        ];
    }

    /**
     * 🔴 Reglas de negocio que cruzan campos.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($v) {
            $paciente = Paciente::find($this->paciente_id);

            // ===== Regla 1: Coherencia peso/talla vs IMC =====
            if ($this->peso && $this->talla) {
                $imc = $this->peso / ($this->talla ** 2);
                if ($imc < 5 || $imc > 100) {
                    $v->errors()->add('peso',
                        "El IMC calculado ({$imc}) está fuera de rango. Revisa peso/talla.");
                }
            }

            // ===== Regla 2: Edad vs signos vitales =====
            if ($paciente) {
                $edad = $paciente->edad ?? null;

                // Menores de 1 año: presión sistólica > 120 es sospechoso
                if ($edad !== null && $edad < 1 && $this->presion_arterial) {
                    [$sis] = explode('/', $this->presion_arterial);
                    if ((int) $sis > 120) {
                        $v->errors()->add('presion_arterial',
                            'Presión sistólica > 120 en menor de 1 año: verifica la captura.');
                    }
                }

                // Adultos mayores: temperatura > 38.5 requiere nota
                if ($edad !== null && $edad > 65 && $this->temperatura > 38.5 && blank($this->notas)) {
                    $v->errors()->add('notas',
                        'Paciente adulto mayor con fiebre alta: agrega una nota clínica.');
                }
            }

            // ===== Regla 3: Triage rojo exige motivo de consulta =====
            if ($this->triage === 'rojo' && blank($this->motivo_consulta)) {
                $v->errors()->add('motivo_consulta',
                    'Un triage ROJO exige describir el motivo de consulta.');
            }

            // ===== Regla 4: Coherencia saturación baja vs frecuencia respiratoria =====
            if ($this->saturacion_oxigeno && $this->saturacion_oxigeno < 90
                && $this->frecuencia_respiratoria && $this->frecuencia_respiratoria < 12) {
                $v->errors()->add('frecuencia_respiratoria',
                    'Saturación < 90% con FR < 12 rpm: valores incoherentes.');
            }

            // ===== Regla 5: No permitir triage_manual sin triage =====
            if ($this->boolean('triage_manual') && blank($this->triage)) {
                $v->errors()->add('triage',
                    'Si marcas "triage manual", debes elegir un nivel.');
            }

            // ===== Regla 6: Escala de dolor alta vs triage =====
            if ($this->escala_dolor !== null && $this->escala_dolor >= 8
                && in_array($this->triage, ['verde', 'azul'])) {
                $v->errors()->add('triage',
                    'Dolor ≥ 8/10 no es compatible con triage verde o azul.');
            }

            // ===== Regla 7: Glucosa alta requiere nota =====
            if ($this->glucosa && $this->glucosa > 300 && blank($this->notas)) {
                $v->errors()->add('notas',
                    'Glucosa > 300 mg/dL: agrega una nota clínica.');
            }
        });
    }

    /**
     * Normaliza el nombre completo del paciente (opcional).
     */
    protected function prepareForValidation(): void
    {
        if ($this->presion_arterial) {
            // Quitar espacios: "120 / 80" → "120/80"
            $this->merge([
                'presion_arterial' => preg_replace('/\s+/', '', $this->presion_arterial),
            ]);
        }
    }
}