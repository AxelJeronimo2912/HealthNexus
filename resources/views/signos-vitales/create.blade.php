<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-3">
            <div class="min-w-0">
                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight truncate">
                    Nuevo Registro de Signos Vitales
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Captura de signos vitales y clasificación de triage</p>
            </div>
            <a href="{{ route('signos-vitales.index') }}"
                class="bg-white hover:bg-indigo-50 text-slate-700 hover:text-indigo-600 border border-slate-200 hover:border-indigo-200 px-5 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 transition-all active:scale-95 group shrink-0">
                <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
                Volver
            </a>
        </div>
    </x-slot>

    @php
        $inputCls =
            'w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-indigo-500 focus:ring-0 outline-none';
        $labelCls = 'block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5';
        $errorCls = 'text-rose-600 text-xs mt-1 font-medium';
    @endphp

    <div class="py-8 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <form action="{{ route('signos-vitales.store') }}" method="POST"
            class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden" id="form-signos">
            @csrf

            <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-800 text-base">Captura de signos vitales</h3>
                    <p class="text-[11px] text-slate-400">Los campos marcados con * son obligatorios</p>
                </div>
            </div>

            <div class="p-6 space-y-6">

                @if (session('warning'))
                    <div
                        class="p-4 bg-amber-50 border border-amber-100 text-amber-800 rounded-2xl text-xs font-semibold flex items-start gap-2">
                        <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>{{ session('warning') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div
                        class="p-4 bg-rose-50 border border-rose-100 text-rose-800 rounded-2xl text-xs font-semibold flex items-center gap-2">
                        <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="p-4 bg-rose-50 border border-rose-100 text-rose-800 rounded-2xl">
                        <p class="text-xs font-bold mb-2 uppercase tracking-wider">Corrige los siguientes errores:</p>
                        <ul class="list-disc list-inside text-xs space-y-1 font-medium">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (request('cita_id'))
                    <input type="hidden" name="cita_id" value="{{ request('cita_id') }}">
                @endif

                {{-- Paciente --}}
                <div>
                    <label class="{{ $labelCls }}">Paciente *</label>
                    <select name="paciente_id" required
                        class="{{ $inputCls }} @error('paciente_id') border-rose-300 @enderror">
                        <option value="">— Selecciona un paciente —</option>
                        @foreach ($pacientes as $p)
                            <option value="{{ $p->id }}" @selected(old('paciente_id', $pacienteSeleccionado?->id ?? '') == $p->id)>
                                {{ $p->nombre_completo }}
                            </option>
                        @endforeach
                    </select>
                    @error('paciente_id')
                        <p class="{{ $errorCls }}">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Signos vitales --}}
                <div class="pt-5 border-t border-slate-100">
                    <div class="flex items-center gap-2 mb-4">
                        <div
                            class="w-8 h-8 rounded-xl bg-rose-50 flex items-center justify-center text-rose-500 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-slate-800 text-sm">Signos vitales</h3>
                            <p class="text-[11px] text-slate-400">El triage se calculará automáticamente</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                            <label class="{{ $labelCls }}">Temperatura (°C)</label>
                            <input type="number" step="0.1" name="temperatura" id="temperatura"
                                value="{{ old('temperatura') }}" placeholder="36.5"
                                class="{{ $inputCls }} @error('temperatura') border-rose-300 @enderror">
                            @error('temperatura')
                                <p class="{{ $errorCls }}">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="{{ $labelCls }}">Frec. cardíaca (lpm)</label>
                            <input type="number" name="frecuencia_cardiaca" id="frecuencia_cardiaca"
                                value="{{ old('frecuencia_cardiaca') }}" placeholder="80"
                                class="{{ $inputCls }} @error('frecuencia_cardiaca') border-rose-300 @enderror">
                            @error('frecuencia_cardiaca')
                                <p class="{{ $errorCls }}">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="{{ $labelCls }}">Frec. respiratoria (rpm)</label>
                            <input type="number" name="frecuencia_respiratoria" id="frecuencia_respiratoria"
                                value="{{ old('frecuencia_respiratoria') }}" placeholder="18"
                                class="{{ $inputCls }} @error('frecuencia_respiratoria') border-rose-300 @enderror">
                            @error('frecuencia_respiratoria')
                                <p class="{{ $errorCls }}">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="{{ $labelCls }}">Presión arterial</label>
                            <input type="text" name="presion_arterial" id="presion_arterial"
                                value="{{ old('presion_arterial') }}" placeholder="120/80"
                                class="{{ $inputCls }} @error('presion_arterial') border-rose-300 @enderror">
                            @error('presion_arterial')
                                <p class="{{ $errorCls }}">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="{{ $labelCls }}">Saturación O₂ (%)</label>
                            <input type="number" name="saturacion_oxigeno" id="saturacion_oxigeno"
                                value="{{ old('saturacion_oxigeno') }}" placeholder="98"
                                class="{{ $inputCls }} @error('saturacion_oxigeno') border-rose-300 @enderror">
                            @error('saturacion_oxigeno')
                                <p class="{{ $errorCls }}">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="{{ $labelCls }}">Glucosa (mg/dL)</label>
                            <input type="number" name="glucosa" id="glucosa" value="{{ old('glucosa') }}"
                                placeholder="100"
                                class="{{ $inputCls }} @error('glucosa') border-rose-300 @enderror">
                            @error('glucosa')
                                <p class="{{ $errorCls }}">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="{{ $labelCls }}">Peso (kg)</label>
                            <input type="number" step="0.1" name="peso" id="peso"
                                value="{{ old('peso') }}" placeholder="70"
                                class="{{ $inputCls }} @error('peso') border-rose-300 @enderror">
                            @error('peso')
                                <p class="{{ $errorCls }}">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="{{ $labelCls }}">Talla (m)</label>
                            <input type="number" step="0.01" name="talla" id="talla"
                                value="{{ old('talla') }}" placeholder="1.70"
                                class="{{ $inputCls }} @error('talla') border-rose-300 @enderror">
                            @error('talla')
                                <p class="{{ $errorCls }}">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="{{ $labelCls }}">IMC (calculado)</label>
                            <input type="text" id="imc" readonly
                                class="w-full bg-slate-100 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-600 outline-none cursor-not-allowed">
                            <p class="text-[11px] text-slate-400 mt-1.5 font-medium" id="imc-clasificacion"></p>
                        </div>
                        <div>
                            <label class="{{ $labelCls }}">Escala de dolor (0-10)</label>
                            <input type="number" min="0" max="10" name="escala_dolor"
                                id="escala_dolor" value="{{ old('escala_dolor') }}" placeholder="0"
                                class="{{ $inputCls }} @error('escala_dolor') border-rose-300 @enderror">
                            @error('escala_dolor')
                                <p class="{{ $errorCls }}">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Triage --}}
                <div class="pt-5 border-t border-slate-100">
                    <div class="flex items-center gap-2 mb-4">
                        <div
                            class="w-8 h-8 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-slate-800 text-sm">Triage</h3>
                            <p class="text-[11px] text-slate-400">Calculado automáticamente. Puedes forzarlo
                                manualmente.</p>
                        </div>
                    </div>

                    <div class="mb-4 p-4 rounded-2xl border-2 border-slate-200 bg-slate-50/70" id="triage-preview">
                        <p class="{{ $labelCls }}">Triage estimado</p>
                        <p class="text-lg font-black text-slate-800 tracking-tight mt-1" id="triage-valor">— Sin
                            calcular —</p>
                        <p class="text-xs font-medium text-slate-500 mt-0.5" id="triage-descripcion"></p>
                    </div>

                    <label
                        class="inline-flex items-center gap-2.5 cursor-pointer p-3 bg-slate-50/70 border border-slate-100 rounded-2xl w-full">
                        <input type="checkbox" name="triage_manual" value="1" id="triage_manual"
                            @checked(old('triage_manual'))
                            class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 focus:ring-0 cursor-pointer">
                        <span class="text-xs font-bold text-slate-700">Forzar clasificación manual</span>
                    </label>

                    <div id="triage-select-container" class="mt-3 {{ old('triage_manual') ? '' : 'hidden' }}">
                        <label class="{{ $labelCls }}">Nivel de triage manual</label>
                        <select name="triage"
                            class="{{ $inputCls }} @error('triage') border-rose-300 @enderror">
                            <option value="">— Selecciona —</option>
                            <option value="rojo" @selected(old('triage') == 'rojo')>🔴 Rojo — Emergencia</option>
                            <option value="naranja" @selected(old('triage') == 'naranja')>🟠 Naranja — Muy urgente</option>
                            <option value="amarillo" @selected(old('triage') == 'amarillo')>🟡 Amarillo — Urgente</option>
                            <option value="verde" @selected(old('triage') == 'verde')>🟢 Verde — No urgente</option>
                            <option value="azul" @selected(old('triage') == 'azul')>🔵 Azul — Baja prioridad</option>
                        </select>
                        @error('triage')
                            <p class="{{ $errorCls }}">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Motivo y notas --}}
                <div class="pt-5 border-t border-slate-100 space-y-4">
                    <div>
                        <label class="{{ $labelCls }}">Motivo de consulta</label>
                        <textarea name="motivo_consulta" rows="2"
                            class="{{ $inputCls }} resize-none @error('motivo_consulta') border-rose-300 @enderror">{{ old('motivo_consulta') }}</textarea>
                        @error('motivo_consulta')
                            <p class="{{ $errorCls }}">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="{{ $labelCls }}">Notas</label>
                        <textarea name="notas" rows="2"
                            class="{{ $inputCls }} resize-none @error('notas') border-rose-300 @enderror">{{ old('notas') }}</textarea>
                        @error('notas')
                            <p class="{{ $errorCls }}">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

            </div>

            <div
                class="px-6 py-4 bg-slate-50/70 border-t border-slate-100 flex flex-wrap justify-between items-center gap-3">
                <a href="{{ route('signos-vitales.index') }}"
                    class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-600 rounded-xl text-xs font-bold transition-all">
                    Cancelar
                </a>
                <button type="submit"
                    class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                    Guardar Registro
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const peso = document.getElementById('peso');
            const talla = document.getElementById('talla');
            const imcInput = document.getElementById('imc');
            const imcClasificacion = document.getElementById('imc-clasificacion');

            const triageManual = document.getElementById('triage_manual');
            const triageSelectCont = document.getElementById('triage-select-container');
            const triagePreview = document.getElementById('triage-preview');
            const triageValor = document.getElementById('triage-valor');
            const triageDescripcion = document.getElementById('triage-descripcion');

            function calcularImc() {
                const p = parseFloat(peso?.value);
                const t = parseFloat(talla?.value);
                if (p > 0 && t > 0) {
                    const imc = p / (t * t);
                    imcInput.value = imc.toFixed(1);

                    let clasif = '';
                    let color = 'text-slate-400';
                    if (imc < 18.5) {
                        clasif = 'Bajo peso';
                        color = 'text-amber-600';
                    } else if (imc < 25) {
                        clasif = 'Normal';
                        color = 'text-emerald-600';
                    } else if (imc < 30) {
                        clasif = 'Sobrepeso';
                        color = 'text-amber-600';
                    } else if (imc < 35) {
                        clasif = 'Obesidad I';
                        color = 'text-orange-600';
                    } else if (imc < 40) {
                        clasif = 'Obesidad II';
                        color = 'text-rose-600';
                    } else {
                        clasif = 'Obesidad III';
                        color = 'text-rose-700';
                    }

                    if (imcClasificacion) {
                        imcClasificacion.textContent = clasif;
                        imcClasificacion.className = 'text-[11px] mt-1.5 font-bold ' + color;
                    }
                } else {
                    imcInput.value = '';
                    if (imcClasificacion) imcClasificacion.textContent = '';
                }
            }
            peso?.addEventListener('input', calcularImc);
            talla?.addEventListener('input', calcularImc);
            calcularImc();

            function toggleTriageManual() {
                if (triageManual.checked) {
                    triageSelectCont.classList.remove('hidden');
                } else {
                    triageSelectCont.classList.add('hidden');
                }
            }
            triageManual?.addEventListener('change', toggleTriageManual);
            toggleTriageManual();

            function previewTriage() {
                const temp = parseFloat(document.getElementById('temperatura')?.value);
                const fc = parseInt(document.getElementById('frecuencia_cardiaca')?.value);
                const fr = parseInt(document.getElementById('frecuencia_respiratoria')?.value);
                const spo2 = parseInt(document.getElementById('saturacion_oxigeno')?.value);
                const pa = document.getElementById('presion_arterial')?.value;
                const dolor = parseInt(document.getElementById('escala_dolor')?.value);

                let nivel = null;
                let motivo = '';

                if ((spo2 && spo2 < 85) || (fc && fc > 180) || (temp && temp >= 41)) {
                    nivel = 'rojo';
                    motivo = 'Signos críticos';
                } else if ((spo2 && spo2 < 90) || (fc && fc > 130) || (temp && temp >= 39.5) || (dolor >= 9)) {
                    nivel = 'naranja';
                    motivo = 'Muy urgente';
                } else if ((temp && temp >= 38.5) || (dolor >= 6) || (fc && fc > 110)) {
                    nivel = 'amarillo';
                    motivo = 'Urgente';
                } else if (temp || fc || fr || spo2) {
                    nivel = 'verde';
                    motivo = 'No urgente';
                }

                if (!nivel) {
                    triageValor.textContent = '— Sin calcular —';
                    triageDescripcion.textContent = '';
                    triagePreview.className = 'mb-4 p-4 rounded-2xl border-2 border-slate-200 bg-slate-50/70';
                    return;
                }

                const colores = {
                    rojo: {
                        bg: 'bg-rose-50',
                        border: 'border-rose-500',
                        texto: '🔴 Rojo',
                        label: 'Emergencia'
                    },
                    naranja: {
                        bg: 'bg-orange-50',
                        border: 'border-orange-500',
                        texto: '🟠 Naranja',
                        label: 'Muy urgente'
                    },
                    amarillo: {
                        bg: 'bg-amber-50',
                        border: 'border-amber-500',
                        texto: '🟡 Amarillo',
                        label: 'Urgente'
                    },
                    verde: {
                        bg: 'bg-emerald-50',
                        border: 'border-emerald-500',
                        texto: '🟢 Verde',
                        label: 'No urgente'
                    },
                    azul: {
                        bg: 'bg-indigo-50',
                        border: 'border-indigo-500',
                        texto: '🔵 Azul',
                        label: 'Baja prioridad'
                    },
                };
                const c = colores[nivel];
                triagePreview.className = `mb-4 p-4 rounded-2xl border-2 ${c.bg} ${c.border}`;
                triageValor.textContent = c.texto;
                triageDescripcion.textContent = c.label + (motivo ? ' — ' + motivo : '');
            }

            ['temperatura', 'frecuencia_cardiaca', 'frecuencia_respiratoria',
                'saturacion_oxigeno', 'presion_arterial', 'escala_dolor'
            ]
            .forEach(id => document.getElementById(id)?.addEventListener('input', previewTriage));
            previewTriage();

            const paInput = document.getElementById('presion_arterial');
            paInput?.addEventListener('blur', function() {
                this.value = this.value.replace(/\s+/g, '');
            });
        });
    </script>
</x-app-layout>
