<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-3">
            <div class="min-w-0">
                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight truncate">
                    Detalle de Nota
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Nota de enfermería y signos vitales registrados</p>
            </div>
            <a href="{{ route('enfermeria.notas.index') }}"
                class="bg-white hover:bg-pink-50 text-slate-700 hover:text-pink-600 border border-slate-200 hover:border-pink-200 px-5 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 transition-all active:scale-95 group shrink-0">
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

    <div class="py-8 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-6">

            {{-- ============ ENCABEZADO: FECHA + ESTADO ============ --}}
            <div
                class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3 border-b border-slate-100 pb-5">
                <div>
                    <p class="{{ $labelCls }}">Fecha</p>
                    <p class="text-sm font-extrabold text-slate-800 mt-1">
                        {{ $nota->created_at->format('d/m/Y H:i') }}
                    </p>
                </div>
                @if ($nota->estado_paciente)
                    <span
                        class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide {{ $nota->estado_color }} self-start">
                        {{ ucfirst($nota->estado_paciente) }}
                    </span>
                @endif
            </div>

            {{-- ============ PACIENTE ============ --}}
            <div>
                <p class="{{ $labelCls }}">Paciente</p>
                <p class="text-xs font-bold text-slate-800 mt-1">
                    {{ $nota->paciente?->nombre_completo ?? '—' }}
                </p>
                @if ($nota->cama)
                    <span
                        class="inline-block mt-1.5 px-2.5 py-1 bg-pink-50 text-pink-700 border border-pink-100 rounded-full text-[10px] font-bold uppercase tracking-wide">
                        Cama: {{ $nota->cama->codigo }}
                    </span>
                @endif
            </div>

            {{-- ============ ENFERMERO/A ============ --}}
            <div>
                <p class="{{ $labelCls }}">Enfermero/a</p>
                <p class="text-xs font-bold text-slate-800 mt-1">
                    {{ $nota->user?->nombre_completo ?? '—' }}
                </p>
            </div>

            {{-- ============ CONTENIDO DE LA NOTA ============ --}}
            <div>
                <p class="{{ $labelCls }} mb-2">Nota</p>
                <p
                    class="whitespace-pre-line text-xs text-slate-600 leading-relaxed bg-slate-50 border border-slate-100 p-4 rounded-2xl">
                    {{ $nota->contenido }}
                </p>
            </div>

            {{-- ============ SIGNOS VITALES ============ --}}
            @if ($nota->temperatura || $nota->frecuencia_cardiaca || $nota->presion_arterial)
                <div>
                    <p class="{{ $labelCls }} mb-3">Signos vitales del momento</p>
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                        @if ($nota->temperatura)
                            <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Temp</p>
                                <p class="text-sm font-extrabold text-slate-800 mt-0.5">{{ $nota->temperatura }} °C</p>
                            </div>
                        @endif
                        @if ($nota->frecuencia_cardiaca)
                            <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">FC</p>
                                <p class="text-sm font-extrabold text-slate-800 mt-0.5">
                                    {{ $nota->frecuencia_cardiaca }} lpm</p>
                            </div>
                        @endif
                        @if ($nota->frecuencia_respiratoria)
                            <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">FR</p>
                                <p class="text-sm font-extrabold text-slate-800 mt-0.5">
                                    {{ $nota->frecuencia_respiratoria }} rpm</p>
                            </div>
                        @endif
                        @if ($nota->presion_arterial)
                            <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">TA</p>
                                <p class="text-sm font-extrabold text-slate-800 mt-0.5">{{ $nota->presion_arterial }}
                                </p>
                            </div>
                        @endif
                        @if ($nota->saturacion_oxigeno)
                            <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">SpO₂</p>
                                <p class="text-sm font-extrabold text-slate-800 mt-0.5">
                                    {{ $nota->saturacion_oxigeno }}%</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- ============ ACCIONES ============ --}}
            <div class="pt-5 border-t border-slate-100 flex flex-wrap gap-2">
                <a href="{{ route('enfermeria.notas.index') }}"
                    class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-600 rounded-xl text-xs font-bold transition-all">
                    Volver
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
