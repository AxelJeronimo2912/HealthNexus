<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Detalle del Rol: {{ ucfirst($role->name) }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded-lg shadow space-y-4">

            <h3 class="text-lg font-bold">{{ ucfirst($role->name) }}</h3>

            <div>
                <p class="font-semibold text-sm mb-1">Permisos asignados:</p>
                @forelse ($role->permissions as $perm)
                    <span class="inline-block px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs mr-1 mb-1">
                        {{ $perm->name }}
                    </span>
                @empty
                    <p class="text-sm text-gray-500">Sin permisos asignados.</p>
                @endforelse
            </div>

            <div>
                <p class="font-semibold text-sm mb-1">
                    Usuarios con este rol ({{ $role->users->count() }}):
                </p>
                <ul class="text-sm list-disc list-inside">
                    @forelse ($role->users as $u)
                        <li>{{ $u->nombre_completo ?: $u->name }} — {{ $u->email }}</li>
                    @empty
                        <li class="text-gray-500">Ninguno.</li>
                    @endforelse
                </ul>
            </div>

            <div class="pt-4 flex space-x-2">
                <a href="{{ route('admin.roles.edit', $role) }}"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm">Editar</a>
                <a href="{{ route('admin.roles.index') }}" class="px-4 py-2 bg-gray-100 rounded-md text-sm">Volver</a>
            </div>
        </div>
    </div>
</x-app-layout>
