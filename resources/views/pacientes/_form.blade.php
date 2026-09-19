@php $p = $paciente ?? null; @endphp

<div x-data="pacienteForm({
    modo: '{{ $p ? 'edit' : 'create' }}',
    pacienteId: {{ $p->id ?? 'null' }},
    erroresServidor: @json($errors->toArray())
})" x-init="init()">

    {{-- ================= BANNER DE ERRORES ================= --}}
    <div x-show="hayErrores()" x-cloak x-transition
        class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-sm flex items-start gap-3 shadow-sm">
        <div class="w-8 h-8 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01M4.93 19h14.14a2 2 0 001.74-2.99l-7.07-12.14a2 2 0 00-3.48 0L2.19 16.01A2 2 0 004.93 19z" />
            </svg>
        </div>
        <div>
            <p class="font-bold">Corrige los siguientes errores:</p>
            <ul class="mt-1 list-disc list-inside space-y-0.5">
                <template x-for="(msg, campo) in errores" :key="campo">
                    <li x-text="Array.isArray(msg) ? msg[0] : msg"></li>
                </template>
            </ul>
        </div>
    </div>

    <form x-ref="formulario" @submit.prevent="enviar" method="POST"
        action="{{ $p ? route('pacientes.update', $p) : route('pacientes.store') }}" class="space-y-8">

        @csrf
        @if ($p)
            @method('PUT')
        @endif

        {{-- 1. IDENTIDAD --}}
        <section>
            <h3 class="text-lg font-bold text-gray-800 mb-1">Identidad del paciente</h3>
            <p class="text-sm text-gray-500 mb-4">Datos personales tal como aparecen en su documentación oficial.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- Nombre --}}
                <div>
                    <label class="block text-sm font-medium">Nombre <span class="text-rose-500">*</span></label>
                    <input type="text" name="nombre" value="{{ old('nombre', $p->nombre ?? '') }}" required
                        maxlength="100"
                        :class="tieneError('nombre') ?
                            'border-rose-400 focus:border-rose-500 focus:ring-rose-200' :
                            'border-gray-300'"
                        class="mt-1 w-full rounded-md shadow-sm border transition">
                    <p x-show="tieneError('nombre')" x-text="mensajeError('nombre')" class="text-rose-600 text-xs mt-1">
                    </p>
                </div>

                {{-- Apellido Paterno --}}
                <div>
                    <label class="block text-sm font-medium">Apellido Paterno <span
                            class="text-rose-500">*</span></label>
                    <input type="text" name="apellido_paterno"
                        value="{{ old('apellido_paterno', $p->apellido_paterno ?? '') }}" required maxlength="100"
                        :class="tieneError('apellido_paterno') ? 'border-rose-400' : 'border-gray-300'"
                        class="mt-1 w-full rounded-md shadow-sm border transition">
                    <p x-show="tieneError('apellido_paterno')" x-text="mensajeError('apellido_paterno')"
                        class="text-rose-600 text-xs mt-1"></p>
                </div>

                {{-- Apellido Materno --}}
                <div>
                    <label class="block text-sm font-medium">Apellido Materno</label>
                    <input type="text" name="apellido_materno"
                        value="{{ old('apellido_materno', $p->apellido_materno ?? '') }}" maxlength="100"
                        :class="tieneError('apellido_materno') ? 'border-rose-400' : 'border-gray-300'"
                        class="mt-1 w-full rounded-md shadow-sm border transition">
                    <p x-show="tieneError('apellido_materno')" x-text="mensajeError('apellido_materno')"
                        class="text-rose-600 text-xs mt-1"></p>
                </div>

                {{-- Fecha de Nacimiento --}}
                <div>
                    <label class="block text-sm font-medium">Fecha de Nacimiento <span
                            class="text-rose-500">*</span></label>
                    <input type="date" name="fecha_nacimiento" id="fecha_nacimiento"
                        value="{{ old('fecha_nacimiento', $p && $p->fecha_nacimiento ? $p->fecha_nacimiento->format('Y-m-d') : '') }}"
                        required max="{{ date('Y-m-d') }}"
                        :class="tieneError('fecha_nacimiento') ? 'border-rose-400' : 'border-gray-300'"
                        class="mt-1 w-full rounded-md shadow-sm border transition">
                    <p x-show="tieneError('fecha_nacimiento')" x-text="mensajeError('fecha_nacimiento')"
                        class="text-rose-600 text-xs mt-1"></p>
                </div>

                {{-- Edad --}}
                <div>
                    <label class="block text-sm font-medium">Edad</label>
                    <input type="text" id="edad" readonly
                        class="mt-1 w-full border-gray-300 rounded-md shadow-sm bg-gray-100">
                </div>

                {{-- Sexo --}}
                <div>
                    <label class="block text-sm font-medium">Sexo <span class="text-rose-500">*</span></label>
                    <select name="sexo" required :class="tieneError('sexo') ? 'border-rose-400' : 'border-gray-300'"
                        class="mt-1 w-full rounded-md shadow-sm border transition">
                        <option value="">— Selecciona —</option>
                        <option value="hombre" @selected(old('sexo', $p->sexo ?? '') == 'hombre')>Hombre</option>
                        <option value="mujer" @selected(old('sexo', $p->sexo ?? '') == 'mujer')>Mujer</option>
                        <option value="otro" @selected(old('sexo', $p->sexo ?? '') == 'otro')>Otro</option>
                    </select>
                    <p x-show="tieneError('sexo')" x-text="mensajeError('sexo')" class="text-rose-600 text-xs mt-1">
                    </p>
                </div>

                {{-- Estado Civil --}}
                <div>
                    <label class="block text-sm font-medium">Estado Civil</label>
                    <select name="estado_civil" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">— Selecciona —</option>
                        @foreach (['Soltero', 'Casado', 'Divorciado', 'Viudo', 'Unión Libre'] as $ec)
                            <option value="{{ $ec }}" @selected(old('estado_civil', $p->estado_civil ?? '') == $ec)>
                                {{ $ec }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Nacionalidad --}}
                <div>
                    <label class="block text-sm font-medium">Nacionalidad <span class="text-rose-500">*</span></label>
                    <select name="nacionalidad" required id="nacionalidad"
                        :class="tieneError('nacionalidad') ? 'border-rose-400' : 'border-gray-300'"
                        class="mt-1 w-full rounded-md shadow-sm border transition">
                        <option value="MEXICANA" @selected(old('nacionalidad', $p->nacionalidad ?? 'MEXICANA') == 'MEXICANA')>Mexicana</option>
                        <option value="EXTRANJERA" @selected(old('nacionalidad', $p->nacionalidad ?? '') == 'EXTRANJERA')>Extranjera</option>
                    </select>
                    <p x-show="tieneError('nacionalidad')" x-text="mensajeError('nacionalidad')"
                        class="text-rose-600 text-xs mt-1"></p>
                </div>

                {{-- Estado de Nacimiento --}}
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

                {{-- País de Nacimiento --}}
                <div id="pais_nacimiento_container" class="hidden">
                    <label class="block text-sm font-medium">País de Nacimiento</label>
                    <input type="text" name="pais_nacimiento"
                        value="{{ old('pais_nacimiento', $p->pais_nacimiento ?? '') }}" maxlength="100"
                        placeholder="Ej. Estados Unidos, España, Argentina"
                        class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                </div>

                {{-- CURP --}}
                <div id="curp-container">
                    <label class="block text-sm font-medium">CURP</label>
                    <input type="text" name="curp" value="{{ old('curp', $p->curp ?? '') }}" maxlength="18"
                        placeholder="XXXX000000HNEXXX09"
                        :class="tieneError('curp') ? 'border-rose-400' : 'border-gray-300'"
                        class="mt-1 w-full rounded-md shadow-sm border uppercase transition">
                    <p x-show="tieneError('curp')" x-text="mensajeError('curp')" class="text-rose-600 text-xs mt-1">
                    </p>
                </div>

                {{-- Pasaporte --}}
                <div id="pasaporte-container" class="hidden">
                    <label class="block text-sm font-medium">Pasaporte</label>
                    <input type="text" name="pasaporte" value="{{ old('pasaporte', $p->pasaporte ?? '') }}"
                        maxlength="50" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                </div>
            </div>
        </section>

        <hr>

        {{-- 2. CONTACTO --}}
        <section>
            <h3 class="text-lg font-bold text-gray-800 mb-1">Datos de contacto</h3>
            <p class="text-sm text-gray-500 mb-4">Facilita números y correos para alertas, recordatorios y seguimiento
                clínico.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium">Teléfono Principal</label>
                    <input type="tel" name="telefono_principal"
                        value="{{ old('telefono_principal', $p->telefono_principal ?? '') }}" maxlength="20"
                        placeholder="Ej. 5512345678"
                        :class="tieneError('telefono_principal') ? 'border-rose-400' : 'border-gray-300'"
                        class="mt-1 w-full rounded-md shadow-sm border transition">
                    <p x-show="tieneError('telefono_principal')" x-text="mensajeError('telefono_principal')"
                        class="text-rose-600 text-xs mt-1"></p>
                </div>

                <div>
                    <label class="block text-sm font-medium">Correo Electrónico</label>
                    <input type="email" name="correo_electronico"
                        value="{{ old('correo_electronico', $p->correo_electronico ?? '') }}" maxlength="255"
                        placeholder="correo@ejemplo.com"
                        :class="tieneError('correo_electronico') ? 'border-rose-400' : 'border-gray-300'"
                        class="mt-1 w-full rounded-md shadow-sm border transition">
                    <p x-show="tieneError('correo_electronico')" x-text="mensajeError('correo_electronico')"
                        class="text-rose-600 text-xs mt-1"></p>
                </div>

                <div>
                    <label class="block text-sm font-medium">Ocupación</label>
                    <input type="text" name="ocupacion" value="{{ old('ocupacion', $p->ocupacion ?? '') }}"
                        maxlength="100" placeholder="Profesión o actividad"
                        class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium">Responsable</label>
                    <input type="text" name="responsable_nombre"
                        value="{{ old('responsable_nombre', $p->responsable_nombre ?? '') }}" maxlength="150"
                        placeholder="Nombre de responsable o tutor"
                        class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                </div>
            </div>
        </section>

        <hr>

        {{-- 3. SALUD --}}
        <section>
            <h3 class="text-lg font-bold text-gray-800 mb-1">Datos de salud</h3>
            <p class="text-sm text-gray-500 mb-4">Anota información crítica para urgencias, alergias y seguimiento
                crónico.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium">Tipo Sanguíneo</label>
                    <select name="tipo_sanguineo" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">— Selecciona —</option>
                        @foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $ts)
                            <option value="{{ $ts }}" @selected(old('tipo_sanguineo', $p->tipo_sanguineo ?? '') == $ts)>
                                {{ $ts }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium">Alergias</label>
                    <input type="text" name="alergias" value="{{ old('alergias', $p->alergias ?? '') }}"
                        maxlength="255" placeholder="Lista de alergias"
                        class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium">Enfermedades Crónicas</label>
                    <textarea name="enfermedades_cronicas" rows="2" maxlength="2000"
                        placeholder="Lista de enfermedades crónicas o antecedentes relevantes"
                        class="mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('enfermedades_cronicas', $p->enfermedades_cronicas ?? '') }}</textarea>
                </div>
            </div>
        </section>

        <hr>

        {{-- DOMICILIO --}}
        <section>
            <h3 class="text-lg font-bold text-gray-800 mb-1">Domicilio</h3>
            <p class="text-sm text-gray-500 mb-4">Ubica rápidamente al paciente. Los municipios se cargan según el
                estado.</p>

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
                    <select name="municipio_id" id="municipio_id"
                        class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
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
                        maxlength="100" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium">Calle</label>
                    <input type="text" name="calle" value="{{ old('calle', $p->calle ?? '') }}"
                        maxlength="150" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium">Número Exterior</label>
                    <input type="text" name="numero_exterior"
                        value="{{ old('numero_exterior', $p->numero_exterior ?? '') }}" maxlength="20"
                        class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium">Número Interior</label>
                    <input type="text" name="numero_interior"
                        value="{{ old('numero_interior', $p->numero_interior ?? '') }}" maxlength="20"
                        class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                </div>
            </div>
        </section>

        <hr>

        {{--  ESTADO --}}
        <section>
            <h3 class="text-lg font-bold text-gray-800 mb-1">Estado del registro</h3>
            <p class="text-sm text-gray-500 mb-4">Revisa los datos antes de guardar el expediente clínico.</p>
            <label class="inline-flex items-center">
                <input type="checkbox" name="activo" value="1" @checked(old('activo', $p->activo ?? true))
                    class="rounded border-gray-300 text-blue-600 shadow-sm">
                <span class="ml-2 text-sm">Paciente activo</span>
            </label>
        </section>

        {{-- BOTONES --}}
        <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
            <button type="button" @click="cerrarModal()" :disabled="enviando"
                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm disabled:opacity-50">
                Cancelar
            </button>
            <button type="submit" :disabled="enviando"
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                <svg x-show="enviando" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                </svg>
                <span
                    x-text="enviando ? 'Guardando...' : '{{ $p ? 'Actualizar Paciente' : 'Guardar Paciente' }}'"></span>
            </button>
        </div>
    </form>
</div>
