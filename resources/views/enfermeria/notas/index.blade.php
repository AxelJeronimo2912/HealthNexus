<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">Notas de Enfermería</h2>
            <a href="{{ route('enfermeria.notas.create') }}"
                class="bg-pink-600 hover:bg-pink-700 text-white px-4 py-2 rounded-md text-sm font-medium flex items-center">
                <x-heroicon-o-plus class="w-4 h-4 mr-1" />
                Nueva Nota
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        @if (session('success'))
            <div class="p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
        @endif

        {{-- Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-xs text-gray-500 uppercase">Total notas</p>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-xs text-gray-500 uppercase">Hoy</p>
                <p class="text-2xl font-bold text-pink-600">{{ $stats['hoy'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-xs text-gray-500 uppercase">Esta semana</p>
                <p class="text-2xl font-bold text-indigo-600">{{ $stats['esta_semana'] }}</p>
            </div>
        </div>

        {{-- Filtros --}}
        <form method="GET" class="bg-white p-4 rounded-lg shadow grid grid-cols-1 md:grid-cols-5 gap-3">
            <div class="md:col-span-2">
                <label class="block text-xs text-gray-500 uppercase">Buscar paciente</label>
                <input type="text" name="buscar" value="{{ $buscar }}" placeholder="Nombre o apellidos"
                    class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
            </div>
            <div>
                <label class="block text-xs text-gray-500 uppercase">Paciente</label>
                <select name="paciente_id" class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
                    <option value="">Todos</option>
                    @foreach ($pacientes as $p)
                        <option value="{{ $p->id }}" @selected($pacienteId == $p->id)>
                            {{ $p->nombre_completo }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 uppercase">Desde</label>
                <input type="date" name="desde" value="{{ $desde }}"
                    class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
            </div>
            <div>
                <label class="block text-xs text-gray-500 uppercase">Hasta</label>
                <input type="date" name="hasta" value="{{ $hasta }}"
                    class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
            </div>
            <div class="md:col-span-5 flex gap-2">
                <button type="submit" class="px-4 py-2 bg-pink-600 hover:bg-pink-700 text-white rounded-md text-sm">
                    Filtrar
                </button>
                <a href="{{ route('enfermeria.notas.index') }}"
                    class="px-4 py-2 bg-white border rounded-md text-sm">Limpiar</a>
            </div>
        </form>

        {{-- Tabla --}}
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paciente</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nota</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Enfermero/a</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($notas as $nota)
                        <tr>
                            <td class="px-4 py-3 text-sm">{{ $nota->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 text-sm">
                                <div class="font-medium">{{ $nota->paciente?->nombre_completo ?? '—' }}</div>
                                @if ($nota->cama)
                                    <div class="text-xs text-gray-500">Cama: {{ $nota->cama->codigo }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if ($nota->estado_paciente)
                                    <span class="px-2 py-1 rounded text-xs {{ $nota->estado_color }}">
                                        {{ ucfirst($nota->estado_paciente) }}
                                    </span>
                                @else
                                    <span class="text-gray-400 text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                {{ Str::limit($nota->contenido, 60) }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                {{ $nota->user?->nombre_completo ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-right text-sm space-x-2">
                                <a href="{{ route('enfermeria.notas.show', $nota) }}"
                                    class="text-blue-600 hover:underline">Ver</a>
                                @if ($nota->user_id === auth()->id() || auth()->user()->hasRole('administrador'))
                                    <form action="{{ route('enfermeria.notas.destroy', $nota) }}" method="POST"
                                        class="inline" onsubmit="return confirm('¿Eliminar esta nota?')">
                                        @csrf @method('DELETE')
                                        <button class="text-red-600 hover:underline">Eliminar</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                                Sin notas de enfermería registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $notas->links() }}</div>
    </div>
</x-app-layout>
