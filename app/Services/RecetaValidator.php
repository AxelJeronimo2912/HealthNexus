<?php

namespace App\Services;

use App\Models\Consulta;
use Illuminate\Validation\ValidationException;

class RecetaValidator
{
    /**
     * Lanza ValidationException si la consulta no puede generar receta.
     */
    public static function validarParaImprimir(Consulta $consulta): void
    {
        $errores = [];

        // 1. Debe haber algo que recetar
        if ($consulta->medicamentos->isEmpty() && blank($consulta->receta_libre)) {
            $errores['receta'] = 'La consulta no tiene medicamentos ni receta libre.';
        }

        // 2. Debe tener diagnóstico principal
        if (blank($consulta->diagnostico_principal_id)) {
            $errores['diagnostico_principal_id'] = 'Falta el diagnóstico principal.';
        }

        // 3. Cada medicamento debe tener dosis y frecuencia
        foreach ($consulta->medicamentos as $i => $med) {
            if (blank($med->pivot->dosis)) {
                $errores["medicamentos.$i.dosis"] = "El medicamento {$med->nombre} no tiene dosis.";
            }
            if (blank($med->pivot->frecuencia)) {
                $errores["medicamentos.$i.frecuencia"] = "El medicamento {$med->nombre} no tiene frecuencia.";
            }
        }

        if (!empty($errores)) {
            throw ValidationException::withMessages($errores);
        }
    }
}