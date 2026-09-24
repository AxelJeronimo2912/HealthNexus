<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Registrar Administración de Medicamento</h2>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <form action="{{ route('enfermeria.administraciones.store') }}" method="POST"
            class="space-y-6 bg-white p-6 rounded-lg shadow">
            @csrf

            @if (session('error'))
                <div class="p-3 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
            @endif

            <div>
                <label class="block text-sm font-medium">Paciente *</label>
                <select name="paciente_id" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                    <option value="">— Selecciona —</option>
                    @foreach ($pacientes as $p)
                        <option value="{{ $p->id }}" @selected(old('paciente_id', $pacienteId) == $p->id)>
                            {{ $p->nombre_completo }}
                        </option>
                    @endforeach
                </select>
                @error('paciente_id')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Medicamento *</label>
                <select name="medicamento_id" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                    <option value="">— Selecciona —</option>
                    @foreach ($medicamentos as $m)
                        <option value="{{ $m->id }}" @selected(old('medicamento_id') == $m->id)>
                            {{ $m->nombre }} {{ $m->concentracion }} — Stock: {{ $m->stock_total_calculado }}
                        </option>
                    @endforeach
                </select>
                @error('medicamento_id')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium">Dosis *</label>
                    <input type="text" name="dosis" value="{{ old('dosis') }}" required placeholder="500 mg"
                        class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium">Vía *</label>
                    <select name="via" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">— Selecciona —</option>
                        @foreach (['Oral', 'Intravenosa', 'Intramuscular', 'Subcutánea', 'Tópica', 'Inhalatoria', 'Oftálmica', 'Rectal'] as $via)
                            <option value="{{ $via }}" @selected(old('via') == $via)>{{ $via }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium">Cantidad *</label>
                    <input type="number" name="cantidad" value="{{ old('cantidad', 1) }}" required min="1"
                        max="100" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                    <p class="text-xs text-gray-500 mt-1">Se descuenta del stock</p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium">Fecha y hora de administración *</label>
                <input type="datetime-local" name="administrado_en"
                    value="{{ old('administrado_en', now()->format('Y-m-d\TH:i')) }}" required
                    class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
            </div>

            <div>
                <label class="inline-flex items-center">
                    <input type="checkbox" name="reaccion_adversa" value="1" @checked(old('reaccion_adversa'))
                        class="rounded border-gray-300 text-red-600 shadow-sm">
                    <span class="ml-2 text-sm font-medium text-red-700">⚠️ Reportar reacción adversa</span>
                </label>
            </div>

            <div>
                <label class="block text-sm font-medium">Observaciones</label>
                <textarea name="observaciones" rows="3" class="mt-1 w-full border-gray-300 rounded-md shadow-sm"
                    placeholder="Cualquier detalle o incidencia...">{{ old('observaciones') }}</textarea>
            </div>

            <div class="flex justify-between items-center pt-4">
                <a href="{{ route('enfermeria.administraciones.index') }}"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">Cancelar</a>
                <button type="submit"
                    class="px-4 py-2 bg-pink-600 hover:bg-pink-700 text-white rounded-md text-sm font-medium">
                    Registrar Administración
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
