<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">Administración de Medicamentos</h2>
            <a href="{{ route('enfermeria.administraciones.create') }}"
                class="bg-pink-600 hover:bg-pink-700 text-white px-4 py-2 rounded-md text-sm font-medium flex items-center">
                <x-heroicon-o-plus class="w-4 h-4 mr-1" />
                Registrar Administración
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        @if (session('success'))
            <div class="p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="p-3 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
        @endif

        {{-- Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-xs text-gray-500 uppercase">Total administraciones</p>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-xs text-gray-500 uppercase">Hoy</p>
                <p class="text-2xl font-bold text-pink-600">{{ $stats['hoy'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-xs text-gray-500 uppercase">Con reacción adversa</p>
                <p class="text-2xl font-bold text-red-600">{{ $stats['reacciones'] }}</p>
            </div>
        </div>

        {{-- Filtros --}}
        <form method="GET" class="bg-white p-4 rounded-lg shadow grid grid-cols-1 md:grid-cols-5 gap-3">
            <div class="md:col-span-2">
                <label class="block text-xs text-gray-500 uppercase">Buscar paciente</label>
                <input type="text" name="buscar" value="{{ $buscar }}"
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
                <a href="{{ route('enfermeria.administraciones.index') }}"
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
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Medicamento</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dosis</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vía</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Enfermero</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Reacción</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($administraciones as $adm)
                        <tr>
                            <td class="px-4 py-3 text-sm">{{ $adm->administrado_en->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 text-sm">{{ $adm->paciente?->nombre_completo ?? '—' }}</td>
                            <td class="px-4 py-3 text-sm">{{ $adm->medicamento?->nombre }}
                                {{ $adm->medicamento?->concentracion }}</td>
                            <td class="px-4 py-3 text-sm">{{ $adm->dosis }}</td>
                            <td class="px-4 py-3 text-sm">{{ $adm->via }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $adm->user?->nombre_completo ?? '—' }}</td>
                            <td class="px-4 py-3 text-center">
                                @if ($adm->reaccion_adversa)
                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs">Sí</span>
                                @else
                                    <span class="text-gray-400 text-xs">No</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right text-sm space-x-2">
                                <a href="{{ route('enfermeria.administraciones.show', $adm) }}"
                                    class="text-blue-600 hover:underline">Ver</a>
                                @if (auth()->user()->hasRole('administrador'))
                                    <form action="{{ route('enfermeria.administraciones.destroy', $adm) }}"
                                        method="POST" class="inline"
                                        onsubmit="return confirm('¿Eliminar y devolver stock?')">
                                        @csrf @method('DELETE')
                                        <button class="text-red-600 hover:underline">Eliminar</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-6 text-center text-gray-500">
                                Sin administraciones registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $administraciones->links() }}</div>
    </div>
</x-app-layout>
