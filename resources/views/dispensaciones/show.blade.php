<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detalle de Receta Médica</h2>
            <a href="{{ route('dispensaciones.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900 transition">← Volver al listado</a>
        </div>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

        @if (session('success'))
            <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm shadow-sm flex items-center justify-between">
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm shadow-sm flex items-center justify-between">
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- Estado --}}
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 border-l-4 {{ $consulta->dispensada ? 'border-l-green-500' : 'border-l-yellow-500' }}">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs uppercase tracking-wider text-gray-400 font-semibold">Estado de la receta</p>
                    <p class="text-lg font-bold mt-0.5">
                        @if ($consulta->dispensada)
                            <span class="text-green-700 flex items-center gap-1.5">
                                <span class="h-2.5 w-2.5 rounded-full bg-green-500"></span> Dispensada
                            </span>
                        @else
                            <span class="text-yellow-700 flex items-center gap-1.5">
                                <span class="h-2.5 w-2.5 rounded-full bg-yellow-500"></span> Pendiente de dispensar
                            </span>
                        @endif
                    </p>
                    @if ($consulta->dispensada)
                        <p class="text-xs text-gray-500 mt-1">
                            Dispensado por <span class="font-medium text-gray-700">{{ $consulta->dispensadaPor?->nombre_completo ?? '—' }}</span> el {{ $consulta->dispensada_en?->format('d/m/Y H:i') }}
                        </p>
                    @endif
                </div>
                <div class="text-right text-xs text-gray-500 bg-gray-50 p-3 rounded-md border border-gray-100">
                    <p class="font-bold text-gray-700">Receta #{{ $consulta->id }}</p>
                    <p class="mt-0.5">{{ $consulta->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>

        {{-- Paciente --}}
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <h3 class="font-semibold text-sm text-gray-800 uppercase tracking-wider mb-3 pb-2 border-b border-gray-100">Información del Paciente</h3>
            <p class="text-base font-bold text-gray-900">{{ $consulta->paciente?->nombre_completo }}</p>
            <dl class="grid grid-cols-2 gap-4 text-sm mt-3 bg-gray-50 p-3 rounded-md">
                <div>
                    <dt class="text-gray-500 text-xs">CURP</dt>
                    <dd class="font-medium text-gray-800 mt-0.5">{{ $consulta->paciente?->curp ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 text-xs">Edad</dt>
                    <dd class="font-medium text-gray-800 mt-0.5">{{ $consulta->paciente?->edad ?? '—' }} años</dd>
                </div>
                <div class="col-span-2">
                    <dt class="text-gray-500 text-xs">Alergias registradas</dt>
                    <dd class="text-red-600 font-semibold mt-0.5">{{ $consulta->paciente?->alergias ?? 'Ninguna registrada' }}</dd>
                </div>
            </dl>
        </div>

        {{-- Médico --}}
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <h3 class="font-semibold text-sm text-gray-800 uppercase tracking-wider mb-2 pb-2 border-b border-gray-100">Médico Prescriptor</h3>
            <p class="font-medium text-gray-900">{{ $consulta->medico?->nombre_completo ?? '—' }}</p>
            @if ($consulta->diagnosticoPrincipal)
                <p class="text-sm text-gray-600 mt-2 bg-indigo-50/50 p-2.5 rounded border border-indigo-100">
                    <strong class="text-indigo-900">Diagnóstico Principal (Dx):</strong> {{ $consulta->diagnosticoPrincipal->etiqueta }}
                </p>
            @endif
        </div>

        {{-- Medicamentos --}}
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <h3 class="font-semibold text-sm text-gray-800 uppercase tracking-wider mb-3 pb-2 border-b border-gray-100">Medicamentos Recetados</h3>

            @if ($consulta->medicamentos->count())
                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="min-w-full text-sm divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2.5 text-left font-medium text-gray-500 text-xs uppercase">Medicamento</th>
                                <th class="px-3 py-2.5 text-left font-medium text-gray-500 text-xs uppercase">Dosis</th>
                                <th class="px-3 py-2.5 text-left font-medium text-gray-500 text-xs uppercase">Vía</th>
                                <th class="px-3 py-2.5 text-left font-medium text-gray-500 text-xs uppercase">Frecuencia</th>
                                <th class="px-3 py-2.5 text-left font-medium text-gray-500 text-xs uppercase">Duración</th>
                                <th class="px-3 py-2.5 text-right font-medium text-gray-500 text-xs uppercase">Stock Disp.</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @foreach ($consulta->medicamentos as $m)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-2.5 font-medium text-gray-900">
                                        {{ $m->nombre }} {{ $m->concentracion }}
                                        @if ($m->pivot->indicaciones)
                                            <div class="text-xs text-gray-500 font-normal mt-0.5">
                                                {{ $m->pivot->indicaciones }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2.5 text-gray-600">{{ $m->pivot->dosis ?? '—' }}</td>
                                    <td class="px-3 py-2.5 text-gray-600">{{ $m->pivot->via ?? '—' }}</td>
                                    <td class="px-3 py-2.5 text-gray-600">{{ $m->pivot->frecuencia ?? '—' }}</td>
                                    <td class="px-3 py-2.5 text-gray-600">{{ $m->pivot->duracion ?? '—' }}</td>
                                    <td class="px-3 py-2.5 text-right">
                                        @php $stock = $m->stock_total_calculado; @endphp
                                        @if ($stock <= 0)
                                            <span class="inline-block px-2 py-0.5 bg-red-100 text-red-700 rounded text-xs font-bold">{{ $stock }}</span>
                                        @elseif ($stock <= $m->stock_minimo)
                                            <span class="inline-block px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded text-xs font-bold">{{ $stock }}</span>
                                        @else
                                            <span class="inline-block px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs font-bold">{{ $stock }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-sm text-gray-500 italic">No hay medicamentos registrados en esta receta.</p>
            @endif

            @if ($consulta->receta_libre)
                <div class="mt-4 p-4 bg-purple-50 border border-purple-200 rounded-lg">
                    <p class="text-xs font-bold text-purple-900 uppercase tracking-wider mb-1">Indicaciones / Receta Libre</p>
                    <p class="text-sm text-gray-700 whitespace-pre-line">{{ $consulta->receta_libre }}</p>
                </div>
            @endif
        </div>

        {{-- Acciones de Dispensación --}}
        @if (!$consulta->dispensada)
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <h3 class="font-semibold text-sm text-gray-800 uppercase tracking-wider mb-3">Acción de Dispensación</h3>
                <form action="{{ route('dispensaciones.dispensar', $consulta) }}" method="POST"
                    onsubmit="return confirm('¿Confirmar dispensación? Se descontará del inventario de forma automática.')"
                    class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-gray-700 uppercase tracking-wider">Notas de dispensación (opcional)</label>
                        <textarea name="notas_dispensacion" rows="2" 
                            class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm focus:border-green-500 focus:ring-green-500"
                            placeholder="Observaciones o comentarios adicionales..."></textarea>
                    </div>
                    <button type="submit"
                        class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-md text-sm font-semibold shadow transition gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                        Dispensar receta y descontar stock
                    </button>
                </form>
            </div>
        @else
            @if (auth()->user()?->hasRole('administrador'))
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-800">Zona Administrativa</p>
                        <p class="text-xs text-gray-500">¿Necesitas anular esta transacción? Los valores del stock serán devueltos.</p>
                    </div>
                    <form action="{{ route('dispensaciones.revertir', $consulta) }}" method="POST"
                        onsubmit="return confirm('¿Estás seguro de revertir la dispensación? El stock recuperará sus cantidades anteriores.')">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 rounded-md text-sm font-medium transition">
                            Revertir dispensación
                        </button>
                    </form>
                </div>
            @endif
        @endif
    </div>
</x-app-layout>