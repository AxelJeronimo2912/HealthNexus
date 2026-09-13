<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">
                {{ $medicamento->nombre }} {{ $medicamento->concentracion }}
            </h2>
            <a href="{{ route('existencias.index') }}" class="text-sm text-gray-600 hover:underline">
                ← Volver
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- Resumen --}}
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-500">{{ $medicamento->sustancia_activa ?? '—' }}</p>
                    <p class="text-sm text-gray-500">{{ $medicamento->presentacion }} —
                        {{ $medicamento->via_administracion }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-500 uppercase">Stock total</p>
                    <p
                        class="text-3xl font-bold
                        {{ $medicamento->stock_total_calculado <= 0 ? 'text-red-600' : ($medicamento->stock_total_calculado <= $medicamento->stock_minimo ? 'text-yellow-600' : 'text-green-600') }}">
                        {{ $medicamento->stock_total_calculado }}
                    </p>
                    <p class="text-xs text-gray-500">
                        Mínimo: {{ $medicamento->stock_minimo }} | Máximo: {{ $medicamento->stock_maximo }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Lotes activos --}}
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-gray-800">Lotes activos</h3>
                <a href="{{ route('existencias.lotes', ['medicamento' => $medicamento->id]) }}"
                    class="text-xs text-blue-600 hover:underline">Ver todos los lotes →</a>
            </div>

            @if ($lotes->isEmpty())
                <p class="text-sm text-gray-500">Sin lotes registrados.</p>
            @else
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs text-gray-500 uppercase">Lote</th>
                            <th class="px-3 py-2 text-left text-xs text-gray-500 uppercase">Caducidad</th>
                            <th class="px-3 py-2 text-right text-xs text-gray-500 uppercase">Inicial</th>
                            <th class="px-3 py-2 text-right text-xs text-gray-500 uppercase">Disponible</th>
                            <th class="px-3 py-2 text-left text-xs text-gray-500 uppercase">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($lotes as $lote)
                            <tr>
                                <td class="px-3 py-2">{{ $lote->codigo_lote ?? 'LOTE-' . $lote->id }}</td>
                                <td class="px-3 py-2">
                                    {{ $lote->fecha_caducidad->format('d/m/Y') }}
                                    <span class="text-xs text-gray-500 block">
                                        @if ($lote->esta_caducado)
                                            Caducó hace {{ abs($lote->dias_para_caducar) }} días
                                        @else
                                            Vence en {{ $lote->dias_para_caducar }} días
                                        @endif
                                    </span>
                                </td>
                                <td class="px-3 py-2 text-right">{{ $lote->cantidad_inicial }}</td>
                                <td class="px-3 py-2 text-right font-semibold">{{ $lote->cantidad_disponible }}</td>
                                <td class="px-3 py-2">
                                    <span class="px-2 py-1 rounded text-xs {{ $lote->estado_color }}">
                                        {{ $lote->estado_label }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        {{-- Movimientos recientes --}}
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="font-bold text-gray-800 mb-4">Movimientos recientes</h3>
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs text-gray-500 uppercase">Fecha</th>
                        <th class="px-3 py-2 text-left text-xs text-gray-500 uppercase">Tipo</th>
                        <th class="px-3 py-2 text-left text-xs text-gray-500 uppercase">Lote</th>
                        <th class="px-3 py-2 text-right text-xs text-gray-500 uppercase">Cantidad</th>
                        <th class="px-3 py-2 text-left text-xs text-gray-500 uppercase">Motivo</th>
                        <th class="px-3 py-2 text-left text-xs text-gray-500 uppercase">Usuario</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($movimientos as $mov)
                        <tr>
                            <td class="px-3 py-2">{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-3 py-2">
                                <span class="px-2 py-1 rounded text-xs {{ $mov->tipo_color }}">
                                    {{ $mov->tipo_label }}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-xs">{{ $mov->lote?->codigo_lote ?? '—' }}</td>
                            <td
                                class="px-3 py-2 text-right {{ $mov->cantidad >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $mov->cantidad >= 0 ? '+' : '' }}{{ $mov->cantidad }}
                            </td>
                            <td class="px-3 py-2 text-xs text-gray-500">{{ $mov->motivo ?? '—' }}</td>
                            <td class="px-3 py-2 text-xs">{{ $mov->user?->nombre_completo ?? 'Sistema' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-gray-500 py-4">Sin movimientos.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
