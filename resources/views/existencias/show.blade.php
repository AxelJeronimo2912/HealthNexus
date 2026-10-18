<div class="p-2">
    {{-- Encabezado con Botón Cerrar --}}
    <div class="flex justify-between items-center gap-3 mb-5 border-b border-slate-100 pb-4">
        <div class="min-w-0">
            <h2 class="font-extrabold text-2xl text-slate-800 leading-tight truncate">
                {{ $medicamento->nombre }} {{ $medicamento->concentracion }}
            </h2>
            <p class="text-xs text-slate-400 mt-0.5">Lotes activos y movimientos recientes del medicamento</p>
        </div>
        <button type="button" onclick="Alpine.store('inventario').openDetalle = false"
            class="w-9 h-9 flex items-center justify-center rounded-full bg-slate-50 text-slate-400 hover:text-rose-500 hover:bg-rose-50 transition-colors text-xl leading-none border border-slate-200 shrink-0">
            &times;
        </button>
    </div>

    @php
        $labelCls = 'text-[11px] font-bold text-slate-400 uppercase tracking-wider';
        $thCls = 'px-5 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider';
    @endphp

    <div class="space-y-6">

        {{-- ============ RESUMEN ============ --}}
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
            <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-5">
                <div class="flex-1">
                    <p class="{{ $labelCls }}">Sustancia activa</p>
                    <p class="text-xs font-extrabold text-slate-800 mt-1">
                        {{ $medicamento->sustancia_activa ?? '—' }}
                    </p>

                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Presentación</p>
                            <p class="text-xs font-extrabold text-slate-800 mt-0.5">
                                {{ $medicamento->presentacion ?? '—' }}</p>
                        </div>
                        <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Vía de
                                administración</p>
                            <p class="text-xs font-extrabold text-slate-800 mt-0.5">
                                {{ $medicamento->via_administracion ?? '—' }}</p>
                        </div>
                    </div>
                </div>

                <div class="md:max-w-[240px] md:text-right space-y-3">
                    @php
                        $stock = $medicamento->stock_total_calculado;
                        if ($stock <= 0) {
                            $stockBoxCls = 'bg-rose-50/60 border-rose-100';
                            $stockLabelCls = 'text-rose-500/70';
                            $stockValCls = 'text-rose-600';
                        } elseif ($stock <= $medicamento->stock_minimo) {
                            $stockBoxCls = 'bg-amber-50/60 border-amber-100';
                            $stockLabelCls = 'text-amber-600/70';
                            $stockValCls = 'text-amber-600';
                        } else {
                            $stockBoxCls = 'bg-emerald-50/60 border-emerald-100';
                            $stockLabelCls = 'text-emerald-600/70';
                            $stockValCls = 'text-emerald-600';
                        }
                    @endphp

                    <div class="{{ $stockBoxCls }} border rounded-2xl p-4">
                        <p class="text-[11px] font-bold uppercase tracking-wider {{ $stockLabelCls }}">Stock total</p>
                        <p class="text-4xl font-black tracking-tight mt-1 {{ $stockValCls }}">
                            {{ $stock }}
                        </p>
                        <p class="text-[10px] font-medium text-slate-400 mt-1">
                            Mínimo: {{ $medicamento->stock_minimo }} · Máximo: {{ $medicamento->stock_maximo }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============ LOTES ACTIVOS ============ --}}
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex flex-wrap justify-between items-center gap-3">
                <div>
                    <h3 class="font-extrabold text-slate-800 text-base">Lotes activos</h3>
                    <p class="text-[11px] text-slate-400">{{ $lotes->count() }} lotes con disponibilidad</p>
                </div>
                {{-- Botón que abre el modal de lotes (pasa el parámetro del medicamento) --}}

            </div>

            @if ($lotes->isEmpty())
                <div class="px-5 py-12 text-center">
                    <p class="text-xs font-semibold text-slate-400">Sin lotes registrados.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-slate-50/70 border-b border-slate-100">
                            <tr>
                                <th class="{{ $thCls }} text-left">Lote</th>
                                <th class="{{ $thCls }} text-left">Caducidad</th>
                                <th class="{{ $thCls }} text-right">Inicial</th>
                                <th class="{{ $thCls }} text-right">Disponible</th>
                                <th class="{{ $thCls }} text-left">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($lotes as $lote)
                                <tr class="hover:bg-indigo-50/30 transition-colors">
                                    <td class="px-5 py-3.5">
                                        <span
                                            class="px-2 py-1 bg-slate-100 text-slate-600 rounded-lg text-[11px] font-mono font-bold">
                                            {{ $lote->codigo_lote ?? 'LOTE-' . $lote->id }}
                                        </span>
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
                                            class="px-2 py-1 bg-slate-100 text-slate-600 rounded-lg text-[11px] font-mono font-bold">
                                            {{ $lote->cantidad_inicial }}
                                        </span>
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
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- ============ MOVIMIENTOS RECIENTES ============ --}}
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="font-extrabold text-slate-800 text-base">Movimientos recientes</h3>
                <p class="text-[11px] text-slate-400">Entradas, salidas y ajustes del medicamento</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-slate-50/70 border-b border-slate-100">
                        <tr>
                            <th class="{{ $thCls }} text-left">Fecha</th>
                            <th class="{{ $thCls }} text-left">Tipo</th>
                            <th class="{{ $thCls }} text-left">Lote</th>
                            <th class="{{ $thCls }} text-right">Cantidad</th>
                            <th class="{{ $thCls }} text-left">Motivo</th>
                            <th class="{{ $thCls }} text-left">Usuario</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($movimientos as $mov)
                            <tr class="hover:bg-indigo-50/30 transition-colors">
                                <td class="px-5 py-3.5 text-[11px] font-medium text-slate-400 whitespace-nowrap">
                                    {{ $mov->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <span
                                        class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide border {{ $mov->tipo_color }}">
                                        {{ $mov->tipo_label }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    @if ($mov->lote?->codigo_lote)
                                        <span
                                            class="px-2 py-1 bg-slate-100 text-slate-600 rounded-lg text-[11px] font-mono font-bold">
                                            {{ $mov->lote->codigo_lote }}
                                        </span>
                                    @else
                                        <span class="text-slate-300 text-xs">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <span
                                        class="px-2 py-1 rounded-lg text-[11px] font-mono font-bold {{ $mov->cantidad >= 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                        {{ $mov->cantidad >= 0 ? '+' : '' }}{{ $mov->cantidad }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <p class="text-xs font-medium text-slate-600">{{ $mov->motivo ?? '—' }}</p>
                                </td>
                                <td class="px-5 py-3.5">
                                    <p class="text-xs font-medium text-slate-600">
                                        {{ $mov->user?->nombre_completo ?? 'Sistema' }}
                                    </p>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center">
                                    <p class="text-xs font-semibold text-slate-400">Sin movimientos.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    // Función para abrir el modal de lotes desde el modal de detalle sin cerrarlo
    function abrirModalLotesDesdeDetalle(url) {
        Alpine.store('inventario').openLotes = true;
        document.getElementById('modal-lotes-content').innerHTML =
            '<div class="p-10 text-center text-slate-400"><svg class="animate-spin h-6 w-6 mx-auto text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></div>';

        fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.text())
            .then(html => document.getElementById('modal-lotes-content').innerHTML = html);
    }
</script>
