<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">Receta médica</h2>
            <a href="{{ route('dispensaciones.index') }}" class="text-sm text-gray-600 hover:underline">← Volver</a>
        </div>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

        @if (session('success'))
            <div class="p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="p-3 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
        @endif

        {{-- Estado --}}
        <div
            class="bg-white p-6 rounded-lg shadow border-l-4
            {{ $consulta->dispensada ? 'border-green-500' : 'border-yellow-500' }}">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-500">Estado de la receta</p>
                    <p class="text-lg font-bold">
                        @if ($consulta->dispensada)
                            <span class="text-green-700">Dispensada</span>
                        @else
                            <span class="text-yellow-700">Pendiente de dispensar</span>
                        @endif
                    </p>
                    @if ($consulta->dispensada)
                        <p class="text-sm text-gray-600 mt-1">
                            Por {{ $consulta->dispensadaPor?->nombre_completo ?? '—' }}
                            el {{ $consulta->dispensada_en?->format('d/m/Y H:i') }}
                        </p>
                    @endif
                </div>
                <div class="text-right text-sm text-gray-500">
                    <p>Receta #{{ $consulta->id }}</p>
                    <p>{{ $consulta->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>

        {{-- Paciente --}}
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="font-semibold text-sm text-gray-700 mb-2">Paciente</h3>
            <p class="text-lg font-bold">{{ $consulta->paciente?->nombre_completo }}</p>
            <dl class="grid grid-cols-2 gap-2 text-sm mt-2">
                <dt class="text-gray-500">CURP:</dt>
                <dd>{{ $consulta->paciente?->curp ?? '—' }}</dd>
                <dt class="text-gray-500">Edad:</dt>
                <dd>{{ $consulta->paciente?->edad ?? '—' }} años</dd>
                <dt class="text-gray-500">Alergias:</dt>
                <dd class="text-red-600 font-semibold">{{ $consulta->paciente?->alergias ?? 'Ninguna' }}</dd>
            </dl>
        </div>

        {{-- Médico --}}
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="font-semibold text-sm text-gray-700 mb-2">Médico prescriptor</h3>
            <p class="font-semibold">{{ $consulta->medico?->nombre_completo ?? '—' }}</p>
            @if ($consulta->diagnosticoPrincipal)
                <p class="text-sm text-gray-600 mt-1">
                    <strong>Dx:</strong> {{ $consulta->diagnosticoPrincipal->etiqueta }}
                </p>
            @endif
        </div>

        {{-- Medicamentos --}}
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="font-semibold text-sm text-gray-700 mb-3">Medicamentos recetados</h3>

            @if ($consulta->medicamentos->count())
                <table class="min-w-full text-sm border">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left">Medicamento</th>
                            <th class="px-3 py-2 text-left">Dosis</th>
                            <th class="px-3 py-2 text-left">Vía</th>
                            <th class="px-3 py-2 text-left">Frecuencia</th>
                            <th class="px-3 py-2 text-left">Duración</th>
                            <th class="px-3 py-2 text-right">Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($consulta->medicamentos as $m)
                            <tr class="border-t">
                                <td class="px-3 py-2">
                                    {{ $m->nombre }} {{ $m->concentracion }}
                                    @if ($m->pivot->indicaciones)
                                        <div class="text-xs text-gray-500 mt-0.5">
                                            {{ $m->pivot->indicaciones }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-3 py-2">{{ $m->pivot->dosis ?? '—' }}</td>
                                <td class="px-3 py-2">{{ $m->pivot->via ?? '—' }}</td>
                                <td class="px-3 py-2">{{ $m->pivot->frecuencia ?? '—' }}</td>
                                <td class="px-3 py-2">{{ $m->pivot->duracion ?? '—' }}</td>
                                <td class="px-3 py-2 text-right">
                                    @php $stock = $m->stock_total_calculado; @endphp
                                    @if ($stock <= 0)
                                        <span class="text-red-600 font-bold">{{ $stock }}</span>
                                    @elseif ($stock <= $m->stock_minimo)
                                        <span class="text-yellow-600 font-bold">{{ $stock }}</span>
                                    @else
                                        <span class="text-green-600 font-bold">{{ $stock }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            @if ($consulta->receta_libre)
                <div class="mt-4 p-3 bg-purple-50 border border-purple-200 rounded">
                    <p class="text-sm font-semibold text-purple-900 mb-1">Receta libre</p>
                    <p class="text-sm text-gray-700 whitespace-pre-line">{{ $consulta->receta_libre }}</p>
                </div>
            @endif
        </div>

        {{-- Acciones --}}
        @if (!$consulta->dispensada)
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="font-semibold text-sm text-gray-700 mb-3">Dispensar receta</h3>
                <form action="{{ route('dispensaciones.dispensar', $consulta) }}" method="POST"
                    onsubmit="return confirm('¿Confirmar dispensación? Se descontará del inventario.')"
                    class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-sm text-gray-600">Notas (opcional)</label>
                        <textarea name="notas_dispensacion" rows="2" class="mt-1 w-full border-gray-300 rounded-md shadow-sm"
                            placeholder="Observaciones..."></textarea>
                    </div>
                    <button type="submit"
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md text-sm font-medium">
                        ✓ Dispensar receta
                    </button>
                </form>
            </div>
        @else
            @if (auth()->user()->hasRole('administrador'))
                <div class="bg-white p-6 rounded-lg shadow">
                    <form action="{{ route('dispensaciones.revertir', $consulta) }}" method="POST"
                        onsubmit="return confirm('¿Revertir la dispensación? El stock será devuelto.')">
                        @csrf
                        <button type="submit" class="text-sm text-red-600 hover:underline">
                            Revertir dispensación
                        </button>
                    </form>
                </div>
            @endif
        @endif
    </div>
</x-app-layout>
