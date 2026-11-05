<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-3">
            <div class="min-w-0">
                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight truncate">
                    Editar Registro
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Actualiza los signos vitales y el triage del paciente</p>
            </div>
            <a href="{{ route('signos-vitales.show', ['signoVital' => $signoVital->id]) }}"
                class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 px-5 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 transition-all active:scale-95 group shrink-0">
                <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
                Volver al detalle
            </a>
        </div>
    </x-slot>

    @php
        $labelCls = 'text-xs font-bold text-slate-500 uppercase tracking-wider mb-1 block';
        $inputCls =
            'w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-800 focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition-all';
        $cardCls = 'bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden';
    @endphp

    <div class="w-full px-4 sm:px-6 lg:px-10 py-8 bg-slate-50 min-h-screen">
        <form id="form-edit-signo" action="{{ route('signos-vitales.update', ['signoVital' => $signoVital->id]) }}"
            method="POST" class="max-w-5xl mx-auto space-y-6">
            @csrf
            @method('PUT')

            {{-- Alerta si el paciente fue eliminado --}}
            @if (!$signoVital->paciente)
                <div
                    class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-2xl text-sm font-semibold flex items-center gap-3 shadow-sm">
                    <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    El paciente original fue eliminado del sistema. Debes reasignar este registro a un paciente válido
                    para continuar.
                </div>
            @endif

            {{-- SECCIÓN 1: Paciente --}}
            <section class="{{ $cardCls }} p-6">
                <div class="flex items-center gap-2 mb-4">
                    <div class="p-2 bg-brand-50 text-brand-600 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <h3 class="font-extrabold text-slate-800 text-base">Paciente</h3>
                </div>

                <div>
                    <label class="{{ $labelCls }}">Seleccionar paciente <span class="text-rose-500">*</span></label>
                    <select name="paciente_id" required class="{{ $inputCls }} cursor-pointer">
                        <option value="" disabled>— Selecciona un paciente —</option>
                        @foreach ($pacientes as $p)
                            <option value="{{ $p->id }}" @selected(old('paciente_id', $signoVital->paciente_id) == $p->id)>
                                {{ $p->nombre_completo }} ({{ $p->curp ?? 'Sin CURP' }})
                            </option>
                        @endforeach
                    </select>
                    @error('paciente_id')
                        <p class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>
            </section>

            {{-- SECCIÓN 2: Signos Vitales --}}
            <section class="{{ $cardCls }} p-6">
                <div class="flex items-center gap-2 mb-4">
                    <div class="p-2 bg-brand-50 text-brand-600 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h3 class="font-extrabold text-slate-800 text-base">Signos Vitales</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label class="{{ $labelCls }}">Temperatura (°C)</label>
                        <input type="number" step="0.1" name="temperatura" id="temperatura"
                            value="{{ old('temperatura', $signoVital->temperatura) }}" class="{{ $inputCls }}">
                    </div>
                    <div>
                        <label class="{{ $labelCls }}">Frec. cardíaca (lpm)</label>
                        <input type="number" name="frecuencia_cardiaca" id="frecuencia_cardiaca"
                            value="{{ old('frecuencia_cardiaca', $signoVital->frecuencia_cardiaca) }}"
                            class="{{ $inputCls }}">
                    </div>
                    <div>
                        <label class="{{ $labelCls }}">Frec. respiratoria (rpm)</label>
                        <input type="number" name="frecuencia_respiratoria" id="frecuencia_respiratoria"
                            value="{{ old('frecuencia_respiratoria', $signoVital->frecuencia_respiratoria) }}"
                            class="{{ $inputCls }}">
                    </div>
                    <div>
                        <label class="{{ $labelCls }}">Presión arterial</label>
                        <input type="text" name="presion_arterial" id="presion_arterial"
                            value="{{ old('presion_arterial', $signoVital->presion_arterial) }}"
                            placeholder="Ej. 120/80" class="{{ $inputCls }}">
                    </div>
                    <div>
                        <label class="{{ $labelCls }}">Saturación O₂ (%)</label>
                        <input type="number" name="saturacion_oxigeno" id="saturacion_oxigeno"
                            value="{{ old('saturacion_oxigeno', $signoVital->saturacion_oxigeno) }}"
                            class="{{ $inputCls }}">
                    </div>
                    <div>
                        <label class="{{ $labelCls }}">Glucosa (mg/dL)</label>
                        <input type="number" name="glucosa" id="glucosa"
                            value="{{ old('glucosa', $signoVital->glucosa) }}" class="{{ $inputCls }}">
                    </div>
                    <div>
                        <label class="{{ $labelCls }}">Peso (kg)</label>
                        <input type="number" step="0.1" name="peso" id="peso"
                            value="{{ old('peso', $signoVital->peso) }}" class="{{ $inputCls }}">
                    </div>
                    <div>
                        <label class="{{ $labelCls }}">Talla (m)</label>
                        <input type="number" step="0.01" name="talla" id="talla"
                            value="{{ old('talla', $signoVital->talla) }}" class="{{ $inputCls }}">
                    </div>
                    <div>
                        <label class="{{ $labelCls }}">Escala de dolor (0-10)</label>
                        <input type="number" min="0" max="10" name="escala_dolor" id="escala_dolor"
                            value="{{ old('escala_dolor', $signoVital->escala_dolor) }}"
                            class="{{ $inputCls }}">
                    </div>
                </div>
            </section>

            {{-- SECCIÓN 3: Triage --}}
            <section class="{{ $cardCls }} p-6">
                <div class="flex items-center gap-2 mb-4">
                    <div class="p-2 bg-brand-50 text-brand-600 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="font-extrabold text-slate-800 text-base">Clasificación de Triage</h3>
                </div>

                <div class="mb-5 p-5 rounded-xl border-2 transition-colors duration-300 {{ $signoVital->triage_color }}"
                    id="triage-preview">
                    <p class="text-xs font-bold uppercase tracking-wider opacity-70 mb-1">Triage actual / estimado:</p>
                    <p class="text-2xl font-black" id="triage-valor">{{ $signoVital->triage_label }}</p>
                    <p class="text-sm font-medium mt-1 opacity-90" id="triage-descripcion">
                        {{ $signoVital->triage_descripcion }}</p>
                </div>

                <label
                    class="inline-flex items-center gap-3 cursor-pointer p-3 bg-slate-50 rounded-xl border border-slate-200 w-full hover:bg-slate-100 transition-colors">
                    <input type="checkbox" name="triage_manual" value="1" id="triage_manual"
                        @checked(old('triage_manual', $signoVital->triage_manual))
                        class="w-5 h-5 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                    <span class="text-sm font-bold text-slate-700">Forzar clasificación manual</span>
                </label>

                <div id="triage-select-container" class="mt-4 {{ $signoVital->triage_manual ? '' : 'hidden' }}">
                    <label class="{{ $labelCls }}">Nivel de triage manual</label>
                    <select name="triage" class="{{ $inputCls }} cursor-pointer">
                        <option value="">— Selecciona un nivel —</option>
                        @foreach (['rojo', 'naranja', 'amarillo', 'verde', 'azul'] as $t)
                            <option value="{{ $t }}" @selected(old('triage', $signoVital->triage) == $t)>
                                {{ ucfirst($t) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </section>

            {{-- SECCIÓN 4: Notas --}}
            <section class="{{ $cardCls }} p-6">
                <div class="flex items-center gap-2 mb-4">
                    <div class="p-2 bg-brand-50 text-brand-600 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                    <h3 class="font-extrabold text-slate-800 text-base">Notas Clínicas</h3>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="{{ $labelCls }}">Motivo de consulta</label>
                        <textarea name="motivo_consulta" rows="3" class="{{ $inputCls }} resize-none">{{ old('motivo_consulta', $signoVital->motivo_consulta) }}</textarea>
                    </div>
                    <div>
                        <label class="{{ $labelCls }}">Notas adicionales</label>
                        <textarea name="notas" rows="3" class="{{ $inputCls }} resize-none">{{ old('notas', $signoVital->notas) }}</textarea>
                    </div>
                </div>
            </section>

            {{-- BOTONES --}}
            <div class="flex justify-end items-center gap-3 pt-2">
                <a href="{{ route('signos-vitales.show', ['signoVital' => $signoVital->id]) }}"
                    class="px-6 py-3 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 rounded-xl text-sm font-bold transition-all active:scale-95">
                    Cancelar
                </a>
                <button type="submit"
                    class="px-6 py-3 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-sm font-bold shadow-sm transition-all active:scale-95 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Actualizar Registro
                </button>
            </div>
        </form>
    </div>

    {{-- ═══════════ MODAL DE CONFIRMACIÓN DE CONTRASEÑA ═══════════ --}}
    <div id="modal-password"
        class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div
            class="bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-md w-full p-6 transform transition-transform">
            <div class="flex items-center gap-3 mb-4 text-rose-600">
                <div class="p-3 bg-rose-100 rounded-full">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-extrabold text-lg text-slate-800">Firma Electrónica</h3>
                    <p class="text-xs text-slate-500">Confirma tu identidad para guardar cambios.</p>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1 block">Contraseña
                        actual</label>
                    <input type="password" id="input-password-confirm" placeholder="••••••••"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition-all">
                    <p id="password-error" class="text-rose-500 text-xs mt-2 font-semibold hidden">La contraseña es
                        incorrecta.</p>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="cerrarModalPassword()"
                    class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition-all">Cancelar</button>
                <button type="button" id="btn-confirmar-password"
                    class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-bold shadow-sm transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Confirmar y Guardar
                </button>
            </div>
        </div>
    </div>

    {{-- Script de Triage Integrado --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- Lógica del Triage ---
            const campos = [
                'temperatura', 'frecuencia_cardiaca', 'frecuencia_respiratoria',
                'presion_arterial', 'saturacion_oxigeno', 'glucosa', 'escala_dolor'
            ];

            const preview = document.getElementById('triage-preview');
            const valor = document.getElementById('triage-valor');
            const descripcion = document.getElementById('triage-descripcion');
            const manualCheck = document.getElementById('triage_manual');
            const selectContainer = document.getElementById('triage-select-container');

            const labels = {
                rojo: {
                    texto: '🔴 Rojo — Emergencia',
                    desc: 'Requiere atención inmediata.',
                    color: 'border-rose-400 bg-rose-50 text-rose-900'
                },
                naranja: {
                    texto: '🟠 Naranja — Muy urgente',
                    desc: 'Debe atenderse rápidamente.',
                    color: 'border-orange-400 bg-orange-50 text-orange-900'
                },
                amarillo: {
                    texto: '🟡 Amarillo — Urgente',
                    desc: 'Urgente, pero puede esperar cierto tiempo.',
                    color: 'border-amber-400 bg-amber-50 text-amber-900'
                },
                verde: {
                    texto: '🟢 Verde — No urgente',
                    desc: 'No urgente. Puede esperar.',
                    color: 'border-emerald-400 bg-emerald-50 text-emerald-900'
                },
                azul: {
                    texto: '🔵 Azul — Baja prioridad',
                    desc: 'Atención de baja prioridad.',
                    color: 'border-blue-400 bg-blue-50 text-blue-900'
                },
            };

            function calcularTriage() {
                const d = {};
                campos.forEach(c => {
                    const el = document.getElementById(c);
                    if (el && el.value !== '') d[c] = el.value;
                });

                let puntos = 0;
                const num = v => parseFloat(v);

                if (d.temperatura !== undefined) {
                    const t = num(d.temperatura);
                    if (t >= 40 || t < 35) puntos += 4;
                    else if (t >= 39 || t < 36) puntos += 2;
                    else if (t >= 38) puntos += 1;
                }
                if (d.frecuencia_cardiaca !== undefined) {
                    const fc = num(d.frecuencia_cardiaca);
                    if (fc > 130 || fc < 40) puntos += 4;
                    else if (fc > 110 || fc < 50) puntos += 2;
                    else if (fc > 100) puntos += 1;
                }
                if (d.frecuencia_respiratoria !== undefined) {
                    const fr = num(d.frecuencia_respiratoria);
                    if (fr > 30 || fr < 8) puntos += 4;
                    else if (fr > 24 || fr < 10) puntos += 2;
                    else if (fr > 20) puntos += 1;
                }
                if (d.saturacion_oxigeno !== undefined) {
                    const s = num(d.saturacion_oxigeno);
                    if (s < 85) puntos += 4;
                    else if (s < 90) puntos += 3;
                    else if (s < 94) puntos += 1;
                }
                if (d.presion_arterial && d.presion_arterial.includes('/')) {
                    const [sis, dia] = d.presion_arterial.split('/').map(num);
                    if (sis >= 180 || sis < 80 || dia >= 120 || dia < 50) puntos += 4;
                    else if (sis >= 160 || sis < 90 || dia >= 100 || dia < 60) puntos += 2;
                    else if (sis >= 140 || dia >= 90) puntos += 1;
                }
                if (d.glucosa !== undefined) {
                    const g = num(d.glucosa);
                    if (g > 400 || g < 50) puntos += 4;
                    else if (g > 250 || g < 70) puntos += 2;
                    else if (g > 180) puntos += 1;
                }
                if (d.escala_dolor !== undefined) {
                    const p = num(d.escala_dolor);
                    if (p >= 8) puntos += 3;
                    else if (p >= 6) puntos += 2;
                    else if (p >= 4) puntos += 1;
                }

                let nivel;
                if (puntos >= 12) nivel = 'rojo';
                else if (puntos >= 8) nivel = 'naranja';
                else if (puntos >= 5) nivel = 'amarillo';
                else if (puntos >= 2) nivel = 'verde';
                else nivel = 'azul';

                if (manualCheck && manualCheck.checked) return;

                const info = labels[nivel];
                valor.textContent = info.texto;
                descripcion.textContent = info.desc;
                preview.className = 'mb-5 p-5 rounded-xl border-2 transition-colors duration-300 ' + info.color;
            }

            campos.forEach(c => {
                const el = document.getElementById(c);
                if (el) el.addEventListener('input', calcularTriage);
            });

            if (manualCheck) {
                manualCheck.addEventListener('change', function() {
                    selectContainer.classList.toggle('hidden', !this.checked);
                    if (!this.checked) calcularTriage();
                });
                if (manualCheck.checked) {
                    selectContainer.classList.remove('hidden');
                }
            }

            calcularTriage();

            // --- Lógica del Modal de Contraseña ---
            const form = document.getElementById('form-edit-signo');
            const modal = document.getElementById('modal-password');
            const inputPassword = document.getElementById('input-password-confirm');
            const btnConfirmar = document.getElementById('btn-confirmar-password');
            const errorText = document.getElementById('password-error');

            let formularioListoParaEnviar = false;

            form.addEventListener('submit', function(e) {
                if (!formularioListoParaEnviar) {
                    e.preventDefault();
                    modal.classList.remove('hidden');
                    inputPassword.value = '';
                    errorText.classList.add('hidden');
                    inputPassword.classList.remove('border-rose-500', 'ring-rose-500/20');
                    inputPassword.focus();
                }
            });

            window.cerrarModalPassword = function() {
                modal.classList.add('hidden');
            };

            btnConfirmar.addEventListener('click', async function() {
                const password = inputPassword.value;
                if (!password) return;

                btnConfirmar.disabled = true;
                btnConfirmar.innerHTML = 'Verificando...';
                errorText.classList.add('hidden');

                try {
                    const response = await fetch('{{ route('signos-vitales.verificar-password') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            password: password
                        })
                    });

                    const data = await response.json();

                    if (response.ok && data.valido) {
                        formularioListoParaEnviar = true;
                        cerrarModalPassword();
                        form.submit();
                    } else {
                        errorText.classList.remove('hidden');
                        inputPassword.classList.add('border-rose-500', 'ring-rose-500/20');
                    }
                } catch (error) {
                    console.error(error);
                    alert('Error de conexión al verificar la contraseña.');
                } finally {
                    btnConfirmar.disabled = false;
                    btnConfirmar.innerHTML = 'Confirmar y Guardar';
                }
            });

            inputPassword.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    btnConfirmar.click();
                }
            });
        });
    </script>
</x-app-layout>
