<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Detalle del Turno</h2>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded-lg shadow space-y-4">

            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-lg font-bold">{{ $turno->nombre }}</h3>
                    <p class="text-sm text-gray-500 font-mono">{{ $turno->codigo }}</p>
                </div>
                @if ($turno->activo)
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded text-sm">Activo</span>
                @else
                    <span class="px-3 py-1 bg-red-100 text-red-800 rounded text-sm">Inactivo</span>
                @endif
            </div>

            <dl class="grid grid-cols-2 gap-3 text-sm">
                <dt class="font-semibold">Horario:</dt>
                <dd>{{ $turno->rango }}</dd>
                <dt class="font-semibold">Duración:</dt>
                <dd>{{ $turno->duracion_horas }} horas</dd>
                <dt class="font-semibold">Cruza medianoche:</dt>
                <dd>{{ $turno->cruza_medianoche ? 'Sí' : 'No' }}</dd>
                @if ($turno->descripcion)
                    <dt class="font-semibold">Descripción:</dt>
                    <dd>{{ $turno->descripcion }}</dd>
                @endif
            </dl>

            <div>
                <h4 class="font-semibold text-sm text-gray-700 mb-2">
                    Usuarios asignados ({{ $turno->users->count() }})
                </h4>
                @forelse ($turno->users as $u)
                    <div class="text-sm py-1 border-b last:border-0 flex justify-between">
                        <span>{{ $u->nombre_completo ?: $u->name }}</span>
                        <span class="text-xs text-gray-500">
                            {{ $u->getRoleNames()->first() ?? '—' }}
                            @if ($u->pivot->dia_semana !== null)
                                — Día {{ ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'][$u->pivot->dia_semana] }}
                            @endif
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Sin usuarios asignados.</p>
                @endforelse
            </div>

            <div class="pt-4 flex space-x-2">
                <a href="{{ route('admin.turnos.edit', $turno) }}"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm">Editar</a>
                <a href="{{ route('admin.turnos.index') }}" class="px-4 py-2 bg-gray-100 rounded-md text-sm">Volver</a>
            </div>
        </div>
    </div>
</x-app-layout>
