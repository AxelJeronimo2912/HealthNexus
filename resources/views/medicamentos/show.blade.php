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
