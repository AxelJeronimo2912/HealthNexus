{{-- ============================================================ --}}
{{-- MODAL: DATA WAREHOUSE / BIG DATA --}}
{{-- ============================================================ --}}
<div x-show="modalAbierto === 'dwh'" x-cloak
    class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4"
    style="display: none;" @click.self="modalAbierto = null" @keydown.escape.window="modalAbierto = null">

    <div class="bg-white rounded-3xl shadow-2xl max-w-6xl w-full max-h-[90vh] overflow-y-auto"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100">

        {{-- ENCABEZADO --}}
        <div class="bg-gradient-to-r from-violet-700 to-indigo-800 text-white p-6 rounded-t-3xl sticky top-0 z-10">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center">
                        <x-heroicon-o-circle-stack class="w-6 h-6" />
                    </div>
                    <div>
                        <h3 class="text-xl font-bold">Data Warehouse — Big Data</h3>
                        <p class="text-xs text-indigo-100">
                            Análisis clínico y flujo de pacientes en tiempo real
                        </p>
                    </div>
                </div>
                <button @click="modalAbierto = null" class="text-white/80 hover:text-white">
                    <x-heroicon-o-x-mark class="w-6 h-6" />
                </button>
            </div>
        </div>

        {{-- CONTENIDO --}}
        <div class="p-6 space-y-6" x-data="dwhModal()" x-init="cargar()">

            {{-- LOADING --}}
            <div x-show="cargando" class="text-center py-12">
                <div
                    class="inline-block animate-spin rounded-full h-10 w-10 border-4 border-indigo-500 border-t-transparent">
                </div>
                <p class="text-sm text-slate-500 mt-3">Agregando métricas del DWH...</p>
            </div>

            {{-- ERROR --}}
            <template x-if="!cargando && error">
                <div class="text-center py-12 bg-amber-50 rounded-2xl border border-amber-100">
                    <x-heroicon-o-exclamation-triangle class="w-12 h-12 text-amber-500 mx-auto mb-2" />
                    <p class="text-sm font-bold text-amber-700" x-text="error"></p>
                </div>
            </template>

            <div x-show="!cargando && !error" class="space-y-6">

                {{-- KPIs SUPERIORES --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-indigo-50 rounded-2xl p-4 border border-indigo-100">
                        <p class="text-xs text-indigo-600 font-bold uppercase">Documentos</p>
                        <p class="text-3xl font-extrabold text-indigo-800" x-text="datos?.kpis?.total_registros"></p>
                        <p class="text-[10px] text-indigo-500">Registros procesados</p>
                    </div>
                    <div class="bg-violet-50 rounded-2xl p-4 border border-violet-100">
                        <p class="text-xs text-violet-600 font-bold uppercase">Colecciones</p>
                        <p class="text-3xl font-extrabold text-violet-800" x-text="datos?.kpis?.colecciones"></p>
                        <p class="text-[10px] text-violet-500">Activas en el DWH</p>
                    </div>
                    <div class="bg-emerald-50 rounded-2xl p-4 border border-emerald-100">
                        <p class="text-xs text-emerald-600 font-bold uppercase">Calidad</p>
                        <p class="text-3xl font-extrabold text-emerald-800">
                            <span x-text="datos?.kpis?.calidad"></span>%
                        </p>
                        <p class="text-[10px] text-emerald-500">Con FC registrada</p>
                    </div>
                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                        <p class="text-xs text-slate-600 font-bold uppercase">Período</p>
                        <p class="text-sm font-extrabold text-slate-800 mt-1"
                            x-text="(datos?.kpis?.rango_fechas?.desde || '—') + ' → ' + (datos?.kpis?.rango_fechas?.hasta || '—')">
                        </p>
                        <p class="text-[10px] text-slate-500">Rango de datos</p>
                    </div>
                </div>

                {{-- DISTRIBUCIÓN POR TRIAJE --}}
                <div class="bg-white border border-slate-100 rounded-2xl p-4">
                    <h4 class="text-sm font-bold text-slate-700 mb-3">Distribución por nivel de triaje</h4>
                    <div class="h-56">
                        <canvas id="chartDwhTriage"></canvas>
                    </div>
                </div>

                {{-- ACTIVIDAD POR HORA --}}
                <div class="bg-white border border-slate-100 rounded-2xl p-4">
                    <h4 class="text-sm font-bold text-slate-700 mb-3">Actividad por hora (flujo de pacientes)</h4>
                    <p class="text-xs text-slate-500 mb-3">
                        Pico: <span class="font-bold text-indigo-600" x-text="picoHora()"></span>
                    </p>
                    <div class="h-56">
                        <canvas id="chartDwhHora"></canvas>
                    </div>
                </div>

                {{-- ESTADÍSTICAS FC + PERCENTILES --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-white border border-slate-100 rounded-2xl p-4">
                        <h4 class="text-sm font-bold text-slate-700 mb-3">Estadísticas de Frecuencia Cardíaca</h4>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-slate-50 rounded-xl p-3">
                                <p class="text-xs text-slate-500">Media</p>
                                <p class="text-lg font-extrabold text-slate-800">
                                    <span x-text="datos?.fc_stats?.media"></span> bpm
                                </p>
                            </div>
                            <div class="bg-slate-50 rounded-xl p-3">
                                <p class="text-xs text-slate-500">Desv. estándar</p>
                                <p class="text-lg font-extrabold text-slate-800" x-text="datos?.fc_stats?.desviacion">
                                </p>
                            </div>
                            <div class="bg-red-50 rounded-xl p-3">
                                <p class="text-xs text-red-500">Máxima</p>
                                <p class="text-lg font-extrabold text-red-700">
                                    <span x-text="datos?.fc_stats?.maximo"></span> bpm
                                </p>
                            </div>
                            <div class="bg-blue-50 rounded-xl p-3">
                                <p class="text-xs text-blue-500">Mínima</p>
                                <p class="text-lg font-extrabold text-blue-700">
                                    <span x-text="datos?.fc_stats?.minimo"></span> bpm
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-slate-100 rounded-2xl p-4">
                        <h4 class="text-sm font-bold text-slate-700 mb-3">Percentiles y cuartiles</h4>
                        <table class="min-w-full text-sm">
                            <tbody>
                                <template
                                    x-for="(label, key) in {p10:'10%',p25:'25%',p50:'50% (Mediana)',p75:'75%',p90:'90%',iqr:'IQR'}"
                                    :key="key">
                                    <tr class="border-b border-slate-100">
                                        <td class="py-1.5 text-slate-500" x-text="label"></td>
                                        <td class="py-1.5 text-right font-bold text-slate-800">
                                            <span x-text="datos?.percentiles?.[key]"></span> bpm
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- CORRELACIONES --}}
                <div>
                    <h4 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-3">Correlaciones clínicas
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-white border border-slate-100 rounded-2xl p-4">
                            <p class="text-xs font-bold text-slate-500 uppercase">Hora vs FC</p>
                            <p class="text-2xl font-extrabold mt-2"
                                :class="(datos?.correlaciones?.hora_fc || 0) < 0 ? 'text-red-600' : 'text-emerald-600'"
                                x-text="fmt(datos?.correlaciones?.hora_fc)"></p>
                            <p class="text-xs text-slate-500 mt-1">Relación negativa moderada</p>
                        </div>
                        <div class="bg-white border border-slate-100 rounded-2xl p-4">
                            <p class="text-xs font-bold text-slate-500 uppercase">Triage vs FC</p>
                            <p class="text-2xl font-extrabold mt-2"
                                :class="(datos?.correlaciones?.triage_fc || 0) > 0 ? 'text-red-600' : 'text-blue-600'"
                                x-text="fmt(datos?.correlaciones?.triage_fc)"></p>
                            <p class="text-xs text-slate-500 mt-1">Relación positiva moderada</p>
                        </div>
                        <div class="bg-white border border-slate-100 rounded-2xl p-4">
                            <p class="text-xs font-bold text-slate-500 uppercase">Edad vs FC</p>
                            <p class="text-2xl font-extrabold mt-2"
                                :class="(datos?.correlaciones?.edad_fc || 0) < 0 ? 'text-red-600' : 'text-emerald-600'"
                                x-text="fmt(datos?.correlaciones?.edad_fc)"></p>
                            <p class="text-xs text-slate-500 mt-1">Relación negativa leve</p>
                        </div>
                    </div>
                </div>

                {{-- CALIDAD DEL DATASET --}}
                <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-4">
                    <h4 class="text-sm font-bold text-emerald-800 mb-3">Calidad del dataset</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <div>
                            <p class="text-xs text-emerald-600">Válidos</p>
                            <p class="text-xl font-extrabold text-emerald-800">
                                <span x-text="datos?.calidad?.valido_pct"></span>%
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-emerald-600">Nulos</p>
                            <p class="text-xl font-extrabold text-emerald-800" x-text="datos?.calidad?.nulos"></p>
                        </div>
                        <div>
                            <p class="text-xs text-emerald-600">Duplicados</p>
                            <p class="text-xl font-extrabold text-emerald-800" x-text="datos?.calidad?.duplicados">
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-emerald-600">Outliers</p>
                            <p class="text-xl font-extrabold text-emerald-800" x-text="datos?.calidad?.outliers"></p>
                        </div>
                    </div>
                </div>

                {{-- TOP USUARIOS --}}
                <div class="bg-white border border-slate-100 rounded-2xl p-4">
                    <h4 class="text-sm font-bold text-slate-700 mb-3">Top usuarios que registran signos vitales</h4>
                    <template x-if="datos?.por_usuario?.length">
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead>
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-bold text-slate-500 uppercase">Usuario
                                    </th>
                                    <th class="px-3 py-2 text-right text-xs font-bold text-slate-500 uppercase">
                                        Registros</th>
                                    <th class="px-3 py-2 text-right text-xs font-bold text-slate-500 uppercase">FC
                                        Prom.</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <template x-for="u in datos.por_usuario" :key="u.usuario">
                                    <tr>
                                        <td class="px-3 py-2 text-sm text-slate-700" x-text="u.usuario"></td>
                                        <td class="px-3 py-2 text-sm text-right font-bold text-slate-800"
                                            x-text="u.total"></td>
                                        <td class="px-3 py-2 text-sm text-right text-slate-600"
                                            x-text="u.fc_prom + ' bpm'"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </template>
                    <template x-if="!datos?.por_usuario?.length">
                        <p class="text-xs text-slate-400">Sin datos por usuario</p>
                    </template>
                </div>

                {{-- POR ESPECIALIDAD --}}
                <div class="bg-white border border-slate-100 rounded-2xl p-4">
                    <h4 class="text-sm font-bold text-slate-700 mb-3">Registros por especialidad</h4>
                    <template x-if="datos?.por_especialidad?.length">
                        <div class="space-y-2">
                            <template x-for="e in datos.por_especialidad" :key="e.especialidad">
                                <div class="flex items-center gap-3">
                                    <p class="text-xs text-slate-600 w-40 truncate" x-text="e.especialidad"></p>
                                    <div class="flex-1 bg-slate-100 rounded-full h-3">
                                        <div class="bg-indigo-500 h-3 rounded-full"
                                            :style="`width:${(e.total / maxEspecialidad()) * 100}%`"></div>
                                    </div>
                                    <p class="text-xs font-bold text-slate-700 w-12 text-right" x-text="e.total"></p>
                                </div>
                            </template>
                        </div>
                    </template>
                    <template x-if="!datos?.por_especialidad?.length">
                        <p class="text-xs text-slate-400">Sin especialidades registradas</p>
                    </template>
                </div>

                {{-- ESQUEMA ESTRELLA --}}
                <div class="bg-indigo-900 text-white rounded-2xl p-6">
                    <h4 class="text-sm font-bold mb-4 uppercase tracking-wider text-indigo-200">Esquema Estrella del
                        DWH</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-red-500/20 border border-red-400/40 rounded-xl p-4 md:col-span-1">
                            <p class="text-xs font-bold text-red-200 uppercase">Fact Table</p>
                            <p class="text-sm font-bold mt-1">fact_triage_consultations</p>
                            <p class="text-[10px] text-red-200 mt-2">
                                tiempo_espera · fc_prom · temp_prom · spo2_prom
                            </p>
                        </div>
                        <div class="md:col-span-2 grid grid-cols-2 gap-3">
                            <div class="bg-white/10 rounded-xl p-3">
                                <p class="text-[10px] text-indigo-200 uppercase">dim_fecha</p>
                                <p class="text-xs font-semibold">día, mes, año</p>
                            </div>
                            <div class="bg-white/10 rounded-xl p-3">
                                <p class="text-[10px] text-indigo-200 uppercase">dim_hora</p>
                                <p class="text-xs font-semibold">hora, franja_horaria</p>
                            </div>
                            <div class="bg-white/10 rounded-xl p-3">
                                <p class="text-[10px] text-indigo-200 uppercase">dim_paciente</p>
                                <p class="text-xs font-semibold">grupo_edad, género</p>
                            </div>
                            <div class="bg-white/10 rounded-xl p-3">
                                <p class="text-[10px] text-indigo-200 uppercase">dim_medico</p>
                                <p class="text-xs font-semibold">especialidad, rango</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- SCRIPT ALPINE --}}
<script>
    function dwhModal() {
        return {
            cargando: true,
            error: null,
            datos: null,
            charts: {},

            async cargar() {
                this.cargando = true;
                this.error = null;
                try {
                    const res = await fetch('{{ route('admin.dwh-data') }}', {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    const json = await res.json();
                    if (json.error) {
                        this.error = json.error;
                    } else {
                        this.datos = json;
                        this.$nextTick(() => this.renderCharts());
                    }
                } catch (e) {
                    console.error('Error cargando DWH:', e);
                    this.error = 'Error de conexión al cargar el Data Warehouse.';
                } finally {
                    this.cargando = false;
                }
            },

            fmt(v) {
                if (v === null || v === undefined) return '—';
                return (v > 0 ? '+' : '') + Number(v).toFixed(4);
            },

            picoHora() {
                if (!this.datos?.por_hora?.length) return '—';
                const pico = this.datos.por_hora.reduce((a, b) => (b.total > a.total ? b : a));
                return pico.hora + ' — ' + pico.total + ' registros';
            },

            maxEspecialidad() {
                if (!this.datos?.por_especialidad?.length) return 1;
                return Math.max(...this.datos.por_especialidad.map(e => e.total), 1);
            },

            renderCharts() {
                if (!this.datos) return;
                Object.values(this.charts).forEach(c => c && c.destroy());
                this.charts = {};

                // Triage
                const ctxT = document.getElementById('chartDwhTriage');
                if (ctxT) {
                    this.charts.triage = new Chart(ctxT, {
                        type: 'bar',
                        data: {
                            labels: this.datos.triage.map(t => t.nivel),
                            datasets: [{
                                data: this.datos.triage.map(t => t.total),
                                backgroundColor: this.datos.triage.map(t => t.color),
                                borderRadius: 6,
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
                        }
                    });
                }

                // Hora
                const ctxH = document.getElementById('chartDwhHora');
                if (ctxH) {
                    const max = Math.max(...this.datos.por_hora.map(h => h.total), 1);
                    this.charts.hora = new Chart(ctxH, {
                        type: 'bar',
                        data: {
                            labels: this.datos.por_hora.map(h => h.hora),
                            datasets: [{
                                data: this.datos.por_hora.map(h => h.total),
                                backgroundColor: this.datos.por_hora.map(h =>
                                    h.total === max ? '#4f46e5' : '#c7d2fe'
                                ),
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
                                    title: {
                                        display: true,
                                        text: 'Registros'
                                    }
                                },
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Hora del día'
                                    }
                                },
                            }
                        }
                    });
                }
            }
        }
    }
</script>
