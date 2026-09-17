<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight">Agenda Médica</h2>
                <p class="text-xs text-slate-400 mt-0.5">Control semanal de citas y disponibilidad de especialistas</p>
            </div>
            
            {{-- Botón con emisión de evento global para Alpine --}}
            <button type="button" 
                    @click="$dispatch('abrir-modal-crear')"
                    class="bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white px-5 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 shadow-sm hover:shadow-indigo-200 hover:shadow-lg transition-all duration-200 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                Nueva Cita
            </button>
        </div>
    </x-slot>

    {{-- Contenedor principal escuchando el evento global --}}
    <div x-data="{ modalCrear: false, modalDetalle: false, citaDetalle: {} }" 
         @abrir-modal-crear.window="modalCrear = true" 
         class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        @if (session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-2xl text-xs font-semibold flex items-center gap-2 shadow-xs">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- Tarjetas de Métricas --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4 hover:shadow-md transition-shadow">
                <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-600 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">TOTAL SEMANA</p>
                    <p class="text-2xl font-extrabold text-slate-800 mt-0.5">{{ $stats['total_semana'] ?? 0 }}</p>
                </div>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4 hover:shadow-md transition-shadow">
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">PROGRAMADAS</p>
                    <p class="text-2xl font-extrabold text-indigo-600 mt-0.5">{{ $stats['programadas'] ?? 0 }}</p>
                </div>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4 hover:shadow-md transition-shadow">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">ATENDIDAS</p>
                    <p class="text-2xl font-extrabold text-emerald-600 mt-0.5">{{ $stats['atendidas'] ?? 0 }}</p>
                </div>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4 hover:shadow-md transition-shadow">
                <div class="w-12 h-12 rounded-2xl bg-rose-50 flex items-center justify-center text-rose-500 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">CANCELADAS</p>
                    <p class="text-2xl font-extrabold text-rose-600 mt-0.5">{{ $stats['canceladas'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        {{-- NAVEGACIÓN DE SEMANA REDISEÑADA --}}
<div class="flex flex-col sm:flex-row justify-between items-center bg-white border border-slate-100 p-4 rounded-3xl shadow-sm gap-4">
    
    {{-- Botón Semana Anterior --}}
    <a href="{{ route('agenda.index', ['fecha' => $inicioSemana->copy()->subWeek()->format('Y-m-d')]) }}"
       class="w-full sm:w-auto px-5 py-2.5 bg-slate-50 hover:bg-indigo-50/80 text-slate-700 hover:text-indigo-600 rounded-2xl text-xs font-extrabold border border-slate-200/70 hover:border-indigo-200 transition-all duration-200 flex items-center justify-center gap-2.5 group active:scale-95 shadow-2xs hover:shadow-md hover:shadow-indigo-500/5">
        <svg class="w-4 h-4 transition-transform duration-200 group-hover:-translate-x-1 text-slate-400 group-hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
        </svg>
        <span>Semana anterior</span>
    </a>

    {{-- Centro: Rango de Fechas + Botón Indicador "Ir a Hoy" --}}
    <div class="flex flex-col items-center gap-1.5 text-center">
        <p class="text-base font-black text-slate-800 tracking-tight">
            {{ $inicioSemana->format('d M') }} — {{ $finSemana->format('d M Y') }}
        </p>
        <a href="{{ route('agenda.index') }}" 
           class="inline-flex items-center gap-1.5 px-3 py-1 bg-indigo-50 hover:bg-indigo-100/80 text-indigo-700 rounded-full text-[11px] font-bold transition-all duration-200 hover:scale-105 active:scale-95">
            <span class="w-1.5 h-1.5 rounded-full bg-indigo-600 animate-pulse"></span>
            Ir a la semana actual
        </a>
    </div>

    {{-- Botón Semana Siguiente --}}
    <a href="{{ route('agenda.index', ['fecha' => $inicioSemana->copy()->addWeek()->format('Y-m-d')]) }}"
       class="w-full sm:w-auto px-5 py-2.5 bg-slate-50 hover:bg-indigo-50/80 text-slate-700 hover:text-indigo-600 rounded-2xl text-xs font-extrabold border border-slate-200/70 hover:border-indigo-200 transition-all duration-200 flex items-center justify-center gap-2.5 group active:scale-95 shadow-2xs hover:shadow-md hover:shadow-indigo-500/5">
        <span>Semana siguiente</span>
        <svg class="w-4 h-4 transition-transform duration-200 group-hover:translate-x-1 text-slate-400 group-hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
        </svg>
    </a>

</div>

        {{-- Grid de Días --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-7 gap-3">
            @foreach ($citasPorDia as $fecha => $citas)
                @php
                    $dia = \Carbon\Carbon::parse($fecha);
                    $esHoy = $dia->isToday();
                @endphp
                <div class="bg-white rounded-3xl border {{ $esHoy ? 'border-indigo-500 ring-4 ring-indigo-500/10 shadow-md animate-pulse-subtle' : 'border-slate-100' }} shadow-sm overflow-hidden flex flex-col transition-all">
                    
                    <div class="p-3 text-center border-b {{ $esHoy ? 'bg-indigo-50/60 border-indigo-100' : 'bg-slate-50/50 border-slate-100' }}">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                            {{ ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'][$dia->dayOfWeek] }}
                        </p>
                        <p class="text-base font-extrabold mt-0.5 {{ $esHoy ? 'text-indigo-600' : 'text-slate-800' }}">
                            {{ $dia->format('d') }}
                        </p>
                    </div>

                    <div class="p-2 space-y-2 flex-1 min-h-[220px] bg-slate-50/30">
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

                                $avatarBg = match($loop->index % 3) {
                                    0 => 'bg-indigo-100 text-indigo-700',
                                    1 => 'bg-slate-800 text-white',
                                    default => 'bg-slate-100 text-slate-700',
                                };

                                $iniciales = mb_substr($cita->paciente->nombre_completo ?? 'P', 0, 2);
                            @endphp
                            <button type="button" 
                                    @click="citaDetalle = {{ json_encode([
                                        'hora' => $cita->fecha_hora->format('H:i'),
                                        'paciente' => $cita->paciente->nombre_completo,
                                        'paciente_id' => $cita->paciente->id ?? 'N/A',
                                        'medico' => $cita->medico->nombre_completo,
                                        'estado' => $cita->estado_label,
                                        'motivo' => $cita->motivo ?? 'Sin motivo especificado',
                                        'triage' => strtoupper($cita->triage_al_momento ?? 'Sin triage'),
                                        'iniciales' => strtoupper($iniciales),
                                        'avatar_bg' => $avatarBg
                                    ]) }}; modalDetalle = true"
                                    class="w-full text-left p-3 bg-white border border-slate-100 rounded-2xl shadow-xs hover:shadow-md hover:scale-[1.02] active:scale-98 transition-all duration-200 cursor-pointer {{ $triageBorder }}">
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="font-bold text-slate-800 text-xs flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        {{ $cita->fecha_hora->format('H:i') }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wide {{ $cita->estado_color }}">
                                        {{ $cita->estado_label }}
                                    </span>
                                </div>
                                
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full {{ $avatarBg }} flex items-center justify-center text-[10px] font-bold shrink-0">
                                        {{ strtoupper($iniciales) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-slate-800 font-bold truncate text-xs">
                                            {{ $cita->paciente->nombre_completo }}
                                        </p>
                                        <p class="text-slate-400 truncate text-[10px] font-medium">
                                            Dr. {{ $cita->medico->nombre_completo }}
                                        </p>
                                    </div>
                                </div>
                            </button>
                        @empty
                            <div class="flex flex-col items-center justify-center h-full min-h-[160px] text-slate-300">
                                <span class="text-[11px] font-semibold text-slate-400">Sin citas</span>
                            </div>
                        @endforelse
                    </div>

                </div>
            @endforeach
        </div>

        {{-- Modal de Creación --}}
        <div x-show="modalCrear"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4"
             style="display: none;">

            <div x-show="modalCrear"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-90 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-90 translate-y-4"
                 @click.away="modalCrear = false"
                 class="bg-white rounded-3xl shadow-2xl max-w-lg w-full p-6 space-y-5 border border-slate-100 max-h-[90vh] overflow-y-auto">
                
                <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-slate-800 text-base">Agendar Cita Médica</h3>
                            <p class="text-xs text-slate-400">Selecciona paciente, fecha y médico especialista</p>
                        </div>
                    </div>
                    <button type="button" @click="modalCrear = false" class="p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form action="{{ route('agenda.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label for="paciente_id" class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Paciente *</label>
                        <select name="paciente_id" id="paciente_id" required
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all outline-none">
                            <option value="">— Selecciona un paciente —</option>
                            @foreach ($pacientes ?? [] as $p)
                                <option value="{{ $p->id }}">{{ $p->nombre_completo }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label for="fecha" class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Fecha *</label>
                            <input type="date" name="fecha" id="fecha" required value="{{ date('Y-m-d') }}"
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-800 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all outline-none">
                        </div>
                        <div>
                            <label for="hora" class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Hora *</label>
                            <input type="time" name="hora" id="hora" required value="09:00"
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-800 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all outline-none">
                        </div>
                        <div>
                            <label for="duracion_minutos" class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Duración (min) *</label>
                            <select name="duracion_minutos" id="duracion_minutos" required
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-800 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all outline-none">
                                <option value="15">15 min</option>
                                <option value="30" selected>30 min</option>
                                <option value="45">45 min</option>
                                <option value="60">60 min</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="medico_id" class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Médico *</label>
                        <select name="medico_id" id="medico_id" required
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all outline-none">
                            <option value="">— Selecciona un médico —</option>
                            @foreach ($medicos ?? [] as $m)
                                <option value="{{ $m->id }}">Dr. {{ $m->nombre_completo }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="motivo" class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Motivo de Consulta</label>
                        <textarea name="motivo" id="motivo" rows="2"
                                  class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs text-slate-800 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all outline-none resize-none"
                                  placeholder="Describe la razón principal..."></textarea>
                    </div>

                    <div>
                        <label for="notas" class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Notas Adicionales</label>
                        <textarea name="notas" id="notas" rows="2"
                                  class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs text-slate-800 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all outline-none resize-none"
                                  placeholder="Indicaciones previas u observaciones..."></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="modalCrear = false"
                                class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition-all active:scale-95 cursor-pointer">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm hover:shadow-indigo-200 hover:shadow-lg transition-all cursor-pointer">
                            Guardar Cita
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal de Detalle --}}
        <div x-show="modalDetalle"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4"
             style="display: none;">
            
            <div x-show="modalDetalle"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-90 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-90 translate-y-4"
                 @click.away="modalDetalle = false"
                 class="bg-white rounded-3xl shadow-xl max-w-md w-full p-6 space-y-5 border border-slate-100">
                
                <div class="flex justify-between items-start">
                    <div class="flex items-center gap-3">
                        <div :class="citaDetalle.avatar_bg" class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-sm shrink-0" x-text="citaDetalle.iniciales">
                        </div>
                        <div>
                            <h3 class="font-extrabold text-slate-800 text-lg leading-tight" x-text="citaDetalle.paciente"></h3>
                            <p class="text-xs text-slate-400 font-medium" x-text="'ID: #' + citaDetalle.paciente_id"></p>
                        </div>
                    </div>
                    <button type="button" @click="modalDetalle = false" class="p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="space-y-3 bg-slate-50/70 p-4 rounded-2xl border border-slate-100 text-xs">
                    <div class="flex justify-between items-center py-1 border-b border-slate-200/50">
                        <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Hora programada</span>
                        <span class="font-extrabold text-slate-800 text-xs flex items-center gap-1" x-text="citaDetalle.hora + ' hrs'"></span>
                    </div>
                    <div class="flex justify-between items-center py-1 border-b border-slate-200/50">
                        <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Médico Asignado</span>
                        <span class="font-bold text-slate-700" x-text="'Dr. ' + citaDetalle.medico"></span>
                    </div>
                    <div class="flex justify-between items-center py-1 border-b border-slate-200/50">
                        <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Triage</span>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold text-indigo-700 bg-indigo-50 border border-indigo-100" x-text="citaDetalle.triage"></span>
                    </div>
                    <div class="flex justify-between items-center py-1">
                        <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Estado</span>
                        <span class="font-bold text-slate-700" x-text="citaDetalle.estado"></span>
                    </div>
                    <div class="pt-2 border-t border-slate-200/60">
                        <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px] block mb-1">Motivo de consulta</span>
                        <p class="text-slate-700 italic bg-white p-2.5 rounded-xl border border-slate-100" x-text="citaDetalle.motivo"></p>
                    </div>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" @click="modalDetalle = false"
                            class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition-all active:scale-95 cursor-pointer">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>