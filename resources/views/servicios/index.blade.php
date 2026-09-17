<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">Servicios Hospitalarios</h2>
            @can('servicios.ver')
                <a href="{{ route('servicios.create') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium flex items-center">
                    <x-heroicon-o-plus class="w-4 h-4 mr-1" />
                    Nuevo Servicio
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        @if (session('success'))
            <div class="p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="p-3 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
        @endif

        {{-- Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-xs text-gray-500 uppercase">Total</p>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-xs text-gray-500 uppercase">Activos</p>
                <p class="text-2xl font-bold text-green-600">{{ $stats['activos'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-xs text-gray-500 uppercase">Tipos</p>
                <p class="text-2xl font-bold text-blue-600">{{ $stats['tipos'] }}</p>
            </div>
        </div>

        {{-- Filtros --}}
        <form method="GET" class="flex gap-2 flex-wrap">
            <input type="text" name="buscar" value="{{ $busqueda }}"
                placeholder="Buscar por nombre, código o ubicación"
                class="flex-1 min-w-[200px] border-gray-300 rounded-md shadow-sm">
            <select name="tipo" class="border-gray-300 rounded-md shadow-sm">
                <option value="">Todos los tipos</option>
                <option value="consulta_externa" @selected($tipo === 'consulta_externa')>Consulta Externa</option>
                <option value="urgencias" @selected($tipo === 'urgencias')>Urgencias</option>
                <option value="hospitalizacion" @selected($tipo === 'hospitalizacion')>Hospitalización</option>
                <option value="quirofano" @selected($tipo === 'quirofano')>Quirófano</option>
                <option value="farmacia" @selected($tipo === 'farmacia')>Farmacia</option>
                <option value="enfermeria" @selected($tipo === 'enfermeria')>Enfermería</option>
                <option value="laboratorio" @selected($tipo === 'laboratorio')>Laboratorio</option>
                <option value="imagenologia" @selected($tipo === 'imagenologia')>Imagenología</option>
                <option value="otro" @selected($tipo === 'otro')>Otro</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">
                Filtrar
            </button>
            @if ($busqueda || $tipo)
                <a href="{{ route('servicios.index') }}"
                    class="px-4 py-2 bg-white border rounded-md text-sm">Limpiar</a>
            @endif
        </form>

        {{-- Tabla --}}
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Código</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ubicación</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Horario</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Personal</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Camas</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($servicios as $servicio)
                        <tr>
                            <td class="px-4 py-3 text-sm font-mono">{{ $servicio->codigo }}</td>
                            <td class="px-4 py-3 text-sm font-medium">{{ $servicio->nombre }}</td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 rounded text-xs {{ $servicio->tipo_color }}">
                                    {{ $servicio->tipo_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                {{ $servicio->ubicacion ?? '—' }}
                                @if ($servicio->piso)
                                    <span class="text-xs text-gray-500 block">{{ $servicio->piso }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                {{ $servicio->horario }}
                            </td>
                            <td class="px-4 py-3 text-sm text-center">
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">
                                    {{ $servicio->users_count }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-center">
                                <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs">
                                    {{ $servicio->camas_count }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if ($servicio->activo)
                                    <span class="text-green-600 font-semibold">Activo</span>
                                @else
                                    <span class="text-red-600 font-semibold">Inactivo</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right text-sm space-x-2">
                                <a href="{{ route('servicios.show', $servicio) }}"
                                    class="text-gray-600 hover:underline">Ver</a>
                                <a href="{{ route('servicios.edit', $servicio) }}"
                                    class="text-blue-600 hover:underline">Editar</a>
                                <a href="{{ route('servicios.personal', $servicio) }}"
                                    class="text-purple-600 hover:underline">Personal</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-6 text-center text-gray-500">
                                Sin servicios registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $servicios->links() }}</div>
    </div>
</x-app-layout>
