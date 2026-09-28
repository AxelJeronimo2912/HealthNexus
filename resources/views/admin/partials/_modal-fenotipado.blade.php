{{-- ============================================================ --}}
{{-- MODAL: FENOTIPADO CLÍNICO --}}
{{-- ============================================================ --}}
<div x-show="modalAbierto === 'fenotipado'" x-cloak
    class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4"
    style="display: none;" @click.self="modalAbierto = null" @keydown.escape.window="modalAbierto = null">

    <div class="bg-white rounded-3xl shadow-2xl max-w-6xl w-full max-h-[90vh] overflow-y-auto"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100">

        {{-- ENCABEZADO --}}
        <div class="bg-gradient-to-r from-rose-600 to-red-800 text-white p-6 rounded-t-3xl sticky top-0 z-10">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center">
                        <x-heroicon-o-beaker class="w-6 h-6" />
                    </div>
                    <div>
                        <h3 class="text-xl font-bold">Fenotipado Clínico</h3>
                        <p class="text-xs text-rose-100">
                            K-Means + PCA sobre signos vitales de urgencias
                        </p>
                    </div>
                </div>
                <button @click="modalAbierto = null" class="text-white/80 hover:text-white">
                    <x-heroicon-o-x-mark class="w-6 h-6" />
                </button>
            </div>
        </div>

        {{-- CONTENIDO --}}
        <div class="p-6 space-y-6" x-data="fenotipadoModal()" x-init="cargar()">

            {{-- LOADING --}}
            <div x-show="cargando" class="text-center py-12">
                <div
                    class="inline-block animate-spin rounded-full h-10 w-10 border-4 border-rose-500 border-t-transparent">
                </div>
                <p class="text-sm text-slate-500 mt-3">Ejecutando K-Means y PCA...</p>
            </div>

            {{-- ERROR --}}
            <template x-if="!cargando && error">
                <div class="text-center py-12 bg-amber-50 rounded-2xl border border-amber-100">
                    <x-heroicon-o-exclamation-triangle class="w-12 h-12 text-amber-500 mx-auto mb-2" />
                    <p class="text-sm font-bold text-amber-700" x-text="error"></p>
                </div>
            </template>

            <div x-show="!cargando && !error" class="space-y-6">

                {{-- KPIs --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                        <p class="text-xs text-slate-500 font-bold uppercase">Silhouette</p>
                        <p class="text-2xl font-extrabold text-rose-600" x-text="datos?.kpis?.silhouette"></p>
                        <p class="text-[10px] text-slate-400">-1 a 1 (mayor = mejor)</p>
                    </div>
                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                        <p class="text-xs text-slate-500 font-bold uppercase">Inercia (SSE)</p>
                        <p class="text-2xl font-extrabold text-blue-600" x-text="datos?.kpis?.inercia"></p>
                        <p class="text-[10px] text-slate-400">Suma distancias al centroide</p>
                    </div>
                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                        <p class="text-xs text-slate-500 font-bold uppercase">Iteraciones</p>
                        <p class="text-2xl font-extrabold text-emerald-600" x-text="datos?.kpis?.iteraciones"></p>
                        <p class="text-[10px] text-slate-400">Para converger</p>
                    </div>
                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                        <p class="text-xs text-slate-500 font-bold uppercase">Pacientes</p>
                        <p class="text-2xl font-extrabold text-slate-800" x-text="datos?.kpis?.total"></p>
                        <p class="text-[10px] text-slate-400">K = <span x-text="datos?.kpis?.k"></span></p>
                    </div>
                </div>

                {{-- FENOTIPOS --}}
                <div>
                    <h4 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-3">
                        Fenotipos identificados
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <template x-for="f in datos?.distribucion || []" :key="f.cluster">
                            <div class="rounded-2xl p-4 border"
                                :style="`border-color:${f.color}40; background:${f.color}10`">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="w-3 h-3 rounded-full" :style="`background:${f.color}`"></span>
                                    <p class="text-sm font-bold text-slate-800" x-text="f.fenotipo"></p>
                                </div>
                                <p class="text-2xl font-extrabold" :style="`color:${f.color}`" x-text="f.total"></p>
                                <p class="text-xs text-slate-500">pacientes</p>
                                <div class="mt-3 pt-3 border-t border-slate-200 space-y-1 text-xs">
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Edad prom.</span>
                                        <span class="font-bold" x-text="f.edad_prom + ' años'"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">FC prom.</span>
                                        <span class="font-bold" x-text="f.fc_prom + ' lpm'"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">SpO2 prom.</span>
                                        <span class="font-bold" x-text="f.spo2_prom + ' %'"></span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- MÉTODO DEL CODO + SILHOUETTE --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-white border border-slate-100 rounded-2xl p-4">
                        <h4 class="text-sm font-bold text-slate-700 mb-3">Método del Codo</h4>
                        <div class="h-56">
                            <canvas id="chartFenoCodo"></canvas>
                        </div>
                    </div>
                    <div class="bg-white border border-slate-100 rounded-2xl p-4">
                        <h4 class="text-sm font-bold text-slate-700 mb-3">Silhouette por K</h4>
                        <div class="h-56">
                            <canvas id="chartFenoSilhouette"></canvas>
                        </div>
                    </div>
                </div>

                {{-- PCA --}}
                <div class="bg-white border border-slate-100 rounded-2xl p-4">
                    <h4 class="text-sm font-bold text-slate-700 mb-3">PCA — Varianza explicada</h4>
                    <div class="h-56">
                        <canvas id="chartFenoPcaVar"></canvas>
                    </div>
                </div>

                {{-- SCATTER PCA --}}
                <div class="bg-white border border-slate-100 rounded-2xl p-4">
                    <h4 class="text-sm font-bold text-slate-700 mb-3">Proyección PCA de pacientes</h4>
                    <p class="text-xs text-slate-500 mb-3">Cada punto es un paciente. Color = fenotipo.</p>
                    <div class="h-72">
                        <canvas id="chartFenoScatter"></canvas>
                    </div>
                </div>

                {{-- TABLA DE PACIENTES --}}
                <div>
                    <h4 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-3">
                        Pacientes clasificados (muestra)
                    </h4>
                    <div class="overflow-x-auto bg-white border border-slate-100 rounded-2xl">
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-bold text-slate-500 uppercase">Paciente
                                    </th>
                                    <th class="px-4 py-2 text-right text-xs font-bold text-slate-500 uppercase">Edad
                                    </th>
                                    <th class="px-4 py-2 text-center text-xs font-bold text-slate-500 uppercase">Triage
                                    </th>
                                    <th class="px-4 py-2 text-right text-xs font-bold text-slate-500 uppercase">FC prom
                                    </th>
                                    <th class="px-4 py-2 text-right text-xs font-bold text-slate-500 uppercase">SpO2
                                        prom</th>
                                    <th class="px-4 py-2 text-center text-xs font-bold text-slate-500 uppercase">
                                        Fenotipo</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <template x-for="p in datos?.pacientes || []" :key="p.paciente_id">
                                    <tr>
                                        <td class="px-4 py-2 text-sm text-slate-700" x-text="p.nombre"></td>
                                        <td class="px-4 py-2 text-sm text-right" x-text="p.edad + ' a'"></td>
                                        <td class="px-4 py-2 text-center">
                                            <span class="px-2 py-0.5 rounded text-xs font-bold capitalize"
                                                :class="{
                                                    'bg-red-100 text-red-700': p.triage === 'rojo',
                                                    'bg-orange-100 text-orange-700': p.triage === 'naranja',
                                                    'bg-yellow-100 text-yellow-700': p.triage === 'amarillo',
                                                    'bg-green-100 text-green-700': p.triage === 'verde',
                                                    'bg-blue-100 text-blue-700': p.triage === 'azul'
                                                }"
                                                x-text="p.triage"></span>
                                        </td>
                                        <td class="px-4 py-2 text-sm text-right" x-text="p.fc_prom"></td>
                                        <td class="px-4 py-2 text-sm text-right" x-text="p.spo2_prom"></td>
                                        <td class="px-4 py-2 text-center">
                                            <span class="px-2 py-0.5 rounded-full text-xs font-bold"
                                                :style="`background:${(datos?.distribucion?.find(d => d.cluster === p.cluster)?.color) || '#94a3b8'}20; color:${(datos?.distribucion?.find(d => d.cluster === p.cluster)?.color) || '#475569'}`"
                                                x-text="p.fenotipo"></span>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- LOADINGS --}}
                <div class="bg-slate-50 border border-slate-100 rounded-2xl p-4">
                    <h4 class="text-sm font-bold text-slate-700 mb-3">Matriz de Loadings (PCA)</h4>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-xs">
                            <thead>
                                <tr>
                                    <th class="px-2 py-1 text-left text-slate-500 font-bold uppercase">Variable</th>
                                    <template x-for="c in datos?.pca?.loadings || []" :key="c.componente">
                                        <th class="px-2 py-1 text-center text-slate-500 font-bold uppercase"
                                            x-text="c.componente + ' (' + c.varianza + '%)'"></th>
                                    </template>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(v, i) in datos?.variables || []" :key="i">
                                    <tr class="border-t border-slate-200">
                                        <td class="px-2 py-1 font-semibold text-slate-700" x-text="v"></td>
                                        <template x-for="c in datos?.pca?.loadings || []" :key="c.componente">
                                            <td class="px-2 py-1 text-center font-mono"
                                                :class="c.valores[i] >= 0 ? 'text-emerald-600' : 'text-red-600'"
                                                x-text="(c.valores[i] >= 0 ? '+' : '') + c.valores[i].toFixed(3)"></td>
                                        </template>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- SCRIPT ALPINE --}}
<script>
    function fenotipadoModal() {
        return {
            cargando: true,
            error: null,
            datos: null,
            charts: {},

            async cargar() {
                this.cargando = true;
                this.error = null;
                try {
                    const res = await fetch('{{ route('admin.fenotipado-data') }}', {
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
                    console.error('Error cargando fenotipado:', e);
                    this.error = 'Error de conexión al cargar el fenotipado.';
                } finally {
                    this.cargando = false;
                }
            },

            renderCharts() {
                if (!this.datos) return;
                Object.values(this.charts).forEach(c => c && c.destroy());
                this.charts = {};

                // Codo
                const ctxCodo = document.getElementById('chartFenoCodo');
                if (ctxCodo) {
                    this.charts.codo = new Chart(ctxCodo, {
                        type: 'line',
                        data: {
                            labels: Object.keys(this.datos.codo).map(k => 'K=' + k),
                            datasets: [{
                                label: 'Inercia',
                                data: Object.values(this.datos.codo),
                                borderColor: '#8b5cf6',
                                backgroundColor: 'rgba(139,92,246,0.15)',
                                tension: 0.3,
                                fill: true,
                                pointRadius: 6,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            }
                        }
                    });
                }

                // Silhouette
                const ctxSil = document.getElementById('chartFenoSilhouette');
                if (ctxSil) {
                    this.charts.sil = new Chart(ctxSil, {
                        type: 'bar',
                        data: {
                            labels: Object.keys(this.datos.silhouettes).map(k => 'K=' + k),
                            datasets: [{
                                label: 'Silhouette',
                                data: Object.values(this.datos.silhouettes),
                                backgroundColor: '#f43f5e',
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
                            }
                        }
                    });
                }

                // PCA varianza
                const ctxVar = document.getElementById('chartFenoPcaVar');
                if (ctxVar) {
                    this.charts.var = new Chart(ctxVar, {
                        type: 'bar',
                        data: {
                            labels: this.datos.pca.loadings.map(l => l.componente),
                            datasets: [{
                                label: '% varianza',
                                data: this.datos.pca.loadings.map(l => l.varianza),
                                backgroundColor: ['#ef4444', '#10b981', '#f59e0b'],
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
                            }
                        }
                    });
                }

                // Scatter PCA
                const ctxSc = document.getElementById('chartFenoScatter');
                if (ctxSc) {
                    const colores = this.datos.distribucion.map(d => d.color);
                    const datasets = this.datos.distribucion.map((d, idx) => ({
                        label: d.fenotipo,
                        data: this.datos.pca.scatter
                            .filter(p => p.cluster === d.cluster)
                            .map(p => ({
                                x: p.x,
                                y: p.y
                            })),
                        backgroundColor: colores[idx],
                        pointRadius: 5,
                    }));
                    this.charts.sc = new Chart(ctxSc, {
                        type: 'scatter',
                        data: {
                            datasets
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                x: {
                                    title: {
                                        display: true,
                                        text: 'PC1'
                                    }
                                },
                                y: {
                                    title: {
                                        display: true,
                                        text: 'PC2'
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
