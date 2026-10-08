<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-3">
            <div class="min-w-0">
                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight truncate">
                    Dispositivos
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">
                    {{ auth()->user()->hasRole('administrador')
                        ? 'Todos los dispositivos conectados al sistema'
                        : 'Tus dispositivos autorizados' }}
                </p>
            </div>
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
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total</p>
                <p class="text-3xl font-black text-slate-800 tracking-tight mt-1">{{ $stats['total'] }}</p>
            </div>
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm">
                <p class="text-[11px] font-bold text-emerald-600/70 uppercase tracking-wider">Confiables</p>
                <p class="text-3xl font-black text-emerald-600 tracking-tight mt-1">{{ $stats['confiables'] }}</p>
            </div>
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm">
                <p class="text-[11px] font-bold text-amber-600/70 uppercase tracking-wider">Pendientes</p>
                <p class="text-3xl font-black text-amber-600 tracking-tight mt-1">{{ $stats['pendientes'] }}</p>
            </div>
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm">
                <p class="text-[11px] font-bold text-rose-600/70 uppercase tracking-wider">Bloqueados</p>
                <p class="text-3xl font-black text-rose-600 tracking-tight mt-1">{{ $stats['bloqueados'] }}</p>
            </div>
        </div>

        {{-- ============ FILTROS ============ --}}
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-5">
            <form method="GET" class="flex gap-2 flex-wrap items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="{{ $labelCls }}">Buscar</label>
                    <input type="text" name="buscar" value="{{ $busqueda }}" placeholder="Nombre, IP o usuario"
                        class="{{ $inputCls }}">
                </div>
                <div class="min-w-[180px]">
                    <label class="{{ $labelCls }}">Filtro</label>
                    <select name="filtro" class="{{ $inputCls }}">
                        <option value="">Todos</option>
                        <option value="confiable" @selected($filtro === 'confiable')>Confiables</option>
                        <option value="pendiente" @selected($filtro === 'pendiente')>Pendientes</option>
                        <option value="bloqueado" @selected($filtro === 'bloqueado')>Bloqueados</option>
                    </select>
                </div>
                <button type="submit"
                    class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all">
                    Filtrar
                </button>
                @if ($busqueda || $filtro)
                    <a href="{{ route('dispositivos.index') }}"
                        class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition-all">
                        Limpiar
                    </a>
                @endif
            </form>
        </div>

        {{-- ============ TABLA ============ --}}
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="font-extrabold text-slate-800 text-base">Dispositivos registrados</h3>
                <p class="text-[11px] text-slate-400">{{ $dispositivos->total() }} dispositivos en total</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-slate-50/70 border-b border-slate-100">
                        <tr>
                            <th class="{{ $thCls }} text-left">Dispositivo</th>
                            @if (auth()->user()->hasRole('administrador'))
                                <th class="{{ $thCls }} text-left">Usuario</th>
                            @endif
                            <th class="{{ $thCls }} text-left">IP</th>
                            <th class="{{ $thCls }} text-left">Último acceso</th>
                            <th class="{{ $thCls }} text-center">Accesos</th>
                            <th class="{{ $thCls }} text-left">Estado</th>
                            <th class="px-5 py-3.5"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($dispositivos as $d)
                            <tr class="hover:bg-indigo-50/30 transition-colors">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-600 shrink-0">
                                            <x-dynamic-component :component="'heroicon-o-' . $d->tipo_icono" class="w-5 h-5" />
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-slate-800">
                                                {{ $d->nombre ?? 'Dispositivo desconocido' }}</p>
                                            <p class="text-[10px] font-medium text-slate-400 mt-0.5">
                                                {{ $d->navegador }} — {{ $d->sistema_operativo }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                @if (auth()->user()->hasRole('administrador'))
                                    <td class="px-5 py-3.5">
                                        <p class="text-xs font-bold text-slate-800">
                                            {{ $d->user?->nombre_completo ?? '—' }}</p>
                                        @if ($d->user?->email)
                                            <p class="text-[10px] font-medium text-slate-400 mt-0.5">
                                                {{ $d->user->email }}</p>
                                        @endif
                                    </td>
                                @endif
                                <td class="px-5 py-3.5">
                                    <span
                                        class="px-2 py-1 bg-slate-100 text-slate-600 rounded-lg text-[11px] font-mono font-bold">
                                        {{ $d->ip_ultimo_acceso ?? ($d->ip_registro ?? '—') }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-xs font-medium text-slate-600">
                                    {{ $d->ultimo_acceso?->diffForHumans() ?? '—' }}
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <span
                                        class="px-2.5 py-1 bg-indigo-50 text-indigo-700 border border-indigo-100 rounded-full text-[10px] font-bold">
                                        {{ $d->total_accesos }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span
                                        class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide border {{ $d->estado_color }}">
                                        {{ $d->estado_label }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex flex-wrap justify-end gap-2 whitespace-nowrap">
                                        <a href="{{ route('dispositivos.show', $d) }}"
                                            class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-[11px] font-bold transition-all">
                                            Ver
                                        </a>

                                        @if (auth()->user()->hasRole('administrador') && !$d->confiable && $d->activo)
                                            <form action="{{ route('dispositivos.confiar', $d) }}" method="POST">
                                                @csrf
                                                <button
                                                    class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg text-[11px] font-bold transition-all">
                                                    Confiar
                                                </button>
                                            </form>
                                        @endif

                                        @if ($d->activo)
                                            <form action="{{ route('dispositivos.bloquear', $d) }}" method="POST"
                                                onsubmit="return confirm('¿Bloquear este dispositivo?')">
                                                @csrf
                                                <button
                                                    class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-[11px] font-bold transition-all">
                                                    Bloquear
                                                </button>
                                            </form>
                                        @elseif (auth()->user()->hasRole('administrador'))
                                            <form action="{{ route('dispositivos.reactivar', $d) }}" method="POST">
                                                @csrf
                                                <button
                                                    class="px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-lg text-[11px] font-bold transition-all">
                                                    Reactivar
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->hasRole('administrador') ? 7 : 6 }}"
                                    class="px-5 py-12 text-center">
                                    <p class="text-xs font-semibold text-slate-400">Sin dispositivos registrados.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">{{ $dispositivos->links() }}</div>
    </div>
</x-app-layout>
