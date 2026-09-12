<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Asignar Turno a {{ $user->nombre_completo }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <form action="{{ route('admin.users.turnos.store', $user) }}" method="POST"
            class="space-y-6 bg-white p-6 rounded-lg shadow">
            @csrf

            <div>
                <label class="block text-sm font-medium">Turno *</label>
                <select name="turno_id" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                    <option value="">— Selecciona un turno —</option>
                    @foreach ($turnos as $t)
                        <option value="{{ $t->id }}" @selected(old('turno_id') == $t->id)>
                            {{ $t->nombre }} ({{ $t->rango }})
                        </option>
                    @endforeach
                </select>
                @error('turno_id')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Día de la semana</label>
                <select name="dia_semana" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                    <option value="">— Todos los días —</option>
                    @foreach (['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'] as $i => $dia)
                        <option value="{{ $i }}" @selected(old('dia_semana') == $i)>{{ $dia }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">Si dejas "Todos los días", aplica a cualquier día de la semana.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium">Fecha de inicio *</label>
                    <input type="date" name="fecha_inicio" value="{{ old('fecha_inicio', now()->format('Y-m-d')) }}"
                        required class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                    @error('fecha_inicio')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium">Fecha de fin</label>
                    <input type="date" name="fecha_fin" value="{{ old('fecha_fin') }}"
                        class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                    <p class="text-xs text-gray-500 mt-1">Vacío = indefinido.</p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium">Área / Servicio</label>
                <input type="text" name="area" value="{{ old('area') }}"
                    placeholder="Ej. Urgencias, Hospitalización"
                    class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
            </div>

            <div>
                <label class="block text-sm font-medium">Notas</label>
                <textarea name="notas" rows="2" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('notas') }}</textarea>
            </div>

            <div class="flex justify-between items-center pt-4">
                <a href="{{ route('admin.users.turnos.index', $user) }}"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">Cancelar</a>
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium">
                    Asignar Turno
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
