<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">Gestión de Colaboradores</h2>
            <a href="{{ route('admin.users.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                + Nuevo Colaborador
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
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Foto</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">CURP</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Correo</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rol</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($users as $user)
                        <tr>
                            <td class="px-4 py-3">
                                @if ($user->foto_perfil)
                                    <img src="{{ asset('storage/' . $user->foto_perfil) }}"
                                        class="w-10 h-10 rounded-full object-cover">
                                @else
                                    <div
                                        class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-xs">
                                        {{ strtoupper(substr($user->nombre ?? $user->name, 0, 1)) }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">{{ $user->nombre_completo ?: $user->name }}</td>
                            <td class="px-4 py-3 text-sm">{{ $user->curp }}</td>
                            <td class="px-4 py-3 text-sm">{{ $user->email }}</td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs">
                                    {{ $user->getRoleNames()->first() }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if ($user->activo)
                                    <span class="text-green-600 font-semibold">Activo</span>
                                @else
                                    <span class="text-red-600 font-semibold">Inactivo</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right text-sm space-x-2">
                                <a href="{{ route('admin.users.show', $user) }}"
                                    class="text-gray-600 hover:underline">Ver</a>
                                <a href="{{ route('admin.users.edit', $user) }}"
                                    class="text-blue-600 hover:underline">Editar</a>
                                <a href="{{ route('admin.users.turnos.index', $user) }}"
                                    class="text-purple-600 hover:underline">Turnos</a>
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline"
                                    onsubmit="return confirm('¿Eliminar este colaborador?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-gray-500">Sin colaboradores
                                registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $users->links() }}</div>
    </div>
</x-app-layout>
