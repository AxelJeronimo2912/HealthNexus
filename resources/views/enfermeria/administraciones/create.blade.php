<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-3">
            <div class="min-w-0">
                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight truncate">
                    Registrar Administración de Medicamento
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Registra la dosis, vía y observaciones del medicamento</p>
            </div>
            <a href="{{ route('enfermeria.administraciones.index') }}"
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
        <form action="{{ route('enfermeria.administraciones.store') }}" method="POST"
            class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            @csrf

            {{-- ============ HEADER DEL FORM ============ --}}
            <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-pink-50 flex items-center justify-center text-pink-600 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-800 text-base">Datos de la administración</h3>
                    <p class="text-[11px] text-slate-400">Los campos marcados con * son obligatorios</p>
                </div>
            </div>

            {{-- ============ CUERPO DEL FORM ============ --}}
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
                    <select name="paciente_id" required class="{{ $inputCls }}">
                        <option value="">— Selecciona —</option>
                        @foreach ($pacientes as $p)
                            <option value="{{ $p->id }}" @selected(old('paciente_id', $pacienteId) == $p->id)>
                                {{ $p->nombre_completo }}
                            </option>
                        @endforeach
                    </select>
                    @error('paciente_id')
                        <p class="{{ $errorCls }}">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Medicamento --}}
                <div>
                    <label class="{{ $labelCls }}">Medicamento *</label>
                    <select name="medicamento_id" required class="{{ $inputCls }}">
                        <option value="">— Selecciona —</option>
                        @foreach ($medicamentos as $m)
                            <option value="{{ $m->id }}" @selected(old('medicamento_id') == $m->id)>
                                {{ $m->nombre }} {{ $m->concentracion }} — Stock: {{ $m->stock_total_calculado }}
                            </option>
                        @endforeach
                    </select>
                    @error('medicamento_id')
                        <p class="{{ $errorCls }}">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Dosis / Vía / Cantidad --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="{{ $labelCls }}">Dosis *</label>
                        <input type="text" name="dosis" value="{{ old('dosis') }}" required placeholder="500 mg"
                            class="{{ $inputCls }}">
                    </div>
                    <div>
                        <label class="{{ $labelCls }}">Vía *</label>
                        <select name="via" required class="{{ $inputCls }}">
                            <option value="">— Selecciona —</option>
                            @foreach (['Oral', 'Intravenosa', 'Intramuscular', 'Subcutánea', 'Tópica', 'Inhalatoria', 'Oftálmica', 'Rectal'] as $via)
                                <option value="{{ $via }}" @selected(old('via') == $via)>{{ $via }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="{{ $labelCls }}">Cantidad *</label>
                        <input type="number" name="cantidad" value="{{ old('cantidad', 1) }}" required min="1"
                            max="100" class="{{ $inputCls }}">
                        <p class="text-[11px] text-slate-400 mt-1.5">Se descuenta del stock</p>
                    </div>
                </div>

                {{-- Fecha y hora --}}
                <div>
                    <label class="{{ $labelCls }}">Fecha y hora de administración *</label>
                    <input type="datetime-local" name="administrado_en"
                        value="{{ old('administrado_en', now()->format('Y-m-d\TH:i')) }}" required
                        class="{{ $inputCls }}">
                </div>

                {{-- Reacción adversa --}}
                <div class="pt-5 border-t border-slate-100">
                    <label
                        class="inline-flex items-center gap-2.5 cursor-pointer p-3 bg-rose-50/60 border border-rose-100 rounded-2xl w-full">
                        <input type="checkbox" name="reaccion_adversa" value="1" @checked(old('reaccion_adversa'))
                            class="w-4 h-4 rounded border-slate-300 text-rose-600 focus:ring-rose-500 focus:ring-0 cursor-pointer">
                        <span class="text-xs font-bold text-rose-700">Reportar reacción adversa</span>
                        <svg class="w-4 h-4 text-rose-500 ml-auto shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </label>
                </div>

                {{-- Observaciones --}}
                <div>
                    <label class="{{ $labelCls }}">Observaciones</label>
                    <textarea name="observaciones" rows="3" class="{{ $inputCls }} resize-none"
                        placeholder="Cualquier detalle o incidencia...">{{ old('observaciones') }}</textarea>
                </div>
            </div>

            {{-- ============ ACCIONES ============ --}}
            <div
                class="px-6 py-4 bg-slate-50/70 border-t border-slate-100 flex flex-wrap justify-between items-center gap-3">
                <a href="{{ route('enfermeria.administraciones.index') }}"
                    class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-600 rounded-xl text-xs font-bold transition-all">
                    Cancelar
                </a>
                <button type="submit"
                    class="px-5 py-2.5 bg-pink-600 hover:bg-pink-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                    Registrar Administración
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
