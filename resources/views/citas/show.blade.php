@php
    use App\Models\SignoVital;
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">Detalle de la Cita</h2>
            <a href="{{ route('citas.index') }}" class="text-sm text-gray-600 hover:underline">
                ← Volver
            </a>
        </div>
    </x-slot>

    @php
        $signoReciente = SignoVital::ultimoDe($cita->paciente_id);
        $diasMaximos = 7;
        $signoValido = $signoReciente && $signoReciente->created_at->diffInDays(now()) <= $diasMaximos;
        $puedeIniciar = in_array($cita->estado, ['confirmada', 'en_curso']);
    @endphp

    <div class="py-8 max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

        @if (session('success'))
            <div class="p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="p-3 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
        @endif

        {{-- Estado --}}
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Estado actual</p>
                    <span class="inline-block mt-1 px-3 py-1 rounded text-sm {{ $cita->estado_color }}">
                        {{ $cita->estado_label }}
                    </span>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">Fecha y hora</p>
                    <p class="text-xl font-bold">{{ $cita->fecha_hora->format('d/m/Y H:i') }}</p>
                    <p class="text-xs text-gray-500">{{ $cita->duracion_minutos }} minutos</p>
                </div>
            </div>
        </div>

        {{-- Paciente --}}
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="font-semibold text-sm text-gray-700 mb-2">Paciente</h3>
            <p class="text-lg font-bold">{{ $cita->paciente->nombre_completo }}</p>
            <dl class="grid grid-cols-2 gap-2 text-sm mt-2">
                <dt class="text-gray-500">CURP:</dt>
                <dd>{{ $cita->paciente->curp ?? '—' }}</dd>
                <dt class="text-gray-500">Teléfono:</dt>
                <dd>{{ $cita->paciente->telefono_principal ?? '—' }}</dd>
                <dt class="text-gray-500">Triage al agendar:</dt>
                <dd>{{ $cita->triage_al_momento ? ucfirst($cita->triage_al_momento) : '—' }}</dd>
            </dl>
        </div>

        {{-- Médico --}}
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="font-semibold text-sm text-gray-700 mb-2">Médico asignado</h3>
            <p class="text-lg font-bold">{{ $cita->medico->nombre_completo }}</p>
            @if ($cita->turno)
                <p class="text-sm text-gray-500 mt-1">
                    Turno: {{ $cita->turno->nombre }} ({{ $cita->turno->rango }})
                </p>
            @endif
        </div>

        {{-- CONSULTA: iniciar o ver --}}
        @if ($cita->consulta)
            {{-- Ya existe consulta --}}
            <div class="bg-white p-6 rounded-lg shadow border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Consulta registrada</p>
                        <p class="font-bold text-gray-800">
                            {{ $cita->consulta->created_at->format('d/m/Y H:i') }}
                        </p>
                        <p class="text-sm text-gray-600">
                            Estado:
                            <span
                                class="px-2 py-0.5 rounded text-xs {{ $cita->consulta->estado === 'finalizada' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($cita->consulta->estado) }}
                            </span>
                        </p>
                    </div>
                    <div class="space-x-2">
                        <a href="{{ route('consultas.show', $cita->consulta) }}"
                            class="inline-block px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium">
                            Ver consulta
                        </a>
                        @if ($cita->consulta->estado === 'borrador')
                            <a href="{{ route('consultas.edit', $cita->consulta) }}"
                                class="inline-block px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-md text-sm font-medium">
                                Continuar consulta
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @elseif ($puedeIniciar && $signoValido)
            {{-- Todo listo para iniciar --}}
            <div class="bg-white p-6 rounded-lg shadow border-l-4 border-green-500">
                <p class="text-sm text-gray-600 mb-3">
                    Signos vitales registrados el {{ $signoReciente->created_at->format('d/m/Y H:i') }}.
                    Puedes iniciar la consulta médica.
                </p>
                <a href="{{ route('consultas.iniciar', $cita) }}"
                    class="inline-block px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md text-sm font-medium">
                    Iniciar consulta (SOAP)
                </a>
            </div>
        @elseif ($puedeIniciar && !$signoValido)
            {{-- Faltan signos vitales --}}
            <div class="bg-white p-6 rounded-lg shadow border-l-4 border-yellow-500">
                <p class="text-sm text-yellow-800 font-semibold mb-1">
                    ⚠️ Signos vitales requeridos
                </p>
                <p class="text-sm text-gray-600 mb-3">
                    @if (!$signoReciente)
                        El paciente no tiene signos vitales registrados.
                    @else
                        Los signos vitales tienen más de {{ $diasMaximos }} días.
                    @endif
                    Debes registrarlos antes de iniciar la consulta.
                </p>
                <a href="{{ route('signos-vitales.create', ['paciente_id' => $cita->paciente_id, 'cita_id' => $cita->id]) }}"
                    class="inline-block px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-md text-sm font-medium">
                    Registrar signos vitales
                </a>
            </div>
        @endif

        {{-- Motivo y notas --}}
        @if ($cita->motivo || $cita->notas)
            <div class="bg-white p-6 rounded-lg shadow space-y-3">
                @if ($cita->motivo)
                    <div>
                        <h4 class="font-semibold text-sm text-gray-700 mb-1">Motivo</h4>
                        <p class="text-sm text-gray-700 whitespace-pre-line">{{ $cita->motivo }}</p>
                    </div>
                @endif
                @if ($cita->notas)
                    <div>
                        <h4 class="font-semibold text-sm text-gray-700 mb-1">Notas</h4>
                        <p class="text-sm text-gray-700 whitespace-pre-line">{{ $cita->notas }}</p>
                    </div>
                @endif
            </div>
        @endif

        {{-- Acciones: cambiar estado --}}
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="font-semibold text-sm text-gray-700 mb-3">Cambiar estado</h3>
            <div class="flex flex-wrap gap-2">
                @foreach ([
        'confirmada' => 'Confirmar',
        'en_curso' => 'En curso',
        'atendida' => 'Marcar atendida',
        'no_asistio' => 'No asistió',
        'cancelada' => 'Cancelar',
    ] as $estado => $label)
                    @if ($cita->estado !== $estado)
                        <form action="{{ route('citas.cambiar-estado', $cita) }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="estado" value="{{ $estado }}">
                            <button class="px-3 py-1.5 text-sm rounded-md border hover:bg-gray-50">
                                {{ $label }}
                            </button>
                        </form>
                    @endif
                @endforeach
            </div>
        </div>

        @if (auth()->user()->hasRole('administrador'))
            <div class="bg-white p-6 rounded-lg shadow">
                <form action="{{ route('citas.destroy', $cita) }}" method="POST"
                    onsubmit="return confirm('¿Eliminar esta cita?')">
                    @csrf @method('DELETE')
                    <button class="text-sm text-red-600 hover:underline">Eliminar cita</button>
                </form>
            </div>
        @endif
    </div>
</x-app-layout>
