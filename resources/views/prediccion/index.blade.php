<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-3">
            <div class="min-w-0">
                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight truncate">
                    Predicción IA de Inventario
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Análisis de consumo histórico y predicción de demanda</p>
            </div>
            <form method="GET" class="flex items-end gap-2 shrink-0">
                <div>
                    <label
                        class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Periodo</label>
                    <select name="dias" onchange="this.form.submit()"
                        class="bg-white border border-slate-200 hover:border-indigo-200 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-700 focus:border-indigo-500 focus:ring-0 outline-none cursor-pointer transition-all">
                        <option value="15" @selected($dias == 15)>15 días</option>
                        <option value="30" @selected($dias == 30)>30 días</option>
                        <option value="60" @selected($dias == 60)>60 días</option>
                        <option value="90" @selected($dias == 90)>90 días</option>
                    </select>
                </div>
            </form>
        </div>
    </x-slot>

    @php
        $labelCls = 'text-[11px] font-bold text-slate-400 uppercase tracking-wider';
        $thCls = 'px-5 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider';
    @endphp

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- ============ ALERTAS CRÍTICAS ============ --}}
        @if (count($alertas))
            <div class="space-y-3">
                @foreach ($alertas as $alerta)
                    <div class="p-4 bg-rose-50 border border-rose-100 rounded-2xl flex items-start gap-3">
                        <div
                            class="w-10 h-10 rounded-2xl bg-rose-100 flex items-center justify-center text-rose-600 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-extrabold text-rose-900">{{ $alerta['titulo'] }}</p>
                            <p class="text-xs font-medium text-rose-700 mt-0.5">{{ $alerta['mensaje'] }}</p>
                        </div>
                        <a href="{{ route('prediccion.show', $alerta['medicamento_id']) }}"
                            class="px-3 py-1.5 bg-rose-100 hover:bg-rose-200 text-rose-700 rounded-lg text-[11px] font-bold transition-all whitespace-nowrap shrink-0">
                            Ver detalle →
                        </a>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- ============ STATS ============ --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Medicamentos</p>
                <p class="text-3xl font-black text-slate-800 tracking-tight mt-1">{{ $stats['total_medicamentos'] }}</p>
            </div>
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm">
                <p class="text-[11px] font-bold text-rose-600/70 uppercase tracking-wider">Stock crítico</p>
                <p class="text-3xl font-black text-rose-600 tracking-tight mt-1">{{ $stats['medicamentos_criticos'] }}
                </p>
            </div>
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm">
                <p class="text-[11px] font-bold text-amber-600/70 uppercase tracking-wider">Stock bajo</p>
                <p class="text-3xl font-black text-amber-600 tracking-tight mt-1">{{ $stats['medicamentos_bajos'] }}</p>
            </div>
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm">
                <p class="text-[11px] font-bold text-emerald-600/70 uppercase tracking-wider">Stock OK</p>
                <p class="text-3xl font-black text-emerald-600 tracking-tight mt-1">{{ $stats['medicamentos_ok'] }}</p>
            </div>
        </div>

        {{-- ============ PREDICCIÓN DE AGOTAMIENTO ============ --}}
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="font-extrabold text-slate-800 text-base">Predicción de agotamiento</h3>
                <p class="text-[11px] text-slate-400">Días estimados hasta agotar el stock según el consumo promedio</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-slate-50/70 border-b border-slate-100">
                        <tr>
                            <th class="{{ $thCls }} text-left">Medicamento</th>
                            <th class="{{ $thCls }} text-right">Stock</th>
                            <th class="{{ $thCls }} text-right">Consumo/día</th>
                            <th class="{{ $thCls }} text-right">Días restantes</th>
                            <th class="{{ $thCls }} text-center">Estado</th>
                            <th class="{{ $thCls }} text-right">Sugerido pedir</th>
                            <th class="px-5 py-3.5"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach (array_slice($agotamiento, 0, 15) as $item)
                            <tr class="hover:bg-indigo-50/30 transition-colors">
                                <td class="px-5 py-3.5">
                                    <p class="text-xs font-bold text-slate-800">{{ $item['nombre'] }}</p>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <span
                                        class="px-2 py-1 bg-slate-100 text-slate-700 rounded-lg text-[11px] font-mono font-bold">
                                        {{ $item['stock_actual'] }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-right text-xs font-semibold text-slate-700">
                                    {{ $item['promedio_diario'] }}
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    @if ($item['dias_restantes'] !== null && $item['dias_restantes'] <= 7)
                                        <span
                                            class="px-2 py-1 bg-rose-50 text-rose-700 border border-rose-100 rounded-lg text-[11px] font-mono font-bold">
                                            {{ $item['dias_restantes'] }}
                                        </span>
                                    @elseif ($item['dias_restantes'] !== null && $item['dias_restantes'] <= 15)
                                        <span
                                            class="px-2 py-1 bg-amber-50 text-amber-700 border border-amber-100 rounded-lg text-[11px] font-mono font-bold">
                                            {{ $item['dias_restantes'] }}
                                        </span>
                                    @else
                                        <span
                                            class="px-2 py-1 bg-slate-100 text-slate-600 rounded-lg text-[11px] font-mono font-bold">
                                            {{ $item['dias_restantes'] ?? '—' }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    @php
                                        $color = match ($item['estado']) {
                                            'critico' => 'bg-rose-50 text-rose-700 border-rose-100',
                                            'bajo' => 'bg-amber-50 text-amber-700 border-amber-100',
                                            'medio' => 'bg-indigo-50 text-indigo-700 border-indigo-100',
                                            'ok' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                            'sin_consumo' => 'bg-slate-100 text-slate-500 border-slate-200',
                                        };
                                        $label = match ($item['estado']) {
                                            'critico' => 'Crítico',
                                            'bajo' => 'Bajo',
                                            'medio' => 'Medio',
                                            'ok' => 'OK',
                                            'sin_consumo' => 'Sin consumo',
                                        };
                                    @endphp
                                    <span
                                        class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide border {{ $color }}">
                                        {{ $label }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    @if ($item['cantidad_sugerida'] > 0)
                                        <span
                                            class="px-2 py-1 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-lg text-[11px] font-mono font-bold">
                                            {{ $item['cantidad_sugerida'] }}
                                        </span>
                                    @else
                                        <span class="text-slate-300 text-xs">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex justify-end">
                                        <a href="{{ route('prediccion.show', $item['medicamento']->id) }}"
                                            class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-[11px] font-bold transition-all">
                                            Ver →
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ============ TOP DEMANDA + TENDENCIAS ============ --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Top demanda --}}
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100">
                    <h3 class="font-extrabold text-slate-800 text-base">Top 10 — Mayor demanda</h3>
                    <p class="text-[11px] text-slate-400">Últimos {{ $dias }} días</p>
                </div>

                <div class="p-5">
                    @if (empty($topDemanda))
                        <div class="py-8 text-center">
                            <p class="text-xs font-semibold text-slate-400">Sin movimientos registrados en el periodo.
                            </p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach ($topDemanda as $i => $item)
                                <div
                                    class="flex items-center gap-3 p-3 bg-slate-50/70 border border-slate-100 rounded-2xl hover:bg-indigo-50/30 hover:border-indigo-100 transition-all">
                                    <span
                                        class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-100 flex items-center justify-center text-[11px] font-bold shrink-0">
                                        {{ $i + 1 }}
                                    </span>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-bold text-slate-800 truncate">{{ $item['nombre'] }}</p>
                                        <p class="text-[10px] font-medium text-slate-400 mt-0.5">
                                            {{ $item['num_salidas'] }} salidas · prom.
                                            {{ $item['promedio_diario'] }}/día
                                        </p>
                                    </div>
                                    <span
                                        class="px-2 py-1 bg-indigo-50 text-indigo-700 border border-indigo-100 rounded-lg text-[11px] font-mono font-bold shrink-0">
                                        {{ $item['total_salidas'] }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Tendencias --}}
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100">
                    <h3 class="font-extrabold text-slate-800 text-base">Tendencias vs periodo anterior</h3>
                    <p class="text-[11px] text-slate-400">Cambio de consumo respecto al periodo previo</p>
                </div>

                <div class="p-5">
                    @if (empty($tendencias))
                        <div class="py-8 text-center">
                            <p class="text-xs font-semibold text-slate-400">Sin datos suficientes para comparar.</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach ($tendencias as $item)
                                @php
                                    if ($item['direccion'] === 'subiendo') {
                                        $iconBg = 'bg-rose-50 border-rose-100';
                                        $iconColor = 'text-rose-600';
                                        $valColor = 'text-rose-600';
                                    } elseif ($item['direccion'] === 'bajando') {
                                        $iconBg = 'bg-emerald-50 border-emerald-100';
                                        $iconColor = 'text-emerald-600';
                                        $valColor = 'text-emerald-600';
                                    } else {
                                        $iconBg = 'bg-slate-100 border-slate-200';
                                        $iconColor = 'text-slate-500';
                                        $valColor = 'text-slate-500';
                                    }
                                @endphp
                                <div
                                    class="flex items-center gap-3 p-3 bg-slate-50/70 border border-slate-100 rounded-2xl">
                                    <div
                                        class="w-7 h-7 rounded-lg {{ $iconBg }} border flex items-center justify-center shrink-0">
                                        @if ($item['direccion'] === 'subiendo')
                                            <svg class="w-4 h-4 {{ $iconColor }}" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2.5" d="M5 15l7-7 7 7" />
                                            </svg>
                                        @elseif ($item['direccion'] === 'bajando')
                                            <svg class="w-4 h-4 {{ $iconColor }}" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4 {{ $iconColor }}" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2.5" d="M5 12h14" />
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-bold text-slate-800 truncate">{{ $item['nombre'] }}</p>
                                        <p class="text-[10px] font-medium text-slate-400 mt-0.5">
                                            {{ $item['consumo_anterior'] }} → {{ $item['consumo_actual'] }}
                                        </p>
                                    </div>
                                    <span
                                        class="px-2 py-1 rounded-lg text-[11px] font-mono font-bold shrink-0
                                        {{ $item['direccion'] === 'subiendo' ? 'bg-rose-50 text-rose-700 border border-rose-100' : ($item['direccion'] === 'bajando' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-slate-100 text-slate-600') }}">
                                        {{ $item['cambio_porcentaje'] > 0 ? '+' : '' }}{{ $item['cambio_porcentaje'] }}%
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
