<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('administrador');
    }

    public function rules(): array
    {
        $userId = $this->route('user')->id;

        return [
            'nombre' => ['required', 'string', 'max:100'],
            'apellido_paterno' => ['required', 'string', 'max:100'],
            'apellido_materno' => ['required', 'string', 'max:100'],
            'curp' => ['required', 'string', 'size:18', Rule::unique('users', 'curp')->ignore($userId)],
            'fecha_nacimiento' => ['required', 'date', 'before:today'],
            'cedula_profesional' => ['nullable', 'string', 'max:50'],
            'telefono' => ['required', 'string', 'max:20'],
            'telefono_contacto' => ['nullable', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'tipo_servicio' => ['required', 'in:presencial,virtual,mixto'],
            'role' => ['required', 'exists:roles,name'],
            'foto_perfil' => ['nullable', 'image', 'max:2048'],
            'firma_archivo' => ['nullable', 'image', 'max:2048'],
            'firma_canvas' => ['nullable', 'string'],
            'activo' => ['boolean'],
            'pin' => ['nullable','digits:4','required_if:role,medico',],
        ];
    }

    public function messages(): array
    {
        return [
            'curp.size' => 'La CURP debe tener exactamente 18 caracteres.',
            'curp.unique' => 'Esta CURP ya está registrada.',
            'email.unique' => 'Este correo ya está en uso.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ];
    }
}