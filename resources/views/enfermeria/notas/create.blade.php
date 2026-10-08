<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-3">
            <div class="min-w-0">
                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight truncate">
                    Nueva Nota de Enfermería
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Registra observaciones y signos vitales del paciente</p>
            </div>
            <a href="{{ route('enfermeria.notas.index') }}"
                class="bg-white hover:bg-pink-50 text-slate-700 hover:text-pink-600 border border-slate-200 hover:border-pink-200 px-5 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 transition-all active:scale-95 group shrink-0">
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
            'w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-pink-500 focus:ring-0 outline-none';
        $labelCls = 'block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5';
        $errorCls = 'text-rose-600 text-xs mt-1 font-medium';
    @endphp

    <div class="py-8 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <form action="{{ route('enfermeria.notas.store') }}" method="POST"
            class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            @csrf

            {{-- ============ HEADER DEL FORM ============ --}}
            <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-pink-50 flex items-center justify-center text-pink-600 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-800 text-base">Datos de la nota</h3>
                    <p class="text-[11px] text-slate-400">Los campos marcados con * son obligatorios</p>
                </div>
            </div>

            {{-- ============ CUERPO DEL FORM ============ --}}
            <div class="p-6 space-y-6">

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

                {{-- Estado del paciente --}}
                <div>
                    <label class="{{ $labelCls }}">Estado del paciente</label>
                    <select name="estado_paciente" class="{{ $inputCls }}">
                        <option value="">— Sin especificar —</option>
                        <option value="estable" @selected(old('estado_paciente') == 'estable')>Estable</option>
                        <option value="mejorando" @selected(old('estado_paciente') == 'mejorando')>Mejorando</option>
                        <option value="grave" @selected(old('estado_paciente') == 'grave')>Grave</option>
                        <option value="critico" @selected(old('estado_paciente') == 'critico')>Crítico</option>
                    </select>
                </div>

                {{-- Contenido --}}
                <div>
                    <label class="{{ $labelCls }}">Contenido de la nota *</label>
                    <textarea name="contenido" rows="5" required
                        placeholder="Describe los cuidados, observaciones, cambios en el estado del paciente..."
                        class="{{ $inputCls }} resize-none">{{ old('contenido') }}</textarea>
                    @error('contenido')
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
                            <h3 class="font-extrabold text-slate-800 text-sm">Signos vitales del momento</h3>
                            <p class="text-[11px] text-slate-400">Opcional — completa solo los que tengas</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        <div>
                            <label class="{{ $labelCls }}">Temperatura (°C)</label>
                            <input type="number" step="0.1" name="temperatura" value="{{ old('temperatura') }}"
                                class="{{ $inputCls }}">
                        </div>
                        <div>
                            <label class="{{ $labelCls }}">Frec. cardíaca</label>
                            <input type="number" name="frecuencia_cardiaca" value="{{ old('frecuencia_cardiaca') }}"
                                class="{{ $inputCls }}">
                        </div>
                        <div>
                            <label class="{{ $labelCls }}">Frec. respiratoria</label>
                            <input type="number" name="frecuencia_respiratoria"
                                value="{{ old('frecuencia_respiratoria') }}" class="{{ $inputCls }}">
                        </div>
                        <div>
                            <label class="{{ $labelCls }}">Presión arterial</label>
                            <input type="text" name="presion_arterial" value="{{ old('presion_arterial') }}"
                                placeholder="120/80" class="{{ $inputCls }}">
                        </div>
                        <div>
                            <label class="{{ $labelCls }}">Saturación O₂ (%)</label>
                            <input type="number" name="saturacion_oxigeno" value="{{ old('saturacion_oxigeno') }}"
                                class="{{ $inputCls }}">
                        </div>
                    </div>
                </div>

            </div>

            {{-- ============ ACCIONES ============ --}}
            <div
                class="px-6 py-4 bg-slate-50/70 border-t border-slate-100 flex flex-wrap justify-between items-center gap-3">
                <a href="{{ route('enfermeria.notas.index') }}"
                    class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-600 rounded-xl text-xs font-bold transition-all">
                    Cancelar
                </a>
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
</x-app-layout>
