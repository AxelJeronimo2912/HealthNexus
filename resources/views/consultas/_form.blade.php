@php
    $c = $consulta ?? null;
    $readonly = $readonly ?? false;

    $inputCls =
        'w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-brand-500 focus:ring-0 outline-none';
    $inputReadonlyCls =
        'w-full bg-slate-100 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-600 outline-none cursor-not-allowed';
    $labelCls = 'block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5';
    $errorCls = 'text-rose-600 text-xs mt-1 font-medium';
    $sectionTitleCls = 'font-extrabold text-slate-800 text-base';
    $sectionSubCls = 'text-xs text-slate-400 mt-0.5';
    $cardCls = 'bg-white border border-slate-200 rounded-2xl shadow-sm p-6';
    $micCls =
        'absolute top-2 right-2 w-8 h-8 flex items-center justify-center bg-white border border-slate-200 rounded-lg hover:bg-brand-50 hover:border-brand-200 text-slate-500 hover:text-brand-600 transition-all text-sm';

    $soapCampos = [
        [
            'name' => 'subjetivo',
            'letra' => 'S',
            'titulo' => 'Subjetivo',
            'sub' => 'Síntomas referidos',
            'color' => 'bg-blue-600',
            'placeholder' => 'Síntomas referidos por el paciente...',
        ],
        [
            'name' => 'objetivo',
            'letra' => 'O',
            'titulo' => 'Objetivo',
            'sub' => 'Exploración física',
            'color' => 'bg-emerald-600',
            'placeholder' => 'Hallazgos de la exploración física...',
        ],
        [
            'name' => 'analisis',
            'letra' => 'A',
            'titulo' => 'Análisis',
            'sub' => 'Diagnóstico',
            'color' => 'bg-amber-500',
            'placeholder' => 'Análisis e interpretación clínica...',
        ],
        [
            'name' => 'plan',
            'letra' => 'P',
            'titulo' => 'Plan',
            'sub' => 'Tratamiento',
            'color' => 'bg-accent-600',
            'placeholder' => 'Plan de tratamiento e indicaciones...',
        ],
    ];
@endphp

{{-- ============ TOM SELECT (autocompletado CIE-10) ============ --}}
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

<style>
    .ts-wrapper.single .ts-control,
    .ts-wrapper .ts-control {
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        background: #f8fafc;
        box-shadow: none;
        padding: 0.55rem 0.9rem;
        min-height: 38px;
        font-size: 0.75rem;
        color: #1e293b;
        transition: background-color 0.15s, border-color 0.15s;
    }

    .ts-wrapper.focus .ts-control {
        background: #ffffff;
        border-color: #4C55C0;
        box-shadow: none;
    }

    .ts-wrapper .ts-control input::placeholder {
        color: #94a3b8;
    }

    .ts-dropdown {
        border-radius: 0.75rem;
        border: 1px solid #e2e8f0;
        font-size: 0.75rem;
        box-shadow: 0 10px 20px rgba(15, 23, 42, 0.08);
        overflow: hidden;
    }

    .ts-dropdown .option {
        padding: 0.5rem 0.9rem;
        color: #334155;
    }

    .ts-dropdown .option.active {
        background-color: #eef2ff;
        color: #2F3488;
    }
</style>

<div class="grid grid-cols-1 xl:grid-cols-12 gap-6">

    {{-- ====================================================== --}}
    {{-- COLUMNA LATERAL: paciente + signos vitales + somatometría --}}
    {{-- ====================================================== --}}
    <aside
        class="xl:col-span-4 2xl:col-span-3 space-y-6 self-start xl:sticky xl:top-4 xl:max-h-[calc(100vh-7rem)] xl:overflow-y-auto xl:pr-1">

        {{-- DATOS DEL PACIENTE --}}
        <section class="bg-brand-50/60 border border-brand-100 rounded-2xl overflow-hidden">
            <div class="h-1.5 bg-gradient-to-r from-brand-600 to-blue-600"></div>
            <div class="p-5">
                <div class="flex items-center gap-3 mb-4">
                    <div
                        class="w-8 h-8 rounded-xl bg-brand-100 flex items-center justify-center text-brand-600 shrink-0">
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

                <div class="grid grid-cols-2 gap-3">
                    <div class="col-span-2 bg-white border border-slate-100 rounded-xl p-3">
                        <p class="{{ $labelCls }} mb-0">Nombre</p>
                        <p class="text-sm font-extrabold text-slate-800 mt-1">{{ $paciente?->nombre_completo ?? '—' }}
                        </p>
                    </div>
                    <div class="bg-white border border-slate-100 rounded-xl p-3">
                        <p class="{{ $labelCls }} mb-0">Edad</p>
                        <p class="text-xs font-extrabold text-slate-800 mt-1">{{ $paciente?->edad ?? '—' }} años</p>
                    </div>
                    <div class="bg-white border border-slate-100 rounded-xl p-3">
                        <p class="{{ $labelCls }} mb-0">Sexo</p>
                        <p class="text-xs font-extrabold text-slate-800 mt-1">{{ ucfirst($paciente?->sexo ?? '—') }}
                        </p>
                    </div>
                    <div class="bg-white border border-slate-100 rounded-xl p-3">
                        <p class="{{ $labelCls }} mb-0">Tipo sanguíneo</p>
                        <p class="text-xs font-extrabold text-slate-800 mt-1">{{ $paciente?->tipo_sanguineo ?? '—' }}
                        </p>
                    </div>
                    <div class="bg-white border border-slate-100 rounded-xl p-3">
                        <p class="{{ $labelCls }} mb-0">Teléfono</p>
                        <p class="text-xs font-extrabold text-slate-800 mt-1">
                            {{ $paciente?->telefono_principal ?? '—' }}</p>
                    </div>
                    <div class="col-span-2 bg-white border border-slate-100 rounded-xl p-3">
                        <p class="{{ $labelCls }} mb-0">CURP</p>
                        <p class="text-xs font-mono font-bold text-slate-700 mt-1">{{ $paciente?->curp ?? '—' }}
                        </p>
                    </div>
                    <div class="col-span-2 bg-rose-50/70 border border-rose-100 rounded-xl p-3">
                        <p class="text-xs font-bold uppercase tracking-wider text-rose-600/70">⚠ Alergias</p>
                        <p class="text-xs font-extrabold text-rose-700 mt-1">{{ $paciente?->alergias ?? 'Ninguna' }}
                        </p>
                    </div>
                    <div class="col-span-2 bg-amber-50/70 border border-amber-100 rounded-xl p-3">
                        <p class="text-xs font-bold uppercase tracking-wider text-amber-600/70">Enf. crónicas</p>
                        <p class="text-xs font-extrabold text-amber-700 mt-1">
                            {{ $paciente?->enfermedades_cronicas ?? 'Ninguna' }}</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- SIGNOS VITALES (SOLO LECTURA) --}}
        <section class="{{ $cardCls }}">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-8 h-8 rounded-xl bg-rose-50 flex items-center justify-center text-rose-500 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="{{ $sectionTitleCls }}">Signos vitales</h3>
                    <p class="{{ $sectionSubCls }}">Último registro (no editables)</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="{{ $labelCls }}">Temp. (°C)</label>
                    <input type="number" step="0.1" name="temperatura" id="temperatura"
                        value="{{ old('temperatura', $c?->temperatura ?? $signo?->temperatura) }}"
                        class="{{ $inputReadonlyCls }}" readonly tabindex="-1">
                </div>
                <div>
                    <label class="{{ $labelCls }}">F. cardíaca (lpm)</label>
                    <input type="number" name="frecuencia_cardiaca" id="frecuencia_cardiaca"
                        value="{{ old('frecuencia_cardiaca', $c?->frecuencia_cardiaca ?? $signo?->frecuencia_cardiaca) }}"
                        class="{{ $inputReadonlyCls }}" readonly tabindex="-1">
                </div>
                <div>
                    <label class="{{ $labelCls }}">F. resp. (rpm)</label>
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
                    <label class="{{ $labelCls }}">Sat. O₂ (%)</label>
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

            <p
                class="mt-4 text-xs font-medium text-amber-700 bg-amber-50/60 border border-amber-100 rounded-xl p-3">
                ℹ️ Se registran en el módulo de signos vitales; aquí solo se muestran como referencia.
            </p>
        </section>

        {{-- SOMATOMETRÍA --}}
        <section class="{{ $cardCls }}">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-8 h-8 rounded-xl bg-brand-50 flex items-center justify-center text-brand-600 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                    </svg>
                </div>
                <div>
                    <h3 class="{{ $sectionTitleCls }}">Somatometría</h3>
                    <p class="{{ $sectionSubCls }}">IMC calculado automáticamente</p>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3">
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
                    <label class="{{ $labelCls }}">IMC</label>
                    <input type="text" id="imc" readonly value="{{ old('imc', $c?->imc) }}"
                        class="{{ $inputReadonlyCls }} font-bold text-center">
                </div>
            </div>
            <p id="imc-estado" class="mt-3 text-xs font-bold text-center"></p>
        </section>
    </aside>

    {{-- ====================================================== --}}
    {{-- COLUMNA PRINCIPAL: SOAP + diagnósticos + receta + notas --}}
    {{-- ====================================================== --}}
    <div class="xl:col-span-8 2xl:col-span-9 space-y-6">

        {{-- SOAP --}}
        <section class="{{ $cardCls }}">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-8 h-8 rounded-xl bg-brand-50 flex items-center justify-center text-brand-600 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
                <div>
                    <h3 class="{{ $sectionTitleCls }}">Nota SOAP</h3>
                    <p class="{{ $sectionSubCls }}">Subjetivo, Objetivo, Análisis y Plan · puedes dictar con 🎤</p>
                </div>
            </div>

            <div class="grid grid-cols-1 2xl:grid-cols-2 gap-5">
                @foreach ($soapCampos as $campo)
                    <div>
                        <div class="flex items-center gap-2.5 mb-2">
                            <span
                                class="h-6 w-6 rounded-md {{ $campo['color'] }} text-white text-xs font-bold flex items-center justify-center">{{ $campo['letra'] }}</span>
                            <label class="text-sm font-bold text-slate-700">{{ $campo['titulo'] }}
                                <span class="text-xs font-medium text-slate-400">·
                                    {{ $campo['sub'] }}</span></label>
                        </div>
                        <div class="relative">
                            <textarea name="{{ $campo['name'] }}" rows="5" data-voz placeholder="{{ $campo['placeholder'] }}"
                                class="{{ $inputCls }} resize-y pr-12">{{ old($campo['name'], $c?->{$campo['name']}) }}</textarea>
                            <button type="button" data-dictar class="{{ $micCls }}"
                                title="Dictar por voz">🎤</button>
                        </div>
                        @error($campo['name'])
                            <p class="{{ $errorCls }}">{{ $message }}</p>
                        @enderror
                    </div>
                @endforeach
            </div>
        </section>

        {{-- DIAGNÓSTICOS --}}
        <section class="{{ $cardCls }}">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-8 h-8 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 shrink-0">
                    🩺
                </div>
                <div>
                    <h3 class="{{ $sectionTitleCls }}">Diagnósticos</h3>
                    <p class="{{ $sectionSubCls }}">Busca por código o nombre CIE-10</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                <div>
                    <label class="{{ $labelCls }}">Diagnóstico principal (CIE-10)</label>
                    <select name="diagnostico_principal_id" id="diag-principal" class="{{ $inputCls }}">
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
                    <select name="diagnostico_secundario_id" id="diag-secundario" class="{{ $inputCls }}">
                        <option value="">— Buscar o seleccionar —</option>
                        @foreach ($diagnosticos as $d)
                            <option value="{{ $d->id }}" @selected(old('diagnostico_secundario_id', $c?->diagnostico_secundario_id) == $d->id)>
                                {{ $d->codigo }} — {{ $d->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('diagnostico_secundario_id')
                        <p class="{{ $errorCls }}">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </section>

        {{-- RECETA --}}
        <section class="{{ $cardCls }} space-y-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div
                        class="w-8 h-8 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600 shrink-0">
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
                <button type="button" onclick="agregarMedicamento()"
                    class="px-4 py-2 bg-brand-600 hover:bg-brand-700 active:scale-95 text-white rounded-xl text-xs font-bold transition-all inline-flex items-center gap-1.5 shadow-sm">
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

            <p id="med-vacio"
                class="hidden text-center text-xs text-slate-400 py-6 border-2 border-dashed border-slate-200 rounded-2xl">
                Aún no hay medicamentos. Pulsa "Agregar medicamento" para comenzar.
            </p>

            <div>
                <label class="{{ $labelCls }}">Receta libre (opcional)</label>
                <div class="relative">
                    <textarea name="receta_libre" rows="3" data-voz placeholder="Si necesitas escribir la receta manualmente..."
                        class="{{ $inputCls }} resize-y pr-12">{{ old('receta_libre', $c?->receta_libre) }}</textarea>
                    <button type="button" data-dictar class="{{ $micCls }}"
                        title="Dictar por voz">🎤</button>
                </div>
            </div>
        </section>

        {{-- NOTAS --}}
        <section class="{{ $cardCls }}">
            <label class="{{ $labelCls }}">Notas adicionales</label>
            <div class="relative">
                <textarea name="notas" rows="3" data-voz class="{{ $inputCls }} resize-y pr-12">{{ old('notas', $c?->notas) }}</textarea>
                <button type="button" data-dictar class="{{ $micCls }}" title="Dictar por voz">🎤</button>
            </div>
        </section>
    </div>
</div>

{{-- Plantilla de fila de medicamento (reutiliza el mismo partial) --}}
<template id="med-template">
    @include('consultas._medicamento_row', [
        'index' => '__INDEX__',
        'med' => [],
        'medicamentos' => $medicamentos,
    ])
</template>

{{-- ============ JS: IMC + medicamentos + dictado por voz + TomSelect ============ --}}
<script>
    let medIndex =
        {{ old('medicamentos')
            ? count(old('medicamentos'))
            : (($c ?? null) && $c->medicamentos
                ? $c->medicamentos->count()
                : 0) }};

    function actualizarVacioMed() {
        const hay = document.querySelectorAll('#medicamentos-container .medicamento-row').length > 0;
        document.getElementById('med-vacio')?.classList.toggle('hidden', hay);
    }

    function agregarMedicamento() {
        const tpl = document.getElementById('med-template').innerHTML.replaceAll('__INDEX__', medIndex);
        document.getElementById('medicamentos-container').insertAdjacentHTML('beforeend', tpl);
        medIndex++;
        actualizarVacioMed();
    }

    document.addEventListener('DOMContentLoaded', function() {

        // ===== Estado vacío de medicamentos (también al eliminar filas) =====
        actualizarVacioMed();
        new MutationObserver(actualizarVacioMed)
            .observe(document.getElementById('medicamentos-container'), {
                childList: true
            });

        // ===== IMC automático + clasificación =====
        const peso = document.getElementById('peso');
        const talla = document.getElementById('talla');
        const imc = document.getElementById('imc');
        const imcEstado = document.getElementById('imc-estado');

        function calcularImc() {
            const p = parseFloat(peso?.value);
            const t = parseFloat(talla?.value);
            if (p > 0 && t > 0) {
                const v = p / (t * t);
                imc.value = v.toFixed(1);
                let txt = 'Obesidad',
                    cls = 'text-red-600';
                if (v < 18.5) {
                    txt = 'Bajo peso';
                    cls = 'text-blue-600';
                } else if (v < 25) {
                    txt = 'Peso normal';
                    cls = 'text-green-600';
                } else if (v < 30) {
                    txt = 'Sobrepeso';
                    cls = 'text-yellow-600';
                }
                imcEstado.textContent = txt;
                imcEstado.className = 'mt-3 text-xs font-bold text-center ' + cls;
            } else {
                imc.value = '';
                imcEstado.textContent = '';
            }
        }
        peso?.addEventListener('input', calcularImc);
        talla?.addEventListener('input', calcularImc);
        calcularImc();

        // ===== Autocompletado de diagnósticos (Tom Select) =====
        if (typeof TomSelect !== 'undefined') {
            const configDiag = {
                placeholder: 'Escribe código o nombre...',
                maxOptions: 300,
                allowEmptyOption: true,
                searchField: ['text'],
                sortField: [{
                    field: '$score'
                }],
                render: {
                    option: (data, escape) => `<div class="py-1">${escape(data.text)}</div>`,
                    item: (data, escape) => `<div>${escape(data.text)}</div>`,
                    no_results: () => '<div class="p-2 text-slate-400 text-xs">Sin resultados</div>'
                }
            };
            new TomSelect('#diag-principal', configDiag);
            new TomSelect('#diag-secundario', configDiag);
        }

        // ===== Dictado por voz =====
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        if (!SpeechRecognition) {
            document.querySelectorAll('[data-dictar]').forEach(btn => btn.style.display = 'none');
            return;
        }

        document.querySelectorAll('[data-dictar]').forEach(btn => {
            const textarea = btn.closest('.relative')?.querySelector('textarea[data-voz]');
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
</script>
