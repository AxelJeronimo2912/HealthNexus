<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">
                Citas del {{ $fecha->format('d/m/Y') }}
            </h2>
            @can('agenda.crear')
                <a href="{{ route('agenda.create', ['fecha' => $fecha->format('Y-m-d')]) }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium flex items-center">
                    <x-heroicon-o-plus class="w-4 h-4 mr-1" />
                    Nueva Cita
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
        @endif

        {{-- Navegación de fecha --}}
        <div class="flex justify-between items-center mb-6">
            <a href="{{ route('citas.index', ['fecha' => $fecha->copy()->subDay()->format('Y-m-d')]) }}"
                class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">← Día anterior</a>

            <div class="text-center">
                <p class="text-lg font-bold">
                    {{ ucfirst($fecha->locale('es')->translatedFormat('l d \d\e F \d\e Y')) }}
                </p>
                <div class="flex gap-2 justify-center mt-1">
                    <a href="{{ route('citas.index', ['fecha' => today()->format('Y-m-d')]) }}"
                        class="text-xs text-blue-600 hover:underline">Hoy</a>
                    <a href="{{ route('agenda.index', ['fecha' => $fecha->format('Y-m-d')]) }}"
                        class="text-xs text-blue-600 hover:underline">Ver semana</a>
                </div>
            </div>

            <a href="{{ route('citas.index', ['fecha' => $fecha->copy()->addDay()->format('Y-m-d')]) }}"
                class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">Día siguiente →</a>
        </div>

        {{-- Estadísticas --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-xs text-gray-500 uppercase">Total</p>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-xs text-gray-500 uppercase">Programadas</p>
                <p class="text-2xl font-bold text-blue-600">{{ $stats['programadas'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-xs text-gray-500 uppercase">Confirmadas</p>
                <p class="text-2xl font-bold text-indigo-600">{{ $stats['confirmadas'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-xs text-gray-500 uppercase">Atendidas</p>
                <p class="text-2xl font-bold text-green-600">{{ $stats['atendidas'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-xs text-gray-500 uppercase">Canceladas</p>
                <p class="text-2xl font-bold text-red-600">{{ $stats['canceladas'] }}</p>
            </div>
        </div>

        {{-- Listado de citas --}}
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hora</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paciente</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Triage</th>
                        @if (auth()->user()->hasRole('administrador'))
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Médico</th>
                        @endif
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Duración</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($citas as $cita)
                        <tr>
                            <td class="px-4 py-3 text-sm font-semibold">
                                {{ $cita->fecha_hora->format('H:i') }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                {{ $cita->paciente->nombre_completo ?? 'Paciente eliminado' }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if ($cita->triage_al_momento)
                                    @php
                                        $color = match ($cita->triage_al_momento) {
                                            'rojo' => 'bg-red-100 text-red-800',
                                            'naranja' => 'bg-orange-100 text-orange-800',
                                            'amarillo' => 'bg-yellow-100 text-yellow-800',
                                            'verde' => 'bg-green-100 text-green-800',
                                            'azul' => 'bg-blue-100 text-blue-800',
                                            default => 'bg-gray-100 text-gray-800',
                                        };
                                    @endphp
                                    <span class="px-2 py-1 rounded text-xs {{ $color }}">
                                        {{ ucfirst($cita->triage_al_momento) }}
                                    </span>
                                @else
                                    —
                                @endif
                            </td>
                            @if (auth()->user()->hasRole('administrador'))
                                <td class="px-4 py-3 text-sm">
                                    {{ $cita->medico->nombre_completo ?? '—' }}
                                </td>
                            @endif
                            <td class="px-4 py-3 text-sm">{{ $cita->duracion_minutos }} min</td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 rounded text-xs {{ $cita->estado_color }}">
                                    {{ $cita->estado_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right text-sm space-x-2">
                                <a href="{{ route('citas.show', $cita) }}"
                                    class="text-blue-600 hover:underline">Ver</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->hasRole('administrador') ? 7 : 6 }}"
                                class="px-4 py-6 text-center text-gray-500">
                                No hay citas para este día.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
