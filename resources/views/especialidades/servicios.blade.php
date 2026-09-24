<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">
                Servicios de: {{ $especialidad->nombre }}
            </h2>
            <a href="{{ route('especialidades.show', $especialidad) }}" class="text-sm text-gray-600 hover:underline">←
                Volver</a>
        </div>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

        @if (session('success'))
            <div class="p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="p-3 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
        @endif

        {{-- Formulario --}}
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="font-bold text-gray-800 mb-3">Asociar servicio</h3>

            @if ($disponibles->isEmpty())
                <p class="text-sm text-gray-500">
                    No hay servicios disponibles para asociar.
                </p>
            @else
                <form action="{{ route('especialidades.servicios.asignar', $especialidad) }}" method="POST"
                    class="flex gap-3 items-end">
                    @csrf

                    <div class="flex-1">
                        <label class="block text-sm font-medium">Servicio</label>
                        <select name="servicio_id" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">— Selecciona —</option>
                            @foreach ($disponibles as $s)
                                <option value="{{ $s->id }}">
                                    {{ $s->nombre }} ({{ $s->tipo_label }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm">
                        Asociar
                    </button>
                </form>
            @endif
        </div>

        {{-- Lista --}}
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="font-bold text-gray-800 mb-3">
                Servicios asociados ({{ $especialidad->servicios->count() }})
            </h3>

            @if ($especialidad->servicios->isEmpty())
                <p class="text-sm text-gray-500">Sin servicios asociados.</p>
            @else
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs text-gray-500 uppercase">Servicio</th>
                            <th class="px-3 py-2 text-left text-xs text-gray-500 uppercase">Tipo</th>
                            <th class="px-3 py-2 text-left text-xs text-gray-500 uppercase">Ubicación</th>
                            <th class="px-3 py-2 text-right text-xs text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($especialidad->servicios as $s)
                            <tr>
                                <td class="px-3 py-2 font-medium">{{ $s->nombre }}</td>
                                <td class="px-3 py-2">
                                    <span class="px-2 py-0.5 rounded text-xs {{ $s->tipo_color }}">
                                        {{ $s->tipo_label }}
                                    </span>
                                </td>
                                <td class="px-3 py-2 text-xs text-gray-500">
                                    {{ $s->ubicacion ?? '—' }}
                                </td>
                                <td class="px-3 py-2 text-right">
                                    <form
                                        action="{{ route('especialidades.servicios.quitar', [$especialidad, $s->pivot->id]) }}"
                                        method="POST" class="inline"
                                        onsubmit="return confirm('¿Quitar este servicio de la especialidad?')">
                                        @csrf @method('DELETE')
                                        <button class="text-red-600 hover:underline text-xs">Quitar</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</x-app-layout>
