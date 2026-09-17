@php $c = $cama ?? null; @endphp

<div class="space-y-5 text-sm">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block font-semibold text-slate-600 mb-1">Código *</label>
            <input type="text" name="codigo" value="{{ old('codigo', $c->codigo ?? '') }}" required placeholder="Ej. CAMA-101"
                class="w-full border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-950 text-sm uppercase">
            @error('codigo') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block font-semibold text-slate-600 mb-1">Nombre descriptivo</label>
            <input type="text" name="nombre" value="{{ old('nombre', $c->nombre ?? '') }}" placeholder="Ej. Cama junto a ventana"
                class="w-full border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-950 text-sm">
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div>
            <label class="block font-semibold text-slate-600 mb-1">Área</label>
            <select name="area" class="w-full border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-950 text-sm">
                <option value="">Seleccionar</option>
                @foreach (['Hospitalización', 'Urgencias', 'UCI', 'Pediatría', 'Ginecología', 'Cirugía', 'Recuperación', 'Aislamiento'] as $area)
                    <option value="{{ $area }}" @selected(old('area', $c->area ?? '') == $area)>{{ $area }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block font-semibold text-slate-600 mb-1">Piso</label>
            <input type="text" name="piso" value="{{ old('piso', $c->piso ?? '') }}" placeholder="Ej. Piso 1"
                class="w-full border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-950 text-sm">
        </div>
        <div>
            <label class="block font-semibold text-slate-600 mb-1">Ala</label>
            <input type="text" name="ala" value="{{ old('ala', $c->ala ?? '') }}" placeholder="Ej. Norte"
                class="w-full border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-950 text-sm">
        </div>
        <div>
            <label class="block font-semibold text-slate-600 mb-1">Habitación</label>
            <input type="text" name="habitacion" value="{{ old('habitacion', $c->habitacion ?? '') }}" placeholder="Ej. 101"
                class="w-full border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-950 text-sm">
        </div>

        <div>
            <label class="block text-sm font-medium">Servicio</label>
            <select name="servicio_id" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                <option value="">— Selecciona —</option>
                @foreach ($servicios as $s)
                    <option value="{{ $s->id }}" @selected(old('servicio_id', $c->servicio_id ?? '') == $s->id)>
                        {{ $s->nombre }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block font-semibold text-slate-600 mb-1">Tipo *</label>
            <select name="tipo" required class="w-full border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-950 text-sm">
                @foreach (['general' => 'General', 'pediatrica' => 'Pediátrica', 'uci' => 'UCI', 'aislamiento' => 'Aislamiento', 'recuperacion' => 'Recuperación', 'urgencias' => 'Urgencias'] as $key => $lbl)
                    <option value="{{ $key }}" @selected(old('tipo', $c->tipo ?? 'general') == $key)>{{ $lbl }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block font-semibold text-slate-600 mb-1">Estado *</label>
            <select name="estado" required class="w-full border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-950 text-sm">
                <option value="disponible" @selected(old('estado', $c->estado ?? 'disponible') == 'disponible')>Disponible</option>
                <option value="ocupada" @selected(old('estado', $c->estado ?? '') == 'ocupada')>Ocupada</option>
                <option value="mantenimiento" @selected(old('estado', $c->estado ?? '') == 'mantenimiento')>Mantenimiento</option>
                <option value="limpieza" @selected(old('estado', $c->estado ?? '') == 'limpieza')>Limpieza</option>
                <option value="fuera_servicio" @selected(old('estado', $c->estado ?? '') == 'fuera_servicio')>Fuera de Servicio</option>
            </select>
        </div>
    </div>

    <div>
        <label class="block font-semibold text-slate-600 mb-1">Notas adicionales</label>
        <textarea name="notas" rows="2" class="w-full border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-950 text-sm" placeholder="Observaciones de la cama...">{{ old('notas', $c->notas ?? '') }}</textarea>
    </div>
</div>