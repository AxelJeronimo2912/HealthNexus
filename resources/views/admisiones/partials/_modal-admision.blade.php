{{-- Modal de Registro de Admisión --}}
<div id="modal-admision" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="modal-admision-titulo">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full p-6 sm:p-8 max-h-[90vh] overflow-y-auto border border-slate-100">

        <div class="flex justify-between items-start mb-6 border-b border-slate-100 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-600 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <h3 id="modal-admision-titulo" class="font-extrabold text-slate-800 text-lg">
                        Nueva Admisión Hospitalaria
                    </h3>
                    <p class="text-xs text-slate-400">Registro de llegada del paciente al hospital</p>
                </div>
            </div>
            <button type="button" id="btn-cerrar-admision" class="text-slate-400 hover:text-slate-600 text-xl font-bold p-2" aria-label="Cerrar">&times;</button>
        </div>

        @php
            $inputCls = 'w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-brand-500 focus:ring-0 outline-none';
            $labelCls = 'block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5';
        @endphp

        <form action="{{ route('admisiones.store') }}" method="POST" class="space-y-4" id="form-admision-modal">
            @csrf

            {{-- Paciente --}}
            <div>
                <label class="{{ $labelCls }}">Paciente *</label>
                <div class="flex gap-2">
                    <select name="paciente_id" id="modal_paciente_id" required class="{{ $inputCls }} flex-1">
    <option value="">— Selecciona un paciente —</option>
    @foreach (\App\Models\Paciente::with('signosVitales')->get() as $p)
        @php
            $signo = $p->signosVitales->first();
            $triageLabel = match ($signo?->triage) {
                'rojo' => '🔴 Rojo',
                'naranja' => '🟠 Naranja',
                'amarillo' => '🟡 Amarillo',
                'verde' => '🟢 Verde',
                'azul' => '🔵 Azul',
                default => '— Sin triage',
            };
        @endphp
        <option value="{{ $p->id }}" data-triage="{{ $signo?->triage ?? '' }}"
            data-triage-fecha="{{ $signo?->created_at?->format('d/m/Y') ?? '' }}"
            @selected(old('paciente_id') == $p->id)>
            {{ $p->nombre_completo }} — {{ $triageLabel }}
            @if ($signo)
                ({{ $signo->created_at->format('d/m/Y') }})
            @endif
        </option>
    @endforeach
</select>

                    <button type="button" id="btn-nuevo-paciente" title="Registrar paciente rápido"
                        class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white rounded-xl text-xs font-bold whitespace-nowrap shadow-sm transition-all">
                        + Nuevo
                    </button>
                </div>
            </div>

            {{-- Panel triage --}}
            <div id="modal-panel-triage" class="hidden p-4 rounded-2xl border bg-slate-50/70 border-slate-100">
                <p class="{{ $labelCls }}">Triage registrado en el último signo vital</p>
                <p id="modal-triage-actual" class="text-base font-black text-slate-800 tracking-tight mt-0.5">—</p>
                <p id="modal-triage-fecha" class="text-xs font-medium text-slate-400"></p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Tipo --}}
                <div>
                    <label class="{{ $labelCls }}">Tipo de admisión *</label>
                    <select name="tipo" required class="{{ $inputCls }}">
                        <option value="urgencias">Urgencias</option>
                        <option value="consulta_externa">Consulta Externa</option>
                        <option value="hospitalizacion">Hospitalización</option>
                        <option value="traslado">Traslado</option>
                    </select>
                </div>

                {{-- Triage --}}
                <div>
                    <label class="{{ $labelCls }}">Triage</label>
                    <select name="triage" id="modal_triage" class="{{ $inputCls }}">
                        <option value="">— Usar triage del último signo vital —</option>
                        <option value="rojo">🔴 Rojo — Emergencia</option>
                        <option value="naranja">🟠 Naranja — Muy urgente</option>
                        <option value="amarillo">🟡 Amarillo — Urgente</option>
                        <option value="verde">🟢 Verde — No urgente</option>
                        <option value="azul">🔵 Azul — Baja prioridad</option>
                    </select>
                </div>
            </div>

            {{-- Motivo --}}
            <div>
                <label class="{{ $labelCls }}">Motivo de la admisión *</label>
                <textarea name="motivo" rows="2" required placeholder="Razón por la que llega el paciente..." class="{{ $inputCls }} resize-none"></textarea>
            </div>

            {{-- Diagnóstico --}}
            <div>
                <label class="{{ $labelCls }}">Diagnóstico presuntivo</label>
                <textarea name="diagnostico_presuntivo" rows="2" placeholder="Diagnóstico inicial..." class="{{ $inputCls }} resize-none"></textarea>
            </div>

            {{-- Notas --}}
            <div>
                <label class="{{ $labelCls }}">Notas adicionales</label>
                <textarea name="notas" rows="2" class="{{ $inputCls }} resize-none"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t border-slate-100">
                <button type="button" id="btn-cancelar-admision"
                    class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-600 rounded-xl text-xs font-bold transition-all">
                    Cancelar
                </button>
                <button type="submit"
                    class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                    Registrar Admisión
                </button>
            </div>
        </form>
    </div>
</div>