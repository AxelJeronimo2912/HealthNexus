<div class="medicamento-row grid grid-cols-1 md:grid-cols-6 gap-3 p-4 bg-slate-50/70 border border-slate-100 rounded-2xl hover:border-indigo-100 transition-colors"
    data-index="{{ $index }}">
    <div class="md:col-span-2">
        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">
            Medicamento *
        </label>
        <select name="medicamentos[{{ $index }}][id]"
            class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-800 focus:border-indigo-500 focus:ring-0 outline-none"
            required>
            <option value="">— Selecciona —</option>
            @foreach ($medicamentos as $m)
                <option value="{{ $m->id }}" @selected(($med['id'] ?? null) == $m->id)>
                    {{ $m->nombre }} {{ $m->concentracion }}
                </option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Dosis</label>
        <input type="text" name="medicamentos[{{ $index }}][dosis]" value="{{ $med['dosis'] ?? '' }}"
            class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-800 focus:border-indigo-500 focus:ring-0 outline-none"
            placeholder="500 mg">
    </div>
    <div>
        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Vía</label>
        <input type="text" name="medicamentos[{{ $index }}][via]" value="{{ $med['via'] ?? '' }}"
            class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-800 focus:border-indigo-500 focus:ring-0 outline-none"
            placeholder="Oral">
    </div>
    <div>
        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Frecuencia</label>
        <input type="text" name="medicamentos[{{ $index }}][frecuencia]"
            value="{{ $med['frecuencia'] ?? '' }}"
            class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-800 focus:border-indigo-500 focus:ring-0 outline-none"
            placeholder="Cada 8h">
    </div>
    <div>
        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Duración</label>
        <input type="text" name="medicamentos[{{ $index }}][duracion]" value="{{ $med['duracion'] ?? '' }}"
            class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-800 focus:border-indigo-500 focus:ring-0 outline-none"
            placeholder="7 días">
    </div>
    <div class="md:col-span-5">
        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Indicaciones</label>
        <input type="text" name="medicamentos[{{ $index }}][indicaciones]"
            value="{{ $med['indicaciones'] ?? '' }}"
            class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-800 focus:border-indigo-500 focus:ring-0 outline-none"
            placeholder="Tomar después de alimentos">
    </div>
    <div class="flex items-end">
        <button type="button" onclick="this.closest('.medicamento-row').remove()"
            class="px-3 py-2 bg-rose-50 hover:bg-rose-100 active:scale-95 text-rose-600 rounded-xl text-[11px] font-bold transition-all inline-flex items-center gap-1.5 w-full md:w-auto justify-center">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            Eliminar
        </button>
    </div>
</div>
