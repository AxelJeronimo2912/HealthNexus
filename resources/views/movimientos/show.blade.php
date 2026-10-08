<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-3">
            <div class="min-w-0">
                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight truncate">
                    Detalle del Movimiento
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Información completa del movimiento de inventario</p>
            </div>
            <a href="{{ route('movimientos.index') }}"
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
        $labelCls = 'text-[11px] font-bold text-slate-400 uppercase tracking-wider';
    @endphp

    <div class="py-8 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-6">

            {{-- ============ ENCABEZADO ============ --}}
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-5">
                <div>
                    <p class="{{ $labelCls }}">Movimiento #{{ $movimiento->id }}</p>
                    <div class="mt-2">
                        <span
                            class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide border {{ $movimiento->tipo_color }}">
                            {{ $movimiento->tipo_label }}
                        </span>
                    </div>
                    <p class="text-[11px] font-medium text-slate-400 mt-2">
                        {{ $movimiento->created_at->format('d/m/Y H:i:s') }}
                    </p>
                </div>

                <div
                    class="{{ $movimiento->cantidad >= 0 ? 'bg-emerald-50/60 border-emerald-100' : 'bg-rose-50/60 border-rose-100' }} border rounded-2xl p-4 text-right shrink-0">
                    <p
                        class="text-[11px] font-bold uppercase tracking-wider {{ $movimiento->cantidad >= 0 ? 'text-emerald-600/70' : 'text-rose-500/70' }}">
                        Cantidad
                    </p>
                    <p
                        class="text-4xl font-black tracking-tight mt-1 {{ $movimiento->cantidad >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                        {{ $movimiento->cantidad >= 0 ? '+' : '' }}{{ $movimiento->cantidad }}
                    </p>
                </div>
            </div>

            {{-- ============ MEDICAMENTO ============ --}}
            <div class="border-t border-slate-100 pt-5">
                <p class="{{ $labelCls }} mb-2">Medicamento</p>
                <p class="text-base font-extrabold text-slate-800">
                    {{ $movimiento->medicamento?->nombre }}
                    @if ($movimiento->medicamento?->concentracion)
                        <span class="text-slate-400 font-medium">· {{ $movimiento->medicamento->concentracion }}</span>
                    @endif
                </p>
                <p class="text-xs font-medium text-slate-500 mt-1">
                    {{ $movimiento->medicamento?->sustancia_activa ?? '—' }}
                </p>
                @if ($movimiento->medicamento)
                    <a href="{{ route('existencias.show', $movimiento->medicamento) }}"
                        class="inline-flex items-center gap-1.5 mt-3 px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg text-[11px] font-bold transition-all">
                        Ver existencias
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                @endif
            </div>

            {{-- ============ LOTE ============ --}}
            @if ($movimiento->lote)
                <div class="border-t border-slate-100 pt-5">
                    <p class="{{ $labelCls }} mb-3">Lote</p>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                            <dt class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Código</dt>
                            <dd class="mt-1">
                                <span
                                    class="px-2 py-1 bg-slate-100 text-slate-600 rounded-lg text-[11px] font-mono font-bold">
                                    {{ $movimiento->lote->codigo_lote ?? 'LOTE-' . $movimiento->lote->id }}
                                </span>
                            </dd>
                        </div>
                        <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                            <dt class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Caducidad</dt>
                            <dd class="text-xs font-extrabold text-slate-800 mt-1">
                                {{ $movimiento->lote->fecha_caducidad->format('d/m/Y') }}
                            </dd>
                        </div>
                        <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3 sm:col-span-2">
                            <dt class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Estado</dt>
                            <dd class="mt-1">
                                <span
                                    class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide border {{ $movimiento->lote->estado_color }}">
                                    {{ $movimiento->lote->estado_label }}
                                </span>
                            </dd>
                        </div>
                    </dl>
                </div>
            @endif

            {{-- ============ STOCK ============ --}}
            <div class="border-t border-slate-100 pt-5">
                <p class="{{ $labelCls }} mb-3">Cambio de stock</p>
                <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-4 flex flex-wrap items-center gap-3">
                    <span class="px-2 py-1 bg-slate-100 text-slate-600 rounded-lg text-[11px] font-mono font-bold">
                        {{ $movimiento->stock_anterior }}
                    </span>
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                    <span class="px-2 py-1 bg-slate-800 text-white rounded-lg text-[11px] font-mono font-bold">
                        {{ $movimiento->stock_nuevo }}
                    </span>
                    <span
                        class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide border {{ $movimiento->cantidad >= 0 ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-rose-50 text-rose-700 border-rose-100' }}">
                        {{ $movimiento->cantidad >= 0 ? '+' : '' }}{{ $movimiento->cantidad }}
                    </span>
                </div>
            </div>

            {{-- ============ MOTIVO ============ --}}
            @if ($movimiento->motivo)
                <div class="border-t border-slate-100 pt-5">
                    <p class="{{ $labelCls }} mb-2">Motivo</p>
                    <p
                        class="text-xs text-slate-600 leading-relaxed whitespace-pre-line bg-slate-50 border border-slate-100 rounded-2xl p-4">
                        {{ $movimiento->motivo }}
                    </p>
                </div>
            @endif

            {{-- ============ REFERENCIA ============ --}}
            @if ($movimiento->referencia_tipo && $movimiento->referencia_id)
                <div class="border-t border-slate-100 pt-5">
                    <p class="{{ $labelCls }} mb-3">Referencia</p>
                    <div
                        class="bg-slate-50/70 border border-slate-100 rounded-2xl p-4 flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tipo</p>
                            <p class="text-xs font-extrabold text-slate-800 mt-0.5">
                                {{ ucfirst($movimiento->referencia_tipo) }}
                            </p>
                        </div>
                        @if ($movimiento->referencia_tipo === 'consulta')
                            <a href="{{ route('consultas.show', $movimiento->referencia_id) }}"
                                class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg text-[11px] font-bold transition-all inline-flex items-center gap-1.5">
                                Ver consulta
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            {{-- ============ USUARIO ============ --}}
            <div class="border-t border-slate-100 pt-5">
                <p class="{{ $labelCls }} mb-2">Usuario</p>
                <p class="text-xs font-extrabold text-slate-800">
                    {{ $movimiento->user?->nombre_completo ?? 'Sistema (automático)' }}
                </p>
            </div>

            {{-- ============ ACCIONES ============ --}}
            <div class="pt-5 border-t border-slate-100 flex flex-wrap gap-2">
                <a href="{{ route('movimientos.index') }}"
                    class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-600 rounded-xl text-xs font-bold transition-all">
                    Volver
                </a>
                @if ($movimiento->medicamento)
                    <a href="{{ route('existencias.show', $movimiento->medicamento) }}"
                        class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all inline-flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        Ver existencias
                    </a>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
