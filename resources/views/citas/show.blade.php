@php
    use App\Models\SignoVital;
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-slate-800 tracking-tight">Detalle de la Cita</h2>
            <a href="{{ route('citas.index') }}" class="text-sm font-medium text-purple-600 hover:text-purple-800 transition-colors flex items-center gap-1">
                ← Volver a la agenda
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
            <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded-r-lg shadow-sm font-medium text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 rounded-r-lg shadow-sm font-medium text-sm">
                {{ session('error') }}
            </div>
        @endif

        {{-- Estado --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Estado actual</p>
                    <span class="inline-block mt-2 px-3 py-1 rounded-full text-xs font-semibold shadow-sm {{ $cita->estado_color }}">
                        {{ $cita->estado_label }}
                    </span>
                </div>
                <div class="text-right">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Fecha y hora</p>
                    <p class="text-2xl font-black text-slate-900 mt-1">{{ $cita->fecha_hora->format('d/m/Y H:i') }}</p>
                    <p class="text-xs text-purple-600 font-medium">{{ $cita->duracion_minutos }} minutos programados</p>
                </div>
            </div>
        </div>

        {{-- Paciente --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <h3 class="text-xs font-bold uppercase tracking-wider text-purple-700 mb-3">Paciente</h3>
            <p class="text-xl font-bold text-slate-900">{{ $cita->paciente->nombre_completo }}</p>
            <dl class="grid grid-cols-2 gap-4 text-sm mt-4 pt-4 border-t border-slate-100">
                <div>
                    <dt class="text-xs text-slate-400">CURP:</dt>
                    <dd class="font-medium text-slate-700 mt-0.5">{{ $cita->paciente->curp ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400">Teléfono:</dt>
                    <dd class="font-medium text-slate-700 mt-0.5">{{ $cita->paciente->telefono_principal ?? '—' }}</dd>
                </div>
                <div class="col-span-2">
                    <dt class="text-xs text-slate-400">Triage al agendar:</dt>
                    <dd class="font-semibold text-slate-800 mt-0.5">{{ $cita->triage_al_momento ? ucfirst($cita->triage_al_momento) : '—' }}</dd>
                </div>
            </dl>
        </div>

        {{-- Médico --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <h3 class="text-xs font-bold uppercase tracking-wider text-purple-700 mb-3">Médico asignado</h3>
            <p class="text-lg font-bold text-slate-900">{{ $cita->medico->nombre_completo }}</p>
            @if ($cita->turno)
                <p class="text-xs text-slate-500 mt-1">
                    Turno: <span class="font-medium text-slate-700">{{ $cita->turno->nombre }}</span> ({{ $cita->turno->rango }})
                </p>
            @endif
        </div>

        {{-- CONSULTA: iniciar o ver --}}
        @if ($cita->consulta)
            {{-- Ya existe consulta --}}
            <div class="bg-gradient-to-r from-slate-900 to-indigo-950 p-6 rounded-2xl shadow-md border-l-4 border-purple-500 text-white">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-purple-300">Consulta registrada</p>
                        <p class="font-bold text-lg mt-1 text-white">
                            {{ $cita->consulta->created_at->format('d/m/Y H:i') }}
                        </p>
                        <p class="text-xs text-slate-300 mt-1">
                            Estado:
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $cita->consulta->estado === 'finalizada' ? 'bg-emerald-500/20 text-emerald-300' : 'bg-amber-500/20 text-amber-300' }}">
                                {{ ucfirst($cita->consulta->estado) }}
                            </span>
                        </p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('consultas.show', $cita->consulta) }}"
                            class="px-4 py-2 bg-purple-600 hover:bg-purple-500 text-white rounded-xl text-sm font-semibold transition-all shadow-sm">
                            Ver consulta
                        </a>
                        @if ($cita->consulta->estado === 'borrador')
                            <a href="{{ route('consultas.edit', $cita->consulta) }}"
                                class="px-4 py-2 bg-amber-500 hover:bg-amber-400 text-slate-950 rounded-xl text-sm font-semibold transition-all shadow-sm">
                                Continuar consulta
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @elseif ($puedeIniciar && $signoValido)
            {{-- Todo listo para iniciar --}}
            <div class="bg-gradient-to-r from-emerald-900/90 to-slate-900 p-6 rounded-2xl shadow-md border-l-4 border-emerald-400 text-white">
                <p class="text-sm text-emerald-100 mb-4">
                    Signos vitales registrados el <strong class="text-white">{{ $signoReciente->created_at->format('d/m/Y H:i') }}</strong>.
                    El paciente está listo para iniciar la atención médica.
                </p>
                <a href="{{ route('consultas.iniciar', $cita) }}"
                    class="inline-flex items-center px-5 py-2.5 bg-emerald-500 hover:bg-emerald-400 text-slate-950 rounded-xl text-sm font-bold transition-all shadow-md">
                    Iniciar consulta (SOAP)
                </a>
            </div>
        @elseif ($puedeIniciar && !$signoValido)
            {{-- Faltan signos vitales --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-amber-500 border-y border-r border-slate-100">
                <p class="text-sm font-bold text-amber-800 mb-1 flex items-center gap-2">
                    ⚠️ Signos vitales requeridos
                </p>
                <p class="text-sm text-slate-600 mb-4">
                    @if (!$signoReciente)
                        El paciente no cuenta con registro previo de signos vitales.
                    @else
                        Los signos vitales exceden el límite permitido de {{ $diasMaximos }} días.
                    @endif
                    Es obligatorio registrarlos antes de iniciar la consulta SOAP.
                </p>
                <a href="{{ route('signos-vitales.create', ['paciente_id' => $cita->paciente_id, 'cita_id' => $cita->id]) }}"
                    class="inline-block px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-sm font-semibold transition-all shadow-sm">
                    Registrar signos vitales
                </a>
            </div>
        @endif

        {{-- Motivo y notas --}}
        @if ($cita->motivo || $cita->notas)
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 space-y-4">
                @if ($cita->motivo)
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-purple-700 mb-1">Motivo</h4>
                        <p class="text-sm text-slate-700 whitespace-pre-line leading-relaxed">{{ $cita->motivo }}</p>
                    </div>
                @endif
                @if ($cita->notas)
                    <div class="pt-3 border-t border-slate-100">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-purple-700 mb-1">Notas adicionales</h4>
                        <p class="text-sm text-slate-700 whitespace-pre-line leading-relaxed">{{ $cita->notas }}</p>
                    </div>
                @endif
            </div>
        @endif

        {{-- Acciones: cambiar estado --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4">Actualizar estado</h3>
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
                            <button class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-slate-200 text-slate-700 hover:bg-purple-50 hover:border-purple-200 hover:text-purple-700 transition-all">
                                {{ $label }}
                            </button>
                        </form>
                    @endif
                @endforeach
            </div>
        </div>

        @if (auth()->user()->hasRole('administrador'))
            <div class="bg-rose-50/50 p-6 rounded-2xl border border-rose-100 flex items-center justify-between">
                <span class="text-xs text-rose-700 font-medium">Zona de administración</span>
                <form action="{{ route('citas.destroy', $cita) }}" method="POST"
                    onsubmit="return confirm('¿Confirma que desea eliminar esta cita de forma permanente?')">
                    @csrf @method('DELETE')
                    <button class="text-xs font-bold text-rose-600 hover:text-rose-800 hover:underline">Eliminar cita</button>
                </form>
            </div>
        @endif
    </div>
</x-app-layout>