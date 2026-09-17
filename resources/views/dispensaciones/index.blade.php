<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dispensación de Recetas</h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('dispensaciones.index', ['filtro' => 'pendientes']) }}"
                class="bg-white p-4 rounded-lg shadow-sm hover:shadow transition border border-gray-100">
                <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Pendientes</p>
                <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $stats['pendientes'] }}</p>
            </a>
            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100">
                <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Dispensadas hoy</p>
                <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['dispensadas_hoy'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100">
                <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Dispensadas este mes</p>
                <p class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['dispensadas_mes'] }}</p>
            </div>
        </div>

        {{-- Filtros --}}
        <form method="GET" class="flex gap-2 flex-wrap bg-white p-4 rounded-lg shadow-sm border border-gray-100">
            <input type="text" name="buscar" value="{{ $busqueda }}" placeholder="Buscar por paciente..."
                class="flex-1 min-w-[200px] border-gray-300 rounded-md shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
            <select name="filtro" class="border-gray-300 rounded-md shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="pendientes" @selected($filtro === 'pendientes')>Pendientes</option>
                <option value="dispensadas" @selected($filtro === 'dispensadas')>Dispensadas</option>
                <option value="todas" @selected($filtro === 'todas')>Todas</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white rounded-md text-sm transition">
                Filtrar
            </button>
        </form>

        {{-- Tabla --}}
        <div class="bg-white shadow-sm rounded-lg overflow-hidden border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paciente</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Médico</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Medicamentos</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($recetas as $receta)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-sm text-gray-600">
                                {{ $receta->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <div class="font-medium text-gray-900">{{ $receta->paciente?->nombre_completo ?? '—' }}</div>
                                <div class="text-xs text-gray-500">CURP: {{ $receta->paciente?->curp ?? '—' }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                {{ $receta->medico?->nombre_completo ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2.5 py-0.5 bg-blue-50 text-blue-700 border border-blue-200 rounded-full text-xs font-medium">
                                    {{ $receta->medicamentos->count() }} meds
                                </span>
                                @if ($receta->receta_libre)
                                    <span class="px-2.5 py-0.5 bg-purple-50 text-purple-700 border border-purple-200 rounded-full text-xs font-medium ml-1">
                                        Libre
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if ($receta->dispensada)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                                        Dispensada
                                    </span>
                                    <div class="text-xs text-gray-400 mt-0.5">
                                        {{ $receta->dispensada_en?->format('d/m/Y H:i') }}
                                    </div>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-50 text-yellow-700 border border-yellow-200">
                                        Pendiente
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right text-sm">
                                <a href="{{ route('dispensaciones.show', $receta) }}"
                                    class="inline-flex items-center px-3 py-1.5 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded-md text-xs font-medium transition">
                                    Ver receta
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500 text-sm">
                                No se encontraron recetas para mostrar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $recetas->links() }}</div>
    </div>
</x-app-layout>