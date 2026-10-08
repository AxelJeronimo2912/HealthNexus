<?php

namespace App\Http\Requests;

use App\Models\Diagnostico;
use App\Models\Medicamento;
use App\Models\Paciente;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreConsultaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            // ===== SOAP =====
            'subjetivo' => ['nullable', 'string', 'max:5000'],
            'objetivo'  => ['nullable', 'string', 'max:5000'],
            'analisis'  => ['nullable', 'string', 'max:5000'],
            'plan'      => ['nullable', 'string', 'max:5000'],

            // ===== Diagnósticos =====
            'diagnostico_principal_id'  => ['nullable', 'exists:diagnosticos,id'],
            'diagnostico_secundario_id' => [
                'nullable',
                'exists:diagnosticos,id',
                'different:diagnostico_principal_id',
            ],

            // ===== Signos vitales (rangos clínicos) =====
            'temperatura'             => ['nullable', 'numeric', 'between:30,45'],
            'frecuencia_cardiaca'     => ['nullable', 'integer', 'between:20,250'],
            'frecuencia_respiratoria' => ['nullable', 'integer', 'between:5,80'],
            'presion_arterial'        => ['nullable', 'string', 'regex:/^\d{2,3}\/\d{2,3}$/'],
            'saturacion_oxigeno'      => ['nullable', 'integer', 'between:50,100'],
            'glucosa'                 => ['nullable', 'numeric', 'between:20,800'],

            // ===== Somatometría =====
            'peso'                => ['nullable', 'numeric', 'between:0.5,500'],
            'talla'               => ['nullable', 'numeric', 'between:0.3,2.5'],
            'perimetro_abdominal' => ['nullable', 'numeric', 'between:20,300'],

            // ===== Receta =====
            'receta_libre' => ['nullable', 'string', 'max:5000'],
            'notas'        => ['nullable', 'string', 'max:2000'],

            'medicamentos'                 => ['nullable', 'array', 'max:20'],
            'medicamentos.*.id'            => ['nullable', 'exists:medicamentos,id'],
            'medicamentos.*.dosis'         => ['nullable', 'string', 'max:100'],
            'medicamentos.*.via'           => ['nullable', 'string', 'max:50'],
            'medicamentos.*.frecuencia'    => ['nullable', 'string', 'max:100'],
            'medicamentos.*.duracion'      => ['nullable', 'string', 'max:100'],
            'medicamentos.*.indicaciones'  => ['nullable', 'string', 'max:500'],

            // ===== Flags =====
            'finalizar' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'diagnostico_principal_id.exists'  => 'El diagnóstico principal no existe.',
            'diagnostico_secundario_id.exists' => 'El diagnóstico secundario no existe.',
            'diagnostico_secundario_id.different' => 'El diagnóstico secundario no puede ser igual al principal.',

            'temperatura.between'             => 'La temperatura debe estar entre 30 y 45 °C.',
            'frecuencia_cardiaca.between'     => 'La frecuencia cardíaca debe estar entre 20 y 250 lpm.',
            'frecuencia_respiratoria.between' => 'La frecuencia respiratoria debe estar entre 5 y 80 rpm.',
            'presion_arterial.regex'          => 'La presión debe tener el formato 120/80.',
            'saturacion_oxigeno.between'      => 'La saturación debe estar entre 50 y 100%.',
            'glucosa.between'                 => 'La glucosa debe estar entre 20 y 800 mg/dL.',

            'peso.between'                => 'El peso debe estar entre 0.5 y 500 kg.',
            'talla.between'               => 'La talla debe estar entre 0.3 y 2.5 m.',
            'perimetro_abdominal.between' => 'El perímetro abdominal debe estar entre 20 y 300 cm.',

            'medicamentos.max' => 'No puedes recetar más de 20 medicamentos.',
        ];
    }

    /**
     * Validaciones personalizadas posteriores a las reglas base.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($v) {

            $paciente = Paciente::find($this->route('cita')?->paciente_id);

            // ===== Regla 1: Coherencia peso/talla vs IMC =====
            if ($this->peso && $this->talla) {
                $imc = $this->peso / ($this->talla ** 2);
                if ($imc < 5 || $imc > 100) {
                    $v->errors()->add('peso',
                        "El IMC calculado ({$imc}) está fuera de rango. Revisa peso/talla.");
                }
            }

            // ===== Regla 2: No repetir medicamentos (ignorando filas vacías) =====
            $ids = collect($this->medicamentos ?? [])
                ->pluck('id')
                ->filter() // <-- Ignora los IDs vacíos/nulos
                ->values();
                
            if ($ids->count() !== $ids->unique()->count()) {
                $v->errors()->add('medicamentos',
                    'No puedes recetar el mismo medicamento dos veces.');
            }

            // ===== Regla 3: Medicamentos sin dosis/frecuencia cuando se finaliza =====
            if ($this->boolean('finalizar')) {
                foreach ($this->medicamentos ?? [] as $i => $m) {
                    
                    // Ignorar filas completamente vacías (sin ID de medicamento)
                    if (empty($m['id'])) {
                        continue; 
                    }

                    if (empty($m['dosis'])) {
                        $v->errors()->add("medicamentos.$i.dosis",
                            'La dosis es obligatoria cuando finalizas la consulta.');
                    }
                    if (empty($m['frecuencia'])) {
                        $v->errors()->add("medicamentos.$i.frecuencia",
                            'La frecuencia es obligatoria cuando finalizas la consulta.');
                    }
                }
            }

            // ===== Regla 4: Alergias vs medicamentos =====
            if ($paciente && $paciente->alergias) {
                $alergias = strtolower($paciente->alergias);
                foreach ($this->medicamentos ?? [] as $i => $m) {
                    
                    // Ignorar filas vacías
                    if (empty($m['id'])) {
                        continue;
                    }

                    $med = Medicamento::find($m['id']);
                    if ($med && str_contains($alergias, strtolower($med->nombre))) {
                        $v->errors()->add("medicamentos.$i.id",
                            "El paciente es alérgico a {$med->nombre}.");
                    }
                }
            }

            // ===== Regla 5: Para finalizar se exige diagnóstico y plan =====
            if ($this->boolean('finalizar')) {
                if (blank($this->diagnostico_principal_id)) {
                    $v->errors()->add('diagnostico_principal_id',
                        'No puedes finalizar la consulta sin un diagnóstico principal.');
                }
                if (blank($this->plan)) {
                    $v->errors()->add('plan',
                        'No puedes finalizar la consulta sin un plan de tratamiento.');
                }
                if (blank($this->subjetivo) && blank($this->objetivo)) {
                    $v->errors()->add('subjetivo',
                        'Registra al menos el Subjetivo o el Objetivo antes de finalizar.');
                }
            }

            // ===== Regla 6: Glucosa alta sin nota =====
            if ($this->glucosa && $this->glucosa > 300 && blank($this->notas)) {
                $v->errors()->add('notas',
                    'Glucosa > 300 mg/dL: agrega una nota clínica.');
            }

            // ===== Regla 7: Adulto mayor con fiebre alta =====
            if ($paciente && ($paciente->edad ?? null) > 65
                && $this->temperatura > 38.5 && blank($this->notas)) {
                $v->errors()->add('notas',
                    'Paciente adulto mayor con fiebre alta: agrega una nota clínica.');
            }

            // ===== Regla 8: Diagnóstico principal obligatorio si hay secundario =====
            if ($this->diagnostico_secundario_id && blank($this->diagnostico_principal_id)) {
                $v->errors()->add('diagnostico_principal_id',
                    'No puedes poner un diagnóstico secundario sin el principal.');
            }
        });
    }

    /**
     * Normaliza la presión arterial (quita espacios).
     */
    protected function prepareForValidation(): void
    {
        if ($this->presion_arterial) {
            $this->merge([
                'presion_arterial' => preg_replace('/\s+/', '', $this->presion_arterial),
            ]);
        }
    }
}