<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-3">
            <div class="min-w-0">
                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight truncate">
                    Nueva Admisión
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Registro de llegada del paciente al hospital</p>
            </div>
            <a href="{{ route('admisiones.index') }}"
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

    <div class="py-8 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <form action="{{ route('admisiones.store') }}" method="POST"
            class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden" id="form-admision">
            @csrf

            <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-800 text-base">Datos de la admisión</h3>
                    <p class="text-[11px] text-slate-400">Los campos marcados con * son obligatorios</p>
                </div>
            </div>

            <div class="p-6 space-y-6">

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

                {{-- Paciente --}}
                <div>
                    <label class="{{ $labelCls }}">Paciente *</label>
                    <div class="flex gap-2">
                        <select name="paciente_id" id="paciente_id" required class="{{ $inputCls }} flex-1">
                            <option value="">— Selecciona un paciente —</option>
                            @foreach ($pacientes as $p)
                                @php
                                    $signo = $p->signosVitales->first();
                                    $triageLabel = match ($signo?->triage) {
                                        'rojo' => '🔴 Rojo',
                                        'naranja' => '🟠 Naranja',
                                        'amarillo' => '🟡 Amarillo',
                                        'verde' => '🟢 Verde',
                                        'azul' => '🔵 Azul',
                                        default => '— Sin triage',
                                    };
                                @endphp
                                <option value="{{ $p->id }}" data-triage="{{ $signo?->triage ?? '' }}"
                                    data-triage-fecha="{{ $signo?->created_at?->format('d/m/Y') ?? '' }}"
                                    @selected(old('paciente_id') == $p->id)>
                                    {{ $p->nombre_completo }} — {{ $triageLabel }}
                                    @if ($signo)
                                        ({{ $signo->created_at->format('d/m/Y') }})
                                    @endif
                                </option>
                            @endforeach
                        </select>

                        <button type="button" id="btn-nuevo-paciente" title="Registrar paciente nuevo (modo urgencias)"
                            class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white rounded-xl text-xs font-bold whitespace-nowrap shadow-sm transition-all">
                            + Nuevo
                        </button>
                    </div>
                    @error('paciente_id')
                        <p class="{{ $errorCls }}">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Panel triage del paciente --}}
                <div id="panel-triage" class="hidden p-4 rounded-2xl border bg-slate-50/70 border-slate-100">
                    <p class="{{ $labelCls }}">Triage registrado en el último signo vital</p>
                    <p id="triage-actual" class="text-lg font-black text-slate-800 tracking-tight mt-1">—</p>
                    <p id="triage-fecha" class="text-[11px] font-medium text-slate-400"></p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Tipo --}}
                    <div>
                        <label class="{{ $labelCls }}">Tipo de admisión *</label>
                        <select name="tipo" required class="{{ $inputCls }}">
                            <option value="urgencias" @selected(old('tipo') == 'urgencias')>Urgencias</option>
                            <option value="consulta_externa" @selected(old('tipo') == 'consulta_externa')>Consulta Externa</option>
                            <option value="hospitalizacion" @selected(old('tipo') == 'hospitalizacion')>Hospitalización</option>
                            <option value="traslado" @selected(old('tipo') == 'traslado')>Traslado</option>
                        </select>
                    </div>

                    {{-- Triage --}}
                    <div>
                        <label class="{{ $labelCls }}">Triage</label>
                        <select name="triage" id="triage" class="{{ $inputCls }}">
                            <option value="">— Usar triage del último signo vital —</option>
                            <option value="rojo" @selected(old('triage') == 'rojo')>🔴 Rojo — Emergencia</option>
                            <option value="naranja" @selected(old('triage') == 'naranja')>🟠 Naranja — Muy urgente</option>
                            <option value="amarillo" @selected(old('triage') == 'amarillo')>🟡 Amarillo — Urgente</option>
                            <option value="verde" @selected(old('triage') == 'verde')>🟢 Verde — No urgente</option>
                            <option value="azul" @selected(old('triage') == 'azul')>🔵 Azul — Baja prioridad</option>
                        </select>
                        <p class="text-[11px] text-slate-400 mt-1.5">Si no seleccionas nada, se usará el triage del
                            último signo vital.</p>
                    </div>
                </div>

                {{-- Motivo --}}
                <div>
                    <label class="{{ $labelCls }}">Motivo de la admisión *</label>
                    <textarea name="motivo" rows="3" required placeholder="Razón por la que llega el paciente..."
                        class="{{ $inputCls }} resize-none">{{ old('motivo') }}</textarea>
                    @error('motivo')
                        <p class="{{ $errorCls }}">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Diagnóstico --}}
                <div>
                    <label class="{{ $labelCls }}">Diagnóstico presuntivo</label>
                    <textarea name="diagnostico_presuntivo" rows="2" placeholder="Diagnóstico inicial (si se conoce)"
                        class="{{ $inputCls }} resize-none">{{ old('diagnostico_presuntivo') }}</textarea>
                </div>

                {{-- Notas --}}
                <div>
                    <label class="{{ $labelCls }}">Notas adicionales</label>
                    <textarea name="notas" rows="2" class="{{ $inputCls }} resize-none">{{ old('notas') }}</textarea>
                </div>
            </div>

            <div
                class="px-6 py-4 bg-slate-50/70 border-t border-slate-100 flex flex-wrap justify-between items-center gap-3">
                <a href="{{ route('admisiones.index') }}"
                    class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-600 rounded-xl text-xs font-bold transition-all">
                    Cancelar
                </a>
                <button type="submit"
                    class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                    Registrar Admisión
                </button>
            </div>
        </form>
    </div>

    @include('admisiones.partials._modal-paciente-rapido')

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const selectPaciente = document.getElementById('paciente_id');
                const selectTriage = document.getElementById('triage');
                const panelTriage = document.getElementById('panel-triage');
                const triageActual = document.getElementById('triage-actual');
                const triageFecha = document.getElementById('triage-fecha');

                const labels = {
                    rojo: '🔴 Rojo — Emergencia',
                    naranja: '🟠 Naranja — Muy urgente',
                    amarillo: '🟡 Amarillo — Urgente',
                    verde: '🟢 Verde — No urgente',
                    azul: '🔵 Azul — Baja prioridad',
                };

                const colores = {
                    rojo: 'bg-rose-50 text-rose-800 border-rose-200',
                    naranja: 'bg-orange-50 text-orange-800 border-orange-200',
                    amarillo: 'bg-amber-50 text-amber-800 border-amber-200',
                    verde: 'bg-emerald-50 text-emerald-800 border-emerald-200',
                    azul: 'bg-indigo-50 text-indigo-800 border-indigo-200',
                };

                function limpiarColoresPanel() {
                    Object.values(colores).forEach(c => panelTriage.classList.remove(...c.split(' ')));
                }

                function actualizarTriage() {
                    const opt = selectPaciente.options[selectPaciente.selectedIndex];
                    const triage = opt?.dataset?.triage;
                    const fecha = opt?.dataset?.triageFecha;

                    if (!triage) {
                        panelTriage.classList.add('hidden');
                        triageFecha.textContent = '';
                        return;
                    }

                    panelTriage.classList.remove('hidden');
                    triageActual.textContent = labels[triage] || triage;
                    triageFecha.textContent = fecha ? `Registrado el ${fecha}` : '';

                    limpiarColoresPanel();
                    panelTriage.classList.add(...colores[triage].split(' '));

                    if (!selectTriage.dataset.touched) {
                        selectTriage.value = triage;
                    }
                }

                window.actualizarTriageAdmision = actualizarTriage;

                selectTriage.addEventListener('change', function() {
                    this.dataset.touched = '1';
                });

                selectPaciente.addEventListener('change', actualizarTriage);

                if (selectPaciente.value) actualizarTriage();

                document.addEventListener('paciente-rapido:creado', () => {
                    actualizarTriage();
                });
            });
        </script>

        @include('admisiones.partials._scripts-paciente-rapido')
    @endpush
</x-app-layout>
