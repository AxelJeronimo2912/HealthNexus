<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex items-center gap-3">
                <div
                    class="w-12 h-12 rounded-2xl bg-nexus-primary/10 text-nexus-primary flex items-center justify-center border border-nexus-primary/20 shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-2xl text-slate-800 leading-tight">Auditoría del Sistema</h2>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">Registro inmutable de todas las acciones de
                        HealthNexus</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <span
                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-600 bg-emerald-50 px-3 py-1.5 rounded-full border border-emerald-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    Monitoreo activo
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-100 min-h-screen" x-data="auditoriaIndex()" x-init="init()">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- MÉTRICAS -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4">
                    <div
                        class="w-12 h-12 rounded-2xl bg-nexus-primary/10 text-nexus-primary flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Eventos Hoy</p>
                        <h4 class="text-xl font-bold text-slate-800">{{ $stats['total_hoy'] }}</h4>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4">
                    <div
                        class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01M4.93 19h14.14a2 2 0 001.74-2.99l-7.07-12.14a2 2 0 00-3.48 0L2.19 16.01A2 2 0 004.93 19z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-rose-600/70 uppercase tracking-wider">Críticos Hoy</p>
                        <h4 class="text-xl font-bold text-rose-600">{{ $stats['criticos_hoy'] }}</h4>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4">
                    <div
                        class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-amber-600/70 uppercase tracking-wider">Accesos Denegados
                        </p>
                        <h4 class="text-xl font-bold text-amber-600">{{ $stats['accesos_denegados'] }}</h4>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4">
                    <div
                        class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-indigo-600/70 uppercase tracking-wider">Última Semana</p>
                        <h4 class="text-xl font-bold text-indigo-600">{{ $stats['total_semana'] }}</h4>
                    </div>
                </div>
            </div>

            <!-- FILTROS -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-5">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-9 h-9 rounded-2xl bg-slate-100 text-slate-500 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-slate-800 text-sm">Filtros de búsqueda</h3>
                    <button type="button" @click="limpiarFiltros()" x-show="hayFiltrosActivos"
                        class="ml-auto text-xs font-bold text-slate-400 hover:text-slate-600">
                        Limpiar todo
                    </button>
                </div>

                <form method="GET" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-3 items-end">
                    <div class="md:col-span-2">
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                            Buscar
                        </label>
                        <div class="relative">
                            <div
                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z" />
                                </svg>
                            </div>
                            <input type="text" name="buscar" value="{{ request('buscar') }}"
                                placeholder="Buscar por descripción, usuario..."
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-3 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-nexus-primary focus:ring-0 outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                            Módulo
                        </label>
                        <select name="modulo"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-nexus-primary focus:ring-0 outline-none">
                            <option value="">Todos los módulos</option>
                            @foreach ($modulos as $m)
                                <option value="{{ $m }}" @selected(request('modulo') === $m)>{{ ucfirst($m) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                            Evento
                        </label>
                        <select name="evento"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-nexus-primary focus:ring-0 outline-none">
                            <option value="">Todos los eventos</option>
                            @foreach ($eventos as $e)
                                <option value="{{ $e->value }}" @selected(request('evento') === $e->value)>
                                    {{ $e->etiqueta() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                            Severidad
                        </label>
                        <select name="severidad"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-nexus-primary focus:ring-0 outline-none">
                            <option value="">Toda severidad</option>
                            <option value="info" @selected(request('severidad') === 'info')>Info</option>
                            <option value="warning" @selected(request('severidad') === 'warning')>Warning</option>
                            <option value="critical" @selected(request('severidad') === 'critical')>Critical</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                            Usuario
                        </label>
                        <select name="user_id"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-nexus-primary focus:ring-0 outline-none">
                            <option value="">Todos los usuarios</option>
                            @foreach ($usuarios as $u)
                                <option value="{{ $u->id }}" @selected(request('user_id') == $u->id)>
                                    {{ $u->nombre_completo }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                            Desde
                        </label>
                        <input type="date" name="desde" value="{{ request('desde') }}"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-nexus-primary focus:ring-0 outline-none">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                            Hasta
                        </label>
                        <input type="date" name="hasta" value="{{ request('hasta') }}"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-nexus-primary focus:ring-0 outline-none">
                    </div>

                    <div class="md:col-span-3 lg:col-span-6 flex flex-wrap gap-2 pt-3 border-t border-slate-100">
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-nexus-primary hover:bg-slate-900 text-nexus-accent rounded-2xl text-xs font-bold shadow-md hover:shadow-lg transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z" />
                            </svg>
                            Filtrar
                        </button>
                        <a href="{{ route('auditoria.index') }}"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-2xl text-xs font-bold transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Limpiar
                        </a>
                    </div>
                </form>
            </div>

            <!-- TABLA -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-2xl bg-slate-100 text-slate-500 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-slate-800 text-base">Registros de auditoría</h3>
                            <p class="text-[11px] text-slate-400">{{ $logs->total() }} eventos registrados</p>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-slate-50/70 border-b border-slate-100">
                            <tr>
                                <th
                                    class="px-5 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider text-left">
                                    Fecha
                                </th>
                                <th
                                    class="px-5 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider text-left">
                                    Usuario
                                </th>
                                <th
                                    class="px-5 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider text-left">
                                    Evento
                                </th>
                                <th
                                    class="px-5 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider text-left">
                                    Módulo
                                </th>
                                <th
                                    class="px-5 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider text-left">
                                    Descripción
                                </th>
                                <th class="px-5 py-3.5"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($logs as $log)
                                <tr
                                    class="{{ $log->severidad === 'critical' ? 'bg-rose-50/40 hover:bg-rose-50/60' : 'hover:bg-indigo-50/30' }} transition-colors">
                                    <td class="px-5 py-3.5 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <div
                                                class="w-7 h-7 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center shrink-0">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                            <div class="text-[11px] font-medium text-slate-500">
                                                <p class="font-bold text-slate-700">
                                                    {{ $log->created_at->format('d/m/Y') }}</p>
                                                <p>{{ $log->created_at->format('H:i:s') }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center gap-2">
                                            <div
                                                class="w-8 h-8 rounded-full bg-nexus-primary/10 text-nexus-primary flex items-center justify-center text-[10px] font-bold shrink-0">
                                                {{ strtoupper(substr($log->user_nombre, 0, 2)) }}
                                            </div>
                                            <div>
                                                <p class="text-xs font-bold text-slate-800">{{ $log->user_nombre }}
                                                </p>
                                                @if ($log->user_rol)
                                                    <p class="text-[10px] font-medium text-slate-400 mt-0.5">
                                                        {{ $log->user_rol }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span
                                            class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide border {{ $log->evento_color }}">
                                            {{ $log->evento_label }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span
                                            class="px-2 py-1 bg-slate-100 text-slate-600 rounded-lg text-[11px] font-bold">
                                            {{ $log->modulo_label }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <p class="text-xs font-medium text-slate-600 max-w-md truncate">
                                            {{ $log->descripcion }}</p>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <div class="flex justify-end">
                                            <a href="{{ route('auditoria.show', $log) }}"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-[11px] font-bold transition-all">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                Ver
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-16 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <div
                                                class="w-14 h-14 rounded-3xl bg-slate-100 text-slate-400 flex items-center justify-center">
                                                <svg class="w-7 h-7" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-slate-700">Sin registros de auditoría
                                                </p>
                                                <p class="text-xs text-slate-400 mt-1">No se encontraron eventos con
                                                    los filtros aplicados.</p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">{{ $logs->links() }}</div>
        </div>
    </div>

    <script>
        function auditoriaIndex() {
            return {
                filtros: {
                    buscar: '{{ request('buscar') ?? '' }}',
                    modulo: '{{ request('modulo') ?? '' }}',
                    evento: '{{ request('evento') ?? '' }}',
                    severidad: '{{ request('severidad') ?? '' }}',
                    user_id: '{{ request('user_id') ?? '' }}',
                    desde: '{{ request('desde') ?? '' }}',
                    hasta: '{{ request('hasta') ?? '' }}',
                },

                init() {},

                get hayFiltrosActivos() {
                    return Object.values(this.filtros).some(v => v !== '');
                },

                limpiarFiltros() {
                    this.filtros = {
                        buscar: '',
                        modulo: '',
                        evento: '',
                        severidad: '',
                        user_id: '',
                        desde: '',
                        hasta: ''
                    };
                    window.location.href = '{{ route('auditoria.index') }}';
                }
            };
        }

        window.auditoriaIndex = auditoriaIndex;
    </script>
</x-app-layout>
