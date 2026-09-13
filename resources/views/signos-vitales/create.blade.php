<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Nuevo Registro de Signos Vitales</h2>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto sm:px-6 lg:px-8">

        <form action="{{ route('signos-vitales.store') }}" method="POST" class="space-y-6 bg-white p-6 rounded-lg shadow"
            id="form-signos">
            @csrf
            @if (session('warning'))
                <div class="mb-4 p-4 bg-yellow-50 border-l-4 border-yellow-500 rounded">
                    <p class="font-semibold text-yellow-800">⚠️ Atención</p>
                    <p class="text-sm text-yellow-700">{{ session('warning') }}</p>
                </div>
            @endif

            @if (request('cita_id'))
                <input type="hidden" name="cita_id" value="{{ request('cita_id') }}">
            @endif
            {{-- Paciente --}}
            <div>
                <label class="block text-sm font-medium">Paciente *</label>
                <select name="paciente_id" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                    <option value="">— Selecciona un paciente —</option>
                    @foreach ($pacientes as $p)
                        <option value="{{ $p->id }}" @selected(old('paciente_id', $pacienteSeleccionado?->id ?? '') == $p->id)>
                            {{ $p->nombre_completo }}
                        </option>
                    @endforeach
                </select>
                @error('paciente_id')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <hr>

            {{-- Signos vitales --}}
            <div>
                <h3 class="text-lg font-bold text-gray-800 mb-1">Signos vitales</h3>
                <p class="text-sm text-gray-500 mb-4">
                    Ingresa los valores. El triage se calculará automáticamente.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium">Temperatura (°C)</label>
                        <input type="number" step="0.1" name="temperatura" id="temperatura"
                            value="{{ old('temperatura') }}" placeholder="36.5"
                            class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Frec. cardíaca (lpm)</label>
                        <input type="number" name="frecuencia_cardiaca" id="frecuencia_cardiaca"
                            value="{{ old('frecuencia_cardiaca') }}" placeholder="80"
                            class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Frec. respiratoria (rpm)</label>
                        <input type="number" name="frecuencia_respiratoria" id="frecuencia_respiratoria"
                            value="{{ old('frecuencia_respiratoria') }}" placeholder="18"
                            class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Presión arterial</label>
                        <input type="text" name="presion_arterial" id="presion_arterial"
                            value="{{ old('presion_arterial') }}" placeholder="120/80"
                            class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Saturación O₂ (%)</label>
                        <input type="number" name="saturacion_oxigeno" id="saturacion_oxigeno"
                            value="{{ old('saturacion_oxigeno') }}" placeholder="98"
                            class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Glucosa (mg/dL)</label>
                        <input type="number" name="glucosa" id="glucosa" value="{{ old('glucosa') }}"
                            placeholder="100" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Peso (kg)</label>
                        <input type="number" step="0.1" name="peso" id="peso" value="{{ old('peso') }}"
                            placeholder="70" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Talla (m)</label>
                        <input type="number" step="0.01" name="talla" id="talla" value="{{ old('talla') }}"
                            placeholder="1.70" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Escala de dolor (0-10)</label>
                        <input type="number" min="0" max="10" name="escala_dolor" id="escala_dolor"
                            value="{{ old('escala_dolor') }}" placeholder="0"
                            class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                </div>
            </div>

            <hr>

            {{-- Triage --}}
            <div>
                <h3 class="text-lg font-bold text-gray-800 mb-1">Triage</h3>
                <p class="text-sm text-gray-500 mb-4">
                    Calculado automáticamente. Puedes forzarlo manualmente si lo necesitas.
                </p>

                <div class="mb-4 p-4 rounded border-2" id="triage-preview">
                    <p class="text-sm text-gray-500">Triage estimado:</p>
                    <p class="text-lg font-bold" id="triage-valor">— Sin calcular —</p>
                    <p class="text-sm text-gray-600" id="triage-descripcion"></p>
                </div>

                <label class="inline-flex items-center">
                    <input type="checkbox" name="triage_manual" value="1" id="triage_manual"
                        @checked(old('triage_manual')) class="rounded border-gray-300 text-blue-600 shadow-sm">
                    <span class="ml-2 text-sm">Forzar clasificación manual</span>
                </label>

                <div id="triage-select-container" class="mt-3 hidden">
                    <label class="block text-sm font-medium">Nivel de triage manual</label>
                    <select name="triage" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">— Selecciona —</option>
                        <option value="rojo" @selected(old('triage') == 'rojo')>🔴 Rojo — Emergencia</option>
                        <option value="naranja" @selected(old('triage') == 'naranja')>🟠 Naranja — Muy urgente</option>
                        <option value="amarillo" @selected(old('triage') == 'amarillo')>🟡 Amarillo — Urgente</option>
                        <option value="verde" @selected(old('triage') == 'verde')>🟢 Verde — No urgente</option>
                        <option value="azul" @selected(old('triage') == 'azul')>🔵 Azul — Baja prioridad</option>
                    </select>
                </div>
            </div>

            <hr>

            {{-- Motivo y notas --}}
            <div>
                <label class="block text-sm font-medium">Motivo de consulta</label>
                <textarea name="motivo_consulta" rows="2" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('motivo_consulta') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium">Notas</label>
                <textarea name="notas" rows="2" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('notas') }}</textarea>
            </div>

            <div class="flex justify-between items-center pt-4">
                <a href="{{ route('signos-vitales.index') }}"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">Cancelar</a>
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium">
                    Guardar Registro
                </button>
            </div>
        </form>
    </div>

    @include('signos-vitales._scripts')
</x-app-layout>
