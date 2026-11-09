<?php

namespace App\Http\Requests;

use App\Models\Paciente;

/**
 * Editar una consulta usa las mismas reglas que registrarla. La diferencia es
 * que la ruta recibe la consulta y no la cita, y de ahí se obtiene el paciente.
 */
class UpdateConsultaRequest extends StoreConsultaRequest
{
    protected function pacienteDeLaConsulta(): ?Paciente
    {
        return Paciente::find($this->route('consulta')?->paciente_id);
    }
}
