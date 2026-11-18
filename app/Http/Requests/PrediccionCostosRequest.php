<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PrediccionCostosRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'tipo' => 'required|string|in:atencion,insumos',
            'meses_historial' => 'nullable|integer|min:1|max:36',
            'meses_prediccion' => 'nullable|integer|min:1|max:12',
            'servicio_id' => 'nullable|integer',
        ];
    }
}