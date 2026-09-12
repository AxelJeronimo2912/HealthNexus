<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">Catálogo de Turnos</h2>
            <a href="{{ route('admin.turnos.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium flex items-center">
                <x-heroicon-o-plus class="w-4 h-4 mr-1" />
                Nuevo Turno
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
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Código</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Horario</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Duración</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usuarios</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($turnos as $turno)
                        <tr>
                            <td class="px-4 py-3 text-sm font-mono">{{ $turno->codigo }}</td>
                            <td class="px-4 py-3 text-sm">{{ $turno->nombre }}</td>
                            <td class="px-4 py-3 text-sm">{{ $turno->rango }}</td>
                            <td class="px-4 py-3 text-sm">{{ $turno->duracion_horas }} h</td>
                            <td class="px-4 py-3 text-sm">{{ $turno->users_count }}</td>
                            <td class="px-4 py-3 text-sm">
                                @if ($turno->activo)
                                    <span class="text-green-600 font-semibold">Activo</span>
                                @else
                                    <span class="text-red-600 font-semibold">Inactivo</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right text-sm space-x-2">
                                <a href="{{ route('admin.turnos.show', $turno) }}"
                                    class="text-gray-600 hover:underline">Ver</a>
                                <a href="{{ route('admin.turnos.edit', $turno) }}"
                                    class="text-blue-600 hover:underline">Editar</a>
                                <form action="{{ route('admin.turnos.destroy', $turno) }}" method="POST"
                                    class="inline" onsubmit="return confirm('¿Eliminar este turno?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-gray-500">Sin turnos registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $turnos->links() }}</div>
    </div>
</x-app-layout>
