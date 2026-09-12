<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">Medicamentos</h2>
            <a href="{{ route('medicamentos.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium flex items-center">
                <x-heroicon-o-plus class="w-4 h-4 mr-1" />
                Nuevo Medicamento
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
        @endif

        {{-- Buscador --}}
        <form method="GET" action="{{ route('medicamentos.index') }}" class="mb-4 flex gap-2">
            <input type="text" name="buscar" value="{{ $busqueda }}"
                placeholder="Buscar por nombre, sustancia o código de barras"
                class="flex-1 border-gray-300 rounded-md shadow-sm">
            <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">
                Buscar
            </button>
            @if ($busqueda)
                <a href="{{ route('medicamentos.index') }}"
                    class="px-4 py-2 bg-white border rounded-md text-sm">Limpiar</a>
            @endif
        </form>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sustancia</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Presentación</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock mín.</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($medicamentos as $med)
                        <tr>
                            <td class="px-4 py-3 text-sm">{{ $med->nombre_completo }}</td>
                            <td class="px-4 py-3 text-sm">{{ $med->sustancia_activa ?? '—' }}</td>
                            <td class="px-4 py-3 text-sm">{{ $med->presentacion ?? '—' }}</td>
                            <td class="px-4 py-3 text-sm">{{ $med->stock_minimo }}</td>
                            <td class="px-4 py-3 text-sm">
                                @if ($med->activo)
                                    <span class="text-green-600 font-semibold">Activo</span>
                                @else
                                    <span class="text-red-600 font-semibold">Inactivo</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right text-sm space-x-2">
                                <a href="{{ route('medicamentos.show', $med) }}"
                                    class="text-gray-600 hover:underline">Ver</a>
                                <a href="{{ route('medicamentos.edit', $med) }}"
                                    class="text-blue-600 hover:underline">Editar</a>
                                <form action="{{ route('medicamentos.destroy', $med) }}" method="POST" class="inline"
                                    onsubmit="return confirm('¿Eliminar este medicamento?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-gray-500">Sin medicamentos registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $medicamentos->links() }}</div>
    </div>
</x-app-layout>
