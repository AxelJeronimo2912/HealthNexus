<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('administrador');
    }

    public function rules(): array
    {
        $esMedico = $this->esRolMedico($this->input('role'));

        return [
            'nombre'           => ['required', 'string', 'max:100'],
            'apellido_paterno' => ['required', 'string', 'max:100'],
            'apellido_materno' => ['required', 'string', 'max:100'],

            // CURP con formato oficial mexicano
            'curp' => [
                'required',
                'string',
                'size:18',
                'regex:/^[A-Z]{4}\d{6}[HM][A-Z]{5}[0-9A-Z]\d$/i',
                'unique:users,curp',
            ],

            // Fecha coherente: entre 18 y 100 años atrás
            'fecha_nacimiento' => [
                'required',
                'date',
                'before_or_equal:' . now()->subYears(18)->format('Y-m-d'),
                'after_or_equal:'  . now()->subYears(100)->format('Y-m-d'),
            ],

            'cedula_profesional' => [Rule::requiredIf($esMedico), 'nullable', 'string', 'max:50'],

            'telefono'          => ['required', 'string', 'regex:/^\d{10}$/'],
            'telefono_contacto' => ['nullable', 'string', 'regex:/^\d{10}$/'],

            'email' => ['required', 'email:rfc,dns', 'max:255', 'unique:users,email'],

            // Contraseña robusta: letras + números + símbolos + confirmación
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(3), // opcional: revisa filtraciones conocidas
            ],

            'tipo_servicio' => ['required', 'in:presencial,virtual,mixto'],
            'role'          => ['required', 'exists:roles,name'],

            'foto_perfil' => ['nullable', 'image', 'max:2048'],

            // Ambas opcionales individualmente…
            'firma_archivo' => ['nullable', 'image', 'max:2048'],
            'firma_canvas'  => ['nullable', 'string'],

            'activo' => ['boolean'],
            'pin'    => [Rule::requiredIf($esMedico), 'nullable', 'digits:4'],
        ];
    }

    /**
     * ✅ Al menos UNA firma (archivo o canvas) debe estar presente.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                $tieneArchivo = $this->hasFile('firma_archivo');
                $tieneCanvas  = filled($this->input('firma_canvas'));

                if (! $tieneArchivo && ! $tieneCanvas) {
                    $validator->errors()->add(
                        'firma_archivo',
                        'La firma es obligatoria. Sube una imagen o dibújala en el lienzo.'
                    );
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            // --- CURP ---
            'curp.required' => 'La CURP es obligatoria.',
            'curp.size'     => 'La CURP debe tener exactamente 18 caracteres.',
            'curp.regex'    => 'La CURP no tiene un formato válido.',
            'curp.unique'   => 'Esta CURP ya está registrada.',

            // --- Fecha de nacimiento ---
            'fecha_nacimiento.required'        => 'La fecha de nacimiento es obligatoria.',
            'fecha_nacimiento.date'            => 'La fecha de nacimiento no es válida.',
            'fecha_nacimiento.before_or_equal' => 'El colaborador debe tener al menos 18 años.',
            'fecha_nacimiento.after_or_equal'  => 'La fecha de nacimiento no puede ser tan antigua (más de 100 años).',

            // --- Identidad ---
            'nombre.required'           => 'El nombre es obligatorio.',
            'nombre.max'                => 'El nombre no debe exceder 100 caracteres.',
            'apellido_paterno.required' => 'El apellido paterno es obligatorio.',
            'apellido_materno.required' => 'El apellido materno es obligatorio.',

            // --- Contacto ---
            'telefono.required'         => 'El teléfono es obligatorio.',
            'telefono.regex'            => 'El teléfono debe tener exactamente 10 dígitos.',
            'telefono_contacto.regex'   => 'El teléfono de contacto debe tener exactamente 10 dígitos.',
            'email.required'            => 'El correo electrónico es obligatorio.',
            'email.email'               => 'El correo electrónico no tiene un formato válido.',
            'email.unique'              => 'Este correo ya está en uso.',

            // --- Contraseña ---
            'password.required'  => 'La contraseña es obligatoria.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.min'       => 'La contraseña debe tener al menos 8 caracteres.',
            'password.letters'   => 'La contraseña debe incluir al menos una letra.',
            'password.numbers'   => 'La contraseña debe incluir al menos un número.',
            'password.symbols'   => 'La contraseña debe incluir al menos un carácter especial (!@#$%&*...).',
            'password.uncompromised' => 'Esta contraseña ha aparecido en filtraciones de seguridad. Elige otra.',

            // --- Rol y servicio ---
            'tipo_servicio.required' => 'El tipo de servicio es obligatorio.',
            'tipo_servicio.in'       => 'El tipo de servicio seleccionado no es válido.',
            'role.required'          => 'Debes seleccionar un rol.',
            'role.exists'            => 'El rol seleccionado no es válido.',

            // --- Cédula y PIN ---
            'cedula_profesional.required' => 'La cédula profesional es obligatoria para médicos.',
            'pin.required'                => 'El PIN es obligatorio para el rol médico.',
            'pin.digits'                  => 'El PIN debe tener exactamente 4 dígitos.',

            // --- Archivos ---
            'foto_perfil.image'    => 'La foto de perfil debe ser una imagen.',
            'foto_perfil.max'      => 'La foto de perfil no debe pesar más de 2 MB.',
            'firma_archivo.image'  => 'La firma debe ser una imagen.',
            'firma_archivo.max'    => 'La firma no debe pesar más de 2 MB.',

            // --- Estado ---
            'activo.boolean' => 'El estado activo no es válido.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre'             => 'nombre',
            'apellido_paterno'   => 'apellido paterno',
            'apellido_materno'   => 'apellido materno',
            'curp'               => 'CURP',
            'fecha_nacimiento'   => 'fecha de nacimiento',
            'cedula_profesional' => 'cédula profesional',
            'telefono'           => 'teléfono',
            'telefono_contacto'  => 'teléfono de contacto',
            'email'              => 'correo electrónico',
            'password'           => 'contraseña',
            'tipo_servicio'      => 'tipo de servicio',
            'role'               => 'rol',
            'foto_perfil'        => 'foto de perfil',
            'firma_archivo'      => 'firma',
            'firma_canvas'       => 'firma',
            'pin'                => 'PIN',
            'activo'             => 'estado activo',
        ];
    }

    /**
     * Regla reutilizable: médico, medicina, medico_general, m, etc.
     */
    private function esRolMedico(?string $rol): bool
    {
        $rol = strtolower(trim($rol ?? ''));

        return $rol === 'm' || str_starts_with($rol, 'medic');
    }
}