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

{{-- 5. Costo del servicio --}}
<section>
    <h3 class="text-lg font-bold text-gray-800 mb-1">Costo del servicio</h3>
    <p class="text-sm text-gray-500 mb-4">
        Define si este servicio genera un cargo al paciente. Ej. Enfermería puede ser gratuito.
    </p>

    @php
        $precioActual = old('precio', $s->precio ?? 0);
        $tieneCosto = old('tiene_costo', $s && (float) $s->precio > 0 ? '1' : '');
        $displayResumen = $tieneCosto ? 'flex' : 'none';
    @endphp

    <label class="inline-flex items-center">
        <input type="checkbox" name="tiene_costo" id="tiene_costo" value="1" @checked($tieneCosto)
            class="rounded border-gray-300 text-blue-600 shadow-sm" onchange="window.toggleCostoCheckbox(this)">
        {{-- LLAMADA GLOBAL --}}
        <span class="ml-2 text-sm font-medium">Este servicio tiene costo</span>
    </label>

    {{-- Recuadro verde --}}
    <div id="precio-resumen" style="display: {{ $displayResumen }};"
        class="mt-3 p-3 bg-green-50 border border-green-200 rounded-lg items-center justify-between">
        <div>
            <p class="text-xs text-gray-500 uppercase">Precio configurado</p>
            <p class="text-lg font-bold text-green-700">
                $<span id="precio-resumen-valor">{{ number_format((float) $precioActual, 2) }}</span>
                <span id="precio-resumen-desc" class="text-xs text-gray-500 font-normal">
                    {{ $s && $s->precio_descripcion ? '/ ' . $s->precio_descripcion : '' }}
                </span>
            </p>
        </div>
        <button type="button" onclick="window.abrirModalPrecio()"
            class="px-3 py-1 bg-white border border-gray-300 rounded text-sm text-gray-700 hover:bg-gray-50 shadow-sm">
            Editar precio
        </button>
    </div>

    <input type="hidden" name="precio" id="precio" value="{{ $precioActual }}">
    <input type="hidden" name="precio_descripcion" id="precio_descripcion"
        value="{{ old('precio_descripcion', $s->precio_descripcion ?? '') }}">

    @error('precio')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
    @enderror
</section>

<hr>

{{-- 6. Notas y estado --}}
<section>
    <h3 class="text-lg font-bold text-gray-800 mb-1">Notas adicionales</h3>
    <textarea name="notas" rows="2" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('notas', $s->notas ?? '') }}</textarea>

    <label class="inline-flex items-center mt-4">
        <input type="checkbox" name="activo" value="1" @checked(old('activo', $s->activo ?? true))
            class="rounded border-gray-300 text-blue-600 shadow-sm">
        <span class="ml-2 text-sm">Servicio activo</span>
    </label>
</section>

{{-- ==================== MODAL DE PRECIO ==================== --}}
<div id="modal-precio" style="display: none; background-color: rgba(0,0,0,0.5);"
    class="fixed inset-0 z-[100] items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6 relative">
        <div class="flex justify-between items-start mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Configurar precio</h3>
            <button type="button" onclick="window.cerrarModalPrecio()"
                class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
        </div>

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium">Precio *</label>
                <div class="mt-1 relative rounded-md shadow-sm">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">$</span>
                    <input type="number" id="modal_precio" step="0.01" min="0" placeholder="0.00"
                        class="pl-7 w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <p id="modal-error-precio" style="display: none;" class="text-red-600 text-xs mt-1"></p>
            </div>

            <div>
                <label class="block text-sm font-medium">Descripción del precio</label>
                <input type="text" id="modal_precio_descripcion" placeholder="Ej. Por consulta, por día"
                    class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
            </div>
        </div>

        <div class="flex justify-end gap-2 pt-4 mt-4 border-t">
            <button type="button" onclick="window.cerrarModalPrecio()"
                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">
                Cancelar
            </button>
            <button type="button" onclick="window.guardarPrecio()"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm">
                Guardar precio
            </button>
        </div>
    </div>
</div>
