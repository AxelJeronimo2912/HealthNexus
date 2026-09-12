<x-app-layout>
    @php
        $mapaPermisos = collect(config('menus.secciones'))
            ->flatMap(fn($s) => $s['items'])
            ->mapWithKeys(fn($i) => [$i['permiso'] => $i['label']]);
    @endphp
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">Roles y Permisos</h2>
            <a href="{{ route('admin.roles.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium flex items-center">
                <x-heroicon-o-plus class="w-4 h-4 mr-1" />
                Nuevo Rol
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
        @endif

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rol</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Permisos</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usuarios</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($roles as $role)
                        <tr>
                            <td class="px-4 py-3 text-sm font-medium">
                                {{ ucfirst($role->name) }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @forelse ($role->permissions as $perm)
                                    <span
                                        class="inline-block px-2 py-0.5 bg-gray-100 text-gray-700 rounded text-xs mr-1 mb-1">
                                        {{ $mapaPermisos[$perm->name] ?? $perm->name }}
                                    </span>
                                @empty
                                    <span class="text-gray-400 text-xs">Sin permisos</span>
                                @endforelse
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs">
                                    {{ $role->users_count }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right text-sm space-x-2">
                                <a href="{{ route('admin.roles.show', $role) }}"
                                    class="text-gray-600 hover:underline">Ver</a>
                                <a href="{{ route('admin.roles.edit', $role) }}"
                                    class="text-blue-600 hover:underline">Editar</a>
                                <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="inline"
                                    onsubmit="return confirm('¿Eliminar este rol?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-gray-500">Sin roles registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $roles->links() }}</div>
    </div>
</x-app-layout>
