@php $m = $medicamento ?? null; @endphp

{{-- 1. Identificación --}}
<section>
    <h3 class="text-lg font-bold text-gray-800 mb-1">Identificación</h3>
    <p class="text-sm text-gray-500 mb-4">Datos básicos del medicamento.</p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="md:col-span-2">
            <label class="block text-sm font-medium">Nombre comercial *</label>
            <input type="text" name="nombre" value="{{ old('nombre', $m->nombre ?? '') }}" required
                class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
            @error('nombre')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-sm font-medium">Sustancia activa</label>
            <input type="text" name="sustancia_activa"
                value="{{ old('sustancia_activa', $m->sustancia_activa ?? '') }}"
                class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
        </div>
        <div>
            <label class="block text-sm font-medium">Laboratorio</label>
            <input type="text" name="laboratorio" value="{{ old('laboratorio', $m->laboratorio ?? '') }}"
                class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
        </div>
        <div>
            <label class="block text-sm font-medium">Presentación</label>
            <select name="presentacion" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                <option value="">— Selecciona —</option>
                @foreach (['Tableta', 'Cápsula', 'Jarabe', 'Suspensión', 'Solución inyectable', 'Crema', 'Ungüento', 'Gotas', 'Supositorio', 'Parche'] as $pres)
                    <option value="{{ $pres }}" @selected(old('presentacion', $m->presentacion ?? '') == $pres)>{{ $pres }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium">Concentración</label>
            <input type="text" name="concentracion" value="{{ old('concentracion', $m->concentracion ?? '') }}"
                placeholder="500 mg, 10 ml, 5 %" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
        </div>
        <div>
            <label class="block text-sm font-medium">Vía de administración</label>
            <select name="via_administracion" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                <option value="">— Selecciona —</option>
                @foreach (['Oral', 'Intravenosa', 'Intramuscular', 'Subcutánea', 'Tópica', 'Oftálmica', 'Ótica', 'Nasal', 'Rectal', 'Sublingual', 'Inhalatoria'] as $via)
                    <option value="{{ $via }}" @selected(old('via_administracion', $m->via_administracion ?? '') == $via)>{{ $via }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium">Grupo terapéutico</label>
            <input type="text" name="grupo_terapeutico"
                value="{{ old('grupo_terapeutico', $m->grupo_terapeutico ?? '') }}"
                placeholder="Analgésico, Antibiótico, etc." class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
        </div>
        <div>
            <label class="block text-sm font-medium">Código de barras</label>
            <input type="text" name="codigo_barras" value="{{ old('codigo_barras', $m->codigo_barras ?? '') }}"
                class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
            @error('codigo_barras')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-sm font-medium">Registro sanitario</label>
            <input type="text" name="registro_sanitario"
                value="{{ old('registro_sanitario', $m->registro_sanitario ?? '') }}"
                class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
        </div>
    </div>
</section>

<hr>

{{-- 2. Clasificación --}}
<section>
    <h3 class="text-lg font-bold text-gray-800 mb-1">Clasificación</h3>
    <p class="text-sm text-gray-500 mb-4">Marca las categorías especiales que apliquen.</p>

    <div class="flex flex-wrap gap-6">
        <label class="inline-flex items-center">
            <input type="checkbox" name="psicotropico" value="1" @checked(old('psicotropico', $m->psicotropico ?? false))
                class="rounded border-gray-300 text-blue-600 shadow-sm">
            <span class="ml-2 text-sm">Psicotrópico</span>
        </label>
        <label class="inline-flex items-center">
            <input type="checkbox" name="antibiotico" value="1" @checked(old('antibiotico', $m->antibiotico ?? false))
                class="rounded border-gray-300 text-blue-600 shadow-sm">
            <span class="ml-2 text-sm">Antibiótico</span>
        </label>
        <label class="inline-flex items-center">
            <input type="checkbox" name="controlado" value="1" @checked(old('controlado', $m->controlado ?? false))
                class="rounded border-gray-300 text-blue-600 shadow-sm">
            <span class="ml-2 text-sm">Controlado</span>
        </label>
    </div>
</section>

<hr>

{{-- 3. Inventario --}}
<section>
    <h3 class="text-lg font-bold text-gray-800 mb-1">Inventario</h3>
    <p class="text-sm text-gray-500 mb-4">Datos para el control de existencias.</p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium">Unidad de medida *</label>
            <select name="unidad_medida" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                @foreach (['pieza', 'caja', 'frasco', 'ampolleta', 'tubo', 'sobre', 'blíster'] as $um)
                    <option value="{{ $um }}" @selected(old('unidad_medida', $m->unidad_medida ?? 'pieza') == $um)>{{ ucfirst($um) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium">Stock mínimo *</label>
            <input type="number" name="stock_minimo" min="0"
                value="{{ old('stock_minimo', $m->stock_minimo ?? 0) }}" required
                class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
            @error('stock_minimo')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-sm font-medium">Stock máximo *</label>
            <input type="number" name="stock_maximo" min="0"
                value="{{ old('stock_maximo', $m->stock_maximo ?? 0) }}" required
                class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
            @error('stock_maximo')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-sm font-medium">Precio compra</label>
            <input type="number" step="0.01" min="0" name="precio_compra"
                value="{{ old('precio_compra', $m->precio_compra ?? '') }}"
                class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
        </div>
        <div>
            <label class="block text-sm font-medium">Precio venta</label>
            <input type="number" step="0.01" min="0" name="precio_venta"
                value="{{ old('precio_venta', $m->precio_venta ?? '') }}"
                class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
        </div>
    </div>
</section>

<hr>

{{-- 4. Estado --}}
<section>
    <h3 class="text-lg font-bold text-gray-800 mb-1">Estado</h3>
    <p class="text-sm text-gray-500 mb-4">Activa o desactiva el medicamento del catálogo.</p>
    <label class="inline-flex items-center">
        <input type="checkbox" name="activo" value="1" @checked(old('activo', $m->activo ?? true))
            class="rounded border-gray-300 text-blue-600 shadow-sm">
        <span class="ml-2 text-sm">Medicamento activo</span>
    </label>
</section>
