{{-- ============================================================ --}}
{{-- MODAL: PULSO OPERATIVO SLA --}}
{{-- ============================================================ --}}
<div x-show="modalAbierto === 'sla'" x-cloak
    class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4"
    style="display: none;" @click.self="modalAbierto = null" @keydown.escape.window="modalAbierto = null">

    <div class="bg-white rounded-3xl shadow-2xl max-w-5xl w-full max-h-[90vh] overflow-y-auto"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100">

        {{-- ENCABEZADO --}}
        <div class="bg-gradient-to-r from-orange-500 to-orange-700 text-white p-6 rounded-t-3xl sticky top-0 z-10">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center">
                        <x-heroicon-o-fire class="w-6 h-6" />
                    </div>
                    <div>
                        <h3 class="text-xl font-bold">Pulso Operativo SLA</h3>
                        <p class="text-xs text-orange-100">
                            Detección de anomalías en tiempo real (Desviación > 2.5σ)
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span
                        class="hidden md:inline-flex items-center gap-1.5 px-3 py-1 bg-white/20 rounded-full text-xs font-bold">
                        <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
                        EN VIVO
                    </span>
                    <button @click="modalAbierto = null" class="text-white/80 hover:text-white">
                        <x-heroicon-o-x-mark class="w-6 h-6" />
                    </button>
                </div>
            </div>
        </div>

        {{-- CONTENIDO --}}
        <div class="p-6 space-y-6" x-data="slaModal()" x-init="cargar()">

            {{-- LOADING --}}
            <div x-show="cargando" class="text-center py-12">
                <div
                    class="inline-block animate-spin rounded-full h-10 w-10 border-4 border-orange-500 border-t-transparent">
                </div>
                <p class="text-sm text-slate-500 mt-3">Analizando anomalías...</p>
            </div>

            <div x-show="!cargando" class="space-y-6">

                {{-- RESUMEN POR MÓDULO --}}
                <div>
                    <h4 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-3">
                        Módulos monitoreados
                    </h4>
                    <template x-if="datos && datos.resumen">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <template x-for="(info, key) in datos.resumen" :key="key">
                                <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                                    <div class="flex items-center justify-between mb-2">
                                        <p class="text-sm font-bold text-slate-800" x-text="info.label"></p>
                                        <span class="text-xs text-slate-400" x-text="info.n + ' eventos'"></span>
                                    </div>
                                    <div class="space-y-1">
                                        <div class="flex items-baseline justify-between">
                                            <span class="text-xs text-slate-500">Promedio</span>
                                            <span class="text-lg font-bold text-slate-800">
                                                <span x-text="info.media"></span> min
                                            </span>
                                        </div>
                                        <div class="flex items-baseline justify-between">
                                            <span class="text-xs text-slate-500">Límite (μ+2.5σ)</span>
                                            <span class="text-sm font-bold text-orange-600">
                                                <span x-text="info.limite"></span> min
                                            </span>
                                        </div>
                                        <div class="flex items-baseline justify-between pt-1 border-t border-slate-200">
                                            <span class="text-xs text-slate-500">Outliers</span>
                                            <span class="px-2 py-0.5 rounded-full text-xs font-bold"
                                                :class="info.outliers > 0 ? 'bg-red-100 text-red-700' :
                                                    'bg-green-100 text-green-700'">
                                                <span x-text="info.outliers"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                {{-- COMPARATIVA DE DURACIÓN --}}
                <div class="bg-white border border-slate-100 rounded-2xl p-4">
                    <h4 class="text-sm font-bold text-slate-700 mb-3">
                        ¿Qué área del hospital es más lenta?
                    </h4>
                    <p class="text-xs text-slate-500 mb-4">
                        Comparación del promedio de duración por módulo
                    </p>
                    <div class="h-64">
                        <canvas id="chartSlaComparativa"></canvas>
                    </div>
                </div>

                {{-- DISTRIBUCIÓN HORARIA DE OUTLIERS --}}
                <div class="bg-white border border-slate-100 rounded-2xl p-4">
                    <h4 class="text-sm font-bold text-slate-700 mb-3">
                        Distribución horaria de anomalías
                    </h4>
                    <p class="text-xs text-slate-500 mb-4">
                        ¿A qué hora del día ocurren más outliers?
                    </p>
                    <div class="h-56">
                        <canvas id="chartSlaHoraria"></canvas>
                    </div>
                </div>

                {{-- TABLA DE OUTLIERS --}}
                <div>
                    <h4 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-3">
                        Últimos eventos atípicos (outliers)
                    </h4>

                    <template x-if="datos && datos.outliers && datos.outliers.length > 0">
                        <div class="overflow-x-auto bg-white border border-slate-100 rounded-2xl">
                            <table class="min-w-full divide-y divide-slate-100">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-bold text-slate-500 uppercase">
                                            Fecha/Hora</th>
                                        <th class="px-4 py-2 text-left text-xs font-bold text-slate-500 uppercase">
                                            Módulo</th>
                                        <th class="px-4 py-2 text-right text-xs font-bold text-slate-500 uppercase">
                                            Duración</th>
                                        <th class="px-4 py-2 text-right text-xs font-bold text-slate-500 uppercase">
                                            Z-score</th>
                                        <th class="px-4 py-2 text-center text-xs font-bold text-slate-500 uppercase">
                                            Severidad</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <template x-for="outlier in datos.outliers" :key="outlier.id">
                                        <tr>
                                            <td class="px-4 py-2 text-sm text-slate-700" x-text="outlier.fecha"></td>
                                            <td class="px-4 py-2">
                                                <span class="px-2 py-0.5 rounded text-xs font-bold"
                                                    :class="outlier.modulo_color" x-text="outlier.modulo"></span>
                                            </td>
                                            <td class="px-4 py-2 text-sm text-right font-semibold text-slate-800">
                                                <span x-text="outlier.duracion"></span> min
                                            </td>
                                            <td class="px-4 py-2 text-right">
                                                <span
                                                    class="px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700"
                                                    x-text="outlier.z_score + 'σ'"></span>
                                            </td>
                                            <td class="px-4 py-2 text-center">
                                                <span class="px-2 py-0.5 rounded-full text-xs font-bold"
                                                    :class="{
                                                        'bg-yellow-100 text-yellow-800': outlier
                                                            .severidad === 'jefe',
                                                        'bg-orange-100 text-orange-800': outlier
                                                            .severidad === 'director',
                                                        'bg-red-100 text-red-800': outlier.severidad === 'critico'
                                                    }"
                                                    x-text="outlier.severidad"></span>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </template>

                    <template x-if="datos && (!datos.outliers || datos.outliers.length === 0)">
                        <div class="text-center py-8 bg-green-50 rounded-2xl border border-green-100">
                            <x-heroicon-o-check-circle class="w-12 h-12 text-green-500 mx-auto mb-2" />
                            <p class="text-sm font-bold text-green-700">Sin anomalías detectadas</p>
                            <p class="text-xs text-green-600 mt-1">
                                Todos los módulos operan dentro de su rango normal
                            </p>
                        </div>
                    </template>
                </div>

                {{-- PROTOCOLO DE ESCALAMIENTO --}}
                <div class="bg-slate-50 border border-slate-100 rounded-2xl p-4">
                    <h4 class="text-sm font-bold text-slate-700 mb-3">Protocolo de escalamiento</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div class="bg-white rounded-xl p-3 border border-yellow-200">
                            <p class="text-lg font-bold text-yellow-600">Z &gt; 2.5σ</p>
                            <p class="text-xs text-slate-500 mt-1">Notificación al jefe del módulo</p>
                        </div>
                        <div class="bg-white rounded-xl p-3 border border-orange-200">
                            <p class="text-lg font-bold text-orange-600">Z &gt; 3.5σ</p>
                            <p class="text-xs text-slate-500 mt-1">Notificación al director</p>
                        </div>
                        <div class="bg-white rounded-xl p-3 border border-red-200">
                            <p class="text-lg font-bold text-red-600">Z &gt; 4.5σ</p>
                            <p class="text-xs text-slate-500 mt-1">Alerta al comité de calidad</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- SCRIPT ALPINE PARA EL MODAL SLA --}}
<script>
    function slaModal() {
        return {
            cargando: true,
            datos: null,
            charts: {},

            async cargar() {
                this.cargando = true;
                try {
                    const res = await fetch('{{ route('admin.sla-data') }}', {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    this.datos = await res.json();
                    this.$nextTick(() => this.renderCharts());
                } catch (e) {
                    console.error('Error cargando SLA:', e);
                } finally {
                    this.cargando = false;
                }
            },

            renderCharts() {
                if (!this.datos) return;

                // Destruir charts previos
                Object.values(this.charts).forEach(c => c && c.destroy());
                this.charts = {};

                // --- Gráfica 1: Comparativa por módulo ---
                const ctxComp = document.getElementById('chartSlaComparativa');
                if (ctxComp) {
                    const resumen = this.datos.resumen;
                    const labels = Object.values(resumen).map(r => r.label);
                    const medias = Object.values(resumen).map(r => r.media);

                    this.charts.comparativa = new Chart(ctxComp, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Duración promedio (min)',
                                data: medias,
                                backgroundColor: ['#f59e0b', '#3b82f6', '#10b981', '#8b5cf6'],
                                borderRadius: 8,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            indexAxis: 'y',
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                x: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Promedio de duración (min)'
                                    }
                                }
                            }
                        }
                    });
                }

                // --- Gráfica 2: Distribución horaria de outliers ---
                const ctxHora = document.getElementById('chartSlaHoraria');
                if (ctxHora) {
                    const distribucion = this.datos.distribucion_horaria;
                    const horas = Object.keys(distribucion).map(h => h + ':00');
                    const totales = Object.values(distribucion);

                    this.charts.horaria = new Chart(ctxHora, {
                        type: 'bar',
                        data: {
                            labels: horas,
                            datasets: [{
                                label: 'Outliers detectados',
                                data: totales,
                                backgroundColor: 'rgba(239, 68, 68, 0.7)',
                                borderColor: 'rgb(239, 68, 68)',
                                borderWidth: 1,
                                borderRadius: 4,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1
                                    },
                                    title: {
                                        display: true,
                                        text: 'Outliers'
                                    }
                                },
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Hora del día'
                                    }
                                }
                            }
                        }
                    });
                }
            }
        }
    }
</script>
