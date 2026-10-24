@php
    $lbl = 'block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1';
    $inp =
        'w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-800 focus:border-brand-500 focus:ring-0 outline-none';
@endphp

<div class="medicamento-row grid grid-cols-2 lg:grid-cols-12 gap-3 p-4 bg-slate-50/70 border border-slate-100 rounded-2xl hover:border-brand-200 transition-colors"
    data-index="{{ $index }}">
    <div class="col-span-2 lg:col-span-4">
        <label class="{{ $lbl }}">Medicamento *</label>
        <select name="medicamentos[{{ $index }}][id]" class="{{ $inp }}" required>
            <option value="">— Selecciona —</option>
            @foreach ($medicamentos as $m)
                <option value="{{ $m->id }}" @selected(($med['id'] ?? null) == $m->id)>
                    {{ $m->nombre }} {{ $m->concentracion }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="lg:col-span-2">
        <label class="{{ $lbl }}">Dosis</label>
        <input type="text" name="medicamentos[{{ $index }}][dosis]" value="{{ $med['dosis'] ?? '' }}"
            class="{{ $inp }}" placeholder="500 mg">
    </div>
    <div class="lg:col-span-2">
        <label class="{{ $lbl }}">Vía</label>
        <input type="text" name="medicamentos[{{ $index }}][via]" value="{{ $med['via'] ?? '' }}"
            class="{{ $inp }}" placeholder="Oral">
    </div>
    <div class="lg:col-span-2">
        <label class="{{ $lbl }}">Frecuencia</label>
        <input type="text" name="medicamentos[{{ $index }}][frecuencia]"
            value="{{ $med['frecuencia'] ?? '' }}" class="{{ $inp }}" placeholder="Cada 8h">
    </div>
    <div class="lg:col-span-2">
        <label class="{{ $lbl }}">Duración</label>
        <input type="text" name="medicamentos[{{ $index }}][duracion]" value="{{ $med['duracion'] ?? '' }}"
            class="{{ $inp }}" placeholder="7 días">
    </div>
    <div class="col-span-2 lg:col-span-10">
        <label class="{{ $lbl }}">Indicaciones</label>
        <input type="text" name="medicamentos[{{ $index }}][indicaciones]"
            value="{{ $med['indicaciones'] ?? '' }}" class="{{ $inp }}"
            placeholder="Tomar después de alimentos">
    </div>
    <div class="col-span-2 lg:col-span-2 flex items-end">
        <button type="button" onclick="this.closest('.medicamento-row').remove()"
            class="w-full px-3 py-2 bg-rose-50 hover:bg-rose-100 active:scale-95 text-rose-600 rounded-xl text-xs font-bold transition-all inline-flex items-center gap-1.5 justify-center">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            Eliminar
        </button>
    </div>
</div>
