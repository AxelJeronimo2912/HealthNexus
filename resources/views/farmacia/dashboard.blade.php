<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                    Panel Farmacia — HealthNexus
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">
                    Inventario, dispensación y predicción
                </p>
            </div>
            <div
                class="hidden md:flex items-center gap-2 px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full text-xs font-semibold">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Sistema en línea
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-100 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- BIENVENIDA --}}
            <div class="bg-gradient-to-r from-amber-600 to-orange-700 rounded-3xl p-6 sm:p-8 shadow-lg text-white">
                <h3 class="text-2xl font-bold">
                    Bienvenido/a, {{ auth()->user()->nombre_completo }}
                </h3>
                <p class="text-sm text-amber-100 mt-1">Rol: Farmacia</p>
                <p class="text-xs text-amber-200">
                    {{ now()->translatedFormat('l d \d\e F \d\e Y') }}
                </p>
            </div>

            {{-- KPIs --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

                <a href="{{ route('existencias.index', ['filtro' => 'bajo']) }}"
                    class="group bg-white p-6 rounded-3xl shadow-sm border border-slate-100 hover:shadow-lg hover:-translate-y-1 transition-all">
                    <div
                        class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center group-hover:bg-amber-600 group-hover:text-white transition-colors">
                        <x-heroicon-o-exclamation-triangle class="w-6 h-6" />
                    </div>
                    <p class="text-xs font-bold text-slate-400 uppercase mt-4">Stock bajo</p>
                    <p class="text-3xl font-extrabold text-slate-800 mt-1">{{ $medicamentosBajosTotal }}</p>
                    <p class="text-xs text-slate-500 mt-1">Por debajo del mínimo</p>
                </a>

                <a href="{{ route('dispensaciones.index') }}"
                    class="group bg-white p-6 rounded-3xl shadow-sm border border-slate-100 hover:shadow-lg hover:-translate-y-1 transition-all">
                    <div
                        class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors">
                        <x-heroicon-o-clipboard-document-list class="w-6 h-6" />
                    </div>
                    <p class="text-xs font-bold text-slate-400 uppercase mt-4">Recetas pendientes</p>
                    <p class="text-3xl font-extrabold text-slate-800 mt-1">{{ $recetasPendientesTotal }}</p>
                    <p class="text-xs text-slate-500 mt-1">Por dispensar</p>
                </a>

                <div class="group bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <x-heroicon-o-check-circle class="w-6 h-6" />
                    </div>
                    <p class="text-xs font-bold text-slate-400 uppercase mt-4">Dispensadas hoy</p>
                    <p class="text-3xl font-extrabold text-slate-800 mt-1">{{ $dispensacionesHoy }}</p>
                    <p class="text-xs text-slate-500 mt-1">Recetas procesadas</p>
                </div>

                <a href="{{ route('prediccion.index') }}"
                    class="group bg-white p-6 rounded-3xl shadow-sm border border-slate-100 hover:shadow-lg hover:-translate-y-1 transition-all">
                    <div
                        class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                        <x-heroicon-o-cpu-chip class="w-6 h-6" />
                    </div>
                    <p class="text-xs font-bold text-slate-400 uppercase mt-4">Predicción IA</p>
                    <p class="text-3xl font-extrabold text-slate-800 mt-1">{{ $topDemandaTotal }}</p>
                    <p class="text-xs text-slate-500 mt-1">Medicamentos monitoreados</p>
                </a>

            </div>

            {{-- MEDICAMENTOS CRÍTICOS + RECETAS PENDIENTES --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Medicamentos críticos --}}
                <div class="bg-white rounded-3xl border border-slate-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-sm font-bold text-slate-700 flex items-center gap-2">
                            <x-heroicon-o-beaker class="w-5 h-5 text-red-600" />
                            Medicamentos críticos
                        </h4>
                        <a href="{{ route('existencias.index') }}"
                            class="text-xs font-bold text-red-600 hover:text-red-800">
                            Ver todos →
                        </a>
                    </div>

                    @forelse($medicamentosCriticos as $m)
                        <div class="flex items-center gap-3 py-3 border-b border-slate-100 last:border-0">
                            <div class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-slate-800 truncate">
                                    {{ $m->nombre }} {{ $m->concentracion }}
                                </p>
                                <p class="text-xs text-slate-500">
                                    Stock: {{ $m->stock_total_calculado }} · Mín: {{ $m->stock_minimo }}
                                </p>
                            </div>
                            <a href="{{ route('existencias.show', $m) }}"
                                class="text-xs font-bold text-red-600 hover:text-red-800 whitespace-nowrap">
                                Ver →
                            </a>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <x-heroicon-o-check-circle class="w-12 h-12 text-emerald-300 mx-auto mb-2" />
                            <p class="text-sm text-slate-400">Sin medicamentos críticos</p>
                        </div>
                    @endforelse
                </div>

                {{-- Recetas pendientes --}}
                <div class="bg-white rounded-3xl border border-slate-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-sm font-bold text-slate-700 flex items-center gap-2">
                            <x-heroicon-o-clipboard-document-list class="w-5 h-5 text-blue-600" />
                            Recetas pendientes
                        </h4>
                        <a href="{{ route('dispensaciones.index') }}"
                            class="text-xs font-bold text-blue-600 hover:text-blue-800">
                            Ver todas →
                        </a>
                    </div>

                    @forelse($recetasPendientes as $c)
                        <div class="flex items-center gap-3 py-3 border-b border-slate-100 last:border-0">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-slate-800 truncate">
                                    {{ $c->paciente->nombre_completo ?? 'Paciente' }}
                                </p>
                                <p class="text-xs text-slate-500 truncate">
                                    {{ $c->created_at->diffForHumans() }}
                                    · {{ $c->medicamentos->count() }} medicamentos
                                </p>
                            </div>
                            <a href="{{ route('dispensaciones.show', $c) }}"
                                class="text-xs font-bold text-blue-600 hover:text-blue-800 whitespace-nowrap">
                                Dispensar →
                            </a>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <x-heroicon-o-check-circle class="w-12 h-12 text-emerald-300 mx-auto mb-2" />
                            <p class="text-sm text-slate-400">Sin recetas pendientes</p>
                        </div>
                    @endforelse
                </div>

            </div>

            {{-- TOP DEMANDA --}}
            <div class="bg-white rounded-3xl border border-slate-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-sm font-bold text-slate-700 flex items-center gap-2">
                        <x-heroicon-o-chart-bar class="w-5 h-5 text-indigo-600" />
                        Top 5 medicamentos con mayor demanda (30 días)
                    </h4>
                    <a href="{{ route('prediccion.index') }}"
                        class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                        Ver predicción →
                    </a>
                </div>

                @forelse($topDemanda as $item)
                    <div class="flex items-center gap-3 py-3 border-b border-slate-100 last:border-0">
                        <div
                            class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-bold">
                            {{ $loop->iteration }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-slate-800 truncate">
                                {{ $item['nombre'] ?? ($item['medicamento'] ?? 'N/A') }}
                            </p>
                            <p class="text-xs text-slate-500">
                                Consumo: {{ $item['total'] ?? ($item['cantidad'] ?? 0) }} unidades
                            </p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-400 py-4 text-center">Sin datos de demanda</p>
                @endforelse
            </div>

            {{-- ALERTAS DE STOCK --}}
            @if ($alertasStockTotal > 0)
                <div class="bg-amber-50 border border-amber-200 rounded-3xl p-6">
                    <h4 class="text-sm font-bold text-amber-800 mb-4 flex items-center gap-2">
                        <x-heroicon-o-bell-alert class="w-5 h-5" />
                        Alertas de stock ({{ $alertasStockTotal }})
                    </h4>
                    @foreach ($alertasStock as $alerta)
                        <div class="flex items-center gap-3 py-2 border-b border-amber-100 last:border-0">
                            <div class="w-2 h-2 rounded-full bg-amber-500"></div>
                            <div class="flex-1">
                                <p class="text-sm font-bold text-amber-900">{{ $alerta->titulo }}</p>
                                <p class="text-xs text-amber-700">{{ $alerta->mensaje ?? '' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- ACCESOS RÁPIDOS --}}
            <div>
                <h4 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-4">
                    Accesos rápidos
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">

                    <a href="{{ route('medicamentos.index') }}"
                        class="group bg-white p-5 rounded-3xl border border-slate-100 shadow-sm hover:shadow-md transition-all">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center group-hover:bg-amber-600 group-hover:text-white transition-colors">
                                <x-heroicon-o-beaker class="w-5 h-5" />
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-800">Medicamentos</p>
                                <p class="text-xs text-slate-500">Catálogo</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('existencias.index') }}"
                        class="group bg-white p-5 rounded-3xl border border-slate-100 shadow-sm hover:shadow-md transition-all">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                <x-heroicon-o-archive-box class="w-5 h-5" />
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-800">Existencias</p>
                                <p class="text-xs text-slate-500">Inventario</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('movimientos.index') }}"
                        class="group bg-white p-5 rounded-3xl border border-slate-100 shadow-sm hover:shadow-md transition-all">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                                <x-heroicon-o-arrow-path class="w-5 h-5" />
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-800">Movimientos</p>
                                <p class="text-xs text-slate-500">Entradas/salidas</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('prediccion.index') }}"
                        class="group bg-gradient-to-br from-amber-600 to-orange-700 p-5 rounded-3xl shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all text-left text-white">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                                <x-heroicon-o-cpu-chip class="w-5 h-5" />
                            </div>
                            <div>
                                <p class="text-sm font-bold">Predicción IA</p>
                                <p class="text-xs text-amber-100">Análisis de demanda</p>
                            </div>
                        </div>
                    </a>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
