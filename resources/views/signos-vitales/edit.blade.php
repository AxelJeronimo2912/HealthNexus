<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Editar Registro — {{ $registro->paciente->nombre_completo }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto sm:px-6 lg:px-8">
        <form action="{{ route('signos-vitales.update', $registro) }}" method="POST"
            class="space-y-6 bg-white p-6 rounded-lg shadow">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium">Paciente *</label>
                <select name="paciente_id" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                    @foreach ($pacientes as $p)
                        <option value="{{ $p->id }}" @selected(old('paciente_id', $registro->paciente_id) == $p->id)>
                            {{ $p->nombre_completo }}
                        </option>
                    @endforeach
                </select>
            </div>

            <hr>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium">Temperatura (°C)</label>
                    <input type="number" step="0.1" name="temperatura" id="temperatura"
                        value="{{ old('temperatura', $registro->temperatura) }}"
                        class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium">Frec. cardíaca (lpm)</label>
                    <input type="number" name="frecuencia_cardiaca" id="frecuencia_cardiaca"
                        value="{{ old('frecuencia_cardiaca', $registro->frecuencia_cardiaca) }}"
                        class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium">Frec. respiratoria (rpm)</label>
                    <input type="number" name="frecuencia_respiratoria" id="frecuencia_respiratoria"
                        value="{{ old('frecuencia_respiratoria', $registro->frecuencia_respiratoria) }}"
                        class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium">Presión arterial</label>
                    <input type="text" name="presion_arterial" id="presion_arterial"
                        value="{{ old('presion_arterial', $registro->presion_arterial) }}"
                        class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium">Saturación O₂ (%)</label>
                    <input type="number" name="saturacion_oxigeno" id="saturacion_oxigeno"
                        value="{{ old('saturacion_oxigeno', $registro->saturacion_oxigeno) }}"
                        class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium">Glucosa (mg/dL)</label>
                    <input type="number" name="glucosa" id="glucosa" value="{{ old('glucosa', $registro->glucosa) }}"
                        class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium">Peso (kg)</label>
                    <input type="number" step="0.1" name="peso" id="peso"
                        value="{{ old('peso', $registro->peso) }}"
                        class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium">Talla (m)</label>
                    <input type="number" step="0.01" name="talla" id="talla"
                        value="{{ old('talla', $registro->talla) }}"
                        class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium">Escala de dolor (0-10)</label>
                    <input type="number" min="0" max="10" name="escala_dolor" id="escala_dolor"
                        value="{{ old('escala_dolor', $registro->escala_dolor) }}"
                        class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                </div>
            </div>

            <hr>

            <div class="mb-4 p-4 rounded border-2 {{ $registro->triage_color }}" id="triage-preview">
                <p class="text-sm text-gray-500">Triage actual / estimado:</p>
                <p class="text-lg font-bold" id="triage-valor">{{ $registro->triage_label }}</p>
                <p class="text-sm text-gray-600" id="triage-descripcion">{{ $registro->triage_descripcion }}</p>
            </div>

            <label class="inline-flex items-center">
                <input type="checkbox" name="triage_manual" value="1" id="triage_manual"
                    @checked(old('triage_manual', $registro->triage_manual)) class="rounded border-gray-300 text-blue-600 shadow-sm">
                <span class="ml-2 text-sm">Forzar clasificación manual</span>
            </label>

            <div id="triage-select-container" class="mt-3 {{ $registro->triage_manual ? '' : 'hidden' }}">
                <label class="block text-sm font-medium">Nivel de triage manual</label>
                <select name="triage" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                    <option value="">— Selecciona —</option>
                    @foreach (['rojo', 'naranja', 'amarillo', 'verde', 'azul'] as $t)
                        <option value="{{ $t }}" @selected(old('triage', $registro->triage) == $t)>
                            {{ ucfirst($t) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <hr>

            <div>
                <label class="block text-sm font-medium">Motivo de consulta</label>
                <textarea name="motivo_consulta" rows="2" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('motivo_consulta', $registro->motivo_consulta) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium">Notas</label>
                <textarea name="notas" rows="2" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('notas', $registro->notas) }}</textarea>
            </div>

            <div class="flex justify-between items-center pt-4">
                <a href="{{ route('signos-vitales.show', $registro) }}"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">Cancelar</a>
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium">
                    Actualizar Registro
                </button>
            </div>
        </form>
    </div>

    @include('signos-vitales._scripts')
</x-app-layout>
