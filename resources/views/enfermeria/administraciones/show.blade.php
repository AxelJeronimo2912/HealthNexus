<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">Detalle de Administración</h2>
            <a href="{{ route('enfermeria.administraciones.index') }}" class="text-sm text-gray-600 hover:underline">←
                Volver</a>
        </div>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded-lg shadow space-y-4">

            @if ($administracion->reaccion_adversa)
                <div class="p-3 bg-red-50 border-l-4 border-red-500 text-red-800 rounded">
                    <p class="font-bold">⚠️ Reacción adversa reportada</p>
                </div>
            @endif

            <dl class="grid grid-cols-2 gap-3 text-sm">
                <dt class="text-gray-500">Fecha de administración:</dt>
                <dd class="font-semibold">{{ $administracion->administrado_en->format('d/m/Y H:i') }}</dd>

                <dt class="text-gray-500">Paciente:</dt>
                <dd class="font-semibold">{{ $administracion->paciente?->nombre_completo ?? '—' }}</dd>

                <dt class="text-gray-500">Medicamento:</dt>
                <dd class="font-semibold">
                    {{ $administracion->medicamento?->nombre }}
                    {{ $administracion->medicamento?->concentracion }}
                </dd>

                <dt class="text-gray-500">Dosis:</dt>
                <dd>{{ $administracion->dosis }}</dd>

                <dt class="text-gray-500">Vía:</dt>
                <dd>{{ $administracion->via }}</dd>

                <dt class="text-gray-500">Administrado por:</dt>
                <dd>{{ $administracion->user?->nombre_completo ?? '—' }}</dd>
            </dl>

            @if ($administracion->observaciones)
                <div class="border-t pt-4">
                    <p class="text-xs text-gray-500 mb-1">Observaciones</p>
                    <p class="whitespace-pre-line text-sm bg-gray-50 p-3 rounded border">
                        {{ $administracion->observaciones }}
                    </p>
                </div>
            @endif

            <div class="pt-4 border-t flex space-x-2">
                <a href="{{ route('enfermeria.administraciones.index') }}"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">Volver</a>
                @if (auth()->user()->hasRole('administrador'))
                    <form action="{{ route('enfermeria.administraciones.destroy', $administracion) }}" method="POST"
                        onsubmit="return confirm('¿Eliminar y devolver stock?')">
                        @csrf @method('DELETE')
                        <button class="px-4 py-2 bg-red-600 text-white rounded-md text-sm">Eliminar</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
