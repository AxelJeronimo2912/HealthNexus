<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">
                Predicción — {{ $medicamento->nombre }} {{ $medicamento->concentracion }}
            </h2>
            <a href="{{ route('prediccion.index') }}" class="text-sm text-gray-600 hover:underline">
                ← Volver
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- Resumen --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-xs text-gray-500 uppercase">Stock actual</p>
                <p
                    class="text-2xl font-bold {{ $stockActual <= $medicamento->stock_minimo ? 'text-red-600' : 'text-green-600' }}">
                    {{ $stockActual }}
                </p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-xs text-gray-500 uppercase">Predicción diaria</p>
                <p class="text-2xl font-bold text-indigo-600">{{ $consumoPromedio }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-xs text-gray-500 uppercase">Días restantes</p>
                <p
                    class="text-2xl font-bold {{ $diasRestantes !== null && $diasRestantes <= 7 ? 'text-red-600' : 'text-gray-800' }}">
                    {{ $diasRestantes ?? '—' }}
                </p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-xs text-gray-500 uppercase">Consumo total 90d</p>
                <p class="text-2xl font-bold text-gray-800">{{ $consumoTotal }}</p>
            </div>
        </div>

        {{-- MODELOS USADOS --}}
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="font-bold text-gray-800 mb-4">Modelos de predicción usados</h3>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div class="border-l-4 border-blue-500 pl-3">
                    <p class="text-xs text-gray-500 uppercase">Promedio 7 días</p>
                    <p class="text-xl font-bold">{{ $prediccion['promedio_7_dias'] }}</p>
                </div>
                <div class="border-l-4 border-blue-500 pl-3">
                    <p class="text-xs text-gray-500 uppercase">Promedio 30 días</p>
                    <p class="text-xl font-bold">{{ $prediccion['promedio_30_dias'] }}</p>
                </div>
                <div class="border-l-4 border-indigo-500 pl-3">
                    <p class="text-xs text-gray-500 uppercase">Promedio móvil ponderado</p>
                    <p class="text-xl font-bold">{{ $prediccion['movil_ponderado'] }}</p>
                </div>
                <div class="border-l-4 border-purple-500 pl-3">
                    <p class="text-xs text-gray-500 uppercase">Suavizado exponencial</p>
                    <p class="text-xl font-bold">{{ $prediccion['suavizado_exponencial'] }}</p>
                </div>
            </div>

            {{-- Regresión lineal --}}
            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                <h4 class="font-semibold text-sm text-gray-700 mb-2">Regresión lineal</h4>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                    <div>
                        <p class="text-xs text-gray-500">Pendiente</p>
                        <p class="font-mono font-bold">{{ $prediccion['regresion']['pendiente'] }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Intercepto</p>
                        <p class="font-mono font-bold">{{ $prediccion['regresion']['intercepto'] }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">R² (bondad de ajuste)</p>
                        <p class="font-mono font-bold">{{ $prediccion['regresion']['r2'] }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Predicción base</p>
                        <p class="font-mono font-bold text-indigo-600">
                            {{ $prediccion['prediccion_diaria_base'] }}/día
                        </p>
                    </div>
                </div>
                <p class="text-xs text-gray-600 mt-3 italic">
                    {{ $prediccion['regresion']['interpretacion'] }}
                </p>
            </div>
        </div>

        {{-- GRÁFICA 1: Historial de consumo --}}
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="font-bold text-gray-800 mb-4">Historial de consumo (últimos 90 días)</h3>
            <div class="h-64">
                <canvas id="chartHistorial"></canvas>
            </div>
        </div>

        {{-- GRÁFICA 2: Predicción próximos 30 días --}}
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h3 class="font-bold text-gray-800">Predicción — Próximos 30 días</h3>
                    <p class="text-xs text-gray-500">
                        Total estimado: <strong>{{ $prediccion['total_predicho'] }}</strong> unidades
                    </p>
                </div>
                <div class="flex gap-4 text-xs">
                    <span class="flex items-center gap-1">
                        <span class="w-3 h-3 rounded-full bg-indigo-500"></span>
                        Consumo diario predicho
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                        Acumulado
                    </span>
                </div>
            </div>
            <div class="h-72">
                <canvas id="chartPrediccion"></canvas>
            </div>
        </div>

        <div class="pt-4 flex space-x-2">
            <a href="{{ route('existencias.show', $medicamento) }}"
                class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm">Ver existencias</a>
            <a href="{{ route('prediccion.index') }}" class="px-4 py-2 bg-gray-100 rounded-md text-sm">Volver</a>
        </div>
    </div>

    {{-- SCRIPTS DE GRÁFICAS --}}
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
