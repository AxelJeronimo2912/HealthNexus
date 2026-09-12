<?php

namespace App\Http\Controllers;

use App\Models\Estado;
use App\Models\Municipio;
use App\Models\Paciente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PacienteController extends Controller
{
    public function index(): View
    {
        $pacientes = Paciente::with(['estado', 'municipio'])
            ->orderBy('apellido_paterno')
            ->paginate(10);

        return view('pacientes.index', compact('pacientes'));
    }

   public function create(): View
{
    return view('pacientes.create', [
        'estados' => Estado::orderBy('nombre')->get(),
        'municipios' => collect(),
    ]);
}

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'apellido_paterno' => ['required', 'string', 'max:100'],
            'apellido_materno' => ['nullable', 'string', 'max:100'],
            'fecha_nacimiento' => ['required', 'date', 'before:today'],
            'sexo' => ['required', 'in:hombre,mujer,otro'],
            'estado_civil' => ['nullable', 'string', 'max:50'],
            'nacionalidad' => ['required', 'string', 'max:50'],
            'pais_nacimiento' => ['nullable', 'string', 'max:100'],
            'estado_nacimiento' => ['nullable', 'string', 'max:100'],
            'curp' => ['nullable', 'string', 'size:18', 'unique:pacientes,curp'],
            'pasaporte' => ['nullable', 'string', 'max:50'],
            'telefono_principal' => ['nullable', 'string', 'max:20'],
            'correo_electronico' => ['nullable', 'email', 'max:255'],
            'ocupacion' => ['nullable', 'string', 'max:100'],
            'responsable_nombre' => ['nullable', 'string', 'max:150'],
            'tipo_sanguineo' => ['nullable', 'string', 'max:10'],
            'alergias' => ['nullable', 'string'],
            'enfermedades_cronicas' => ['nullable', 'string'],
            'estado_id' => ['nullable', 'exists:estados,id'],
            'municipio_id' => ['nullable', 'exists:municipios,id'],
            'colonia' => ['nullable', 'string', 'max:100'],
            'calle' => ['nullable', 'string', 'max:150'],
            'numero_exterior' => ['nullable', 'string', 'max:20'],
            'numero_interior' => ['nullable', 'string', 'max:20'],
            'activo' => ['boolean'],
        ]);

        $data['activo'] = $request->boolean('activo');

        Paciente::create($data);

        return redirect()->route('pacientes.index')
            ->with('success', 'Paciente registrado correctamente.');
    }

    public function show(Paciente $paciente): View
    {
        return view('pacientes.show', compact('paciente'));
    }

    public function edit(Paciente $paciente): View
{
    return view('pacientes.edit', [
        'paciente' => $paciente,
        'estados' => Estado::orderBy('nombre')->get(),
        'municipios' => Municipio::where('estado_id', $paciente->estado_id)->orderBy('nombre')->get(),
    ]);
}

    public function update(Request $request, Paciente $paciente): RedirectResponse
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'apellido_paterno' => ['required', 'string', 'max:100'],
            'apellido_materno' => ['nullable', 'string', 'max:100'],
            'fecha_nacimiento' => ['required', 'date', 'before:today'],
            'sexo' => ['required', 'in:hombre,mujer,otro'],
            'estado_civil' => ['nullable', 'string', 'max:50'],
            'nacionalidad' => ['required', 'string', 'max:50'],
            'pais_nacimiento' => ['nullable', 'string', 'max:100'],
            'estado_nacimiento' => ['nullable', 'string', 'max:100'],
            'curp' => ['nullable', 'string', 'size:18', 'unique:pacientes,curp,' . $paciente->id],
            'pasaporte' => ['nullable', 'string', 'max:50'],
            'telefono_principal' => ['nullable', 'string', 'max:20'],
            'correo_electronico' => ['nullable', 'email', 'max:255'],
            'ocupacion' => ['nullable', 'string', 'max:100'],
            'responsable_nombre' => ['nullable', 'string', 'max:150'],
            'tipo_sanguineo' => ['nullable', 'string', 'max:10'],
            'alergias' => ['nullable', 'string'],
            'enfermedades_cronicas' => ['nullable', 'string'],
            'estado_id' => ['nullable', 'exists:estados,id'],
            'municipio_id' => ['nullable', 'exists:municipios,id'],
            'colonia' => ['nullable', 'string', 'max:100'],
            'calle' => ['nullable', 'string', 'max:150'],
            'numero_exterior' => ['nullable', 'string', 'max:20'],
            'numero_interior' => ['nullable', 'string', 'max:20'],
            'activo' => ['boolean'],
        ]);

        $data['activo'] = $request->boolean('activo');

        $paciente->update($data);

        return redirect()->route('pacientes.index')
            ->with('success', 'Paciente actualizado correctamente.');
    }

    public function destroy(Paciente $paciente): RedirectResponse
    {
        $paciente->delete();

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