<div x-show="modalCrear" x-cloak x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
    class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 p-4 backdrop-blur-sm"
    style="display: none;">
    <div x-show="modalCrear" @click.away="modalCrear = false" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="scale-90 translate-y-4 opacity-0"
        x-transition:enter-end="scale-100 translate-y-0 opacity-100"
        class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-3xl border border-slate-100 bg-white shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 p-6 pb-3">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-slate-800">Agendar Cita Médica</h3>
                    <p class="text-xs text-slate-400">Completa los datos para agendar</p>
                </div>
            </div>
            <button type="button" @click="modalCrear = false"
                class="rounded-xl p-1.5 text-slate-400 transition-all hover:bg-slate-100 hover:text-slate-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form action="{{ route('agenda.store') }}" method="POST" class="space-y-4 p-6 pt-4">
            @csrf

            <div>
                <label for="paciente_id"
                    class="mb-1.5 block text-[11px] font-bold uppercase tracking-wider text-slate-400">
                    Paciente *
                </label>
                <select name="paciente_id" id="paciente_id" x-model="form.paciente_id" @change="cargarMedicos()" required
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs text-slate-800 outline-none focus:border-indigo-500 focus:bg-white">
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
                            data-medico-nombre="{{ $medicoAsignado }}"
                            @selected(old('paciente_id') == $p->id)>
                            {{ $p->nombre_completo }} — {{ $triageEmoji }} {{ $triageLabel }}
                            @if ($medicoAsignado)
                                (Dr. {{ $medicoAsignado }})
                            @endif
                        </option>
                    @endforeach
                </select>
                @error('paciente_id')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror

                <div x-show="avisoBloqueo" x-cloak
                    class="mt-2 rounded border border-yellow-300 bg-yellow-50 p-2 text-xs text-yellow-800">
                    ⚠️ Este paciente ya está asignado a un médico.
                    <span x-show="avisoNombreMedico">Su médico asignado es <span class="aviso-nombre font-bold"
                            x-text="avisoNombreMedico"></span>.</span>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                <div>
                    <label for="fecha"
                        class="mb-1.5 block text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        Fecha *
                    </label>
                    <input type="date" name="fecha" id="fecha" x-model="form.fecha" @change="cargarMedicos()" required
                        min="{{ now()->format('Y-m-d') }}"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-800 outline-none focus:border-indigo-500 focus:bg-white">
                    @error('fecha')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="hora"
                        class="mb-1.5 block text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        Hora *
                    </label>
                    <input type="time" name="hora" id="hora" x-model="form.hora" @change="cargarMedicos()" required
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-800 outline-none focus:border-indigo-500 focus:bg-white">
                    @error('hora')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="duracion_minutos"
                        class="mb-1.5 block text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        Duración *
                    </label>
                    <select name="duracion_minutos" id="duracion_minutos" x-model="form.duracion_minutos"
                        @change="cargarMedicos()" required
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-800 outline-none focus:border-indigo-500 focus:bg-white">
                        <option value="15">15 min</option>
                        <option value="30">30 min</option>
                        <option value="45">45 min</option>
                        <option value="60">60 min</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <div>
                    <label for="especialidad_id"
                        class="mb-1.5 block text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        Especialidad
                    </label>
                    <select name="especialidad_id" id="especialidad_id" x-model="form.especialidad_id"
                        @change="cargarMedicos()"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs text-slate-800 outline-none focus:border-indigo-500 focus:bg-white">
                        <option value="">— Todas las especialidades —</option>
                        @foreach ($especialidades ?? [] as $esp)
                            <option value="{{ $esp->id }}" @selected(old('especialidad_id') == $esp->id)>
                                {{ $esp->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('especialidad_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="servicio_id"
                        class="mb-1.5 block text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        Servicio
                    </label>
                    <select name="servicio_id" id="servicio_id" x-model="form.servicio_id"
                        @change="cargarMedicos()"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs text-slate-800 outline-none focus:border-indigo-500 focus:bg-white">
                        <option value="">— Todos los servicios —</option>
                        @foreach ($servicios ?? [] as $srv)
                            <option value="{{ $srv->id }}" @selected(old('servicio_id') == $srv->id)>
                                {{ $srv->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('servicio_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="medico_id"
                    class="mb-1.5 block text-[11px] font-bold uppercase tracking-wider text-slate-400">
                    Médico *
                </label>
                <select name="medico_id" id="medico_id" x-model="form.medico_id" required
                    :disabled="medicoSelectDisabled"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs text-slate-800 outline-none focus:border-indigo-500 focus:bg-white disabled:cursor-not-allowed disabled:opacity-60">
                    <option value="" x-text="mensajeMedicos"></option>
                    <template x-for="medico in medicos" :key="medico.id">
                        <option :value="medico.id" x-text="`${medico.nombre} (${medico.rol})`"
                            :disabled="medico.ocupado"></option>
                    </template>
                </select>
                <p x-show="cargandoMedicos" class="mt-1 text-xs text-slate-500">Cargando médicos...</p>
                <p x-show="!cargandoMedicos && !medicoSelectDisabled && medicos.length === 0"
                    class="mt-1 text-xs text-red-500">Sin médicos disponibles para esos criterios.</p>
                @error('medico_id')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="motivo"
                    class="mb-1.5 block text-[11px] font-bold uppercase tracking-wider text-slate-400">
                    Motivo de Consulta
                </label>
                <textarea name="motivo" id="motivo" rows="2" x-model="form.motivo"
                    class="w-full resize-none rounded-xl border border-slate-200 bg-slate-50 p-3 text-xs text-slate-800 outline-none focus:border-indigo-500 focus:bg-white"
                    placeholder="Razón principal..."></textarea>
                @error('motivo')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="notas"
                    class="mb-1.5 block text-[11px] font-bold uppercase tracking-wider text-slate-400">
                    Notas Adicionales
                </label>
                <textarea name="notas" id="notas" rows="2" x-model="form.notas"
                    class="w-full resize-none rounded-xl border border-slate-200 bg-slate-50 p-3 text-xs text-slate-800 outline-none focus:border-indigo-500 focus:bg-white"
                    placeholder="Observaciones..."></textarea>
                @error('notas')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end gap-2 border-t border-slate-100 pt-3">
                <button type="button" @click="modalCrear = false"
                    class="rounded-xl bg-slate-100 px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-200">
                    Cancelar
                </button>
                <button type="submit"
                    class="rounded-xl bg-indigo-600 px-5 py-2 text-xs font-bold text-white shadow-sm hover:bg-indigo-700">
                    Guardar Cita
                </button>
            </div>
        </form>
    </div>
</div>
