<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">Gestión de Camas</h2>
            <a href="{{ route('camas.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium flex items-center">
                <x-heroicon-o-plus class="w-4 h-4 mr-1" />
                Nueva Cama
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
        @endif

        {{-- Tarjetas resumen --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-xs text-gray-500 uppercase">Total</p>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-xs text-gray-500 uppercase">Disponibles</p>
                <p class="text-2xl font-bold text-green-600">{{ $stats['disponibles'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-xs text-gray-500 uppercase">Ocupadas</p>
                <p class="text-2xl font-bold text-red-600">{{ $stats['ocupadas'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-xs text-gray-500 uppercase">No disponibles</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $stats['mantenimiento'] }}</p>
            </div>
        </div>

        {{-- Filtros --}}
        <form method="GET" action="{{ route('camas.index') }}" class="mb-4 flex flex-wrap gap-2">
            <input type="text" name="buscar" value="{{ $busqueda }}"
                placeholder="Buscar por código, habitación o área"
                class="flex-1 min-w-[200px] border-gray-300 rounded-md shadow-sm">
            <select name="estado" class="border-gray-300 rounded-md shadow-sm">
                <option value="">Todos los estados</option>
                @foreach (['disponible', 'ocupada', 'mantenimiento', 'limpieza', 'fuera_servicio'] as $e)
                    <option value="{{ $e }}" @selected($filtroEstado == $e)>
                        {{ ucfirst(str_replace('_', ' ', $e)) }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">Filtrar</button>
            @if ($busqueda || $filtroEstado)
                <a href="{{ route('camas.index') }}" class="px-4 py-2 bg-white border rounded-md text-sm">Limpiar</a>
            @endif
        </form>

        {{-- Tabla --}}
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Código</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Área / Hab.</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Equipo</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($camas as $cama)
                        <tr>
                            <td class="px-4 py-3 text-sm font-medium">{{ $cama->codigo }}</td>
                            <td class="px-4 py-3 text-sm">
                                {{ $cama->area ?? '—' }}
                                @if ($cama->habitacion)
                                    <span class="text-gray-500">/ Hab. {{ $cama->habitacion }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">{{ $cama->tipo_label }}</td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 rounded text-xs {{ $cama->estado_color }}">
                                    {{ $cama->estado_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if ($cama->oxigeno)
                                    <span class="text-xs bg-blue-50 text-blue-700 px-1 rounded">O₂</span>
                                @endif
                                @if ($cama->monitor)
                                    <span class="text-xs bg-purple-50 text-purple-700 px-1 rounded">Mon</span>
                                @endif
                                @if ($cama->ventilador)
                                    <span class="text-xs bg-red-50 text-red-700 px-1 rounded">Vent</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right text-sm space-x-2">
                                <a href="{{ route('camas.show', $cama) }}"
                                    class="text-gray-600 hover:underline">Ver</a>
                                <a href="{{ route('camas.edit', $cama) }}"
                                    class="text-blue-600 hover:underline">Editar</a>
                                <form action="{{ route('camas.destroy', $cama) }}" method="POST" class="inline"
                                    onsubmit="return confirm('¿Eliminar esta cama?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-gray-500">Sin camas registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $camas->links() }}</div>
    </div>
</x-app-layout>
