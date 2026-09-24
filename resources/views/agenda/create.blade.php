<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Nueva Cita</h2>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto sm:px-6 lg:px-8">

        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded">
                <p class="font-semibold text-red-800 mb-2">Errores:</p>
                <ul class="list-disc list-inside text-sm text-red-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('agenda.store') }}" method="POST" class="space-y-6 bg-white p-6 rounded-lg shadow"
            id="form-cita">
            @csrf

            {{-- Paciente --}}
            <div>
                <label class="block text-sm font-medium">Paciente *</label>
                <select name="paciente_id" id="paciente_id" required
                    class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
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
                    class="hidden mt-2 p-2 bg-yellow-50 border border-yellow-300 text-yellow-800 rounded text-xs">
                    Este paciente ya está asignado a un médico. Solo ese médico puede atenderlo.
                </p>
            </div>

            {{-- Fecha y hora --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium">Fecha *</label>
                    <input type="date" name="fecha" id="fecha" required
                        value="{{ old('fecha', $fechaSeleccionada) }}"
                        class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium">Hora *</label>
                    <input type="time" name="hora" id="hora" required
                        value="{{ old('hora', $horaSeleccionada) }}"
                        class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium">Duración (min) *</label>
                    <select name="duracion_minutos" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                        <option value="15" @selected(old('duracion_minutos') == 15)>15</option>
                        <option value="30" @selected(old('duracion_minutos', 30) == 30)>30</option>
                        <option value="45" @selected(old('duracion_minutos') == 45)>45</option>
                        <option value="60" @selected(old('duracion_minutos') == 60)>60</option>
                    </select>
                </div>
            </div>

            {{-- Médico --}}
            <div>
                <label class="block text-sm font-medium">Médico *</label>

                <div>
                    <label class="block text-sm font-medium">Especialidad</label>
                    <select name="especialidad_id" id="especialidad_id"
                        class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">— Todas las especialidades —</option>
                        @foreach ($especialidades as $esp)
                            <option value="{{ $esp->id }}" @selected(old('especialidad_id') == $esp->id)>
                                {{ $esp->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <select name="medico_id" id="medico_id" required
                    class="mt-1 w-full border-gray-300 rounded-md shadow-sm"
                    {{ old('paciente_id') ? '' : 'disabled' }}>
                    <option value="">— Selecciona primero paciente, fecha y hora —</option>
                </select>
                @error('medico_id')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
                <p id="loading-medicos" class="hidden text-xs text-gray-500 mt-1">Cargando médicos disponibles...</p>
            </div>

            {{-- Motivo y notas --}}
            <div>
                <label class="block text-sm font-medium">Motivo de consulta</label>
                <textarea name="motivo" rows="2" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('motivo') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium">Notas</label>
                <textarea name="notas" rows="2" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('notas') }}</textarea>
            </div>

            <div class="flex justify-between items-center pt-4">
                <a href="{{ route('agenda.index') }}"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">Cancelar</a>
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium">
                    Agendar Cita
                </button>
            </div>
        </form>
    </div>

    @include('agenda._scripts')
</x-app-layout>
