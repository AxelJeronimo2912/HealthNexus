<?php

namespace App\Http\Requests;

use App\Models\Especialidad;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EspecialidadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        // En update viene el modelo (o su id); en store es null.
        $param = $this->route('especialidad');
        $id = $param instanceof Especialidad ? $param->id : $param;

        return [
            'codigo' => [
                'required', 'string', 'min:2', 'max:20',
                'regex:/^[A-Za-z0-9_-]+$/',
                Rule::unique('especialidades', 'codigo')->ignore($id),
            ],
            'nombre' => [
                'required', 'string', 'min:3', 'max:150',
                Rule::unique('especialidades', 'nombre')->ignore($id),
            ],
            'descripcion' => ['nullable', 'string', 'max:1000'],
            'grupo' => ['required', Rule::in(['clinica', 'quirurgica', 'diagnostica', 'basica', 'otra'])],
            'duracion_consulta_default' => ['required', 'integer', 'min:5', 'max:240', 'multiple_of:5'],
            'color' => ['nullable', 'string', 'regex:/^#?[0-9A-Fa-f]{6}$/'],
            'activo' => ['boolean'],
        ];
    }

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

            'color.regex' => 'El color debe ser un hexadecimal válido (ej: #3B82F6).',
        ];
    }

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

    protected function prepareForValidation(): void
    {
        $merge = ['activo' => $this->boolean('activo')];

        if ($this->filled('codigo')) {
            $merge['codigo'] = mb_strtoupper(trim((string) $this->input('codigo')));
        }

        if ($this->filled('nombre')) {
            $merge['nombre'] = preg_replace('/\s+/', ' ', trim((string) $this->input('nombre')));
        }

        if ($this->has('descripcion')) {
            $desc = trim((string) $this->input('descripcion'));
            $merge['descripcion'] = $desc === '' ? null : $desc;
        }

        if ($this->filled('color')) {
            $merge['color'] = '#' . strtoupper(ltrim((string) $this->input('color'), '#'));
        }

        $this->merge($merge);
    }
}