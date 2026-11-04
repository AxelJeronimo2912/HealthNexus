<?php

namespace App\Http\Requests;

use App\Models\Servicio;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ServicioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $param = $this->route('servicio');
        $id = $param instanceof Servicio ? $param->id : $param;

        return [
            'codigo' => [
                'required',
                'string',
                'min:2',
                'max:20',
                'regex:/^[A-Z0-9_-]+$/',
                Rule::unique('servicios', 'codigo')->ignore($id),
            ],

            'nombre' => [
                'required',
                'string',
                'min:3',
                'max:150',
                'regex:/^[\pL\pN\s.\-,()\/]+$/u',
            ],

            'tipo' => [
                'required',
                Rule::in([
                    'consulta_externa',
                    'urgencias',
                    'hospitalizacion',
                    'quirofano',
                    'farmacia',
                    'enfermeria',
                    'laboratorio',
                    'imagenologia',
                    'otro',
                ]),
            ],

            'ubicacion' => ['nullable', 'string', 'max:200'],
            'piso' => ['nullable', 'string', 'max:50'],
            'ala' => ['nullable', 'string', 'max:50'],

            'hora_apertura' => ['nullable', 'date_format:H:i,H:i:s'],
            'hora_cierre' => ['nullable', 'date_format:H:i,H:i:s'],

            'capacidad' => ['nullable', 'integer', 'min:0'],

            'tiene_costo' => ['nullable', 'boolean'],

            'precio' => $this->boolean('tiene_costo')
                ? [
                    'required',
                    'numeric',
                    'gt:0',
                    'max:99999999.99',
                    'decimal:0,2',
                ]
                : [
                    'nullable',
                    'numeric',
                    'min:0',
                    'max:99999999.99',
                    'decimal:0,2',
                ],

            'precio_descripcion' => ['nullable', 'string', 'max:100'],
            'extension_telefonica' => ['nullable', 'string', 'max:20'],
            'descripcion' => ['nullable', 'string', 'max:5000'],
            'notas' => ['nullable', 'string', 'max:5000'],
            'abierto_24h' => ['nullable', 'boolean'],
            'activo' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'codigo.required' => 'El código del servicio es obligatorio.',
            'codigo.min' => 'El código debe tener al menos 2 caracteres.',
            'codigo.max' => 'El código no puede superar los 20 caracteres.',
            'codigo.regex' => 'El código solo puede contener letras mayúsculas, números, guiones y guiones bajos.',
            'codigo.unique' => 'Ya existe un servicio con ese código.',

            'nombre.required' => 'El nombre del servicio es obligatorio.',
            'nombre.min' => 'El nombre debe tener al menos 3 caracteres.',
            'nombre.max' => 'El nombre no puede superar los 150 caracteres.',
            'nombre.regex' => 'El nombre contiene caracteres no permitidos.',

            'tipo.required' => 'Debes seleccionar el tipo de servicio.',
            'tipo.in' => 'El tipo de servicio seleccionado no es válido.',

            'capacidad.integer' => 'La capacidad debe ser un número entero.',
            'capacidad.min' => 'La capacidad no puede ser negativa.',

            'precio.required' => 'Debes indicar el precio del servicio.',
            'precio.numeric' => 'El precio debe ser un valor numérico.',
            'precio.gt' => 'El precio debe ser mayor que cero cuando el servicio tiene costo.',
            'precio.min' => 'El precio no puede ser negativo.',
            'precio.max' => 'El precio supera el máximo permitido.',
            'precio.decimal' => 'El precio debe tener como máximo dos decimales.',

            'precio_descripcion.max' => 'La descripción del precio no puede superar los 100 caracteres.',

            'hora_apertura.date_format' => 'La hora de apertura debe tener un formato válido.',
            'hora_cierre.date_format' => 'La hora de cierre debe tener un formato válido.',
        ];
    }

    public function attributes(): array
    {
        return [
            'codigo' => 'código',
            'nombre' => 'nombre',
            'tipo' => 'tipo de servicio',
            'precio' => 'precio',
            'precio_descripcion' => 'descripción del precio',
            'capacidad' => 'capacidad',
            'hora_apertura' => 'hora de apertura',
            'hora_cierre' => 'hora de cierre',
        ];
    }

    protected function prepareForValidation(): void
    {
        $merge = [
            'abierto_24h' => $this->boolean('abierto_24h'),
            'activo' => $this->boolean('activo'),
            'tiene_costo' => $this->boolean('tiene_costo'),
        ];

        if ($this->filled('codigo')) {
            $merge['codigo'] = mb_strtoupper(
                trim((string) $this->input('codigo'))
            );
        }

        if ($this->filled('nombre')) {
            $merge['nombre'] = preg_replace(
                '/\s+/',
                ' ',
                trim((string) $this->input('nombre'))
            );
        }

        if ($this->has('precio_descripcion')) {
            $descripcion = trim(
                (string) $this->input('precio_descripcion')
            );

            $merge['precio_descripcion'] =
                $descripcion === '' ? null : $descripcion;
        }

        if (!$this->boolean('tiene_costo')) {
            $merge['precio'] = 0;
            $merge['precio_descripcion'] = null;
        }

        $this->merge($merge);
    }
}

