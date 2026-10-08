<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-3">
            <div class="min-w-0">
                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight truncate">
                    Servicios Hospitalarios
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Catálogo de servicios, precios y personal asignado</p>
            </div>
            @can('servicios.ver')
                <a href="{{ route('servicios.create') }}"
                    class="bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white px-5 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 shadow-sm transition-all shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    Nuevo Servicio
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
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total</p>
                <p class="text-3xl font-black text-slate-800 tracking-tight mt-1">{{ $stats['total'] }}</p>
            </div>
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm">
                <p class="text-[11px] font-bold text-emerald-600/70 uppercase tracking-wider">Activos</p>
                <p class="text-3xl font-black text-emerald-600 tracking-tight mt-1">{{ $stats['activos'] }}</p>
            </div>
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm">
                <p class="text-[11px] font-bold text-indigo-600/70 uppercase tracking-wider">Tipos</p>
                <p class="text-3xl font-black text-indigo-600 tracking-tight mt-1">{{ $stats['tipos'] }}</p>
            </div>
        </div>

        {{-- ============ FILTROS ============ --}}
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-5">
            <form method="GET" class="flex gap-2 flex-wrap items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="{{ $labelCls }}">Buscar</label>
                    <input type="text" name="buscar" value="{{ $busqueda }}"
                        placeholder="Nombre, código o ubicación" class="{{ $inputCls }}">
                </div>
                <div class="min-w-[180px]">
                    <label class="{{ $labelCls }}">Tipo</label>
                    <select name="tipo" class="{{ $inputCls }}">
                        <option value="">Todos los tipos</option>
                        <option value="consulta_externa" @selected($tipo === 'consulta_externa')>Consulta Externa</option>
                        <option value="urgencias" @selected($tipo === 'urgencias')>Urgencias</option>
                        <option value="hospitalizacion" @selected($tipo === 'hospitalizacion')>Hospitalización</option>
                        <option value="quirofano" @selected($tipo === 'quirofano')>Quirófano</option>
                        <option value="farmacia" @selected($tipo === 'farmacia')>Farmacia</option>
                        <option value="enfermeria" @selected($tipo === 'enfermeria')>Enfermería</option>
                        <option value="laboratorio" @selected($tipo === 'laboratorio')>Laboratorio</option>
                        <option value="imagenologia" @selected($tipo === 'imagenologia')>Imagenología</option>
                        <option value="otro" @selected($tipo === 'otro')>Otro</option>
                    </select>
                </div>
                <button type="submit"
                    class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all">
                    Filtrar
                </button>
                @if ($busqueda || $tipo)
                    <a href="{{ route('servicios.index') }}"
                        class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition-all">
                        Limpiar
                    </a>
                @endif
            </form>
        </div>

        {{-- ============ TABLA ============ --}}
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="font-extrabold text-slate-800 text-base">Catálogo de servicios</h3>
                <p class="text-[11px] text-slate-400">{{ $servicios->total() }} servicios registrados</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-slate-50/70 border-b border-slate-100">
                        <tr>
                            <th class="{{ $thCls }} text-left">Código</th>
                            <th class="{{ $thCls }} text-left">Nombre</th>
                            <th class="{{ $thCls }} text-left">Tipo</th>
                            <th class="{{ $thCls }} text-left">Ubicación</th>
                            <th class="{{ $thCls }} text-left">Horario</th>
                            <th class="{{ $thCls }} text-center">Personal</th>
                            <th class="{{ $thCls }} text-right">Precio</th>
                            <th class="{{ $thCls }} text-left">Estado</th>
                            <th class="px-5 py-3.5"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($servicios as $servicio)
                            <tr class="hover:bg-indigo-50/30 transition-colors">
                                <td class="px-5 py-3.5">
                                    <span
                                        class="px-2 py-1 bg-slate-100 text-slate-600 rounded-lg text-[11px] font-mono font-bold">
                                        {{ $servicio->codigo }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <p class="text-xs font-bold text-slate-800">{{ $servicio->nombre }}</p>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span
                                        class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide {{ $servicio->tipo_color }}">
                                        {{ $servicio->tipo_label }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <p class="text-xs font-medium text-slate-600">{{ $servicio->ubicacion ?? '—' }}</p>
                                    @if ($servicio->piso)
                                        <p class="text-[10px] text-slate-400 mt-0.5">{{ $servicio->piso }}</p>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-xs font-medium text-slate-600">
                                    {{ $servicio->horario }}
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <span
                                        class="px-2.5 py-1 bg-indigo-50 text-indigo-700 border border-indigo-100 rounded-full text-[10px] font-bold">
                                        {{ $servicio->users_count }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    @if ((float) $servicio->precio > 0)
                                        <p class="text-xs font-extrabold text-slate-800">
                                            {{ $servicio->precio_formateado }}
                                        </p>
                                        @if ($servicio->precio_descripcion)
                                            <p class="text-[10px] text-slate-400 mt-0.5">
                                                {{ $servicio->precio_descripcion }}
                                            </p>
                                        @endif
                                    @else
                                        <span
                                            class="px-2.5 py-1 bg-slate-100 text-slate-500 rounded-full text-[10px] font-bold uppercase tracking-wide">
                                            Sin costo
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    @if ($servicio->activo)
                                        <span
                                            class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-full text-[10px] font-bold uppercase tracking-wide">
                                            Activo
                                        </span>
                                    @else
                                        <span
                                            class="px-2.5 py-1 bg-rose-50 text-rose-700 border border-rose-100 rounded-full text-[10px] font-bold uppercase tracking-wide">
                                            Inactivo
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('servicios.show', $servicio) }}"
                                            class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-[11px] font-bold transition-all">
                                            Ver
                                        </a>
                                        <a href="{{ route('servicios.edit', $servicio) }}"
                                            class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg text-[11px] font-bold transition-all">
                                            Editar
                                        </a>
                                        <a href="{{ route('servicios.personal', $servicio) }}"
                                            class="px-3 py-1.5 bg-purple-50 hover:bg-purple-100 text-purple-700 rounded-lg text-[11px] font-bold transition-all">
                                            Personal
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-5 py-12 text-center">
                                    <p class="text-xs font-semibold text-slate-400">Sin servicios registrados.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">{{ $servicios->links() }}</div>
    </div>
</x-app-layout>
