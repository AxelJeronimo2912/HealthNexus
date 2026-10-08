<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-3">
            <div class="min-w-0">
                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight truncate">
                    Seguimiento de Pacientes
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Monitoreo continuo de signos vitales y evolución</p>
            </div>
            @can('seguimientos.crear')
                <a href="{{ route('seguimientos.create') }}"
                    class="bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white px-5 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 shadow-sm transition-all shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    Nuevo Seguimiento
                </a>
            @endcan
        </div>
    </x-slot>

    @php
        $inputCls =
            'w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-indigo-500 focus:ring-0 outline-none';
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

        {{-- ============ STATS ============ --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('seguimientos.index', ['filtro' => 'todos']) }}"
                class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm hover:shadow-md hover:border-indigo-200 transition-all active:scale-95 group">
                <div class="flex items-center justify-between">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total en seguimiento</p>
                    <svg class="w-4 h-4 text-slate-300 group-hover:text-indigo-500 transition-colors" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
                <p class="text-3xl font-black text-slate-800 tracking-tight mt-1">{{ $stats['total'] }}</p>
            </a>
            <a href="{{ route('seguimientos.index', ['filtro' => 'cama']) }}"
                class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm hover:shadow-md hover:border-indigo-200 transition-all active:scale-95 group">
                <div class="flex items-center justify-between">
                    <p class="text-[11px] font-bold text-indigo-600/70 uppercase tracking-wider">Con cama asignada</p>
                    <svg class="w-4 h-4 text-slate-300 group-hover:text-indigo-500 transition-colors" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
                <p class="text-3xl font-black text-indigo-600 tracking-tight mt-1">{{ $stats['con_cama'] }}</p>
            </a>
            <a href="{{ route('seguimientos.index', ['filtro' => 'triage']) }}"
                class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm hover:shadow-md hover:border-rose-200 transition-all active:scale-95 group">
                <div class="flex items-center justify-between">
                    <p class="text-[11px] font-bold text-rose-600/70 uppercase tracking-wider">Triage grave (24h)</p>
                    <svg class="w-4 h-4 text-slate-300 group-hover:text-rose-500 transition-colors" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
                <p class="text-3xl font-black text-rose-600 tracking-tight mt-1">{{ $stats['con_triage'] }}</p>
            </a>
        </div>

        {{-- ============ BUSCADOR ============ --}}
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-5">
            <form method="GET" class="flex gap-2 flex-wrap items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="{{ $labelCls }}">Buscar</label>
                    <input type="text" name="buscar" value="{{ $busqueda }}" placeholder="Nombre del paciente"
                        class="{{ $inputCls }}">
                </div>
                <div class="min-w-[180px]">
                    <label class="{{ $labelCls }}">Filtro</label>
                    <select name="filtro" class="{{ $inputCls }}">
                        <option value="todos" @selected($filtro === 'todos')>Todos</option>
                        <option value="cama" @selected($filtro === 'cama')>Con cama</option>
                        <option value="triage" @selected($filtro === 'triage')>Con triage grave</option>
                    </select>
                </div>
                <button type="submit"
                    class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all">
                    Filtrar
                </button>
                @if ($busqueda || $filtro !== 'todos')
                    <a href="{{ route('seguimientos.index') }}"
                        class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition-all">
                        Limpiar
                    </a>
                @endif
            </form>
        </div>

        {{-- ============ TABLA ============ --}}
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="font-extrabold text-slate-800 text-base">Pacientes en seguimiento</h3>
                <p class="text-[11px] text-slate-400">{{ $pacientes->total() }} pacientes activos</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-slate-50/70 border-b border-slate-100">
                        <tr>
                            <th class="{{ $thCls }} text-left">Paciente</th>
                            <th class="{{ $thCls }} text-left">Cama</th>
                            <th class="{{ $thCls }} text-left">Triage</th>
                            <th class="{{ $thCls }} text-left">Último seguimiento</th>
                            <th class="px-5 py-3.5"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($pacientes as $paciente)
                            <tr class="hover:bg-indigo-50/30 transition-colors">
                                <td class="px-5 py-3.5">
                                    <p class="text-xs font-bold text-slate-800">{{ $paciente->nombre_completo }}</p>
                                    <p class="text-[10px] font-medium text-slate-400 mt-0.5">
                                        {{ $paciente->edad }} años — {{ ucfirst($paciente->sexo) }}
                                    </p>
                                </td>
                                <td class="px-5 py-3.5">
                                    @if ($paciente->cama_actual)
                                        <span
                                            class="px-2.5 py-1 bg-indigo-50 text-indigo-700 border border-indigo-100 rounded-full text-[10px] font-bold uppercase tracking-wide">
                                            {{ $paciente->cama_actual->cama->codigo }}
                                        </span>
                                        <p class="text-[10px] font-medium text-slate-400 mt-1">
                                            {{ $paciente->cama_actual->cama->area }}
                                        </p>
                                    @else
                                        <span
                                            class="px-2.5 py-1 bg-slate-100 text-slate-500 rounded-full text-[10px] font-bold uppercase tracking-wide">
                                            Sin cama
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    @if ($paciente->ultimoSignoVital)
                                        <span
                                            class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide {{ $paciente->ultimoSignoVital->triage_color }}">
                                            {{ $paciente->ultimoSignoVital->triage_label }}
                                        </span>
                                        <p class="text-[10px] font-medium text-slate-400 mt-1">
                                            {{ $paciente->ultimoSignoVital->created_at->diffForHumans() }}
                                        </p>
                                    @else
                                        <span
                                            class="px-2.5 py-1 bg-slate-100 text-slate-500 rounded-full text-[10px] font-bold uppercase tracking-wide">
                                            Sin signos
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    @if ($paciente->seguimientoActual)
                                        <p class="text-[11px] font-medium text-slate-400">
                                            {{ $paciente->seguimientoActual->created_at->diffForHumans() }}
                                        </p>
                                        <p class="text-xs font-bold text-slate-700 mt-0.5">
                                            {{ $paciente->seguimientoActual->user?->nombre_completo ?? '—' }}
                                        </p>
                                    @else
                                        <span
                                            class="px-2.5 py-1 bg-slate-100 text-slate-500 rounded-full text-[10px] font-bold uppercase tracking-wide">
                                            Sin seguimiento
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex justify-end gap-2 whitespace-nowrap">
                                        <a href="{{ route('seguimientos.show', $paciente) }}"
                                            class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-[11px] font-bold transition-all">
                                            Ver seguimiento
                                        </a>
                                        <a href="{{ route('seguimientos.create', $paciente) }}"
                                            class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg text-[11px] font-bold transition-all">
                                            + Nuevo
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-12 text-center">
                                    <p class="text-xs font-semibold text-slate-400">Sin pacientes en seguimiento.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">{{ $pacientes->links() }}</div>
    </div>
</x-app-layout>
