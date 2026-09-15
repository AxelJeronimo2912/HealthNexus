<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Nuevo seguimiento — {{ $paciente->nombre_completo }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <form action="{{ route('seguimientos.store', $paciente) }}" method="POST"
            class="space-y-6 bg-white p-6 rounded-lg shadow">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium">Tipo de nota *</label>
                    <select name="tipo" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                        <option value="evolucion">Evolución médica</option>
                        <option value="nota_enfermeria">Nota de enfermería</option>
                        <option value="interconsulta">Interconsulta</option>
                        <option value="traslado">Traslado</option>
                        <option value="alta">Alta</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium">Estado del paciente</label>
                    <select name="estado_paciente" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">— Sin especificar —</option>
                        <option value="estable">Estable</option>
                        <option value="mejorando">Mejorando</option>
                        <option value="grave">Grave</option>
                        <option value="critico">Crítico</option>
                        <option value="fallecido">Fallecido</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium">Contenido de la nota *</label>
                <textarea name="contenido" rows="6" required placeholder="Describe la evolución del paciente..."
                    class="mt-1 w-full border-gray-300 rounded-md shadow-sm"></textarea>
            </div>

            <div>
                <h3 class="text-sm font-bold text-gray-700 mb-2">Signos vitales del momento (opcional)</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs">Temperatura (°C)</label>
                        <input type="number" step="0.1" name="temperatura"
                            class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-xs">Frec. cardíaca</label>
                        <input type="number" name="frecuencia_cardiaca"
                            class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-xs">Frec. respiratoria</label>
                        <input type="number" name="frecuencia_respiratoria"
                            class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-xs">Presión arterial</label>
                        <input type="text" name="presion_arterial" placeholder="120/80"
                            class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-xs">Saturación O₂ (%)</label>
                        <input type="number" name="saturacion_oxigeno"
                            class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
                    </div>
                </div>
            </div>

            <div class="flex justify-between items-center pt-4">
                <a href="{{ route('seguimientos.show', $paciente) }}"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">Cancelar</a>
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium">
                    Guardar seguimiento
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
    