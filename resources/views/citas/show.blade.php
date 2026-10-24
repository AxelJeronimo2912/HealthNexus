@php
    use App\Models\SignoVital;
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-xs uppercase tracking-wider text-slate-400 font-bold">Agenda</p>
                <h2 class="font-extrabold text-2xl text-slate-800 tracking-tight">Detalle de la cita</h2>
            </div>
            <a href="{{ route('citas.index') }}"
                class="inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-slate-200 hover:border-accent-300 hover:bg-accent-50 text-sm font-semibold text-accent-700 rounded-xl shadow-sm transition">
                ← Volver a la agenda
            </a>
        </div>
    </x-slot>

    @php
        $signoReciente = SignoVital::ultimoDe($cita->paciente_id);
        $diasMaximos = 7;
        $signoValido = $signoReciente && $signoReciente->created_at->diffInDays(now()) <= $diasMaximos;
        $puedeIniciar = in_array($cita->estado, ['confirmada', 'en_curso']);

        // Estados finales: ya no se puede cambiar el estado
        $estadosFinales = ['atendida', 'cancelada', 'no_asistio'];
        $esFinal = in_array($cita->estado, $estadosFinales);

        // Solo el administrador puede reabrir una cita cerrada
        $esAdmin = auth()->user()->hasRole('administrador');
        $puedeCambiarEstado = !$esFinal || $esAdmin;

        // ── Flujo de estados para el stepper ──
        $flujo = [
            'programada' => ['label' => 'Programada', 'siguiente' => 'confirmada', 'accion' => 'Confirmar cita'],
            'confirmada' => ['label' => 'Confirmada', 'siguiente' => 'en_curso', 'accion' => 'Poner en curso'],
            'en_curso' => ['label' => 'En curso', 'siguiente' => 'atendida', 'accion' => 'Marcar como atendida'],
            'atendida' => ['label' => 'Atendida', 'siguiente' => null, 'accion' => null],
            'cancelada' => ['label' => 'Cancelada', 'siguiente' => null, 'accion' => null],
            'no_asistio' => ['label' => 'No asistió', 'siguiente' => null, 'accion' => null],
        ];

        $pasoActual = $flujo[$cita->estado] ?? null;
        $siguiente = $pasoActual['siguiente'] ?? null;
        $accionPrincipal = $pasoActual['accion'] ?? null;

        // Acciones secundarias: todas menos la actual y la siguiente
        $todasLasAcciones = [
            'programada' => 'Programar',
            'confirmada' => 'Confirmar',
            'en_curso' => 'En curso',
            'atendida' => 'Marcar atendida',
            'no_asistio' => 'No asistió',
            'cancelada' => 'Cancelar',
        ];

        $accionesSecundarias = collect($todasLasAcciones)->reject(
            fn($label, $estado) => $estado === $cita->estado || $estado === $siguiente,
        );

        // Stepper
        $pasos = ['programada', 'confirmada', 'en_curso', 'atendida'];
        $indiceActual = array_search($cita->estado, $pasos);
        $esDescartada = in_array($cita->estado, ['cancelada', 'no_asistio'], true);

        // Triage
        $triage = $cita->triage_al_momento;
        $triageCls = match (strtolower((string) $triage)) {
            'rojo', 'critico', 'crítico' => 'bg-rose-100 text-rose-700 ring-rose-600/20',
            'naranja', 'alto' => 'bg-orange-100 text-orange-700 ring-orange-600/20',
            'amarillo', 'medio' => 'bg-yellow-100 text-yellow-800 ring-yellow-600/20',
            'verde', 'bajo' => 'bg-emerald-100 text-emerald-700 ring-emerald-600/20',
            default => 'bg-slate-100 text-slate-600 ring-slate-500/20',
        };

        $cardCls = 'bg-white rounded-2xl shadow-sm border border-slate-200';
        $tituloCls = 'text-xs font-bold uppercase tracking-wider text-accent-700';
    @endphp

    <div class="w-full px-4 sm:px-6 lg:px-10 py-6 bg-slate-50 min-h-screen space-y-6">


        {{-- ═══════════════ HERO ═══════════════ --}}
        <div class="{{ $cardCls }} overflow-hidden">
            <div class="h-1.5 bg-gradient-to-r from-accent-600 via-brand-600 to-blue-600"></div>
            <div class="p-6 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="flex items-center gap-5">
                    <div
                        class="h-16 w-16 rounded-2xl bg-gradient-to-br from-accent-600 to-brand-600 text-white flex items-center justify-center font-black text-2xl shadow">
                        {{ strtoupper(mb_substr($cita->paciente->nombre_completo, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Paciente</p>
                        <p class="text-2xl font-extrabold text-slate-900 leading-tight">
                            {{ $cita->paciente->nombre_completo }}</p>
                        <div class="flex flex-wrap items-center gap-2 mt-2">
                            <span
                                class="px-3 py-1 rounded-full text-xs font-semibold shadow-sm {{ $cita->estado_color }}">
                                {{ $cita->estado_label }}
                            </span>
                            @if ($triage)
                                <span
                                    class="px-3 py-1 rounded-full text-xs font-semibold ring-1 ring-inset {{ $triageCls }}">
                                    Triage: {{ ucfirst($triage) }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-8 lg:text-right">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Fecha</p>
                        <p class="text-3xl font-black text-slate-900 leading-none mt-1">
                            {{ $cita->fecha_hora->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Hora</p>
                        <p class="text-3xl font-black text-accent-700 leading-none mt-1">
                            {{ $cita->fecha_hora->format('H:i') }}</p>
                        <p class="text-xs text-slate-500 font-medium mt-1">{{ $cita->duracion_minutos }} min</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">

            {{-- ═══════════════ COLUMNA PRINCIPAL ═══════════════ --}}
            <div class="xl:col-span-8 2xl:col-span-9 space-y-6">

                {{-- CONSULTA: iniciar o ver --}}
                @if ($cita->consulta)
                    <div
                        class="bg-gradient-to-r from-slate-900 to-brand-950 p-6 rounded-2xl shadow-md border-l-4 border-accent-500 text-white">
                        <div class="flex items-center justify-between flex-wrap gap-4">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-accent-300">Consulta
                                    registrada</p>
                                <p class="font-bold text-xl mt-1">
                                    {{ $cita->consulta->created_at->format('d/m/Y H:i') }}</p>
                                <p class="text-xs text-slate-300 mt-2">
                                    Estado:
                                    <span
                                        class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $cita->consulta->estado === 'finalizada' ? 'bg-emerald-500/20 text-emerald-300' : 'bg-amber-500/20 text-amber-300' }}">
                                        {{ ucfirst($cita->consulta->estado) }}
                                    </span>
                                </p>
                            </div>
                            <div class="flex gap-2">
                                <a href="{{ route('consultas.show', $cita->consulta) }}"
                                    class="px-5 py-2.5 bg-accent-600 hover:bg-accent-500 text-white rounded-xl text-sm font-semibold transition-all shadow-sm">
                                    Ver consulta
                                </a>
                                @if ($cita->consulta->estado === 'borrador')
                                    <a href="{{ route('consultas.edit', $cita->consulta) }}"
                                        class="px-5 py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-950 rounded-xl text-sm font-semibold transition-all shadow-sm">
                                        Continuar consulta
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @elseif ($puedeIniciar && $signoValido)
                    <div
                        class="bg-gradient-to-r from-emerald-900/90 to-slate-900 p-6 rounded-2xl shadow-md border-l-4 border-emerald-400 text-white">
                        <div class="flex items-center justify-between flex-wrap gap-4">
                            <p class="text-sm text-emerald-100 max-w-2xl">
                                Signos vitales registrados el <strong
                                    class="text-white">{{ $signoReciente->created_at->format('d/m/Y H:i') }}</strong>.
                                El paciente está listo para iniciar la atención médica.
                            </p>
                            <a href="{{ route('consultas.iniciar', $cita) }}"
                                class="inline-flex items-center px-6 py-3 bg-emerald-500 hover:bg-emerald-400 text-slate-950 rounded-xl text-sm font-bold transition-all shadow-md">
                                Iniciar consulta (SOAP) →
                            </a>
                        </div>
                    </div>
                @elseif ($puedeIniciar && !$signoValido)
                    <div
                        class="bg-white p-6 rounded-2xl shadow-sm border border-amber-200 border-l-4 border-l-amber-500">
                        <div class="flex items-center justify-between flex-wrap gap-4">
                            <div>
                                <p class="text-sm font-bold text-amber-800 mb-1">⚠️ Signos vitales requeridos</p>
                                <p class="text-sm text-slate-600 max-w-2xl">
                                    @if (!$signoReciente)
                                        El paciente no cuenta con registro previo de signos vitales.
                                    @else
                                        Los signos vitales exceden el límite permitido de {{ $diasMaximos }} días.
                                    @endif
                                    Es obligatorio registrarlos antes de iniciar la consulta SOAP.
                                </p>
                            </div>
                            <a href="{{ route('signos-vitales.create', ['paciente_id' => $cita->paciente_id, 'cita_id' => $cita->id]) }}"
                                class="inline-block px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-sm font-semibold transition-all shadow-sm">
                                Registrar signos vitales
                            </a>
                        </div>
                    </div>
                @endif

                {{-- ACCIONES: cambiar estado --}}
                @if ($puedeCambiarEstado)
                    <div class="{{ $cardCls }} overflow-hidden">

                        {{-- Stepper --}}
                        <div class="px-6 pt-6 pb-5 border-b border-slate-100">
                            <div
                                class="flex items-center justify-between text-xs font-semibold uppercase tracking-wider">
                                @foreach ($pasos as $i => $paso)
                                    @php
                                        $completado = !$esDescartada && $indiceActual !== false && $i < $indiceActual;
                                        $activo = !$esDescartada && $i === $indiceActual;
                                    @endphp
                                    <div class="{{ $loop->last ? '' : 'flex-1' }} flex items-center">
                                        <div class="flex flex-col items-center gap-1.5">
                                            <div
                                                class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold
                                                {{ $completado ? 'bg-emerald-500 text-white' : ($activo ? 'bg-accent-600 text-white ring-4 ring-accent-100' : 'bg-slate-100 text-slate-400') }}">
                                                @if ($completado)
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5"
                                                        fill="none" viewBox="0 0 24 24" stroke-width="3"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M4.5 12.75l6 6 9-13.5" />
                                                    </svg>
                                                @else
                                                    {{ $i + 1 }}
                                                @endif
                                            </div>
                                            <span
                                                class="{{ $activo ? 'text-accent-700' : ($completado ? 'text-emerald-600' : 'text-slate-400') }}">
                                                {{ $flujo[$paso]['label'] }}
                                            </span>
                                        </div>
                                        @if (!$loop->last)
                                            <div
                                                class="flex-1 h-0.5 mx-3 mb-5 rounded {{ $completado ? 'bg-emerald-400' : 'bg-slate-100' }}">
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            @if ($esDescartada)
                                <div class="mt-4 flex items-center gap-2 text-xs">
                                    <span
                                        class="px-2 py-0.5 rounded-full font-semibold {{ $cita->estado === 'cancelada' ? 'bg-rose-100 text-rose-700' : 'bg-slate-200 text-slate-600' }}">
                                        {{ $cita->estado_label }}
                                    </span>
                                    <span class="text-slate-500">— flujo finalizado</span>
                                </div>
                            @endif
                        </div>

                        {{-- Acción principal --}}
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Siguiente acción
                                </h3>
                                @if ($esFinal && $esAdmin)
                                    <span
                                        class="text-xs font-bold uppercase tracking-wider text-amber-600 bg-amber-50 px-2 py-1 rounded-full">
                                        Modo admin · cita cerrada
                                    </span>
                                @endif
                            </div>

                            @if ($siguiente && $accionPrincipal)
                                @php
                                    $estilosPrincipal = match ($siguiente) {
                                        'confirmada' => 'bg-blue-600 hover:bg-blue-500 text-white',
                                        'en_curso' => 'bg-amber-500 hover:bg-amber-400 text-slate-950',
                                        'atendida' => 'bg-emerald-600 hover:bg-emerald-500 text-white',
                                        default => 'bg-accent-600 hover:bg-accent-500 text-white',
                                    };
                                @endphp
                                <form action="{{ route('citas.cambiar-estado', $cita) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="estado" value="{{ $siguiente }}">
                                    <button
                                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-3 rounded-xl text-sm font-bold shadow-sm transition-all {{ $estilosPrincipal }}">
                                        {{ $accionPrincipal }}
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                            viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                        </svg>
                                    </button>
                                </form>
                            @else
                                <p class="text-sm text-slate-500">No hay una siguiente acción natural para esta cita.
                                </p>
                            @endif

                            {{-- Acciones secundarias --}}
                            @if ($accionesSecundarias->isNotEmpty())
                                <div class="mt-6 pt-5 border-t border-slate-100">
                                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Otras
                                        acciones</p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($accionesSecundarias as $estado => $label)
                                            @php
                                                $esNegativo = in_array($estado, ['cancelada', 'no_asistio'], true);
                                                $clase = $esNegativo
                                                    ? 'border-rose-200 text-rose-700 hover:bg-rose-50 hover:border-rose-300'
                                                    : 'border-slate-200 text-slate-700 hover:bg-accent-50 hover:border-accent-200 hover:text-accent-700';
                                            @endphp
                                            <form action="{{ route('citas.cambiar-estado', $cita) }}" method="POST"
                                                @if ($esNegativo) onsubmit="return confirm('¿Confirmas cambiar el estado a «{{ $label }}»?')" @endif
                                                class="inline">
                                                @csrf
                                                <input type="hidden" name="estado" value="{{ $estado }}">
                                                <button
                                                    class="px-3.5 py-2 text-xs font-semibold rounded-lg border bg-white transition-all {{ $clase }}">
                                                    {{ $label }}
                                                </button>
                                            </form>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="bg-slate-100 border border-slate-200 p-5 rounded-2xl flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-slate-500" fill="none"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-700">Cita cerrada ({{ $cita->estado_label }})</p>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Esta cita ya no puede cambiar de estado.
                                @if (!$esAdmin)
                                    Solo un administrador puede reabrirla.
                                @endif
                            </p>
                        </div>
                    </div>
                @endif

                {{-- Motivo y notas --}}
                @if ($cita->motivo || $cita->notas)
                    <div class="grid grid-cols-1 {{ $cita->motivo && $cita->notas ? 'lg:grid-cols-2' : '' }} gap-6">
                        @if ($cita->motivo)
                            <div class="{{ $cardCls }} p-6">
                                <h4 class="{{ $tituloCls }} mb-2">Motivo de la cita</h4>
                                <p class="text-sm text-slate-700 whitespace-pre-line leading-relaxed">
                                    {{ $cita->motivo }}</p>
                            </div>
                        @endif
                        @if ($cita->notas)
                            <div class="{{ $cardCls }} p-6">
                                <h4 class="{{ $tituloCls }} mb-2">Notas adicionales</h4>
                                <p class="text-sm text-slate-700 whitespace-pre-line leading-relaxed">
                                    {{ $cita->notas }}</p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            {{-- ═══════════════ COLUMNA LATERAL ═══════════════ --}}
            <aside class="xl:col-span-4 2xl:col-span-3 space-y-6 self-start xl:sticky xl:top-4">

                {{-- Paciente --}}
                <div class="{{ $cardCls }} p-6">
                    <h3 class="{{ $tituloCls }} mb-4">Datos del paciente</h3>
                    <dl class="space-y-4 text-sm">
                        <div>
                            <dt class="text-xs text-slate-400">Nombre</dt>
                            <dd class="font-bold text-slate-900 mt-0.5">{{ $cita->paciente->nombre_completo }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-slate-400">CURP</dt>
                            <dd class="font-mono text-xs font-semibold text-slate-700 mt-0.5">
                                {{ $cita->paciente->curp ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-slate-400">Teléfono</dt>
                            <dd class="font-medium text-slate-700 mt-0.5">
                                {{ $cita->paciente->telefono_principal ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-slate-400">Triage al agendar</dt>
                            <dd class="font-semibold text-slate-800 mt-0.5">{{ $triage ? ucfirst($triage) : '—' }}
                            </dd>
                        </div>
                    </dl>
                </div>

                {{-- Médico --}}
                <div class="{{ $cardCls }} p-6">
                    <h3 class="{{ $tituloCls }} mb-4">Médico asignado</h3>
                    <p class="text-lg font-bold text-slate-900">{{ $cita->medico->nombre_completo }}</p>
                    @if ($cita->especialidad)
                        <p class="text-sm text-slate-500 mt-1">
                            Especialidad: <strong class="text-slate-700">{{ $cita->especialidad->nombre }}</strong>
                        </p>
                    @endif
                    @if ($cita->turno)
                        <p class="text-xs text-slate-500 mt-2 pt-3 border-t border-slate-100">
                            Turno: <span class="font-medium text-slate-700">{{ $cita->turno->nombre }}</span>
                            ({{ $cita->turno->rango }})
                        </p>
                    @endif
                </div>

                {{-- Signos vitales (resumen de estado) --}}
                <div class="{{ $cardCls }} p-6">
                    <h3 class="{{ $tituloCls }} mb-3">Signos vitales</h3>
                    @if ($signoValido)
                        <p class="text-sm text-emerald-700 font-medium flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span> Vigentes
                        </p>
                        <p class="text-xs text-slate-500 mt-1">Registrados el
                            {{ $signoReciente->created_at->format('d/m/Y H:i') }}</p>
                    @else
                        <p class="text-sm text-amber-700 font-medium flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                            {{ $signoReciente ? 'Vencidos' : 'Sin registro' }}
                        </p>
                        <p class="text-xs text-slate-500 mt-1">Vigencia máxima: {{ $diasMaximos }} días</p>
                    @endif
                </div>

                {{-- Zona de administración --}}
                @if ($esAdmin)
                    <div
                        class="bg-rose-50/60 p-5 rounded-2xl border border-rose-100 flex items-center justify-between gap-3">
                        <span class="text-xs text-rose-700 font-semibold">Zona de administración</span>
                        <form action="{{ route('citas.destroy', $cita) }}" method="POST"
                            onsubmit="return confirm('¿Confirma que desea eliminar esta cita de forma permanente?')">
                            @csrf @method('DELETE')
                            <button
                                class="px-3 py-1.5 text-xs font-bold text-rose-600 bg-white border border-rose-200 hover:bg-rose-600 hover:text-white rounded-lg transition">
                                Eliminar cita
                            </button>
                        </form>
                    </div>
                @endif
            </aside>
        </div>
    </div>
</x-app-layout>
