<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">
                Seguimiento — {{ $paciente->nombre_completo }}
            </h2>
            <a href="{{ route('seguimientos.index') }}" class="text-sm text-gray-600 hover:underline">
                ← Volver
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

        @if (session('success'))
            <div class="p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
        @endif

        {{-- Ficha --}}
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-lg font-bold">{{ $paciente->nombre_completo }}</h3>
                    <p class="text-sm text-gray-500">
                        {{ $paciente->edad }} años — {{ ucfirst($paciente->sexo) }}
                    </p>
                    @if ($paciente->ultimoSignoVital)
                        <span
                            class="inline-block mt-2 px-2 py-1 rounded text-xs {{ $paciente->ultimoSignoVital->triage_color }}">
                            {{ $paciente->ultimoSignoVital->triage_label }}
                        </span>
                    @endif
                </div>
                <div class="text-right">
                    @if ($camaActual)
                        <p class="text-xs text-gray-500">Cama actual</p>
                        <p class="font-bold text-blue-700">{{ $camaActual->cama->codigo }}</p>
                        <p class="text-xs text-gray-500">{{ $camaActual->cama->area }}</p>
                    @else
                        <p class="text-xs text-gray-400">Sin cama</p>
                    @endif
                </div>
            </div>

            {{-- Alergias y crónicas destacadas --}}
            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3">
                <div class="p-3 bg-red-50 border border-red-200 rounded">
                    <p class="text-xs text-red-800 font-semibold">Alergias</p>
                    <p class="text-sm">{{ $paciente->alergias ?? 'Ninguna' }}</p>
                </div>
                <div class="p-3 bg-yellow-50 border border-yellow-200 rounded">
                    <p class="text-xs text-yellow-800 font-semibold">Enfermedades crónicas</p>
                    <p class="text-sm">{{ $paciente->enfermedades_cronicas ?? 'Ninguna' }}</p>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('seguimientos.create', $paciente) }}"
                    class="inline-block px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md text-sm font-medium">
                    + Nuevo seguimiento
                </a>
                <a href="{{ route('expedientes.show', $paciente) }}"
                    class="inline-block px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm ml-2">
                    Ver expediente completo
                </a>
            </div>
        </div>

        {{-- Timeline de seguimientos --}}
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="font-bold text-gray-800 mb-4">Historial de seguimientos</h3>

            @if ($seguimientos->isEmpty())
                <p class="text-sm text-gray-500 text-center py-6">Sin seguimientos registrados.</p>
            @else
                <div class="space-y-4">
                    @foreach ($seguimientos as $seg)
                        <div class="border-l-4 border-blue-500 pl-4 py-2">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-xs text-gray-500">
                                        {{ $seg->created_at->format('d/m/Y H:i') }}
                                    </p>
                                    <p class="font-semibold text-sm">
                                        {{ $seg->tipo_label }}
                                        @if ($seg->estado_paciente)
                                            <span class="ml-2 px-2 py-0.5 rounded text-xs {{ $seg->estado_color }}">
                                                {{ ucfirst($seg->estado_paciente) }}
                                            </span>
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        Por {{ $seg->user?->nombre_completo ?? 'Sistema' }}
                                        @if ($seg->cama)
                                            — Cama {{ $seg->cama->codigo }}
                                        @endif
                                    </p>
                                </div>
                                <form action="{{ route('seguimientos.destroy', $seg) }}" method="POST"
                                    onsubmit="return confirm('¿Eliminar este seguimiento?')">
                                    @csrf @method('DELETE')
                                    <button class="text-xs text-red-600 hover:underline">Eliminar</button>
                                </form>
                            </div>

                            <p class="text-sm text-gray-700 whitespace-pre-line mt-2">{{ $seg->contenido }}</p>

                            {{-- Signos vitales del momento --}}
                            @if ($seg->temperatura || $seg->frecuencia_cardiaca || $seg->presion_arterial)
                                <div class="mt-2 flex flex-wrap gap-3 text-xs text-gray-600">
                                    @if ($seg->temperatura)
                                        <span>🌡️ {{ $seg->temperatura }} °C</span>
                                    @endif
                                    @if ($seg->frecuencia_cardiaca)
                                        <span>❤️ {{ $seg->frecuencia_cardiaca }} lpm</span>
                                    @endif
                                    @if ($seg->frecuencia_respiratoria)
                                        <span>🫁 {{ $seg->frecuencia_respiratoria }} rpm</span>
                                    @endif
                                    @if ($seg->presion_arterial)
                                        <span>🩸 {{ $seg->presion_arterial }}</span>
                                    @endif
                                    @if ($seg->saturacion_oxigeno)
                                        <span>💨 {{ $seg->saturacion_oxigeno }}%</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
