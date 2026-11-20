{{-- ============================================================ --}}
{{-- MODAL: SIMULADOR DE RIESGO CLÍNICO --}}
{{-- ============================================================ --}}
<div x-show="modalAbierto === 'simulador-riesgo'" x-cloak
    class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4"
    style="display: none;" @click.self="modalAbierto = null" @keydown.escape.window="modalAbierto = null">

    <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100">

        {{-- ENCABEZADO --}}
        <div class="bg-gradient-to-r from-rose-600 to-red-800 text-white p-6 rounded-t-2xl sticky top-0 z-10">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center shrink-0">
                        <x-heroicon-o-heart class="w-6 h-6" />
                    </div>
                    <div>
                        <h3 class="text-xl font-bold">Simulador de Riesgo Clínico</h3>
                        <p class="text-xs text-rose-100">
                            Validación de signos vitales e identificación de pacientes de riesgo
                        </p>
                    </div>
                </div>
                <button type="button" @click="modalAbierto = null" class="text-white/80 hover:text-white transition">
                    <x-heroicon-o-x-mark class="w-6 h-6" />
                </button>
            </div>
        </div>

        {{-- CONTENIDO --}}
        <div class="p-6 space-y-5" x-data="simuladorRiesgoModal()" x-init="cargarFenotipos()">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <p class="text-xs text-slate-500">
                    Completa los signos vitales con validación de rangos clínicos para evaluar el riesgo del paciente.
                </p>
                <div class="flex items-center gap-2">
                    <button type="button" @click="cargarCasoCritico()"
                        class="px-3 py-1.5 rounded-xl text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100 transition">
                        Cargar caso crítico
                    </button>
                    <button type="button" @click="limpiarSimulador()"
                        class="px-3 py-1.5 rounded-xl text-xs font-bold bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 transition">
                        Limpiar
                    </button>
                </div>
            </div>

            <form @submit.prevent="simularCaso()" novalidate class="space-y-4">
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-600 mb-1">
                            Edad (años) *
                        </label>
                        <input type="number" step="1" min="0" max="120"
                            x-model="sim.edad" @input="validarCampo('edad')"
                            placeholder="Ej. 65"
                            class="w-full rounded-xl bg-slate-50/60 px-3 py-2 text-sm border outline-none transition"
                            :class="erroresSim.edad ? 'border-red-500 bg-red-50/40 focus:border-red-600' : 'border-slate-200 focus:border-rose-500 focus:bg-white'">
                        <p x-show="erroresSim.edad" x-text="erroresSim.edad"
                            class="mt-1 text-xs font-medium text-red-600"></p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-600 mb-1">
                            FC (lpm) *
                        </label>
                        <input type="number" step="1" min="20" max="240"
                            x-model="sim.fc" @input="validarCampo('fc')"
                            placeholder="60 - 100"
                            class="w-full rounded-xl bg-slate-50/60 px-3 py-2 text-sm border outline-none transition"
                            :class="erroresSim.fc ? 'border-red-500 bg-red-50/40 focus:border-red-600' : 'border-slate-200 focus:border-rose-500 focus:bg-white'">
                        <p x-show="erroresSim.fc" x-text="erroresSim.fc"
                            class="mt-1 text-xs font-medium text-red-600"></p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-600 mb-1">
                            SpO2 (%) *
                        </label>
                        <input type="number" step="0.1" min="40" max="100"
                            x-model="sim.spo2" @input="validarCampo('spo2')"
                            placeholder="95 - 100"
                            class="w-full rounded-xl bg-slate-50/60 px-3 py-2 text-sm border outline-none transition"
                            :class="erroresSim.spo2 ? 'border-red-500 bg-red-50/40 focus:border-red-600' : 'border-slate-200 focus:border-rose-500 focus:bg-white'">
                        <p x-show="erroresSim.spo2" x-text="erroresSim.spo2"
                            class="mt-1 text-xs font-medium text-red-600"></p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-600 mb-1">
                            P.A. Sistólica (mmHg) *
                        </label>
                        <input type="number" step="1" min="50" max="260"
                            x-model="sim.pas" @input="validarCampo('pas')"
                            placeholder="90 - 129"
                            class="w-full rounded-xl bg-slate-50/60 px-3 py-2 text-sm border outline-none transition"
                            :class="erroresSim.pas ? 'border-red-500 bg-red-50/40 focus:border-red-600' : 'border-slate-200 focus:border-rose-500 focus:bg-white'">
                        <p x-show="erroresSim.pas" x-text="erroresSim.pas"
                            class="mt-1 text-xs font-medium text-red-600"></p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-600 mb-1">
                            P.A. Diastólica (mmHg) *
                        </label>
                        <input type="number" step="1" min="30" max="160"
                            x-model="sim.pad" @input="validarCampo('pad')"
                            placeholder="60 - 84"
                            class="w-full rounded-xl bg-slate-50/60 px-3 py-2 text-sm border outline-none transition"
                            :class="erroresSim.pad ? 'border-red-500 bg-red-50/40 focus:border-red-600' : 'border-slate-200 focus:border-rose-500 focus:bg-white'">
                        <p x-show="erroresSim.pad" x-text="erroresSim.pad"
                            class="mt-1 text-xs font-medium text-red-600"></p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-600 mb-1">
                            Temperatura (°C) *
                        </label>
                        <input type="number" step="0.1" min="30" max="43"
                            x-model="sim.temp" @input="validarCampo('temp')"
                            placeholder="36.0 - 37.4"
                            class="w-full rounded-xl bg-slate-50/60 px-3 py-2 text-sm border outline-none transition"
                            :class="erroresSim.temp ? 'border-red-500 bg-red-50/40 focus:border-red-600' : 'border-slate-200 focus:border-rose-500 focus:bg-white'">
                        <p x-show="erroresSim.temp" x-text="erroresSim.temp"
                            class="mt-1 text-xs font-medium text-red-600"></p>
                    </div>
                </div>

                <div class="flex items-center justify-end pt-1">
                    <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-sm transition">
                        Evaluar riesgo del caso
                    </button>
                </div>
            </form>

            {{-- RESULTADO DE SIMULACIÓN --}}
            <template x-if="resultadoSim">
                <div class="rounded-2xl p-4 border space-y-3"
                    :class="{
                        'bg-red-50/70 border-red-200': resultadoSim.nivel === 'Alto',
                        'bg-amber-50/70 border-amber-200': resultadoSim.nivel === 'Moderado',
                        'bg-emerald-50/70 border-emerald-200': resultadoSim.nivel === 'Bajo'
                    }">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5">
                            <span class="px-3 py-1 rounded-full text-xs font-extrabold uppercase"
                                :class="{
                                    'bg-red-600 text-white': resultadoSim.nivel === 'Alto',
                                    'bg-amber-500 text-white': resultadoSim.nivel === 'Moderado',
                                    'bg-emerald-600 text-white': resultadoSim.nivel === 'Bajo'
                                }"
                                x-text="'Riesgo ' + resultadoSim.nivel"></span>
                            <span class="text-sm font-bold text-slate-800"
                                x-text="'Fenotipo afín: ' + resultadoSim.fenotipo"></span>
                        </div>
                        <span class="text-xs font-bold text-slate-600"
                            x-text="'Puntaje de alerta: ' + resultadoSim.puntaje"></span>
                    </div>

                    <ul class="list-disc list-inside text-xs text-slate-700 space-y-1">
                        <template x-for="(hallazgo, idx) in resultadoSim.hallazgos" :key="idx">
                            <li x-text="hallazgo"></li>
                        </template>
                    </ul>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
    function simuladorRiesgoModal() {
        return {
            distribucion: [],
            sim: {
                edad: '',
                fc: '',
                spo2: '',
                pas: '',
                pad: '',
                temp: ''
            },
            erroresSim: {},
            resultadoSim: null,

            reglasVitales: {
                edad: { min: 0, max: 120, etiqueta: 'Edad', unidad: 'años' },
                fc:   { min: 20, max: 240, etiqueta: 'FC', unidad: 'lpm' },
                spo2: { min: 40, max: 100, etiqueta: 'SpO2', unidad: '%' },
                pas:  { min: 50, max: 260, etiqueta: 'P.A. Sistólica', unidad: 'mmHg' },
                pad:  { min: 30, max: 160, etiqueta: 'P.A. Diastólica', unidad: 'mmHg' },
                temp: { min: 30, max: 43, etiqueta: 'Temperatura', unidad: '°C' }
            },

            async cargarFenotipos() {
                try {
                    const res = await fetch('{{ route('admin.fenotipado-data') }}', {
                        headers: { 'Accept': 'application/json' }
                    });
                    const json = await res.json();
                    if (!json.error && Array.isArray(json.distribucion)) {
                        this.distribucion = json.distribucion;
                    }
                } catch (e) {
                    console.warn('No se pudieron cargar fenotipos de referencia:', e);
                }
            },

            validarCampo(campo) {
                const regla = this.reglasVitales[campo];
                const raw = this.sim[campo];

                if (raw === '' || raw === null || raw === undefined) {
                    this.erroresSim[campo] = `${regla.etiqueta} es obligatoria.`;
                    return false;
                }

                const num = Number(raw);
                if (Number.isNaN(num)) {
                    this.erroresSim[campo] = 'Debe ser un número válido.';
                    return false;
                }

                if (num < regla.min || num > regla.max) {
                    this.erroresSim[campo] = `Rango: ${regla.min} a ${regla.max} ${regla.unidad}.`;
                    return false;
                }

                delete this.erroresSim[campo];

                if ((campo === 'pas' || campo === 'pad') && this.sim.pas !== '' && this.sim.pad !== '') {
                    if (Number(this.sim.pad) >= Number(this.sim.pas)) {
                        this.erroresSim.pad = 'La diastólica debe ser menor que la sistólica.';
                        return false;
                    }
                    delete this.erroresSim.pad;
                }

                return true;
            },

            validarTodo() {
                this.erroresSim = {};
                let ok = true;
                for (const campo of Object.keys(this.reglasVitales)) {
                    if (!this.validarCampo(campo)) {
                        ok = false;
                    }
                }
                return ok;
            },

            cargarCasoCritico() {
                this.sim = {
                    edad: 68,
                    fc: 128,
                    spo2: 86,
                    pas: 84,
                    pad: 52,
                    temp: 39.1
                };
                this.validarTodo();
                this.simularCaso();
            },

            limpiarSimulador() {
                this.sim = {
                    edad: '',
                    fc: '',
                    spo2: '',
                    pas: '',
                    pad: '',
                    temp: ''
                };
                this.erroresSim = {};
                this.resultadoSim = null;
            },

            simularCaso() {
                if (!this.validarTodo()) {
                    this.resultadoSim = null;
                    return;
                }

                const edad = Number(this.sim.edad);
                const fc = Number(this.sim.fc);
                const spo2 = Number(this.sim.spo2);
                const pas = Number(this.sim.pas);
                const pad = Number(this.sim.pad);
                const temp = Number(this.sim.temp);

                let puntaje = 0;
                const hallazgos = [];

                if (spo2 < 90) {
                    puntaje += 3;
                    hallazgos.push(`Hipoxemia crítica (SpO2 ${spo2}% < 90%).`);
                } else if (spo2 < 94) {
                    puntaje += 1;
                    hallazgos.push(`Saturación límite (SpO2 ${spo2}%).`);
                }

                if (fc > 120 || fc < 50) {
                    puntaje += 2;
                    hallazgos.push(`Frecuencia cardíaca fuera de margen seguro (${fc} lpm).`);
                } else if (fc > 100 || fc < 60) {
                    puntaje += 1;
                    hallazgos.push(`Alteración leve de FC (${fc} lpm).`);
                }

                if (pas < 90 || pad < 60) {
                    puntaje += 3;
                    hallazgos.push(`Hipotensión arterial (${pas}/${pad} mmHg).`);
                } else if (pas >= 180 || pad >= 120) {
                    puntaje += 3;
                    hallazgos.push(`Elevación tensional severa (${pas}/${pad} mmHg).`);
                } else if (pas >= 140 || pad >= 90) {
                    puntaje += 1;
                    hallazgos.push(`Presión arterial elevada (${pas}/${pad} mmHg).`);
                }

                if (temp >= 38.5 || temp < 35.5) {
                    puntaje += 2;
                    hallazgos.push(`Temperatura crítica (${temp} °C).`);
                } else if (temp >= 37.5) {
                    puntaje += 1;
                    hallazgos.push(`Febrícula (${temp} °C).`);
                }

                if (edad >= 65) {
                    puntaje += 1;
                    hallazgos.push(`Adulto mayor (${edad} años) con mayor vulnerabilidad clínica.`);
                }

                if (hallazgos.length === 0) {
                    hallazgos.push('Signos vitales dentro de parámetros habituales sin alertas agudas.');
                }

                let nivel = 'Bajo';
                if (puntaje >= 4) {
                    nivel = 'Alto';
                } else if (puntaje >= 2) {
                    nivel = 'Moderado';
                }

                let fenotipoAsignado = 'No determinado';
                if (this.distribucion.length > 0) {
                    let mejor = null;
                    let menorDistancia = Infinity;

                    for (const g of this.distribucion) {
                        const dFc = (fc - Number(g.fc_prom || 80)) / 30;
                        const dSpo2 = (spo2 - Number(g.spo2_prom || 96)) / 6;
                        const dEdad = (edad - Number(g.edad_prom || 45)) / 25;
                        const dist = Math.sqrt(dFc * dFc + dSpo2 * dSpo2 + dEdad * dEdad);
                        if (dist < menorDistancia) {
                            menorDistancia = dist;
                            mejor = g;
                        }
                    }
                    if (mejor) {
                        fenotipoAsignado = mejor.fenotipo;
                    }
                }

                this.resultadoSim = {
                    nivel,
                    puntaje,
                    fenotipo: fenotipoAsignado,
                    hallazgos
                };
            }
        }
    }
</script>