
<div class="p-2">
    <div class="flex justify-between items-center mb-5 border-b border-slate-100 pb-4">
        <div class="min-w-0">
            <h3 class="text-lg font-bold text-slate-800">Detalle de Nota</h3>
            <p class="text-xs text-slate-500 mt-0.5">Nota de enfermería y signos vitales</p>
        </div>
        <button type="button" onclick="Alpine.$data(document.getElementById('modal-nota-show')).open = false"
            class="w-9 h-9 flex items-center justify-center rounded-full bg-slate-50 text-slate-400 hover:text-rose-500 hover:bg-rose-50 transition-colors text-xl leading-none border border-slate-200 shrink-0">&times;</button>
    </div>

    @php
        $labelCls = 'text-[11px] font-bold text-slate-400 uppercase tracking-wider';
    @endphp

    <div class="space-y-6">
        {{-- Fecha + Estado --}}
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3 border-b border-slate-100 pb-5">
            <div>
                <p class="{{ $labelCls }}">Fecha</p>
                <p class="text-sm font-extrabold text-slate-800 mt-1">{{ $nota->created_at->format('d/m/Y H:i') }}</p>
            </div>
            @if ($nota->estado_paciente)
                <span
                    class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide {{ $nota->estado_color }} self-start">
                    {{ ucfirst($nota->estado_paciente) }}
                </span>
            @endif
        </div>

        {{-- Paciente --}}
        <div>
            <p class="{{ $labelCls }}">Paciente</p>
            <p class="text-xs font-bold text-slate-800 mt-1">{{ $nota->paciente?->nombre_completo ?? '—' }}</p>
            @if ($nota->cama)
                <span
                    class="inline-block mt-1.5 px-2.5 py-1 bg-pink-50 text-pink-700 border border-pink-100 rounded-full text-[10px] font-bold uppercase tracking-wide">
                    Cama: {{ $nota->cama->codigo }}
                </span>
            @endif
        </div>

        {{-- Enfermero --}}
        <div>
            <p class="{{ $labelCls }}">Enfermero/a</p>
            <p class="text-xs font-bold text-slate-800 mt-1">{{ $nota->user?->nombre_completo ?? '—' }}</p>
        </div>

        {{-- Contenido --}}
        <div>
            <p class="{{ $labelCls }} mb-2">Nota</p>
            <p
                class="whitespace-pre-line text-xs text-slate-600 leading-relaxed bg-slate-50 border border-slate-100 p-4 rounded-2xl">
                {{ $nota->contenido }}</p>
        </div>

        {{-- Signos --}}
        @if ($nota->temperatura || $nota->frecuencia_cardiaca || $nota->presion_arterial)
            <div>
                <p class="{{ $labelCls }} mb-3">Signos vitales</p>
                <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                    @if ($nota->temperatura)
                        <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3 text-center">
                            <p class="text-[10px] font-bold text-slate-400 uppercase">Temp</p>
                            <p class="text-sm font-extrabold text-slate-800 mt-0.5">{{ $nota->temperatura }}°C</p>
                        </div>
                    @endif
                    @if ($nota->frecuencia_cardiaca)
                        <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3 text-center">
                            <p class="text-[10px] font-bold text-slate-400 uppercase">FC</p>
                            <p class="text-sm font-extrabold text-slate-800 mt-0.5">{{ $nota->frecuencia_cardiaca }}
                                lpm</p>
                        </div>
                    @endif
                    @if ($nota->frecuencia_respiratoria)
                        <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3 text-center">
                            <p class="text-[10px] font-bold text-slate-400 uppercase">FR</p>
                            <p class="text-sm font-extrabold text-slate-800 mt-0.5">
                                {{ $nota->frecuencia_respiratoria }} rpm</p>
                        </div>
                    @endif
                    @if ($nota->presion_arterial)
                        <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3 text-center">
                            <p class="text-[10px] font-bold text-slate-400 uppercase">TA</p>
                            <p class="text-sm font-extrabold text-slate-800 mt-0.5">{{ $nota->presion_arterial }}</p>
                        </div>
                    @endif
                    @if ($nota->saturacion_oxigeno)
                        <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3 text-center">
                            <p class="text-[10px] font-bold text-slate-400 uppercase">SpO₂</p>
                            <p class="text-sm font-extrabold text-slate-800 mt-0.5">{{ $nota->saturacion_oxigeno }}%
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

