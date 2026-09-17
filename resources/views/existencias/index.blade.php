<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('inventario', {
            openMovimiento: false,
        });
    });
</script>

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-nexus-primary/10 text-nexus-primary flex items-center justify-center border border-nexus-primary/20 shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                        Existencias de Inventario
                    </h2>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">Gestión de stock, lotes y caducidades de medicamentos</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                {{-- BOTÓN QUE ABRE EL MODAL (usa el store global) --}}
                <button @click="$store.inventario.openMovimiento = true"
                   type="button"
                   class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-nexus-accent bg-nexus-primary hover:bg-slate-900 rounded-2xl transition-all shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nuevo Movimiento
                </button>

                <a href="{{ route('existencias.lotes') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 rounded-2xl transition-all shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                    Ver por lotes
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-100 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- TARJETAS DE ESTADÍSTICAS -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('existencias.index') }}"
                   class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4 hover:shadow-md transition-all group">
                    <div class="w-12 h-12 rounded-2xl bg-slate-50 text-slate-700 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Medicamentos</p>
                        <h4 class="text-xl font-bold text-slate-800">{{ $stats['total_medicamentos'] }}</h4>
                    </div>
                </a>

                <a href="{{ route('existencias.index', ['filtro' => 'bajo']) }}"
                   class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4 hover:shadow-md transition-all group">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Stock bajo</p>
                        <h4 class="text-xl font-bold text-amber-600">{{ $stats['stock_bajo'] }}</h4>
                    </div>
                </a>

                <a href="{{ route('existencias.index', ['filtro' => 'proximo']) }}"
                   class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4 hover:shadow-md transition-all group">
                    <div class="w-12 h-12 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Próx. a caducar</p>
                        <h4 class="text-xl font-bold text-orange-600">{{ $stats['proximos_caducar'] }}</h4>
                    </div>
                </a>

                <a href="{{ route('existencias.index', ['filtro' => 'caducado']) }}"
                   class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4 hover:shadow-md transition-all group">
                    <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Caducados</p>
                        <h4 class="text-xl font-bold text-rose-600">{{ $stats['caducados'] }}</h4>
                    </div>
                </a>
            </div>

            <!-- BUSCADOR Y FILTROS -->
            <div class="bg-white p-4 rounded-3xl border border-slate-100 shadow-sm">
                <form method="GET" action="{{ route('existencias.index') }}" class="flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-1">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input type="text" name="buscar" value="{{ $busqueda }}"
                               placeholder="Buscar por nombre o sustancia..."
                               class="w-full pl-10 pr-4 py-2.5 rounded-2xl border-slate-200 text-sm focus:border-nexus-primary focus:ring-nexus-primary">
                    </div>

                    <select name="filtro" class="rounded-2xl border-slate-200 text-sm focus:border-nexus-primary focus:ring-nexus-primary py-2.5 px-4">
                        <option value="">Todos</option>
                        <option value="bajo" @selected($filtro === 'bajo')>Solo stock bajo</option>
                        <option value="proximo" @selected($filtro === 'proximo')>Próximos a caducar</option>
                        <option value="caducado" @selected($filtro === 'caducado')>Caducados</option>
                    </select>

                    <div class="flex items-center gap-2">
                        <button type="submit" class="flex-1 sm:flex-none px-6 py-2.5 bg-nexus-primary text-nexus-accent text-xs font-bold rounded-2xl hover:bg-slate-900 transition-all shadow-md">
                            Filtrar
                        </button>
                        @if ($busqueda || $filtro)
                            <a href="{{ route('existencias.index') }}"
                               class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold rounded-2xl transition-all text-center">
                                Limpiar
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- TABLA DE EXISTENCIAS -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/70 border-b border-slate-100">
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Medicamento</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Sustancia</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Stock</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Mínimo</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Lotes</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Caducidad</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse ($medicamentos as $med)
                                @php
                                    $stock = $med->stock_total_calculado;
                                    $bajo = $stock <= $med->stock_minimo;

                                    $loteProximo = $med->lotes
                                        ->where('cantidad_disponible', '>', 0)
                                        ->where('activo', true)
                                        ->sortBy('fecha_caducidad')
                                        ->first();
                                @endphp
                                <tr class="hover:bg-slate-50/80 transition-colors group">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-600 font-bold flex items-center justify-center text-xs group-hover:bg-nexus-primary group-hover:text-nexus-accent transition-colors">
                                                {{ strtoupper(substr($med->nombre, 0, 2)) }}
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-800">{{ $med->nombre }} {{ $med->concentracion }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 text-sm text-slate-500 font-medium">
                                        {{ $med->sustancia_activa ?? '—' }}
                                    </td>

                                    <td class="px-6 py-4 text-sm font-bold">
                                        @if ($stock <= 0)
                                            <span class="text-rose-600">{{ $stock }}</span>
                                        @elseif ($bajo)
                                            <span class="text-amber-600">{{ $stock }}</span>
                                        @else
                                            <span class="text-emerald-600">{{ $stock }}</span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 text-sm text-slate-500 font-medium">
                                        {{ $med->stock_minimo }}
                                    </td>

                                    <td class="px-6 py-4 text-sm">
                                        <span class="px-2.5 py-1 bg-slate-100 text-slate-700 rounded-full text-xs font-semibold">
                                            {{ $med->lotes->count() }} activos
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 text-sm">
                                        @if ($loteProximo)
                                            @php
                                                $dias = $loteProximo->dias_para_caducar;
                                                $color = $loteProximo->esta_caducado
                                                    ? 'bg-rose-50 text-rose-700'
                                                    : ($dias <= 30
                                                        ? 'bg-amber-50 text-amber-700'
                                                        : 'bg-emerald-50 text-emerald-700');
                                            @endphp
                                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $color }}">
                                                {{ $loteProximo->fecha_caducidad->format('d/m/Y') }}
                                            </span>
                                            <span class="text-[11px] text-slate-400 block mt-1">
                                                @if ($loteProximo->esta_caducado)
                                                    Caducó hace {{ abs($dias) }} días
                                                @else
                                                    Vence en {{ $dias }} días
                                                @endif
                                            </span>
                                        @else
                                            <span class="text-slate-400 text-xs italic">Sin lotes</span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('existencias.show', $med) }}"
                                           class="inline-flex items-center gap-1 px-3.5 py-1.5 text-xs font-bold text-nexus-secondary bg-nexus-secondary/10 hover:bg-nexus-secondary hover:text-white rounded-xl transition-all">
                                            Ver detalle
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="max-w-xs mx-auto text-center space-y-3">
                                            <div class="w-12 h-12 bg-slate-100 rounded-2xl text-slate-400 flex items-center justify-center mx-auto">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                </svg>
                                            </div>
                                            <p class="text-slate-500 font-medium text-sm">Sin medicamentos en inventario.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($medicamentos->hasPages())
                    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                        {{ $medicamentos->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- MODAL: NUEVO MOVIMIENTO (Controlado por $store.inventario.openMovimiento) --}}
    {{-- SUPUESTOS A CONFIRMAR: ruta 'existencias.movimientos.store', campos medicamento_id, tipo, codigo_lote, fecha_caducidad, cantidad, motivo --}}
    <template x-teleport="body">
        <div x-show="$store.inventario.openMovimiento" x-cloak x-data="{ tipo: '{{ old('tipo', '') }}' }"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
            <div @click.away="$store.inventario.openMovimiento = false"
                 class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-xl border border-slate-100 space-y-4 text-left max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center pb-3 border-b border-slate-100">
                    <h3 class="text-lg font-bold text-slate-900">Nuevo Movimiento de Inventario</h3>
                    <button @click="$store.inventario.openMovimiento = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                @if ($errors->any())
                    <div class="p-3 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('existencias.movimientos.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Medicamento *</label>
                        <select name="medicamento_id" required class="w-full border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-nexus-primary">
                            <option value="">— Selecciona —</option>
                            @foreach ($medicamentos as $medOpt)
                                <option value="{{ $medOpt->id }}" @selected(old('medicamento_id') == $medOpt->id)>
                                    {{ $medOpt->nombre }} {{ $medOpt->concentracion }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Tipo de movimiento *</label>
                        <select name="tipo" x-model="tipo" required class="w-full border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-nexus-primary">
                            <option value="">— Selecciona —</option>
                            <option value="entrada" @selected(old('tipo') == 'entrada')>Entrada</option>
                            <option value="salida" @selected(old('tipo') == 'salida')>Salida</option>
                            <option value="ajuste" @selected(old('tipo') == 'ajuste')>Ajuste</option>
                        </select>
                    </div>

                    {{-- Datos de lote existente: para salida o ajuste --}}
                    <div x-show="tipo === 'salida' || tipo === 'ajuste'" x-cloak>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Código de lote (referencia)</label>
                        <input type="text" name="codigo_lote" value="{{ old('codigo_lote') }}" placeholder="LOTE-0001"
                               class="w-full border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-nexus-primary">
                    </div>

                    {{-- Datos de lote nuevo: para entrada --}}
                    <div x-show="tipo === 'entrada'" x-cloak class="space-y-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <p class="text-xs font-semibold text-slate-500">Datos del lote que ingresa</p>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">Código de lote</label>
                                <input type="text" name="codigo_lote_nuevo" value="{{ old('codigo_lote_nuevo') }}"
                                       class="w-full border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-nexus-primary">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">Fecha de caducidad</label>
                                <input type="date" name="fecha_caducidad" value="{{ old('fecha_caducidad') }}"
                                       class="w-full border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-nexus-primary">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Cantidad *</label>
                        <input type="number" name="cantidad" min="1" value="{{ old('cantidad') }}" required
                               class="w-full border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-nexus-primary">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Motivo</label>
                        <textarea name="motivo" rows="2"
                                  class="w-full border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-nexus-primary">{{ old('motivo') }}</textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                        <button @click="$store.inventario.openMovimiento = false" type="button" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-sm font-medium">Cancelar</button>
                        <button type="submit" class="px-4 py-2 bg-nexus-primary text-nexus-accent rounded-xl text-sm font-medium">Guardar movimiento</button>
                    </div>
                </form>
            </div>
        </div>
    </template>

</x-app-layout>