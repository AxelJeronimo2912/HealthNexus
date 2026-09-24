<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight">Agenda Médica</h2>
                <p class="text-xs text-slate-400 mt-0.5">Control semanal de citas y disponibilidad de especialistas</p>
            </div>

            <button type="button" @click="$dispatch('abrir-modal-crear')"
                class="bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white px-5 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 shadow-sm hover:shadow-indigo-200 hover:shadow-lg transition-all duration-200 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                Nueva Cita
            </button>
        </div>
    </x-slot>

    <div x-data="agendaData()" @abrir-modal-crear.window="abrirModal()"
        class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        @if (session('success'))
            <div
                class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-2xl text-xs font-semibold flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- Tarjetas de Métricas --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4">
                <div
                    class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-600 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">TOTAL SEMANA</p>
                    <p class="text-2xl font-extrabold text-slate-800 mt-0.5">{{ $stats['total_semana'] ?? 0 }}</p>
                </div>
            </div>
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4">
                <div
                    class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">PROGRAMADAS</p>
                    <p class="text-2xl font-extrabold text-indigo-600 mt-0.5">{{ $stats['programadas'] ?? 0 }}</p>
                </div>
            </div>
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4">
                <div
                    class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">ATENDIDAS</p>
                    <p class="text-2xl font-extrabold text-emerald-600 mt-0.5">{{ $stats['atendidas'] ?? 0 }}</p>
                </div>
            </div>
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-rose-50 flex items-center justify-center text-rose-500 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">CANCELADAS</p>
                    <p class="text-2xl font-extrabold text-rose-600 mt-0.5">{{ $stats['canceladas'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        {{-- Navegación de semana --}}
        <div
            class="flex flex-col sm:flex-row justify-between items-center bg-white border border-slate-100 p-4 rounded-3xl shadow-sm gap-4">
            <a href="{{ route('agenda.index', ['fecha' => $inicioSemana->copy()->subWeek()->format('Y-m-d')]) }}"
                class="w-full sm:w-auto px-5 py-2.5 bg-slate-50 hover:bg-indigo-50 text-slate-700 hover:text-indigo-600 rounded-2xl text-xs font-extrabold border border-slate-200 hover:border-indigo-200 transition-all flex items-center justify-center gap-2 group active:scale-95">
                <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
                <span>Semana anterior</span>
            </a>

            <div class="flex flex-col items-center gap-1.5 text-center">
                <p class="text-base font-black text-slate-800 tracking-tight">
                    {{ $inicioSemana->format('d M') }} — {{ $finSemana->format('d M Y') }}
                </p>
                <a href="{{ route('agenda.index') }}"
                    class="inline-flex items-center gap-1.5 px-3 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-full text-[11px] font-bold transition-all">
                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-600 animate-pulse"></span>
                    Ir a la semana actual
                </a>
            </div>

            <a href="{{ route('agenda.index', ['fecha' => $inicioSemana->copy()->addWeek()->format('Y-m-d')]) }}"
                class="w-full sm:w-auto px-5 py-2.5 bg-slate-50 hover:bg-indigo-50 text-slate-700 hover:text-indigo-600 rounded-2xl text-xs font-extrabold border border-slate-200 hover:border-indigo-200 transition-all flex items-center justify-center gap-2 group active:scale-95">
                <span>Semana siguiente</span>
                <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
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
                <div
                    class="bg-white rounded-3xl border {{ $esHoy ? 'border-indigo-500 ring-4 ring-indigo-500/10' : 'border-slate-100' }} shadow-sm overflow-hidden flex flex-col">
                    <div
                        class="p-3 text-center border-b {{ $esHoy ? 'bg-indigo-50/60 border-indigo-100' : 'bg-slate-50/50 border-slate-100' }}">
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
                                $iniciales = mb_substr($cita->paciente->nombre_completo ?? 'P', 0, 2);
                            @endphp
                            <button type="button" @click="abrirDetalle(@js([
    'hora' => $cita->fecha_hora->format('H:i'),
    'paciente' => $cita->paciente->nombre_completo ?? '—',
    'paciente_id' => $cita->paciente->id ?? 'N/A',
    'medico' => $cita->medico->nombre_completo ?? '—',
    'especialidad' => $cita->especialidad?->nombre ?? '—',
    'estado' => $cita->estado_label,
    'motivo' => $cita->motivo ?? 'Sin motivo especificado',
    'triage' => strtoupper($cita->triage_al_momento ?? 'Sin triage'),
    'iniciales' => strtoupper($iniciales),
]))"
                                class="w-full text-left p-3 bg-white border border-slate-100 rounded-2xl shadow-sm hover:shadow-md hover:scale-[1.02] active:scale-98 transition-all {{ $triageBorder }}">
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="font-bold text-slate-800 text-xs flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $cita->fecha_hora->format('H:i') }}
                                    </span>
                                    <span
                                        class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wide {{ $cita->estado_color }}">
                                        {{ $cita->estado_label }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div
                                        class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-[10px] font-bold shrink-0">
                                        {{ strtoupper($iniciales) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-slate-800 font-bold truncate text-xs">
                                            {{ $cita->paciente->nombre_completo ?? '—' }}
                                        </p>
                                        <p class="text-slate-400 truncate text-[10px] font-medium">
                                            Dr. {{ $cita->medico->nombre_completo ?? '—' }}
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

        {{-- MODAL DE CREAR --}}
        <div x-show="modalCrear" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4"
            style="display: none;">

            <div x-show="modalCrear" @click.away="modalCrear = false"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-90 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                class="bg-white rounded-3xl shadow-2xl max-w-lg w-full p-6 space-y-5 border border-slate-100 max-h-[90vh] overflow-y-auto">

                <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-slate-800 text-base">Agendar Cita Médica</h3>
                            <p class="text-xs text-slate-400">Paciente, especialidad, fecha y médico</p>
                        </div>
                    </div>
                    <button type="button" @click="modalCrear = false"
                        class="p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('agenda.store') }}" method="POST" class="space-y-4">
                    @csrf

                    {{-- PACIENTE --}}
                    <div>
                        <label for="paciente_id"
                            class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                            Paciente *
                        </label>
                        <select name="paciente_id" id="paciente_id" x-model="form.paciente_id"
                            @change="cargarMedicos()" required
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-indigo-500 outline-none">
                            <option value="">— Selecciona un paciente —</option>
                            @foreach ($pacientes ?? [] as $p)
                                @php
                                    $signo = $p->signosVitales->first();
                                    $triageEmoji = match ($signo?->triage) {
                                        'rojo' => '🔴',
                                        'naranja' => '🟠',
                                        'amarillo' => '🟡',
                                        'verde' => '🟢',
                                        'azul' => '🔵',
                                        default => '⚪',
                                    };
                                    $triageLabel = match ($signo?->triage) {
                                        'rojo' => 'Rojo',
                                        'naranja' => 'Naranja',
                                        'amarillo' => 'Amarillo',
                                        'verde' => 'Verde',
                                        'azul' => 'Azul',
                                        default => 'Sin triage',
                                    };
                                    $medicoAsignado = $p->medicoAsignado?->medico?->nombre_completo;
                                @endphp
                                <option value="{{ $p->id }}"
                                    data-medico-asignado="{{ $p->medicoAsignado?->medico_id }}"
                                    data-medico-nombre="{{ $medicoAsignado }}" @selected(old('paciente_id') == $p->id)>
                                    {{ $p->nombre_completo }} — {{ $triageEmoji }} {{ $triageLabel }}
                                    @if ($medicoAsignado)
                                        (Dr. {{ $medicoAsignado }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('paciente_id')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror

                        {{-- Aviso de bloqueo --}}
                        <div x-show="avisoBloqueo" x-cloak
                            class="mt-2 p-2 bg-yellow-50 border border-yellow-300 text-yellow-800 rounded text-xs">
                            ⚠️ Este paciente ya está asignado a un médico. Solo ese médico puede atenderlo.
                        </div>
                    </div>

                    {{-- ESPECIALIDAD --}}
                    <div>
                        <label for="especialidad_id"
                            class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                            Especialidad
                        </label>
                        <select name="especialidad_id" id="especialidad_id" x-model="form.especialidad_id"
                            @change="cargarMedicos()"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-indigo-500 outline-none">
                            <option value="">— Todas las especialidades —</option>
                            @foreach ($especialidades ?? [] as $esp)
                                <option value="{{ $esp->id }}" @selected(old('especialidad_id') == $esp->id)>
                                    {{ $esp->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- FECHA / HORA / DURACIÓN --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label for="fecha"
                                class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                                Fecha *
                            </label>
                            <input type="date" name="fecha" id="fecha" x-model="form.fecha"
                                @change="cargarMedicos()" required min="{{ now()->format('Y-m-d') }}"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-800 focus:bg-white focus:border-indigo-500 outline-none">
                        </div>
                        <div>
                            <label for="hora"
                                class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                                Hora *
                            </label>
                            <input type="time" name="hora" id="hora" x-model="form.hora"
                                @change="cargarMedicos()" required
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-800 focus:bg-white focus:border-indigo-500 outline-none">
                        </div>
                        <div>
                            <label for="duracion_minutos"
                                class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                                Duración *
                            </label>
                            <select name="duracion_minutos" id="duracion_minutos" x-model="form.duracion_minutos"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-800 focus:bg-white focus:border-indigo-500 outline-none">
                                <option value="15">15 min</option>
                                <option value="30" selected>30 min</option>
                                <option value="45">45 min</option>
                                <option value="60">60 min</option>
                            </select>
                        </div>
                    </div>

                    {{-- MÉDICO --}}
                    <div>
                        <label for="medico_id"
                            class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                            Médico *
                        </label>
                        <select name="medico_id" id="medico_id" x-model="form.medico_id" required
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-indigo-500 outline-none">
                            <option value="">— Selecciona fecha, hora y especialidad —</option>
                            <template x-for="m in medicos" :key="m.id">
                                <option :value="m.id"
                                    x-text="`${m.nombre} (${m.rol})${m.ocupado ? ' — OCUPADO' : ''}`"
                                    :disabled="m.ocupado"></option>
                            </template>
                        </select>
                        <p x-show="cargandoMedicos" class="text-xs text-slate-500 mt-1">Cargando médicos...</p>
                        <p x-show="!cargandoMedicos && medicos.length === 0 && form.fecha && form.hora"
                            class="text-xs text-red-500 mt-1">
                            Sin médicos disponibles para ese horario y especialidad.
                        </p>
                        @error('medico_id')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- MOTIVO --}}
                    <div>
                        <label for="motivo"
                            class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                            Motivo de Consulta
                        </label>
                        <textarea name="motivo" id="motivo" rows="2"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs text-slate-800 focus:bg-white focus:border-indigo-500 outline-none resize-none"
                            placeholder="Razón principal..."></textarea>
                    </div>

                    {{-- NOTAS --}}
                    <div>
                        <label for="notas"
                            class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                            Notas Adicionales
                        </label>
                        <textarea name="notas" id="notas" rows="2"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs text-slate-800 focus:bg-white focus:border-indigo-500 outline-none resize-none"
                            placeholder="Observaciones..."></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="modalCrear = false"
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-sm">
                            Guardar Cita
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL DE DETALLE --}}
        <div x-show="modalDetalle" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4"
            style="display: none;">

            <div x-show="modalDetalle" @click.away="modalDetalle = false"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-90 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                class="bg-white rounded-3xl shadow-xl max-w-md w-full p-6 space-y-5 border border-slate-100">

                <div class="flex justify-between items-start">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-sm shrink-0"
                            x-text="citaDetalle.iniciales"></div>
                        <div>
                            <h3 class="font-extrabold text-slate-800 text-lg leading-tight"
                                x-text="citaDetalle.paciente"></h3>
                            <p class="text-xs text-slate-400 font-medium" x-text="'ID: #' + citaDetalle.paciente_id">
                            </p>
                        </div>
                    </div>
                    <button type="button" @click="modalDetalle = false"
                        class="p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="space-y-3 bg-slate-50/70 p-4 rounded-2xl border border-slate-100 text-xs">
                    <div class="flex justify-between items-center py-1 border-b border-slate-200/50">
                        <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Hora</span>
                        <span class="font-extrabold text-slate-800" x-text="citaDetalle.hora + ' hrs'"></span>
                    </div>
                    <div class="flex justify-between items-center py-1 border-b border-slate-200/50">
                        <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Médico</span>
                        <span class="font-bold text-slate-700" x-text="'Dr. ' + citaDetalle.medico"></span>
                    </div>
                    <div class="flex justify-between items-center py-1 border-b border-slate-200/50">
                        <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Especialidad</span>
                        <span class="font-bold text-slate-700" x-text="citaDetalle.especialidad"></span>
                    </div>
                    <div class="flex justify-between items-center py-1 border-b border-slate-200/50">
                        <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Triage</span>
                        <span
                            class="px-2.5 py-1 rounded-full text-[10px] font-bold text-indigo-700 bg-indigo-50 border border-indigo-100"
                            x-text="citaDetalle.triage"></span>
                    </div>
                    <div class="flex justify-between items-center py-1">
                        <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Estado</span>
                        <span class="font-bold text-slate-700" x-text="citaDetalle.estado"></span>
                    </div>
                    <div class="pt-2 border-t border-slate-200/60">
                        <span
                            class="text-slate-400 font-bold uppercase tracking-wider text-[10px] block mb-1">Motivo</span>
                        <p class="text-slate-700 italic bg-white p-2.5 rounded-xl border border-slate-100"
                            x-text="citaDetalle.motivo"></p>
                    </div>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" @click="modalDetalle = false"
                        class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPT ALPINE --}}
    <script>
        function agendaData() {
            return {
                modalCrear: false,
                modalDetalle: false,
                citaDetalle: {},
                avisoBloqueo: false,
                cargandoMedicos: false,
                medicos: [],
                form: {
                    paciente_id: '',
                    especialidad_id: '',
                    medico_id: '',
                    fecha: '{{ now()->format('Y-m-d') }}',
                    hora: '09:00',
                    duracion_minutos: '30',
                },

                abrirModal() {
                    this.modalCrear = true;
                    this.$nextTick(() => this.cargarMedicos());
                },

                abrirDetalle(data) {
                    this.citaDetalle = data;
                    this.modalDetalle = true;
                },

                async cargarMedicos() {
                    const {
                        paciente_id,
                        especialidad_id,
                        fecha,
                        hora
                    } = this.form;

                    if (!fecha || !hora) {
                        this.medicos = [];
                        return;
                    }

                    this.cargandoMedicos = true;
                    this.medicos = [];
                    this.form.medico_id = '';

                    const params = new URLSearchParams({
                        fecha,
                        hora
                    });
                    if (paciente_id) params.append('paciente_id', paciente_id);
                    if (especialidad_id) params.append('especialidad_id', especialidad_id);

                    try {
                        const res = await fetch(`{{ route('agenda.medicos-disponibles') }}?${params}`);
                        const data = await res.json();

                        if (Array.isArray(data)) {
                            this.medicos = data;

                            // Aviso de bloqueo
                            const pacienteOpt = document.querySelector(`#paciente_id option[value="${paciente_id}"]`);
                            const medicoAsignadoId = pacienteOpt?.dataset?.medicoAsignado;

                            this.avisoBloqueo = !!medicoAsignadoId;

                            // Si el paciente tiene médico asignado y está en la lista, autoseleccionar
                            if (medicoAsignadoId) {
                                const medico = this.medicos.find(m => m.id == medicoAsignadoId && !m.ocupado);
                                if (medico) this.form.medico_id = medico.id;
                            }
                        }
                    } catch (e) {
                        console.error('Error al cargar médicos:', e);
                        this.medicos = [];
                    } finally {
                        this.cargandoMedicos = false;
                    }
                },
            };
        }
    </script>
</x-app-layout>
