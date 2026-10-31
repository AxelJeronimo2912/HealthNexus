<?php

namespace App\Http\Requests;

use App\Models\Medicamento;
use App\Models\Paciente;
use Illuminate\Validation\Validator;

/**
 * Validación al EDITAR una consulta (PUT consultas/{consulta}).
 *
 * Usa las mismas reglas que StoreConsultaRequest. La única diferencia es de dónde sale
 * el paciente: al crear viene de la cita de la ruta ({cita}); al editar, de la consulta
 * ({consulta}). Por eso aquí se vuelven a revisar las reglas que dependen del paciente
 * (alergias y adulto mayor con fiebre), que en StoreConsultaRequest se saltan al no haber {cita}.
 */
class UpdateConsultaRequest extends StoreConsultaRequest
{
    public function withValidator(Validator $validator): void
    {
        parent::withValidator($validator);

        $validator->after(function ($v) {
            $consulta = $this->route('consulta');
            $paciente = $consulta ? Paciente::find($consulta->paciente_id) : null;

            if (! $paciente) {
                return;
            }

            // Alergias vs medicamentos recetados
            $medicamentos = is_array($this->medicamentos) ? $this->medicamentos : [];
            if ($paciente->alergias) {
                $alergias = strtolower($paciente->alergias);
                foreach ($medicamentos as $i => $m) {
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

            // Adulto mayor con fiebre alta: exige nota clínica
            if (($paciente->edad ?? null) > 65 && $this->temperatura > 38.5 && blank($this->notas)) {
                $v->errors()->add('notas',
                    'Paciente adulto mayor con fiebre alta: agrega una nota clínica.');
            }
        });
    }
}
