<div class="medicamento-row grid grid-cols-1 md:grid-cols-6 gap-2 p-3 bg-gray-50 rounded border"
    data-index="{{ $index }}">
    <div class="md:col-span-2">
        <label class="block text-xs text-gray-500">Medicamento</label>
        <select name="medicamentos[{{ $index }}][id]" class="w-full border-gray-300 rounded-md text-sm" required>
            <option value="">— Selecciona —</option>
            @foreach ($medicamentos as $m)
                <option value="{{ $m->id }}" @selected(($med['id'] ?? null) == $m->id)>
                    {{ $m->nombre }} {{ $m->concentracion }}
                </option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs text-gray-500">Dosis</label>
        <input type="text" name="medicamentos[{{ $index }}][dosis]" value="{{ $med['dosis'] ?? '' }}"
            class="w-full border-gray-300 rounded-md text-sm" placeholder="500 mg">
    </div>
    <div>
        <label class="block text-xs text-gray-500">Vía</label>
        <input type="text" name="medicamentos[{{ $index }}][via]" value="{{ $med['via'] ?? '' }}"
            class="w-full border-gray-300 rounded-md text-sm" placeholder="Oral">
    </div>
    <div>
        <label class="block text-xs text-gray-500">Frecuencia</label>
        <input type="text" name="medicamentos[{{ $index }}][frecuencia]"
            value="{{ $med['frecuencia'] ?? '' }}" class="w-full border-gray-300 rounded-md text-sm"
            placeholder="Cada 8h">
    </div>
    <div>
        <label class="block text-xs text-gray-500">Duración</label>
        <input type="text" name="medicamentos[{{ $index }}][duracion]" value="{{ $med['duracion'] ?? '' }}"
            class="w-full border-gray-300 rounded-md text-sm" placeholder="7 días">
    </div>
    <div class="md:col-span-5">
        <label class="block text-xs text-gray-500">Indicaciones</label>
        <input type="text" name="medicamentos[{{ $index }}][indicaciones]"
            value="{{ $med['indicaciones'] ?? '' }}" class="w-full border-gray-300 rounded-md text-sm"
            placeholder="Tomar después de alimentos">
    </div>
    <div class="flex items-end">
        <button type="button" onclick="this.closest('.medicamento-row').remove()"
            class="text-red-600 hover:underline text-sm">Eliminar</button>
    </div>
</div>
