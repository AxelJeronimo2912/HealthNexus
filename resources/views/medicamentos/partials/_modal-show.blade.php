{{-- MODAL VER --}}
<template x-teleport="body">
    <div x-show="openShow" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
        <div @click.away="openShow = false"
            class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 space-y-5 text-left max-h-[90vh] overflow-y-auto">

            <div class="flex justify-between items-center pb-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-600 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-800 text-base">Detalles del Medicamento</h3>
                        <p class="text-xs text-slate-400">Información completa</p>
                    </div>
                </div>
                <button @click="openShow = false"
                    class="text-slate-400 hover:text-slate-700 text-xl leading-none">✕</button>
            </div>

            <div class="space-y-4">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                            Identificación</p>
                        <p class="text-base font-extrabold text-slate-800 mt-1">{{ $med->nombre }}</p>
                        <p class="text-xs font-bold text-brand-600 mt-0.5">
                            {{ $med->sustancia_activa ?? 'Sin sustancia activa' }}</p>
                    </div>
                    @if ($med->activo ?? true)
                        <span
                            class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-full text-xs font-bold uppercase tracking-wide shrink-0">Activo</span>
                    @else
                        <span
                            class="px-2.5 py-1 bg-slate-100 text-slate-500 rounded-full text-xs font-bold uppercase tracking-wide shrink-0">Inactivo</span>
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-3 pt-4 border-t border-slate-100">
                    <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Laboratorio</p>
                        <p class="text-xs font-extrabold text-slate-800 mt-0.5">{{ $med->laboratorio ?? '—' }}</p>
                    </div>
                    <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Presentación</p>
                        <p class="text-xs font-extrabold text-slate-800 mt-0.5">{{ $med->presentacion ?? '—' }}</p>
                    </div>
                    <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Concentración</p>
                        <p class="text-xs font-extrabold text-slate-800 mt-0.5">{{ $med->concentracion ?? '—' }}</p>
                    </div>
                    <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Vía Admin.</p>
                        <p class="text-xs font-extrabold text-slate-800 mt-0.5">{{ $med->via_administracion ?? '—' }}
                        </p>
                    </div>
                    <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Grupo Terapéutico</p>
                        <p class="text-xs font-extrabold text-slate-800 mt-0.5">{{ $med->grupo_terapeutico ?? '—' }}</p>
                    </div>
                    <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Reg. Sanitario</p>
                        <p class="text-xs font-extrabold text-slate-800 mt-0.5">{{ $med->registro_sanitario ?? '—' }}
                        </p>
                    </div>
                    <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Código Barras</p>
                        <p class="text-xs font-mono font-bold text-slate-800 mt-0.5">{{ $med->codigo_barras ?? '—' }}
                        </p>
                    </div>
                    <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Unidad Medida</p>
                        <p class="text-xs font-extrabold text-slate-800 mt-0.5">
                            {{ ucfirst($med->unidad_medida ?? 'Pieza') }}</p>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100">
                    <p class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Clasificación
                    </p>
                    <div class="flex flex-wrap gap-1.5">
                        @if ($med->psicotropico)
                            <span
                                class="px-2.5 py-1 bg-accent-50 text-accent-700 border border-accent-100 rounded-full text-xs font-bold uppercase tracking-wide">Psicotrópico</span>
                        @endif
                        @if ($med->antibiotico)
                            <span
                                class="px-2.5 py-1 bg-brand-50 text-brand-700 border border-brand-100 rounded-full text-xs font-bold uppercase tracking-wide">Antibiótico</span>
                        @endif
                        @if ($med->controlado)
                            <span
                                class="px-2.5 py-1 bg-rose-50 text-rose-700 border border-rose-100 rounded-full text-xs font-bold uppercase tracking-wide">Controlado</span>
                        @endif
                        @if (!$med->psicotropico && !$med->antibiotico && !$med->controlado)
                            <span class="text-slate-300 text-xs italic">Ninguna</span>
                        @endif
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 grid grid-cols-3 gap-3">
                    <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3 text-center">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Stock Mín / Máx</p>
                        <p class="text-xs font-extrabold text-slate-800 mt-0.5">{{ $med->stock_minimo ?? 0 }} /
                            {{ $med->stock_maximo ?? 0 }}</p>
                    </div>
                    <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3 text-center">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Precio Compra</p>
                        <p class="text-xs font-extrabold text-slate-800 mt-0.5">
                            {{ isset($med->precio_compra) ? '$' . number_format($med->precio_compra, 2) : '—' }}</p>
                    </div>
                    <div class="bg-emerald-50/60 border border-emerald-100 rounded-2xl p-3 text-center">
                        <p class="text-xs font-bold text-emerald-600/70 uppercase tracking-wider">Precio Venta</p>
                        <p class="text-xs font-extrabold text-emerald-600 mt-0.5">
                            {{ isset($med->precio_venta) ? '$' . number_format($med->precio_venta, 2) : '—' }}</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-slate-100">
                <button @click="openShow = false" type="button"
                    class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-600 rounded-xl text-xs font-bold transition-all">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</template>
