<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">Especialidades Médicas</h2>
            <a href="{{ route('especialidades.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium flex items-center">
                <x-heroicon-o-plus class="w-4 h-4 mr-1" />
                Nueva Especialidad
            </a>
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
                <p class="text-xs text-gray-500 uppercase">Activas</p>
                <p class="text-2xl font-bold text-green-600">{{ $stats['activas'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-xs text-gray-500 uppercase">Grupos</p>
                <p class="text-2xl font-bold text-blue-600">{{ $stats['grupos'] }}</p>
            </div>
        </div>

        {{-- Filtros --}}
        <form method="GET" class="flex gap-2 flex-wrap">
            <input type="text" name="buscar" value="{{ $busqueda }}" placeholder="Buscar por nombre o código"
                class="flex-1 min-w-[200px] border-gray-300 rounded-md shadow-sm">
            <select name="grupo" class="border-gray-300 rounded-md shadow-sm">
                <option value="">Todos los grupos</option>
                <option value="clinica" @selected($grupo === 'clinica')>Clínica</option>
                <option value="quirurgica" @selected($grupo === 'quirurgica')>Quirúrgica</option>
                <option value="diagnostica" @selected($grupo === 'diagnostica')>Diagnóstica</option>
                <option value="basica" @selected($grupo === 'basica')>Básica</option>
                <option value="otra" @selected($grupo === 'otra')>Otra</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">
                Filtrar
            </button>
            @if ($busqueda || $grupo)
                <a href="{{ route('especialidades.index') }}"
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
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Grupo</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Duración</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Médicos</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Servicios</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Consultas</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($especialidades as $esp)
                        <tr>
                            <td class="px-4 py-3 text-sm font-mono">{{ $esp->codigo }}</td>
                            <td class="px-4 py-3 text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    @if ($esp->color)
                                        <span class="w-3 h-3 rounded-full"
                                            style="background-color: {{ $esp->color_hex }}"></span>
                                    @endif
                                    {{ $esp->nombre }}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 rounded text-xs {{ $esp->grupo_color }}">
                                    {{ $esp->grupo_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-center">
                                {{ $esp->duracion_consulta_default }} min
                            </td>
                            <td class="px-4 py-3 text-sm text-center">
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">
                                    {{ $esp->medicos_count }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-center">
                                <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded text-xs">
                                    {{ $esp->servicios_count }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-center">
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">
                                    {{ $esp->consultas_count }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if ($esp->activo)
                                    <span class="text-green-600 font-semibold">Activa</span>
                                @else
                                    <span class="text-red-600 font-semibold">Inactiva</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right text-sm space-x-2 whitespace-nowrap">
                                <a href="{{ route('especialidades.show', $esp) }}"
                                    class="text-gray-600 hover:underline">Ver</a>
                                <a href="{{ route('especialidades.edit', $esp) }}"
                                    class="text-blue-600 hover:underline">Editar</a>
                                <a href="{{ route('especialidades.medicos', $esp) }}"
                                    class="text-purple-600 hover:underline">Médicos</a>
                                <a href="{{ route('especialidades.servicios', $esp) }}"
                                    class="text-indigo-600 hover:underline">Servicios</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-6 text-center text-gray-500">
                                Sin especialidades registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $especialidades->links() }}</div>
    </div>
</x-app-layout>
