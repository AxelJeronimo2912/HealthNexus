<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800">Admisión Hospitalaria</h2>
                <p class="text-xs text-gray-500 mt-0.5">Registro de llegada y derivaciones de pacientes</p>
            </div>
            <a href="{{ route('admisiones.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium flex items-center">
                <x-heroicon-o-plus class="w-4 h-4 mr-1" />
                Nueva Admisión
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        @if (session('success'))
            <div class="p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
        @endif

        {{-- Disponibilidad --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-xs text-gray-500 uppercase">Camas libres</p>
                <p class="text-2xl font-bold text-green-600">{{ $disponibilidad['camas_libres'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-xs text-gray-500 uppercase">Camas ocupadas</p>
                <p class="text-2xl font-bold text-red-600">{{ $disponibilidad['camas_ocupadas'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-xs text-gray-500 uppercase">Médicos activos</p>
                <p class="text-2xl font-bold text-blue-600">{{ $disponibilidad['medicos_activos'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-xs text-gray-500 uppercase">En espera</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $disponibilidad['en_espera'] }}</p>
            </div>
        </div>

        {{-- Filtros --}}
        <form method="GET" class="flex gap-2 flex-wrap">
            <input type="date" name="fecha" value="{{ $fecha }}"
                class="border-gray-300 rounded-md shadow-sm">
            <input type="text" name="buscar" value="{{ $buscar }}" placeholder="Buscar folio, nombre o CURP"
                class="flex-1 min-w-[200px] border-gray-300 rounded-md shadow-sm">
            <select name="estado" class="border-gray-300 rounded-md shadow-sm">
                <option value="">Todos los estados</option>
                <option value="en_espera" @selected($filtroEstado === 'en_espera')>En espera</option>
                <option value="hospitalizado" @selected($filtroEstado === 'hospitalizado')>Hospitalizado</option>
                <option value="derivado" @selected($filtroEstado === 'derivado')>Derivado</option>
                <option value="alta" @selected($filtroEstado === 'alta')>Alta</option>
                <option value="fallecido" @selected($filtroEstado === 'fallecido')>Fallecido</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">
                Filtrar
            </button>
            <a href="{{ route('admisiones.index') }}" class="px-4 py-2 bg-white border rounded-md text-sm">Limpiar</a>
        </form>

        {{-- Tabla --}}
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Folio</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hora</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paciente</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Triage</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Destino</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($admisiones as $adm)
                        <tr>
                            <td class="px-4 py-3 text-sm font-mono">{{ $adm->folio }}</td>
                            <td class="px-4 py-3 text-sm">{{ $adm->fecha_hora_llegada->format('H:i') }}</td>
                            <td class="px-4 py-3 text-sm">
                                <div class="font-medium">{{ $adm->paciente?->nombre_completo ?? '—' }}</div>
                                <div class="text-xs text-gray-500">{{ $adm->paciente?->curp ?? '' }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm">{{ $adm->tipo_label }}</td>
                            <td class="px-4 py-3 text-sm">
                                @if ($adm->triage)
                                    @php
                                        $triageColor = match ($adm->triage) {
                                            'rojo' => 'bg-red-100 text-red-800',
                                            'naranja' => 'bg-orange-100 text-orange-800',
                                            'amarillo' => 'bg-yellow-100 text-yellow-800',
                                            'verde' => 'bg-green-100 text-green-800',
                                            'azul' => 'bg-blue-100 text-blue-800',
                                            default => 'bg-gray-100 text-gray-800',
                                        };
                                    @endphp
                                    <span class="px-2 py-1 rounded text-xs {{ $triageColor }}">
                                        {{ ucfirst($adm->triage) }}
                                    </span>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 rounded text-xs {{ $adm->estado_color }}">
                                    {{ $adm->estado_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                @if ($adm->hospitalDerivado)
                                    <span class="text-xs">→ {{ $adm->hospitalDerivado->nombre }}</span>
                                @elseif ($adm->cama)
                                    <span class="text-xs">{{ $adm->cama->codigo }}</span>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right text-sm">
                                <a href="{{ route('admisiones.show', $adm) }}"
                                    class="text-blue-600 hover:underline">Ver</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-6 text-center text-gray-500">Sin admisiones.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $admisiones->links() }}</div>
    </div>
</x-app-layout>
