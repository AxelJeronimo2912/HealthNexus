<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">Expedientes</h2>
            <a href="{{ route('pacientes.index') }}" class="text-sm text-gray-600 hover:underline">
                Ver todos los pacientes →
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- Buscador --}}
        <form method="GET" action="{{ route('expedientes.index') }}" class="mb-4 flex gap-2">
            <input type="text" name="buscar" value="{{ $busqueda }}"
                placeholder="Buscar por nombre, apellidos o CURP" class="flex-1 border-gray-300 rounded-md shadow-sm">
            <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">
                Buscar
            </button>
            @if ($busqueda)
                <a href="{{ route('expedientes.index') }}"
                    class="px-4 py-2 bg-white border rounded-md text-sm">Limpiar</a>
            @endif
        </form>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paciente</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">CURP</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Edad</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Último triage</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Consultas</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($pacientes as $paciente)
                        <tr>
                            <td class="px-4 py-3 text-sm font-medium">
                                {{ $paciente->nombre_completo }}
                            </td>
                            <td class="px-4 py-3 text-sm">{{ $paciente->curp ?? '—' }}</td>
                            <td class="px-4 py-3 text-sm">{{ $paciente->edad }} años</td>
                            <td class="px-4 py-3 text-sm">
                                @if ($paciente->ultimoSignoVital)
                                    <span
                                        class="px-2 py-1 rounded text-xs {{ $paciente->ultimoSignoVital->triage_color }}">
                                        {{ $paciente->ultimoSignoVital->triage_label }}
                                    </span>
                                @else
                                    <span class="text-gray-400 text-xs">Sin signos vitales</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">
                                    {{ $paciente->consultas_count }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right text-sm space-x-2">
                                <a href="{{ route('expedientes.show', $paciente) }}"
                                    class="text-blue-600 hover:underline">Ver expediente</a>
                                <a href="{{ route('pacientes.show', $paciente) }}"
                                    class="text-gray-600 hover:underline">Ficha</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                                Sin pacientes con expediente.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $pacientes->links() }}</div>
    </div>
</x-app-layout>
