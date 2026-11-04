<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('movimientosStore', {
            openDetalle: false,
            loading: false,
        });
    });
</script>

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-start gap-4">
            <div class="min-w-0 flex-1">
                <nav class="flex items-center gap-1.5 text-xs text-slate-400 mb-1.5" aria-label="Breadcrumb">
                    <a href="{{ route('existencias.index') }}" class="hover:text-brand-600 transition-colors">Inventario</a>
                    <svg class="w-3 h-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                    </svg>
                    <span class="text-slate-600 font-medium">Movimientos</span>
                </nav>
                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight truncate">
                    Historial de movimientos
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Entradas, salidas, ajustes y devoluciones del inventario</p>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <span class="hidden sm:inline-flex items-center gap-1.5 px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-600">
                    <x-heroicon-o-document-arrow-down class="w-4 h-4 text-slate-400" />
                    {{ $movimientos->total() }} registros
                </span>
            </div>
        </div>
    </x-slot>

    @php
        $inputCls = 'w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition';
        $labelCls = 'block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5';
        $thCls = 'px-5 py-3.5 text-xs font-bold text-slate-400 uppercase tracking-wider';

        // Chips rápidos de rango de fecha
        $rangos = [
            'hoy'    => ['label' => 'Hoy',      'desde' => now()->toDateString(), 'hasta' => now()->toDateString()],
            '7d'     => ['label' => '7 días',   'desde' => now()->subDays(7)->toDateString(), 'hasta' => now()->toDateString()],
            '30d'    => ['label' => '30 días',  'desde' => now()->subDays(30)->toDateString(), 'hasta' => now()->toDateString()],
            'mes'    => ['label' => 'Este mes', 'desde' => now()->startOfMonth()->toDateString(), 'hasta' => now()->toDateString()],
        ];

        $rangoActivo = null;
        if ($desde && $hasta) {
            foreach ($rangos as $key => $r) {
                if ($desde === $r['desde'] && $hasta === $r['hasta']) {
                    $rangoActivo = $key;
                    break;
                }
            }
        }

        $tipos = [
            'entrada'    => ['label' => 'Entradas',    'color' => 'emerald', 'icon' => 'arrow-down-tray'],
            'salida'     => ['label' => 'Salidas',     'color' => 'rose',    'icon' => 'arrow-up-tray'],
            'ajuste'     => ['label' => 'Ajustes',     'color' => 'amber',   'icon' => 'adjustments-horizontal'],
            'devolucion' => ['label' => 'Devoluciones','color' => 'sky',     'icon' => 'arrow-uturn-left'],
        ];

        $filtrosActivos = collect([
            $medicamentoId ? 'medicamento' : null,
            $tipo ? 'tipo' : null,
            $userId ? 'usuario' : null,
            $loteId ?? null ? 'lote' : null,
            $desde ? 'desde' : null,
            $hasta ? 'hasta' : null,
        ])->filter()->count();
    @endphp

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- ============ ESTADÍSTICAS ============ --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-2xl ring-1 ring-slate-200/70 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total</p>
                    <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center">
                        <x-heroicon-o-rectangle-stack class="w-4 h-4" />
                    </div>
                </div>
                <p class="text-3xl font-black text-slate-800 tracking-tight">{{ $stats['total_movimientos'] }}</p>
                <p class="text-xs text-slate-500 mt-1">movimientos registrados</p>
            </div>

            <div class="bg-white p-5 rounded-2xl ring-1 ring-slate-200/70 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs font-bold text-emerald-600/70 uppercase tracking-wider">Entradas hoy</p>
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
                    </div>
                </div>
                <p class="text-3xl font-black text-emerald-600 tracking-tight">{{ $stats['entradas_hoy'] }}</p>
                <p class="text-xs text-slate-500 mt-1">ingresos al almacén</p>
            </div>

            <div class="bg-white p-5 rounded-2xl ring-1 ring-slate-200/70 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs font-bold text-rose-600/70 uppercase tracking-wider">Salidas hoy</p>
                    <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                        <x-heroicon-o-arrow-up-tray class="w-4 h-4" />
                    </div>
                </div>
                <p class="text-3xl font-black text-rose-600 tracking-tight">{{ $stats['salidas_hoy'] }}</p>
                <p class="text-xs text-slate-500 mt-1">egresos del almacén</p>
            </div>

            <div class="bg-white p-5 rounded-2xl ring-1 ring-slate-200/70 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs font-bold text-amber-600/70 uppercase tracking-wider">Ajustes del mes</p>
                    <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                        <x-heroicon-o-adjustments-horizontal class="w-4 h-4" />
                    </div>
                </div>
                <p class="text-3xl font-black text-amber-600 tracking-tight">{{ $stats['ajustes_mes'] }}</p>
                <p class="text-xs text-slate-500 mt-1">correcciones de inventario</p>
            </div>
        </div>

        {{-- ============ FILTROS ============ --}}
        <div class="bg-white rounded-2xl ring-1 ring-slate-200/70 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-funnel class="w-4 h-4 text-slate-400" />
                    <h3 class="font-extrabold text-slate-800 text-sm">Filtros</h3>
                    @if ($filtrosActivos > 0)
                        <span class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full bg-brand-50 text-brand-700 text-[11px] font-bold ring-1 ring-inset ring-brand-100">
                            {{ $filtrosActivos }}
                        </span>
                    @endif
                </div>
                @if ($filtrosActivos > 0)
                    <a href="{{ route('movimientos.index') }}"
                       class="text-xs font-bold text-slate-500 hover:text-rose-600 transition-colors inline-flex items-center gap-1">
                        <x-heroicon-o-x-mark class="w-3.5 h-3.5" />
                        Limpiar todo
                    </a>
                @endif
            </div>

            <div class="p-5 space-y-4">

                {{-- Chips rápidos por tipo --}}
                <div>
                    <label class="{{ $labelCls }}">Tipo de movimiento</label>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ request()->fullUrlWithQuery(['tipo' => null, 'page' => null]) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold border transition
                                  {{ !$tipo
                                      ? 'bg-slate-800 text-white border-slate-800'
                                      : 'bg-white text-slate-600 border-slate-200 hover:border-slate-300 hover:bg-slate-50' }}">
                            <x-heroicon-o-squares-2x2 class="w-3.5 h-3.5" />
                            Todos
                        </a>
                        @foreach ($tipos as $key => $t)
                            <a href="{{ request()->fullUrlWithQuery(['tipo' => $key, 'page' => null]) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold border transition
                                      {{ $tipo === $key
                                          ? 'bg-' . $t['color'] . '-600 text-white border-' . $t['color'] . '-600'
                                          : 'bg-white text-slate-600 border-slate-200 hover:border-slate-300 hover:bg-slate-50' }}">
                                <x-dynamic-component :component="'heroicon-o-' . $t['icon']" class="w-3.5 h-3.5" />
                                {{ $t['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- Chips rápidos por fecha --}}
                <div>
                    <label class="{{ $labelCls }}">Rango de fecha</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($rangos as $key => $r)
                            <a href="{{ request()->fullUrlWithQuery(['desde' => $r['desde'], 'hasta' => $r['hasta'], 'page' => null]) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold border transition
                                      {{ $rangoActivo === $key
                                          ? 'bg-brand-600 text-white border-brand-600'
                                          : 'bg-white text-slate-600 border-slate-200 hover:border-slate-300 hover:bg-slate-50' }}">
                                <x-heroicon-o-calendar class="w-3.5 h-3.5" />
                                {{ $r['label'] }}
                            </a>
                        @endforeach
                        @if ($rangoActivo === null && ($desde || $hasta))
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-brand-600 text-white border border-brand-600">
                                <x-heroicon-o-calendar class="w-3.5 h-3.5" />
                                Personalizado
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Formulario detallado --}}
                <form method="GET" action="{{ route('movimientos.index') }}"
                      class="grid grid-cols-1 md:grid-cols-12 gap-3 pt-4 border-t border-slate-100">

                    <div class="md:col-span-4">
                        <label class="{{ $labelCls }}">Medicamento</label>
                        <select name="medicamento_id" class="{{ $inputCls }}">
                            <option value="">Todos los medicamentos</option>
                            @foreach ($medicamentos as $m)
                                <option value="{{ $m->id }}" @selected($medicamentoId == $m->id)>
                                    {{ $m->nombre }} {{ $m->concentracion }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-3">
                        <label class="{{ $labelCls }}">Lote</label>
                        <select name="lote_id" class="{{ $inputCls }}">
                            <option value="">Todos los lotes</option>
                            @foreach ($lotes ?? [] as $l)
                                <option value="{{ $l->id }}" @selected(($loteId ?? null) == $l->id)>
                                    {{ $l->codigo_lote }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-3">
                        <label class="{{ $labelCls }}">Usuario</label>
                        <select name="user_id" class="{{ $inputCls }}">
                            <option value="">Todos los usuarios</option>
                            @foreach ($usuarios as $u)
                                <option value="{{ $u->id }}" @selected($userId == $u->id)>
                                    {{ $u->nombre }} {{ $u->apellido_paterno }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-1">
                        <label class="{{ $labelCls }}">Desde</label>
                        <input type="date" name="desde" value="{{ $desde }}" class="{{ $inputCls }}">
                    </div>

                    <div class="md:col-span-1">
                        <label class="{{ $labelCls }}">Hasta</label>
                        <input type="date" name="hasta" value="{{ $hasta }}" class="{{ $inputCls }}">
                    </div>

                    {{-- Preservar tipo si ya se eligió por chip --}}
                    @if ($tipo)
                        <input type="hidden" name="tipo" value="{{ $tipo }}">
                    @endif

                    <div class="md:col-span-12 flex flex-wrap gap-2 pt-2">
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-slate-800 hover:bg-slate-900 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all">
                            <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                            Aplicar filtros
                        </button>
                        <a href="{{ route('movimientos.index') }}"
                           class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition-all">
                            <x-heroicon-o-arrow-path class="w-4 h-4" />
                            Limpiar filtros
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- ============ TABLA ============ --}}
        <div class="bg-white rounded-2xl ring-1 ring-slate-200/70 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h3 class="font-extrabold text-slate-800 text-base">Movimientos registrados</h3>
                    <p class="text-xs text-slate-400">
                        Mostrando
                        <span class="font-bold text-slate-600">{{ $movimientos->firstItem() ?? 0 }}–{{ $movimientos->lastItem() ?? 0 }}</span>
                        de <span class="font-bold text-slate-600">{{ $movimientos->total() }}</span>
                        @if ($filtrosActivos > 0)
                            <span class="text-slate-400">· filtrados</span>
                        @endif
                    </p>
                </div>
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
                                    <div>{{ $mov->created_at->format('d/m/Y') }}</div>
                                    <div class="text-[11px] text-slate-300">{{ $mov->created_at->format('H:i') }}</div>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide border {{ $mov->tipo_color }}">
                                        {{ $mov->tipo_label }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <p class="text-xs font-bold text-slate-800">{{ $mov->medicamento?->nombre ?? '—' }}</p>
                                    @if ($mov->medicamento?->concentracion)
                                        <p class="text-[11px] font-medium text-slate-400 mt-0.5">
                                            {{ $mov->medicamento->concentracion }}
                                        </p>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    @if ($mov->lote?->codigo_lote)
                                        <a href="{{ request()->fullUrlWithQuery(['lote_id' => $mov->lote->id, 'page' => null]) }}"
                                           class="inline-block px-2 py-1 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg text-[11px] font-mono font-bold transition-colors"
                                           title="Filtrar por este lote">
                                            {{ $mov->lote->codigo_lote }}
                                        </a>
                                    @else
                                        <span class="text-slate-300 text-xs">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <span class="px-2 py-1 rounded-lg text-xs font-mono font-bold {{ $mov->cantidad >= 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                        {{ $mov->cantidad >= 0 ? '+' : '' }}{{ $mov->cantidad }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                    <span class="text-xs font-medium text-slate-400">{{ $mov->stock_anterior }}</span>
                                    <span class="text-slate-300 mx-0.5">→</span>
                                    <span class="text-xs font-extrabold text-slate-800">{{ $mov->stock_nuevo }}</span>
                                </td>
                                <td class="px-5 py-3.5 max-w-xs">
                                    <p class="text-xs font-medium text-slate-600 truncate" title="{{ $mov->motivo }}">
                                        {{ $mov->motivo ?? '—' }}
                                    </p>
                                </td>
                                <td class="px-5 py-3.5">
                                    <p class="text-xs font-medium text-slate-600">
                                        {{ $mov->user?->nombre_completo ?? 'Sistema' }}
                                    </p>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex justify-end">
                                        <button type="button"
                                                onclick="abrirModalDetalleMovimiento('{{ route('movimientos.show', $mov) }}')"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-100 hover:bg-slate-800 hover:text-white text-slate-700 rounded-lg text-xs font-bold transition-all active:scale-95">
                                            <x-heroicon-o-eye class="w-3.5 h-3.5" />
                                            Ver
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-5 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mb-3">
                                            <x-heroicon-o-archive-box-x-mark class="w-7 h-7" />
                                        </div>
                                        <p class="text-sm font-semibold text-slate-700">
                                            Sin movimientos para los filtros seleccionados
                                        </p>
                                        <p class="text-xs text-slate-500 mt-1 max-w-sm">
                                            Ajusta los filtros o limpia la búsqueda para ver todos los movimientos registrados.
                                        </p>
                                        @if ($filtrosActivos > 0)
                                            <a href="{{ route('movimientos.index') }}"
                                               class="mt-4 inline-flex items-center gap-1.5 px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-xs font-bold transition-all">
                                                <x-heroicon-o-arrow-path class="w-4 h-4" />
                                                Limpiar filtros
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($movimientos->hasPages())
                <div class="px-5 py-4 border-t border-slate-100">
                    {{ $movimientos->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- ============ MODAL: DETALLE DEL MOVIMIENTO ============ --}}
    <template x-teleport="body">
        <div x-show="$store.movimientosStore.openDetalle" x-cloak
             @keydown.escape.window="$store.movimientosStore.openDetalle = false"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm"
             role="dialog" aria-modal="true">
            <div @click.away="$store.movimientosStore.openDetalle = false"
                 class="bg-white rounded-2xl max-w-3xl w-full shadow-2xl border border-slate-100 max-h-[90vh] overflow-y-auto relative">

                {{-- Botón cerrar flotante --}}
                <button type="button"
                        @click="$store.movimientosStore.openDetalle = false"
                        class="absolute top-4 right-4 z-10 w-9 h-9 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-700 flex items-center justify-center transition"
                        aria-label="Cerrar">
                    <x-heroicon-o-x-mark class="w-5 h-5" />
                </button>

                <div id="modal-detalle-movimiento-content">
                    <div class="p-10 text-center text-slate-400">Cargando detalle...</div>
                </div>
            </div>
        </div>
    </template>

    {{-- SCRIPT PARA CARGAR EL MODAL POR AJAX --}}
    <script>
        function abrirModalDetalleMovimiento(url) {
            const store = Alpine.store('movimientosStore');
            store.openDetalle = true;
            store.loading = true;

            const container = document.getElementById('modal-detalle-movimiento-content');
            container.innerHTML = `
                <div class="p-16 text-center text-slate-400">
                    <svg class="animate-spin h-7 w-7 mx-auto text-brand-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="text-xs mt-3">Cargando detalle…</p>
                </div>`;

            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(res => {
                    if (!res.ok) throw new Error('Error ' + res.status);
                    return res.text();
                })
                .then(html => { container.innerHTML = html; })
                .catch(err => {
                    container.innerHTML = `
                        <div class="p-16 text-center">
                            <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center mx-auto mb-3">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <p class="text-sm font-semibold text-slate-700">No se pudo cargar el detalle</p>
                            <p class="text-xs text-slate-500 mt-1">${err.message}</p>
                        </div>`;
                })
                .finally(() => { store.loading = false; });
        }
    </script>
</x-app-layout>