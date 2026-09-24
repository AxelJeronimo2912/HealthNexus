<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800">Predicción IA de Inventario</h2>
                <p class="text-xs text-gray-500 mt-0.5">
                    Análisis de consumo histórico y predicción de demanda
                </p>
            </div>
            <form method="GET" class="flex items-center gap-2">
                <label class="text-xs text-gray-500">Periodo:</label>
                <select name="dias" onchange="this.form.submit()" class="border-gray-300 rounded-md shadow-sm text-sm">
                    <option value="15" @selected($dias == 15)>15 días</option>
                    <option value="30" @selected($dias == 30)>30 días</option>
                    <option value="60" @selected($dias == 60)>60 días</option>
                    <option value="90" @selected($dias == 90)>90 días</option>
                </select>
            </form>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- Alertas críticas --}}
        @if (count($alertas))
            <div class="space-y-2">
                @foreach ($alertas as $alerta)
                    <div class="p-4 bg-red-50 border-l-4 border-red-500 rounded-lg flex items-start gap-3">
                        <svg class="w-5 h-5 text-red-600 mt-0.5 shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01M5 19h14a2 2 0 001.84-2.75L13.74 4a2 2 0 00-3.5 0L3 16.25A2 2 0 005 19z" />
                        </svg>
                        <div>
                            <p class="font-bold text-red-900 text-sm">{{ $alerta['titulo'] }}</p>
                            <p class="text-sm text-red-700">{{ $alerta['mensaje'] }}</p>
                        </div>
                        <a href="{{ route('prediccion.show', $alerta['medicamento_id']) }}"
                            class="ml-auto text-xs text-red-700 hover:underline whitespace-nowrap">Ver detalle →</a>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Stats --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-xs text-gray-500 uppercase">Medicamentos</p>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['total_medicamentos'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-xs text-gray-500 uppercase">Stock crítico</p>
                <p class="text-2xl font-bold text-red-600">{{ $stats['medicamentos_criticos'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-xs text-gray-500 uppercase">Stock bajo</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $stats['medicamentos_bajos'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-xs text-gray-500 uppercase">Stock OK</p>
                <p class="text-2xl font-bold text-green-600">{{ $stats['medicamentos_ok'] }}</p>
            </div>
        </div>

        {{-- Predicción de agotamiento --}}
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="font-bold text-gray-800 mb-1">Predicción de agotamiento</h3>
            <p class="text-xs text-gray-500 mb-4">
                Días estimados hasta agotar el stock según el consumo promedio
            </p>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Medicamento</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Stock</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Consumo/día
                            </th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Días restantes
                            </th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Estado</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Sugerido pedir
                            </th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach (array_slice($agotamiento, 0, 15) as $item)
                            <tr>
                                <td class="px-3 py-2 text-sm font-medium">{{ $item['nombre'] }}</td>
                                <td class="px-3 py-2 text-sm text-right">{{ $item['stock_actual'] }}</td>
                                <td class="px-3 py-2 text-sm text-right">{{ $item['promedio_diario'] }}</td>
                                <td
                                    class="px-3 py-2 text-sm text-right font-semibold
                                    @if ($item['dias_restantes'] !== null && $item['dias_restantes'] <= 7) text-red-600
                                    @elseif ($item['dias_restantes'] !== null && $item['dias_restantes'] <= 15) text-yellow-600 @endif">
                                    {{ $item['dias_restantes'] ?? '—' }}
                                </td>
                                <td class="px-3 py-2 text-center">
                                    @php
                                        $color = match ($item['estado']) {
                                            'critico' => 'bg-red-100 text-red-800',
                                            'bajo' => 'bg-yellow-100 text-yellow-800',
                                            'medio' => 'bg-blue-100 text-blue-800',
                                            'ok' => 'bg-green-100 text-green-800',
                                            'sin_consumo' => 'bg-gray-100 text-gray-600',
                                        };
                                        $label = match ($item['estado']) {
                                            'critico' => 'Crítico',
                                            'bajo' => 'Bajo',
                                            'medio' => 'Medio',
                                            'ok' => 'OK',
                                            'sin_consumo' => 'Sin consumo',
                                        };
                                    @endphp
                                    <span class="px-2 py-0.5 rounded text-xs {{ $color }}">
                                        {{ $label }}
                                    </span>
                                </td>
                                <td class="px-3 py-2 text-sm text-right font-semibold">
                                    {{ $item['cantidad_sugerida'] > 0 ? $item['cantidad_sugerida'] : '—' }}
                                </td>
                                <td class="px-3 py-2 text-right text-xs">
                                    <a href="{{ route('prediccion.show', $item['medicamento']->id) }}"
                                        class="text-blue-600 hover:underline">Ver →</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Top demanda --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="font-bold text-gray-800 mb-4">
                    Top 10 — Mayor demanda (últimos {{ $dias }} días)
                </h3>

                @if (empty($topDemanda))
                    <p class="text-sm text-gray-500">Sin movimientos registrados en el periodo.</p>
                @else
                    <div class="space-y-3">
                        @foreach ($topDemanda as $i => $item)
                            <div class="flex items-center gap-3">
                                <span
                                    class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-bold shrink-0">
                                    {{ $i + 1 }}
                                </span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium truncate">{{ $item['nombre'] }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $item['num_salidas'] }} salidas —
                                        promedio {{ $item['promedio_diario'] }}/día
                                    </p>
                                </div>
                                <span class="text-sm font-bold text-indigo-600">
                                    {{ $item['total_salidas'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Tendencias --}}
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="font-bold text-gray-800 mb-4">
                    Tendencias vs periodo anterior
                </h3>

                @if (empty($tendencias))
                    <p class="text-sm text-gray-500">Sin datos suficientes para comparar.</p>
                @else
                    <div class="space-y-3">
                        @foreach ($tendencias as $item)
                            <div class="flex items-center gap-3">
                                @if ($item['direccion'] === 'subiendo')
                                    <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M5 15l7-7 7 7" />
                                    </svg>
                                @elseif ($item['direccion'] === 'bajando')
                                    <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-gray-400 shrink-0" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M5 12h14" />
                                    </svg>
                                @endif

                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium truncate">{{ $item['nombre'] }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $item['consumo_anterior'] }} → {{ $item['consumo_actual'] }}
                                    </p>
                                </div>
                                <span
                                    class="text-sm font-bold
                                    {{ $item['direccion'] === 'subiendo' ? 'text-red-600' : ($item['direccion'] === 'bajando' ? 'text-green-600' : 'text-gray-500') }}">
                                    {{ $item['cambio_porcentaje'] > 0 ? '+' : '' }}{{ $item['cambio_porcentaje'] }}%
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
