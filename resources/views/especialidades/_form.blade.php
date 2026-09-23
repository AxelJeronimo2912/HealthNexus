@php $e = $especialidad ?? null; @endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium">Código *</label>
        <input type="text" name="codigo" value="{{ old('codigo', $e->codigo ?? '') }}" required
            placeholder="Ej. CARD, PED, GIN" maxlength="20"
            class="mt-1 w-full border-gray-300 rounded-md shadow-sm uppercase">
        @error('codigo')
            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label class="block text-sm font-medium">Nombre *</label>
        <input type="text" name="nombre" value="{{ old('nombre', $e->nombre ?? '') }}" required
            placeholder="Ej. Cardiología" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
        @error('nombre')
            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>
    <div class="md:col-span-2">
        <label class="block text-sm font-medium">Descripción</label>
        <textarea name="descripcion" rows="2" class="mt-1 w-full border-gray-300 rounded-md shadow-sm"
            placeholder="Breve descripción de la especialidad...">{{ old('descripcion', $e->descripcion ?? '') }}</textarea>
    </div>
    <div>
        <label class="block text-sm font-medium">Grupo *</label>
        <select name="grupo" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
            @foreach ([
        'clinica' => 'Clínica',
        'quirurgica' => 'Quirúrgica',
        'diagnostica' => 'Diagnóstica',
        'basica' => 'Básica',
        'otra' => 'Otra',
    ] as $key => $label)
                <option value="{{ $key }}" @selected(old('grupo', $e->grupo ?? 'clinica') == $key)>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        @error('grupo')
            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label class="block text-sm font-medium">Duración consulta (minutos) *</label>
        <input type="number" name="duracion_consulta_default"
            value="{{ old('duracion_consulta_default', $e->duracion_consulta_default ?? 30) }}" min="5"
            max="240" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
        @error('duracion_consulta_default')
            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label class="block text-sm font-medium">Color</label>
        <input type="color" name="color"
            value="{{ old('color', $e?->color ? '#' . ltrim($e->color, '#') : '#3B82F6') }}"
            class="mt-1 h-10 w-20 border-gray-300 rounded-md shadow-sm">
        <p class="text-xs text-gray-500 mt-1">Se usará en el calendario de citas</p>
    </div>
    <div class="md:col-span-2">
        <label class="inline-flex items-center mt-2">
            <input type="checkbox" name="activo" value="1" @checked(old('activo', $e->activo ?? true))
                class="rounded border-gray-300 text-blue-600 shadow-sm">
            <span class="ml-2 text-sm">Especialidad activa</span>
        </label>
    </div>
</div>
