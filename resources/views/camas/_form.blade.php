@php $c = $cama ?? null; @endphp

{{-- 1. Identificación --}}
<section>
    <h3 class="text-lg font-bold text-gray-800 mb-1">Identificación</h3>
    <p class="text-sm text-gray-500 mb-4">Código único y nombre descriptivo de la cama.</p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium">Código *</label>
            <input type="text" name="codigo" value="{{ old('codigo', $c->codigo ?? '') }}" required
                placeholder="Ej. CAMA-101" class="mt-1 w-full border-gray-300 rounded-md shadow-sm uppercase">
            @error('codigo')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-sm font-medium">Nombre descriptivo</label>
            <input type="text" name="nombre" value="{{ old('nombre', $c->nombre ?? '') }}"
                placeholder="Ej. Cama junto a ventana" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
        </div>
    </div>
</section>

<hr>

{{-- 2. Ubicación --}}
<section>
    <h3 class="text-lg font-bold text-gray-800 mb-1">Ubicación</h3>
    <p class="text-sm text-gray-500 mb-4">Dónde se encuentra físicamente la cama dentro del hospital.</p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium">Área</label>
            <select name="area" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                <option value="">— Selecciona —</option>
                @foreach (['Hospitalización', 'Urgencias', 'UCI', 'Pediatría', 'Ginecología', 'Cirugía', 'Recuperación', 'Aislamiento'] as $area)
                    <option value="{{ $area }}" @selected(old('area', $c->area ?? '') == $area)>{{ $area }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium">Piso</label>
            <input type="text" name="piso" value="{{ old('piso', $c->piso ?? '') }}" placeholder="Ej. Piso 1"
                class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
        </div>
        <div>
            <label class="block text-sm font-medium">Ala</label>
            <input type="text" name="ala" value="{{ old('ala', $c->ala ?? '') }}" placeholder="Ej. Ala Norte"
                class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
        </div>
        <div>
            <label class="block text-sm font-medium">Habitación</label>
            <input type="text" name="habitacion" value="{{ old('habitacion', $c->habitacion ?? '') }}"
                placeholder="Ej. 101" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
        </div>
    </div>
</section>

<hr>

{{-- 3. Tipo y estado --}}
<section>
    <h3 class="text-lg font-bold text-gray-800 mb-1">Tipo y estado</h3>
    <p class="text-sm text-gray-500 mb-4">Clasificación de la cama y su disponibilidad actual.</p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium">Tipo de cama *</label>
            <select name="tipo" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                @foreach (['general', 'pediatrica', 'uci', 'aislamiento', 'recuperacion', 'urgencias'] as $t)
                    <option value="{{ $t }}" @selected(old('tipo', $c->tipo ?? 'general') == $t)>
                        {{ ucfirst(str_replace('_', ' ', $t)) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium">Estado operativo *</label>
            <select name="estado" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                <option value="disponible" @selected(old('estado', $c->estado ?? 'disponible') == 'disponible')>Disponible</option>
                <option value="ocupada" @selected(old('estado', $c->estado ?? '') == 'ocupada')>Ocupada</option>
                <option value="mantenimiento" @selected(old('estado', $c->estado ?? '') == 'mantenimiento')>Mantenimiento</option>
                <option value="limpieza" @selected(old('estado', $c->estado ?? '') == 'limpieza')>Limpieza</option>
                <option value="fuera_servicio" @selected(old('estado', $c->estado ?? '') == 'fuera_servicio')>Fuera de servicio</option>
            </select>
        </div>
    </div>
</section>

<hr>

{{-- 4. Equipo --}}
<section>
    <h3 class="text-lg font-bold text-gray-800 mb-1">Equipo disponible</h3>
    <p class="text-sm text-gray-500 mb-4">Marca los recursos con los que cuenta la cama.</p>

    <div class="flex flex-wrap gap-6">
        <label class="inline-flex items-center">
            <input type="checkbox" name="oxigeno" value="1" @checked(old('oxigeno', $c->oxigeno ?? false))
                class="rounded border-gray-300 text-blue-600 shadow-sm">
            <span class="ml-2 text-sm">Oxígeno</span>
        </label>
        <label class="inline-flex items-center">
            <input type="checkbox" name="monitor" value="1" @checked(old('monitor', $c->monitor ?? false))
                class="rounded border-gray-300 text-blue-600 shadow-sm">
            <span class="ml-2 text-sm">Monitor de signos vitales</span>
        </label>
        <label class="inline-flex items-center">
            <input type="checkbox" name="ventilador" value="1" @checked(old('ventilador', $c->ventilador ?? false))
                class="rounded border-gray-300 text-blue-600 shadow-sm">
            <span class="ml-2 text-sm">Ventilador mecánico</span>
        </label>
    </div>
</section>

<hr>

{{-- 5. Notas y estado del registro --}}
<section>
    <h3 class="text-lg font-bold text-gray-800 mb-1">Notas</h3>
    <p class="text-sm text-gray-500 mb-4">Observaciones adicionales sobre la cama.</p>

    <textarea name="notas" rows="3" class="mt-1 w-full border-gray-300 rounded-md shadow-sm"
        placeholder="Ej. Requiere calibración mensual del monitor.">{{ old('notas', $c->notas ?? '') }}</textarea>

    <label class="inline-flex items-center mt-4">
        <input type="checkbox" name="activo" value="1" @checked(old('activo', $c->activo ?? true))
            class="rounded border-gray-300 text-blue-600 shadow-sm">
        <span class="ml-2 text-sm">Cama activa en el sistema</span>
    </label>
</section>
