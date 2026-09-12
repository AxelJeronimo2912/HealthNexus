<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">
                Turnos de {{ $user->nombre_completo }}
            </h2>
            <a href="{{ route('admin.users.turnos.create', $user) }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium flex items-center">
                <x-heroicon-o-plus class="w-4 h-4 mr-1" />
                Asignar Turno
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto sm:px-6 lg:px-8">

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
        @endif

        <div class="mb-4">
            <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-600 hover:underline">
                ← Volver a usuarios
            </a>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Turno</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Horario</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Día</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vigencia</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Área</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($asignaciones as $turno)
                        @php
                            $p = $turno->pivot;
                            $pivotId = $p->id;
                            $dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
                        @endphp
                        <tr>
                            <td class="px-4 py-3 text-sm">
                                <div class="font-medium">{{ $turno->nombre }}</div>
                                <div class="text-xs text-gray-500 font-mono">{{ $turno->codigo }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm">{{ $turno->rango }}</td>
                            <td class="px-4 py-3 text-sm">
                                {{ $p->dia_semana !== null ? $dias[$p->dia_semana] : 'Todos los días' }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                {{ \Carbon\Carbon::parse($p->fecha_inicio)->format('d/m/Y') }}
                                @if ($p->fecha_fin)
                                    — {{ \Carbon\Carbon::parse($p->fecha_fin)->format('d/m/Y') }}
                                @else
                                    — indefinido
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">{{ $p->area ?? '—' }}</td>
                            <td class="px-4 py-3 text-sm">
                                @if ($p->activo)
                                    <span class="text-green-600 font-semibold">Activo</span>
                                @else
                                    <span class="text-gray-400 font-semibold">Inactivo</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right text-sm space-x-2">
                                <form action="{{ route('admin.users.turnos.toggle', [$user, $pivotId]) }}"
                                    method="POST" class="inline">
                                    @csrf
                                    <button class="text-blue-600 hover:underline">
                                        {{ $p->activo ? 'Desactivar' : 'Activar' }}
                                    </button>
                                </form>
                                <form action="{{ route('admin.users.turnos.destroy', [$user, $pivotId]) }}"
                                    method="POST" class="inline"
                                    onsubmit="return confirm('¿Eliminar esta asignación?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-gray-500">Sin turnos asignados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
