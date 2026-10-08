{{-- MODAL DE CREAR --}}
<div x-show="modalCrear" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4"
    style="display: none;">

    <div x-show="modalCrear" @click.away="modalCrear = false" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-90 translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        class="bg-white rounded-3xl shadow-2xl max-w-2xl w-full border border-slate-100 max-h-[90vh] overflow-y-auto">

        {{-- Header --}}
        <div class="flex justify-between items-center border-b border-slate-100 p-6 pb-3">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-800 text-base">Agendar Cita Médica</h3>
                    <p class="text-xs text-slate-400">Completa los datos para agendar</p>
                </div>
            </div>
            <button type="button" @click="modalCrear = false"
                class="p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form action="{{ route('agenda.store') }}" method="POST" class="p-6 pt-4 space-y-4">
            @csrf

            {{-- PACIENTE --}}
            <div>
                <label for="paciente_id"
                    class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                    Paciente *
                </label>
                <select name="paciente_id" id="paciente_id" x-model="form.paciente_id" @change="cargarMedicos()"
                    required
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
                        <option value="{{ $p->id }}" data-medico-asignado="{{ $p->medicoAsignado?->medico_id }}"
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

                <div x-show="avisoBloqueo" x-cloak
                    class="mt-2 p-2 bg-yellow-50 border border-yellow-300 text-yellow-800 rounded text-xs">
                    ⚠️ Este paciente ya está asignado a un médico. Solo ese médico puede atenderlo.
                </div>
            </div>

            {{-- FECHA / HORA / DURACIÓN --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label for="fecha"
                        class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                        Fecha *
                    </label>
                    <input type="date" name="fecha" id="fecha" x-model="form.fecha" @change="cargarMedicos()"
                        required min="{{ now()->format('Y-m-d') }}"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-800 focus:bg-white focus:border-indigo-500 outline-none">
                </div>
                <div>
                    <label for="hora"
                        class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                        Hora *
                    </label>
                    <input type="time" name="hora" id="hora" x-model="form.hora" @change="cargarMedicos()"
                        required
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-800 focus:bg-white focus:border-indigo-500 outline-none">
                </div>
                <div>
                    <label for="duracion_minutos"
                        class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                        Duración *
                    </label>
                    <select name="duracion_minutos" id="duracion_minutos" x-model="form.duracion_minutos"
                        @change="cargarMedicos()"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-800 focus:bg-white focus:border-indigo-500 outline-none">
                        <option value="15">15 min</option>
                        <option value="30">30 min</option>
                        <option value="45">45 min</option>
                        <option value="60">60 min</option>
                    </select>
                </div>
            </div>

            {{-- ESPECIALIDAD / SERVICIO --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
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

                <div>
                    <label for="servicio_id"
                        class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                        Servicio
                    </label>
                    <select name="servicio_id" id="servicio_id" x-model="form.servicio_id" @change="cargarMedicos()"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-indigo-500 outline-none">
                        <option value="">— Todos los servicios —</option>
                        @foreach ($servicios ?? [] as $srv)
                            <option value="{{ $srv->id }}" @selected(old('servicio_id') == $srv->id)>
                                {{ $srv->nombre }}
                            </option>
                        @endforeach
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
                            x-text="`${m.nombre} (${m.rol})${m.ocupado ? ' — OCUPADO' : ''}`" :disabled="m.ocupado">
                        </option>
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
                <textarea name="motivo" id="motivo" rows="2" x-model="form.motivo"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs text-slate-800 focus:bg-white focus:border-indigo-500 outline-none resize-none"
                    placeholder="Razón principal..."></textarea>
            </div>

            {{-- NOTAS --}}
            <div>
                <label for="notas"
                    class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                    Notas Adicionales
                </label>
                <textarea name="notas" id="notas" rows="2" x-model="form.notas"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs text-slate-800 focus:bg-white focus:border-indigo-500 outline-none resize-none"
                    placeholder="Observaciones..."></textarea>
            </div>

            {{-- FOOTER --}}
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
