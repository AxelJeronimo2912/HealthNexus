<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">Existencias de Inventario</h2>
            <a href="{{ route('existencias.lotes') }}"
                class="text-sm bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-md">
                Ver por lotes
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- Estadísticas --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('existencias.index') }}" class="bg-white p-4 rounded-lg shadow hover:shadow-md transition">
                <p class="text-xs text-gray-500 uppercase">Medicamentos</p>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['total_medicamentos'] }}</p>
            </a>
            <a href="{{ route('existencias.index', ['filtro' => 'bajo']) }}"
                class="bg-white p-4 rounded-lg shadow hover:shadow-md transition">
                <p class="text-xs text-gray-500 uppercase">Stock bajo</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $stats['stock_bajo'] }}</p>
            </a>
            <a href="{{ route('existencias.index', ['filtro' => 'proximo']) }}"
                class="bg-white p-4 rounded-lg shadow hover:shadow-md transition">
                <p class="text-xs text-gray-500 uppercase">Próximos a caducar</p>
                <p class="text-2xl font-bold text-orange-600">{{ $stats['proximos_caducar'] }}</p>
            </a>
            <a href="{{ route('existencias.index', ['filtro' => 'caducado']) }}"
                class="bg-white p-4 rounded-lg shadow hover:shadow-md transition">
                <p class="text-xs text-gray-500 uppercase">Caducados</p>
                <p class="text-2xl font-bold text-red-600">{{ $stats['caducados'] }}</p>
            </a>
        </div>

        {{-- Buscador --}}
        <form method="GET" action="{{ route('existencias.index') }}" class="flex gap-2 flex-wrap">
            <input type="text" name="buscar" value="{{ $busqueda }}"
                placeholder="Buscar por nombre o sustancia"
                class="flex-1 min-w-[200px] border-gray-300 rounded-md shadow-sm">
            <select name="filtro" class="border-gray-300 rounded-md shadow-sm">
                <option value="">Todos</option>
                <option value="bajo" @selected($filtro === 'bajo')>Solo stock bajo</option>
                <option value="proximo" @selected($filtro === 'proximo')>Próximos a caducar</option>
                <option value="caducado" @selected($filtro === 'caducado')>Caducados</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">
                Filtrar
            </button>
            @if ($busqueda || $filtro)
                <a href="{{ route('existencias.index') }}"
                    class="px-4 py-2 bg-white border rounded-md text-sm">Limpiar</a>
            @endif
        </form>

        {{-- Tabla --}}
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Medicamento</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sustancia</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mínimo</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lotes</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Caducidad</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($medicamentos as $med)
                        @php
                            $stock = $med->stock_total_calculado;
                            $bajo = $stock <= $med->stock_minimo;

                            // Lote activo con la caducidad más próxima (siempre)
                            $loteProximo = $med->lotes
                                ->where('cantidad_disponible', '>', 0)
                                ->where('activo', true)
                                ->sortBy('fecha_caducidad')
                                ->first();
                        @endphp
                        <tr>
                            <td class="px-4 py-3 text-sm font-medium">
                                {{ $med->nombre }} {{ $med->concentracion }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                {{ $med->sustancia_activa ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if ($stock <= 0)
                                    <span class="text-red-600 font-bold">{{ $stock }}</span>
                                @elseif ($bajo)
                                    <span class="text-yellow-600 font-bold">{{ $stock }}</span>
                                @else
                                    <span class="text-green-600 font-bold">{{ $stock }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $med->stock_minimo }}</td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 bg-gray-100 rounded text-xs">
                                    {{ $med->lotes->count() }} activos
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if ($loteProximo)
                                    @php
                                        $dias = $loteProximo->dias_para_caducar;
                                        $color = $loteProximo->esta_caducado
                                            ? 'bg-red-100 text-red-800'
                                            : ($dias <= 30
                                                ? 'bg-yellow-100 text-yellow-800'
                                                : 'bg-green-100 text-green-800');
                                    @endphp
                                    <span class="px-2 py-1 rounded text-xs font-medium {{ $color }}">
                                        {{ $loteProximo->fecha_caducidad->format('d/m/Y') }}
                                    </span>
                                    <span class="text-xs text-gray-500 block mt-0.5">
                                        @if ($loteProximo->esta_caducado)
                                            Caducó hace {{ abs($dias) }} días
                                        @else
                                            Vence en {{ $dias }} días
                                        @endif
                                    </span>
                                @else
                                    <span class="text-gray-400 text-xs">Sin lotes</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right text-sm space-x-2">
                                <a href="{{ route('existencias.show', $med) }}"
                                    class="text-blue-600 hover:underline">Ver detalle</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                                Sin medicamentos en inventario.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $medicamentos->links() }}</div>
    </div>
</x-app-layout>
