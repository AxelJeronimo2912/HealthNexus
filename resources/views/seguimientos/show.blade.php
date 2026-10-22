<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-3">
            <div class="min-w-0">
                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight truncate">
                    Seguimiento — {{ $paciente->nombre_completo }}
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Historial clínico y evolución del paciente</p>
            </div>
            <a href="{{ route('seguimientos.index') }}"
                class="bg-white hover:bg-brand-50 text-slate-700 hover:text-brand-600 border border-slate-200 hover:border-brand-200 px-5 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 transition-all active:scale-95 group shrink-0">
                <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
                Volver
            </a>
        </div>
    </x-slot>

    @php
        $labelCls = 'text-xs font-bold text-slate-400 uppercase tracking-wider';
    @endphp

    <div class="py-8 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">


        {{-- ============ FICHA DEL PACIENTE ============ --}}
        <div class="bg-white p-6 rounded-2xl ring-1 ring-slate-200/70 shadow-card space-y-5">
            <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-5">
                <div class="flex-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        @if ($paciente->ultimoSignoVital)
                            <span
                                class="px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wide {{ $paciente->ultimoSignoVital->triage_color }}">
                                {{ $paciente->ultimoSignoVital->triage_label }}
                            </span>
                        @endif
                    </div>

                    <p class="{{ $labelCls }} mt-4">Paciente</p>
                    <p class="text-3xl font-black text-slate-800 tracking-tight mt-0.5">
                        {{ $paciente->nombre_completo }}
                    </p>
                    <p class="text-xs text-slate-400 mt-1">
                        {{ $paciente->edad }} años — {{ ucfirst($paciente->sexo) }}
                    </p>
                </div>

                <div class="md:text-right space-y-2 md:max-w-[220px]">
                    @if ($camaActual)
                        <div class="bg-brand-50/60 border border-brand-100 rounded-2xl p-3 md:text-right">
                            <p class="{{ $labelCls }}">Cama actual</p>
                            <p class="text-sm font-extrabold text-brand-600 mt-0.5">
                                {{ $camaActual->cama->codigo }}
                            </p>
                            <p class="text-xs font-medium text-slate-400">{{ $camaActual->cama->area }}</p>
                        </div>
                    @else
                        <div class="bg-slate-50 border border-slate-100 rounded-2xl p-3 md:text-right">
                            <p class="{{ $labelCls }}">Cama actual</p>
                            <p class="text-xs font-bold text-slate-500 mt-0.5">Sin cama</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Alergias y crónicas --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-4 border-t border-slate-100">
                <div class="bg-rose-50/60 border border-rose-100 rounded-2xl p-4">
                    <div class="flex items-center gap-2">
                        <div
                            class="w-7 h-7 rounded-lg bg-rose-100 flex items-center justify-center text-rose-600 shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <p class="text-xs font-bold text-rose-700 uppercase tracking-wider">Alergias</p>
                    </div>
                    <p class="text-xs font-bold text-slate-800 mt-2">{{ $paciente->alergias ?? 'Ninguna' }}</p>
                </div>
                <div class="bg-amber-50/60 border border-amber-100 rounded-2xl p-4">
                    <div class="flex items-center gap-2">
                        <div
                            class="w-7 h-7 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600 shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <p class="text-xs font-bold text-amber-700 uppercase tracking-wider">Enfermedades crónicas
                        </p>
                    </div>
                    <p class="text-xs font-bold text-slate-800 mt-2">
                        {{ $paciente->enfermedades_cronicas ?? 'Ninguna' }}</p>
                </div>
            </div>

            {{-- Acciones --}}
            <div class="pt-5 border-t border-slate-100 flex flex-wrap gap-2">

                {{-- 🔴 LÓGICA DE NUEVO SEGUIMIENTO (BLOQUEADO SI NO HAY CAMA) --}}
                @if ($camaActual)
                    <button type="button"
                        onclick="abrirModalSeguimiento('{{ route('seguimientos.create', $paciente) }}')"
                        class="px-4 py-2.5 bg-brand-600 hover:bg-brand-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all inline-flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M12 4v16m8-8H4" />
                        </svg>
                        Nuevo seguimiento
                    </button>
                @else
                    <div class="px-4 py-2.5 bg-slate-50 border border-slate-200 text-slate-400 rounded-xl text-xs font-bold inline-flex items-center gap-1.5 cursor-not-allowed"
                        title="El paciente ya fue dado de alta. No se pueden agregar más seguimientos.">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Sin cama activa (Alta)
                    </div>
                @endif

                <button type="button" onclick="abrirTimeline('{{ route('seguimientos.timeline', $paciente) }}')"
                    class="px-4 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 active:scale-95 text-slate-700 rounded-xl text-xs font-bold shadow-sm transition-all inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Ver Línea de Tiempo
                </button>

                {{-- 🔴 BOTÓN DE ALTA MÉDICA (SOLO SI HAY CAMA) --}}
                @if ($camaActual)
                    <form action="{{ route('seguimientos.alta', $paciente) }}" method="POST"
                        onsubmit="return confirm('¿Estás seguro de dar de alta al paciente? Se liberará la cama y saldrá de la lista de seguimiento.')">
                        @csrf
                        <button type="submit"
                            class="px-4 py-2.5 bg-rose-600 hover:bg-rose-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Dar de Alta
                        </button>
                    </form>
                @endif

                <a href="{{ route('expedientes.show', $paciente) }}"
                    class="px-4 py-2.5 bg-slate-800 hover:bg-slate-900 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Ver expediente completo
                </a>
            </div>
        </div>

        {{-- ============ TIMELINE CLÍNICO LOCAL (SOLO SEGUIMIENTOS) ============ --}}
        <div class="bg-white rounded-2xl ring-1 ring-slate-200/70 shadow-card overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="font-extrabold text-slate-800 text-base">Historial de seguimientos</h3>
                <p class="text-xs text-slate-400">{{ $seguimientos->count() }} registros clínicos</p>
            </div>

            @if ($seguimientos->isEmpty())
                <div class="px-5 py-12 text-center">
                    <p class="text-xs font-semibold text-slate-400">Sin seguimientos registrados.</p>
                </div>
            @else
                <div class="p-5 space-y-4">
                    @foreach ($seguimientos as $seg)
                        <div
                            class="relative bg-slate-50/70 border border-slate-100 rounded-2xl p-5 hover:border-brand-200 hover:bg-brand-50/20 transition-all shadow-sm">

                            <div class="flex flex-wrap justify-between items-start gap-3 mb-3">
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap mb-1">
                                        <span
                                            class="px-2.5 py-1 rounded-md text-xs font-bold uppercase tracking-wider
                                            @if ($seg->tipo === 'evolucion') bg-brand-100 text-brand-700
                                            @elseif($seg->tipo === 'nota_enfermeria') bg-emerald-100 text-emerald-700
                                            @elseif($seg->tipo === 'alta') bg-rose-100 text-rose-700
                                            @else bg-amber-100 text-amber-700 @endif">
                                            {{ $seg->tipo_label }}
                                        </span>

                                        @if ($seg->estado_paciente)
                                            <span
                                                class="px-2.5 py-1 rounded-md text-xs font-bold uppercase tracking-wider border {{ $seg->estado_color }}">
                                                {{ ucfirst($seg->estado_paciente) }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-xs font-bold text-slate-800">
                                        Por: {{ $seg->user?->nombre_completo ?? 'Sistema' }}
                                    </p>
                                </div>

                                <div class="text-right">
                                    <p class="text-xs font-extrabold text-slate-700">
                                        {{ $seg->created_at->format('d M, Y') }}</p>
                                    <p class="text-xs font-bold text-slate-400 uppercase">
                                        {{ $seg->created_at->format('h:i A') }}</p>
                                </div>
                            </div>

                            {{-- Signos vitales con comparativa --}}
                            @if ($seg->temperatura || $seg->frecuencia_cardiaca)
                                <div class="flex flex-wrap gap-2 mb-4">
                                    @if ($seg->temperatura)
                                        <div
                                            class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg flex items-center gap-2 shadow-sm">
                                            <span class="text-xs font-bold text-slate-400 uppercase">Temp</span>
                                            <span
                                                class="text-xs font-extrabold text-slate-700">{{ $seg->temperatura }}
                                                °C</span>
                                            @if (isset($seg->temp_diff) && $seg->temp_diff != 0)
                                                <span
                                                    class="text-xs font-bold {{ $seg->temp_diff > 0 ? 'text-rose-500' : 'text-emerald-500' }} flex items-center">
                                                    {{ $seg->temp_diff > 0 ? '↑' : '↓' }}
                                                    {{ number_format(abs($seg->temp_diff), 1) }}
                                                </span>
                                            @endif
                                        </div>
                                    @endif

                                    @if ($seg->frecuencia_cardiaca)
                                        <div
                                            class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg flex items-center gap-2 shadow-sm">
                                            <span class="text-xs font-bold text-slate-400 uppercase">FC</span>
                                            <span
                                                class="text-xs font-extrabold text-slate-700">{{ $seg->frecuencia_cardiaca }}
                                                lpm</span>
                                            @if (isset($seg->fc_diff) && $seg->fc_diff != 0)
                                                <span
                                                    class="text-xs font-bold {{ $seg->fc_diff > 0 ? 'text-rose-500' : 'text-emerald-500' }} flex items-center">
                                                    {{ $seg->fc_diff > 0 ? '↑' : '↓' }} {{ abs($seg->fc_diff) }}
                                                </span>
                                            @endif
                                        </div>
                                    @endif

                                    @if ($seg->presion_arterial)
                                        <div
                                            class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg flex items-center gap-2 shadow-sm">
                                            <span class="text-xs font-bold text-slate-400 uppercase">TA</span>
                                            <span
                                                class="text-xs font-extrabold text-slate-700">{{ $seg->presion_arterial }}</span>
                                        </div>
                                    @endif

                                    @if ($seg->saturacion_oxigeno)
                                        <div
                                            class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg flex items-center gap-2 shadow-sm">
                                            <span class="text-xs font-bold text-slate-400 uppercase">SpO₂</span>
                                            <span
                                                class="text-xs font-extrabold text-slate-700">{{ $seg->saturacion_oxigeno }}%</span>
                                            @if (isset($seg->sat_diff) && $seg->sat_diff != 0)
                                                <span
                                                    class="text-xs font-bold {{ $seg->sat_diff < 0 ? 'text-rose-500' : 'text-emerald-500' }} flex items-center">
                                                    {{ $seg->sat_diff < 0 ? '↓' : '↑' }} {{ abs($seg->sat_diff) }}%
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endif

                            {{-- Contenido --}}
                            <div
                                class="bg-white rounded-xl p-4 border border-slate-100 text-xs text-slate-600 leading-relaxed whitespace-pre-line">
                                {{ $seg->contenido }}
                            </div>

                            {{-- Footer --}}
                            <div class="mt-3 flex justify-between items-center pt-3 border-t border-slate-200/50">
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                                    @if ($seg->cama)
                                        Ubicación: {{ $seg->cama->codigo }}
                                    @else
                                        Sin cama
                                    @endif
                                </p>

                                <form action="{{ route('seguimientos.destroy', $seg) }}" method="POST"
                                    onsubmit="return confirm('¿Eliminar este registro clínico?')">
                                    @csrf @method('DELETE')
                                    <button
                                        class="text-xs font-bold text-rose-400 hover:text-rose-600 transition-colors inline-flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- MODAL DE NUEVO SEGUIMIENTO --}}
    <template x-teleport="body">
        <div id="modal-seguimiento" x-data="{ open: false }" x-show="open" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
            <div @click.away="open = false"
                class="bg-white rounded-2xl max-w-3xl w-full p-6 shadow-xl border border-slate-100 max-h-[90vh] overflow-y-auto relative">
                <div id="modal-seguimiento-content">
                    <div class="p-10 text-center text-slate-400">Cargando formulario...</div>
                </div>
            </div>
        </div>
    </template>

    {{-- MODAL DE LÍNEA DE TIEMPO GLOBAL --}}
    <template x-teleport="body">
        <div id="modal-timeline" x-data="{ open: false }" x-show="open" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
            <div @click.away="open = false"
                class="bg-white rounded-2xl max-w-3xl w-full shadow-xl border border-slate-100 max-h-[90vh] overflow-y-auto relative custom-scrollbar">
                <div id="modal-timeline-content">
                    <div class="p-10 text-center text-slate-400">Cargando línea de tiempo...</div>
                </div>
            </div>
        </div>
    </template>

    <script>
        function abrirModalSeguimiento(url) {
            const modalElement = document.getElementById('modal-seguimiento');
            Alpine.$data(modalElement).open = true;
            const contenedor = document.getElementById('modal-seguimiento-content');
            contenedor.innerHTML =
                '<div class="p-10 text-center text-slate-400"><svg class="animate-spin h-6 w-6 mx-auto text-brand-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></div>';
            fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.text())
                .then(html => contenedor.innerHTML = html);
        }

        function abrirTimeline(url) {
            const modalElement = document.getElementById('modal-timeline');
            Alpine.$data(modalElement).open = true;
            const contenedor = document.getElementById('modal-timeline-content');
            contenedor.innerHTML =
                '<div class="p-10 text-center text-slate-400"><svg class="animate-spin h-6 w-6 mx-auto text-brand-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></div>';
            fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.text())
                .then(html => contenedor.innerHTML = html);
        }
    </script>
</x-app-layout>
