<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-3">
            <div class="min-w-0">
                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight truncate">
                    Asignar Turno a {{ $user->nombre_completo }}
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Configura horario, día y vigencia del turno</p>
            </div>
            <a href="{{ route('admin.users.turnos.index', $user) }}"
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
        <form action="{{ route('admin.users.turnos.store', $user) }}" method="POST"
            class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            @csrf

            <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-800 text-base">Datos del turno</h3>
                    <p class="text-[11px] text-slate-400">Los campos marcados con * son obligatorios</p>
                </div>
            </div>

            <div class="p-6 space-y-6">

                {{-- Turno --}}
                <div>
                    <label class="{{ $labelCls }}">Turno *</label>
                    <select name="turno_id" required class="{{ $inputCls }}">
                        <option value="">— Selecciona un turno —</option>
                        @foreach ($turnos as $t)
                            <option value="{{ $t->id }}" @selected(old('turno_id') == $t->id)>
                                {{ $t->nombre }} ({{ $t->rango }})
                            </option>
                        @endforeach
                    </select>
                    @error('turno_id')
                        <p class="{{ $errorCls }}">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Día --}}
                <div>
                    <label class="{{ $labelCls }}">Día de la semana</label>
                    <select name="dia_semana" class="{{ $inputCls }}">
                        <option value="">— Todos los días —</option>
                        @foreach (['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'] as $i => $dia)
                            <option value="{{ $i }}" @selected(old('dia_semana') == $i)>{{ $dia }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[11px] text-slate-400 mt-1.5">
                        Si dejas "Todos los días", aplica a cualquier día de la semana.
                    </p>
                </div>

                {{-- Vigencia --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="{{ $labelCls }}">Fecha de inicio *</label>
                        <input type="date" name="fecha_inicio"
                            value="{{ old('fecha_inicio', now()->format('Y-m-d')) }}" required
                            class="{{ $inputCls }}">
                        @error('fecha_inicio')
                            <p class="{{ $errorCls }}">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="{{ $labelCls }}">Fecha de fin</label>
                        <input type="date" name="fecha_fin" value="{{ old('fecha_fin') }}"
                            class="{{ $inputCls }}">
                        <p class="text-[11px] text-slate-400 mt-1.5">Vacío = indefinido.</p>
                    </div>
                </div>

                {{-- Área --}}
                <div>
                    <label class="{{ $labelCls }}">Área / Servicio</label>
                    <input type="text" name="area" value="{{ old('area') }}"
                        placeholder="Ej. Urgencias, Hospitalización" class="{{ $inputCls }}">
                </div>

                {{-- Notas --}}
                <div>
                    <label class="{{ $labelCls }}">Notas</label>
                    <textarea name="notas" rows="2" class="{{ $inputCls }} resize-none">{{ old('notas') }}</textarea>
                </div>
            </div>

            <div
                class="px-6 py-4 bg-slate-50/70 border-t border-slate-100 flex flex-wrap justify-between items-center gap-3">
                <a href="{{ route('admin.users.turnos.index', $user) }}"
                    class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-600 rounded-xl text-xs font-bold transition-all">
                    Cancelar
                </a>
                <button type="submit"
                    class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                    Asignar Turno
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
