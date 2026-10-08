<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-3">
            <div class="min-w-0">
                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight truncate">
                    Predicción — {{ $medicamento->nombre }} {{ $medicamento->concentracion }}
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Análisis predictivo de consumo y agotamiento</p>
            </div>
            <a href="{{ route('prediccion.index') }}"
                class="bg-white hover:bg-indigo-50 text-slate-700 hover:text-indigo-600 border border-slate-200 hover:border-indigo-200 px-5 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 transition-all active:scale-95 group shrink-0">
                <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
                Volver
            </a>
        </div>
    </x-slot>

    @php
        $labelCls = 'text-[11px] font-bold text-slate-400 uppercase tracking-wider';
    @endphp

    <div class="py-8 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- ============ RESUMEN ============ --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm">
                <p
                    class="text-[11px] font-bold uppercase tracking-wider {{ $stockActual <= $medicamento->stock_minimo ? 'text-rose-600/70' : 'text-emerald-600/70' }}">
                    Stock actual
                </p>
                <p
                    class="text-3xl font-black tracking-tight mt-1 {{ $stockActual <= $medicamento->stock_minimo ? 'text-rose-600' : 'text-emerald-600' }}">
                    {{ $stockActual }}
                </p>
            </div>
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm">
                <p class="text-[11px] font-bold text-indigo-600/70 uppercase tracking-wider">Predicción diaria</p>
                <p class="text-3xl font-black text-indigo-600 tracking-tight mt-1">{{ $consumoPromedio }}</p>
            </div>
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm">
                <p
                    class="text-[11px] font-bold uppercase tracking-wider {{ $diasRestantes !== null && $diasRestantes <= 7 ? 'text-rose-600/70' : 'text-slate-400' }}">
                    Días restantes
                </p>
                <p
                    class="text-3xl font-black tracking-tight mt-1 {{ $diasRestantes !== null && $diasRestantes <= 7 ? 'text-rose-600' : 'text-slate-800' }}">
                    {{ $diasRestantes ?? '—' }}
                </p>
            </div>
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Consumo total 90d</p>
                <p class="text-3xl font-black text-slate-800 tracking-tight mt-1">{{ $consumoTotal }}</p>
            </div>
        </div>

        {{-- ============ MODELOS USADOS ============ --}}
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-5">
            <div>
                <h3 class="font-extrabold text-slate-800 text-base">Modelos de predicción usados</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Algoritmos aplicados al consumo histórico</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-4 border-l-4 border-l-indigo-400">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Promedio 7 días</p>
                    <p class="text-2xl font-black text-slate-800 tracking-tight mt-1">
                        {{ $prediccion['promedio_7_dias'] }}</p>
                </div>
                <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-4 border-l-4 border-l-indigo-400">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Promedio 30 días</p>
                    <p class="text-2xl font-black text-slate-800 tracking-tight mt-1">
                        {{ $prediccion['promedio_30_dias'] }}</p>
                </div>
                <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-4 border-l-4 border-l-indigo-500">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Mov. ponderado</p>
                    <p class="text-2xl font-black text-slate-800 tracking-tight mt-1">
                        {{ $prediccion['movil_ponderado'] }}</p>
                </div>
                <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-4 border-l-4 border-l-purple-500">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Suavizado exp.</p>
                    <p class="text-2xl font-black text-slate-800 tracking-tight mt-1">
                        {{ $prediccion['suavizado_exponencial'] }}</p>
                </div>
            </div>

            {{-- Regresión lineal --}}
            <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-4 space-y-4">
                <div class="flex items-center gap-2">
                    <div
                        class="w-8 h-8 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-slate-800 text-sm">Regresión lineal</h4>
                        <p class="text-[11px] text-slate-400">Ajuste de tendencia sobre los datos históricos</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <div class="bg-white border border-slate-100 rounded-2xl p-3">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Pendiente</p>
                        <p class="text-xs font-mono font-bold text-slate-800 mt-1">
                            {{ $prediccion['regresion']['pendiente'] }}</p>
                    </div>
                    <div class="bg-white border border-slate-100 rounded-2xl p-3">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Intercepto</p>
                        <p class="text-xs font-mono font-bold text-slate-800 mt-1">
                            {{ $prediccion['regresion']['intercepto'] }}</p>
                    </div>
                    <div class="bg-white border border-slate-100 rounded-2xl p-3">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">R² (bondad)</p>
                        <p class="text-xs font-mono font-bold text-slate-800 mt-1">{{ $prediccion['regresion']['r2'] }}
                        </p>
                    </div>
                    <div class="bg-indigo-50/60 border border-indigo-100 rounded-2xl p-3">
                        <p class="text-[10px] font-bold text-indigo-600/70 uppercase tracking-wider">Predicción base</p>
                        <p class="text-xs font-mono font-bold text-indigo-700 mt-1">
                            {{ $prediccion['prediccion_diaria_base'] }}/día</p>
                    </div>
                </div>

                <p class="text-[11px] font-medium text-slate-500 italic leading-relaxed">
                    {{ $prediccion['regresion']['interpretacion'] }}
                </p>
            </div>
        </div>

        {{-- ============ GRÁFICA 1: HISTORIAL ============ --}}
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="font-extrabold text-slate-800 text-base">Historial de consumo</h3>
                <p class="text-[11px] text-slate-400">Últimos 90 días</p>
            </div>
            <div class="p-5">
                <div class="h-64">
                    <canvas id="chartHistorial"></canvas>
                </div>
            </div>
        </div>

        {{-- ============ GRÁFICA 2: PREDICCIÓN ============ --}}
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex flex-wrap justify-between items-center gap-3">
                <div>
                    <h3 class="font-extrabold text-slate-800 text-base">Predicción — Próximos 30 días</h3>
                    <p class="text-[11px] text-slate-400">
                        Total estimado:
                        <span class="font-bold text-slate-700">{{ $prediccion['total_predicho'] }}</span> unidades
                    </p>
                </div>
                <div class="flex flex-wrap gap-3 text-[11px] font-bold">
                    <span
                        class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-indigo-50 text-indigo-700 border border-indigo-100 rounded-full">
                        <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                        Consumo diario
                    </span>
                    <span
                        class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-full">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Acumulado
                    </span>
                </div>
            </div>
            <div class="p-5">
                <div class="h-72">
                    <canvas id="chartPrediccion"></canvas>
                </div>
            </div>
        </div>

        {{-- ============ ACCIONES ============ --}}
        <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex flex-wrap gap-2">
            <a href="{{ route('existencias.show', $medicamento) }}"
                class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all inline-flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                Ver existencias
            </a>
            <a href="{{ route('prediccion.index') }}"
                class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-600 rounded-xl text-xs font-bold transition-all">
                Volver
            </a>
        </div>
    </div>

    {{-- ============ SCRIPTS DE GRÁFICAS ============ --}}
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // ============ GRÁFICA 1: Historial ============
                const ctxHistorial = document.getElementById('chartHistorial');
                if (ctxHistorial) {
                    new Chart(ctxHistorial, {
                        type: 'line',
                        data: {
                            labels: @json($chartHistorial['labels']),
                            datasets: [{
                                label: 'Consumo diario',
                                data: @json($chartHistorial['data']),
                                borderColor: 'rgb(99, 102, 241)',
                                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                                tension: 0.3,
                                fill: true,
                                pointRadius: 2,
                                pointHoverRadius: 5,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    callbacks: {
                                        label: ctx => `Consumo: ${ctx.parsed.y}`
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Unidades'
                                    }
                                },
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Fecha'
                                    },
                                    ticks: {
                                        maxTicksLimit: 10
                                    }
                                }
                            }
                        }
                    });
                }

                // ============ GRÁFICA 2: Predicción ============
                const ctxPrediccion = document.getElementById('chartPrediccion');
                if (ctxPrediccion) {
                    new Chart(ctxPrediccion, {
                        data: {
                            labels: @json($chartPrediccion['labels']),
                            datasets: [{
                                    type: 'bar',
                                    label: 'Consumo diario predicho',
                                    data: @json($chartPrediccion['data']),
                                    backgroundColor: 'rgba(99, 102, 241, 0.7)',
                                    borderColor: 'rgb(99, 102, 241)',
                                    borderWidth: 1,
                                    yAxisID: 'y',
                                },
                                {
                                    type: 'line',
                                    label: 'Acumulado',
                                    data: @json($chartPrediccion['acumulado']),
                                    borderColor: 'rgb(16, 185, 129)',
                                    backgroundColor: 'rgba(16, 185, 129, 0.2)',
                                    tension: 0.3,
                                    fill: false,
                                    pointRadius: 2,
                                    yAxisID: 'y1',
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'top'
                                },
                                tooltip: {
                                    mode: 'index',
                                    intersect: false,
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    position: 'left',
                                    title: {
                                        display: true,
                                        text: 'Consumo diario'
                                    }
                                },
                                y1: {
                                    beginAtZero: true,
                                    position: 'right',
                                    title: {
                                        display: true,
                                        text: 'Acumulado'
                                    },
                                    grid: {
                                        drawOnChartArea: false
                                    }
                                },
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Fecha'
                                    },
                                    ticks: {
                                        maxTicksLimit: 15
                                    }
                                }
                            }
                        }
                    });
                }
            });
        </script>
    @endpush
</x-app-layout>
