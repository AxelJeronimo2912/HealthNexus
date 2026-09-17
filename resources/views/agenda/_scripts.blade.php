<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-xl text-slate-800 leading-tight">Agenda Médica</h2>
                <p class="text-xs text-slate-400">Planificación semanal de citas y disponibilidad</p>
            </div>
            <a href="{{ route('agenda.create') }}"
               class="bg-blue-600 hover:bg-blue-700 active:scale-95 text-white px-4 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 shadow-xs hover:shadow-md transition-all">
                <x-heroicon-o-plus class="w-4 h-4" />
                Nueva Cita
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        @if (session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200/60 text-emerald-800 rounded-2xl text-xs font-semibold flex items-center gap-2 animate-fade-in">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- Navegación de semana --}}
        <div class="flex flex-col sm:flex-row justify-between items-center bg-white border border-slate-100 p-3 sm:p-4 rounded-2xl shadow-xs gap-3">
            <a href="{{ route('agenda.index', ['fecha' => $inicioSemana->copy()->subWeek()->format('Y-m-d')]) }}"
               class="px-4 py-2 bg-slate-50 hover:bg-slate-100 text-slate-600 rounded-xl text-xs font-bold border border-slate-200 transition-all flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Semana anterior
            </a>

            <div class="text-center">
                <p class="text-sm sm:text-base font-bold text-slate-800">
                    {{ $inicioSemana->format('d M') }} — {{ $finSemana->format('d M Y') }}
                </p>
                <a href="{{ route('agenda.index') }}" class="text-[11px] text-blue-600 font-semibold hover:underline">
                    Ir a la semana actual
                </a>
            </div>

            <a href="{{ route('agenda.index', ['fecha' => $inicioSemana->copy()->addWeek()->format('Y-m-d')]) }}"
               class="px-4 py-2 bg-slate-50 hover:bg-slate-100 text-slate-600 rounded-xl text-xs font-bold border border-slate-200 transition-all flex items-center gap-1.5">
                Semana siguiente
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        {{-- Métricas Semanales --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-xs hover:border-slate-200 transition-all">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Semana</p>
                <p class="text-2xl sm:text-3xl font-extrabold text-slate-800 mt-1">{{ $stats['total_semana'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-xs hover:border-slate-200 transition-all">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Programadas</p>
                <p class="text-2xl sm:text-3xl font-extrabold text-blue-600 mt-1">{{ $stats['programadas'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-xs hover:border-slate-200 transition-all">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Atendidas</p>
                <p class="text-2xl sm:text-3xl font-extrabold text-emerald-600 mt-1">{{ $stats['atendidas'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-xs hover:border-slate-200 transition-all">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Canceladas</p>
                <p class="text-2xl sm:text-3xl font-extrabold text-rose-600 mt-1">{{ $stats['canceladas'] }}</p>
            </div>
        </div>

        {{-- Grid de Días de la Semana --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-7 gap-3">
            @foreach ($citasPorDia as $fecha => $citas)
                @php
                    $dia = \Carbon\Carbon::parse($fecha);
                    $esHoy = $dia->isToday();
                @endphp
                <div class="bg-white rounded-2xl border {{ $esHoy ? 'border-blue-500/80 ring-2 ring-blue-500/20' : 'border-slate-100' }} shadow-xs overflow-hidden flex flex-col transition-all">
                    
                    {{-- Cabeza del Día --}}
                    <div class="p-3 text-center border-b {{ $esHoy ? 'bg-blue-50/60 border-blue-100' : 'bg-slate-50/50 border-slate-100' }}">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                            {{ ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'][$dia->dayOfWeek] }}
                        </p>
                        <p class="text-base font-bold mt-0.5 {{ $esHoy ? 'text-blue-600' : 'text-slate-800' }}">
                            {{ $dia->format('d') }}
                        </p>
                    </div>

                    {{-- Lista de Citas del Día --}}
                    <div class="p-2 space-y-2 flex-1 min-h-[220px] bg-slate-50/20">
                        @forelse ($citas as $cita)
                            @php
                                $triageBorder = match ($cita->triage_al_momento) {
                                    'rojo' => 'border-l-4 border-l-rose-500',
                                    'naranja' => 'border-l-4 border-l-amber-500',
                                    'amarillo' => 'border-l-4 border-l-yellow-400',
                                    'verde' => 'border-l-4 border-l-emerald-500',
                                    'azul' => 'border-l-4 border-l-sky-500',
                                    default => 'border-l-4 border-l-slate-300',
                                };
                            @endphp
                            <a href="#"
                               class="block p-2.5 bg-white border border-slate-100 rounded-xl shadow-xs hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 {{ $triageBorder }}">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="font-bold text-slate-800 text-xs">
                                        {{ $cita->fecha_hora->format('H:i') }}
                                    </span>
                                    <span class="px-1.5 py-0.5 rounded-md text-[9px] font-bold uppercase tracking-wide {{ $cita->estado_color }}">
                                        {{ $cita->estado_label }}
                                    </span>
                                </div>
                                
                                <p class="text-slate-700 font-semibold truncate text-xs">
                                    {{ $cita->paciente->nombre_completo }}
                                </p>
                                <p class="text-slate-400 truncate text-[10px] font-medium mt-0.5">
                                    Dr. {{ $cita->medico->nombre_completo }}
                                </p>
                            </a>
                        @empty
                            <div class="flex items-center justify-center h-full min-h-[160px]">
                                <span class="text-[11px] font-medium text-slate-400">Sin citas</span>
                            </div>
                        @endforelse
                    </div>

                </div>
            @endforeach
        </div>

    </div>
</x-app-layout>