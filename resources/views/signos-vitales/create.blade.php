<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Nuevo Registro de Signos Vitales</h2>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto sm:px-6 lg:px-8">

        <form action="{{ route('signos-vitales.store') }}" method="POST" class="space-y-6 bg-white p-6 rounded-lg shadow"
            id="form-signos">
            @csrf

            {{-- ====== ALERTAS DE SESIÓN ====== --}}
            @if (session('warning'))
                <div class="p-4 bg-yellow-50 border-l-4 border-yellow-500 rounded">
                    <p class="font-semibold text-yellow-800">⚠️ Atención</p>
                    <p class="text-sm text-yellow-700">{{ session('warning') }}</p>
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 bg-red-50 border-l-4 border-red-500 rounded">
                    <p class="font-semibold text-red-800">❌ Error</p>
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            @endif

            {{-- ====== BLOQUE GENERAL DE ERRORES DE VALIDACIÓN ====== --}}
            @if ($errors->any())
                <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg">
                    <p class="font-semibold mb-2">Corrige los siguientes errores:</p>
                    <ul class="list-disc list-inside text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (request('cita_id'))
                <input type="hidden" name="cita_id" value="{{ request('cita_id') }}">
            @endif

            {{-- ====== PACIENTE ====== --}}
            <div>
                <label class="block text-sm font-medium">Paciente *</label>
                <select name="paciente_id" required
                    class="mt-1 w-full border-gray-300 rounded-md shadow-sm @error('paciente_id') border-red-500 @enderror">
                    <option value="">— Selecciona un paciente —</option>
                    @foreach ($pacientes as $p)
                        <option value="{{ $p->id }}" @selected(old('paciente_id', $pacienteSeleccionado?->id ?? '') == $p->id)>
                            {{ $p->nombre_completo }}
                        </option>
                    @endforeach
                </select>
                @error('paciente_id')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <hr>

            {{-- ====== SIGNOS VITALES ====== --}}
            <div>
                <h3 class="text-lg font-bold text-gray-800 mb-1">Signos vitales</h3>
                <p class="text-sm text-gray-500 mb-4">
                    Ingresa los valores. El triage se calculará automáticamente.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                    {{-- Temperatura --}}
                    <div>
                        <label class="block text-sm font-medium">Temperatura (°C)</label>
                        <input type="number" step="0.1" name="temperatura" id="temperatura"
                            value="{{ old('temperatura') }}" placeholder="36.5"
                            class="mt-1 w-full border-gray-300 rounded-md shadow-sm @error('temperatura') border-red-500 @enderror">
                        @error('temperatura')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Frecuencia cardíaca --}}
                    <div>
                        <label class="block text-sm font-medium">Frec. cardíaca (lpm)</label>
                        <input type="number" name="frecuencia_cardiaca" id="frecuencia_cardiaca"
                            value="{{ old('frecuencia_cardiaca') }}" placeholder="80"
                            class="mt-1 w-full border-gray-300 rounded-md shadow-sm @error('frecuencia_cardiaca') border-red-500 @enderror">
                        @error('frecuencia_cardiaca')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Frecuencia respiratoria --}}
                    <div>
                        <label class="block text-sm font-medium">Frec. respiratoria (rpm)</label>
                        <input type="number" name="frecuencia_respiratoria" id="frecuencia_respiratoria"
                            value="{{ old('frecuencia_respiratoria') }}" placeholder="18"
                            class="mt-1 w-full border-gray-300 rounded-md shadow-sm @error('frecuencia_respiratoria') border-red-500 @enderror">
                        @error('frecuencia_respiratoria')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Presión arterial --}}
                    <div>
                        <label class="block text-sm font-medium">Presión arterial</label>
                        <input type="text" name="presion_arterial" id="presion_arterial"
                            value="{{ old('presion_arterial') }}" placeholder="120/80"
                            class="mt-1 w-full border-gray-300 rounded-md shadow-sm @error('presion_arterial') border-red-500 @enderror">
                        @error('presion_arterial')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Saturación --}}
                    <div>
                        <label class="block text-sm font-medium">Saturación O₂ (%)</label>
                        <input type="number" name="saturacion_oxigeno" id="saturacion_oxigeno"
                            value="{{ old('saturacion_oxigeno') }}" placeholder="98"
                            class="mt-1 w-full border-gray-300 rounded-md shadow-sm @error('saturacion_oxigeno') border-red-500 @enderror">
                        @error('saturacion_oxigeno')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Glucosa --}}
                    <div>
                        <label class="block text-sm font-medium">Glucosa (mg/dL)</label>
                        <input type="number" name="glucosa" id="glucosa" value="{{ old('glucosa') }}"
                            placeholder="100"
                            class="mt-1 w-full border-gray-300 rounded-md shadow-sm @error('glucosa') border-red-500 @enderror">
                        @error('glucosa')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Peso --}}
                    <div>
                        <label class="block text-sm font-medium">Peso (kg)</label>
                        <input type="number" step="0.1" name="peso" id="peso" value="{{ old('peso') }}"
                            placeholder="70"
                            class="mt-1 w-full border-gray-300 rounded-md shadow-sm @error('peso') border-red-500 @enderror">
                        @error('peso')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Talla --}}
                    <div>
                        <label class="block text-sm font-medium">Talla (m)</label>
                        <input type="number" step="0.01" name="talla" id="talla" value="{{ old('talla') }}"
                            placeholder="1.70"
                            class="mt-1 w-full border-gray-300 rounded-md shadow-sm @error('talla') border-red-500 @enderror">
                        @error('talla')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- IMC --}}
                    <div>
                        <label class="block text-sm font-medium">IMC (calculado)</label>
                        <input type="text" id="imc" readonly
                            class="mt-1 w-full border-gray-300 rounded-md shadow-sm bg-gray-100">
                        <p class="text-xs text-gray-500 mt-1" id="imc-clasificacion"></p>
                    </div>

                    {{-- Escala de dolor --}}
                    <div>
                        <label class="block text-sm font-medium">Escala de dolor (0-10)</label>
                        <input type="number" min="0" max="10" name="escala_dolor" id="escala_dolor"
                            value="{{ old('escala_dolor') }}" placeholder="0"
                            class="mt-1 w-full border-gray-300 rounded-md shadow-sm @error('escala_dolor') border-red-500 @enderror">
                        @error('escala_dolor')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <hr>

            {{-- ====== TRIAGE ====== --}}
            <div>
                <h3 class="text-lg font-bold text-gray-800 mb-1">Triage</h3>
                <p class="text-sm text-gray-500 mb-4">
                    Calculado automáticamente. Puedes forzarlo manualmente si lo necesitas.
                </p>

                <div class="mb-4 p-4 rounded border-2" id="triage-preview">
                    <p class="text-sm text-gray-500">Triage estimado:</p>
                    <p class="text-lg font-bold" id="triage-valor">— Sin calcular —</p>
                    <p class="text-sm text-gray-600" id="triage-descripcion"></p>
                </div>

                <label class="inline-flex items-center">
                    <input type="checkbox" name="triage_manual" value="1" id="triage_manual"
                        @checked(old('triage_manual')) class="rounded border-gray-300 text-blue-600 shadow-sm">
                    <span class="ml-2 text-sm">Forzar clasificación manual</span>
                </label>

                <div id="triage-select-container" class="mt-3 {{ old('triage_manual') ? '' : 'hidden' }}">
                    <label class="block text-sm font-medium">Nivel de triage manual</label>
                    <select name="triage"
                        class="mt-1 w-full border-gray-300 rounded-md shadow-sm @error('triage') border-red-500 @enderror">
                        <option value="">— Selecciona —</option>
                        <option value="rojo" @selected(old('triage') == 'rojo')>🔴 Rojo — Emergencia</option>
                        <option value="naranja" @selected(old('triage') == 'naranja')>🟠 Naranja — Muy urgente</option>
                        <option value="amarillo" @selected(old('triage') == 'amarillo')>🟡 Amarillo — Urgente</option>
                        <option value="verde" @selected(old('triage') == 'verde')>🟢 Verde — No urgente</option>
                        <option value="azul" @selected(old('triage') == 'azul')>🔵 Azul — Baja prioridad</option>
                    </select>
                    @error('triage')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <hr>

            {{-- ====== MOTIVO Y NOTAS ====== --}}
            <div>
                <label class="block text-sm font-medium">Motivo de consulta</label>
                <textarea name="motivo_consulta" rows="2"
                    class="mt-1 w-full border-gray-300 rounded-md shadow-sm @error('motivo_consulta') border-red-500 @enderror">{{ old('motivo_consulta') }}</textarea>
                @error('motivo_consulta')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Notas</label>
                <textarea name="notas" rows="2"
                    class="mt-1 w-full border-gray-300 rounded-md shadow-sm @error('notas') border-red-500 @enderror">{{ old('notas') }}</textarea>
                @error('notas')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- ====== ACCIONES ====== --}}
            <div class="flex justify-between items-center pt-4">
                <a href="{{ route('signos-vitales.index') }}"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">Cancelar</a>
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium">
                    Guardar Registro
                </button>
            </div>
        </form>
    </div>

    {{-- ====== SCRIPT INLINE ====== --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ===== Referencias =====
            const peso = document.getElementById('peso');
            const talla = document.getElementById('talla');
            const imcInput = document.getElementById('imc');
            const imcClasificacion = document.getElementById('imc-clasificacion');

            const triageManual = document.getElementById('triage_manual');
            const triageSelectCont = document.getElementById('triage-select-container');
            const triagePreview = document.getElementById('triage-preview');
            const triageValor = document.getElementById('triage-valor');
            const triageDescripcion = document.getElementById('triage-descripcion');

            // ===== IMC automático =====
            function calcularImc() {
                const p = parseFloat(peso?.value);
                const t = parseFloat(talla?.value);
                if (p > 0 && t > 0) {
                    const imc = p / (t * t);
                    imcInput.value = imc.toFixed(1);

                    let clasif = '';
                    let color = 'text-gray-500';
                    if (imc < 18.5) {
                        clasif = 'Bajo peso';
                        color = 'text-yellow-600';
                    } else if (imc < 25) {
                        clasif = 'Normal';
                        color = 'text-green-600';
                    } else if (imc < 30) {
                        clasif = 'Sobrepeso';
                        color = 'text-yellow-600';
                    } else if (imc < 35) {
                        clasif = 'Obesidad I';
                        color = 'text-orange-600';
                    } else if (imc < 40) {
                        clasif = 'Obesidad II';
                        color = 'text-red-600';
                    } else {
                        clasif = 'Obesidad III';
                        color = 'text-red-700';
                    }

                    if (imcClasificacion) {
                        imcClasificacion.textContent = clasif;
                        imcClasificacion.className = 'text-xs mt-1 ' + color;
                    }
                } else {
                    imcInput.value = '';
                    if (imcClasificacion) imcClasificacion.textContent = '';
                }
            }
            peso?.addEventListener('input', calcularImc);
            talla?.addEventListener('input', calcularImc);
            calcularImc();

            // ===== Mostrar/ocultar select de triage manual =====
            function toggleTriageManual() {
                if (triageManual.checked) {
                    triageSelectCont.classList.remove('hidden');
                } else {
                    triageSelectCont.classList.add('hidden');
                }
            }
            triageManual?.addEventListener('change', toggleTriageManual);
            toggleTriageManual();

            // ===== Preview de triage automático (cliente, solo referencial) =====
            // NOTA: el cálculo real lo hace TriageService en el backend.
            function previewTriage() {
                const temp = parseFloat(document.getElementById('temperatura')?.value);
                const fc = parseInt(document.getElementById('frecuencia_cardiaca')?.value);
                const fr = parseInt(document.getElementById('frecuencia_respiratoria')?.value);
                const spo2 = parseInt(document.getElementById('saturacion_oxigeno')?.value);
                const pa = document.getElementById('presion_arterial')?.value;
                const dolor = parseInt(document.getElementById('escala_dolor')?.value);

                let nivel = null;
                let motivo = '';

                // Reglas simples de ejemplo (ajusta a tu TriageService)
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
                    triagePreview.className = 'mb-4 p-4 rounded border-2 border-gray-200';
                    return;
                }

                const colores = {
                    rojo: {
                        bg: 'bg-red-50',
                        border: 'border-red-500',
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
                        bg: 'bg-yellow-50',
                        border: 'border-yellow-500',
                        texto: '🟡 Amarillo',
                        label: 'Urgente'
                    },
                    verde: {
                        bg: 'bg-green-50',
                        border: 'border-green-500',
                        texto: '🟢 Verde',
                        label: 'No urgente'
                    },
                    azul: {
                        bg: 'bg-blue-50',
                        border: 'border-blue-500',
                        texto: '🔵 Azul',
                        label: 'Baja prioridad'
                    },
                };
                const c = colores[nivel];
                triagePreview.className = `mb-4 p-4 rounded border-2 ${c.bg} ${c.border}`;
                triageValor.textContent = c.texto;
                triageDescripcion.textContent = c.label + (motivo ? ' — ' + motivo : '');
            }

            ['temperatura', 'frecuencia_cardiaca', 'frecuencia_respiratoria',
                'saturacion_oxigeno', 'presion_arterial', 'escala_dolor'
            ]
            .forEach(id => document.getElementById(id)?.addEventListener('input', previewTriage));
            previewTriage();

            // ===== Normalizar presión arterial (quitar espacios) =====
            const paInput = document.getElementById('presion_arterial');
            paInput?.addEventListener('blur', function() {
                this.value = this.value.replace(/\s+/g, '');
            });
        });
    </script>
</x-app-layout>
