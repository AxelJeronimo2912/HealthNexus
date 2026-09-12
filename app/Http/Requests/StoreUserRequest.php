<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('administrador');
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:100'],
            'apellido_paterno' => ['required', 'string', 'max:100'],
            'apellido_materno' => ['required', 'string', 'max:100'],
            'curp' => ['required', 'string', 'size:18', 'unique:users,curp'],
            'fecha_nacimiento' => ['required', 'date', 'before:today'],
            'cedula_profesional' => ['nullable', 'string', 'max:50'],
            'telefono' => ['required', 'string', 'max:20'],
            'telefono_contacto' => ['nullable', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'tipo_servicio' => ['required', 'in:presencial,virtual,mixto'],
            'role' => ['required', 'exists:roles,name'],
            'foto_perfil' => ['nullable', 'image', 'max:2048'],
            'firma_archivo' => ['nullable', 'image', 'max:2048'],
            'firma_canvas' => ['nullable', 'string'],
            'activo' => ['boolean'],
            'pin' => ['nullable','digits:4', 'required_if:role,medico',],
        ];
    }

    public function messages(): array
    {
        return [
            'curp.size' => 'La CURP debe tener exactamente 18 caracteres.',
            'curp.unique' => 'Esta CURP ya está registrada.',
            'email.unique' => 'Este correo ya está en uso.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'pin.required_if' => 'El PIN es obligatorio para el rol médico.',
            'pin.digits' => 'El PIN debe tener exactamente 4 dígitos.',
        ];
    }
}