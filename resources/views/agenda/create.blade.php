<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div
                class="w-9 h-9 rounded-full bg-gradient-to-br from-teal-500 to-cyan-600 flex items-center justify-center shadow-sm">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nueva Cita</h2>
                <p class="text-xs text-gray-400">Completa los datos para agendar</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto sm:px-6 lg:px-8">

        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg flex gap-3">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                </svg>
                <div>
                    <p class="font-semibold text-red-800 text-sm mb-1">Revisa los siguientes campos</p>
                    <ul class="list-disc list-inside text-sm text-red-700 space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form action="{{ route('agenda.store') }}" method="POST"
            class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden" id="form-cita">
            @csrf

            {{-- ==================== Paciente ==================== --}}
            <div class="p-6 border-b border-gray-100">
                <p class="text-[11px] font-semibold text-teal-600 tracking-wide mb-3">1. Paciente</p>

                <label for="paciente_id" class="block text-sm font-medium text-gray-700 mb-1">Paciente <span
                        class="text-red-500">*</span></label>
                <select name="paciente_id" id="paciente_id" required
                    class="w-full border-gray-200 rounded-md shadow-sm text-sm focus:border-teal-500 focus:ring-teal-500">
                    <option value="">— Selecciona un paciente —</option>
                    @foreach ($pacientes as $p)
                        @php
                            $signo = $p->signosVitales()->latest()->first();
                            $triageLabel = match ($signo?->triage) {
                                'rojo' => '🔴 Rojo',
                                'naranja' => '🟠 Naranja',
                                'amarillo' => '🟡 Amarillo',
                                'verde' => '🟢 Verde',
                                'azul' => '🔵 Azul',
                                default => '—',
                            };
                        @endphp
                        <option value="{{ $p->id }}" data-medico-asignado="{{ $p->medicoAsignado?->medico_id }}"
                            data-medico-nombre="{{ $p->medicoAsignado?->medico?->nombre_completo }}"
                            @selected(old('paciente_id') == $p->id)>
                            {{ $p->nombre_completo }} — {{ $triageLabel }}
                            @if ($p->medicoAsignado)
                                (Dr. {{ $p->medicoAsignado->medico->nombre_completo }})
                            @endif
                        </option>
                    @endforeach
                </select>
                @error('paciente_id')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror

                <p id="aviso-bloqueo"
                    class="hidden mt-3 p-3 bg-amber-50 border border-amber-200 text-amber-800 rounded-md text-xs flex items-start gap-2">
                    <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                    <span>Este paciente ya está asignado a un médico. Solo ese médico puede atenderlo.</span>
                </p>
            </div>

            {{-- ==================== Fecha y hora ==================== --}}
            <div class="p-6 border-b border-gray-100">
                <p class="text-[11px] font-semibold text-teal-600 tracking-wide mb-3">2. Fecha y duración</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="fecha" class="block text-sm font-medium text-gray-700 mb-1">Fecha <span
                                class="text-red-500">*</span></label>
                        <input type="date" name="fecha" id="fecha" required
                            value="{{ old('fecha', $fechaSeleccionada) }}"
                            class="w-full border-gray-200 rounded-md shadow-sm text-sm focus:border-teal-500 focus:ring-teal-500">
                    </div>
                    <div>
                        <label for="hora" class="block text-sm font-medium text-gray-700 mb-1">Hora <span
                                class="text-red-500">*</span></label>
                        <input type="time" name="hora" id="hora" required
                            value="{{ old('hora', $horaSeleccionada) }}"
                            class="w-full border-gray-200 rounded-md shadow-sm text-sm focus:border-teal-500 focus:ring-teal-500">
                    </div>
                    <div>
                        <label for="duracion_minutos" class="block text-sm font-medium text-gray-700 mb-1">Duración
                            (min) <span class="text-red-500">*</span></label>
                        <select name="duracion_minutos" id="duracion_minutos" required
                            class="w-full border-gray-200 rounded-md shadow-sm text-sm focus:border-teal-500 focus:ring-teal-500">
                            <option value="15" @selected(old('duracion_minutos') == 15)>15</option>
                            <option value="30" @selected(old('duracion_minutos', 30) == 30)>30</option>
                            <option value="45" @selected(old('duracion_minutos') == 45)>45</option>
                            <option value="60" @selected(old('duracion_minutos') == 60)>60</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- ==================== Médico ==================== --}}
            <div class="p-6 border-b border-gray-100">
                <p class="text-[11px] font-semibold text-teal-600 tracking-wide mb-3">3. Médico</p>

                {{-- Filtros: Especialidad + Servicio --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                    <div>
                        <label for="especialidad_id" class="block text-xs text-gray-500 mb-1">Especialidad</label>
                        <select name="especialidad_id" id="especialidad_id"
                            class="w-full border-gray-200 rounded-md shadow-sm text-sm focus:border-teal-500 focus:ring-teal-500">
                            <option value="">— Todas las especialidades —</option>
                            @foreach ($especialidades as $esp)
                                <option value="{{ $esp->id }}" @selected(old('especialidad_id') == $esp->id)>
                                    {{ $esp->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="servicio_id" class="block text-xs text-gray-500 mb-1">Servicio</label>
                        <select name="servicio_id" id="servicio_id"
                            class="w-full border-gray-200 rounded-md shadow-sm text-sm focus:border-teal-500 focus:ring-teal-500">
                            <option value="">— Todos los servicios —</option>
                            @foreach ($servicios as $srv)
                                <option value="{{ $srv->id }}" @selected(old('servicio_id') == $srv->id)>
                                    {{ $srv->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Select de médicos --}}
                <div>
                    <label for="medico_id" class="block text-xs text-gray-500 mb-1">Médico disponible</label>
                    <select name="medico_id" id="medico_id" required
                        class="w-full border-gray-200 rounded-md shadow-sm text-sm focus:border-teal-500 focus:ring-teal-500 disabled:bg-gray-50 disabled:text-gray-400"
                        {{ old('paciente_id') ? '' : 'disabled' }}>
                        <option value="">— Selecciona primero paciente, fecha y hora —</option>
                    </select>
                    @error('medico_id')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p id="loading-medicos" class="hidden text-xs text-gray-500 mt-2 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 animate-spin text-teal-600" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Cargando médicos disponibles...
                    </p>
                </div>
            </div>

            {{-- ==================== Motivo y notas ==================== --}}
            <div class="p-6 border-b border-gray-100 space-y-4">
                <p class="text-[11px] font-semibold text-teal-600 tracking-wide">4. Detalles adicionales</p>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Motivo de consulta</label>
                    <textarea name="motivo" rows="2"
                        class="w-full border-gray-200 rounded-md shadow-sm text-sm focus:border-teal-500 focus:ring-teal-500">{{ old('motivo') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notas</label>
                    <textarea name="notas" rows="2"
                        class="w-full border-gray-200 rounded-md shadow-sm text-sm focus:border-teal-500 focus:ring-teal-500">{{ old('notas') }}</textarea>
                </div>
            </div>

            <div class="flex justify-between items-center px-6 py-4 bg-gray-50">
                <a href="{{ route('agenda.index') }}"
                    class="px-4 py-2 bg-white hover:bg-gray-100 border border-gray-200 rounded-md text-sm text-gray-600 transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                    class="px-5 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-md text-sm font-medium transition-colors shadow-sm">
                    Agendar Cita
                </button>
            </div>
        </form>
    </div>

    @include('agenda._scripts')
</x-app-layout>
