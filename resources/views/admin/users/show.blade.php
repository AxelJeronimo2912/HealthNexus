<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Detalle del Colaborador</h2>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto sm:px-6 lg:px-8">

        {{-- Mensaje flash: PIN recién generado --}}
        @if (session('pin_generado'))
            <div class="mb-6 p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded">
                <p class="font-semibold text-yellow-800 mb-1">
                    Nuevo PIN generado
                </p>
                <p class="text-sm text-yellow-700 mb-3">
                    Copia este PIN y compártelo con el médico.
                    <strong>No se volverá a mostrar.</strong>
                </p>
                <div class="flex items-center gap-3">
                    <span
                        class="font-mono text-3xl font-bold tracking-widest text-yellow-900 bg-white px-4 py-2 rounded border">
                        {{ session('pin_generado') }}
                    </span>
                    <button type="button"
                        onclick="navigator.clipboard.writeText('{{ session('pin_generado') }}').then(() => alert('PIN copiado'))"
                        class="px-3 py-2 bg-yellow-100 hover:bg-yellow-200 rounded text-sm">
                        Copiar
                    </button>
                </div>
            </div>
        @endif

        <div class="bg-white p-6 rounded-lg shadow space-y-4">

            @if ($user->foto_perfil)
                <img src="{{ asset('storage/' . $user->foto_perfil) }}" class="w-24 h-24 rounded-full object-cover">
            @endif

            <h3 class="text-lg font-bold">{{ $user->nombre_completo }}</h3>

            <dl class="grid grid-cols-2 gap-3 text-sm">
                <dt class="font-semibold">CURP:</dt>
                <dd>{{ $user->curp }}</dd>
                <dt class="font-semibold">Fecha de Nacimiento:</dt>
                <dd>{{ $user->fecha_nacimiento?->format('d/m/Y') }}</dd>
                <dt class="font-semibold">Cédula Profesional:</dt>
                <dd>{{ $user->cedula_profesional ?? '—' }}</dd>
                <dt class="font-semibold">Teléfono:</dt>
                <dd>{{ $user->telefono }}</dd>
                <dt class="font-semibold">Teléfono Contacto:</dt>
                <dd>{{ $user->telefono_contacto ?? '—' }}</dd>
                <dt class="font-semibold">Correo:</dt>
                <dd>{{ $user->email }}</dd>
                <dt class="font-semibold">Tipo de servicio:</dt>
                <dd>{{ ucfirst($user->tipo_servicio) }}</dd>
                <dt class="font-semibold">Rol:</dt>
                <dd>{{ $user->getRoleNames()->first() }}</dd>
                <dt class="font-semibold">Estado:</dt>
                <dd>{{ $user->activo ? 'Activo' : 'Inactivo' }}</dd>
            </dl>

            {{-- PIN del médico --}}
            @if (strtolower($user->getRoleNames()->first() ?? '') === 'medico')
                <div class="border-t pt-4 mt-4">
                    <p class="font-semibold text-sm mb-2">PIN de acceso</p>

                    @if ($user->pin)
                        <div class="flex items-center gap-3">
                            <span
                                class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 rounded text-sm">
                                <x-heroicon-o-check-circle class="w-4 h-4 mr-1" />
                                PIN configurado
                            </span>

                            <form action="{{ route('admin.users.regenerar-pin', $user) }}" method="POST"
                                onsubmit="return confirm('¿Regenerar el PIN? El médico deberá usar el nuevo PIN.')">
                                @csrf
                                <button type="submit"
                                    class="text-sm bg-yellow-100 hover:bg-yellow-200 text-yellow-800 px-3 py-1 rounded">
                                    Regenerar PIN
                                </button>
                            </form>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">
                            Por seguridad, el PIN está hasheado y no puede mostrarse.
                            Si lo olvidó, regéneralo y compártelo.
                        </p>
                    @else
                        <span class="inline-flex items-center px-3 py-1 bg-red-100 text-red-800 rounded text-sm">
                            <x-heroicon-o-x-circle class="w-4 h-4 mr-1" />
                            Sin PIN configurado
                        </span>
                        <p class="text-xs text-gray-500 mt-2">
                            Este médico no podrá iniciar sesión hasta que se le asigne un PIN.
                        </p>
                    @endif
                </div>
            @endif

            @if ($user->firma_archivo)
                <div>
                    <p class="font-semibold text-sm mb-1">Firma (archivo):</p>
                    <img src="{{ asset('storage/' . $user->firma_archivo) }}" class="h-16 bg-white border">
                </div>
            @endif

            @if ($user->firma_canvas)
                <div>
                    <p class="font-semibold text-sm mb-1">Firma en pantalla:</p>
                    <img src="{{ $user->firma_canvas }}" class="h-16 bg-white border">
                </div>
            @endif


            {{-- ESPECIALIDADES --}}
            @php
                $esMedico = $user->roles->contains(function ($rol) {
                    $n = strtolower($rol->name);
                    return str_contains($n, 'medic') || str_contains($n, 'doctor') || str_contains($n, 'médic');
                });
            @endphp

            @if ($esMedico)
                <div class="border-t pt-4 mt-4">
                    <div class="flex justify-between items-center mb-2">
                        <p class="font-semibold text-sm">Especialidades médicas</p>
                        <a href="{{ route('admin.users.especialidades', $user) }}"
                            class="text-xs text-blue-600 hover:underline">Gestionar →</a>
                    </div>

                    @if ($user->especialidades->isEmpty())
                        <p class="text-sm text-gray-500">Sin especialidades asignadas.</p>
                    @else
                        <div class="flex flex-wrap gap-2">
                            @foreach ($user->especialidades as $esp)
                                <span
                                    class="px-3 py-1 rounded text-sm
                        {{ $esp->pivot->es_principal ? 'bg-yellow-100 text-yellow-800 border border-yellow-300' : 'bg-blue-50 text-blue-800' }}">
                                    {{ $esp->nombre }}
                                    @if ($esp->pivot->es_principal)
                                        ★
                                    @endif
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif{{-- ESPECIALIDADES --}}
            @php
                $esMedico = $user->roles->contains(function ($rol) {
                    $n = strtolower($rol->name);
                    return str_contains($n, 'medic') || str_contains($n, 'doctor') || str_contains($n, 'médic');
                });
            @endphp

            @if ($esMedico)
                <div class="border-t pt-4 mt-4">
                    <div class="flex justify-between items-center mb-2">
                        <p class="font-semibold text-sm">Especialidades médicas</p>
                        <a href="{{ route('admin.users.especialidades', $user) }}"
                            class="text-xs text-blue-600 hover:underline">Gestionar →</a>
                    </div>

                    @if ($user->especialidades->isEmpty())
                        <p class="text-sm text-gray-500">Sin especialidades asignadas.</p>
                    @else
                        <div class="flex flex-wrap gap-2">
                            @foreach ($user->especialidades as $esp)
                                <span
                                    class="px-3 py-1 rounded text-sm
                        {{ $esp->pivot->es_principal ? 'bg-yellow-100 text-yellow-800 border border-yellow-300' : 'bg-blue-50 text-blue-800' }}">
                                    {{ $esp->nombre }}
                                    @if ($esp->pivot->es_principal)
                                        ★
                                    @endif
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif
            <div class="pt-4 flex space-x-2">
                <a href="{{ route('admin.users.edit', $user) }}"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm">Editar</a>
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-100 rounded-md text-sm">Volver</a>
            </div>
        </div>
    </div>
</x-app-layout>
