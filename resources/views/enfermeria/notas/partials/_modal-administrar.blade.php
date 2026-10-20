<div class="p-6">
    <div class="flex justify-between items-center mb-5 border-b border-slate-100 pb-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-pink-50 flex items-center justify-center text-pink-600 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-800">Administrar Medicamento</h3>
                <p class="text-xs text-slate-500 mt-0.5">Registra la dosis y actualiza el inventario</p>
            </div>
        </div>
        <button type="button" onclick="Alpine.$data(document.getElementById('modal-administrar')).open = false"
            class="w-9 h-9 flex items-center justify-center rounded-full bg-slate-50 text-slate-400 hover:text-rose-500 hover:bg-rose-50 transition-colors text-xl leading-none border border-slate-200 shrink-0">&times;</button>
    </div>

    {{-- AQUÍ LLAMAMOS A LA FUNCIÓN GLOBAL --}}
    <form id="form-administrar" action="{{ route('enfermeria.administraciones.store') }}" method="POST"
        onsubmit="event.preventDefault(); window.guardarMedicamento(this);" class="space-y-5">
        @csrf

        {{-- 🔴 ESTO ES LO QUE SOLUCIONA EL ERROR 422 --}}
        <input type="hidden" name="tipo" value="nota_enfermeria">

        {{-- Contenedor de errores por si falla algo en el servidor --}}
        <div id="error-container-administrar"
            class="hidden p-4 bg-rose-50 border border-rose-100 text-rose-800 rounded-2xl text-xs font-semibold mb-4">
            <p class="font-bold mb-1">Corrige los siguientes errores:</p>
            <ul class="list-disc list-inside" id="error-list-administrar"></ul>
        </div>

        <div>
            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Paciente *</label>
            <select name="paciente_id" required
                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-pink-500 focus:ring-0 outline-none">
                <option value="">— Selecciona —</option>
                @foreach ($pacientes as $p)
                    <option value="{{ $p->id }}" @selected(old('paciente_id') == $p->id)>{{ $p->nombre_completo }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Medicamento
                *</label>
            <select id="select-medicamento" name="medicamento_id" required placeholder="Busca un medicamento...">
                <option value="">— Selecciona un medicamento —</option>
                @foreach ($medicamentos as $m)
                    <option value="{{ $m->id }}" @selected(old('medicamento_id') == $m->id)>
                        {{ $m->nombre }} {{ $m->concentracion }} (Stock: {{ $m->stock_total_calculado }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Dosis
                    *</label>
                <input type="text" name="dosis" value="{{ old('dosis') }}" placeholder="Ej. 1 tableta, 5 ml"
                    required
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-pink-500 focus:ring-0 outline-none">
            </div>
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Vía *</label>
                <select name="via" required
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-pink-500 focus:ring-0 outline-none">
                    <option value="">— Selecciona —</option>
                    @foreach (['Oral', 'Intravenosa', 'Intramuscular', 'Subcutánea', 'Tópica', 'Oftálmica', 'Inhalatoria', 'Rectal'] as $via)
                        <option value="{{ $via }}" @selected(old('via') == $via)>{{ $via }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label
                class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Observaciones</label>
            <textarea name="observaciones" rows="2" placeholder="Ej. Paciente refiere mejoría tras la dosis..."
                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-pink-500 focus:ring-0 outline-none resize-none">{{ old('observaciones') }}</textarea>
        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
            <button type="button" onclick="Alpine.$data(document.getElementById('modal-administrar')).open = false"
                class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-600 rounded-xl text-xs font-bold transition-all">
                Cancelar
            </button>
            <button type="submit" id="btn-submit-administrar"
                class="px-5 py-2.5 bg-pink-600 hover:bg-pink-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all inline-flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
                Administrar y Registrar
            </button>
        </div>
    </form>
</div>

<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<style>
    .ts-control {
        background-color: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 0.75rem !important;
        padding: 0.625rem 0.875rem !important;
        font-size: 0.75rem !important;
        color: #1e293b !important;
        box-shadow: none !important;
    }

    .ts-control.focus {
        background-color: #ffffff !important;
        border-color: #ec4899 !important;
        outline: none !important;
    }

    .ts-dropdown {
        font-size: 0.75rem !important;
        border-radius: 0.75rem !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1) !important;
    }

    .ts-dropdown .option {
        padding: 0.5rem 0.875rem !important;
    }

    .ts-dropdown .option.active {
        background-color: #fce7f3 !important;
        color: #be185d !important;
    }
</style>

<script>
    setTimeout(() => {
        if (document.getElementById("select-medicamento") && !document.getElementById("select-medicamento")
            .tomselect) {
            new TomSelect("#select-medicamento", {
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                placeholder: "Busca un medicamento...",
                maxOptions: 50,
            });
        }
    }, 100);
</script>
