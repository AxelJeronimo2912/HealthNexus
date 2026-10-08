<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-3">
            <div class="min-w-0">
                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight truncate">
                    Alertas Inteligentes
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Monitoreo automático del sistema hospitalario</p>
            </div>
            <form action="{{ route('alertas.generar') }}" method="POST" class="shrink-0">
                @csrf
                <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white px-5 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 shadow-sm transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Analizar ahora
                </button>
            </form>
        </div>
    </x-slot>

    @php
        $inputCls =
            'w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-indigo-500 focus:ring-0 outline-none';
        $labelCls = 'block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5';
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

        {{-- ============ STATS ============ --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm">
                <p class="text-[11px] font-bold text-rose-600/70 uppercase tracking-wider">Críticas</p>
                <p class="text-3xl font-black text-rose-600 tracking-tight mt-1">{{ $stats['criticas'] }}</p>
            </div>
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm">
                <p class="text-[11px] font-bold text-amber-600/70 uppercase tracking-wider">Advertencias</p>
                <p class="text-3xl font-black text-amber-600 tracking-tight mt-1">{{ $stats['advertencias'] }}</p>
            </div>
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm">
                <p class="text-[11px] font-bold text-indigo-600/70 uppercase tracking-wider">Total activas</p>
                <p class="text-3xl font-black text-indigo-600 tracking-tight mt-1">{{ $stats['total_activas'] }}</p>
            </div>
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm">
                <p class="text-[11px] font-bold text-emerald-600/70 uppercase tracking-wider">Resueltas hoy</p>
                <p class="text-3xl font-black text-emerald-600 tracking-tight mt-1">{{ $stats['resueltas_hoy'] }}</p>
            </div>
        </div>

        {{-- ============ FILTROS ============ --}}
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-5">
            <form method="GET" class="flex gap-2 flex-wrap items-end">
                <div class="min-w-[160px]">
                    <label class="{{ $labelCls }}">Estado</label>
                    <select name="estado" class="{{ $inputCls }}">
                        <option value="activas" @selected($filtroEstado === 'activas')>Activas</option>
                        <option value="vista" @selected($filtroEstado === 'vista')>Vistas</option>
                        <option value="resuelta" @selected($filtroEstado === 'resuelta')>Resueltas</option>
                        <option value="descartada" @selected($filtroEstado === 'descartada')>Descartadas</option>
                        <option value="" @selected($filtroEstado === '')>Todas</option>
                    </select>
                </div>
                <div class="min-w-[160px]">
                    <label class="{{ $labelCls }}">Nivel</label>
                    <select name="nivel" class="{{ $inputCls }}">
                        <option value="">Todos los niveles</option>
                        <option value="critico" @selected($filtroNivel === 'critico')>Crítico</option>
                        <option value="advertencia" @selected($filtroNivel === 'advertencia')>Advertencia</option>
                        <option value="info" @selected($filtroNivel === 'info')>Información</option>
                    </select>
                </div>
                <div class="min-w-[160px]">
                    <label class="{{ $labelCls }}">Tipo</label>
                    <select name="tipo" class="{{ $inputCls }}">
                        <option value="">Todos los tipos</option>
                        <option value="inventario" @selected($filtroTipo === 'inventario')>Inventario</option>
                        <option value="paciente" @selected($filtroTipo === 'paciente')>Paciente</option>
                        <option value="operacion" @selected($filtroTipo === 'operacion')>Operación</option>
                        <option value="seguridad" @selected($filtroTipo === 'seguridad')>Seguridad</option>
                    </select>
                </div>
                <button type="submit"
                    class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all">
                    Filtrar
                </button>
                <a href="{{ route('alertas.index') }}"
                    class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition-all">
                    Limpiar
                </a>
            </form>
        </div>

        {{-- ============ LISTA DE ALERTAS ============ --}}
        <div class="space-y-3">
            @forelse ($alertas as $alerta)
                @php
                    $nivelBorder = match ($alerta->nivel) {
                        'critico' => 'border-l-rose-500 bg-rose-50/40',
                        'advertencia' => 'border-l-amber-500 bg-amber-50/30',
                        default => 'border-l-indigo-500 bg-indigo-50/20',
                    };
                @endphp
                <div
                    class="bg-white rounded-3xl border border-slate-100 shadow-sm border-l-4 {{ $nivelBorder }} overflow-hidden">
                    <div class="p-5">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2 mb-2">
                                    <span
                                        class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide border {{ $alerta->nivel_color }}">
                                        {{ $alerta->nivel_label }}
                                    </span>
                                    <span
                                        class="px-2 py-1 bg-slate-100 text-slate-500 rounded-lg text-[10px] font-bold uppercase tracking-wide">
                                        {{ $alerta->tipo }}
                                    </span>
                                    @if ($alerta->estado === 'vista')
                                        <span class="text-[11px] font-bold text-amber-600">· Vista</span>
                                    @elseif ($alerta->estado === 'resuelta')
                                        <span class="text-[11px] font-bold text-emerald-600">
                                            · Resuelta por {{ $alerta->resueltaPor?->nombre_completo }}
                                        </span>
                                    @endif
                                </div>

                                <h3 class="text-sm font-extrabold text-slate-800">{{ $alerta->titulo }}</h3>
                                <p class="text-xs font-medium text-slate-600 mt-1 leading-relaxed">
                                    {{ $alerta->mensaje }}</p>

                                <p class="text-[11px] font-medium text-slate-400 mt-2">
                                    {{ $alerta->created_at->format('d/m/Y H:i') }}
                                    <span class="text-slate-300">·</span>
                                    {{ $alerta->created_at->diffForHumans() }}
                                </p>
                            </div>

                            <div class="flex flex-row sm:flex-col gap-2 shrink-0">
                                @if ($alerta->estado === 'activa')
                                    <form action="{{ route('alertas.vista', $alerta) }}" method="POST">
                                        @csrf
                                        <button
                                            class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg text-[11px] font-bold transition-all whitespace-nowrap">
                                            Marcar vista
                                        </button>
                                    </form>
                                @endif

                                @if (in_array($alerta->estado, ['activa', 'vista']))
                                    <form action="{{ route('alertas.resolver', $alerta) }}" method="POST">
                                        @csrf
                                        <button
                                            class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg text-[11px] font-bold transition-all whitespace-nowrap">
                                            ✓ Resolver
                                        </button>
                                    </form>
                                    <form action="{{ route('alertas.descartar', $alerta) }}" method="POST">
                                        @csrf
                                        <button
                                            class="px-3 py-1.5 bg-slate-50 hover:bg-slate-100 text-slate-500 rounded-lg text-[11px] font-bold transition-all whitespace-nowrap">
                                            Descartar
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        @if ($alerta->datos && count($alerta->datos))
                            <div class="mt-4 pt-4 border-t border-slate-100 grid grid-cols-2 md:grid-cols-4 gap-2">
                                @foreach ($alerta->datos as $key => $value)
                                    <div class="bg-slate-50/70 border border-slate-100 rounded-xl p-2.5">
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                            {{ ucfirst(str_replace('_', ' ', $key)) }}
                                        </p>
                                        <p class="text-xs font-extrabold text-slate-800 mt-0.5 truncate">
                                            @if (is_array($value))
                                                {{ implode(', ', $value) }}
                                            @else
                                                {{ $value }}
                                            @endif
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white p-12 rounded-3xl border border-slate-100 shadow-sm text-center">
                    <p class="text-xs font-semibold text-slate-400">No hay alertas para mostrar.</p>
                    <form action="{{ route('alertas.generar') }}" method="POST" class="mt-4">
                        @csrf
                        <button type="submit"
                            class="px-5 py-2.5 bg-indigo-50 hover:bg-indigo-100 active:scale-95 text-indigo-700 border border-indigo-100 rounded-xl text-xs font-bold transition-all">
                            Generar alertas ahora
                        </button>
                    </form>
                </div>
            @endforelse
        </div>

        <div class="mt-4">{{ $alertas->links() }}</div>
    </div>
</x-app-layout>
