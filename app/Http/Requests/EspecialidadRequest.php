<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EspecialidadRequest extends FormRequest
{
    /**
     * Solo usuarios autenticados (ajusta según tus policies/roles).
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Reglas de validación.
     */
    public function rules(): array
    {
        // Si es update, obtenemos el ID del modelo por route model binding.
        $especialidad = $this->route('especialidad');
        $id = $especialidad?->id ?? $this->input('especialidad_id');

        return [
            'codigo' => [
                'required',
                'string',
                'max:20',
                'min:2',
                // Solo letras, números, guion y guion bajo (ej: CARD-01, ORL_02)
                'regex:/^[A-Za-z0-9_-]+$/',
                Rule::unique('especialidades', 'codigo')->ignore($id),
            ],
            'nombre' => [
                'required',
                'string',
                'min:3',
                'max:150',
                // Evitar nombres duplicados (case-insensitive)
                Rule::unique('especialidades', 'nombre')->ignore($id),
            ],
            'descripcion' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'grupo' => [
                'required',
                'string',
                Rule::in(['clinica', 'quirurgica', 'diagnostica', 'basica', 'otra']),
            ],
            'duracion_consulta_default' => [
                'required',
                'integer',
                'min:5',
                'max:240',
                // Múltiplos de 5 para coherencia con agenda
                'multiple_of:5',
            ],
            'color' => [
                'nullable',
                'string',
                'regex:/^#?[0-9A-Fa-f]{6}$/', // Acepta "#RRGGBB" o "RRGGBB"
            ],
            'activo' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    /**
     * Mensajes personalizados.
     */
    public function messages(): array
    {
        return [
            'codigo.required' => 'El código de la especialidad es obligatorio.',
            'codigo.min'      => 'El código debe tener al menos 2 caracteres.',
            'codigo.max'      => 'El código no puede superar los 20 caracteres.',
            'codigo.regex'    => 'El código solo puede contener letras, números, guiones y guiones bajos.',
            'codigo.unique'   => 'Ya existe una especialidad con ese código.',

            'nombre.required' => 'El nombre de la especialidad es obligatorio.',
            'nombre.min'      => 'El nombre debe tener al menos 3 caracteres.',
            'nombre.max'      => 'El nombre no puede superar los 150 caracteres.',
            'nombre.unique'   => 'Ya existe una especialidad con ese nombre.',

            'descripcion.max' => 'La descripción no puede superar los 1000 caracteres.',

            'grupo.required'  => 'Debes seleccionar un grupo.',
            'grupo.in'        => 'El grupo seleccionado no es válido.',

            'duracion_consulta_default.required'    => 'La duración por defecto es obligatoria.',
            'duracion_consulta_default.integer'     => 'La duración debe ser un número entero.',
            'duracion_consulta_default.min'         => 'La duración mínima es de 5 minutos.',
            'duracion_consulta_default.max'         => 'La duración máxima es de 240 minutos.',
            'duracion_consulta_default.multiple_of' => 'La duración debe ser múltiplo de 5 minutos.',

            'color.regex' => 'El color debe ser un valor hexadecimal válido (ej: #3B82F6).',
        ];
    }

    /**
     * Nombres legibles de los atributos.
     */
    public function attributes(): array
    {
        return [
            'codigo'                    => 'código',
            'nombre'                    => 'nombre',
            'descripcion'               => 'descripción',
            'grupo'                     => 'grupo',
            'duracion_consulta_default' => 'duración por defecto',
            'color'                     => 'color',
        ];
    }

    /**
     * Normaliza los datos antes de validar.
     */
    protected function prepareForValidation(): void
    {
        $merge = [];

        // Código en mayúsculas y sin espacios al inicio/fin
        if ($this->filled('codigo')) {
            $merge['codigo'] = strtoupper(trim((string) $this->input('codigo')));
        }

        // Nombre con trim
        if ($this->filled('nombre')) {
            $merge['nombre'] = trim((string) $this->input('nombre'));
        }

        // Descripción trim (null si vacío)
        if ($this->has('descripcion')) {
            $desc = trim((string) $this->input('descripcion'));
            $merge['descripcion'] = $desc === '' ? null : $desc;
        }

        // Color: normalizar a formato #RRGGBB
        if ($this->filled('color')) {
            $color = ltrim((string) $this->input('color'), '#');
            $merge['color'] = '#' . strtoupper($color);
        }

        // Activo como booleano
        if ($this->has('activo')) {
            $merge['activo'] = $this->boolean('activo');
        }

        $this->merge($merge);
    }
}