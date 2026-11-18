<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Predicción de Costos"
            subtitle="Sprint de Analítica Avanzada · Regresión Lineal y Suavizado Exponencial">
            <x-slot:actions>
                <a href="{{ route('admin.dashboard') }}"
                    class="text-sm font-semibold text-brand-600 hover:text-brand-800">
                    &larr; Volver al Dashboard
                </a>
            </x-slot:actions>
        </x-page-header>
    </x-slot>

    <div class="py-7">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Gráfica Principal -->
            <x-card>
                <h3 class="text-lg font-semibold text-slate-900 mb-6">Proyección de Costos (Próximos 3 meses)</h3>
                <div class="w-full h-96">
                    <canvas id="graficaCostos"></canvas>
                </div>
            </x-card>

            <!-- Tarjetas de Resultados -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white rounded-2xl p-6 ring-1 ring-slate-200/70 shadow-card">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="grid h-10 w-10 place-items-center rounded-xl bg-blue-50 text-blue-600">
                            <x-heroicon-o-chart-bar class="h-5 w-5" />
                        </span>
                        <h4 class="font-semibold text-slate-900">Regresión Lineal (Tendencia)</h4>
                    </div>
                    <p class="text-3xl font-bold text-slate-900 tabular">
                        ${{ number_format($prediccionLineal[0] ?? 0, 2) }}</p>
                    <p class="text-sm text-slate-500 mt-1">Predicción para el próximo mes</p>
                </div>

                <div class="bg-white rounded-2xl p-6 ring-1 ring-slate-200/70 shadow-card">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="grid h-10 w-10 place-items-center rounded-xl bg-emerald-50 text-emerald-600">
                            <x-heroicon-o-arrow-trending-up class="h-5 w-5" />
                        </span>
                        <h4 class="font-semibold text-slate-900">Suavizado Exponencial</h4>
                    </div>
                    <p class="text-3xl font-bold text-slate-900 tabular">
                        ${{ number_format($prediccionSuavizada[0] ?? 0, 2) }}</p>
                    <p class="text-sm text-slate-500 mt-1">Predicción para el próximo mes</p>
                </div>
            </div>

        </div>
    </div>

    <!-- Script de Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('graficaCostos').getContext('2d');

            const historico = @json($valores);
            const etiquetasHistoricas = @json($etiquetas);
            const predLineal = @json($prediccionLineal);
            const predSuavizada = @json($prediccionSuavizada);

            const etiquetasFuturas = ['Mes +1', 'Mes +2', 'Mes +3'];
            const todasLasEtiquetas = [...etiquetasHistoricas, ...etiquetasFuturas];
            const rellenoNulos = Array(historico.length - 1).fill(null);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: todasLasEtiquetas,
                    datasets: [{
                            label: 'Costo Histórico',
                            data: historico,
                            borderColor: '#0f172a', // slate-900
                            backgroundColor: 'rgba(15, 23, 42, 0.1)',
                            borderWidth: 3,
                            tension: 0.3,
                            fill: true
                        },
                        {
                            label: 'Predicción (Regresión Lineal)',
                            data: [...rellenoNulos, historico[historico.length - 1], ...predLineal],
                            borderColor: '#2563eb', // blue-600
                            borderDash: [5, 5],
                            borderWidth: 2,
                            tension: 0
                        },
                        {
                            label: 'Predicción (Suavizado)',
                            data: [...rellenoNulos, historico[historico.length - 1], ...predSuavizada],
                            borderColor: '#059669', // emerald-600
                            borderDash: [5, 5],
                            borderWidth: 2,
                            tension: 0
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
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) label += ': ';
                                    if (context.parsed.y !== null) {
                                        label += new Intl.NumberFormat('es-MX', {
                                            style: 'currency',
                                            currency: 'MXN'
                                        }).format(context.parsed.y);
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>
