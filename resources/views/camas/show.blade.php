<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Detalle de la Cama</h2>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded-lg shadow space-y-6">

            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold">{{ $cama->codigo }}</h3>
                    @if ($cama->nombre)
                        <p class="text-sm text-gray-500">{{ $cama->nombre }}</p>
                    @endif
                </div>
                <span class="px-3 py-1 rounded text-sm {{ $cama->estado_color }}">
                    {{ $cama->estado_label }}
                </span>
            </div>

            <div>
                <h4 class="font-semibold text-sm text-gray-700 mb-2">Ubicación</h4>
                <dl class="grid grid-cols-2 gap-3 text-sm">
                    <dt class="font-semibold">Área:</dt>
                    <dd>{{ $cama->area ?? '—' }}</dd>
                    <dt class="font-semibold">Piso:</dt>
                    <dd>{{ $cama->piso ?? '—' }}</dd>
                    <dt class="font-semibold">Ala:</dt>
                    <dd>{{ $cama->ala ?? '—' }}</dd>
                    <dt class="font-semibold">Habitación:</dt>
                    <dd>{{ $cama->habitacion ?? '—' }}</dd>
                </dl>
            </div>

            <div>
                <h4 class="font-semibold text-sm text-gray-700 mb-2">Tipo</h4>
                <p class="text-sm">{{ $cama->tipo_label }}</p>
            </div>

            <div>
                <h4 class="font-semibold text-sm text-gray-700 mb-2">Equipo</h4>
                <div class="flex flex-wrap gap-2">
                    @if ($cama->oxigeno)
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">Oxígeno</span>
                    @endif
                    @if ($cama->monitor)
                        <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded text-xs">Monitor</span>
                    @endif
                    @if ($cama->ventilador)
                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs">Ventilador</span>
                    @endif
                    @if (!$cama->oxigeno && !$cama->monitor && !$cama->ventilador)
                        <span class="text-sm text-gray-500">Sin equipo especial</span>
                    @endif
                </div>
            </div>

            @if ($cama->notas)
                <div>
                    <h4 class="font-semibold text-sm text-gray-700 mb-2">Notas</h4>
                    <p class="text-sm text-gray-600 whitespace-pre-line">{{ $cama->notas }}</p>
                </div>
            @endif

            <div>
                <h4 class="font-semibold text-sm text-gray-700 mb-2">Estado del registro</h4>
                @if ($cama->activo)
                    <span
                        class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 rounded text-sm">Activa</span>
                @else
                    <span
                        class="inline-flex items-center px-3 py-1 bg-red-100 text-red-800 rounded text-sm">Inactiva</span>
                @endif
            </div>

            <div class="pt-4 flex space-x-2">
                <a href="{{ route('camas.edit', $cama) }}"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm">Editar</a>
                <a href="{{ route('camas.index') }}" class="px-4 py-2 bg-gray-100 rounded-md text-sm">Volver</a>
            </div>
        </div>
    </div>
</x-app-layout>
