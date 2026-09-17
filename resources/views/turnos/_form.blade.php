@php
    $t = $turno ?? null;
@endphp

<div class="space-y-4 text-gray-800">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Nombre -->
        <div>
            <label for="nombre" class="block text-xs font-bold text-gray-600 uppercase mb-1">
                Nombre del Turno *
            </label>
            <input type="text" name="nombre" id="nombre" 
                   value="{{ old('nombre', $t?->nombre) }}" 
                   placeholder="Ej. Mañana" 
                   required
                   class="w-full text-sm rounded-lg border-gray-200 bg-gray-50/50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 text-gray-900 shadow-sm transition">
            @error('nombre')
                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Código -->
        <div>
            <label for="codigo" class="block text-xs font-bold text-gray-600 uppercase mb-1">
                Código *
            </label>
            <input type="text" name="codigo" id="codigo" 
                   value="{{ old('codigo', $t?->codigo) }}" 
                   placeholder="EJ. MAT" 
                   required
                   class="w-full text-sm rounded-lg border-gray-200 bg-gray-50/50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 text-gray-900 shadow-sm uppercase font-mono transition">
            @error('codigo')
                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Hora Inicio -->
        <div>
            <label for="hora_inicio" class="block text-xs font-bold text-gray-600 uppercase mb-1">
                Hora de inicio *
            </label>
            <input type="time" name="hora_inicio" id="hora_inicio" 
                   value="{{ old('hora_inicio', $t?->hora_inicio ? \Carbon\Carbon::parse($t->hora_inicio)->format('H:i') : '') }}" 
                   required
                   class="w-full text-sm rounded-lg border-gray-200 bg-gray-50/50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 text-gray-900 shadow-sm transition">
            @error('hora_inicio')
                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Hora Fin -->
        <div>
            <label for="hora_fin" class="block text-xs font-bold text-gray-600 uppercase mb-1">
                Hora de fin *
            </label>
            <input type="time" name="hora_fin" id="hora_fin" 
                   value="{{ old('hora_fin', $t?->hora_fin ? \Carbon\Carbon::parse($t->hora_fin)->format('H:i') : '') }}" 
                   required
                   class="w-full text-sm rounded-lg border-gray-200 bg-gray-50/50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 text-gray-900 shadow-sm transition">
            @error('hora_fin')
                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Descripción -->
    <div>
        <label for="descripcion" class="block text-xs font-bold text-gray-600 uppercase mb-1">
            Descripción
        </label>
        <textarea name="descripcion" id="descripcion" rows="3" 
                  placeholder="Detalles sobre las responsabilidades o rango de este turno..."
                  class="w-full text-sm rounded-lg border-gray-200 bg-gray-50/50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 text-gray-900 shadow-sm transition">{{ old('descripcion', $t?->descripcion) }}</textarea>
        @error('descripcion')
            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Checkbox Activo -->
    <div class="pt-2">
        <label class="inline-flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="activo" value="1" 
                   {{ old('activo', $t?->activo ?? true) ? 'checked' : '' }}
                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 w-4 h-4">
            <span class="text-sm font-semibold text-gray-700">Turno activo</span>
        </label>
    </div>
</div>