@php $esAdmin = auth()->user()->hasRole('administrador'); @endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                    Panel de Administración — HealthNexus
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">
                    Resumen operativo del hospital en tiempo real
                </p>
            </div>
            <div
                class="hidden md:flex items-center gap-2 px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full text-xs font-semibold">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Sistema en línea
            </div>
        </div>
    </x-slot>

    <div x-data="{ modalAbierto: null }" @abrir-modal-sla.window="modalAbierto = 'sla'"
        @abrir-modal-fenotipado.window="modalAbierto = 'fenotipado'" @abrir-modal-dwh.window="modalAbierto = 'dwh'"
        @abrir-modal-ia-medica.window="modalAbierto = 'ia-medica'" @keydown.escape.window="modalAbierto = null"
        class="py-8 bg-slate-100 min-h-screen">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- TARJETA DE BIENVENIDA --}}
            <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 rounded-3xl p-6 sm:p-8 shadow-lg text-white">
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <h3 class="text-2xl font-bold">
                            Bienvenido, {{ auth()->user()->name }}
                        </h3>
                        <p class="text-sm text-indigo-100">
                            Rol activo:
                            <span class="font-semibold capitalize">
                                {{ auth()->user()->getRoleNames()->first() ?? 'Administrador' }}
                            </span>
                        </p>
                        <p class="text-xs text-indigo-200">
                            {{ now()->translatedFormat('l d \d\e F \d\e Y') }}
                        </p>
                    </div>
                    <div
                        class="flex items-center gap-3 bg-white/10 backdrop-blur rounded-2xl p-3 border border-white/20">
                        <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center">
                            <x-heroicon-s-heart class="w-6 h-6 text-white" />
                        </div>
                        <div class="pr-2">
                            <p class="text-xs font-bold">HealthNexus</p>
                            <p class="text-[10px] text-indigo-200">Sistema Médico</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 4 CARDS PRINCIPALES (KPIs) --}}
            <div>
                <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-4">
                    Indicadores Críticos
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

                    {{-- CARD 1: ALERTAS --}}
                    <a href="{{ route('alertas.index') }}"
                        class="group bg-white p-6 rounded-3xl shadow-sm border border-slate-100 hover:shadow-lg hover:-translate-y-1 transition-all duration-200">
                        <div class="flex items-start justify-between">
                            <div
                                class="w-12 h-12 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center group-hover:bg-red-600 group-hover:text-white transition-colors">
                                <x-heroicon-o-bell-alert class="w-6 h-6" />
                            </div>
                            @if ($alertasCriticas > 0)
                                <span
                                    class="px-2 py-0.5 bg-red-100 text-red-700 rounded-full text-xs font-bold animate-pulse">
                                    {{ $alertasCriticas }} críticas
                                </span>
                            @endif
                        </div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-4">
                            Alertas Activas
                        </p>
                        <p class="text-3xl font-extrabold text-slate-800 mt-1">
                            {{ $alertasActivas }}
                        </p>
                        <p class="text-xs text-slate-500 mt-2">
                            Requieren atención
                        </p>
                        <div class="mt-4 flex items-center text-xs font-semibold text-red-600">
                            <span>Ver todas</span>
                            <x-heroicon-o-arrow-right
                                class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" />
                        </div>
                    </a>

                    {{-- CARD 2: MEDICAMENTOS BAJOS --}}
                    <a href="{{ route('existencias.index', ['filtro' => 'bajo']) }}"
                        class="group bg-white p-6 rounded-3xl shadow-sm border border-slate-100 hover:shadow-lg hover:-translate-y-1 transition-all duration-200">
                        <div class="flex items-start justify-between">
                            <div
                                class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center group-hover:bg-amber-600 group-hover:text-white transition-colors">
                                <x-heroicon-o-beaker class="w-6 h-6" />
                            </div>
                        </div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-4">
                            Stock Bajo
                        </p>
                        <p class="text-3xl font-extrabold text-slate-800 mt-1">
                            {{ $medicamentosBajos }}
                        </p>
                        <p class="text-xs text-slate-500 mt-2">
                            Medicamentos por debajo del mínimo
                        </p>
                        <div class="mt-4 flex items-center text-xs font-semibold text-amber-600">
                            <span>Ver detalle</span>
                            <x-heroicon-o-arrow-right
                                class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" />
                        </div>
                    </a>

                    {{-- CARD 3: CAMAS DISPONIBLES --}}
                    <a href="{{ route('camas.index') }}"
                        class="group bg-white p-6 rounded-3xl shadow-sm border border-slate-100 hover:shadow-lg hover:-translate-y-1 transition-all duration-200">
                        <div class="flex items-start justify-between">
                            <div
                                class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                                <x-heroicon-o-home-modern class="w-6 h-6" />
                            </div>
                            @php
                                $ocupacion =
                                    $camasTotales > 0
                                        ? round((($camasTotales - $camasDisponibles) / $camasTotales) * 100)
                                        : 0;
                            @endphp
                            <span
                                class="px-2 py-0.5 {{ $ocupacion > 85 ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700' }} rounded-full text-xs font-bold">
                                {{ $ocupacion }}% ocupado
                            </span>
                        </div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-4">
                            Camas Disponibles
                        </p>
                        <p class="text-3xl font-extrabold text-slate-800 mt-1">
                            {{ $camasDisponibles }}
                        </p>
                        <p class="text-xs text-slate-500 mt-2">
                            De {{ $camasTotales }} camas totales
                        </p>
                        <div class="mt-4 flex items-center text-xs font-semibold text-emerald-600">
                            <span>Ver distribución</span>
                            <x-heroicon-o-arrow-right
                                class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" />
                        </div>
                    </a>

                    {{-- CARD 4: AUDITORÍA CRÍTICA --}}
                    <a href="{{ route('auditoria.index', ['severidad' => 'critical']) }}"
                        class="group bg-white p-6 rounded-3xl shadow-sm border border-slate-100 hover:shadow-lg hover:-translate-y-1 transition-all duration-200">
                        <div class="flex items-start justify-between">
                            <div
                                class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                                <x-heroicon-o-shield-exclamation class="w-6 h-6" />
                            </div>
                            @if ($auditoriaCritica > 0)
                                <span class="px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded-full text-xs font-bold">
                                    {{ $auditoriaCritica }} hoy
                                </span>
                            @endif
                        </div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-4">
                            Eventos Críticos
                        </p>
                        <p class="text-3xl font-extrabold text-slate-800 mt-1">
                            {{ $auditoriaCritica }}
                        </p>
                        <p class="text-xs text-slate-500 mt-2">
                            Auditoría de hoy
                        </p>
                        <div class="mt-4 flex items-center text-xs font-semibold text-indigo-600">
                            <span>Ver logs</span>
                            <x-heroicon-o-arrow-right
                                class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" />
                        </div>
                    </a>

                </div>
            </div>

            {{-- RESUMEN DE OPERACIÓN --}}
            <div>
                <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-4">
                    Operación de Hoy
                </h3>

                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <a href="{{ route('pacientes.index') }}"
                        class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-all">
                        <div class="flex items-center gap-2 text-slate-400 text-xs font-bold uppercase">
                            <x-heroicon-o-user-group class="w-4 h-4" />
                            Pacientes
                        </div>
                        <p class="text-2xl font-extrabold text-slate-800 mt-1">{{ $resumen['pacientes_activos'] }}</p>
                    </a>
                    <a href="{{ route('citas.index') }}"
                        class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-all">
                        <div class="flex items-center gap-2 text-slate-400 text-xs font-bold uppercase">
                            <x-heroicon-o-calendar-days class="w-4 h-4" />
                            Citas hoy
                        </div>
                        <p class="text-2xl font-extrabold text-slate-800 mt-1">{{ $resumen['citas_hoy'] }}</p>
                    </a>
                    <a href="{{ route('citas.index') }}"
                        class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-all">
                        <div class="flex items-center gap-2 text-slate-400 text-xs font-bold uppercase">
                            <x-heroicon-o-clipboard-document-list class="w-4 h-4" />
                            Consultas
                        </div>
                        <p class="text-2xl font-extrabold text-slate-800 mt-1">{{ $resumen['consultas_hoy'] }}</p>
                    </a>
                    <a href="{{ route('camas.index') }}"
                        class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-all">
                        <div class="flex items-center gap-2 text-slate-400 text-xs font-bold uppercase">
                            <x-heroicon-o-home-modern class="w-4 h-4" />
                            Ocupadas
                        </div>
                        <p class="text-2xl font-extrabold text-slate-800 mt-1">{{ $resumen['camas_ocupadas'] }}</p>
                    </a>
                </div>
            </div>

            {{-- ACCESOS RÁPIDOS --}}
            <div>
                <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-4">
                    Modelos
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">



                    @if ($esAdmin)
                        <button type="button" @click="$dispatch('abrir-modal-sla')"
                            class="group bg-gradient-to-br from-orange-500 to-orange-700 p-5 rounded-3xl shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all text-left text-white">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                                    <x-heroicon-o-fire class="w-5 h-5" />
                                </div>
                                <div>
                                    <p class="text-sm font-bold">Pulso Operativo SLA</p>
                                    <p class="text-xs text-orange-100">Detección de anomalías</p>
                                </div>
                            </div>
                        </button>

                        <button type="button" @click="$dispatch('abrir-modal-fenotipado')"
                            class="group bg-gradient-to-br from-rose-500 to-red-700 p-5 rounded-3xl shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all text-left text-white">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                                    <x-heroicon-o-beaker class="w-5 h-5" />
                                </div>
                                <div>
                                    <p class="text-sm font-bold">Fenotipado Clínico</p>
                                    <p class="text-xs text-rose-100">K-Means + PCA</p>
                                </div>
                            </div>
                        </button>

                        <button type="button" @click="$dispatch('abrir-modal-dwh')"
                            class="group bg-gradient-to-br from-violet-600 to-indigo-800 p-5 rounded-3xl shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all text-left text-white">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                                    <x-heroicon-o-circle-stack class="w-5 h-5" />
                                </div>
                                <div>
                                    <p class="text-sm font-bold">Data Warehouse</p>
                                    <p class="text-xs text-indigo-100">Big Data · Analítica</p>
                                </div>
                            </div>
                        </button>

                        <button type="button" @click="$dispatch('abrir-modal-ia-medica')"
                            class="group bg-gradient-to-br from-red-600 to-rose-800 p-5 rounded-3xl shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all text-left text-white">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                                    <x-heroicon-o-heart class="w-5 h-5" />
                                </div>
                                <div>
                                    <p class="text-sm font-bold">IA Médica Predictiva</p>
                                    <p class="text-xs text-rose-100">5 algoritmos</p>
                                </div>
                            </div>
                        </button>
                    @endif

                </div>
            </div>

        </div>

        @if ($esAdmin)
            @include('admin.partials._modal-sla')
            @include('admin.partials._modal-fenotipado')
            @include('admin.partials._modal-dwh')
            @include('admin.partials._modal-ia-medica')
        @endif

    </div>
</x-app-layout>
