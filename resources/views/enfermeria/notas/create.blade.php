<div class="p-2">
    <div class="flex justify-between items-center mb-5 border-b border-slate-100 pb-4">
        <div class="min-w-0">
            <h3 class="text-lg font-bold text-slate-800">Nueva Nota de Enfermería</h3>
            <p class="text-xs text-slate-500 mt-0.5">Registra observaciones y signos vitales</p>
        </div>
        <button type="button" onclick="Alpine.$data(document.getElementById('modal-nota-create')).open = false"
            class="w-9 h-9 flex items-center justify-center rounded-full bg-slate-50 text-slate-400 hover:text-rose-500 hover:bg-rose-50 transition-colors text-xl leading-none border border-slate-200 shrink-0">&times;</button>
    </div>

    @php
        $inputCls =
            'w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-pink-500 focus:ring-0 outline-none';
        $labelCls = 'block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5';
        $errorCls = 'text-rose-600 text-xs mt-1 font-medium';
    @endphp

    <div x-data="{
        temp: '{{ old('temperatura') }}',
        fc: '{{ old('frecuencia_cardiaca') }}',
        fr: '{{ old('frecuencia_respiratoria') }}',
        spo2: '{{ old('saturacion_oxigeno') }}',
        estadoCalculado: '{{ old('estado_paciente') }}',
    
        calcularEstado() {
            let t = parseFloat(this.temp) || 0;
            let f = parseInt(this.fc) || 0;
            let r = parseInt(this.fr) || 0;
            let s = parseInt(this.spo2) || 0;
    
            if (t === 0 && f === 0 && r === 0 && s === 0) {
                this.estadoCalculado = '';
                return;
            }
    
            if (s > 0 && s < 90) {
                this.estadoCalculado = 'critico';
            } else if (t >= 39 || f > 120 || r > 30) {
                this.estadoCalculado = 'critico';
            } else if (t >= 37.5 || f > 100 || r > 20 || (s > 0 && s < 94)) {
                this.estadoCalculado = 'grave';
            } else if (t >= 37.2) {
                this.estadoCalculado = 'mejorando';
            } else {
                this.estadoCalculado = 'estable';
            }
        }
    }">
        <form action="{{ route('enfermeria.notas.store') }}" method="POST" class="space-y-6">
            @csrf

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
                <select name="paciente_id" required class="{{ $inputCls }}">
                    <option value="">— Selecciona —</option>
                    @foreach ($pacientes as $p)
                        <option value="{{ $p->id }}" @selected(old('paciente_id', $paciente?->id) == $p->id)>
                            {{ $p->nombre_completo }}
                        </option>
                    @endforeach
                </select>
                @error('paciente_id')
                    <p class="{{ $errorCls }}">{{ $message }}</p>
                @enderror
            </div>

            {{-- Estado --}}
            <div>
                <label class="{{ $labelCls }}">Estado del paciente (Calculado) *</label>
                <select name="estado_paciente" x-model="estadoCalculado" class="{{ $inputCls }}" required>
                    <option value="">— Calculando... —</option>
                    <option value="estable">Estable</option>
                    <option value="mejorando">Mejorando</option>
                    <option value="grave">Grave</option>
                    <option value="critico">Crítico</option>
                </select>
                @error('estado_paciente')
                    <p class="{{ $errorCls }}">{{ $message }}</p>
                @enderror
            </div>

            {{-- Contenido --}}
            <div>
                <label class="{{ $labelCls }}">Contenido de la nota *</label>
                <textarea name="contenido" rows="4" required placeholder="Describe los cuidados u observaciones..."
                    class="{{ $inputCls }} resize-none">{{ old('contenido') }}</textarea>
                @error('contenido')
                    <p class="{{ $errorCls }}">{{ $message }}</p>
                @enderror
            </div>

            {{-- Signos Vitales --}}
            <div class="pt-5 border-t border-slate-100">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded-xl bg-rose-50 flex items-center justify-center text-rose-500 shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-800 text-sm">Signos vitales del momento *</h3>
                        <p class="text-[11px] text-slate-400">El estado se calculará automáticamente</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    <div>
                        <label class="{{ $labelCls }}">Temperatura (°C) *</label>
                        <input type="number" step="0.1" min="34" max="42" name="temperatura"
                            x-model="temp" @input="calcularEstado()" required class="{{ $inputCls }}">
                        @error('temperatura')
                            <p class="{{ $errorCls }}">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="{{ $labelCls }}">Frec. cardíaca *</label>
                        <input type="number" min="30" max="200" name="frecuencia_cardiaca" x-model="fc"
                            @input="calcularEstado()" required class="{{ $inputCls }}">
                        @error('frecuencia_cardiaca')
                            <p class="{{ $errorCls }}">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="{{ $labelCls }}">Frec. respiratoria *</label>
                        <input type="number" min="8" max="60" name="frecuencia_respiratoria"
                            x-model="fr" @input="calcularEstado()" required class="{{ $inputCls }}">
                        @error('frecuencia_respiratoria')
                            <p class="{{ $errorCls }}">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="{{ $labelCls }}">Presión arterial *</label>
                        <input type="text" name="presion_arterial" value="{{ old('presion_arterial') }}"
                            placeholder="120/80" pattern="\d{2,3}/\d{2,3}" required class="{{ $inputCls }}">
                        @error('presion_arterial')
                            <p class="{{ $errorCls }}">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="{{ $labelCls }}">Saturación O₂ (%) *</label>
                        <input type="number" min="50" max="100" name="saturacion_oxigeno" x-model="spo2"
                            @input="calcularEstado()" required class="{{ $inputCls }}">
                        @error('saturacion_oxigeno')
                            <p class="{{ $errorCls }}">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Botones --}}
            <div class="pt-5 border-t border-slate-100 flex flex-wrap justify-end items-center gap-3">
                <button type="button"
                    onclick="Alpine.$data(document.getElementById('modal-nota-create')).open = false"
                    class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-600 rounded-xl text-xs font-bold transition-all">
                    Cancelar
                </button>
                <button type="submit"
                    class="px-5 py-2.5 bg-pink-600 hover:bg-pink-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                    Guardar Nota
                </button>
            </div>
        </form>
    </div>
</div>
