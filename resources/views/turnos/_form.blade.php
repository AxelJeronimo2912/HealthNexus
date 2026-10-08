@php
    $t = $turno ?? null;
    $inputCls =
        'w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-indigo-500 focus:ring-0 outline-none';
    $labelCls = 'block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5';
    $errorCls = 'text-rose-600 text-xs mt-1 font-medium';
@endphp

<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        {{-- Nombre --}}
        <div>
            <label for="nombre" class="{{ $labelCls }}">Nombre del Turno *</label>
            <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $t?->nombre) }}"
                placeholder="Ej. Mañana" required class="{{ $inputCls }}">
            @error('nombre')
                <p class="{{ $errorCls }}">{{ $message }}</p>
            @enderror
        </div>

        {{-- Código --}}
        <div>
            <label for="codigo" class="{{ $labelCls }}">Código *</label>
            <input type="text" name="codigo" id="codigo" value="{{ old('codigo', $t?->codigo) }}"
                placeholder="EJ. MAT" required class="{{ $inputCls }} uppercase font-mono">
            @error('codigo')
                <p class="{{ $errorCls }}">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        {{-- Hora inicio --}}
        <div>
            <label for="hora_inicio" class="{{ $labelCls }}">Hora de inicio *</label>
            <input type="time" name="hora_inicio" id="hora_inicio"
                value="{{ old('hora_inicio', $t?->hora_inicio ? \Carbon\Carbon::parse($t->hora_inicio)->format('H:i') : '') }}"
                required class="{{ $inputCls }}">
            @error('hora_inicio')
                <p class="{{ $errorCls }}">{{ $message }}</p>
            @enderror
        </div>

        {{-- Hora fin --}}
        <div>
            <label for="hora_fin" class="{{ $labelCls }}">Hora de fin *</label>
            <input type="time" name="hora_fin" id="hora_fin"
                value="{{ old('hora_fin', $t?->hora_fin ? \Carbon\Carbon::parse($t->hora_fin)->format('H:i') : '') }}"
                required class="{{ $inputCls }}">
            @error('hora_fin')
                <p class="{{ $errorCls }}">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Descripción --}}
    <div>
        <label for="descripcion" class="{{ $labelCls }}">Descripción</label>
        <textarea name="descripcion" id="descripcion" rows="3"
            placeholder="Detalles sobre las responsabilidades o rango de este turno..."
            class="{{ $inputCls }} resize-none">{{ old('descripcion', $t?->descripcion) }}</textarea>
        @error('descripcion')
            <p class="{{ $errorCls }}">{{ $message }}</p>
        @enderror
    </div>

    {{-- Checkbox activo --}}
    <div class="pt-2">
        <label
            class="inline-flex items-center gap-2.5 cursor-pointer px-3 py-2 bg-emerald-50/60 border border-emerald-100 rounded-xl">
            <input type="checkbox" name="activo" value="1"
                {{ old('activo', $t?->activo ?? true) ? 'checked' : '' }}
                class="w-4 h-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 focus:ring-0 cursor-pointer">
            <span class="text-xs font-bold text-emerald-700">Turno activo</span>
        </label>
    </div>
</div>
