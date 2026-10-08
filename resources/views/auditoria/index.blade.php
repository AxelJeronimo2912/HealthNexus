<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-3">
            <div class="min-w-0">
                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight truncate">
                    Auditoría del Sistema
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Registro inmutable de todas las acciones</p>
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

        {{-- ============ STATS ============ --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Eventos hoy</p>
                <p class="text-3xl font-black text-slate-800 tracking-tight mt-1">{{ $stats['total_hoy'] }}</p>
            </div>
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm">
                <p class="text-[11px] font-bold text-rose-600/70 uppercase tracking-wider">Críticos hoy</p>
                <p class="text-3xl font-black text-rose-600 tracking-tight mt-1">{{ $stats['criticos_hoy'] }}</p>
            </div>
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm">
                <p class="text-[11px] font-bold text-orange-600/70 uppercase tracking-wider">Accesos denegados</p>
                <p class="text-3xl font-black text-orange-600 tracking-tight mt-1">{{ $stats['accesos_denegados'] }}</p>
            </div>
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm">
                <p class="text-[11px] font-bold text-indigo-600/70 uppercase tracking-wider">Última semana</p>
                <p class="text-3xl font-black text-indigo-600 tracking-tight mt-1">{{ $stats['total_semana'] }}</p>
            </div>
        </div>

        {{-- ============ FILTROS ============ --}}
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-5">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-3 items-end">
                <div class="md:col-span-2">
                    <label class="{{ $labelCls }}">Buscar</label>
                    <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar..."
                        class="{{ $inputCls }}">
                </div>

                <div>
                    <label class="{{ $labelCls }}">Módulo</label>
                    <select name="modulo" class="{{ $inputCls }}">
                        <option value="">Todos los módulos</option>
                        @foreach ($modulos as $m)
                            <option value="{{ $m }}" @selected(request('modulo') === $m)>{{ ucfirst($m) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="{{ $labelCls }}">Evento</label>
                    <select name="evento" class="{{ $inputCls }}">
                        <option value="">Todos los eventos</option>
                        @foreach ($eventos as $e)
                            <option value="{{ $e->value }}" @selected(request('evento') === $e->value)>
                                {{ $e->etiqueta() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="{{ $labelCls }}">Severidad</label>
                    <select name="severidad" class="{{ $inputCls }}">
                        <option value="">Toda severidad</option>
                        <option value="info" @selected(request('severidad') === 'info')>Info</option>
                        <option value="warning" @selected(request('severidad') === 'warning')>Warning</option>
                        <option value="critical" @selected(request('severidad') === 'critical')>Critical</option>
                    </select>
                </div>

                <div>
                    <label class="{{ $labelCls }}">Usuario</label>
                    <select name="user_id" class="{{ $inputCls }}">
                        <option value="">Todos los usuarios</option>
                        @foreach ($usuarios as $u)
                            <option value="{{ $u->id }}" @selected(request('user_id') == $u->id)>
                                {{ $u->nombre_completo }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="{{ $labelCls }}">Desde</label>
                    <input type="date" name="desde" value="{{ request('desde') }}" class="{{ $inputCls }}">
                </div>

                <div>
                    <label class="{{ $labelCls }}">Hasta</label>
                    <input type="date" name="hasta" value="{{ request('hasta') }}" class="{{ $inputCls }}">
                </div>

                <div class="md:col-span-6 flex flex-wrap gap-2 pt-2 border-t border-slate-100">
                    <button type="submit"
                        class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all">
                        Filtrar
                    </button>
                    <a href="{{ route('auditoria.index') }}"
                        class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition-all">
                        Limpiar
                    </a>
                </div>
            </form>
        </div>

        {{-- ============ TABLA ============ --}}
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="font-extrabold text-slate-800 text-base">Registros de auditoría</h3>
                <p class="text-[11px] text-slate-400">{{ $logs->total() }} eventos registrados</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-slate-50/70 border-b border-slate-100">
                        <tr>
                            <th class="{{ $thCls }} text-left">Fecha</th>
                            <th class="{{ $thCls }} text-left">Usuario</th>
                            <th class="{{ $thCls }} text-left">Evento</th>
                            <th class="{{ $thCls }} text-left">Módulo</th>
                            <th class="{{ $thCls }} text-left">Descripción</th>
                            <th class="px-5 py-3.5"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($logs as $log)
                            <tr
                                class="{{ $log->severidad === 'critical' ? 'bg-rose-50/40 hover:bg-rose-50/60' : 'hover:bg-indigo-50/30' }} transition-colors">
                                <td class="px-5 py-3.5 text-[11px] font-medium text-slate-400 whitespace-nowrap">
                                    {{ $log->created_at->format('d/m/Y H:i:s') }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <p class="text-xs font-bold text-slate-800">{{ $log->user_nombre }}</p>
                                    @if ($log->user_rol)
                                        <p class="text-[10px] font-medium text-slate-400 mt-0.5">{{ $log->user_rol }}
                                        </p>
                                    @endif
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
                                    <p class="text-xs font-medium text-slate-600">{{ $log->descripcion }}</p>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex justify-end">
                                        <a href="{{ route('auditoria.show', $log) }}"
                                            class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-[11px] font-bold transition-all">
                                            Ver
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center">
                                    <p class="text-xs font-semibold text-slate-400">Sin registros.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">{{ $logs->links() }}</div>
    </div>
</x-app-layout>
