<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-3">
            <div class="min-w-0">
                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight truncate">
                    Lotes
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Trazabilidad y caducidad por lote de medicamento</p>
            </div>
            <a href="{{ route('existencias.index') }}"
                class="bg-white hover:bg-indigo-50 text-slate-700 hover:text-indigo-600 border border-slate-200 hover:border-indigo-200 px-5 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 transition-all active:scale-95 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                </svg>
                Ver por medicamento
            </a>
        </div>
    </x-slot>

    @php
        $thCls = 'px-5 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider';
    @endphp

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- ============ FILTROS ============ --}}
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('existencias.lotes') }}"
                class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all active:scale-95 {{ !$filtro ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                Todos
            </a>
            <a href="{{ route('existencias.lotes', ['filtro' => 'vigentes']) }}"
                class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all active:scale-95 {{ $filtro === 'vigentes' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-emerald-50 hover:text-emerald-700' }}">
                Vigentes
            </a>
            <a href="{{ route('existencias.lotes', ['filtro' => 'proximos']) }}"
                class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all active:scale-95 {{ $filtro === 'proximos' ? 'bg-amber-500 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-amber-50 hover:text-amber-700' }}">
                Próximos a caducar (30d)
            </a>
            <a href="{{ route('existencias.lotes', ['filtro' => 'caducados']) }}"
                class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all active:scale-95 {{ $filtro === 'caducados' ? 'bg-rose-600 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-rose-50 hover:text-rose-700' }}">
                Caducados
            </a>
        </div>

        {{-- ============ TABLA ============ --}}
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="font-extrabold text-slate-800 text-base">Lotes registrados</h3>
                <p class="text-[11px] text-slate-400">{{ $lotes->total() }} lotes en total</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-slate-50/70 border-b border-slate-100">
                        <tr>
                            <th class="{{ $thCls }} text-left">Lote</th>
                            <th class="{{ $thCls }} text-left">Medicamento</th>
                            <th class="{{ $thCls }} text-left">Caducidad</th>
                            <th class="{{ $thCls }} text-right">Disponible</th>
                            <th class="{{ $thCls }} text-left">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($lotes as $lote)
                            <tr class="hover:bg-indigo-50/30 transition-colors">
                                <td class="px-5 py-3.5">
                                    <span
                                        class="px-2 py-1 bg-slate-100 text-slate-600 rounded-lg text-[11px] font-mono font-bold">
                                        {{ $lote->codigo_lote ?? 'LOTE-' . $lote->id }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <p class="text-xs font-bold text-slate-800">
                                        {{ $lote->medicamento?->nombre ?? '—' }}</p>
                                    @if ($lote->medicamento?->concentracion)
                                        <p class="text-[10px] font-medium text-slate-400 mt-0.5">
                                            {{ $lote->medicamento->concentracion }}
                                        </p>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    <p class="text-xs font-semibold text-slate-700">
                                        {{ $lote->fecha_caducidad->format('d/m/Y') }}
                                    </p>
                                    <p class="text-[10px] font-medium text-slate-400 mt-0.5">
                                        @if ($lote->esta_caducado)
                                            Caducó hace {{ abs($lote->dias_para_caducar) }} días
                                        @else
                                            Vence en {{ $lote->dias_para_caducar }} días
                                        @endif
                                    </p>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <span
                                        class="px-2 py-1 bg-slate-100 text-slate-700 rounded-lg text-[11px] font-mono font-bold">
                                        {{ $lote->cantidad_disponible }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span
                                        class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide border {{ $lote->estado_color }}">
                                        {{ $lote->estado_label }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-12 text-center">
                                    <p class="text-xs font-semibold text-slate-400">Sin lotes.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">{{ $lotes->links() }}</div>
    </div>
</x-app-layout>
