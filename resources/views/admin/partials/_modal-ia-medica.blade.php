{{-- ============================================================ --}}
{{-- MODAL: IA MÉDICA PREDICTIVA --}}
{{-- ============================================================ --}}
<div x-show="modalAbierto === 'ia-medica'" x-cloak
    class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4"
    style="display: none;" @click.self="modalAbierto = null" @keydown.escape.window="modalAbierto = null">

    <div class="bg-white rounded-3xl shadow-2xl max-w-6xl w-full max-h-[90vh] overflow-y-auto"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100">

        {{-- ENCABEZADO --}}
        <div class="bg-gradient-to-r from-red-600 to-rose-800 text-white p-6 rounded-t-3xl sticky top-0 z-10">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center">
                        <x-heroicon-o-heart class="w-6 h-6" />
                    </div>
                    <div>
                        <h3 class="text-xl font-bold">IA Médica Predictiva</h3>
                        <p class="text-xs text-rose-100">
                            Cinco modelos, un solo veredicto · Apoyo al triaje en milisegundos
                        </p>
                    </div>
                </div>
                <button @click="modalAbierto = null" class="text-white/80 hover:text-white">
                    <x-heroicon-o-x-mark class="w-6 h-6" />
                </button>
            </div>
        </div>

        {{-- CONTENIDO --}}
        <div class="p-6 space-y-6" x-data="iaMedicaModal()" x-init="cargarMetricas()">

            {{-- ==================== FORMULARIO ==================== --}}
            <div class="bg-slate-50 border border-slate-100 rounded-2xl p-5">
                <h4 class="text-sm font-bold text-slate-700 mb-4 uppercase tracking-wider">
                    Simulador Predictivo — Ingresa 4 signos vitales
                </h4>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase">FC (lpm)</label>
                        <input type="number" x-model.number="input.fc" min="30" max="250"
                            class="w-full mt-1 px-3 py-2 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-rose-500 focus:border-rose-500">
                        <p class="text-[10px] text-slate-400 mt-1">Normal 60–100</p>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase">SpO₂ (%)</label>
                        <input type="number" x-model.number="input.spo2" min="50" max="100"
                            class="w-full mt-1 px-3 py-2 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-rose-500 focus:border-rose-500">
                        <p class="text-[10px] text-slate-400 mt-1">Normal 95–100</p>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase">Temp (°C)</label>
                        <input type="number" step="0.1" x-model.number="input.temp" min="34" max="43"
                            class="w-full mt-1 px-3 py-2 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-rose-500 focus:border-rose-500">
                        <p class="text-[10px] text-slate-400 mt-1">Normal 36–37.5</p>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase">Edad (años)</label>
                        <input type="number" x-model.number="input.edad" min="0" max="120"
                            class="w-full mt-1 px-3 py-2 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-rose-500 focus:border-rose-500">
                        <p class="text-[10px] text-slate-400 mt-1">0–120</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 mt-4">
                    <button type="button" @click="predecir()" :disabled="prediciendo"
                        class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white text-sm font-bold rounded-xl transition-colors disabled:opacity-50">
                        <span x-show="!prediciendo">Ejecutar 5 modelos</span>
                        <span x-show="prediciendo">Analizando...</span>
                    </button>
                    <button type="button" @click="resetear()"
                        class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm font-bold rounded-xl">
                        Limpiar
                    </button>
                    <p class="text-xs text-slate-500" x-show="errorPrediccion" x-text="errorPrediccion"></p>
                </div>
            </div>

            {{-- ==================== RESULTADOS DE LOS 5 MODELOS ==================== --}}
            <template x-if="resultado">
                <div class="space-y-6">

                    {{-- 5 TARJETAS --}}
                    <div>
                        <h4 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-3">
                            Predicciones de los 5 algoritmos
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

                            {{-- LogReg --}}
                            <div class="bg-white border-2 border-red-200 rounded-2xl p-4">
                                <p class="text-xs font-bold text-red-600 uppercase">Regresión Logística</p>
                                <p class="text-3xl font-extrabold text-slate-800 mt-1"
                                    x-text="resultado?.logistica?.porcentaje + '%'"></p>
                                <p class="text-xs text-slate-500">Probabilidad de estado crítico</p>
                                <p class="text-[10px] text-slate-400 mt-2 font-mono"
                                    x-text="resultado?.logistica?.formula"></p>
                            </div>

                            {{-- SVM --}}
                            <div class="bg-white border-2 rounded-2xl p-4"
                                :class="resultado?.svm?.riesgo === 'Alto' ? 'border-orange-300' : 'border-emerald-300'">
                                <p class="text-xs font-bold uppercase"
                                    :class="resultado?.svm?.riesgo === 'Alto' ? 'text-orange-600' : 'text-emerald-600'">
                                    SVM
                                </p>
                                <p class="text-3xl font-extrabold mt-1"
                                    :class="resultado?.svm?.riesgo === 'Alto' ? 'text-orange-700' : 'text-emerald-700'"
                                    x-text="resultado?.svm?.riesgo + ' riesgo'"></p>
                                <p class="text-xs text-slate-500">Hiperplano: <span
                                        x-text="resultado?.svm?.hiperplano"></span></p>
                                <p class="text-[10px] text-slate-400 mt-2 font-mono" x-text="resultado?.svm?.formula">
                                </p>
                            </div>

                            {{-- Árbol --}}
                            <div class="bg-white border-2 rounded-2xl p-4"
                                :class="{
                                    'border-red-300': resultado?.arbol?.color === 'red',
                                    'border-orange-300': resultado?.arbol?.color === 'orange',
                                    'border-yellow-300': resultado?.arbol?.color === 'yellow',
                                    'border-green-300': resultado?.arbol?.color === 'green',
                                }">
                                <p class="text-xs font-bold text-slate-600 uppercase">Árbol de Decisión</p>
                                <p class="text-lg font-extrabold mt-1"
                                    :class="{
                                        'text-red-700': resultado?.arbol?.color === 'red',
                                        'text-orange-700': resultado?.arbol?.color === 'orange',
                                        'text-yellow-700': resultado?.arbol?.color === 'yellow',
                                        'text-green-700': resultado?.arbol?.color === 'green',
                                    }"
                                    x-text="resultado?.arbol?.recomendacion"></p>
                                <p class="text-xs text-slate-500" x-text="resultado?.arbol?.razon"></p>
                            </div>

                            {{-- Random Forest --}}
                            <div class="bg-white border-2 rounded-2xl p-4"
                                :class="resultado?.rf?.voto_final === 'Crítico' ? 'border-red-300' : 'border-emerald-300'">
                                <p class="text-xs font-bold text-slate-600 uppercase">Random Forest</p>
                                <p class="text-2xl font-extrabold mt-1"
                                    :class="resultado?.rf?.voto_final === 'Crítico' ? 'text-red-700' : 'text-emerald-700'"
                                    x-text="resultado?.rf?.voto_final"></p>
                                <div class="flex gap-1 mt-2">
                                    <template x-for="(v, i) in resultado?.rf?.votos || []" :key="i">
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded"
                                            :class="v === 'Crítico' ? 'bg-red-100 text-red-700' :
                                                'bg-emerald-100 text-emerald-700'"
                                            x-text="'Árbol ' + (i+1)"></span>
                                    </template>
                                </div>
                                <p class="text-xs text-slate-500 mt-2">
                                    Confianza: <span class="font-bold" x-text="resultado?.rf?.confianza + '%'"></span>
                                </p>
                            </div>

                            {{-- Regresión Lineal --}}
                            <div class="bg-white border-2 border-blue-200 rounded-2xl p-4">
                                <p class="text-xs font-bold text-blue-600 uppercase">Regresión Lineal</p>
                                <p class="text-2xl font-extrabold text-slate-800 mt-1">
                                    SpO₂ <span x-text="resultado?.lineal?.spo2_esperado"></span>%
                                </p>
                                <p class="text-xs text-slate-500">
                                    Actual: <span x-text="resultado?.lineal?.spo2_actual"></span>% ·
                                    <span class="font-bold" x-text="resultado?.lineal?.estado"></span>
                                </p>
                                <p class="text-[10px] text-slate-400 mt-2 font-mono"
                                    x-text="resultado?.lineal?.formula"></p>
                            </div>

                            {{-- Resumen crítico --}}
                            <div class="rounded-2xl p-4 text-white"
                                :class="esCritico() ? 'bg-gradient-to-br from-red-600 to-red-800' :
                                    'bg-gradient-to-br from-emerald-600 to-emerald-800'">
                                <p class="text-xs font-bold uppercase opacity-80">Veredicto conjunto</p>
                                <p class="text-2xl font-extrabold mt-1"
                                    x-text="esCritico() ? '⚠ RIESGO ALTO' : '✓ ESTABLE'"></p>
                                <p class="text-xs opacity-90 mt-1"
                                    x-text="esCritico()
                                    ? 'La mayoría de los modelos coincide en riesgo crítico.'
                                    : 'La mayoría de los modelos coincide en bajo riesgo.'">
                                </p>
                            </div>

                        </div>
                    </div>

                    {{-- PREDICCIÓN DE COSTOS Y DÍAS --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4">
                            <p class="text-xs font-bold text-amber-700 uppercase">Costo estimado</p>
                            <p class="text-3xl font-extrabold text-amber-900 mt-1">
                                $<span
                                    x-text="Number(resultado?.costo?.costo_estimado || 0).toLocaleString('es-MX')"></span>
                                <span class="text-sm font-medium text-amber-600"
                                    x-text="resultado?.costo?.moneda"></span>
                            </p>
                            <p class="text-xs text-amber-600 mt-1">Predicción del modelo financiero</p>
                        </div>
                        <div class="bg-indigo-50 border border-indigo-200 rounded-2xl p-4">
                            <p class="text-xs font-bold text-indigo-700 uppercase">Días de estancia</p>
                            <p class="text-3xl font-extrabold text-indigo-900 mt-1" x-text="resultado?.dias?.rango">
                            </p>
                            <p class="text-xs text-indigo-600 mt-1">Estimación de hospitalización</p>
                        </div>
                    </div>

                </div>
            </template>

            {{-- ==================== MÉTRICAS DEL MODELO ==================== --}}
            <div class="space-y-6">

                {{-- KPIs --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                        <p class="text-xs text-slate-500 font-bold uppercase">Casos cerrados</p>
                        <p class="text-2xl font-extrabold text-slate-800" x-text="metricas?.casos_cerrados"></p>
                    </div>
                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                        <p class="text-xs text-slate-500 font-bold uppercase">Accuracy</p>
                        <p class="text-2xl font-extrabold text-red-600" x-text="metricas?.accuracy + '%'"></p>
                    </div>
                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                        <p class="text-xs text-slate-500 font-bold uppercase">Recall</p>
                        <p class="text-2xl font-extrabold text-orange-600" x-text="metricas?.recall + '%'"></p>
                    </div>
                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                        <p class="text-xs text-slate-500 font-bold uppercase">F1-Score</p>
                        <p class="text-2xl font-extrabold text-emerald-600" x-text="metricas?.f1 + '%'"></p>
                    </div>
                </div>

                {{-- Accuracy por modelo --}}
                <div class="bg-white border border-slate-100 rounded-2xl p-4">
                    <h4 class="text-sm font-bold text-slate-700 mb-3">Accuracy por modelo (%)</h4>
                    <div class="h-56">
                        <canvas id="chartIaAccuracy"></canvas>
                    </div>
                </div>

                {{-- Matriz de confusión --}}
                <template x-if="metricas?.matriz">
                    <div class="bg-white border border-slate-100 rounded-2xl p-4">
                        <h4 class="text-sm font-bold text-slate-700 mb-3">Matriz de confusión</h4>
                        <div class="grid grid-cols-3 gap-2 text-center text-sm">
                            <div></div>
                            <div class="font-bold text-slate-500">Pred: Vivo</div>
                            <div class="font-bold text-slate-500">Pred: Fallece</div>

                            <div class="font-bold text-slate-500 flex items-center">Real: Vivo</div>
                            <div class="bg-emerald-50 rounded-lg p-3">
                                <p class="text-2xl font-extrabold text-emerald-700" x-text="metricas?.matriz?.vn"></p>
                                <p class="text-xs text-emerald-600">VN</p>
                            </div>
                            <div class="bg-red-50 rounded-lg p-3">
                                <p class="text-2xl font-extrabold text-red-700" x-text="metricas?.matriz?.fn"></p>
                                <p class="text-xs text-red-600">FN</p>
                            </div>

                            <div class="font-bold text-slate-500 flex items-center">Real: Fallece</div>
                            <div class="bg-orange-50 rounded-lg p-3">
                                <p class="text-2xl font-extrabold text-orange-700" x-text="metricas?.matriz?.fp"></p>
                                <p class="text-xs text-orange-600">FP</p>
                            </div>
                            <div class="bg-emerald-50 rounded-lg p-3">
                                <p class="text-2xl font-extrabold text-emerald-700" x-text="metricas?.matriz?.vp"></p>
                                <p class="text-xs text-emerald-600">VP</p>
                            </div>
                        </div>
                    </div>
                </template>

                {{-- Feature importance --}}
                <div class="bg-white border border-slate-100 rounded-2xl p-4">
                    <h4 class="text-sm font-bold text-slate-700 mb-3">Feature Importance (Random Forest)</h4>
                    <div class="space-y-2">
                        <template x-for="f in featureImportance" :key="f.variable">
                            <div class="flex items-center gap-3">
                                <p class="text-xs text-slate-600 w-20 font-bold" x-text="f.variable"></p>
                                <div class="flex-1 bg-slate-100 rounded-full h-3">
                                    <div class="bg-gradient-to-r from-red-500 to-rose-600 h-3 rounded-full"
                                        :style="`width:${(f.peso / 40) * 100}%`"></div>
                                </div>
                                <p class="text-xs font-bold text-slate-700 w-12 text-right" x-text="f.peso + '%'"></p>
                            </div>
                        </template>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

{{-- SCRIPT ALPINE --}}
<script>
    function iaMedicaModal() {
        return {
            input: {
                fc: 85,
                spo2: 96,
                temp: 36.8,
                edad: 45
            },
            resultado: null,
            prediciendo: false,
            errorPrediccion: null,
            metricas: null,
            featureImportance: [],
            accuracyPorModelo: [],
            charts: {},

            async cargarMetricas() {
                try {
                    const res = await fetch('{{ route('admin.ia-medica-data') }}', {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    const json = await res.json();
                    this.metricas = json.metricas;
                    this.featureImportance = json.feature_importance;
                    this.accuracyPorModelo = json.accuracy_por_modelo;
                    this.$nextTick(() => this.renderChart());
                } catch (e) {
                    console.error('Error cargando métricas IA:', e);
                }
            },

            async predecir() {
                this.prediciendo = true;
                this.errorPrediccion = null;
                try {
                    const res = await fetch('{{ route('admin.ia-medica-predecir') }}', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(this.input)
                    });
                    if (!res.ok) {
                        const err = await res.json();
                        this.errorPrediccion = err.message || 'Error al predecir';
                        return;
                    }
                    this.resultado = await res.json();
                } catch (e) {
                    console.error(e);
                    this.errorPrediccion = 'Error de conexión';
                } finally {
                    this.prediciendo = false;
                }
            },

            resetear() {
                this.input = {
                    fc: 85,
                    spo2: 96,
                    temp: 36.8,
                    edad: 45
                };
                this.resultado = null;
                this.errorPrediccion = null;
            },

            esCritico() {
                if (!this.resultado) return false;
                const criticos = [
                    this.resultado.logistica?.nivel === 'crítico',
                    this.resultado.svm?.riesgo === 'Alto',
                    this.resultado.arbol?.recomendacion === 'UCI Inmediata',
                    this.resultado.rf?.voto_final === 'Crítico',
                ].filter(Boolean).length;
                return criticos >= 2;
            },

            renderChart() {
                const ctx = document.getElementById('chartIaAccuracy');
                if (!ctx) return;
                if (this.charts.acc) this.charts.acc.destroy();

                this.charts.acc = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: this.accuracyPorModelo.map(m => m.modelo),
                        datasets: [{
                            data: this.accuracyPorModelo.map(m => m.accuracy),
                            backgroundColor: this.accuracyPorModelo.map(m => m.color),
                            borderRadius: 6,
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
                                    label: (ctx) => ctx.parsed.y + '%'
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100,
                                title: {
                                    display: true,
                                    text: 'Accuracy (%)'
                                }
                            }
                        }
                    }
                });
            }
        }
    }
</script>
