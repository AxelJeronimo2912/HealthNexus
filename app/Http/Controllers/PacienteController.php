<?php

namespace App\Http\Controllers;

use App\Models\Estado;
use App\Models\Municipio;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class PacienteController extends Controller
{
    
    private function reglas(?Paciente $paciente = null): array
    {
        return [
            // ==================== IDENTIDAD ====================
            'nombre' => ['required', 'string', 'min:2', 'max:100', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñÜü\s\'-]+$/'],
            'apellido_paterno' => ['required', 'string', 'min:2', 'max:100', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñÜü\s\'-]+$/'],
            'apellido_materno' => ['required', 'string', 'min:2', 'max:100', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñÜü\s\'-]+$/'],
            'fecha_nacimiento' => ['required', 'date', 'before:today', 'after:1900-01-01'],
            'sexo' => ['required', 'in:hombre,mujer,otro'],
            'estado_civil' => ['required', 'string', 'in:Soltero,Casado,Divorciado,Viudo,Unión Libre'],
            'nacionalidad' => ['required', 'string', 'in:MEXICANA,EXTRANJERA'],

            // --- Condicionales según nacionalidad ---
            'estado_nacimiento' => ['required_if:nacionalidad,MEXICANA', 'nullable', 'string', 'max:100'],
            'curp' => [
                'required_if:nacionalidad,MEXICANA',
                'nullable',
                'string',
                'size:18',
                $paciente
                    ? 'unique:pacientes,curp,' . $paciente->id
                    : 'unique:pacientes,curp',
            ],
            'pais_nacimiento' => ['required_if:nacionalidad,EXTRANJERA', 'nullable', 'string', 'max:100'],
            'pasaporte' => ['required_if:nacionalidad,EXTRANJERA', 'nullable', 'string', 'max:50'],

            // ==================== CONTACTO ====================
            'telefono_principal' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s()]+$/'],
            'correo_electronico' => ['required', 'email:rfc', 'max:255'],
            'ocupacion' => ['required', 'string', 'max:100'],
            'responsable_nombre' => ['required', 'string', 'max:150'],

            // ==================== SALUD ====================
            'tipo_sanguineo' => ['required', 'string', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'alergias' => ['required', 'string', 'max:1000'],
            'enfermedades_cronicas' => ['required', 'string', 'max:2000'],

            // ==================== DOMICILIO ====================
            'estado_id' => ['required', 'exists:estados,id'],
            'municipio_id' => ['required', 'integer'], // se valida cruce con estado_id en after()
            'colonia' => ['required', 'string', 'max:100'],
            'calle' => ['required', 'string', 'max:150'],
            'numero_exterior' => ['required', 'string', 'max:20'],
            'numero_interior' => ['required', 'string', 'max:20'],

            // ==================== ESTADO ====================
            'activo' => ['sometimes', 'boolean'],
        ];
    }

    private function mensajes(): array
    {
        return [
            // Nombre
            'nombre.required' => 'El nombre del paciente es obligatorio.',
            'nombre.min' => 'El nombre debe tener al menos 2 caracteres.',
            'nombre.max' => 'El nombre no debe superar los 100 caracteres.',
            'nombre.regex' => 'El nombre solo puede contener letras, espacios, apóstrofes y guiones.',

            // Apellido paterno
            'apellido_paterno.required' => 'El apellido paterno es obligatorio.',
            'apellido_paterno.min' => 'El apellido paterno debe tener al menos 2 caracteres.',
            'apellido_paterno.max' => 'El apellido paterno no debe superar los 100 caracteres.',
            'apellido_paterno.regex' => 'El apellido paterno solo puede contener letras, espacios, apóstrofes y guiones.',

            // Apellido materno
            'apellido_materno.required' => 'El apellido materno es obligatorio.',
            'apellido_materno.min' => 'El apellido materno debe tener al menos 2 caracteres.',
            'apellido_materno.max' => 'El apellido materno no debe superar los 100 caracteres.',
            'apellido_materno.regex' => 'El apellido materno solo puede contener letras, espacios, apóstrofes y guiones.',

            // Fecha nacimiento
            'fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria.',
            'fecha_nacimiento.date' => 'Ingresa una fecha de nacimiento válida.',
            'fecha_nacimiento.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
            'fecha_nacimiento.after' => 'La fecha de nacimiento no puede ser anterior a 1900.',

            // Sexo
            'sexo.required' => 'Selecciona el sexo del paciente.',
            'sexo.in' => 'El sexo debe ser hombre, mujer u otro.',

            // Estado civil
            'estado_civil.required' => 'Selecciona el estado civil.',
            'estado_civil.in' => 'El estado civil seleccionado no es válido.',

            // Condicionales
            'estado_nacimiento.required_if' => 'El estado de nacimiento es obligatorio para nacionalidad mexicana.',
            'estado_nacimiento.max' => 'El estado de nacimiento no debe superar los 100 caracteres.',
            'curp.required_if' => 'El CURP es obligatorio para nacionalidad mexicana.',
            'curp.size' => 'El CURP debe tener exactamente 18 caracteres.',
            'curp.regex' => 'El formato del CURP no es válido. Ejemplo: XXXX000000HNEXXX09.',
            'curp.unique' => 'Este CURP ya está registrado para otro paciente.',
            'pais_nacimiento.required_if' => 'El país de nacimiento es obligatorio para nacionalidad extranjera.',
            'pais_nacimiento.max' => 'El país de nacimiento no debe superar los 100 caracteres.',
            'pasaporte.required_if' => 'El pasaporte es obligatorio para nacionalidad extranjera.',
            'pasaporte.max' => 'El pasaporte no debe superar los 50 caracteres.',

            // Nacionalidad
            'nacionalidad.required' => 'La nacionalidad es obligatoria.',
            'nacionalidad.in' => 'La nacionalidad debe ser MEXICANA o EXTRANJERA.',

            // Teléfono
            'telefono_principal.required' => 'El teléfono principal es obligatorio.',
            'telefono_principal.max' => 'El teléfono no debe superar los 20 caracteres.',
            'telefono_principal.regex' => 'El teléfono solo puede contener números, espacios, +, -, y paréntesis.',

            // Correo
            'correo_electronico.required' => 'El correo electrónico es obligatorio.',
            'correo_electronico.email' => 'Ingresa un correo electrónico válido.',
            'correo_electronico.max' => 'El correo no debe superar los 255 caracteres.',

            // Ocupación
            'ocupacion.required' => 'La ocupación es obligatoria.',
            'ocupacion.max' => 'La ocupación no debe superar los 100 caracteres.',

            // Responsable
            'responsable_nombre.required' => 'El nombre del responsable es obligatorio.',
            'responsable_nombre.max' => 'El nombre del responsable no debe superar los 150 caracteres.',

            // Salud
            'tipo_sanguineo.required' => 'Selecciona el tipo sanguíneo.',
            'tipo_sanguineo.in' => 'El tipo sanguíneo debe ser uno de: A+, A-, B+, B-, AB+, AB-, O+, O-.',
            'alergias.required' => 'La lista de alergias es obligatoria.',
            'alergias.max' => 'La lista de alergias no debe superar los 1000 caracteres.',
            'enfermedades_cronicas.required' => 'La lista de enfermedades crónicas es obligatoria.',
            'enfermedades_cronicas.max' => 'La lista de enfermedades crónicas no debe superar los 2000 caracteres.',

            // Domicilio
            'estado_id.required' => 'Selecciona un estado.',
            'estado_id.exists' => 'El estado seleccionado no es válido.',
            'municipio_id.required' => 'Selecciona un municipio.',
            'municipio_id.integer' => 'El municipio seleccionado no es válido.',
            'colonia.required' => 'La colonia es obligatoria.',
            'colonia.max' => 'La colonia no debe superar los 100 caracteres.',
            'calle.required' => 'La calle es obligatoria.',
            'calle.max' => 'La calle no debe superar los 150 caracteres.',
            'numero_exterior.required' => 'El número exterior es obligatorio.',
            'numero_exterior.max' => 'El número exterior no debe superar los 20 caracteres.',
            'numero_interior.required' => 'El número interior es obligatorio.',
            'numero_interior.max' => 'El número interior no debe superar los 20 caracteres.',
        ];
    }

    private function atributos(): array
    {
        return [
            'nombre' => 'nombre',
            'apellido_paterno' => 'apellido paterno',
            'apellido_materno' => 'apellido materno',
            'fecha_nacimiento' => 'fecha de nacimiento',
            'sexo' => 'sexo',
            'estado_civil' => 'estado civil',
            'nacionalidad' => 'nacionalidad',
            'pais_nacimiento' => 'país de nacimiento',
            'estado_nacimiento' => 'estado de nacimiento',
            'curp' => 'CURP',
            'pasaporte' => 'pasaporte',
            'telefono_principal' => 'teléfono principal',
            'correo_electronico' => 'correo electrónico',
            'ocupacion' => 'ocupación',
            'responsable_nombre' => 'nombre del responsable',
            'tipo_sanguineo' => 'tipo sanguíneo',
            'alergias' => 'alergias',
            'enfermedades_cronicas' => 'enfermedades crónicas',
            'estado_id' => 'estado',
            'municipio_id' => 'municipio',
            'colonia' => 'colonia',
            'calle' => 'calle',
            'numero_exterior' => 'número exterior',
            'numero_interior' => 'número interior',
        ];
    }

   
    /**
     * Normaliza la entrada antes de validar:
     * - trim en strings; "" → null
     * - CURP / pasaporte / nacionalidad en mayúsculas
     * - correo en minúsculas
     * - activo → bool
     */
    private function normalizar(array $input): array
    {
        foreach ($input as $key => $value) {
            if (is_string($value)) {
                $value = trim($value);
                $input[$key] = $value === '' ? null : $value;
            }
        }

        if (!empty($input['curp'])) {
            $input['curp'] = strtoupper($input['curp']);
        }
        if (!empty($input['pasaporte'])) {
            $input['pasaporte'] = strtoupper($input['pasaporte']);
        }
        if (!empty($input['nacionalidad'])) {
            $input['nacionalidad'] = strtoupper($input['nacionalidad']);
        }
        if (!empty($input['correo_electronico'])) {
            $input['correo_electronico'] = strtolower($input['correo_electronico']);
        }

        $input['activo'] = filter_var(
            $input['activo'] ?? false,
            FILTER_VALIDATE_BOOLEAN
        );

        return $input;
    }

    /**
     * Limpia los campos que no corresponden a la nacionalidad elegida.
     */
    private function limpiarSegunNacionalidad(array $data): array
    {
        if (($data['nacionalidad'] ?? null) === 'MEXICANA') {
            $data['pais_nacimiento'] = null;
            $data['pasaporte'] = null;
        } else {
            $data['estado_nacimiento'] = null;
            $data['curp'] = null;
        }

        return $data;
    }

    /**
     * Determina si el request espera JSON.
     */
    private function esperaJson(Request $request): bool
    {
        return $request->wantsJson()
            || $request->ajax()
            || $request->header('Accept') === 'application/json'
            || $request->header('X-Requested-With') === 'XMLHttpRequest';
    }

    /**
     * Construye el Validator con reglas, mensajes, atributos
     * y validación cruzada municipio ↔ estado.
     */
    private function validarPaciente(Request $request, ?Paciente $paciente = null)
    {
        $request->merge($this->normalizar($request->all()));

        $validator = Validator::make(
            $request->all(),
            $this->reglas($paciente),
            $this->mensajes(),
            $this->atributos()
        );

        $validator->after(function ($v) use ($request) {
            $estadoId = $request->input('estado_id');
            $municipioId = $request->input('municipio_id');

            if ($estadoId && $municipioId) {
                $pertenece = Municipio::where('id', $municipioId)
                    ->where('estado_id', $estadoId)
                    ->exists();

                if (! $pertenece) {
                    $v->errors()->add(
                        'municipio_id',
                        'El municipio seleccionado no pertenece al estado indicado.'
                    );
                }
            }
        });

        return $validator;
    }

  
    public function index(): View
    {
        $pacientes = Paciente::with(['estado', 'municipio'])
            ->latest('id')
            ->paginate(10);

        return view('pacientes.index', compact('pacientes'));
    }

    public function buscar(Request $request)
    {
        $busqueda = trim($request->get('q', ''));

        $pacientes = Paciente::with(['estado', 'municipio'])
            ->when($busqueda, function ($query, $busqueda) {
                $query->where(function ($q) use ($busqueda) {
                    $q->where('nombre', 'like', "%{$busqueda}%")
                      ->orWhere('apellido_paterno', 'like', "%{$busqueda}%")
                      ->orWhere('apellido_materno', 'like', "%{$busqueda}%")
                      ->orWhere('correo_electronico', 'like', "%{$busqueda}%")
                      ->orWhere('telefono_principal', 'like', "%{$busqueda}%")
                      ->orWhere('id', 'like', "%{$busqueda}%");
                });
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('pacientes._tabla', compact('pacientes', 'busqueda'))->render();
    }

    public function create(): View
    {
        return view('pacientes.create', [
            'estados' => Estado::orderBy('nombre')->get(),
            'municipios' => collect(),
        ]);
    }

    public function store(Request $request)
    {
        $validator = $this->validarPaciente($request);

        if ($validator->fails()) {
            if ($this->esperaJson($request)) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Hay errores en el formulario.',
                    'errors' => $validator->errors(),
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $data = $this->limpiarSegunNacionalidad($validator->validated());
        $data['activo'] = $request->boolean('activo');

        try {
            $paciente = Paciente::create($data);
        } catch (\Throwable $e) {
            report($e);

            if ($this->esperaJson($request)) {
                return response()->json([
                    'ok' => false,
                    'message' => 'No se pudo registrar el paciente. Intenta nuevamente.',
                ], 500);
            }

            return back()->withInput()
                ->with('error', 'No se pudo registrar el paciente. Intenta nuevamente.');
        }

        if ($this->esperaJson($request)) {
            return response()->json([
                'ok' => true,
                'mensaje' => 'Paciente registrado correctamente.',
                'paciente' => $paciente->load(['estado', 'municipio']),
            ], 201);
        }

        return redirect()->route('pacientes.index')
            ->with('success', 'Paciente registrado correctamente.');
    }

    public function show(Paciente $paciente): View
    {
        $paciente->load(['estado', 'municipio']);
        return view('pacientes.show', compact('paciente'));
    }

    public function edit(Paciente $paciente): View
    {
        return view('pacientes.edit', [
            'paciente' => $paciente,
            'estados' => Estado::orderBy('nombre')->get(),
            'municipios' => Municipio::where('estado_id', $paciente->estado_id)
                ->orderBy('nombre')
                ->get(),
        ]);
    }

    public function update(Request $request, Paciente $paciente)
    {
        $validator = $this->validarPaciente($request, $paciente);

        if ($validator->fails()) {
            if ($this->esperaJson($request)) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Hay errores en el formulario.',
                    'errors' => $validator->errors(),
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $data = $this->limpiarSegunNacionalidad($validator->validated());
        $data['activo'] = $request->boolean('activo');

        try {
            $paciente->update($data);
        } catch (\Throwable $e) {
            report($e);

            if ($this->esperaJson($request)) {
                return response()->json([
                    'ok' => false,
                    'message' => 'No se pudo actualizar el paciente. Intenta nuevamente.',
                ], 500);
            }

            return back()->withInput()
                ->with('error', 'No se pudo actualizar el paciente. Intenta nuevamente.');
        }

        if ($this->esperaJson($request)) {
            return response()->json([
                'ok' => true,
                'mensaje' => 'Paciente actualizado correctamente.',
                'paciente' => $paciente->fresh()->load(['estado', 'municipio']),
            ], 200);
        }

        return redirect()->route('pacientes.index')
            ->with('success', 'Paciente actualizado correctamente.');
    }

    public function destroy(Paciente $paciente)
    {
        try {
            $paciente->delete();
        } catch (\Throwable $e) {
            report($e);

            if ($this->esperaJson(request())) {
                return response()->json([
                    'ok' => false,
                    'message' => 'No se pudo eliminar el paciente.',
                ], 500);
            }

            return back()->with('error', 'No se pudo eliminar el paciente.');
        }

        if ($this->esperaJson(request())) {
            return response()->json([
                'ok' => true,
                'mensaje' => 'Paciente eliminado correctamente.',
            ]);
        }

        return redirect()->route('pacientes.index')
            ->with('success', 'Paciente eliminado correctamente.');
    }

    public function municipiosPorEstado(Estado $estado)
    {
        return response()->json(
            $estado->municipios()->orderBy('nombre')->get(['id', 'nombre'])
        );
    }
}