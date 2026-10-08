<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-3">
            <div class="min-w-0">
                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight truncate">
                    Detalle del Registro
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Signos vitales, triage y asignación de cama</p>
            </div>
            <a href="{{ route('signos-vitales.index') }}"
                class="bg-white hover:bg-indigo-50 text-slate-700 hover:text-indigo-600 border border-slate-200 hover:border-indigo-200 px-5 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 transition-all active:scale-95 group shrink-0">
                <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
                Volver
            </a>
        </div>
    </x-slot>

    @php
        $labelCls = 'text-[11px] font-bold text-slate-400 uppercase tracking-wider';
        $inputCls =
            'w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-indigo-500 focus:ring-0 outline-none';
    @endphp

    <div class="py-8 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        @if (session('success'))
            <div
                class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-2xl text-xs font-semibold flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div
                class="p-4 bg-rose-50 border border-rose-100 text-rose-800 rounded-2xl text-xs font-semibold flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- Triage destacado --}}
        <div class="p-6 rounded-3xl border-2 {{ $signoVital->triage_color }}">
            <p class="{{ $labelCls }}">Clasificación de triage</p>
            <p class="text-4xl font-black tracking-tight mt-1">{{ $signoVital->triage_label }}</p>
            <p class="text-xs font-medium mt-2 opacity-80">{{ $signoVital->triage_descripcion }}</p>
            @if ($signoVital->triage_manual)
                <span
                    class="inline-block mt-3 px-2.5 py-1 bg-slate-800 text-white rounded-full text-[10px] font-bold uppercase tracking-wide">
                    Manual
                </span>
            @endif
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-6">

            {{-- Encabezado --}}
            <div class="border-b border-slate-100 pb-5">
                <p class="{{ $labelCls }}">Paciente</p>
                <p class="text-3xl font-black text-slate-800 tracking-tight mt-0.5">
                    {{ $signoVital->paciente->nombre_completo }}
                </p>
                <p class="text-[11px] font-medium text-slate-400 mt-1">
                    Registrado el {{ $signoVital->created_at->format('d/m/Y H:i') }}
                    por {{ $signoVital->user->name ?? '—' }}
                </p>
            </div>

            {{-- Signos vitales --}}
            <div>
                <p class="{{ $labelCls }} mb-3">Signos vitales</p>
                <dl class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                        <dt class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Temperatura</dt>
                        <dd class="text-sm font-extrabold text-slate-800 mt-0.5">
                            {{ $signoVital->temperatura ? $signoVital->temperatura . ' °C' : '—' }}
                        </dd>
                    </div>
                    <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                        <dt class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Frec. cardíaca</dt>
                        <dd class="text-sm font-extrabold text-slate-800 mt-0.5">
                            {{ $signoVital->frecuencia_cardiaca ? $signoVital->frecuencia_cardiaca . ' lpm' : '—' }}
                        </dd>
                    </div>
                    <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                        <dt class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Frec. respiratoria
                        </dt>
                        <dd class="text-sm font-extrabold text-slate-800 mt-0.5">
                            {{ $signoVital->frecuencia_respiratoria ? $signoVital->frecuencia_respiratoria . ' rpm' : '—' }}
                        </dd>
                    </div>
                    <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                        <dt class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Presión arterial</dt>
                        <dd class="text-sm font-extrabold text-slate-800 mt-0.5">
                            {{ $signoVital->presion_arterial ?? '—' }}
                        </dd>
                    </div>
                    <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                        <dt class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Saturación O₂</dt>
                        <dd class="text-sm font-extrabold text-slate-800 mt-0.5">
                            {{ $signoVital->saturacion_oxigeno ? $signoVital->saturacion_oxigeno . ' %' : '—' }}
                        </dd>
                    </div>
                    <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                        <dt class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Glucosa</dt>
                        <dd class="text-sm font-extrabold text-slate-800 mt-0.5">
                            {{ $signoVital->glucosa ? $signoVital->glucosa . ' mg/dL' : '—' }}
                        </dd>
                    </div>
                    <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                        <dt class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Peso</dt>
                        <dd class="text-sm font-extrabold text-slate-800 mt-0.5">
                            {{ $signoVital->peso ? $signoVital->peso . ' kg' : '—' }}
                        </dd>
                    </div>
                    <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                        <dt class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Talla</dt>
                        <dd class="text-sm font-extrabold text-slate-800 mt-0.5">
                            {{ $signoVital->talla ? $signoVital->talla . ' m' : '—' }}
                        </dd>
                    </div>
                    <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                        <dt class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">IMC</dt>
                        <dd class="text-sm font-extrabold text-slate-800 mt-0.5">
                            {{ $signoVital->imc ?? '—' }}
                        </dd>
                    </div>
                    <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3 sm:col-span-3">
                        <dt class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Escala de dolor</dt>
                        <dd class="text-sm font-extrabold text-slate-800 mt-0.5">
                            {{ $signoVital->escala_dolor !== null ? $signoVital->escala_dolor . ' / 10' : '—' }}
                        </dd>
                    </div>
                </dl>
            </div>

            @if ($signoVital->motivo_consulta)
                <div class="border-t border-slate-100 pt-5">
                    <p class="{{ $labelCls }} mb-2">Motivo de consulta</p>
                    <p
                        class="text-xs text-slate-600 leading-relaxed whitespace-pre-line bg-slate-50 border border-slate-100 rounded-2xl p-4">
                        {{ $signoVital->motivo_consulta }}
                    </p>
                </div>
            @endif

            @if ($signoVital->notas)
                <div class="border-t border-slate-100 pt-5">
                    <p class="{{ $labelCls }} mb-2">Notas</p>
                    <p
                        class="text-xs text-slate-600 leading-relaxed whitespace-pre-line bg-slate-50 border border-slate-100 rounded-2xl p-4">
                        {{ $signoVital->notas }}
                    </p>
                </div>
            @endif

            {{-- Cama asignada --}}
            <div class="border-t border-slate-100 pt-5">
                <p class="{{ $labelCls }} mb-3">Cama asignada</p>
                @php $asignacion = $signoVital->asignaciones()->where('activa', true)->first(); @endphp
                @if ($asignacion)
                    <div class="bg-indigo-50/60 border border-indigo-100 rounded-2xl p-4">
                        <p class="text-xs font-extrabold text-slate-800">
                            {{ $asignacion->cama->codigo }}
                            <span class="text-slate-400 font-medium">— {{ $asignacion->cama->area }} / Hab.
                                {{ $asignacion->cama->habitacion }}</span>
                        </p>
                        <p class="text-[11px] font-medium text-slate-400 mt-1">
                            Ingreso: {{ $asignacion->fecha_ingreso->format('d/m/Y H:i') }}
                        </p>
                        <form action="{{ route('signos-vitales.liberar-cama', $signoVital) }}" method="POST"
                            onsubmit="return confirm('¿Liberar la cama?')" class="mt-3">
                            @csrf
                            <button
                                class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-[11px] font-bold transition-all">
                                Liberar cama
                            </button>
                        </form>
                    </div>
                @else
                    <p class="text-xs font-semibold text-slate-400 mb-3">Sin cama asignada.</p>

                    @if (in_array($signoVital->triage, ['rojo', 'naranja']))
                        <button type="button" onclick="abrirModalCama()"
                            class="px-4 py-2.5 bg-rose-600 hover:bg-rose-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all">
                            Asignar cama urgente
                        </button>
                    @endif
                @endif
            </div>

            {{-- Acciones --}}
            <div class="pt-5 border-t border-slate-100 flex flex-wrap gap-2">
                <a href="{{ route('signos-vitales.edit', $signoVital) }}"
                    class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Editar
                </a>
                <a href="{{ route('signos-vitales.index') }}"
                    class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-600 rounded-xl text-xs font-bold transition-all">
                    Volver
                </a>
            </div>
        </div>
    </div>

    {{-- Modal de asignación de cama --}}
    <div id="modal-cama"
        class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div
            class="bg-white rounded-3xl shadow-2xl border border-slate-100 max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center px-6 py-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-800 text-base">Asignar cama al paciente</h3>
                        <p class="text-[11px] text-slate-400">Selecciona una cama disponible</p>
                    </div>
                </div>
                <button onclick="cerrarModalCama()"
                    class="text-slate-400 hover:text-slate-700 text-xl leading-none">✕</button>
            </div>

            <form action="{{ route('signos-vitales.asignar-cama', $signoVital) }}" method="POST"
                class="p-6 space-y-5">
                @csrf

                <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Triage</p>
                    <p class="text-sm font-extrabold text-slate-800 mt-0.5">{{ $signoVital->triage_label }}</p>
                </div>

                @if ($camasDisponibles->isEmpty())
                    <div class="p-4 bg-rose-50 border border-rose-100 text-rose-800 rounded-2xl text-xs font-semibold">
                        No hay camas disponibles en este momento.
                    </div>
                @else
                    <div>
                        <label class="{{ $labelCls }}">Selecciona una cama disponible *</label>
                        <select name="cama_id" required class="{{ $inputCls }}">
                            <option value="">— Selecciona —</option>
                            @foreach ($camasDisponibles as $c)
                                <option value="{{ $c->id }}">
                                    {{ $c->codigo }} — {{ $c->area }} / Hab. {{ $c->habitacion }}
                                    ({{ $c->tipo_label }})
                                    @if ($c->oxigeno)
                                        | O₂
                                    @endif
                                    @if ($c->monitor)
                                        | Monitor
                                    @endif
                                    @if ($c->ventilador)
                                        | Ventilador
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex justify-end gap-2 pt-4 border-t border-slate-100">
                        <button type="button" onclick="cerrarModalCama()"
                            class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-600 rounded-xl text-xs font-bold transition-all">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all">
                            Asignar cama
                        </button>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <script>
        function abrirModalCama() {
            document.getElementById('modal-cama').classList.remove('hidden');
        }

        function cerrarModalCama() {
            document.getElementById('modal-cama').classList.add('hidden');
        }

        @if (session('sugerir_cama') && $camasDisponibles->isNotEmpty())
            abrirModalCama();
        @endif
    </script>
</x-app-layout>
