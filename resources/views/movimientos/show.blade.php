<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">Detalle del Movimiento</h2>
            <a href="{{ route('movimientos.index') }}" class="text-sm text-gray-600 hover:underline">
                ← Volver
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded-lg shadow space-y-6">

            {{-- Encabezado --}}
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Movimiento #{{ $movimiento->id }}</p>
                    <p class="text-lg font-bold">
                        <span class="px-2 py-1 rounded text-sm {{ $movimiento->tipo_color }}">
                            {{ $movimiento->tipo_label }}
                        </span>
                    </p>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ $movimiento->created_at->format('d/m/Y H:i:s') }}
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-500 uppercase">Cantidad</p>
                    <p class="text-3xl font-bold {{ $movimiento->cantidad >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $movimiento->cantidad >= 0 ? '+' : '' }}{{ $movimiento->cantidad }}
                    </p>
                </div>
            </div>

            {{-- Medicamento --}}
            <div class="border-t pt-4">
                <h3 class="font-semibold text-sm text-gray-700 mb-2">Medicamento</h3>
                <p class="text-lg font-bold">
                    {{ $movimiento->medicamento?->nombre }}
                    {{ $movimiento->medicamento?->concentracion }}
                </p>
                <p class="text-sm text-gray-500">
                    {{ $movimiento->medicamento?->sustancia_activa ?? '—' }}
                </p>
                @if ($movimiento->medicamento)
                    <a href="{{ route('existencias.show', $movimiento->medicamento) }}"
                        class="text-xs text-blue-600 hover:underline mt-1 inline-block">
                        Ver existencias →
                    </a>
                @endif
            </div>

            {{-- Lote --}}
            @if ($movimiento->lote)
                <div class="border-t pt-4">
                    <h3 class="font-semibold text-sm text-gray-700 mb-2">Lote</h3>
                    <dl class="grid grid-cols-2 gap-2 text-sm">
                        <dt class="text-gray-500">Código:</dt>
                        <dd class="font-mono">{{ $movimiento->lote->codigo_lote ?? 'LOTE-' . $movimiento->lote->id }}
                        </dd>
                        <dt class="text-gray-500">Caducidad:</dt>
                        <dd>{{ $movimiento->lote->fecha_caducidad->format('d/m/Y') }}</dd>
                        <dt class="text-gray-500">Estado:</dt>
                        <dd>
                            <span class="px-2 py-0.5 rounded text-xs {{ $movimiento->lote->estado_color }}">
                                {{ $movimiento->lote->estado_label }}
                            </span>
                        </dd>
                    </dl>
                </div>
            @endif

            {{-- Stock --}}
            <div class="border-t pt-4">
                <h3 class="font-semibold text-sm text-gray-700 mb-2">Cambio de stock</h3>
                <div class="flex items-center gap-4 text-lg">
                    <span class="text-gray-500">{{ $movimiento->stock_anterior }}</span>
                    <span class="text-gray-400">→</span>
                    <span class="font-bold">{{ $movimiento->stock_nuevo }}</span>
                    <span class="text-sm {{ $movimiento->cantidad >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        ({{ $movimiento->cantidad >= 0 ? '+' : '' }}{{ $movimiento->cantidad }})
                    </span>
                </div>
            </div>

            {{-- Motivo --}}
            @if ($movimiento->motivo)
                <div class="border-t pt-4">
                    <h3 class="font-semibold text-sm text-gray-700 mb-2">Motivo</h3>
                    <p class="text-sm text-gray-700 whitespace-pre-line">{{ $movimiento->motivo }}</p>
                </div>
            @endif

            {{-- Referencia --}}
            @if ($movimiento->referencia_tipo && $movimiento->referencia_id)
                <div class="border-t pt-4">
                    <h3 class="font-semibold text-sm text-gray-700 mb-2">Referencia</h3>
                    <p class="text-sm">
                        Tipo: <strong>{{ ucfirst($movimiento->referencia_tipo) }}</strong>
                        @if ($movimiento->referencia_tipo === 'consulta')
                            <a href="{{ route('consultas.show', $movimiento->referencia_id) }}"
                                class="text-blue-600 hover:underline ml-2">
                                Ver consulta →
                            </a>
                        @endif
                    </p>
                </div>
            @endif

            {{-- Usuario --}}
            <div class="border-t pt-4">
                <h3 class="font-semibold text-sm text-gray-700 mb-2">Usuario</h3>
                <p class="text-sm">{{ $movimiento->user?->nombre_completo ?? 'Sistema (automático)' }}</p>
            </div>

            <div class="pt-4 border-t flex space-x-2">
                <a href="{{ route('movimientos.index') }}"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">Volver</a>
                @if ($movimiento->medicamento)
                    <a href="{{ route('existencias.show', $movimiento->medicamento) }}"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm">
                        Ver existencias
                    </a>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
