<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('movimientosStore', {
            openDetalle: false,
        });
    });
</script>

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-3">
            <div class="min-w-0">
                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight truncate">
                    Movimientos de Inventario
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Entradas, salidas y ajustes del inventario de medicamentos</p>
            </div>
        </div>
    </x-slot>

    @php
        $inputCls =
            'w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-brand-500 focus:ring-0 outline-none';
        $labelCls = 'block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5';
        $thCls = 'px-5 py-3.5 text-xs font-bold text-slate-400 uppercase tracking-wider';
    @endphp

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- ============ ESTADÍSTICAS ============ --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-2xl ring-1 ring-slate-200/70 shadow-card">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total movimientos</p>
                <p class="text-3xl font-black text-slate-800 tracking-tight mt-1">{{ $stats['total_movimientos'] }}</p>
            </div>
            <div class="bg-white p-5 rounded-2xl ring-1 ring-slate-200/70 shadow-card">
                <p class="text-xs font-bold text-emerald-600/70 uppercase tracking-wider">Entradas hoy</p>
                <p class="text-3xl font-black text-emerald-600 tracking-tight mt-1">{{ $stats['entradas_hoy'] }}</p>
            </div>
            <div class="bg-white p-5 rounded-2xl ring-1 ring-slate-200/70 shadow-card">
                <p class="text-xs font-bold text-rose-600/70 uppercase tracking-wider">Salidas hoy</p>
                <p class="text-3xl font-black text-rose-600 tracking-tight mt-1">{{ $stats['salidas_hoy'] }}</p>
            </div>
            <div class="bg-white p-5 rounded-2xl ring-1 ring-slate-200/70 shadow-card">
                <p class="text-xs font-bold text-amber-600/70 uppercase tracking-wider">Ajustes del mes</p>
                <p class="text-3xl font-black text-amber-600 tracking-tight mt-1">{{ $stats['ajustes_mes'] }}</p>
            </div>
        </div>

        {{-- ============ FILTROS ============ --}}
        <div class="bg-white rounded-2xl ring-1 ring-slate-200/70 shadow-card p-5">
            <form method="GET" action="{{ route('movimientos.index') }}"
                class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">

                <div class="md:col-span-2">
                    <label class="{{ $labelCls }}">Medicamento</label>
                    <select name="medicamento_id" class="{{ $inputCls }}">
                        <option value="">Todos</option>
                        @foreach ($medicamentos as $m)
                            <option value="{{ $m->id }}" @selected($medicamentoId == $m->id)>
                                {{ $m->nombre }} {{ $m->concentracion }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="{{ $labelCls }}">Tipo</label>
                    <select name="tipo" class="{{ $inputCls }}">
                        <option value="">Todos</option>
                        <option value="entrada" @selected($tipo === 'entrada')>Entrada</option>
                        <option value="salida" @selected($tipo === 'salida')>Salida</option>
                        <option value="ajuste" @selected($tipo === 'ajuste')>Ajuste</option>
                        <option value="devolucion" @selected($tipo === 'devolucion')>Devolución</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="{{ $labelCls }}">Usuario</label>
                    <select name="user_id" class="{{ $inputCls }}">
                        <option value="">Todos</option>
                        @foreach ($usuarios as $u)
                            <option value="{{ $u->id }}" @selected($userId == $u->id)>
                                {{ $u->nombre }} {{ $u->apellido_paterno }}
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
                        class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all">
                        Filtrar
                    </button>
                    <a href="{{ route('movimientos.index') }}"
                        class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition-all">
                        Limpiar filtros
                    </a>
                </div>
            </form>
        </div>

        {{-- ============ TABLA ============ --}}
        <div class="bg-white rounded-2xl ring-1 ring-slate-200/70 shadow-card overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="font-extrabold text-slate-800 text-base">Movimientos registrados</h3>
                <p class="text-xs text-slate-400">{{ $movimientos->total() }} movimientos en total</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-slate-50/70 border-b border-slate-100">
                        <tr>
                            <th class="{{ $thCls }} text-left">Fecha</th>
                            <th class="{{ $thCls }} text-left">Tipo</th>
                            <th class="{{ $thCls }} text-left">Medicamento</th>
                            <th class="{{ $thCls }} text-left">Lote</th>
                            <th class="{{ $thCls }} text-right">Cantidad</th>
                            <th class="{{ $thCls }} text-right">Stock</th>
                            <th class="{{ $thCls }} text-left">Motivo</th>
                            <th class="{{ $thCls }} text-left">Usuario</th>
                            <th class="px-5 py-3.5"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($movimientos as $mov)
                            <tr class="hover:bg-brand-50/30 transition-colors">
                                <td class="px-5 py-3.5 text-xs font-medium text-slate-400 whitespace-nowrap">
                                    {{ $mov->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <span
                                        class="px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wide border {{ $mov->tipo_color }}">
                                        {{ $mov->tipo_label }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <p class="text-xs font-bold text-slate-800">{{ $mov->medicamento?->nombre ?? '—' }}
                                    </p>
                                    @if ($mov->medicamento?->concentracion)
                                        <p class="text-xs font-medium text-slate-400 mt-0.5">
                                            {{ $mov->medicamento->concentracion }}
                                        </p>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    @if ($mov->lote?->codigo_lote)
                                        <span
                                            class="px-2 py-1 bg-slate-100 text-slate-600 rounded-lg text-xs font-mono font-bold">
                                            {{ $mov->lote->codigo_lote }}
                                        </span>
                                    @else
                                        <span class="text-slate-300 text-xs">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <span
                                        class="px-2 py-1 rounded-lg text-xs font-mono font-bold {{ $mov->cantidad >= 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                        {{ $mov->cantidad >= 0 ? '+' : '' }}{{ $mov->cantidad }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <span
                                        class="text-xs font-medium text-slate-400">{{ $mov->stock_anterior }}</span>
                                    <span class="text-slate-300 mx-0.5">→</span>
                                    <span class="text-xs font-extrabold text-slate-800">{{ $mov->stock_nuevo }}</span>
                                </td>
                                <td class="px-5 py-3.5 max-w-xs">
                                    <p class="text-xs font-medium text-slate-600 truncate">{{ $mov->motivo ?? '—' }}
                                    </p>
                                </td>
                                <td class="px-5 py-3.5">
                                    <p class="text-xs font-medium text-slate-600">
                                        {{ $mov->user?->nombre_completo ?? 'Sistema' }}
                                    </p>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex justify-end">
                                        {{-- BOTÓN MODIFICADO PARA ABRIR EL MODAL --}}
                                        <button type="button"
                                            onclick="abrirModalDetalleMovimiento('{{ route('movimientos.show', $mov) }}')"
                                            class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold transition-all">
                                            Ver
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-5 py-12 text-center">
                                    <p class="text-xs font-semibold text-slate-400">
                                        Sin movimientos para los filtros seleccionados.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">{{ $movimientos->links() }}</div>
    </div>

    {{-- MODAL: DETALLE DEL MOVIMIENTO --}}
    <template x-teleport="body">
        <div x-show="$store.movimientosStore.openDetalle" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
            <div @click.away="$store.movimientosStore.openDetalle = false"
                class="bg-white rounded-2xl max-w-3xl w-full p-6 shadow-xl border border-slate-100 max-h-[90vh] overflow-y-auto relative">
                <div id="modal-detalle-movimiento-content">
                    <div class="p-10 text-center text-slate-400">Cargando detalle...</div>
                </div>
            </div>
        </div>
    </template>

    {{-- SCRIPT PARA CARGAR EL MODAL POR AJAX --}}
    <script>
        function abrirModalDetalleMovimiento(url) {
            Alpine.store('movimientosStore').openDetalle = true;
            document.getElementById('modal-detalle-movimiento-content').innerHTML =
                '<div class="p-10 text-center text-slate-400"><svg class="animate-spin h-6 w-6 mx-auto text-brand-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></div>';

            fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.text())
                .then(html => document.getElementById('modal-detalle-movimiento-content').innerHTML = html);
        }
    </script>
</x-app-layout>
