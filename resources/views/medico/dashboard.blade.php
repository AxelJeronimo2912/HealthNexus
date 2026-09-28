<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                    Panel Médico — HealthNexus
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">
                    Tu agenda y pacientes del día
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
            <div class="bg-gradient-to-r from-teal-600 to-cyan-800 rounded-3xl p-6 sm:p-8 shadow-lg text-white">
                <h3 class="text-2xl font-bold">Bienvenido, Dr. {{ auth()->user()->nombre_completo }}</h3>
                <p class="text-sm text-teal-100 mt-1">Rol: Médico / Especialista</p>
                <p class="text-xs text-teal-200">{{ now()->translatedFormat('l d \d\e F \d\e Y') }}</p>
            </div>

            {{-- KPIs --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

                <a href="{{ route('citas.index') }}"
                    class="group bg-white p-6 rounded-3xl shadow-sm border border-slate-100 hover:shadow-lg hover:-translate-y-1 transition-all">
                    <div
                        class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center group-hover:bg-teal-600 group-hover:text-white transition-colors">
                        <x-heroicon-o-calendar-days class="w-6 h-6" />
                    </div>
                    <p class="text-xs font-bold text-slate-400 uppercase mt-4">Citas hoy</p>
                    <p class="text-3xl font-extrabold text-slate-800 mt-1">{{ $citasHoy->count() }}</p>
                    <p class="text-xs text-slate-500 mt-1">
                        {{ $citasAtendidasHoy }} atendidas
                    </p>
                </a>

                <a href="{{ route('pacientes.index') }}"
                    class="group bg-white p-6 rounded-3xl shadow-sm border border-slate-100 hover:shadow-lg hover:-translate-y-1 transition-all">
                    <div
                        class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors">
                        <x-heroicon-o-user-group class="w-6 h-6" />
                    </div>
                    <p class="text-xs font-bold text-slate-400 uppercase mt-4">Pacientes asignados</p>
                    <p class="text-3xl font-extrabold text-slate-800 mt-1">{{ $pacientesAsignados }}</p>
                </a>

                <div class="group bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <x-heroicon-o-clipboard-document-list class="w-6 h-6" />
                    </div>
                    <p class="text-xs font-bold text-slate-400 uppercase mt-4">Consultas registradas hoy</p>
                    <p class="text-3xl font-extrabold text-slate-800 mt-1">{{ $consultasHoy }}</p>
                </div>

                <a href="{{ route('alertas.index') }}"
                    class="group bg-white p-6 rounded-3xl shadow-sm border border-slate-100 hover:shadow-lg hover:-translate-y-1 transition-all">
                    <div
                        class="w-12 h-12 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center group-hover:bg-red-600 group-hover:text-white transition-colors">
                        <x-heroicon-o-bell-alert class="w-6 h-6" />
                    </div>
                    <p class="text-xs font-bold text-slate-400 uppercase mt-4">Alertas activas</p>
                    <p class="text-3xl font-extrabold text-slate-800 mt-1">{{ $alertasActivas }}</p>
                </a>

            </div>

            {{-- AGENDA DE HOY + PACIENTES EN RIESGO --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Agenda de hoy --}}
                <div class="bg-white rounded-3xl border border-slate-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-sm font-bold text-slate-700 flex items-center gap-2">
                            <x-heroicon-o-clock class="w-5 h-5 text-teal-600" />
                            Agenda de hoy
                        </h4>
                        <a href="{{ route('citas.index') }}"
                            class="text-xs font-bold text-teal-600 hover:text-teal-800">
                            Ver todo →
                        </a>
                    </div>

                    @forelse($citasHoy as $cita)
                        <div class="flex items-center gap-3 py-3 border-b border-slate-100 last:border-0">
                            <div
                                class="w-2 h-2 rounded-full
                                {{ $cita->estado === 'atendida' ? 'bg-emerald-500' : '' }}
                                {{ $cita->estado === 'en_curso' ? 'bg-yellow-500 animate-pulse' : '' }}
                                {{ $cita->estado === 'confirmada' ? 'bg-teal-500' : '' }}
                                {{ $cita->estado === 'cancelada' ? 'bg-red-500' : '' }}
                                {{ !$cita->estado ? 'bg-slate-300' : '' }}">
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-slate-800 truncate">
                                    {{ $cita->paciente->nombre_completo ?? 'Paciente' }}
                                </p>
                                <p class="text-xs text-slate-500">
                                    {{ \Carbon\Carbon::parse($cita->fecha_hora)->format('H:i') }}
                                    · {{ ucfirst($cita->estado ?? 'pendiente') }}
                                </p>
                            </div>
                            @if ($cita->estado === 'confirmada' && !$cita->consulta)
                                <a href="{{ route('consultas.iniciar', $cita) }}"
                                    class="text-xs font-bold text-teal-600 hover:text-teal-800 whitespace-nowrap">
                                    Iniciar →
                                </a>
                            @elseif($cita->consulta)
                                <a href="{{ route('consultas.show', $cita->consulta) }}"
                                    class="text-xs font-bold text-indigo-600 hover:text-indigo-800 whitespace-nowrap">
                                    Ver consulta →
                                </a>
                            @else
                                <a href="{{ route('citas.show', $cita) }}"
                                    class="text-xs font-bold text-slate-500 hover:text-slate-700 whitespace-nowrap">
                                    Ver →
                                </a>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <x-heroicon-o-calendar class="w-12 h-12 text-slate-300 mx-auto mb-2" />
                            <p class="text-sm text-slate-400">Sin citas agendadas hoy</p>
                        </div>
                    @endforelse
                </div>

                {{-- Pacientes en riesgo --}}
                <div class="bg-white rounded-3xl border border-slate-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-sm font-bold text-slate-700 flex items-center gap-2">
                            <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-red-600" />
                            Pacientes en riesgo (24h)
                        </h4>
                    </div>

                    @forelse($pacientesRiesgo as $p)
                        <div class="flex items-center gap-3 py-3 border-b border-slate-100 last:border-0">
                            <div class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-slate-800 truncate">{{ $p->nombre_completo }}</p>
                                <p class="text-xs text-slate-500">
                                    {{ $p->edad }} años
                                    @if ($p->ultimoSignoVital)
                                        · Triage: {{ ucfirst($p->ultimoSignoVital->triage ?? 'sin triaje') }}
                                    @endif
                                </p>
                            </div>
                            <a href="{{ route('expedientes.show', $p) }}"
                                class="text-xs font-bold text-red-600 hover:text-red-800 whitespace-nowrap">
                                Ver →
                            </a>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <x-heroicon-o-check-circle class="w-12 h-12 text-emerald-300 mx-auto mb-2" />
                            <p class="text-sm text-slate-400">Sin pacientes en riesgo</p>
                        </div>
                    @endforelse
                </div>

            </div>

            {{-- PRÓXIMAS CITAS --}}
            <div class="bg-white rounded-3xl border border-slate-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-sm font-bold text-slate-700 flex items-center gap-2">
                        <x-heroicon-o-calendar class="w-5 h-5 text-indigo-600" />
                        Próximas citas (3 días)
                    </h4>
                    <a href="{{ route('agenda.index') }}"
                        class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                        Ver agenda →
                    </a>
                </div>

                @forelse($proximasCitas as $cita)
                    <div class="flex items-center justify-between py-3 border-b border-slate-100 last:border-0">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-bold text-slate-800 truncate">
                                {{ $cita->paciente->nombre_completo ?? 'Paciente' }}
                            </p>
                            <p class="text-xs text-slate-500">
                                {{ \Carbon\Carbon::parse($cita->fecha_hora)->translatedFormat('D d M, H:i') }}
                            </p>
                        </div>
                        <span class="px-2 py-1 bg-teal-50 text-teal-700 rounded-lg text-xs font-bold capitalize">
                            {{ $cita->estado }}
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-slate-400 py-4 text-center">Sin citas próximas</p>
                @endforelse
            </div>

            {{-- ACCESOS RÁPIDOS --}}
            <div>
                <h4 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-4">Accesos rápidos</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">

                    <a href="{{ route('citas.index') }}"
                        class="group bg-white p-5 rounded-3xl border border-slate-100 shadow-sm hover:shadow-md transition-all">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center group-hover:bg-teal-600 group-hover:text-white transition-colors">
                                <x-heroicon-o-calendar-days class="w-5 h-5" />
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-800">Mis citas</p>
                                <p class="text-xs text-slate-500">Consultas del día</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('agenda.index') }}"
                        class="group bg-white p-5 rounded-3xl border border-slate-100 shadow-sm hover:shadow-md transition-all">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                <x-heroicon-o-calendar class="w-5 h-5" />
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-800">Mi agenda</p>
                                <p class="text-xs text-slate-500">Calendario</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('expedientes.index') }}"
                        class="group bg-white p-5 rounded-3xl border border-slate-100 shadow-sm hover:shadow-md transition-all">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                                <x-heroicon-o-document-text class="w-5 h-5" />
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-800">Expedientes</p>
                                <p class="text-xs text-slate-500">Historial clínico</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('asistente.index') }}"
                        class="group bg-gradient-to-br from-teal-600 to-cyan-800 p-5 rounded-3xl shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all text-left text-white">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                                <x-heroicon-o-sparkles class="w-5 h-5" />
                            </div>
                            <div>
                                <p class="text-sm font-bold">Asistente IA</p>
                                <p class="text-xs text-teal-100">Consultas inteligentes</p>
                            </div>
                        </div>
                    </a>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
