@php $p = $paciente ?? null; @endphp

{{-- 1. Identidad del paciente --}}
<section>
    <h3 class="text-lg font-bold text-gray-800 mb-1">Identidad del paciente</h3>
    <p class="text-sm text-gray-500 mb-4">
        Datos personales tal como aparecen en su documentación oficial.
    </p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium">Nombre</label>
            <input type="text" name="nombre" value="{{ old('nombre', $p->nombre ?? '') }}" required
                class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
            @error('nombre')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-sm font-medium">Apellido Paterno</label>
            <input type="text" name="apellido_paterno"
                value="{{ old('apellido_paterno', $p->apellido_paterno ?? '') }}" required
                class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
            @error('apellido_paterno')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-sm font-medium">Apellido Materno</label>
            <input type="text" name="apellido_materno"
                value="{{ old('apellido_materno', $p->apellido_materno ?? '') }}"
                class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
        </div>
        <div>
            <label class="block text-sm font-medium">Fecha de Nacimiento</label>
            <input type="date" name="fecha_nacimiento" id="fecha_nacimiento"
                value="{{ old('fecha_nacimiento', $p && $p->fecha_nacimiento ? $p->fecha_nacimiento->format('Y-m-d') : '') }}"
                required class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
            @error('fecha_nacimiento')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-sm font-medium">Edad</label>
            <input type="text" id="edad" readonly
                class="mt-1 w-full border-gray-300 rounded-md shadow-sm bg-gray-100">
        </div>
        <div>
            <label class="block text-sm font-medium">Sexo</label>
            <select name="sexo" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                <option value="hombre" @selected(old('sexo', $p->sexo ?? '') == 'hombre')>Hombre</option>
                <option value="mujer" @selected(old('sexo', $p->sexo ?? '') == 'mujer')>Mujer</option>
                <option value="otro" @selected(old('sexo', $p->sexo ?? '') == 'otro')>Otro</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium">Estado Civil</label>
            <select name="estado_civil" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                <option value="">— Selecciona —</option>
                @foreach (['Soltero', 'Casado', 'Divorciado', 'Viudo', 'Unión Libre'] as $ec)
                    <option value="{{ $ec }}" @selected(old('estado_civil', $p->estado_civil ?? '') == $ec)>{{ $ec }}</option>
                @endforeach
            </select>
        </div>

        {{-- Nacionalidad --}}
        <div>
            <label class="block text-sm font-medium">Nacionalidad</label>
            <select name="nacionalidad" required id="nacionalidad"
                class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                <option value="MEXICANA" @selected(old('nacionalidad', $p->nacionalidad ?? 'MEXICANA') == 'MEXICANA')>Mexicana</option>
                <option value="EXTRANJERA" @selected(old('nacionalidad', $p->nacionalidad ?? '') == 'EXTRANJERA')>Extranjera</option>
            </select>
        </div>

        {{-- Estado de Nacimiento: solo si es mexicana --}}
        <div id="estado_nacimiento_container">
            <label class="block text-sm font-medium">Estado de Nacimiento</label>
            <select name="estado_nacimiento" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                <option value="">NO ESPECIFICADO</option>
                @foreach ($estados as $estado)
                    <option value="{{ $estado->nombre }}" @selected(old('estado_nacimiento', $p->estado_nacimiento ?? '') == $estado->nombre)>
                        {{ $estado->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- País de Nacimiento: solo si es extranjera --}}
        <div id="pais_nacimiento_container" class="hidden">
            <label class="block text-sm font-medium">País de Nacimiento</label>
            <input type="text" name="pais_nacimiento"
                value="{{ old('pais_nacimiento', $p->pais_nacimiento ?? '') }}"
                placeholder="Ej. Estados Unidos, España, Argentina"
                class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
        </div>

        {{-- CURP: solo si es mexicana --}}
        <div id="curp-container">
            <label class="block text-sm font-medium">CURP</label>
            <input type="text" name="curp" value="{{ old('curp', $p->curp ?? '') }}" maxlength="18"
                placeholder="XXXX000000HNEXXX09" class="mt-1 w-full border-gray-300 rounded-md shadow-sm uppercase">
            @error('curp')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Pasaporte: solo si es extranjera --}}
        <div id="pasaporte-container" class="hidden">
            <label class="block text-sm font-medium">Pasaporte</label>
            <input type="text" name="pasaporte" value="{{ old('pasaporte', $p->pasaporte ?? '') }}"
                class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
        </div>
    </div>
</section>

<hr>

{{-- 2. Datos de contacto --}}
<section>
    <h3 class="text-lg font-bold text-gray-800 mb-1">Datos de contacto</h3>
    <p class="text-sm text-gray-500 mb-4">
        Facilita números y correos para alertas, recordatorios y seguimiento clínico.
    </p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium">Teléfono Principal</label>
            <input type="text" name="telefono_principal"
                value="{{ old('telefono_principal', $p->telefono_principal ?? '') }}"
                class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
        </div>
        <div>
            <label class="block text-sm font-medium">Correo Electrónico</label>
            <input type="email" name="correo_electronico"
                value="{{ old('correo_electronico', $p->correo_electronico ?? '') }}" placeholder="correo@ejemplo.com"
                class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
        </div>
        <div>
            <label class="block text-sm font-medium">Ocupación</label>
            <input type="text" name="ocupacion" value="{{ old('ocupacion', $p->ocupacion ?? '') }}"
                placeholder="Profesión o actividad" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
        </div>
        <div>
            <label class="block text-sm font-medium">Responsable</label>
            <input type="text" name="responsable_nombre"
                value="{{ old('responsable_nombre', $p->responsable_nombre ?? '') }}"
                placeholder="Nombre de responsable o tutor" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
        </div>
    </div>
</section>

<hr>

{{-- 3. Datos de salud --}}
<section>
    <h3 class="text-lg font-bold text-gray-800 mb-1">Datos de salud</h3>
    <p class="text-sm text-gray-500 mb-4">
        Anota información crítica para urgencias, alergias y seguimiento crónico.
    </p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium">Tipo Sanguíneo</label>
            <select name="tipo_sanguineo" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                <option value="">— Selecciona —</option>
                @foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $ts)
                    <option value="{{ $ts }}" @selected(old('tipo_sanguineo', $p->tipo_sanguineo ?? '') == $ts)>{{ $ts }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium">Alergias</label>
            <input type="text" name="alergias" value="{{ old('alergias', $p->alergias ?? '') }}"
                placeholder="Lista de alergias" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium">Enfermedades Crónicas</label>
            <textarea name="enfermedades_cronicas" rows="2"
                placeholder="Lista de enfermedades crónicas o antecedentes relevantes"
                class="mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('enfermedades_cronicas', $p->enfermedades_cronicas ?? '') }}</textarea>
        </div>
    </div>
</section>

<hr>

{{-- 4. Domicilio --}}
<section>
    <h3 class="text-lg font-bold text-gray-800 mb-1">Domicilio</h3>
    <p class="text-sm text-gray-500 mb-4">
        Ubica rápidamente al paciente. Los municipios se cargan según el estado.
    </p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium">Estado</label>
            <select name="estado_id" id="estado_id" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                <option value="">— Selecciona un estado —</option>
                @foreach ($estados as $estado)
                    <option value="{{ $estado->id }}" @selected(old('estado_id', $p->estado_id ?? '') == $estado->id)>
                        {{ $estado->nombre }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium">Municipio</label>
            <select name="municipio_id" id="municipio_id" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                <option value="">— Selecciona un municipio —</option>
                @foreach ($municipios ?? [] as $municipio)
                    <option value="{{ $municipio->id }}" @selected(old('municipio_id', $p->municipio_id ?? '') == $municipio->id)>
                        {{ $municipio->nombre }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium">Colonia</label>
            <input type="text" name="colonia" value="{{ old('colonia', $p->colonia ?? '') }}"
                class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
        </div>
        <div>
            <label class="block text-sm font-medium">Calle</label>
            <input type="text" name="calle" value="{{ old('calle', $p->calle ?? '') }}"
                class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
        </div>
        <div>
            <label class="block text-sm font-medium">Número Exterior</label>
            <input type="text" name="numero_exterior"
                value="{{ old('numero_exterior', $p->numero_exterior ?? '') }}"
                class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
        </div>
        <div>
            <label class="block text-sm font-medium">Número Interior</label>
            <input type="text" name="numero_interior"
                value="{{ old('numero_interior', $p->numero_interior ?? '') }}"
                class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
        </div>
    </div>
</section>

<hr>

{{-- 5. Estado del registro --}}
<section>
    <h3 class="text-lg font-bold text-gray-800 mb-1">Estado del registro</h3>
    <p class="text-sm text-gray-500 mb-4">Revisa los datos antes de guardar el expediente clínico.</p>
    <label class="inline-flex items-center">
        <input type="checkbox" name="activo" value="1" @checked(old('activo', $p->activo ?? true))
            class="rounded border-gray-300 text-blue-600 shadow-sm">
        <span class="ml-2 text-sm">Paciente activo</span>
    </label>
</section>
