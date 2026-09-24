<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use App\Models\Especialidad;

class UserController extends Controller
{
    /**
     * Devuelve true si el rol es médico (o cualquier variante: medico a, b, m, etc.)
     */
    private function esRolMedico(?string $rol): bool
    {
        $rol = strtolower(trim($rol ?? ''));
        return $rol === 'm' || str_starts_with($rol, 'medic');
    }

    public function index(): View
    {
        $users = User::with('roles')
            ->orderBy('apellido_paterno')
            ->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        return view('admin.users.create', [
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $data['name'] = trim("{$data['nombre']} {$data['apellido_paterno']} {$data['apellido_materno']}");
        $data['activo'] = $request->boolean('activo');

        // Solo limpia el PIN si NO es un rol médico
        if (!$this->esRolMedico($data['role'] ?? '')) {
            $data['pin'] = null;
        }

        if ($request->hasFile('foto_perfil')) {
            $data['foto_perfil'] = $request->file('foto_perfil')->store('usuarios/fotos', 'public');
        }
        if ($request->hasFile('firma_archivo')) {
            $data['firma_archivo'] = $request->file('firma_archivo')->store('usuarios/firmas', 'public');
        }

        $user = User::create($data);
        $user->assignRole($data['role']);

        return redirect()->route('admin.users.index')
            ->with('success', 'Colaborador registrado correctamente.');
    }

    public function show(User $user): View
{
    $user->load(['roles', 'especialidades', 'especialidadPrincipal']);

    return view('admin.users.show', compact('user'));
}

    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'user' => $user,
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        $data['name'] = trim("{$data['nombre']} {$data['apellido_paterno']} {$data['apellido_materno']}");
        $data['activo'] = $request->boolean('activo');

        if (empty($data['password'])) {
            unset($data['password']);
        }

        // Manejo del PIN
        if (!$this->esRolMedico($data['role'] ?? '')) {
            // Si el nuevo rol no es médico, borrar PIN
            $data['pin'] = null;
        } elseif (empty($data['pin'])) {
            // Si sigue siendo médico y no se envió PIN, conservar el anterior
            unset($data['pin']);
        }

        if ($request->hasFile('foto_perfil')) {
            if ($user->foto_perfil) Storage::disk('public')->delete($user->foto_perfil);
            $data['foto_perfil'] = $request->file('foto_perfil')->store('usuarios/fotos', 'public');
        }
        if ($request->hasFile('firma_archivo')) {
            if ($user->firma_archivo) Storage::disk('public')->delete($user->firma_archivo);
            $data['firma_archivo'] = $request->file('firma_archivo')->store('usuarios/firmas', 'public');
        }

        $user->update($data);
        $user->syncRoles([$data['role']]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Colaborador actualizado correctamente.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminar tu propio usuario.');
        }

        if ($user->foto_perfil) Storage::disk('public')->delete($user->foto_perfil);
        if ($user->firma_archivo) Storage::disk('public')->delete($user->firma_archivo);

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Colaborador eliminado correctamente.');
    }
    

    public function regenerarPin(User $user): RedirectResponse
    {
        // Acepta cualquier rol que empiece con "medic"
        if (!$this->esRolMedico($user->getRoleNames()->first())) {
            return back()->with('error', 'Solo los médicos pueden tener PIN.');
        }

        $pin = str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);

        $user->pin = $pin;
        $user->save();

        return redirect()
            ->route('admin.users.show', $user)
            ->with('pin_generado', $pin);
    }

    public function especialidades(User $user): View
{
    $user->load('especialidades');

    $asignadas = $user->especialidades->pluck('id')->toArray();

    $disponibles = Especialidad::whereNotIn('id', $asignadas)
        ->where('activo', true)
        ->orderBy('nombre')
        ->get();

    return view('admin.users.especialidades', compact('user', 'disponibles'));
}

public function asignarEspecialidad(Request $request, User $user): RedirectResponse
{
    $data = $request->validate([
        'especialidad_id' => ['required', 'exists:especialidades,id'],
        'es_principal' => ['boolean'],
        'numero_cedula_especialidad' => ['nullable', 'string', 'max:50'],
        'fecha_certificacion' => ['nullable', 'date'],
    ]);

    if ($user->especialidades()->wherePivot('especialidad_id', $data['especialidad_id'])->exists()) {
        return back()->with('error', 'Esa especialidad ya está asignada al usuario.');
    }

    // Si es principal, quitar el flag de las demás
    if (!empty($data['es_principal'])) {
        \DB::table('especialidad_user')
            ->where('user_id', $user->id)
            ->update(['es_principal' => false]);
    }

    $user->especialidades()->attach($data['especialidad_id'], [
        'es_principal' => !empty($data['es_principal']),
        'numero_cedula_especialidad' => $data['numero_cedula_especialidad'] ?? null,
        'fecha_certificacion' => $data['fecha_certificacion'] ?? null,
        'activo' => true,
    ]);

    return back()->with('success', 'Especialidad asignada correctamente.');
}

public function quitarEspecialidad(User $user, $pivotId): RedirectResponse
{
    $user->especialidades()->newPivotStatement()
        ->where('id', $pivotId)
        ->where('user_id', $user->id)
        ->delete();

    return back()->with('success', 'Especialidad removida.');
}

public function marcarPrincipal(User $user, $pivotId): RedirectResponse
{
    // Quitar el flag principal de todas
    \DB::table('especialidad_user')
        ->where('user_id', $user->id)
        ->update(['es_principal' => false]);

    // Marcar la seleccionada como principal
    \DB::table('especialidad_user')
        ->where('id', $pivotId)
        ->where('user_id', $user->id)
        ->update(['es_principal' => true]);

    return back()->with('success', 'Especialidad marcada como principal.');
}
}