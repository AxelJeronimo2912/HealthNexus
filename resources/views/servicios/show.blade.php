<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">
                Servicio: {{ $servicio->nombre }}
            </h2>
            <a href="{{ route('servicios.index') }}" class="text-sm text-gray-600 hover:underline">
                ← Volver
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- Encabezado --}}
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs text-gray-500 font-mono">{{ $servicio->codigo }}</p>
                    <p class="text-2xl font-bold">{{ $servicio->nombre }}</p>
                    <span class="inline-block mt-1 px-2 py-1 rounded text-xs {{ $servicio->tipo_color }}">
                        {{ $servicio->tipo_label }}
                    </span>
                </div>
                @if ($servicio->activo)
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded text-sm">Activo</span>
                @else
                    <span class="px-3 py-1 bg-red-100 text-red-800 rounded text-sm">Inactivo</span>
                @endif
            </div>

            @if ($servicio->descripcion)
                <p class="mt-3 text-sm text-gray-700 whitespace-pre-line">{{ $servicio->descripcion }}</p>
            @endif
        </div>

        {{-- Ubicación y horario --}}
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="font-bold text-gray-800 mb-3">Ubicación y horario</h3>
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                <div>
                    <dt class="text-gray-500">Ubicación</dt>
                    <dd>{{ $servicio->ubicacion ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Piso / Ala</dt>
                    <dd>{{ $servicio->piso ?? '—' }} {{ $servicio->ala ? '/ ' . $servicio->ala : '' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Horario</dt>
                    <dd>{{ $servicio->horario }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Extensión telefónica</dt>
                    <dd>{{ $servicio->extension_telefonica ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Capacidad</dt>
                    <dd>{{ $servicio->capacidad ?? '—' }}</dd>
                </div>
            </dl>
        </div>

        {{-- Personal asignado --}}
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex justify-between items-center mb-3">
                <h3 class="font-bold text-gray-800">Personal asignado ({{ $servicio->users->count() }})</h3>
                <a href="{{ route('servicios.personal', $servicio) }}"
                    class="text-xs text-blue-600 hover:underline">Gestionar personal →</a>
            </div>

            @if ($servicio->users->isEmpty())
                <p class="text-sm text-gray-500">Sin personal asignado.</p>
            @else
                <ul class="divide-y">
                    @foreach ($servicio->users as $u)
                        <li class="py-2 flex justify-between text-sm">
                            <span>{{ $u->nombre_completo ?: $u->name }}</span>
                            <span class="text-xs text-gray-500">
                                {{ $u->pivot->rol_en_servicio ?? ($u->getRoleNames()->first() ?? '—') }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        {{-- Camas --}}
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="font-bold text-gray-800 mb-3">Camas ({{ $servicio->camas->count() }})</h3>
            @if ($servicio->camas->isEmpty())
                <p class="text-sm text-gray-500">Sin camas asignadas a este servicio.</p>
            @else
                <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                    @foreach ($servicio->camas as $cama)
                        <a href="{{ route('camas.show', $cama) }}" class="block p-2 border rounded hover:bg-gray-50">
                            <p class="font-mono text-xs">{{ $cama->codigo }}</p>
                            <p class="text-xs text-gray-500">{{ $cama->area }}</p>
                            <span class="inline-block mt-1 px-2 py-0.5 rounded text-xs {{ $cama->estado_color }}">
                                {{ $cama->estado_label }}
                            </span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="pt-4 flex space-x-2">
            <a href="{{ route('servicios.edit', $servicio) }}"
                class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm">Editar</a>
            <a href="{{ route('servicios.personal', $servicio) }}"
                class="px-4 py-2 bg-purple-600 text-white rounded-md text-sm">Gestionar personal</a>
            <a href="{{ route('servicios.index') }}" class="px-4 py-2 bg-gray-100 rounded-md text-sm">Volver</a>
        </div>
    </div>
</x-app-layout>
