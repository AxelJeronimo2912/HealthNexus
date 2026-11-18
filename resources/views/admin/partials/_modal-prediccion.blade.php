<div x-show="modalAbierto === 'prediccion'" x-cloak
    class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4"
    style="display: none;" @click.self="modalAbierto = null" @keydown.escape.window="modalAbierto = null"
    x-data="prediccionCostosModal()">

    <div class="bg-white rounded-2xl shadow-2xl max-w-5xl w-full max-h-[90vh] overflow-y-auto"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100">

        {{-- ENCABEZADO --}}
        <div
            class="bg-gradient-to-r from-emerald-600 to-teal-800 text-white p-6 rounded-t-3xl sticky top-0 z-10 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center">
                    <x-heroicon-o-chart-bar class="w-6 h-6" />
                </div>
                <div>
                    <h3 class="text-xl font-bold">Planificación Presupuestal</h3>
                    <p class="text-xs text-emerald-100">Estimación de costos a partir de historial real</p>
                </div>
            </div>

            <button @click="modalAbierto = null" class="text-white/80 hover:text-white">
                <x-heroicon-o-x-mark class="w-6 h-6" />
            </button>
        </div>

        {{-- CONTENIDO --}}
        <div class="p-6 space-y-6">

            {{-- SELECTOR DE TIPO --}}
            <div
                class="bg-slate-50 border border-slate-200 rounded-2xl p-4 flex flex-col md:flex-row items-center justify-between gap-4">
                <div>
                    <h4 class="text-sm font-bold text-slate-700">¿Qué deseas predecir?</h4>
                    <p class="text-xs text-slate-500">Selecciona el origen de los datos históricos</p>
                </div>
                <select x-model="tipoSeleccionado" @change="cargarDatos()"
                    class="w-full md:w-64 px-4 py-2 border border-slate-300 rounded-xl text-sm font-semibold focus:ring-2 focus:ring-emerald-500">
                    <option value="atencion">Costos de Atención (Servicios)</option>
                    <option value="insumos">Costos de Insumos (Compras)</option>
                </select>
            </div>

            {{-- GRÁFICA --}}
            <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm relative" x-show="!cargando">
                <h4 class="text-sm font-bold text-slate-700 mb-4 uppercase tracking-wider">
                    Proyección de Costos (Próximos 3 meses)
                </h4>
                <div class="h-72 w-full relative">
                    <canvas id="graficaCostosModal"></canvas>
                </div>
            </div>

            {{-- ESTADO DE CARGA --}}
            <div x-show="cargando" class="h-72 flex items-center justify-center text-slate-500 font-semibold">
                Consultando base de datos...
            </div>

            {{-- RESULTADOS NUMÉRICOS --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5">
                    <p class="text-xs font-bold text-blue-600 uppercase">Regresión Lineal</p>
                    <p class="text-3xl font-extrabold text-slate-800 mt-1">
                        $<span x-text="formatearMoneda(resultados.prediccionLineal?.[0])"></span>
                    </p>
                    <p class="text-xs text-slate-500 mt-1">Tendencia para el próximo mes</p>
                </div>

                <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-5">
                    <p class="text-xs font-bold text-emerald-600 uppercase">Suavizado Exponencial</p>
                    <p class="text-3xl font-extrabold text-slate-800 mt-1">
                        $<span x-text="formatearMoneda(resultados.prediccionSuavizada?.[0])"></span>
                    </p>
                    <p class="text-xs text-slate-500 mt-1">Reacción a gastos recientes</p>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- SCRIPT ALPINE Y CHART.JS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function prediccionCostosModal() {
        return {
            tipoSeleccionado: 'atencion',
            cargando: false, // Iniciamos en falso
            resultados: {},
            chartInstance: null,

            init() {
                // Observamos cuando se abre el modal para disparar la petición
                this.$watch('modalAbierto', (valor) => {
                    if (valor === 'prediccion') {
                        this.cargarDatos();
                    }
                });
            },

            formatearMoneda(valor) {
                if (!valor) return '0.00';
                return new Intl.NumberFormat('es-MX', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(valor);
            },

            async cargarDatos() {
                this.cargando = true;
                try {
                    const res = await fetch(
                        `{{ route('admin.prediccion-costos') }}?tipo=${this.tipoSeleccionado}`, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });

                    if (!res.ok) {
                        throw new Error(`Error del servidor: ${res.status}`);
                    }

                    const data = await res.json();
                    this.resultados = data;

                    // Esperamos 350ms para asegurarnos de que el modal ya se mostró en pantalla
                    setTimeout(() => {
                        this.renderChart(data);
                    }, 350);

                } catch (error) {
                    console.error("Error cargando predicciones", error);
                    alert("Error al consultar la base de datos. Revisa la consola (F12).");
                } finally {
                    this.cargando = false;
                }
            },

            renderChart(data) {
                const canvas = document.getElementById('graficaCostosModal');
                if (!canvas) return;

                if (this.chartInstance) {
                    this.chartInstance.destroy();
                }

                const ctx = canvas.getContext('2d');
                const historico = data.valores;
                const etiquetasHistoricas = data.etiquetas;
                const predLineal = data.prediccionLineal;
                const predSuavizada = data.prediccionSuavizada;

                const etiquetasFuturas = ['Mes +1', 'Mes +2', 'Mes +3'];
                const todasLasEtiquetas = [...etiquetasHistoricas, ...etiquetasFuturas];
                const rellenoNulos = Array(historico.length - 1).fill(null);

                this.chartInstance = new Chart(ctx, {
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
                            },
                            x: {
                                ticks: {
                                    maxRotation: 45,
                                    minRotation: 45
                                }
                            }
                        }
                    }
                });
            }
        }
    }
</script>
