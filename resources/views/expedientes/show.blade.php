<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">
                Expediente — {{ $paciente->nombre_completo }}
            </h2>
            <a href="{{ route('expedientes.index') }}" class="text-sm text-gray-600 hover:underline">
                ← Volver
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- Ficha del paciente --}}
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="text-lg font-bold">{{ $paciente->nombre_completo }}</h3>
                    <p class="text-sm text-gray-500">
                        {{ $paciente->edad }} años — {{ ucfirst($paciente->sexo) }}
                    </p>
                </div>
                <a href="{{ route('pacientes.show', $paciente) }}" class="text-sm text-blue-600 hover:underline">Ver
                    ficha completa →</a>
            </div>

            <dl class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm mt-4">
                <div>
                    <dt class="text-gray-500">CURP</dt>
                    <dd class="font-semibold">{{ $paciente->curp ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Tipo sanguíneo</dt>
                    <dd class="font-semibold">{{ $paciente->tipo_sanguineo ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Alergias</dt>
                    <dd class="font-semibold">{{ $paciente->alergias ?? 'Ninguna' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Enf. crónicas</dt>
                    <dd class="font-semibold">{{ $paciente->enfermedades_cronicas ?? 'Ninguna' }}</dd>
                </div>
            </dl>
        </div>

        {{-- Tabs --}}
        <div x-data="{ tab: 'consultas' }">
            <div class="border-b border-gray-200">
                <nav class="flex gap-4">
                    <button @click="tab = 'consultas'"
                        :class="tab === 'consultas' ? 'border-blue-500 text-blue-600' :
                            'border-transparent text-gray-500 hover:text-gray-700'"
                        class="py-2 px-1 border-b-2 font-medium text-sm">
                        Consultas ({{ $consultas->count() }})
                    </button>
                    <button @click="tab = 'signos'"
                        :class="tab === 'signos' ? 'border-blue-500 text-blue-600' :
                            'border-transparent text-gray-500 hover:text-gray-700'"
                        class="py-2 px-1 border-b-2 font-medium text-sm">
                        Signos vitales ({{ $signosVitales->count() }})
                    </button>
                    <button @click="tab = 'citas'"
                        :class="tab === 'citas' ? 'border-blue-500 text-blue-600' :
                            'border-transparent text-gray-500 hover:text-gray-700'"
                        class="py-2 px-1 border-b-2 font-medium text-sm">
                        Citas ({{ $citas->count() }})
                    </button>
                </nav>
            </div>

            {{-- TAB: Consultas --}}
            <div x-show="tab === 'consultas'" class="mt-6 space-y-4">
                @forelse ($consultas as $consulta)
                    <div
                        class="bg-white p-5 rounded-lg shadow border-l-4
                                {{ $consulta->estado === 'finalizada' ? 'border-green-500' : 'border-yellow-500' }}">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs text-gray-500">
                                    {{ $consulta->created_at->format('d/m/Y H:i') }}
                                </p>
                                <p class="font-semibold text-gray-800">
                                    Dr. {{ $consulta->medico?->nombre_completo ?? '—' }}
                                </p>
                                @if ($consulta->diagnosticoPrincipal)
                                    <p class="text-sm text-blue-700 mt-1">
                                        <strong>Dx:</strong> {{ $consulta->diagnosticoPrincipal->etiqueta }}
                                    </p>
                                @elseif ($consulta->analisis)
                                    <p class="text-sm text-gray-700 mt-1">
                                        <strong>Dx:</strong> {{ Str::limit($consulta->analisis, 100) }}
                                    </p>
                                @endif
                            </div>
                            <div class="text-right">
                                <span
                                    class="px-2 py-1 rounded text-xs
                                    {{ $consulta->estado === 'finalizada' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($consulta->estado) }}
                                </span>
                                <div class="mt-2 space-x-2">
                                    <a href="{{ route('consultas.show', $consulta) }}"
                                        class="text-blue-600 hover:underline text-sm">Ver</a>
                                    <a href="{{ route('consultas.pdf', $consulta) }}" target="_blank"
                                        class="text-red-600 hover:underline text-sm">PDF</a>
                                    @if ($consulta->tiene_receta)
                                        <a href="{{ route('consultas.receta.pdf', $consulta) }}" target="_blank"
                                            class="text-green-600 hover:underline text-sm">Receta</a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Resumen SOAP --}}
                        @if ($consulta->subjetivo || $consulta->objetivo || $consulta->plan)
                            <div class="mt-3 grid grid-cols-2 md:grid-cols-4 gap-2 text-xs">
                                @if ($consulta->subjetivo)
                                    <div>
                                        <p class="text-gray-500 uppercase font-semibold">S</p>
                                        <p class="text-gray-700">{{ Str::limit($consulta->subjetivo, 80) }}</p>
                                    </div>
                                @endif
                                @if ($consulta->objetivo)
                                    <div>
                                        <p class="text-gray-500 uppercase font-semibold">O</p>
                                        <p class="text-gray-700">{{ Str::limit($consulta->objetivo, 80) }}</p>
                                    </div>
                                @endif
                                @if ($consulta->analisis)
                                    <div>
                                        <p class="text-gray-500 uppercase font-semibold">A</p>
                                        <p class="text-gray-700">{{ Str::limit($consulta->analisis, 80) }}</p>
                                    </div>
                                @endif
                                @if ($consulta->plan)
                                    <div>
                                        <p class="text-gray-500 uppercase font-semibold">P</p>
                                        <p class="text-gray-700">{{ Str::limit($consulta->plan, 80) }}</p>
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- Medicamentos --}}
                        @if ($consulta->medicamentos->count())
                            <div class="mt-3 pt-3 border-t">
                                <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Receta</p>
                                <ul class="text-xs text-gray-700 space-y-0.5">
                                    @foreach ($consulta->medicamentos as $m)
                                        <li>
                                            • {{ $m->nombre }} {{ $m->concentracion }}
                                            @if ($m->pivot->dosis)
                                                — {{ $m->pivot->dosis }}
                                            @endif
                                            @if ($m->pivot->frecuencia)
                                                — {{ $m->pivot->frecuencia }}
                                            @endif
                                            @if ($m->pivot->duracion)
                                                por {{ $m->pivot->duracion }}
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="bg-white p-6 rounded-lg shadow text-center text-gray-500">
                        Sin consultas registradas.
                    </div>
                @endforelse
            </div>

            {{-- TAB: Signos vitales --}}
            <div x-show="tab === 'signos'" x-cloak class="mt-6">
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <table class="min-w-full text-sm divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs text-gray-500 uppercase">Fecha</th>
                                <th class="px-3 py-2 text-left text-xs text-gray-500 uppercase">Temp</th>
                                <th class="px-3 py-2 text-left text-xs text-gray-500 uppercase">FC</th>
                                <th class="px-3 py-2 text-left text-xs text-gray-500 uppercase">FR</th>
                                <th class="px-3 py-2 text-left text-xs text-gray-500 uppercase">TA</th>
                                <th class="px-3 py-2 text-left text-xs text-gray-500 uppercase">SpO₂</th>
                                <th class="px-3 py-2 text-left text-xs text-gray-500 uppercase">Triage</th>
                                <th class="px-3 py-2 text-right text-xs text-gray-500 uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($signosVitales as $sv)
                                <tr>
                                    <td class="px-3 py-2">{{ $sv->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-3 py-2">{{ $sv->temperatura ?? '—' }}</td>
                                    <td class="px-3 py-2">{{ $sv->frecuencia_cardiaca ?? '—' }}</td>
                                    <td class="px-3 py-2">{{ $sv->frecuencia_respiratoria ?? '—' }}</td>
                                    <td class="px-3 py-2">{{ $sv->presion_arterial ?? '—' }}</td>
                                    <td class="px-3 py-2">{{ $sv->saturacion_oxigeno ?? '—' }}</td>
                                    <td class="px-3 py-2">
                                        <span class="px-2 py-1 rounded text-xs {{ $sv->triage_color }}">
                                            {{ $sv->triage_label }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 text-right">
                                        <a href="{{ route('signos-vitales.show', $sv) }}"
                                            class="text-blue-600 hover:underline text-xs">Ver</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-gray-500 py-6">Sin signos vitales.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- TAB: Citas --}}
            <div x-show="tab === 'citas'" x-cloak class="mt-6">
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <table class="min-w-full text-sm divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs text-gray-500 uppercase">Fecha</th>
                                <th class="px-3 py-2 text-left text-xs text-gray-500 uppercase">Médico</th>
                                <th class="px-3 py-2 text-left text-xs text-gray-500 uppercase">Estado</th>
                                <th class="px-3 py-2 text-right text-xs text-gray-500 uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($citas as $cita)
                                <tr>
                                    <td class="px-3 py-2">{{ $cita->fecha_hora->format('d/m/Y H:i') }}</td>
                                    <td class="px-3 py-2">{{ $cita->medico?->nombre_completo ?? '—' }}</td>
                                    <td class="px-3 py-2">
                                        <span class="px-2 py-1 rounded text-xs {{ $cita->estado_color }}">
                                            {{ $cita->estado_label }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 text-right">
                                        <a href="{{ route('citas.show', $cita) }}"
                                            class="text-blue-600 hover:underline text-xs">Ver</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-gray-500 py-6">Sin citas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
