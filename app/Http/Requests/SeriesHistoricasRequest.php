<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SeriesHistoricasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // El indicador puede ser 'atencion' o 'insumos'
            'indicador' => 'required|string|in:atencion,insumos',
            
            // Fechas opcionales para filtrar el periodo exacto
            'fecha_inicio' => 'nullable|date|before_or_equal:today',
'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            
            // Si es un servicio específico para atención
            'servicio_id' => 'nullable|integer|exists:servicios,id',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Parámetros de consulta inválidos.',
            'errors' => $validator->errors()
        ], 422));
    }
}