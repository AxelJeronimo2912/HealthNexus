<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-start gap-4">
            {{-- Bloque izquierdo: breadcrumb + título + meta --}}
            <div class="min-w-0 flex-1">
                <nav class="flex items-center gap-1.5 text-xs text-slate-400 mb-1.5" aria-label="Breadcrumb">
                    <a href="{{ route('prediccion.index') }}"
                        class="inline-flex items-center gap-1 hover:text-brand-600 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Predicción
                    </a>
                    <svg class="w-3 h-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                    </svg>
                    <span class="text-slate-600 font-medium truncate max-w-[200px] sm:max-w-none">
                        {{ $medicamento->nombre }}
                    </span>
                </nav>

                <div class="flex items-center gap-2 flex-wrap">
                    <h2 class="font-extrabold text-2xl text-slate-800 leading-tight truncate">
                        {{ $medicamento->nombre }} {{ $medicamento->concentracion }}
                    </h2>

                    @if (isset($stockActual) && isset($medicamento->stock_minimo) && $stockActual <= $medicamento->stock_minimo)
                        <span
                            class="inline-flex items-center gap-1 px-2 py-0.5 bg-rose-50 text-rose-700 border border-rose-200 rounded-full text-[11px] font-bold">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                            Stock crítico
                        </span>
                    @endif
                </div>

                <p class="text-xs text-slate-400 mt-1 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Análisis predictivo de consumo y agotamiento
                </p>
            </div>

            {{-- Bloque derecho: acciones --}}
            <div class="flex items-center gap-2 shrink-0">
                @if (Route::has('existencias.show'))
                    <a href="{{ route('existencias.show', $medicamento) }}"
                        class="hidden sm:inline-flex items-center gap-1.5 px-4 py-2.5 bg-white hover:bg-brand-50 text-slate-700 hover:text-brand-600 border border-slate-200 hover:border-brand-200 rounded-xl text-xs font-bold transition-all active:scale-95 group">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        Existencias
                    </a>
                @endif

                <a href="{{ route('prediccion.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-xl text-xs font-bold transition-all active:scale-95 group">
                    <svg class="w-4 h-4 transition-transform group-hover:-translate-x-0.5" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                    Volver
                </a>
            </div>
        </div>
    </x-slot>

    @php
        // ====== Lógica de tendencia ======
        $tendencia = 'estable';
        $tendenciaColor = 'slate';
        $tendenciaIcon = '→';
        $tendenciaTexto = 'Estable';

        if ($prediccion['promedio_30_dias'] > 0) {
            if ($prediccion['promedio_7_dias'] > $prediccion['promedio_30_dias'] * 1.1) {
                $tendencia = 'al alza';
                $tendenciaColor = 'rose';
                $tendenciaIcon = '↑';
                $tendenciaTexto = 'Al alza';
            } elseif ($prediccion['promedio_7_dias'] < $prediccion['promedio_30_dias'] * 0.9) {
                $tendencia = 'a la baja';
                $tendenciaColor = 'emerald';
                $tendenciaIcon = '↓';
                $tendenciaTexto = 'A la baja';
            }
        }

        $esCritico = $diasRestantes !== null && $diasRestantes <= 7;
        $esAlerta = $diasRestantes !== null && $diasRestantes > 7 && $diasRestantes <= 30;

        // Color del hero según criticidad
        $heroGradient = $esCritico
            ? 'from-rose-500 to-rose-600'
            : ($esAlerta
                ? 'from-amber-500 to-amber-600'
                : 'from-brand-500 to-brand-600');
    @endphp

    <div class="py-8 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- ============ BANNER DE ALERTA ============ --}}
        @if ($esCritico)
            <div role="alert"
                class="flex flex-wrap items-start gap-3 p-4 bg-rose-50 border border-rose-200 rounded-2xl">
                <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-rose-800">
                        Stock crítico — quedan {{ $diasRestantes }} días de existencia
                    </p>
                    <p class="text-xs text-rose-700 mt-0.5">
                        Con el consumo actual ({{ $consumoPromedio }} u/día) el medicamento se agotará pronto.
                        Considera reabastecer.
                    </p>
                </div>
                @if (Route::has('existencias.show'))
                    <a href="{{ route('existencias.show', $medicamento) }}"
                        class="px-3 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-xs font-bold shrink-0 transition active:scale-95">
                        Reabastecer
                    </a>
                @endif
            </div>
        @elseif ($esAlerta)
            <div role="alert"
                class="flex flex-wrap items-start gap-3 p-4 bg-amber-50 border border-amber-200 rounded-2xl">
                <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-amber-800">
                        Quedan {{ $diasRestantes }} días de existencia
                    </p>
                    <p class="text-xs text-amber-700 mt-0.5">
                        Planifica el reabastecimiento para evitar desabasto.
                    </p>
                </div>
            </div>
        @endif

        {{-- ============ HERO + KPIs ============ --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

            {{-- Hero: días restantes --}}
            <div
                class="lg:col-span-1 bg-gradient-to-br {{ $heroGradient }} rounded-2xl p-6 text-white shadow-lg relative overflow-hidden">
                <div
                    class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16 pointer-events-none">
                </div>
                <div
                    class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full -ml-12 -mb-12 pointer-events-none">
                </div>
                <div class="relative">
                    <p class="text-xs font-bold uppercase tracking-wider text-white/80">
                        Días al agotamiento
                    </p>
                    <p class="text-5xl font-black tracking-tight mt-2 leading-none">
                        {{ $diasRestantes ?? '—' }}
                    </p>
                    <p class="text-xs text-white/80 mt-2">
                        {{ $diasRestantes !== null ? 'días restantes estimados' : 'Sin datos suficientes' }}
                    </p>

                    <div class="mt-5 pt-4 border-t border-white/20 space-y-1.5">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-white/80">Stock actual</span>
                            <span class="font-bold">{{ $stockActual }} u</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-white/80">Consumo diario</span>
                            <span class="font-bold">{{ $consumoPromedio }} u/día</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-white/80">Mínimo requerido</span>
                            <span class="font-bold">{{ $medicamento->stock_minimo ?? '—' }} u</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KPIs secundarios --}}
            <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-4">

                {{-- Stock actual --}}
                <div class="bg-white p-5 rounded-2xl ring-1 ring-slate-200/70 shadow-sm">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Stock actual</p>
                        <div
                            class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-black text-slate-800 tracking-tight">{{ $stockActual }}</p>
                    <p class="text-xs text-slate-500 mt-1">unidades disponibles</p>
                </div>

                {{-- Demanda diaria con tendencia --}}
                <div class="bg-white p-5 rounded-2xl ring-1 ring-slate-200/70 shadow-sm">
                    <div class="flex items-center justify-between mb-2 gap-2">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Demanda diaria</p>
                        <span
                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold
                                     bg-{{ $tendenciaColor }}-50 text-{{ $tendenciaColor }}-700 ring-1 ring-{{ $tendenciaColor }}-100 shrink-0">
                            <span>{{ $tendenciaIcon }}</span>
                            <span>{{ $tendenciaTexto }}</span>
                        </span>
                    </div>
                    <p class="text-3xl font-black text-brand-600 tracking-tight">{{ $consumoPromedio }}</p>
                    <p class="text-xs text-slate-500 mt-1">unidades por día</p>
                </div>

                {{-- Consumo 90d --}}
                <div class="bg-white p-5 rounded-2xl ring-1 ring-slate-200/70 shadow-sm">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Consumo 90d</p>
                        <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-black text-slate-800 tracking-tight">{{ $consumoTotal }}</p>
                    <p class="text-xs text-slate-500 mt-1">unidades totales</p>
                </div>
            </div>
        </div>

        {{-- ============ RANKING DE DEMANDA (opcional) ============ --}}
        @isset($rankingDemanda)
            <div class="bg-white rounded-2xl ring-1 ring-slate-200/70 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between gap-3">
                    <div>
                        <h3 class="font-extrabold text-slate-800 text-base">Ranking de demanda</h3>
                        <p class="text-xs text-slate-400">Posición de este medicamento en su servicio</p>
                    </div>
                    <span
                        class="px-2.5 py-1 bg-brand-50 text-brand-700 text-xs font-bold rounded-full ring-1 ring-brand-100 shrink-0">
                        #{{ $rankingDemanda['posicion'] }} de {{ $rankingDemanda['total'] }}
                    </span>
                </div>
                <div class="p-5 space-y-3">
                    @foreach ($rankingDemanda['items'] as $i => $item)
                        <div class="flex items-center gap-3">
                            <span
                                class="w-6 h-6 rounded-lg {{ $item['es_actual'] ? 'bg-brand-600 text-white' : 'bg-slate-100 text-slate-500' }} flex items-center justify-center text-xs font-bold shrink-0">
                                {{ $i + 1 }}
                            </span>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between mb-1 gap-2">
                                    <p
                                        class="text-sm text-slate-700 truncate {{ $item['es_actual'] ? 'font-bold' : 'font-medium' }}">
                                        {{ $item['nombre'] }}
                                        @if ($item['es_actual'])
                                            <span class="ml-1 text-[10px] font-bold text-brand-600">(este)</span>
                                        @endif
                                    </p>
                                    <p class="text-xs font-bold text-slate-600 shrink-0">{{ $item['consumo'] }} u</p>
                                </div>
                                <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full {{ $item['es_actual'] ? 'bg-brand-500' : 'bg-slate-300' }} rounded-full"
                                        style="width: {{ $item['porcentaje'] }}%"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endisset

        {{-- ============ COMPORTAMIENTO: histórico + predicción ============ --}}
        <div class="bg-white rounded-2xl ring-1 ring-slate-200/70 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="font-extrabold text-slate-800 text-base">Comportamiento del consumo</h3>
                <p class="text-xs text-slate-400">Histórico de 90 días y proyección a 30 días</p>
            </div>

            {{-- Histórico --}}
            <div class="p-5 border-b border-slate-100">
                <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Histórico (90d)</p>
                    <span
                        class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-50 text-slate-600 border border-slate-200 rounded-full text-[10px] font-bold">
                        <span class="w-2 h-2 rounded-full bg-brand-500"></span>
                        Consumo real
                    </span>
                </div>
                <div class="h-56">
                    <canvas id="chartHistorial"></canvas>
                </div>
            </div>

            {{-- Predicción --}}
            <div class="p-5">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Proyección (30d)</p>
                        <p class="text-xs text-slate-400 mt-0.5">
                            Total estimado:
                            <span class="font-bold text-slate-700">{{ $prediccion['total_predicho'] }}</span> unidades
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-2 text-[10px] font-bold">
                        <span
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-brand-50 text-brand-700 border border-brand-100 rounded-full">
                            <span class="w-2 h-2 rounded-full bg-brand-500"></span>
                            Consumo diario
                        </span>
                        <span
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-full">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Acumulado
                        </span>
                    </div>
                </div>
                <div class="h-64">
                    <canvas id="chartPrediccion"></canvas>
                </div>
            </div>
        </div>

        {{-- ============ DETALLE TÉCNICO (colapsable) ============ --}}
        <details class="bg-white rounded-2xl ring-1 ring-slate-200/70 shadow-sm overflow-hidden group">
            <summary
                class="px-5 py-4 cursor-pointer list-none flex items-center justify-between hover:bg-slate-50/50 transition select-none">
                <div class="flex items-center gap-3">
                    <div
                        class="w-9 h-9 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-800 text-sm">Detalle de modelos predictivos</h3>
                        <p class="text-xs text-slate-400">Algoritmos aplicados y regresión lineal</p>
                    </div>
                </div>
                <svg class="w-5 h-5 text-slate-400 transition-transform group-open:rotate-180 shrink-0" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </summary>

            <div class="px-5 pb-5 pt-2 border-t border-slate-100 space-y-4">
                {{-- 4 modelos --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <div class="bg-slate-50/70 border border-slate-100 rounded-xl p-3 border-l-4 border-l-brand-400">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Promedio 7d</p>
                        <p class="text-xl font-black text-slate-800 tracking-tight mt-1">
                            {{ $prediccion['promedio_7_dias'] }}</p>
                    </div>
                    <div class="bg-slate-50/70 border border-slate-100 rounded-xl p-3 border-l-4 border-l-brand-400">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Promedio 30d</p>
                        <p class="text-xl font-black text-slate-800 tracking-tight mt-1">
                            {{ $prediccion['promedio_30_dias'] }}</p>
                    </div>
                    <div class="bg-slate-50/70 border border-slate-100 rounded-xl p-3 border-l-4 border-l-brand-500">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Mov. ponderado</p>
                        <p class="text-xl font-black text-slate-800 tracking-tight mt-1">
                            {{ $prediccion['movil_ponderado'] }}</p>
                    </div>
                    <div class="bg-slate-50/70 border border-slate-100 rounded-xl p-3 border-l-4 border-l-accent-500">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Suavizado exp.</p>
                        <p class="text-xl font-black text-slate-800 tracking-tight mt-1">
                            {{ $prediccion['suavizado_exponencial'] }}</p>
                    </div>
                </div>

                {{-- Regresión --}}
                <div class="bg-slate-50/70 border border-slate-100 rounded-xl p-4 space-y-4">
                    <div class="flex items-center gap-2">
                        <div
                            class="w-8 h-8 rounded-xl bg-brand-50 flex items-center justify-center text-brand-600 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-slate-800 text-sm">Regresión lineal</h4>
                            <p class="text-xs text-slate-400">Ajuste de tendencia sobre los datos históricos</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <div class="bg-white border border-slate-100 rounded-xl p-3">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Pendiente</p>
                            <p class="text-xs font-mono font-bold text-slate-800 mt-1">
                                {{ $prediccion['regresion']['pendiente'] }}</p>
                        </div>
                        <div class="bg-white border border-slate-100 rounded-xl p-3">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Intercepto</p>
                            <p class="text-xs font-mono font-bold text-slate-800 mt-1">
                                {{ $prediccion['regresion']['intercepto'] }}</p>
                        </div>
                        <div class="bg-white border border-slate-100 rounded-xl p-3">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">R² (bondad)</p>
                            <p class="text-xs font-mono font-bold text-slate-800 mt-1">
                                {{ $prediccion['regresion']['r2'] }}</p>
                        </div>
                        <div class="bg-brand-50/60 border border-brand-100 rounded-xl p-3">
                            <p class="text-[10px] font-bold text-brand-600/70 uppercase tracking-wider">Predicción base
                            </p>
                            <p class="text-xs font-mono font-bold text-brand-700 mt-1">
                                {{ $prediccion['prediccion_diaria_base'] }}/día</p>
                        </div>
                    </div>

                    <p class="text-xs font-medium text-slate-500 italic leading-relaxed">
                        {{ $prediccion['regresion']['interpretacion'] }}
                    </p>
                </div>
            </div>
        </details>

        {{-- ============ ACCIONES ============ --}}
        <div class="bg-white p-5 rounded-2xl ring-1 ring-slate-200/70 shadow-sm flex flex-wrap gap-2">
            @if (Route::has('existencias.show'))
                <a href="{{ route('existencias.show', $medicamento) }}"
                    class="px-4 py-2.5 bg-brand-600 hover:bg-brand-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    Ver existencias
                </a>
            @endif
            <a href="{{ route('prediccion.index') }}"
                class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-600 rounded-xl text-xs font-bold transition-all">
                Volver al listado
            </a>
        </div>
    </div>

    {{-- ============ SCRIPTS DE GRÁFICAS ============ --}}
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // ============ GRÁFICA 1: Historial ============
                const ctxHistorial = document.getElementById('chartHistorial');
                if (ctxHistorial) {
                    new Chart(ctxHistorial, {
                        type: 'line',
                        data: {
                            labels: @json($chartHistorial['labels']),
                            datasets: [{
                                label: 'Consumo diario',
                                data: @json($chartHistorial['data']),
                                borderColor: 'rgb(58, 64, 166)',
                                backgroundColor: 'rgba(58, 64, 166, 0.1)',
                                tension: 0.3,
                                fill: true,
                                pointRadius: 2,
                                pointHoverRadius: 5,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    callbacks: {
                                        label: ctx => `Consumo: ${ctx.parsed.y}`
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Unidades'
                                    }
                                },
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Fecha'
                                    },
                                    ticks: {
                                        maxTicksLimit: 10
                                    }
                                }
                            }
                        }
                    });
                }

                // ============ GRÁFICA 2: Predicción ============
                const ctxPrediccion = document.getElementById('chartPrediccion');
                if (ctxPrediccion) {
                    new Chart(ctxPrediccion, {
                        data: {
                            labels: @json($chartPrediccion['labels']),
                            datasets: [{
                                    type: 'bar',
                                    label: 'Consumo diario predicho',
                                    data: @json($chartPrediccion['data']),
                                    backgroundColor: 'rgba(58, 64, 166, 0.7)',
                                    borderColor: 'rgb(58, 64, 166)',
                                    borderWidth: 1,
                                    yAxisID: 'y',
                                },
                                {
                                    type: 'line',
                                    label: 'Acumulado',
                                    data: @json($chartPrediccion['acumulado']),
                                    borderColor: 'rgb(16, 185, 129)',
                                    backgroundColor: 'rgba(16, 185, 129, 0.2)',
                                    tension: 0.3,
                                    fill: false,
                                    pointRadius: 2,
                                    yAxisID: 'y1',
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'top'
                                },
                                tooltip: {
                                    mode: 'index',
                                    intersect: false
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    position: 'left',
                                    title: {
                                        display: true,
                                        text: 'Consumo diario'
                                    }
                                },
                                y1: {
                                    beginAtZero: true,
                                    position: 'right',
                                    title: {
                                        display: true,
                                        text: 'Acumulado'
                                    },
                                    grid: {
                                        drawOnChartArea: false
                                    }
                                },
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Fecha'
                                    },
                                    ticks: {
                                        maxTicksLimit: 15
                                    }
                                }
                            }
                        }
                    });
                }
            });
        </script>
    @endpush
</x-app-layout>
