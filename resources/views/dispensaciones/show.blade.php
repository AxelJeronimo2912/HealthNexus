<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-3">
            <div class="min-w-0">
                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight truncate">
                    Detalle de Receta Médica
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Medicamentos, indicaciones y dispensación</p>
            </div>
            <a href="{{ route('dispensaciones.index') }}"
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
        $inputCls =
            'w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-indigo-500 focus:ring-0 outline-none';
        $thCls = 'px-5 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider';
    @endphp

    <div class="py-8 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

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

        {{-- ============ ESTADO ============ --}}
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <div class="flex-1">
                    <p class="{{ $labelCls }}">Estado de la receta</p>
                    <div class="mt-2">
                        @if ($consulta->dispensada)
                            <span
                                class="inline-flex items-center gap-2 px-3 py-1.5 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-full text-xs font-bold uppercase tracking-wide">
                                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                Dispensada
                            </span>
                        @else
                            <span
                                class="inline-flex items-center gap-2 px-3 py-1.5 bg-amber-50 text-amber-700 border border-amber-100 rounded-full text-xs font-bold uppercase tracking-wide">
                                <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                                Pendiente de dispensar
                            </span>
                        @endif
                    </div>
                    @if ($consulta->dispensada)
                        <p class="text-[11px] font-medium text-slate-400 mt-2">
                            Dispensado por
                            <span
                                class="font-bold text-slate-600">{{ $consulta->dispensadaPor?->nombre_completo ?? '—' }}</span>
                            el {{ $consulta->dispensada_en?->format('d/m/Y H:i') }}
                        </p>
                    @endif
                </div>

                <div class="bg-slate-50 border border-slate-100 rounded-2xl p-3 text-right shrink-0">
                    <p class="text-xs font-extrabold text-slate-700">Receta #{{ $consulta->id }}</p>
                    <p class="text-[11px] font-medium text-slate-400 mt-0.5">
                        {{ $consulta->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>

        {{-- ============ PACIENTE ============ --}}
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-5">
            <div>
                <p class="{{ $labelCls }}">Paciente</p>
                <p class="text-2xl font-black text-slate-800 tracking-tight mt-0.5">
                    {{ $consulta->paciente?->nombre_completo }}
                </p>
            </div>

            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                    <dt class="{{ $labelCls }}">CURP</dt>
                    <dd class="text-xs font-extrabold text-slate-800 mt-1">{{ $consulta->paciente?->curp ?? '—' }}</dd>
                </div>
                <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                    <dt class="{{ $labelCls }}">Edad</dt>
                    <dd class="text-xs font-extrabold text-slate-800 mt-1">{{ $consulta->paciente?->edad ?? '—' }} años
                    </dd>
                </div>
                <div class="bg-rose-50/60 border border-rose-100 rounded-2xl p-3 sm:col-span-2">
                    <dt class="{{ $labelCls }}">Alergias registradas</dt>
                    <dd class="text-xs font-extrabold text-rose-700 mt-1">
                        {{ $consulta->paciente?->alergias ?? 'Ninguna registrada' }}
                    </dd>
                </div>
            </dl>
        </div>

        {{-- ============ MÉDICO ============ --}}
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-4">
            <div>
                <p class="{{ $labelCls }}">Médico prescriptor</p>
                <p class="text-xs font-extrabold text-slate-800 mt-1">
                    {{ $consulta->medico?->nombre_completo ?? '—' }}
                </p>
            </div>

            @if ($consulta->diagnosticoPrincipal)
                <div class="bg-indigo-50/60 border border-indigo-100 rounded-2xl p-4">
                    <p class="text-[11px] font-bold text-indigo-700 uppercase tracking-wider">Diagnóstico principal (Dx)
                    </p>
                    <p class="text-xs font-extrabold text-slate-800 mt-1">
                        {{ $consulta->diagnosticoPrincipal->etiqueta }}
                    </p>
                </div>
            @endif
        </div>

        {{-- ============ MEDICAMENTOS ============ --}}
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="font-extrabold text-slate-800 text-base">Medicamentos recetados</h3>
                <p class="text-[11px] text-slate-400">
                    {{ $consulta->medicamentos->count() }} medicamentos en esta receta
                </p>
            </div>

            @if ($consulta->medicamentos->count())
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-slate-50/70 border-b border-slate-100">
                            <tr>
                                <th class="{{ $thCls }} text-left">Medicamento</th>
                                <th class="{{ $thCls }} text-left">Dosis</th>
                                <th class="{{ $thCls }} text-left">Vía</th>
                                <th class="{{ $thCls }} text-left">Frecuencia</th>
                                <th class="{{ $thCls }} text-left">Duración</th>
                                <th class="{{ $thCls }} text-right">Stock disp.</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($consulta->medicamentos as $m)
                                <tr class="hover:bg-indigo-50/30 transition-colors">
                                    <td class="px-5 py-3.5">
                                        <p class="text-xs font-bold text-slate-800">
                                            {{ $m->nombre }}
                                            @if ($m->concentracion)
                                                <span class="text-slate-400 font-medium">·
                                                    {{ $m->concentracion }}</span>
                                            @endif
                                        </p>
                                        @if ($m->pivot->indicaciones)
                                            <p class="text-[10px] font-medium text-slate-400 mt-0.5">
                                                {{ $m->pivot->indicaciones }}
                                            </p>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span
                                            class="px-2 py-1 bg-slate-100 text-slate-600 rounded-lg text-[11px] font-mono font-bold">
                                            {{ $m->pivot->dosis ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span
                                            class="px-2.5 py-1 bg-indigo-50 text-indigo-700 border border-indigo-100 rounded-full text-[10px] font-bold uppercase tracking-wide">
                                            {{ $m->pivot->via ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 text-xs font-medium text-slate-600">
                                        {{ $m->pivot->frecuencia ?? '—' }}
                                    </td>
                                    <td class="px-5 py-3.5 text-xs font-medium text-slate-600">
                                        {{ $m->pivot->duracion ?? '—' }}
                                    </td>
                                    <td class="px-5 py-3.5 text-right">
                                        @php $stock = $m->stock_total_calculado; @endphp
                                        @if ($stock <= 0)
                                            <span
                                                class="px-2.5 py-1 bg-rose-50 text-rose-700 border border-rose-100 rounded-full text-[10px] font-bold">
                                                {{ $stock }}
                                            </span>
                                        @elseif ($stock <= $m->stock_minimo)
                                            <span
                                                class="px-2.5 py-1 bg-amber-50 text-amber-700 border border-amber-100 rounded-full text-[10px] font-bold">
                                                {{ $stock }}
                                            </span>
                                        @else
                                            <span
                                                class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-full text-[10px] font-bold">
                                                {{ $stock }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-5 py-12 text-center">
                    <p class="text-xs font-semibold text-slate-400">No hay medicamentos registrados en esta receta.</p>
                </div>
            @endif

            @if ($consulta->receta_libre)
                <div class="p-5 border-t border-slate-100">
                    <div class="bg-purple-50/60 border border-purple-100 rounded-2xl p-4">
                        <p class="text-[11px] font-bold text-purple-700 uppercase tracking-wider">
                            Indicaciones / Receta libre
                        </p>
                        <p class="text-xs text-slate-600 leading-relaxed whitespace-pre-line mt-2">
                            {{ $consulta->receta_libre }}
                        </p>
                    </div>
                </div>
            @endif
        </div>

        {{-- ============ DISPENSACIÓN ============ --}}
        @if (!$consulta->dispensada)
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-800 text-base">Acción de dispensación</h3>
                        <p class="text-[11px] text-slate-400">Al confirmar, se descontará el inventario automáticamente
                        </p>
                    </div>
                </div>

                <form action="{{ route('dispensaciones.dispensar', $consulta) }}" method="POST"
                    onsubmit="return confirm('¿Confirmar dispensación? Se descontará del inventario de forma automática.')"
                    class="p-6 space-y-5">
                    @csrf
                    <div>
                        <label class="{{ $labelCls }}">Notas de dispensación (opcional)</label>
                        <textarea name="notas_dispensacion" rows="2" placeholder="Observaciones o comentarios adicionales..."
                            class="{{ $inputCls }} resize-none"></textarea>
                    </div>

                    <div class="flex justify-end pt-5 border-t border-slate-100">
                        <button type="submit"
                            class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Dispensar receta y descontar stock
                        </button>
                    </div>
                </form>
            </div>
        @else
            @if (auth()->user()?->hasRole('administrador'))
                <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-2xl bg-rose-50 flex items-center justify-center text-rose-500 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-extrabold text-slate-800">Zona administrativa</p>
                                <p class="text-[11px] font-medium text-slate-400 mt-0.5">
                                    ¿Necesitas anular esta transacción? Los valores del stock serán devueltos.
                                </p>
                            </div>
                        </div>

                        <form action="{{ route('dispensaciones.revertir', $consulta) }}" method="POST"
                            onsubmit="return confirm('¿Estás seguro de revertir la dispensación? El stock recuperará sus cantidades anteriores.')">
                            @csrf
                            <button type="submit"
                                class="px-4 py-2.5 bg-rose-50 hover:bg-rose-100 active:scale-95 text-rose-700 border border-rose-100 rounded-xl text-xs font-bold transition-all whitespace-nowrap">
                                Revertir dispensación
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        @endif

    </div>
</x-app-layout>
