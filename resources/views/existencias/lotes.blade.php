<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">Lotes</h2>
            <a href="{{ route('existencias.index') }}" class="text-sm bg-gray-100 hover:bg-gray-200 px-3 py-2 rounded-md">
                Ver por medicamento
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- Filtros --}}
        <div class="flex gap-2">
            <a href="{{ route('existencias.lotes') }}"
                class="px-3 py-1.5 text-sm rounded-md {{ !$filtro ? 'bg-blue-600 text-white' : 'bg-white border' }}">
                Todos
            </a>
            <a href="{{ route('existencias.lotes', ['filtro' => 'vigentes']) }}"
                class="px-3 py-1.5 text-sm rounded-md {{ $filtro === 'vigentes' ? 'bg-blue-600 text-white' : 'bg-white border' }}">
                Vigentes
            </a>
            <a href="{{ route('existencias.lotes', ['filtro' => 'proximos']) }}"
                class="px-3 py-1.5 text-sm rounded-md {{ $filtro === 'proximos' ? 'bg-blue-600 text-white' : 'bg-white border' }}">
                Próximos a caducar (30d)
            </a>
            <a href="{{ route('existencias.lotes', ['filtro' => 'caducados']) }}"
                class="px-3 py-1.5 text-sm rounded-md {{ $filtro === 'caducados' ? 'bg-blue-600 text-white' : 'bg-white border' }}">
                Caducados
            </a>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lote</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Medicamento</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Caducidad</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Disponible</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($lotes as $lote)
                        <tr>
                            <td class="px-4 py-3 text-sm font-mono">
                                {{ $lote->codigo_lote ?? 'LOTE-' . $lote->id }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                {{ $lote->medicamento?->nombre ?? '—' }}
                                {{ $lote->medicamento?->concentracion }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                {{ $lote->fecha_caducidad->format('d/m/Y') }}
                                <span class="text-xs text-gray-500 block">
                                    @if ($lote->esta_caducado)
                                        Caducó hace {{ abs($lote->dias_para_caducar) }} días
                                    @else
                                        Vence en {{ $lote->dias_para_caducar }} días
                                    @endif
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-right font-semibold">
                                {{ $lote->cantidad_disponible }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 rounded text-xs {{ $lote->estado_color }}">
                                    {{ $lote->estado_label }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-6 text-gray-500">Sin lotes.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $lotes->links() }}</div>
    </div>
</x-app-layout>
