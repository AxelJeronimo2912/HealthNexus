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
    @endphp

    <div class="py-8 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

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

        {{-- ============ FICHA DEL PACIENTE ============ --}}
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-5">
            <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-5">
                <div class="flex-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        @if ($paciente->ultimoSignoVital)
                            <span
                                class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide {{ $paciente->ultimoSignoVital->triage_color }}">
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
                        <div class="bg-indigo-50/60 border border-indigo-100 rounded-2xl p-3 md:text-right">
                            <p class="{{ $labelCls }}">Cama actual</p>
                            <p class="text-sm font-extrabold text-indigo-600 mt-0.5">
                                {{ $camaActual->cama->codigo }}
                            </p>
                            <p class="text-[10px] font-medium text-slate-400">{{ $camaActual->cama->area }}</p>
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
                        <p class="text-[11px] font-bold text-rose-700 uppercase tracking-wider">Alergias</p>
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
                        <p class="text-[11px] font-bold text-amber-700 uppercase tracking-wider">Enfermedades crónicas
                        </p>
                    </div>
                    <p class="text-xs font-bold text-slate-800 mt-2">
                        {{ $paciente->enfermedades_cronicas ?? 'Ninguna' }}</p>
                </div>
            </div>

            {{-- Acciones --}}
            <div class="pt-5 border-t border-slate-100 flex flex-wrap gap-2">
                <a href="{{ route('seguimientos.create', $paciente) }}"
                    class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    Nuevo seguimiento
                </a>
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

        {{-- ============ TIMELINE ============ --}}
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="font-extrabold text-slate-800 text-base">Historial de seguimientos</h3>
                <p class="text-[11px] text-slate-400">{{ $seguimientos->count() }} registros clínicos</p>
            </div>

            @if ($seguimientos->isEmpty())
                <div class="px-5 py-12 text-center">
                    <p class="text-xs font-semibold text-slate-400">Sin seguimientos registrados.</p>
                </div>
            @else
                <div class="p-5 space-y-4">
                    @foreach ($seguimientos as $seg)
                        <div
                            class="relative bg-slate-50/70 border border-slate-100 rounded-2xl p-4 hover:border-indigo-200 hover:bg-indigo-50/20 transition-all">
                            {{-- Barra izquierda de color --}}
                            <span class="absolute left-0 top-4 bottom-4 w-1 rounded-r-full bg-indigo-500"></span>

                            <div class="pl-4">
                                <div class="flex flex-wrap justify-between items-start gap-3">
                                    <div class="min-w-0">
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                            {{ $seg->created_at->format('d/m/Y H:i') }}
                                        </p>
                                        <div class="flex items-center gap-2 flex-wrap mt-1">
                                            <p class="text-xs font-extrabold text-slate-800">{{ $seg->tipo_label }}
                                            </p>
                                            @if ($seg->estado_paciente)
                                                <span
                                                    class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide {{ $seg->estado_color }}">
                                                    {{ ucfirst($seg->estado_paciente) }}
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-[11px] font-medium text-slate-400 mt-1">
                                            Por {{ $seg->user?->nombre_completo ?? 'Sistema' }}
                                            @if ($seg->cama)
                                                <span class="text-slate-300">·</span> Cama {{ $seg->cama->codigo }}
                                            @endif
                                        </p>
                                    </div>

                                    <form action="{{ route('seguimientos.destroy', $seg) }}" method="POST"
                                        onsubmit="return confirm('¿Eliminar este seguimiento?')">
                                        @csrf @method('DELETE')
                                        <button
                                            class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-[11px] font-bold transition-all">
                                            Eliminar
                                        </button>
                                    </form>
                                </div>

                                <p
                                    class="text-xs text-slate-600 leading-relaxed whitespace-pre-line mt-3 bg-white border border-slate-100 rounded-xl p-3">
                                    {{ $seg->contenido }}
                                </p>

                                {{-- Signos vitales --}}
                                @if ($seg->temperatura || $seg->frecuencia_cardiaca || $seg->presion_arterial)
                                    <div class="mt-3 grid grid-cols-2 md:grid-cols-5 gap-2">
                                        @if ($seg->temperatura)
                                            <div class="bg-white border border-slate-100 rounded-xl p-2 text-center">
                                                <p
                                                    class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">
                                                    Temp</p>
                                                <p class="text-xs font-extrabold text-slate-800 mt-0.5">
                                                    {{ $seg->temperatura }} °C</p>
                                            </div>
                                        @endif
                                        @if ($seg->frecuencia_cardiaca)
                                            <div class="bg-white border border-slate-100 rounded-xl p-2 text-center">
                                                <p
                                                    class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">
                                                    FC</p>
                                                <p class="text-xs font-extrabold text-slate-800 mt-0.5">
                                                    {{ $seg->frecuencia_cardiaca }} lpm</p>
                                            </div>
                                        @endif
                                        @if ($seg->frecuencia_respiratoria)
                                            <div class="bg-white border border-slate-100 rounded-xl p-2 text-center">
                                                <p
                                                    class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">
                                                    FR</p>
                                                <p class="text-xs font-extrabold text-slate-800 mt-0.5">
                                                    {{ $seg->frecuencia_respiratoria }} rpm</p>
                                            </div>
                                        @endif
                                        @if ($seg->presion_arterial)
                                            <div class="bg-white border border-slate-100 rounded-xl p-2 text-center">
                                                <p
                                                    class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">
                                                    TA</p>
                                                <p class="text-xs font-extrabold text-slate-800 mt-0.5">
                                                    {{ $seg->presion_arterial }}</p>
                                            </div>
                                        @endif
                                        @if ($seg->saturacion_oxigeno)
                                            <div class="bg-white border border-slate-100 rounded-xl p-2 text-center">
                                                <p
                                                    class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">
                                                    SpO₂</p>
                                                <p class="text-xs font-extrabold text-slate-800 mt-0.5">
                                                    {{ $seg->saturacion_oxigeno }}%</p>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
