<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-3">
            <div class="min-w-0">
                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight truncate">
                    Notas de Enfermería
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Registro de notas y seguimiento de pacientes</p>
            </div>
            <a href="{{ route('enfermeria.notas.create') }}"
                class="bg-pink-600 hover:bg-pink-700 active:scale-95 text-white px-5 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 shadow-sm transition-all shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                Nueva Nota
            </a>
        </div>
    </x-slot>

    @php
        $inputCls =
            'w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-pink-500 focus:ring-0 outline-none';
        $labelCls = 'block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5';
        $thCls = 'px-5 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider';
    @endphp

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

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

        {{-- ============ STATS ============ --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total notas</p>
                <p class="text-3xl font-black text-slate-800 tracking-tight mt-1">{{ $stats['total'] }}</p>
            </div>
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm">
                <p class="text-[11px] font-bold text-pink-600/70 uppercase tracking-wider">Hoy</p>
                <p class="text-3xl font-black text-pink-600 tracking-tight mt-1">{{ $stats['hoy'] }}</p>
            </div>
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm">
                <p class="text-[11px] font-bold text-indigo-600/70 uppercase tracking-wider">Esta semana</p>
                <p class="text-3xl font-black text-indigo-600 tracking-tight mt-1">{{ $stats['esta_semana'] }}</p>
            </div>
        </div>

        {{-- ============ FILTROS ============ --}}
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-5">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
                <div class="md:col-span-2">
                    <label class="{{ $labelCls }}">Buscar paciente</label>
                    <input type="text" name="buscar" value="{{ $buscar }}" placeholder="Nombre o apellidos"
                        class="{{ $inputCls }}">
                </div>
                <div>
                    <label class="{{ $labelCls }}">Paciente</label>
                    <select name="paciente_id" class="{{ $inputCls }}">
                        <option value="">Todos</option>
                        @foreach ($pacientes as $p)
                            <option value="{{ $p->id }}" @selected($pacienteId == $p->id)>
                                {{ $p->nombre_completo }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="{{ $labelCls }}">Desde</label>
                    <input type="date" name="desde" value="{{ $desde }}" class="{{ $inputCls }}">
                </div>
                <div>
                    <label class="{{ $labelCls }}">Hasta</label>
                    <input type="date" name="hasta" value="{{ $hasta }}" class="{{ $inputCls }}">
                </div>
                <div class="md:col-span-5 flex flex-wrap gap-2 pt-2 border-t border-slate-100">
                    <button type="submit"
                        class="px-5 py-2.5 bg-pink-600 hover:bg-pink-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all">
                        Filtrar
                    </button>
                    <a href="{{ route('enfermeria.notas.index') }}"
                        class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition-all">
                        Limpiar
                    </a>
                </div>
            </form>
        </div>

        {{-- ============ TABLA ============ --}}
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="font-extrabold text-slate-800 text-base">Notas registradas</h3>
                <p class="text-[11px] text-slate-400">{{ $notas->total() }} notas en total</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-slate-50/70 border-b border-slate-100">
                        <tr>
                            <th class="{{ $thCls }} text-left">Fecha</th>
                            <th class="{{ $thCls }} text-left">Paciente</th>
                            <th class="{{ $thCls }} text-left">Estado</th>
                            <th class="{{ $thCls }} text-left">Nota</th>
                            <th class="{{ $thCls }} text-left">Enfermero/a</th>
                            <th class="px-5 py-3.5"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($notas as $nota)
                            <tr class="hover:bg-pink-50/30 transition-colors">
                                <td class="px-5 py-3.5 text-[11px] font-medium text-slate-400 whitespace-nowrap">
                                    {{ $nota->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <p class="text-xs font-bold text-slate-800">
                                        {{ $nota->paciente?->nombre_completo ?? '—' }}
                                    </p>
                                    @if ($nota->cama)
                                        <span
                                            class="inline-block mt-1 px-2 py-0.5 bg-pink-50 text-pink-600 rounded-full text-[10px] font-bold">
                                            Cama: {{ $nota->cama->codigo }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    @if ($nota->estado_paciente)
                                        <span
                                            class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide {{ $nota->estado_color }}">
                                            {{ ucfirst($nota->estado_paciente) }}
                                        </span>
                                    @else
                                        <span class="text-slate-300 text-xs">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    <p class="text-xs font-medium text-slate-600">
                                        {{ Str::limit($nota->contenido, 60) }}
                                    </p>
                                </td>
                                <td class="px-5 py-3.5">
                                    <p class="text-xs font-medium text-slate-600">
                                        {{ $nota->user?->nombre_completo ?? '—' }}
                                    </p>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('enfermeria.notas.show', $nota) }}"
                                            class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-[11px] font-bold transition-all">
                                            Ver
                                        </a>
                                        @if ($nota->user_id === auth()->id() || auth()->user()->hasRole('administrador'))
                                            <form action="{{ route('enfermeria.notas.destroy', $nota) }}"
                                                method="POST" onsubmit="return confirm('¿Eliminar esta nota?')">
                                                @csrf @method('DELETE')
                                                <button
                                                    class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-[11px] font-bold transition-all">
                                                    Eliminar
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center">
                                    <p class="text-xs font-semibold text-slate-400">Sin notas de enfermería
                                        registradas.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">{{ $notas->links() }}</div>
    </div>
</x-app-layout>
