<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">Detalle de Nota</h2>
            <a href="{{ route('enfermeria.notas.index') }}" class="text-sm text-gray-600 hover:underline">
                ← Volver
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded-lg shadow space-y-4">
            <div class="flex justify-between items-start border-b pb-3">
                <div>
                    <p class="text-xs text-gray-500">Fecha</p>
                    <p class="font-semibold">{{ $nota->created_at->format('d/m/Y H:i') }}</p>
                </div>
                @if ($nota->estado_paciente)
                    <span class="px-3 py-1 rounded text-sm {{ $nota->estado_color }}">
                        {{ ucfirst($nota->estado_paciente) }}
                    </span>
                @endif
            </div>

            <div>
                <p class="text-xs text-gray-500">Paciente</p>
                <p class="font-semibold">{{ $nota->paciente?->nombre_completo ?? '—' }}</p>
                @if ($nota->cama)
                    <p class="text-xs text-gray-500 mt-1">Cama: {{ $nota->cama->codigo }}</p>
                @endif
            </div>

            <div>
                <p class="text-xs text-gray-500">Enfermero/a</p>
                <p class="font-semibold">{{ $nota->user?->nombre_completo ?? '—' }}</p>
            </div>

            <div>
                <p class="text-xs text-gray-500 mb-1">Nota</p>
                <p class="whitespace-pre-line text-sm bg-gray-50 p-3 rounded border">{{ $nota->contenido }}</p>
            </div>

            @if ($nota->temperatura || $nota->frecuencia_cardiaca || $nota->presion_arterial)
                <div>
                    <p class="text-xs text-gray-500 mb-2">Signos vitales del momento</p>
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 text-sm">
                        @if ($nota->temperatura)
                            <div><strong>Temp:</strong> {{ $nota->temperatura }} °C</div>
                        @endif
                        @if ($nota->frecuencia_cardiaca)
                            <div><strong>FC:</strong> {{ $nota->frecuencia_cardiaca }} lpm</div>
                        @endif
                        @if ($nota->frecuencia_respiratoria)
                            <div><strong>FR:</strong> {{ $nota->frecuencia_respiratoria }} rpm</div>
                        @endif
                        @if ($nota->presion_arterial)
                            <div><strong>TA:</strong> {{ $nota->presion_arterial }}</div>
                        @endif
                        @if ($nota->saturacion_oxigeno)
                            <div><strong>SpO₂:</strong> {{ $nota->saturacion_oxigeno }}%</div>
                        @endif
                    </div>
                </div>
            @endif

            <div class="pt-4 border-t flex space-x-2">
                <a href="{{ route('enfermeria.notas.index') }}"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">Volver</a>
            </div>
        </div>
    </div>
</x-app-layout>
