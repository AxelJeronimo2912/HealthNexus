@php
    $c = $consulta ?? null;
    $readonly = $readonly ?? false;
@endphp

@php
    $inputCls =
        'w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-indigo-500 focus:ring-0 outline-none';
    $inputReadonlyCls =
        'w-full bg-slate-100 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-600 outline-none cursor-not-allowed';
    $labelCls = 'block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5';
    $errorCls = 'text-rose-600 text-xs mt-1 font-medium';
    $sectionTitleCls = 'font-extrabold text-slate-800 text-base';
    $sectionSubCls = 'text-[11px] text-slate-400 mt-0.5';
@endphp

{{-- ============ DATOS DEL PACIENTE ============ --}}
<section class="bg-indigo-50/60 border border-indigo-100 rounded-3xl p-5">
    <div class="flex items-center gap-3 mb-4">
        <div class="w-8 h-8 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-600 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
        </div>
        <div>
            <h3 class="{{ $sectionTitleCls }}">Datos del paciente</h3>
            <p class="{{ $sectionSubCls }}">Información general del expediente</p>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="bg-white border border-slate-100 rounded-2xl p-3">
            <p class="{{ $labelCls }}">Nombre</p>
            <p class="text-xs font-extrabold text-slate-800 mt-1">{{ $paciente?->nombre_completo ?? '—' }}</p>
        </div>
        <div class="bg-white border border-slate-100 rounded-2xl p-3">
            <p class="{{ $labelCls }}">Edad</p>
            <p class="text-xs font-extrabold text-slate-800 mt-1">{{ $paciente?->edad ?? '—' }} años</p>
        </div>
        <div class="bg-white border border-slate-100 rounded-2xl p-3">
            <p class="{{ $labelCls }}">Sexo</p>
            <p class="text-xs font-extrabold text-slate-800 mt-1">{{ ucfirst($paciente?->sexo ?? '—') }}</p>
        </div>
        <div class="bg-white border border-slate-100 rounded-2xl p-3">
            <p class="{{ $labelCls }}">Tipo sanguíneo</p>
            <p class="text-xs font-extrabold text-slate-800 mt-1">{{ $paciente?->tipo_sanguineo ?? '—' }}</p>
        </div>
        <div class="bg-rose-50/70 border border-rose-100 rounded-2xl p-3">
            <p class="text-[11px] font-bold uppercase tracking-wider text-rose-600/70">Alergias</p>
            <p class="text-xs font-extrabold text-rose-700 mt-1">{{ $paciente?->alergias ?? 'Ninguna' }}</p>
        </div>
        <div class="bg-amber-50/70 border border-amber-100 rounded-2xl p-3">
            <p class="text-[11px] font-bold uppercase tracking-wider text-amber-600/70">Enf. crónicas</p>
            <p class="text-xs font-extrabold text-amber-700 mt-1">{{ $paciente?->enfermedades_cronicas ?? 'Ninguna' }}
            </p>
        </div>
        <div class="bg-white border border-slate-100 rounded-2xl p-3">
            <p class="{{ $labelCls }}">CURP</p>
            <p class="text-[11px] font-mono font-bold text-slate-700 mt-1">{{ $paciente?->curp ?? '—' }}</p>
        </div>
        <div class="bg-white border border-slate-100 rounded-2xl p-3">
            <p class="{{ $labelCls }}">Teléfono</p>
            <p class="text-xs font-extrabold text-slate-800 mt-1">{{ $paciente?->telefono_principal ?? '—' }}</p>
        </div>
    </div>
</section>

{{-- ============ SIGNOS VITALES (SOLO LECTURA) ============ --}}
<section>
    <div class="flex items-center gap-3 mb-4">
        <div class="w-8 h-8 rounded-xl bg-rose-50 flex items-center justify-center text-rose-500 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
        </div>
        <div>
            <h3 class="{{ $sectionTitleCls }}">Signos vitales</h3>
            <p class="{{ $sectionSubCls }}">Valores registrados en el último signo vital (no editables)</p>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        <div>
            <label class="{{ $labelCls }}">Temperatura (°C)</label>
            <input type="number" step="0.1" name="temperatura" id="temperatura"
                value="{{ old('temperatura', $c?->temperatura ?? $signo?->temperatura) }}"
                class="{{ $inputReadonlyCls }}" readonly tabindex="-1">
        </div>
        <div>
            <label class="{{ $labelCls }}">Frec. cardíaca (lpm)</label>
            <input type="number" name="frecuencia_cardiaca" id="frecuencia_cardiaca"
                value="{{ old('frecuencia_cardiaca', $c?->frecuencia_cardiaca ?? $signo?->frecuencia_cardiaca) }}"
                class="{{ $inputReadonlyCls }}" readonly tabindex="-1">
        </div>
        <div>
            <label class="{{ $labelCls }}">Frec. respiratoria (rpm)</label>
            <input type="number" name="frecuencia_respiratoria" id="frecuencia_respiratoria"
                value="{{ old('frecuencia_respiratoria', $c?->frecuencia_respiratoria ?? $signo?->frecuencia_respiratoria) }}"
                class="{{ $inputReadonlyCls }}" readonly tabindex="-1">
        </div>
        <div>
            <label class="{{ $labelCls }}">Presión arterial</label>
            <input type="text" name="presion_arterial"
                value="{{ old('presion_arterial', $c?->presion_arterial ?? $signo?->presion_arterial) }}"
                placeholder="120/80" class="{{ $inputReadonlyCls }}" readonly tabindex="-1">
        </div>
        <div>
            <label class="{{ $labelCls }}">Saturación O₂ (%)</label>
            <input type="number" name="saturacion_oxigeno"
                value="{{ old('saturacion_oxigeno', $c?->saturacion_oxigeno ?? $signo?->saturacion_oxigeno) }}"
                class="{{ $inputReadonlyCls }}" readonly tabindex="-1">
        </div>
        <div>
            <label class="{{ $labelCls }}">Glucosa (mg/dL)</label>
            <input type="number" name="glucosa" value="{{ old('glucosa', $c?->glucosa ?? $signo?->glucosa) }}"
                class="{{ $inputReadonlyCls }}" readonly tabindex="-1">
        </div>
    </div>

    <div class="mt-3 p-3 bg-amber-50/60 border border-amber-100 rounded-2xl flex items-start gap-2">
        <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <p class="text-[11px] font-medium text-amber-700">
            Los signos vitales se registran por separado en el módulo de signos vitales. Esta vista solo los muestra
            como referencia clínica.
        </p>
    </div>
</section>

{{-- ============ SOMATOMETRÍA ============ --}}
<section>
    <div class="flex items-center gap-3 mb-4">
        <div class="w-8 h-8 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                    d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
            </svg>
        </div>
        <div>
            <h3 class="{{ $sectionTitleCls }}">Somatometría</h3>
            <p class="{{ $sectionSubCls }}">Peso, talla e IMC calculado automáticamente</p>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        <div>
            <label class="{{ $labelCls }}">Peso (kg)</label>
            <input type="number" step="0.01" name="peso" id="peso"
                value="{{ old('peso', $c?->peso ?? $signo?->peso) }}" class="{{ $inputCls }}">
        </div>
        <div>
            <label class="{{ $labelCls }}">Talla (m)</label>
            <input type="number" step="0.01" name="talla" id="talla"
                value="{{ old('talla', $c?->talla ?? $signo?->talla) }}" class="{{ $inputCls }}">
        </div>
        <div>
            <label class="{{ $labelCls }}">IMC (calculado)</label>
            <input type="text" id="imc" readonly value="{{ old('imc', $c?->imc) }}"
                class="{{ $inputReadonlyCls }}">
        </div>
    </div>
</section>

<div class="border-t border-slate-100"></div>

{{-- ============ SOAP ============ --}}
<section class="space-y-5">
    <div class="flex items-center gap-3">
        <div class="w-8 h-8 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
        </div>
        <div>
            <h3 class="{{ $sectionTitleCls }}">Nota SOAP</h3>
            <p class="{{ $sectionSubCls }}">Subjetivo, Objetivo, Análisis y Plan</p>
        </div>
    </div>

    @foreach ([['name' => 'subjetivo', 'label' => 'S — Subjetivo (síntomas referidos)', 'placeholder' => 'Síntomas referidos por el paciente...'], ['name' => 'objetivo', 'label' => 'O — Objetivo (exploración física)', 'placeholder' => 'Hallazgos de la exploración física...'], ['name' => 'analisis', 'label' => 'A — Análisis (diagnóstico)', 'placeholder' => 'Análisis e interpretación clínica...'], ['name' => 'plan', 'label' => 'P — Plan (tratamiento)', 'placeholder' => 'Plan de tratamiento e indicaciones...']] as $campo)
        <div>
            <label class="{{ $labelCls }}">{{ $campo['label'] }}</label>
            <div class="relative">
                <textarea name="{{ $campo['name'] }}" rows="3" data-voz placeholder="{{ $campo['placeholder'] }}"
                    class="{{ $inputCls }} resize-none pr-12">{{ old($campo['name'], $c?->{$campo['name']}) }}</textarea>
                <button type="button" data-dictar
                    class="absolute top-2 right-2 w-8 h-8 flex items-center justify-center bg-white border border-slate-200 rounded-lg hover:bg-indigo-50 hover:border-indigo-200 text-slate-500 hover:text-indigo-600 transition-all text-sm"
                    title="Dictar por voz">🎤</button>
            </div>
        </div>
    @endforeach

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="{{ $labelCls }}">Diagnóstico principal (CIE-10)</label>
            <select name="diagnostico_principal_id" class="{{ $inputCls }}">
                <option value="">— Buscar o seleccionar —</option>
                @foreach ($diagnosticos as $d)
                    <option value="{{ $d->id }}" @selected(old('diagnostico_principal_id', $c?->diagnostico_principal_id) == $d->id)>
                        {{ $d->codigo }} — {{ $d->nombre }}
                    </option>
                @endforeach
            </select>
            @error('diagnostico_principal_id')
                <p class="{{ $errorCls }}">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="{{ $labelCls }}">Diagnóstico secundario (CIE-10)</label>
            <select name="diagnostico_secundario_id" class="{{ $inputCls }}">
                <option value="">— Buscar o seleccionar —</option>
                @foreach ($diagnosticos as $d)
                    <option value="{{ $d->id }}" @selected(old('diagnostico_secundario_id', $c?->diagnostico_secundario_id) == $d->id)>
                        {{ $d->codigo }} — {{ $d->nombre }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</section>

<div class="border-t border-slate-100"></div>

{{-- ============ RECETA ============ --}}
<section class="space-y-5">
    <div class="flex items-center gap-3">
        <div class="w-8 h-8 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                    d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
            </svg>
        </div>
        <div>
            <h3 class="{{ $sectionTitleCls }}">Receta</h3>
            <p class="{{ $sectionSubCls }}">Medicamentos del catálogo y receta libre</p>
        </div>
    </div>

    <div>
        <div class="flex flex-wrap justify-between items-center gap-2 mb-3">
            <label class="{{ $labelCls }} mb-0">Medicamentos del catálogo</label>
            <button type="button" onclick="agregarMedicamento()"
                class="px-3.5 py-2 bg-indigo-50 hover:bg-indigo-100 active:scale-95 text-indigo-700 border border-indigo-100 rounded-xl text-[11px] font-bold transition-all inline-flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                Agregar medicamento
            </button>
        </div>

        <div id="medicamentos-container" class="space-y-3">
            @if (old('medicamentos'))
                @foreach (old('medicamentos') as $i => $m)
                    @include('consultas._medicamento_row', [
                        'index' => $i,
                        'med' => $m,
                        'medicamentos' => $medicamentos,
                    ])
                @endforeach
            @elseif ($c && $c->medicamentos && $c->medicamentos->count())
                @foreach ($c->medicamentos as $i => $med)
                    @include('consultas._medicamento_row', [
                        'index' => $i,
                        'med' => [
                            'id' => $med->id,
                            'dosis' => $med->pivot->dosis,
                            'via' => $med->pivot->via,
                            'frecuencia' => $med->pivot->frecuencia,
                            'duracion' => $med->pivot->duracion,
                            'indicaciones' => $med->pivot->indicaciones,
                        ],
                        'medicamentos' => $medicamentos,
                    ])
                @endforeach
            @endif
        </div>
    </div>

    <div>
        <label class="{{ $labelCls }}">Receta libre (opcional)</label>
        <div class="relative">
            <textarea name="receta_libre" rows="3" data-voz placeholder="Si necesitas escribir la receta manualmente..."
                class="{{ $inputCls }} resize-none pr-12">{{ old('receta_libre', $c?->receta_libre) }}</textarea>
            <button type="button" data-dictar
                class="absolute top-2 right-2 w-8 h-8 flex items-center justify-center bg-white border border-slate-200 rounded-lg hover:bg-indigo-50 hover:border-indigo-200 text-slate-500 hover:text-indigo-600 transition-all text-sm"
                title="Dictar por voz">🎤</button>
        </div>
    </div>
</section>

{{-- ============ NOTAS ============ --}}
<section>
    <label class="{{ $labelCls }}">Notas adicionales</label>
    <div class="relative">
        <textarea name="notas" rows="2" data-voz class="{{ $inputCls }} resize-none pr-12">{{ old('notas', $c?->notas) }}</textarea>
        <button type="button" data-dictar
            class="absolute top-2 right-2 w-8 h-8 flex items-center justify-center bg-white border border-slate-200 rounded-lg hover:bg-indigo-50 hover:border-indigo-200 text-slate-500 hover:text-indigo-600 transition-all text-sm"
            title="Dictar por voz">🎤</button>
    </div>
</section>

{{-- ============ JS: IMC + agregar medicamentos + dictado por voz ============ --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // IMC automático
        const peso = document.getElementById('peso');
        const talla = document.getElementById('talla');
        const imc = document.getElementById('imc');

        function calcularImc() {
            const p = parseFloat(peso?.value);
            const t = parseFloat(talla?.value);
            if (p > 0 && t > 0) {
                imc.value = (p / (t * t)).toFixed(1);
            } else if (imc) {
                imc.value = '';
            }
        }
        if (peso) peso.addEventListener('input', calcularImc);
        if (talla) talla.addEventListener('input', calcularImc);
        calcularImc();

        // Dictado por voz
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        if (!SpeechRecognition) {
            document.querySelectorAll('[data-dictar]').forEach(btn => btn.style.display = 'none');
            return;
        }

        document.querySelectorAll('[data-dictar]').forEach(btn => {
            const wrapper = btn.closest('.relative');
            const textarea = wrapper?.querySelector('textarea[data-voz]');
            if (!textarea) return;

            const recognition = new SpeechRecognition();
            recognition.lang = 'es-MX';
            recognition.continuous = true;
            recognition.interimResults = false;

            let escuchando = false;
            let textoBase = '';

            recognition.onstart = () => {
                escuchando = true;
                textoBase = textarea.value ? textarea.value.trim() + ' ' : '';
                btn.textContent = '⏹️';
                btn.classList.add('bg-rose-50', 'border-rose-200', 'text-rose-600');
                btn.title = 'Detener dictado';
            };

            recognition.onresult = (event) => {
                let transcripcion = '';
                for (let i = event.resultIndex; i < event.results.length; i++) {
                    if (event.results[i].isFinal) {
                        transcripcion += event.results[i][0].transcript;
                    }
                }
                if (transcripcion) {
                    textarea.value = (textoBase + transcripcion).trim();
                    textoBase = textarea.value + ' ';
                }
            };

            recognition.onerror = (e) => {
                console.warn('Error de dictado:', e.error);
                if (e.error === 'not-allowed') {
                    alert('Permiso de micrófono denegado. Habilítalo en tu navegador.');
                }
            };

            recognition.onend = () => {
                escuchando = false;
                btn.textContent = '🎤';
                btn.classList.remove('bg-rose-50', 'border-rose-200', 'text-rose-600');
                btn.title = 'Dictar por voz';
            };

            btn.addEventListener('click', () => {
                if (escuchando) {
                    recognition.stop();
                } else {
                    try {
                        recognition.start();
                    } catch (err) {
                        console.warn(err);
                    }
                }
            });
        });
    });

    let medIndex =
        {{ old('medicamentos')
            ? count(old('medicamentos'))
            : (($c ?? null) && $c->medicamentos
                ? $c->medicamentos->count()
                : 0) }};

    function agregarMedicamento() {
        const container = document.getElementById('medicamentos-container');
        const html = `
            <div class="medicamento-row grid grid-cols-1 md:grid-cols-6 gap-3 p-4 bg-slate-50/70 border border-slate-100 rounded-2xl" data-index="${medIndex}">
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Medicamento</label>
                    <select name="medicamentos[${medIndex}][id]" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-800 focus:border-indigo-500 focus:ring-0 outline-none" required>
                        <option value="">— Selecciona —</option>
                        @foreach ($medicamentos as $m)
                            <option value="{{ $m->id }}">{{ $m->nombre }} {{ $m->concentracion }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Dosis</label>
                    <input type="text" name="medicamentos[${medIndex}][dosis]" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-800 focus:border-indigo-500 focus:ring-0 outline-none" placeholder="500 mg">
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Vía</label>
                    <input type="text" name="medicamentos[${medIndex}][via]" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-800 focus:border-indigo-500 focus:ring-0 outline-none" placeholder="Oral">
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Frecuencia</label>
                    <input type="text" name="medicamentos[${medIndex}][frecuencia]" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-800 focus:border-indigo-500 focus:ring-0 outline-none" placeholder="Cada 8h">
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Duración</label>
                    <input type="text" name="medicamentos[${medIndex}][duracion]" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-800 focus:border-indigo-500 focus:ring-0 outline-none" placeholder="7 días">
                </div>
                <div class="md:col-span-5">
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Indicaciones</label>
                    <input type="text" name="medicamentos[${medIndex}][indicaciones]" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-800 focus:border-indigo-500 focus:ring-0 outline-none" placeholder="Tomar después de alimentos">
                </div>
                <div class="flex items-end">
                    <button type="button" onclick="this.closest('.medicamento-row').remove()"
                            class="px-3 py-2 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-xl text-[11px] font-bold transition-all">
                        Eliminar
                    </button>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
        medIndex++;
    }
</script>
