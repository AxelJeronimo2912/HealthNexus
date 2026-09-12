@php $t = $turno ?? null; @endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium">Nombre *</label>
        <input type="text" name="nombre" value="{{ old('nombre', $t->nombre ?? '') }}" required placeholder="Ej. Mañana"
            class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
        @error('nombre')
            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label class="block text-sm font-medium">Código *</label>
        <input type="text" name="codigo" value="{{ old('codigo', $t->codigo ?? '') }}" required placeholder="Ej. MAT"
            maxlength="20" class="mt-1 w-full border-gray-300 rounded-md shadow-sm uppercase">
        @error('codigo')
            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label class="block text-sm font-medium">Hora de inicio *</label>
        <input type="time" name="hora_inicio"
            value="{{ old('hora_inicio', $t?->hora_inicio?->format('H:i') ?? '') }}" required
            class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
        @error('hora_inicio')
            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label class="block text-sm font-medium">Hora de fin *</label>
        <input type="time" name="hora_fin" value="{{ old('hora_fin', $t?->hora_fin?->format('H:i') ?? '') }}"
            required class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
        @error('hora_fin')
            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>
    <div class="md:col-span-2">
        <label class="block text-sm font-medium">Descripción</label>
        <textarea name="descripcion" rows="2" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('descripcion', $t->descripcion ?? '') }}</textarea>
    </div>
    <div class="md:col-span-2">
        <label class="inline-flex items-center">
            <input type="checkbox" name="activo" value="1" @checked(old('activo', $t->activo ?? true))
                class="rounded border-gray-300 text-blue-600 shadow-sm">
            <span class="ml-2 text-sm">Turno activo</span>
        </label>
    </div>
</div>
