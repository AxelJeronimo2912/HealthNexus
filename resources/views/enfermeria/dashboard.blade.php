<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                    Panel Enfermería — HealthNexus
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">
                    Cuidados y monitoreo de pacientes
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
            <div class="bg-gradient-to-r from-purple-600 to-fuchsia-800 rounded-3xl p-6 sm:p-8 shadow-lg text-white">
                <h3 class="text-2xl font-bold">
                    Bienvenido/a, {{ auth()->user()->nombre_completo }}
                </h3>
                <p class="text-sm text-purple-100 mt-1">Rol: Enfermería</p>
                <p class="text-xs text-purple-200">
                    {{ now()->translatedFormat('l d \d\e F \d\e Y') }}
                </p>
            </div>

            {{-- KPIs --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

                <a href="{{ route('signos-vitales.index') }}"
                    class="group bg-white p-6 rounded-3xl shadow-sm border border-slate-100 hover:shadow-lg hover:-translate-y-1 transition-all">
                    <div
                        class="w-12 h-12 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center group-hover:bg-red-600 group-hover:text-white transition-colors">
                        <x-heroicon-o-heart class="w-6 h-6" />
                    </div>
                    <p class="text-xs font-bold text-slate-400 uppercase mt-4">Signos hoy</p>
                    <p class="text-3xl font-extrabold text-slate-800 mt-1">{{ $signosHoy }}</p>
                    <p class="text-xs text-slate-500 mt-1">Tomas registradas</p>
                </a>

                <a href="{{ route('camas.index') }}"
                    class="group bg-white p-6 rounded-3xl shadow-sm border border-slate-100 hover:shadow-lg hover:-translate-y-1 transition-all">
                    <div
                        class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                        <x-heroicon-o-home-modern class="w-6 h-6" />
                    </div>
                    <p class="text-xs font-bold text-slate-400 uppercase mt-4">Camas ocupadas</p>
                    <p class="text-3xl font-extrabold text-slate-800 mt-1">{{ $camasOcupadas }}/{{ $camasTotales }}</p>
                    <p class="text-xs text-slate-500 mt-1">{{ $camasDisponibles }} disponibles</p>
                </a>

                <a href="{{ route('admisiones.index') }}"
                    class="group bg-white p-6 rounded-3xl shadow-sm border border-slate-100 hover:shadow-lg hover:-translate-y-1 transition-all">
                    <div
                        class="w-12 h-12 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center group-hover:bg-orange-600 group-hover:text-white transition-colors">
                        <x-heroicon-o-clipboard-document-check class="w-6 h-6" />
                    </div>
                    <p class="text-xs font-bold text-slate-400 uppercase mt-4">Admisiones en espera</p>
                    <p class="text-3xl font-extrabold text-slate-800 mt-1">{{ $admisionesEnEsperaTotal }}</p>
                    <p class="text-xs text-slate-500 mt-1">Requieren atención</p>
                </a>

                <a href="{{ route('alertas.index') }}"
                    class="group bg-white p-6 rounded-3xl shadow-sm border border-slate-100 hover:shadow-lg hover:-translate-y-1 transition-all">
                    <div
                        class="w-12 h-12 rounded-2xl bg-yellow-50 text-yellow-600 flex items-center justify-center group-hover:bg-yellow-600 group-hover:text-white transition-colors">
                        <x-heroicon-o-bell-alert class="w-6 h-6" />
                    </div>
                    <p class="text-xs font-bold text-slate-400 uppercase mt-4">Alertas activas</p>
                    <p class="text-3xl font-extrabold text-slate-800 mt-1">{{ $alertasActivasTotal }}</p>
                    <p class="text-xs text-slate-500 mt-1">Últimas 5 mostradas</p>
                </a>

            </div>

            {{-- PACIENTES EN SEGUIMIENTO + ADMISIONES EN ESPERA --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Pacientes en seguimiento --}}
                <div class="bg-white rounded-3xl border border-slate-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-sm font-bold text-slate-700 flex items-center gap-2">
                            <x-heroicon-o-user-group class="w-5 h-5 text-purple-600" />
                            Pacientes en seguimiento
                        </h4>
                        <a href="{{ route('pacientes.index') }}"
                            class="text-xs font-bold text-purple-600 hover:text-purple-800">
                            Ver todos →
                        </a>
                    </div>

                    @forelse($pacientesEnSeguimiento as $p)
                        <div class="flex items-center gap-3 py-3 border-b border-slate-100 last:border-0">
                            <div
                                class="w-2 h-2 rounded-full {{ $p->en_seguimiento ? 'bg-red-500 animate-pulse' : 'bg-emerald-500' }}">
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-slate-800 truncate">{{ $p->nombre_completo }}</p>
                                <p class="text-xs text-slate-500 truncate">
                                    {{ $p->edad }} años · Último signo:
                                    {{ $p->ultimoSignoVital?->created_at?->diffForHumans() ?? 'Sin datos' }}
                                </p>
                            </div>
                            <a href="{{ route('signos-vitales.create') }}?paciente_id={{ $p->id }}"
                                class="text-xs font-bold text-purple-600 hover:text-purple-800 whitespace-nowrap">
                                Registrar →
                            </a>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <x-heroicon-o-check-circle class="w-12 h-12 text-emerald-300 mx-auto mb-2" />
                            <p class="text-sm text-slate-400">Sin pacientes en seguimiento</p>
                        </div>
                    @endforelse
                </div>

                {{-- Admisiones en espera --}}
                <div class="bg-white rounded-3xl border border-slate-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-sm font-bold text-slate-700 flex items-center gap-2">
                            <x-heroicon-o-clipboard-document-list class="w-5 h-5 text-orange-600" />
                            Admisiones en espera
                        </h4>
                        <a href="{{ route('admisiones.index') }}"
                            class="text-xs font-bold text-orange-600 hover:text-orange-800">
                            Ver todas →
                        </a>
                    </div>

                    @forelse($admisionesEnEspera as $a)
                        <div class="flex items-center gap-3 py-3 border-b border-slate-100 last:border-0">
                            <span
                                class="px-2 py-0.5 rounded text-xs font-bold whitespace-nowrap
                                @if ($a->triage === 'rojo') bg-red-100 text-red-700
                                @elseif($a->triage === 'naranja') bg-orange-100 text-orange-700
                                @elseif($a->triage === 'amarillo') bg-yellow-100 text-yellow-700
                                @elseif($a->triage === 'verde') bg-green-100 text-green-700
                                @elseif($a->triage === 'azul') bg-blue-100 text-blue-700
                                @else bg-slate-100 text-slate-600 @endif">
                                {{ strtoupper($a->triage ?? 'N/A') }}
                            </span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-slate-800 truncate">
                                    {{ $a->paciente->nombre_completo ?? 'Paciente' }}
                                </p>
                                <p class="text-xs text-slate-500 truncate">
                                    {{ $a->folio }} ·
                                    {{ $a->fecha_hora_llegada?->diffForHumans() ?? 'Sin fecha' }}
                                </p>
                            </div>
                            <a href="{{ route('admisiones.show', $a) }}"
                                class="text-xs font-bold text-orange-600 hover:text-orange-800 whitespace-nowrap">
                                Ver →
                            </a>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <x-heroicon-o-check-circle class="w-12 h-12 text-emerald-300 mx-auto mb-2" />
                            <p class="text-sm text-slate-400">Sin admisiones en espera</p>
                        </div>
                    @endforelse
                </div>

            </div>

            {{-- ACCESOS RÁPIDOS --}}
            <div>
                <h4 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-4">
                    Accesos rápidos
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">

                    <a href="{{ route('signos-vitales.create') }}"
                        class="group bg-white p-5 rounded-3xl border border-slate-100 shadow-sm hover:shadow-md transition-all">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center group-hover:bg-red-600 group-hover:text-white transition-colors">
                                <x-heroicon-o-plus class="w-5 h-5" />
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-800">Registrar signos</p>
                                <p class="text-xs text-slate-500">Nueva toma</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('admisiones.index') }}"
                        class="group bg-white p-5 rounded-3xl border border-slate-100 shadow-sm hover:shadow-md transition-all">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center group-hover:bg-orange-600 group-hover:text-white transition-colors">
                                <x-heroicon-o-clipboard-document-check class="w-5 h-5" />
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-800">Admisiones</p>
                                <p class="text-xs text-slate-500">Ingresos</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('enfermeria.administraciones.create') }}"
                        class="group bg-white p-5 rounded-3xl border border-slate-100 shadow-sm hover:shadow-md transition-all">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-xl bg-cyan-50 text-cyan-600 flex items-center justify-center group-hover:bg-cyan-600 group-hover:text-white transition-colors">
                                <x-heroicon-o-beaker class="w-5 h-5" />
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-800">Administrar medicamento</p>
                                <p class="text-xs text-slate-500">Registrar dosis</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('asistente.index') }}"
                        class="group bg-gradient-to-br from-purple-600 to-fuchsia-800 p-5 rounded-3xl shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all text-left text-white">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                                <x-heroicon-o-sparkles class="w-5 h-5" />
                            </div>
                            <div>
                                <p class="text-sm font-bold">Asistente IA</p>
                                <p class="text-xs text-purple-100">Consultas</p>
                            </div>
                        </div>
                    </a>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
