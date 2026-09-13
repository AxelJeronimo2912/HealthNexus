<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Dispensación de Recetas</h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('dispensaciones.index', ['filtro' => 'pendientes']) }}"
                class="bg-white p-4 rounded-lg shadow hover:shadow-md transition">
                <p class="text-xs text-gray-500 uppercase">Pendientes</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $stats['pendientes'] }}</p>
            </a>
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-xs text-gray-500 uppercase">Dispensadas hoy</p>
                <p class="text-2xl font-bold text-green-600">{{ $stats['dispensadas_hoy'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-xs text-gray-500 uppercase">Dispensadas este mes</p>
                <p class="text-2xl font-bold text-blue-600">{{ $stats['dispensadas_mes'] }}</p>
            </div>
        </div>

        {{-- Filtros --}}
        <form method="GET" class="flex gap-2 flex-wrap">
            <input type="text" name="buscar" value="{{ $busqueda }}" placeholder="Buscar por paciente"
                class="flex-1 min-w-[200px] border-gray-300 rounded-md shadow-sm">
            <select name="filtro" class="border-gray-300 rounded-md shadow-sm">
                <option value="pendientes" @selected($filtro === 'pendientes')>Pendientes</option>
                <option value="dispensadas" @selected($filtro === 'dispensadas')>Dispensadas</option>
                <option value="todas" @selected($filtro === 'todas')>Todas</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">
                Filtrar
            </button>
        </form>

        {{-- Tabla --}}
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paciente</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Médico</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Medicamentos</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($recetas as $receta)
                        <tr>
                            <td class="px-4 py-3 text-sm">
                                {{ $receta->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <div class="font-medium">{{ $receta->paciente?->nombre_completo ?? '—' }}</div>
                                <div class="text-xs text-gray-500">
                                    {{ $receta->paciente?->curp ?? '—' }}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                {{ $receta->medico?->nombre_completo ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">
                                    {{ $receta->medicamentos->count() }} medicamentos
                                </span>
                                @if ($receta->receta_libre)
                                    <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded text-xs ml-1">
                                        +libre
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if ($receta->dispensada)
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">
                                        Dispensada
                                    </span>
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ $receta->dispensada_en?->format('d/m/Y H:i') }}
                                    </div>
                                @else
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs">
                                        Pendiente
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right text-sm space-x-2">
                                <a href="{{ route('dispensaciones.show', $receta) }}"
                                    class="text-blue-600 hover:underline">Ver receta</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                                Sin recetas para mostrar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $recetas->links() }}</div>
    </div>
</x-app-layout>
