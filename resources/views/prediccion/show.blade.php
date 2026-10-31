<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="min-w-0">
                <div class="flex items-center gap-2 text-xs font-bold text-indigo-500 uppercase tracking-wider mb-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                    Análisis Predictivo
                </div>
                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight truncate">
                    {{ $medicamento->nombre }} <span
                        class="text-slate-400 font-medium">{{ $medicamento->concentracion }}</span>
                </h2>
            </div>
            <a href="{{ route('prediccion.index') }}"
                class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 px-5 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 transition-all active:scale-95 group shrink-0 shadow-sm">
                <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
                Volver al Ranking
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- ============ BANNER DE ALERTA CRÍTICA ============ --}}
        @if ($diasRestantes !== null && $diasRestantes <= 7)
            <div
                class="bg-rose-50 border border-rose-200 rounded-3xl p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-sm animate-pulse-slow">
                <div class="flex items-start gap-4">
                    <div
                        class="w-12 h-12 rounded-full bg-rose-100 flex items-center justify-center text-rose-600 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-rose-800">Alerta de desabasto próximo</h3>
                        <p class="text-xs text-rose-600 mt-1 font-medium">Quedan <span
                                class="font-bold">{{ $diasRestantes }} días</span> de stock según la predicción. Se
                            recomienda generar una orden de compra inmediata.</p>
                    </div>
                </div>
                <a href="#"
                    class="shrink-0 bg-rose-600 hover:bg-rose-700 text-white px-4 py-2 rounded-xl text-xs font-bold transition-colors shadow-sm">
                    Generar Orden de Compra
                </a>
            </div>
        @endif

        {{-- ============ RESUMEN (KPIs) ============ --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Stock Actual --}}
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm relative overflow-hidden group">
                <div
                    class="absolute -right-4 -top-4 w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center opacity-50 group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <p
                    class="text-[11px] font-bold uppercase tracking-wider {{ $stockActual <= $medicamento->stock_minimo ? 'text-rose-600' : 'text-emerald-600' }}">
                    Stock Actual
                </p>
                <div class="flex items-baseline gap-2 mt-1">
                    <p
                        class="text-3xl font-black tracking-tight {{ $stockActual <= $medicamento->stock_minimo ? 'text-rose-600' : 'text-slate-800' }}">
                        {{ number_format($stockActual) }}
                    </p>
                    <span class="text-xs font-semibold text-slate-400">uds.</span>
                </div>
                <p class="text-[10px] text-slate-400 font-medium mt-2">Mínimo requerido:
                    {{ $medicamento->stock_minimo }}</p>
            </div>

            {{-- Predicción Diaria --}}
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm relative overflow-hidden group">
                <div
                    class="absolute -right-4 -top-4 w-16 h-16 bg-indigo-50 rounded-full flex items-center justify-center opacity-50 group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                    </svg>
                </div>
                <p class="text-[11px] font-bold text-indigo-600 uppercase tracking-wider">Predicción Diaria</p>
                <div class="flex items-baseline gap-2 mt-1">
                    <p class="text-3xl font-black text-slate-800 tracking-tight">{{ $consumoPromedio }}</p>
                    <span class="text-xs font-semibold text-slate-400">uds/día</span>
                </div>
                <p class="text-[10px] text-slate-400 font-medium mt-2">Basado en tendencia histórica</p>
            </div>

            {{-- Días Restantes --}}
            <div
                class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm relative overflow-hidden group {{ $diasRestantes !== null && $diasRestantes <= 7 ? 'ring-2 ring-rose-100' : '' }}">
                <div
                    class="absolute -right-4 -top-4 w-16 h-16 {{ $diasRestantes !== null && $diasRestantes <= 7 ? 'bg-rose-50' : 'bg-slate-50' }} rounded-full flex items-center justify-center opacity-50 group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8 {{ $diasRestantes !== null && $diasRestantes <= 7 ? 'text-rose-300' : 'text-slate-300' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <p
                    class="text-[11px] font-bold uppercase tracking-wider {{ $diasRestantes !== null && $diasRestantes <= 7 ? 'text-rose-600' : 'text-slate-400' }}">
                    Días Restantes
                </p>
                <div class="flex items-baseline gap-2 mt-1">
                    <p
                        class="text-3xl font-black tracking-tight {{ $diasRestantes !== null && $diasRestantes <= 7 ? 'text-rose-600' : 'text-slate-800' }}">
                        {{ $diasRestantes ?? '—' }}
                    </p>
                    <span class="text-xs font-semibold text-slate-400">días</span>
                </div>
                <p
                    class="text-[10px] font-medium mt-2 {{ $diasRestantes !== null && $diasRestantes <= 7 ? 'text-rose-500' : 'text-slate-400' }}">
                    {{ $diasRestantes !== null && $diasRestantes <= 7 ? '¡Reposición urgente!' : 'Tiempo estimado de stock' }}
                </p>
            </div>

            {{-- Consumo Total --}}
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm relative overflow-hidden group">
                <div
                    class="absolute -right-4 -top-4 w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center opacity-50 group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Consumo 90 Días</p>
                <div class="flex items-baseline gap-2 mt-1">
                    <p class="text-3xl font-black text-slate-800 tracking-tight">{{ number_format($consumoTotal) }}</p>
                    <span class="text-xs font-semibold text-slate-400">uds.</span>
                </div>
                <p class="text-[10px] text-slate-400 font-medium mt-2">Total histórico registrado</p>
            </div>
        </div>

        {{-- ============ MODELOS USADOS ============ --}}
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <h3 class="font-extrabold text-slate-800 text-base flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                        Algoritmos de Predicción Aplicados
                    </h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Desglose matemático del cálculo diario</p>
                </div>
            </div>

            {{-- Grid de 4 modelos --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div
                    class="bg-slate-50/50 border border-slate-100 rounded-2xl p-4 border-l-4 border-l-indigo-400 hover:bg-slate-50 transition-colors">
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Promedio 7 días</p>
                    <div class="flex items-baseline gap-1 mt-1">
                        <p class="text-2xl font-black text-slate-800 tracking-tight">
                            {{ $prediccion['promedio_7_dias'] }}</p>
                        <span class="text-[10px] text-slate-400 font-medium">uds/día</span>
                    </div>
                </div>
                <div
                    class="bg-slate-50/50 border border-slate-100 rounded-2xl p-4 border-l-4 border-l-indigo-400 hover:bg-slate-50 transition-colors">
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Promedio 30 días</p>
                    <div class="flex items-baseline gap-1 mt-1">
                        <p class="text-2xl font-black text-slate-800 tracking-tight">
                            {{ $prediccion['promedio_30_dias'] }}</p>
                        <span class="text-[10px] text-slate-400 font-medium">uds/día</span>
                    </div>
                </div>
                <div
                    class="bg-slate-50/50 border border-slate-100 rounded-2xl p-4 border-l-4 border-l-indigo-500 hover:bg-slate-50 transition-colors">
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Mov. Ponderado</p>
                    <div class="flex items-baseline gap-1 mt-1">
                        <p class="text-2xl font-black text-slate-800 tracking-tight">
                            {{ $prediccion['movil_ponderado'] }}</p>
                        <span class="text-[10px] text-slate-400 font-medium">uds/día</span>
                    </div>
                </div>
                <div
                    class="bg-slate-50/50 border border-slate-100 rounded-2xl p-4 border-l-4 border-l-purple-500 hover:bg-slate-50 transition-colors">
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Suavizado Exponencial</p>
                    <div class="flex items-baseline gap-1 mt-1">
                        <p class="text-2xl font-black text-slate-800 tracking-tight">
                            {{ $prediccion['suavizado_exponencial'] }}</p>
                        <span class="text-[10px] text-slate-400 font-medium">uds/día</span>
                    </div>
                </div>
            </div>

            {{-- Regresión lineal (Destacada) --}}
            <div
                class="bg-gradient-to-br from-indigo-50/80 to-white border border-indigo-100 rounded-2xl p-5 space-y-4 shadow-sm">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center text-white shrink-0 shadow-md">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-slate-800 text-sm">Regresión Lineal Simple</h4>
                        <p class="text-[11px] text-slate-500">Ajuste de tendencia (Mínimos Cuadrados)</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <div class="bg-white border border-slate-100 rounded-xl p-3 shadow-sm">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Pendiente (m)</p>
                        <p class="text-xs font-mono font-bold text-slate-700 mt-1">
                            {{ $prediccion['regresion']['pendiente'] }}</p>
                    </div>
                    <div class="bg-white border border-slate-100 rounded-xl p-3 shadow-sm">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Intercepto (b)</p>
                        <p class="text-xs font-mono font-bold text-slate-700 mt-1">
                            {{ $prediccion['regresion']['intercepto'] }}</p>
                    </div>
                    <div class="bg-white border border-slate-100 rounded-xl p-3 shadow-sm relative overflow-hidden">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Bondad (R²)</p>
                        <p class="text-xs font-mono font-bold text-slate-700 mt-1">
                            {{ $prediccion['regresion']['r2'] }}</p>
                        {{-- Barra visual de R2 (opcional si es un valor entre 0 y 1) --}}
                        <div class="absolute bottom-0 left-0 h-1 bg-indigo-500"
                            style="width: {{ min(100, max(0, $prediccion['regresion']['r2'] * 100)) }}%"></div>
                    </div>
                    <div class="bg-indigo-600 border border-indigo-700 rounded-xl p-3 shadow-sm">
                        <p class="text-[10px] font-bold text-indigo-200 uppercase tracking-wider">Predicción Base</p>
                        <p class="text-xs font-mono font-bold text-white mt-1">
                            {{ $prediccion['prediccion_diaria_base'] }}/día</p>
                    </div>
                </div>

                <div class="bg-white/60 rounded-xl p-3 border border-indigo-100/50">
                    <p class="text-[11px] font-medium text-slate-600 italic leading-relaxed flex gap-2">
                        <svg class="w-4 h-4 text-indigo-400 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ $prediccion['regresion']['interpretacion'] }}
                    </p>
                </div>
            </div>
        </div>

        {{-- ============ GRÁFICAS ============ --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Gráfica 1: Historial --}}
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden flex flex-col">
                <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center">
                    <div>
                        <h3 class="font-extrabold text-slate-800 text-sm">Historial de Consumo</h3>
                        <p class="text-[11px] text-slate-400">Últimos 90 días</p>
                    </div>
                    <span class="px-2.5 py-1 bg-slate-100 text-slate-600 text-[10px] font-bold rounded-lg">Real</span>
                </div>
                <div class="p-5 flex-grow">
                    <div class="h-64">
                        <canvas id="chartHistorial"></canvas>
                    </div>
                </div>
            </div>

            {{-- Gráfica 2: Predicción --}}
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden flex flex-col">
                <div class="px-5 py-4 border-b border-slate-100 flex flex-wrap justify-between items-center gap-2">
                    <div>
                        <h3 class="font-extrabold text-slate-800 text-sm">Proyección a 30 Días</h3>
                        <p class="text-[11px] text-slate-400">
                            Total est.: <span
                                class="font-bold text-indigo-600">{{ $prediccion['total_predicho'] }}</span> uds.
                        </p>
                    </div>
                    <div class="flex gap-2 text-[10px] font-bold">
                        <span
                            class="inline-flex items-center gap-1.5 px-2 py-1 bg-indigo-50 text-indigo-700 rounded-md">
                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span> Diario
                        </span>
                        <span
                            class="inline-flex items-center gap-1.5 px-2 py-1 bg-emerald-50 text-emerald-700 rounded-md">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Acum.
                        </span>
                    </div>
                </div>
                <div class="p-5 flex-grow">
                    <div class="h-64">
                        <canvas id="chartPrediccion"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============ ACCIONES ============ --}}
        <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex flex-wrap gap-3">

            <a href="{{ route('prediccion.index') }}"
                class="px-5 py-3 bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-700 rounded-xl text-xs font-bold transition-all inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                </svg>
                Ir al Ranking
            </a>
        </div>
    </div>

    {{-- ============ SCRIPTS DE GRÁFICAS ============ --}}
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Configuración global para Chart.js (Colores modo claro/oscuro)
                Chart.defaults.font.family = "'Inter', sans-serif";
                Chart.defaults.color = '#64748b'; // slate-500

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
                                borderColor: 'rgb(99, 102, 241)', // indigo-500
                                backgroundColor: 'rgba(99, 102, 241, 0.08)',
                                borderWidth: 2,
                                tension: 0.4, // Curva más suave
                                fill: true,
                                pointRadius: 0,
                                pointHoverRadius: 6,
                                pointHoverBackgroundColor: 'rgb(99, 102, 241)',
                                pointHoverBorderColor: '#fff',
                                pointHoverBorderWidth: 2,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: {
                                intersect: false,
                                mode: 'index',
                            },
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                                    titleFont: {
                                        size: 12,
                                        weight: 'bold'
                                    },
                                    bodyFont: {
                                        size: 12
                                    },
                                    padding: 12,
                                    cornerRadius: 8,
                                    displayColors: false,
                                    callbacks: {
                                        label: ctx => `${ctx.parsed.y} unidades`
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        color: '#f1f5f9',
                                        drawBorder: false
                                    },
                                    ticks: {
                                        font: {
                                            size: 11
                                        }
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false
                                    },
                                    ticks: {
                                        maxTicksLimit: 8,
                                        font: {
                                            size: 11
                                        }
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
                                    label: 'Consumo diario',
                                    data: @json($chartPrediccion['data']),
                                    backgroundColor: 'rgba(99, 102, 241, 0.6)',
                                    hoverBackgroundColor: 'rgba(99, 102, 241, 0.9)',
                                    borderRadius: 4,
                                    barPercentage: 0.7,
                                    yAxisID: 'y',
                                },
                                {
                                    type: 'line',
                                    label: 'Acumulado',
                                    data: @json($chartPrediccion['acumulado']),
                                    borderColor: 'rgb(16, 185, 129)', // emerald-500
                                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                    borderWidth: 2,
                                    tension: 0.4,
                                    fill: false,
                                    pointRadius: 0,
                                    pointHoverRadius: 5,
                                    yAxisID: 'y1',
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: {
                                mode: 'index',
                                intersect: false
                            },
                            plugins: {
                                legend: {
                                    display: false
                                }, // Ocultamos leyenda nativa, usamos la custom del HTML
                                tooltip: {
                                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                                    padding: 12,
                                    cornerRadius: 8,
                                    callbacks: {
                                        label: function(context) {
                                            let label = context.dataset.label || '';
                                            if (label) {
                                                label += ': ';
                                            }
                                            if (context.parsed.y !== null) {
                                                label += context.parsed.y + (context.datasetIndex === 0 ?
                                                    ' uds.' : ' uds. acum.');
                                            }
                                            return label;
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    position: 'left',
                                    grid: {
                                        color: '#f1f5f9',
                                        drawBorder: false
                                    },
                                    title: {
                                        display: true,
                                        text: 'Diario',
                                        font: {
                                            size: 10,
                                            weight: 'bold'
                                        },
                                        color: '#94a3b8'
                                    },
                                    ticks: {
                                        font: {
                                            size: 10
                                        }
                                    }
                                },
                                y1: {
                                    beginAtZero: true,
                                    position: 'right',
                                    title: {
                                        display: true,
                                        text: 'Acumulado',
                                        font: {
                                            size: 10,
                                            weight: 'bold'
                                        },
                                        color: '#94a3b8'
                                    },
                                    grid: {
                                        drawOnChartArea: false,
                                        drawBorder: false
                                    },
                                    ticks: {
                                        font: {
                                            size: 10
                                        }
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false
                                    },
                                    ticks: {
                                        maxTicksLimit: 10,
                                        font: {
                                            size: 10
                                        }
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
