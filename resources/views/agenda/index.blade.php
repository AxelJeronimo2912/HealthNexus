<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">Agenda Médica</h2>
            <a href="{{ route('agenda.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium flex items-center">
                <x-heroicon-o-plus class="w-4 h-4 mr-1" />
                Nueva Cita
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
        @endif

        {{-- Navegación de semana --}}
        <div class="flex justify-between items-center mb-6">
            <a href="{{ route('agenda.index', ['fecha' => $inicioSemana->copy()->subWeek()->format('Y-m-d')]) }}"
                class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">← Semana anterior</a>

            <div class="text-center">
                <p class="text-lg font-bold">
                    {{ $inicioSemana->format('d M') }} — {{ $finSemana->format('d M Y') }}
                </p>
                <a href="{{ route('agenda.index') }}" class="text-xs text-blue-600 hover:underline">Hoy</a>
            </div>

            <a href="{{ route('agenda.index', ['fecha' => $inicioSemana->copy()->addWeek()->format('Y-m-d')]) }}"
                class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">Semana siguiente →</a>
        </div>

        {{-- Estadísticas --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-xs text-gray-500 uppercase">Total semana</p>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['total_semana'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-xs text-gray-500 uppercase">Programadas</p>
                <p class="text-2xl font-bold text-blue-600">{{ $stats['programadas'] }}</p>
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

        {{-- Calendario semanal --}}
        <div class="grid grid-cols-1 md:grid-cols-7 gap-2">
            @foreach ($citasPorDia as $fecha => $citas)
                @php
                    $dia = \Carbon\Carbon::parse($fecha);
                    $esHoy = $dia->isToday();
                @endphp
                <div class="bg-white rounded-lg shadow {{ $esHoy ? 'ring-2 ring-blue-500' : '' }}">
                    <div class="p-3 border-b {{ $esHoy ? 'bg-blue-50' : 'bg-gray-50' }}">
                        <p class="text-xs text-gray-500 uppercase">
                            {{ ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'][$dia->dayOfWeek] }}
                        </p>
                        <p class="font-bold {{ $esHoy ? 'text-blue-600' : 'text-gray-800' }}">
                            {{ $dia->format('d') }}
                        </p>
                    </div>

                    <div class="p-2 space-y-1 min-h-[200px]">
                        @forelse ($citas as $cita)
                            @php
                                $triageColor = match ($cita->triage_al_momento) {
                                    'rojo' => 'border-l-4 border-red-500',
                                    'naranja' => 'border-l-4 border-orange-500',
                                    'amarillo' => 'border-l-4 border-yellow-500',
                                    'verde' => 'border-l-4 border-green-500',
                                    'azul' => 'border-l-4 border-blue-500',
                                    default => 'border-l-4 border-gray-300',
                                };
                            @endphp
                            <a href="#"
                                class="block p-2 bg-gray-50 hover:bg-gray-100 rounded text-xs {{ $triageColor }}">
                                <p class="font-semibold text-gray-800">
                                    {{ $cita->fecha_hora->format('H:i') }}
                                </p>
                                <p class="text-gray-700 truncate">
                                    {{ $cita->paciente->nombre_completo }}
                                </p>
                                <p class="text-gray-500 truncate text-[10px]">
                                    {{ $cita->medico->nombre_completo }}
                                </p>
                                <span
                                    class="inline-block mt-1 px-1 py-0.5 rounded text-[10px] {{ $cita->estado_color }}">
                                    {{ $cita->estado_label }}
                                </span>
                            </a>
                        @empty
                            <p class="text-xs text-gray-400 text-center py-4">Sin citas</p>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
