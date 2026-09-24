<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Nueva Nota de Enfermería</h2>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <form action="{{ route('enfermeria.notas.store') }}" method="POST"
            class="space-y-6 bg-white p-6 rounded-lg shadow">
            @csrf

            <div>
                <label class="block text-sm font-medium">Paciente *</label>
                <select name="paciente_id" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                    <option value="">— Selecciona —</option>
                    @foreach ($pacientes as $p)
                        <option value="{{ $p->id }}" @selected(old('paciente_id', $paciente?->id) == $p->id)>
                            {{ $p->nombre_completo }}
                        </option>
                    @endforeach
                </select>
                @error('paciente_id')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Estado del paciente</label>
                <select name="estado_paciente" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                    <option value="">— Sin especificar —</option>
                    <option value="estable" @selected(old('estado_paciente') == 'estable')>Estable</option>
                    <option value="mejorando" @selected(old('estado_paciente') == 'mejorando')>Mejorando</option>
                    <option value="grave" @selected(old('estado_paciente') == 'grave')>Grave</option>
                    <option value="critico" @selected(old('estado_paciente') == 'critico')>Crítico</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium">Contenido de la nota *</label>
                <textarea name="contenido" rows="5" required
                    placeholder="Describe los cuidados, observaciones, cambios en el estado del paciente..."
                    class="mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('contenido') }}</textarea>
                @error('contenido')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <h3 class="text-sm font-bold text-gray-700 mb-2">Signos vitales del momento (opcional)</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs">Temperatura (°C)</label>
                        <input type="number" step="0.1" name="temperatura" value="{{ old('temperatura') }}"
                            class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-xs">Frec. cardíaca</label>
                        <input type="number" name="frecuencia_cardiaca" value="{{ old('frecuencia_cardiaca') }}"
                            class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-xs">Frec. respiratoria</label>
                        <input type="number" name="frecuencia_respiratoria"
                            value="{{ old('frecuencia_respiratoria') }}"
                            class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-xs">Presión arterial</label>
                        <input type="text" name="presion_arterial" value="{{ old('presion_arterial') }}"
                            placeholder="120/80" class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-xs">Saturación O₂ (%)</label>
                        <input type="number" name="saturacion_oxigeno" value="{{ old('saturacion_oxigeno') }}"
                            class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
                    </div>
                </div>
            </div>

            <div class="flex justify-between items-center pt-4">
                <a href="{{ route('enfermeria.notas.index') }}"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">Cancelar</a>
                <button type="submit"
                    class="px-4 py-2 bg-pink-600 hover:bg-pink-700 text-white rounded-md text-sm font-medium">
                    Guardar Nota
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
