<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-3">
            <div class="min-w-0">
                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight truncate">
                    Médicos de: {{ $especialidad->nombre }}
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Asignación y gestión de médicos por especialidad</p>
            </div>
            <a href="{{ route('especialidades.show', $especialidad) }}"
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
        $thCls = 'px-5 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider';
    @endphp

    <div class="py-8 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        @if (session('success'))
            <div
                class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-2xl text-xs font-semibold flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div
                class="p-4 bg-rose-50 border border-rose-100 text-rose-800 rounded-2xl text-xs font-semibold flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- ============ FORMULARIO ASIGNAR ============ --}}
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-800 text-base">Asignar médico</h3>
                    <p class="text-[11px] text-slate-400">Agrega un médico a esta especialidad</p>
                </div>
            </div>

            <div class="p-5">
                @if ($disponibles->isEmpty())
                    <div class="px-5 py-8 text-center">
                        <p class="text-xs font-semibold text-slate-400">
                            No hay médicos disponibles para asignar (todos ya están en esta especialidad).
                        </p>
                    </div>
                @else
                    <form action="{{ route('especialidades.medicos.asignar', $especialidad) }}" method="POST"
                        class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                        @csrf

                        <div class="md:col-span-2">
                            <label class="{{ $labelCls }}">Médico *</label>
                            <select name="user_id" required class="{{ $inputCls }}">
                                <option value="">— Selecciona —</option>
                                @foreach ($disponibles as $u)
                                    <option value="{{ $u->id }}">
                                        {{ $u->nombre_completo ?: $u->name }}
                                        ({{ $u->getRoleNames()->first() ?? 'sin rol' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="{{ $labelCls }}">Cédula especialidad</label>
                            <input type="text" name="numero_cedula_especialidad" class="{{ $inputCls }}">
                        </div>

                        <div class="flex items-end gap-2">
                            <label class="inline-flex items-center gap-2 cursor-pointer pb-2.5">
                                <input type="checkbox" name="es_principal" value="1"
                                    class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 focus:ring-0 cursor-pointer">
                                <span class="text-xs font-bold text-slate-700">Principal</span>
                            </label>
                        </div>

                        <div class="md:col-span-4 flex justify-end pt-2 border-t border-slate-100">
                            <button type="submit"
                                class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all inline-flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                Asignar
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>

        {{-- ============ LISTA MÉDICOS ============ --}}
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="font-extrabold text-slate-800 text-base">Médicos asignados</h3>
                <p class="text-[11px] text-slate-400">{{ $especialidad->medicos->count() }} médicos en esta
                    especialidad</p>
            </div>

            @if ($especialidad->medicos->isEmpty())
                <div class="px-5 py-12 text-center">
                    <p class="text-xs font-semibold text-slate-400">Sin médicos asignados.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-slate-50/70 border-b border-slate-100">
                            <tr>
                                <th class="{{ $thCls }} text-left">Médico</th>
                                <th class="{{ $thCls }} text-left">Rol sistema</th>
                                <th class="{{ $thCls }} text-left">Cédula esp.</th>
                                <th class="{{ $thCls }} text-center">Principal</th>
                                <th class="px-5 py-3.5"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($especialidad->medicos as $u)
                                <tr class="hover:bg-indigo-50/30 transition-colors">
                                    <td class="px-5 py-3.5">
                                        <p class="text-xs font-bold text-slate-800">
                                            {{ $u->nombre_completo ?: $u->name }}
                                        </p>
                                        <p class="text-[10px] font-medium text-slate-400 mt-0.5">{{ $u->email }}
                                        </p>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span
                                            class="px-2.5 py-1 bg-indigo-50 text-indigo-700 border border-indigo-100 rounded-full text-[10px] font-bold uppercase tracking-wide">
                                            {{ $u->getRoleNames()->first() ?? 'sin rol' }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        @if ($u->pivot->numero_cedula_especialidad)
                                            <span
                                                class="px-2 py-1 bg-slate-100 text-slate-600 rounded-lg text-[11px] font-mono font-bold">
                                                {{ $u->pivot->numero_cedula_especialidad }}
                                            </span>
                                        @else
                                            <span class="text-slate-300 text-xs">—</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 text-center">
                                        @if ($u->pivot->es_principal)
                                            <span
                                                class="px-2.5 py-1 bg-amber-50 text-amber-700 border border-amber-100 rounded-full text-[10px] font-bold uppercase tracking-wide">
                                                ★ Principal
                                            </span>
                                        @else
                                            <span class="text-slate-300 text-xs">—</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <div class="flex justify-end">
                                            <form
                                                action="{{ route('especialidades.medicos.quitar', [$especialidad, $u->pivot->id]) }}"
                                                method="POST"
                                                onsubmit="return confirm('¿Quitar este médico de la especialidad?')">
                                                @csrf @method('DELETE')
                                                <button
                                                    class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-[11px] font-bold transition-all">
                                                    Quitar
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
