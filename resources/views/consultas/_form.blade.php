@php $c = $consulta ?? null; @endphp

{{-- DATOS DEL PACIENTE --}}
<section class="bg-blue-50 p-4 rounded-lg">
    <h3 class="text-lg font-bold text-blue-900 mb-3">Datos del paciente</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
        <div>
            <p class="text-gray-500">Nombre</p>
            <p class="font-semibold">{{ $paciente?->nombre_completo ?? '—' }}</p>
        </div>
        <div>
            <p class="text-gray-500">Edad</p>
            <p class="font-semibold">{{ $paciente?->edad ?? '—' }} años</p>
        </div>
        <div>
            <p class="text-gray-500">Sexo</p>
            <p class="font-semibold">{{ ucfirst($paciente?->sexo ?? '—') }}</p>
        </div>
        <div>
            <p class="text-gray-500">Tipo sanguíneo</p>
            <p class="font-semibold">{{ $paciente?->tipo_sanguineo ?? '—' }}</p>
        </div>
        <div>
            <p class="text-gray-500">Alergias</p>
            <p class="font-semibold">{{ $paciente?->alergias ?? 'Ninguna' }}</p>
        </div>
        <div>
            <p class="text-gray-500">Enf. crónicas</p>
            <p class="font-semibold">{{ $paciente?->enfermedades_cronicas ?? 'Ninguna' }}</p>
        </div>
        <div>
            <p class="text-gray-500">CURP</p>
            <p class="font-semibold">{{ $paciente?->curp ?? '—' }}</p>
        </div>
        <div>
            <p class="text-gray-500">Teléfono</p>
            <p class="font-semibold">{{ $paciente?->telefono_principal ?? '—' }}</p>
        </div>
    </div>
</section>

{{-- SIGNOS VITALES --}}
<section>
    <h3 class="text-lg font-bold text-gray-800 mb-3">Signos vitales</h3>
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium">Temperatura (°C)</label>
            <input type="number" step="0.1" name="temperatura" id="temperatura"
                value="{{ old('temperatura', $c?->temperatura ?? $signo?->temperatura) }}"
                class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
        </div>
        <div>
            <label class="block text-sm font-medium">Frec. cardíaca (lpm)</label>
            <input type="number" name="frecuencia_cardiaca" id="frecuencia_cardiaca"
                value="{{ old('frecuencia_cardiaca', $c?->frecuencia_cardiaca ?? $signo?->frecuencia_cardiaca) }}"
                class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
        </div>
        <div>
            <label class="block text-sm font-medium">Frec. respiratoria (rpm)</label>
            <input type="number" name="frecuencia_respiratoria" id="frecuencia_respiratoria"
                value="{{ old('frecuencia_respiratoria', $c?->frecuencia_respiratoria ?? $signo?->frecuencia_respiratoria) }}"
                class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
        </div>
        <div>
            <label class="block text-sm font-medium">Presión arterial</label>
            <input type="text" name="presion_arterial"
                value="{{ old('presion_arterial', $c?->presion_arterial ?? $signo?->presion_arterial) }}"
                placeholder="120/80" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
        </div>
        <div>
            <label class="block text-sm font-medium">Saturación O₂ (%)</label>
            <input type="number" name="saturacion_oxigeno"
                value="{{ old('saturacion_oxigeno', $c?->saturacion_oxigeno ?? $signo?->saturacion_oxigeno) }}"
                class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
        </div>
        <div>
            <label class="block text-sm font-medium">Glucosa (mg/dL)</label>
            <input type="number" name="glucosa" value="{{ old('glucosa', $c?->glucosa ?? $signo?->glucosa) }}"
                class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
        </div>
    </div>
</section>

{{-- SOMATOMETRÍA --}}
<section>
    <h3 class="text-lg font-bold text-gray-800 mb-3">Somatometría</h3>
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium">Peso (kg)</label>
            <input type="number" step="0.01" name="peso" id="peso"
                value="{{ old('peso', $c?->peso ?? $signo?->peso) }}"
                class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
        </div>
        <div>
            <label class="block text-sm font-medium">Talla (m)</label>
            <input type="number" step="0.01" name="talla" id="talla"
                value="{{ old('talla', $c?->talla ?? $signo?->talla) }}"
                class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
        </div>
        <div>
            <label class="block text-sm font-medium">IMC</label>
            <input type="text" id="imc" readonly value="{{ old('imc', $c?->imc) }}"
                class="mt-1 w-full border-gray-300 rounded-md shadow-sm bg-gray-100">
        </div>
    </div>
</section>

<hr>

{{-- SOAP --}}
<section class="space-y-4">
    <h3 class="text-lg font-bold text-gray-800">SOAP</h3>

    <div>
        <label class="block text-sm font-medium">S — Subjetivo (síntomas referidos)</label>
        <div class="relative">
            <textarea name="subjetivo" rows="3" data-voz class="mt-1 w-full border-gray-300 rounded-md shadow-sm pr-12">{{ old('subjetivo', $c?->subjetivo) }}</textarea>
            <button type="button" data-dictar
                class="absolute top-2 right-2 text-xs bg-white border border-gray-300 rounded px-2 py-1 hover:bg-gray-100"
                title="Dictar por voz">🎤</button>
        </div>
    </div>
    <div>
        <label class="block text-sm font-medium">O — Objetivo (exploración física)</label>
        <div class="relative">
            <textarea name="objetivo" rows="3" data-voz class="mt-1 w-full border-gray-300 rounded-md shadow-sm pr-12">{{ old('objetivo', $c?->objetivo) }}</textarea>
            <button type="button" data-dictar
                class="absolute top-2 right-2 text-xs bg-white border border-gray-300 rounded px-2 py-1 hover:bg-gray-100"
                title="Dictar por voz">🎤</button>
        </div>
    </div>
    <div>
        <label class="block text-sm font-medium">A — Análisis (diagnóstico)</label>
        <div class="relative">
            <textarea name="analisis" rows="3" data-voz class="mt-1 w-full border-gray-300 rounded-md shadow-sm pr-12">{{ old('analisis', $c?->analisis) }}</textarea>
            <button type="button" data-dictar
                class="absolute top-2 right-2 text-xs bg-white border border-gray-300 rounded px-2 py-1 hover:bg-gray-100"
                title="Dictar por voz">🎤</button>
        </div>
    </div>
    <div>
        <label class="block text-sm font-medium">P — Plan (tratamiento)</label>
        <div class="relative">
            <textarea name="plan" rows="3" data-voz class="mt-1 w-full border-gray-300 rounded-md shadow-sm pr-12">{{ old('plan', $c?->plan) }}</textarea>
            <button type="button" data-dictar
                class="absolute top-2 right-2 text-xs bg-white border border-gray-300 rounded px-2 py-1 hover:bg-gray-100"
                title="Dictar por voz">🎤</button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium">Diagnóstico principal (CIE-10)</label>
            <select name="diagnostico_principal_id" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                <option value="">— Buscar o seleccionar —</option>
                @foreach ($diagnosticos as $d)
                    <option value="{{ $d->id }}" @selected(old('diagnostico_principal_id', $c?->diagnostico_principal_id) == $d->id)>
                        {{ $d->codigo }} — {{ $d->nombre }}
                    </option>
                @endforeach
            </select>
            @error('diagnostico_principal_id')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-sm font-medium">Diagnóstico secundario (CIE-10)</label>
            <select name="diagnostico_secundario_id" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
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

<hr>

{{-- RECETA --}}
<section class="space-y-4">
    <h3 class="text-lg font-bold text-gray-800">Receta</h3>

    {{-- Medicamentos del catálogo --}}
    <div>
        <div class="flex justify-between items-center mb-2">
            <label class="block text-sm font-medium">Medicamentos del catálogo</label>
            <button type="button" onclick="agregarMedicamento()"
                class="text-sm bg-blue-100 hover:bg-blue-200 text-blue-800 px-3 py-1 rounded">
                + Agregar medicamento
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

    {{-- Receta libre --}}
    <div>
        <label class="block text-sm font-medium">Receta libre (opcional)</label>
        <div class="relative">
            <textarea name="receta_libre" rows="3" data-voz placeholder="Si necesitas escribir la receta manualmente..."
                class="mt-1 w-full border-gray-300 rounded-md shadow-sm pr-12">{{ old('receta_libre', $c?->receta_libre) }}</textarea>
            <button type="button" data-dictar
                class="absolute top-2 right-2 text-xs bg-white border border-gray-300 rounded px-2 py-1 hover:bg-gray-100"
                title="Dictar por voz">🎤</button>
        </div>
    </div>
</section>

{{-- Notas --}}
<section>
    <label class="block text-sm font-medium">Notas adicionales</label>
    <div class="relative">
        <textarea name="notas" rows="2" data-voz class="mt-1 w-full border-gray-300 rounded-md shadow-sm pr-12">{{ old('notas', $c?->notas) }}</textarea>
        <button type="button" data-dictar
            class="absolute top-2 right-2 text-xs bg-white border border-gray-300 rounded px-2 py-1 hover:bg-gray-100"
            title="Dictar por voz">🎤</button>
    </div>
</section>

{{-- JS: IMC automático + agregar/quitar medicamentos + dictado por voz --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ===== IMC automático =====
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

        // ===== Dictado por voz (solo campos con [data-voz]) =====
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

        if (!SpeechRecognition) {
            // Navegador sin soporte: ocultar botones de micrófono
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
                btn.classList.add('bg-red-100', 'border-red-400');
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
                btn.classList.remove('bg-red-100', 'border-red-400');
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
            <div class="medicamento-row grid grid-cols-1 md:grid-cols-6 gap-2 p-3 bg-gray-50 rounded border" data-index="${medIndex}">
                <div class="md:col-span-2">
                    <label class="block text-xs text-gray-500">Medicamento</label>
                    <select name="medicamentos[${medIndex}][id]" class="w-full border-gray-300 rounded-md text-sm" required>
                        <option value="">— Selecciona —</option>
                        @foreach ($medicamentos as $m)
                            <option value="{{ $m->id }}">{{ $m->nombre }} {{ $m->concentracion }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500">Dosis</label>
                    <input type="text" name="medicamentos[${medIndex}][dosis]" class="w-full border-gray-300 rounded-md text-sm" placeholder="500 mg">
                </div>
                <div>
                    <label class="block text-xs text-gray-500">Vía</label>
                    <input type="text" name="medicamentos[${medIndex}][via]" class="w-full border-gray-300 rounded-md text-sm" placeholder="Oral">
                </div>
                <div>
                    <label class="block text-xs text-gray-500">Frecuencia</label>
                    <input type="text" name="medicamentos[${medIndex}][frecuencia]" class="w-full border-gray-300 rounded-md text-sm" placeholder="Cada 8h">
                </div>
                <div>
                    <label class="block text-xs text-gray-500">Duración</label>
                    <input type="text" name="medicamentos[${medIndex}][duracion]" class="w-full border-gray-300 rounded-md text-sm" placeholder="7 días">
                </div>
                <div class="md:col-span-5">
                    <label class="block text-xs text-gray-500">Indicaciones</label>
                    <input type="text" name="medicamentos[${medIndex}][indicaciones]" class="w-full border-gray-300 rounded-md text-sm" placeholder="Tomar después de alimentos">
                </div>
                <div class="flex items-end">
                    <button type="button" onclick="this.closest('.medicamento-row').remove()"
                            class="text-red-600 hover:underline text-sm">Eliminar</button>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
        medIndex++;
    }
</script>
