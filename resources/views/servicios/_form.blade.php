@php $s = $servicio ?? null; @endphp

{{-- 1. Identificación --}}
<section>
    <h3 class="text-lg font-bold text-gray-800 mb-1">Identificación</h3>
    <p class="text-sm text-gray-500 mb-4">Datos básicos del servicio.</p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium">Código *</label>
            <input type="text" name="codigo" value="{{ old('codigo', $s->codigo ?? '') }}" required
                placeholder="Ej. CONS-EXT, URG, HOSP" class="mt-1 w-full border-gray-300 rounded-md shadow-sm uppercase">
            @error('codigo')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-sm font-medium">Nombre *</label>
            <input type="text" name="nombre" value="{{ old('nombre', $s->nombre ?? '') }}" required
                placeholder="Ej. Consulta Externa" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
            @error('nombre')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium">Tipo *</label>
            <select name="tipo" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                <option value="">— Selecciona —</option>
                @foreach ([
        'consulta_externa' => 'Consulta Externa',
        'urgencias' => 'Urgencias',
        'hospitalizacion' => 'Hospitalización',
        'quirofano' => 'Quirófano',
        'farmacia' => 'Farmacia',
        'enfermeria' => 'Enfermería',
        'laboratorio' => 'Laboratorio',
        'imagenologia' => 'Imagenología',
        'otro' => 'Otro',
    ] as $key => $label)
                    <option value="{{ $key }}" @selected(old('tipo', $s->tipo ?? '') == $key)>{{ $label }}</option>
                @endforeach
            </select>
            @error('tipo')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium">Descripción</label>
            <textarea name="descripcion" rows="2" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('descripcion', $s->descripcion ?? '') }}</textarea>
        </div>
    </div>
</section>

<hr>

{{-- 2. Ubicación --}}
<section>
    <h3 class="text-lg font-bold text-gray-800 mb-1">Ubicación</h3>
    <p class="text-sm text-gray-500 mb-4">Dónde se encuentra físicamente el servicio.</p>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="md:col-span-3">
            <label class="block text-sm font-medium">Ubicación</label>
            <input type="text" name="ubicacion" value="{{ old('ubicacion', $s->ubicacion ?? '') }}"
                placeholder="Ej. Edificio A, planta baja" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
        </div>
        <div>
            <label class="block text-sm font-medium">Piso</label>
            <input type="text" name="piso" value="{{ old('piso', $s->piso ?? '') }}" placeholder="Ej. Piso 1"
                class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
        </div>
        <div>
            <label class="block text-sm font-medium">Ala</label>
            <input type="text" name="ala" value="{{ old('ala', $s->ala ?? '') }}" placeholder="Ej. Ala Norte"
                class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
        </div>
        <div>
            <label class="block text-sm font-medium">Extensión telefónica</label>
            <input type="text" name="extension_telefonica"
                value="{{ old('extension_telefonica', $s->extension_telefonica ?? '') }}"
                class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
        </div>
    </div>
</section>

<hr>

{{-- 3. Horario --}}
<section>
    <h3 class="text-lg font-bold text-gray-800 mb-1">Horario</h3>
    <p class="text-sm text-gray-500 mb-4">Define el horario de atención.</p>

    <div class="mb-3">
        <label class="inline-flex items-center">
            <input type="checkbox" name="abierto_24h" value="1" @checked(old('abierto_24h', $s->abierto_24h ?? false))
                class="rounded border-gray-300 text-blue-600 shadow-sm">
            <span class="ml-2 text-sm">Abierto 24 horas</span>
        </label>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium">Hora de apertura</label>
            <input type="time" name="hora_apertura" value="{{ old('hora_apertura', $s->hora_apertura ?? '') }}"
                class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
        </div>
        <div>
            <label class="block text-sm font-medium">Hora de cierre</label>
            <input type="time" name="hora_cierre" value="{{ old('hora_cierre', $s->hora_cierre ?? '') }}"
                class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
        </div>
    </div>
</section>

<hr>

{{-- 4. Capacidad --}}
<section>
    <h3 class="text-lg font-bold text-gray-800 mb-1">Capacidad</h3>
    <p class="text-sm text-gray-500 mb-4">Número máximo de pacientes simultáneos, camas o recursos.</p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium">Capacidad</label>
            <input type="number" name="capacidad" min="0" value="{{ old('capacidad', $s->capacidad ?? '') }}"
                class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
        </div>
    </div>
</section>

<hr>

{{-- 5. Notas y estado --}}
<section>
    <h3 class="text-lg font-bold text-gray-800 mb-1">Notas adicionales</h3>
    <textarea name="notas" rows="2" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('notas', $s->notas ?? '') }}</textarea>

    <label class="inline-flex items-center mt-4">
        <input type="checkbox" name="activo" value="1" @checked(old('activo', $s->activo ?? true))
            class="rounded border-gray-300 text-blue-600 shadow-sm">
        <span class="ml-2 text-sm">Servicio activo</span>
    </label>
</section>
