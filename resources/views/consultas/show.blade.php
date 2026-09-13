<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">Consulta — {{ $consulta->paciente->nombre_completo }}</h2>
            <div class="space-x-2">
                <a href="{{ route('consultas.pdf', $consulta) }}" target="_blank"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md text-sm">PDF consulta</a>
                @if ($consulta->tiene_receta)
                    <a href="{{ route('consultas.receta.pdf', $consulta) }}" target="_blank"
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md text-sm">PDF receta</a>
                @endif
                @if ($consulta->estado === 'borrador')
                    <a href="{{ route('consultas.edit', $consulta) }}"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm">Editar</a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

        @if (session('success'))
            <div class="p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
        @endif

        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex justify-between items-center mb-4">
                <p class="text-sm text-gray-500">
                    Fecha: {{ $consulta->created_at->format('d/m/Y H:i') }}
                </p>
                <span
                    class="px-3 py-1 rounded text-sm {{ $consulta->estado === 'finalizada' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                    {{ ucfirst($consulta->estado) }}
                </span>
            </div>

            <dl class="grid grid-cols-2 gap-3 text-sm">
                <dt class="font-semibold">Médico:</dt>
                <dd>{{ $consulta->medico->nombre_completo }}</dd>
                <dt class="font-semibold">Diagnóstico principal:</dt>
                <dd>{{ $consulta->diagnosticoPrincipal?->nombre ?? '—' }}</dd>
                <dt class="font-semibold">Diagnóstico secundario:</dt>
                <dd>{{ $consulta->diagnosticoSecundario?->nombre ?? '—' }}</dd>
            </dl>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="font-bold text-gray-800 mb-3">SOAP</h3>
            <div class="space-y-3 text-sm">
                <div><strong>S — Subjetivo:</strong>
                    <p class="whitespace-pre-line">{{ $consulta->subjetivo ?? '—' }}</p>
                </div>
                <div><strong>O — Objetivo:</strong>
                    <p class="whitespace-pre-line">{{ $consulta->objetivo ?? '—' }}</p>
                </div>
                <div><strong>A — Análisis:</strong>
                    <p class="whitespace-pre-line">{{ $consulta->analisis ?? '—' }}</p>
                </div>
                <div><strong>P — Plan:</strong>
                    <p class="whitespace-pre-line">{{ $consulta->plan ?? '—' }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="font-bold text-gray-800 mb-3">Signos vitales y somatometría</h3>
            <dl class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm">
                <dt class="font-semibold">Temperatura:</dt>
                <dd>{{ $consulta->temperatura ?? '—' }} °C</dd>
                <dt class="font-semibold">Frec. cardíaca:</dt>
                <dd>{{ $consulta->frecuencia_cardiaca ?? '—' }} lpm</dd>
                <dt class="font-semibold">Frec. respiratoria:</dt>
                <dd>{{ $consulta->frecuencia_respiratoria ?? '—' }} rpm</dd>
                <dt class="font-semibold">Presión arterial:</dt>
                <dd>{{ $consulta->presion_arterial ?? '—' }}</dd>
                <dt class="font-semibold">Saturación O₂:</dt>
                <dd>{{ $consulta->saturacion_oxigeno ?? '—' }} %</dd>
                <dt class="font-semibold">Glucosa:</dt>
                <dd>{{ $consulta->glucosa ?? '—' }} mg/dL</dd>
                <dt class="font-semibold">Peso:</dt>
                <dd>{{ $consulta->peso ?? '—' }} kg</dd>
                <dt class="font-semibold">Talla:</dt>
                <dd>{{ $consulta->talla ?? '—' }} m</dd>
                <dt class="font-semibold">IMC:</dt>
                <dd>{{ $consulta->imc ?? '—' }}</dd>
                <dt class="font-semibold">Perímetro abdominal:</dt>
                <dd>{{ $consulta->perimetro_abdominal ?? '—' }} cm</dd>
            </dl>
        </div>

        @if ($consulta->medicamentos->count() || $consulta->receta_libre)
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="font-bold text-gray-800">Receta</h3>
                    @if ($consulta->tiene_receta)
                        <a href="{{ route('consultas.receta.pdf', $consulta) }}" target="_blank"
                            class="text-sm bg-green-100 hover:bg-green-200 text-green-800 px-3 py-1 rounded">
                            Ver receta PDF
                        </a>
                    @endif
                </div>

                @if ($consulta->medicamentos->count())
                    <table class="min-w-full text-sm border">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left">Medicamento</th>
                                <th class="px-3 py-2 text-left">Dosis</th>
                                <th class="px-3 py-2 text-left">Vía</th>
                                <th class="px-3 py-2 text-left">Frecuencia</th>
                                <th class="px-3 py-2 text-left">Duración</th>
                                <th class="px-3 py-2 text-left">Indicaciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($consulta->medicamentos as $m)
                                <tr>
                                    <td class="px-3 py-2">{{ $m->nombre }} {{ $m->concentracion }}</td>
                                    <td class="px-3 py-2">{{ $m->pivot->dosis }}</td>
                                    <td class="px-3 py-2">{{ $m->pivot->via }}</td>
                                    <td class="px-3 py-2">{{ $m->pivot->frecuencia }}</td>
                                    <td class="px-3 py-2">{{ $m->pivot->duracion }}</td>
                                    <td class="px-3 py-2">{{ $m->pivot->indicaciones }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

                @if ($consulta->receta_libre)
                    <div class="mt-4">
                        <strong class="text-sm">Receta libre:</strong>
                        <p class="whitespace-pre-line text-sm">{{ $consulta->receta_libre }}</p>
                    </div>
                @endif
            </div>
        @endif
    </div>
</x-app-layout>
