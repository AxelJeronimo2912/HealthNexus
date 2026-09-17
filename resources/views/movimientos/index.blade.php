<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">Movimientos de Inventario</h2>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- Estadísticas --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-xs text-gray-500 uppercase">Total movimientos</p>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['total_movimientos'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-xs text-gray-500 uppercase">Entradas hoy</p>
                <p class="text-2xl font-bold text-green-600">{{ $stats['entradas_hoy'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-xs text-gray-500 uppercase">Salidas hoy</p>
                <p class="text-2xl font-bold text-red-600">{{ $stats['salidas_hoy'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-xs text-gray-500 uppercase">Ajustes del mes</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $stats['ajustes_mes'] }}</p>
            </div>
        </div>

        {{-- Filtros --}}
        <form method="GET" action="{{ route('movimientos.index') }}"
            class="bg-white p-4 rounded-lg shadow grid grid-cols-1 md:grid-cols-5 gap-3">

            <div>
                <label class="block text-xs text-gray-500 uppercase">Medicamento</label>
                <select name="medicamento_id" class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
                    <option value="">Todos</option>
                    @foreach ($medicamentos as $m)
                        <option value="{{ $m->id }}" @selected($medicamentoId == $m->id)>
                            {{ $m->nombre }} {{ $m->concentracion }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs text-gray-500 uppercase">Tipo</label>
                <select name="tipo" class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
                    <option value="">Todos</option>
                    <option value="entrada" @selected($tipo === 'entrada')>Entrada</option>
                    <option value="salida" @selected($tipo === 'salida')>Salida</option>
                    <option value="ajuste" @selected($tipo === 'ajuste')>Ajuste</option>
                    <option value="devolucion" @selected($tipo === 'devolucion')>Devolución</option>
                </select>
            </div>

            <div>
                <label class="block text-xs text-gray-500 uppercase">Usuario</label>
                <select name="user_id" class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
                    <option value="">Todos</option>
                    @foreach ($usuarios as $u)
                        <option value="{{ $u->id }}" @selected($userId == $u->id)>
                            {{ $u->nombre }} {{ $u->apellido_paterno }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs text-gray-500 uppercase">Desde</label>
                <input type="date" name="desde" value="{{ $desde }}"
                    class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
            </div>

            <div>
                <label class="block text-xs text-gray-500 uppercase">Hasta</label>
                <input type="date" name="hasta" value="{{ $hasta }}"
                    class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
            </div>

            <div class="md:col-span-5 flex gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm">
                    Filtrar
                </button>
                <a href="{{ route('movimientos.index') }}"
                    class="px-4 py-2 bg-white border rounded-md text-sm hover:bg-gray-50">
                    Limpiar filtros
                </a>
            </div>
        </form>

        {{-- Tabla --}}
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Medicamento</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lote</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Stock</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Motivo</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usuario</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($movimientos as $mov)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm whitespace-nowrap">
                                {{ $mov->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 rounded text-xs {{ $mov->tipo_color }}">
                                    {{ $mov->tipo_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                {{ $mov->medicamento?->nombre ?? '—' }}
                                {{ $mov->medicamento?->concentracion }}
                            </td>
                            <td class="px-4 py-3 text-sm font-mono text-xs">
                                {{ $mov->lote?->codigo_lote ?? '—' }}
                            </td>
                            <td
                                class="px-4 py-3 text-sm text-right font-semibold
                                {{ $mov->cantidad >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $mov->cantidad >= 0 ? '+' : '' }}{{ $mov->cantidad }}
                            </td>
                            <td class="px-4 py-3 text-sm text-right">
                                <span class="text-xs text-gray-500">
                                    {{ $mov->stock_anterior }} →
                                </span>
                                <span class="font-semibold">{{ $mov->stock_nuevo }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600 max-w-xs truncate">
                                {{ $mov->motivo ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-xs text-gray-500">
                                {{ $mov->user?->nombre_completo ?? 'Sistema' }}
                            </td>
                            <td class="px-4 py-3 text-right text-sm">
                                <a href="{{ route('movimientos.show', $mov) }}"
                                    class="text-blue-600 hover:underline text-xs">Ver</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-6 text-center text-gray-500">
                                Sin movimientos para los filtros seleccionados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $movimientos->links() }}</div>
    </div>
</x-app-layout>
