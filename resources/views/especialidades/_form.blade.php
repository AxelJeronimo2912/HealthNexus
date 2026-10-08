@php
    $e = $especialidad ?? null;
    $prefix = $prefix ?? 'create';
@endphp

@php
    $inputCls =
        'w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-indigo-500 focus:ring-0 outline-none';
    $labelCls = 'block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5';
    $errorCls = 'text-rose-600 text-xs mt-1 font-medium';
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="{{ $labelCls }}">Código *</label>
        <input type="text" id="{{ $prefix }}_codigo" name="codigo" value="{{ old('codigo', $e->codigo ?? '') }}"
            required placeholder="Ej. CARD, PED, GIN" maxlength="20" class="{{ $inputCls }} uppercase">
        @error('codigo')
            <p class="{{ $errorCls }}">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="{{ $labelCls }}">Nombre *</label>
        <input type="text" id="{{ $prefix }}_nombre" name="nombre"
            value="{{ old('nombre', $e->nombre ?? '') }}" required placeholder="Ej. Cardiología"
            class="{{ $inputCls }}">
        @error('nombre')
            <p class="{{ $errorCls }}">{{ $message }}</p>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label class="{{ $labelCls }}">Descripción</label>
        <textarea id="{{ $prefix }}_descripcion" name="descripcion" rows="2"
            class="{{ $inputCls }} resize-none" placeholder="Breve descripción de la especialidad...">{{ old('descripcion', $e->descripcion ?? '') }}</textarea>
    </div>

    <div>
        <label class="{{ $labelCls }}">Grupo *</label>
        <select id="{{ $prefix }}_grupo" name="grupo" required class="{{ $inputCls }}">
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
            <p class="{{ $errorCls }}">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="{{ $labelCls }}">Duración consulta (minutos) *</label>
        <input type="number" id="{{ $prefix }}_duracion_consulta_default" name="duracion_consulta_default"
            value="{{ old('duracion_consulta_default', $e->duracion_consulta_default ?? 30) }}" min="5"
            max="240" required class="{{ $inputCls }}">
        @error('duracion_consulta_default')
            <p class="{{ $errorCls }}">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="{{ $labelCls }}">Color</label>
        <input type="color" id="{{ $prefix }}_color" name="color"
            value="{{ old('color', $e?->color ? '#' . ltrim($e->color, '#') : '#3B82F6') }}"
            class="mt-0.5 h-10 w-20 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer">
        <p class="text-[11px] text-slate-400 mt-1.5">Se usará en el calendario de citas</p>
    </div>

    <div class="md:col-span-2">
        <label class="inline-flex items-center gap-2.5 mt-1 cursor-pointer">
            <input type="checkbox" id="{{ $prefix }}_activo" name="activo" value="1"
                @checked(old('activo', $e->activo ?? true))
                class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 focus:ring-0 cursor-pointer">
            <span class="text-xs font-bold text-slate-700">Especialidad activa</span>
        </label>
    </div>
</div>
