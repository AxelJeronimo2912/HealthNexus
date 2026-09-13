<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">Signos Vitales y Triage</h2>
            <a href="{{ route('signos-vitales.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium flex items-center">
                <x-heroicon-o-plus class="w-4 h-4 mr-1" />
                Nuevo Registro
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
        @endif

        {{-- Filtros --}}
        <form method="GET" class="mb-4 flex flex-wrap gap-2">
            <input type="text" name="buscar" value="{{ $busqueda }}" placeholder="Buscar paciente por nombre"
                class="flex-1 min-w-[200px] border-gray-300 rounded-md shadow-sm">
            <select name="triage" class="border-gray-300 rounded-md shadow-sm">
                <option value="">Todos los triages</option>
                <option value="rojo" @selected($filtroTriage == 'rojo')>🔴 Rojo</option>
                <option value="naranja" @selected($filtroTriage == 'naranja')>🟠 Naranja</option>
                <option value="amarillo" @selected($filtroTriage == 'amarillo')>🟡 Amarillo</option>
                <option value="verde" @selected($filtroTriage == 'verde')>🟢 Verde</option>
                <option value="azul" @selected($filtroTriage == 'azul')>🔵 Azul</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">Filtrar</button>
            @if ($busqueda || $filtroTriage)
                <a href="{{ route('signos-vitales.index') }}"
                    class="px-4 py-2 bg-white border rounded-md text-sm">Limpiar</a>
            @endif
        </form>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paciente</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Temp</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">FC</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">FR</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">SpO₂</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">TA</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Triage</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($registros as $r)
                        <tr>
                            <td class="px-4 py-3 text-sm">{{ $r->paciente->nombre_completo }}</td>
                            <td class="px-4 py-3 text-sm">{{ $r->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 text-sm">{{ $r->temperatura ? $r->temperatura . '°C' : '—' }}</td>
                            <td class="px-4 py-3 text-sm">{{ $r->frecuencia_cardiaca ?? '—' }}</td>
                            <td class="px-4 py-3 text-sm">{{ $r->frecuencia_respiratoria ?? '—' }}</td>
                            <td class="px-4 py-3 text-sm">
                                {{ $r->saturacion_oxigeno ? $r->saturacion_oxigeno . '%' : '—' }}</td>
                            <td class="px-4 py-3 text-sm">{{ $r->presion_arterial ?? '—' }}</td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 rounded text-xs border {{ $r->triage_color }}">
                                    {{ $r->triage_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right text-sm space-x-2">
                                <a href="{{ route('signos-vitales.show', $r) }}"
                                    class="text-gray-600 hover:underline">Ver</a>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-6 text-center text-gray-500">Sin registros.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $registros->links() }}</div>
    </div>
</x-app-layout>
