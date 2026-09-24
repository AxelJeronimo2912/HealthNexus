<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">
                Médicos de: {{ $especialidad->nombre }}
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
            <h3 class="font-bold text-gray-800 mb-3">Asignar médico</h3>

            @if ($disponibles->isEmpty())
                <p class="text-sm text-gray-500">
                    No hay médicos disponibles para asignar (todos ya están en esta especialidad).
                </p>
            @else
                <form action="{{ route('especialidades.medicos.asignar', $especialidad) }}" method="POST"
                    class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    @csrf

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium">Médico</label>
                        <select name="user_id" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">— Selecciona —</option>
                            @foreach ($disponibles as $u)
                                <option value="{{ $u->id }}">
                                    {{ $u->nombre_completo ?: $u->name }}
                                    ({{ $u->getRoleNames()->first() ?? 'sin rol' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Cédula especialidad</label>
                        <input type="text" name="numero_cedula_especialidad"
                            class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div class="flex items-end gap-2">
                        <label class="inline-flex items-center text-sm">
                            <input type="checkbox" name="es_principal" value="1"
                                class="rounded border-gray-300 text-blue-600 shadow-sm">
                            <span class="ml-2">Principal</span>
                        </label>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm">
                            Asignar
                        </button>
                    </div>
                </form>
            @endif
        </div>

        {{-- Lista --}}
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="font-bold text-gray-800 mb-3">
                Médicos asignados ({{ $especialidad->medicos->count() }})
            </h3>

            @if ($especialidad->medicos->isEmpty())
                <p class="text-sm text-gray-500">Sin médicos asignados.</p>
            @else
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs text-gray-500 uppercase">Médico</th>
                            <th class="px-3 py-2 text-left text-xs text-gray-500 uppercase">Rol sistema</th>
                            <th class="px-3 py-2 text-left text-xs text-gray-500 uppercase">Cédula esp.</th>
                            <th class="px-3 py-2 text-center text-xs text-gray-500 uppercase">Principal</th>
                            <th class="px-3 py-2 text-right text-xs text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($especialidad->medicos as $u)
                            <tr>
                                <td class="px-3 py-2">
                                    <div class="font-medium">{{ $u->nombre_completo ?: $u->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $u->email }}</div>
                                </td>
                                <td class="px-3 py-2">
                                    <span class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded text-xs">
                                        {{ $u->getRoleNames()->first() ?? 'sin rol' }}
                                    </span>
                                </td>
                                <td class="px-3 py-2 text-xs">
                                    {{ $u->pivot->numero_cedula_especialidad ?? '—' }}
                                </td>
                                <td class="px-3 py-2 text-center">
                                    @if ($u->pivot->es_principal)
                                        <span class="px-2 py-0.5 bg-yellow-100 text-yellow-800 rounded text-xs">
                                            ★ Principal
                                        </span>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2 text-right">
                                    <form
                                        action="{{ route('especialidades.medicos.quitar', [$especialidad, $u->pivot->id]) }}"
                                        method="POST" class="inline"
                                        onsubmit="return confirm('¿Quitar este médico de la especialidad?')">
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
