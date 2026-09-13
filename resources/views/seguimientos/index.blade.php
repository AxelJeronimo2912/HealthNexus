<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Seguimiento de Pacientes</h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('seguimientos.index', ['filtro' => 'todos']) }}"
                class="bg-white p-4 rounded-lg shadow hover:shadow-md transition">
                <p class="text-xs text-gray-500 uppercase">Total en seguimiento</p>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
            </a>
            <a href="{{ route('seguimientos.index', ['filtro' => 'cama']) }}"
                class="bg-white p-4 rounded-lg shadow hover:shadow-md transition">
                <p class="text-xs text-gray-500 uppercase">Con cama asignada</p>
                <p class="text-2xl font-bold text-blue-600">{{ $stats['con_cama'] }}</p>
            </a>
            <a href="{{ route('seguimientos.index', ['filtro' => 'triage']) }}"
                class="bg-white p-4 rounded-lg shadow hover:shadow-md transition">
                <p class="text-xs text-gray-500 uppercase">Triage grave (24h)</p>
                <p class="text-2xl font-bold text-red-600">{{ $stats['con_triage'] }}</p>
            </a>
        </div>

        {{-- Buscador --}}
        <form method="GET" class="flex gap-2 flex-wrap">
            <input type="text" name="buscar" value="{{ $busqueda }}" placeholder="Buscar paciente por nombre"
                class="flex-1 min-w-[200px] border-gray-300 rounded-md shadow-sm">
            <select name="filtro" class="border-gray-300 rounded-md shadow-sm">
                <option value="todos" @selected($filtro === 'todos')>Todos</option>
                <option value="cama" @selected($filtro === 'cama')>Con cama</option>
                <option value="triage" @selected($filtro === 'triage')>Con triage grave</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">
                Filtrar
            </button>
        </form>

        {{-- Tabla --}}
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paciente</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cama</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Triage</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Último seguimiento
                        </th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($pacientes as $paciente)
                        <tr>
                            <td class="px-4 py-3 text-sm">
                                <div class="font-medium">{{ $paciente->nombre_completo }}</div>
                                <div class="text-xs text-gray-500">
                                    {{ $paciente->edad }} años — {{ ucfirst($paciente->sexo) }}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if ($paciente->cama_actual)
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">
                                        {{ $paciente->cama_actual->cama->codigo }}
                                    </span>
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ $paciente->cama_actual->cama->area }}
                                    </div>
                                @else
                                    <span class="text-gray-400 text-xs">Sin cama</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if ($paciente->ultimoSignoVital)
                                    <span
                                        class="px-2 py-1 rounded text-xs {{ $paciente->ultimoSignoVital->triage_color }}">
                                        {{ $paciente->ultimoSignoVital->triage_label }}
                                    </span>
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ $paciente->ultimoSignoVital->created_at->diffForHumans() }}
                                    </div>
                                @else
                                    <span class="text-gray-400 text-xs">Sin signos</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if ($paciente->seguimientoActual)
                                    <div class="text-xs text-gray-500">
                                        {{ $paciente->seguimientoActual->created_at->diffForHumans() }}
                                    </div>
                                    <div class="text-xs text-gray-700">
                                        {{ $paciente->seguimientoActual->user?->nombre_completo ?? '—' }}
                                    </div>
                                @else
                                    <span class="text-gray-400 text-xs">Sin seguimiento</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right text-sm space-x-2">
                                <a href="{{ route('seguimientos.show', $paciente) }}"
                                    class="text-blue-600 hover:underline">Ver seguimiento</a>
                                <a href="{{ route('seguimientos.create', $paciente) }}"
                                    class="text-green-600 hover:underline">+ Nuevo</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                                Sin pacientes en seguimiento.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $pacientes->links() }}</div>
    </div>
</x-app-layout>
