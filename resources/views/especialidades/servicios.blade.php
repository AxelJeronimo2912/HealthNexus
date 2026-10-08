<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-3">
            <div class="min-w-0">
                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight truncate">
                    Servicios de: {{ $especialidad->nombre }}
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Asociación y gestión de servicios por especialidad</p>
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

        {{-- ============ FORMULARIO ASOCIAR ============ --}}
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-600 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-800 text-base">Asociar servicio</h3>
                    <p class="text-[11px] text-slate-400">Agrega un servicio a esta especialidad</p>
                </div>
            </div>

            <div class="p-5">
                @if ($disponibles->isEmpty())
                    <div class="px-5 py-8 text-center">
                        <p class="text-xs font-semibold text-slate-400">
                            No hay servicios disponibles para asociar.
                        </p>
                    </div>
                @else
                    <form action="{{ route('especialidades.servicios.asignar', $especialidad) }}" method="POST"
                        class="flex flex-col sm:flex-row gap-3 items-end">
                        @csrf

                        <div class="flex-1 w-full">
                            <label class="{{ $labelCls }}">Servicio *</label>
                            <select name="servicio_id" required class="{{ $inputCls }}">
                                <option value="">— Selecciona —</option>
                                @foreach ($disponibles as $s)
                                    <option value="{{ $s->id }}">
                                        {{ $s->nombre }} ({{ $s->tipo_label }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit"
                            class="w-full sm:w-auto px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all inline-flex items-center justify-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Asociar
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- ============ LISTA SERVICIOS ============ --}}
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="font-extrabold text-slate-800 text-base">Servicios asociados</h3>
                <p class="text-[11px] text-slate-400">{{ $especialidad->servicios->count() }} servicios en esta
                    especialidad</p>
            </div>

            @if ($especialidad->servicios->isEmpty())
                <div class="px-5 py-12 text-center">
                    <p class="text-xs font-semibold text-slate-400">Sin servicios asociados.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-slate-50/70 border-b border-slate-100">
                            <tr>
                                <th class="{{ $thCls }} text-left">Servicio</th>
                                <th class="{{ $thCls }} text-left">Tipo</th>
                                <th class="{{ $thCls }} text-left">Ubicación</th>
                                <th class="px-5 py-3.5"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($especialidad->servicios as $s)
                                <tr class="hover:bg-indigo-50/30 transition-colors">
                                    <td class="px-5 py-3.5">
                                        <p class="text-xs font-bold text-slate-800">{{ $s->nombre }}</p>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span
                                            class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide {{ $s->tipo_color }}">
                                            {{ $s->tipo_label }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        @if ($s->ubicacion)
                                            <p class="text-xs font-medium text-slate-600">{{ $s->ubicacion }}</p>
                                        @else
                                            <span class="text-slate-300 text-xs">—</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <div class="flex justify-end">
                                            <form
                                                action="{{ route('especialidades.servicios.quitar', [$especialidad, $s->pivot->id]) }}"
                                                method="POST"
                                                onsubmit="return confirm('¿Quitar este servicio de la especialidad?')">
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
