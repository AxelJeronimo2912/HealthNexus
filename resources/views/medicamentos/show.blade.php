<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Detalle del Medicamento</h2>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded-lg shadow space-y-6">

            <div>
                <h3 class="text-lg font-bold">{{ $medicamento->nombre_completo }}</h3>
                <p class="text-sm text-gray-500">{{ $medicamento->sustancia_activa ?? '—' }}</p>
            </div>

            <div>
                <h4 class="font-semibold text-sm text-gray-700 mb-2">Identificación</h4>
                <dl class="grid grid-cols-2 gap-3 text-sm">
                    <dt class="font-semibold">Laboratorio:</dt>
                    <dd>{{ $medicamento->laboratorio ?? '—' }}</dd>
                    <dt class="font-semibold">Presentación:</dt>
                    <dd>{{ $medicamento->presentacion ?? '—' }}</dd>
                    <dt class="font-semibold">Concentración:</dt>
                    <dd>{{ $medicamento->concentracion ?? '—' }}</dd>
                    <dt class="font-semibold">Vía:</dt>
                    <dd>{{ $medicamento->via_administracion ?? '—' }}</dd>
                    <dt class="font-semibold">Grupo terapéutico:</dt>
                    <dd>{{ $medicamento->grupo_terapeutico ?? '—' }}</dd>
                    <dt class="font-semibold">Código de barras:</dt>
                    <dd>{{ $medicamento->codigo_barras ?? '—' }}</dd>
                    <dt class="font-semibold">Registro sanitario:</dt>
                    <dd>{{ $medicamento->registro_sanitario ?? '—' }}</dd>
                </dl>
            </div>

            <div>
                <h4 class="font-semibold text-sm text-gray-700 mb-2">Clasificación</h4>
                <div class="flex flex-wrap gap-2">
                    @if ($medicamento->psicotropico)
                        <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded text-xs">Psicotrópico</span>
                    @endif
                    @if ($medicamento->antibiotico)
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">Antibiótico</span>
                    @endif
                    @if ($medicamento->controlado)
                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs">Controlado</span>
                    @endif
                    @if (!$medicamento->psicotropico && !$medicamento->antibiotico && !$medicamento->controlado)
                        <span class="text-sm text-gray-500">Sin clasificación especial</span>
                    @endif
                </div>
            </div>

            <div>
                <h4 class="font-semibold text-sm text-gray-700 mb-2">Inventario</h4>
                <dl class="grid grid-cols-2 gap-3 text-sm">
                    <dt class="font-semibold">Unidad de medida:</dt>
                    <dd>{{ ucfirst($medicamento->unidad_medida) }}</dd>
                    <dt class="font-semibold">Stock mínimo:</dt>
                    <dd>{{ $medicamento->stock_minimo }}</dd>
                    <dt class="font-semibold">Stock máximo:</dt>
                    <dd>{{ $medicamento->stock_maximo }}</dd>
                    <dt class="font-semibold">Precio compra:</dt>
                    <dd>{{ $medicamento->precio_compra ? '$' . number_format($medicamento->precio_compra, 2) : '—' }}
                    </dd>
                    <dt class="font-semibold">Precio venta:</dt>
                    <dd>{{ $medicamento->precio_venta ? '$' . number_format($medicamento->precio_venta, 2) : '—' }}
                    </dd>
                </dl>
            </div>

            {{-- STOCK --}}
            <div class="bg-white p-6 rounded-lg shadow space-y-4">

                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Stock actual</p>
                        <p
                            class="text-3xl font-bold {{ $medicamento->stock_bajo ? 'text-red-600' : 'text-green-600' }}">
                            {{ $medicamento->stock_actual }} {{ $medicamento->unidad_medida }}
                        </p>
                        <p class="text-xs text-gray-500">
                            Mínimo: {{ $medicamento->stock_minimo }} | Máximo: {{ $medicamento->stock_maximo }}
                        </p>
                        @if ($medicamento->stock_bajo)
                            <p class="text-xs text-red-600 mt-1">⚠️ Stock por debajo del mínimo</p>
                        @endif
                    </div>
                </div>

                {{-- Formulario rápido de entrada --}}
                <form action="{{ route('medicamentos.entrada', $medicamento) }}" method="POST"
                    class="flex gap-2 items-end pt-4 border-t">
                    @csrf
                    <div class="flex-1">
                        <label class="block text-xs text-gray-500">Registrar entrada</label>
                        <input type="number" name="cantidad" min="1" value="10"
                            class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs text-gray-500">Motivo</label>
                        <input type="text" name="motivo" placeholder="Compra, donación..."
                            class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                    </div>
                    <button type="submit"
                        class="px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md text-sm">
                        + Entrada
                    </button>
                </form>

                {{-- Historial de movimientos --}}
                <div>
                    <h4 class="font-semibold text-sm text-gray-700 mb-2">Últimos movimientos</h4>
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-2 py-1 text-left text-xs text-gray-500">Fecha</th>
                                <th class="px-2 py-1 text-left text-xs text-gray-500">Tipo</th>
                                <th class="px-2 py-1 text-right text-xs text-gray-500">Cant.</th>
                                <th class="px-2 py-1 text-right text-xs text-gray-500">Stock</th>
                                <th class="px-2 py-1 text-left text-xs text-gray-500">Motivo</th>
                                <th class="px-2 py-1 text-left text-xs text-gray-500">Usuario</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($medicamento->movimientos()->limit(20)->get() as $mov)
                                <tr class="border-b">
                                    <td class="px-2 py-1">{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-2 py-1">
                                        <span class="px-2 py-0.5 rounded text-xs {{ $mov->tipo_color }}">
                                            {{ $mov->tipo_label }}
                                        </span>
                                    </td>
                                    <td
                                        class="px-2 py-1 text-right {{ $mov->cantidad >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $mov->cantidad >= 0 ? '+' : '' }}{{ $mov->cantidad }}
                                    </td>
                                    <td class="px-2 py-1 text-right">{{ $mov->stock_nuevo }}</td>
                                    <td class="px-2 py-1 text-xs text-gray-500">{{ $mov->motivo ?? '—' }}</td>
                                    <td class="px-2 py-1 text-xs">{{ $mov->user?->nombre_completo ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-2 py-3 text-center text-gray-500 text-xs">Sin
                                        movimientos.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                <h4 class="font-semibold text-sm text-gray-700 mb-2">Estado</h4>
                @if ($medicamento->activo)
                    <span
                        class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 rounded text-sm">Activo</span>
                @else
                    <span
                        class="inline-flex items-center px-3 py-1 bg-red-100 text-red-800 rounded text-sm">Inactivo</span>
                @endif
            </div>

            <div class="pt-4 flex space-x-2">
                <a href="{{ route('medicamentos.edit', $medicamento) }}"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm">Editar</a>
                <a href="{{ route('medicamentos.index') }}" class="px-4 py-2 bg-gray-100 rounded-md text-sm">Volver</a>
            </div>
        </div>
    </div>
</x-app-layout>
