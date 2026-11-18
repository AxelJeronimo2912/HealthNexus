{{-- ============================================================ --}}
{{-- MODAL: PREDICCIÓN DE COSTOS --}}
{{-- ============================================================ --}}
<div x-show="modalAbierto === 'prediccion'" x-cloak
    class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4"
    style="display: none;" @click.self="modalAbierto = null" @keydown.escape.window="modalAbierto = null">

    <div class="bg-white rounded-2xl shadow-2xl max-w-5xl w-full max-h-[90vh] overflow-y-auto"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100">

        {{-- ENCABEZADO --}}
        <div class="bg-gradient-to-r from-emerald-600 to-teal-800 text-white p-6 rounded-t-3xl sticky top-0 z-10">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center">
                        <x-heroicon-o-chart-bar class="w-6 h-6" />
                    </div>
                    <div>
                        <h3 class="text-xl font-bold">Predicción de Costos</h3>
                        <p class="text-xs text-emerald-100">
                            Regresión Lineal y Suavizado Exponencial · Analítica de gastos
                        </p>
                    </div>
                </div>
                <button @click="modalAbierto = null" class="text-white/80 hover:text-white">
                    <x-heroicon-o-x-mark class="w-6 h-6" />
                </button>
            </div>
        </div>

        {{-- CONTENIDO --}}
        <div class="p-6 space-y-6" x-data="prediccionModal()">

            {{-- GRÁFICA --}}
            <div class="bg-slate-50 border border-slate-100 rounded-2xl p-5">
                <h4 class="text-sm font-bold text-slate-700 mb-4 uppercase tracking-wider">
                    Proyección de Costos (Próximos 3 meses)
                </h4>
                <div class="h-72 w-full">
                    <canvas id="graficaCostosModal"></canvas>
                </div>
            </div>

            {{-- RESULTADOS NUMÉRICOS --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Regresión Lineal --}}
                <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5">
                    <p class="text-xs font-bold text-blue-600 uppercase">Regresión Lineal</p>
                    <p class="text-3xl font-extrabold text-slate-800 mt-1">
                        ${{ number_format($datosPrediccion['prediccionLineal'][0] ?? 0, 2) }}
                    </p>
                    <p class="text-xs text-slate-500 mt-1">Proyección para el próximo mes</p>
                    <p class="text-xs text-slate-400 mt-2 font-mono">Tendencia matemática a largo plazo</p>
                </div>

                {{-- Suavizado Exponencial --}}
                <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-5">
                    <p class="text-xs font-bold text-emerald-600 uppercase">Suavizado Exponencial</p>
                    <p class="text-3xl font-extrabold text-slate-800 mt-1">
                        ${{ number_format($datosPrediccion['prediccionSuavizada'][0] ?? 0, 2) }}
                    </p>
                    <p class="text-xs text-slate-500 mt-1">Proyección para el próximo mes</p>
                    <p class="text-xs text-slate-400 mt-2 font-mono">Mayor peso a los gastos recientes</p>
                </div>
            </div>

            {{-- INTERPRETACIÓN --}}
            <div class="bg-slate-800 text-white rounded-2xl p-5">
                <p class="text-xs font-bold uppercase opacity-80 mb-2">Veredicto analítico</p>
                <p class="text-sm">
                    Ambas proyecciones sugieren una tendencia de costos. La <strong>Regresión Lineal</strong> es útil
                    para presupuestos anuales, mientras que el <strong>Suavizado Exponencial</strong> reacciona más
                    rápido a picos de gastos recientes.
                </p>
            </div>

        </div>
    </div>
</div>

{{-- SCRIPT ALPINE Y CHART.JS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function prediccionModal() {
        return {
            init() {
                this.$nextTick(() => {
                    this.renderChart();
                });
            },
            renderChart() {
                const ctx = document.getElementById('graficaCostosModal');
                if (!ctx) return;

                // Destruir si ya existe para evitar duplicados
                if (window.graficaPrediccion) window.graficaPrediccion.destroy();

                const historico = @json($datosPrediccion['valores']);
                const etiquetasHistoricas = @json($datosPrediccion['etiquetas']);
                const predLineal = @json($datosPrediccion['prediccionLineal']);
                const predSuavizada = @json($datosPrediccion['prediccionSuavizada']);

                const etiquetasFuturas = ['Mes +1', 'Mes +2', 'Mes +3'];
                const todasLasEtiquetas = [...etiquetasHistoricas, ...etiquetasFuturas];
                const rellenoNulos = Array(historico.length - 1).fill(null);

                window.graficaPrediccion = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: todasLasEtiquetas,
                        datasets: [{
                                label: 'Costo Histórico',
                                data: historico,
                                borderColor: '#0f172a',
                                backgroundColor: 'rgba(15, 23, 42, 0.1)',
                                borderWidth: 3,
                                tension: 0.3,
                                fill: true
                            },
                            {
                                label: 'Regresión Lineal',
                                data: [...rellenoNulos, historico[historico.length - 1], ...predLineal],
                                borderColor: '#2563eb',
                                borderDash: [5, 5],
                                borderWidth: 2,
                                tension: 0
                            },
                            {
                                label: 'Suavizado Exponencial',
                                data: [...rellenoNulos, historico[historico.length - 1], ...predSuavizada],
                                borderColor: '#059669',
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
            }
        }
    }
</script>
