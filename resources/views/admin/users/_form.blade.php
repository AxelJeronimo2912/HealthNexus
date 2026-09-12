@php $u = $user ?? null; @endphp

{{-- 1. Identidad del colaborador --}}
<section>
    <h3 class="text-lg font-bold text-gray-800 mb-1">Identidad del colaborador</h3>
    <p class="text-sm text-gray-500 mb-4">
        Introduce los datos personales tal como aparecen en su documentación oficial.
    </p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium">Nombre</label>
            <input type="text" name="nombre" value="{{ old('nombre', $u->nombre ?? '') }}" required
                class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
            @error('nombre')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-sm font-medium">Apellido Paterno</label>
            <input type="text" name="apellido_paterno"
                value="{{ old('apellido_paterno', $u->apellido_paterno ?? '') }}" required
                class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
            @error('apellido_paterno')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-sm font-medium">Apellido Materno</label>
            <input type="text" name="apellido_materno"
                value="{{ old('apellido_materno', $u->apellido_materno ?? '') }}" required
                class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
            @error('apellido_materno')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-sm font-medium">CURP</label>
            <input type="text" name="curp" value="{{ old('curp', $u->curp ?? '') }}" maxlength="18" required
                class="mt-1 w-full border-gray-300 rounded-md shadow-sm uppercase">
            @error('curp')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-sm font-medium">Fecha de Nacimiento</label>
            <input type="date" name="fecha_nacimiento"
                value="{{ old('fecha_nacimiento', $u && $u->fecha_nacimiento ? $u->fecha_nacimiento->format('Y-m-d') : '') }}"
                required class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
            @error('fecha_nacimiento')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-sm font-medium">Cédula Profesional</label>
            <input type="text" name="cedula_profesional"
                value="{{ old('cedula_profesional', $u->cedula_profesional ?? '') }}"
                class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
        </div>
    </div>
</section>

<hr>

{{-- 2. Contacto y acceso --}}
<section>
    <h3 class="text-lg font-bold text-gray-800 mb-1">Contacto y acceso</h3>
    <p class="text-sm text-gray-500 mb-4">
        Define cómo podremos contactar al colaborador y establece las credenciales de ingreso al sistema.
    </p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium">Teléfono</label>
            <input type="text" name="telefono" value="{{ old('telefono', $u->telefono ?? '') }}" required
                class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
            @error('telefono')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-sm font-medium">Teléfono Contacto</label>
            <input type="text" name="telefono_contacto"
                value="{{ old('telefono_contacto', $u->telefono_contacto ?? '') }}"
                class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium">Correo electrónico</label>
            <input type="email" name="email" value="{{ old('email', $u->email ?? '') }}" required
                class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
            @error('email')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-sm font-medium">
                Contraseña {{ $u ? '(dejar vacío para no cambiar)' : '*' }}
            </label>
            <input type="password" name="password" id="password" {{ $u ? '' : 'required' }}
                class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
            @error('password')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-sm font-medium">Confirmar Contraseña</label>
            <input type="password" name="password_confirmation" id="password_confirmation" {{ $u ? '' : 'required' }}
                class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
        </div>
    </div>

    <div class="mt-3">
        <p class="text-xs text-gray-500 mb-2">
            ¿Necesitas una contraseña segura? Genérala automáticamente.
        </p>
        <button type="button" onclick="generarPassword()"
            class="text-sm bg-gray-100 hover:bg-gray-200 px-3 py-1 rounded">
            Generar contraseña
        </button>
    </div>
</section>

<hr>

{{-- 3. Rol y servicio --}}
<section>
    <h3 class="text-lg font-bold text-gray-800 mb-1">Rol y servicio</h3>
    <p class="text-sm text-gray-500 mb-4">
        Selecciona el rol operativo y confirma la modalidad de servicio.
    </p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium">Tipo de servicio</label>
            <select name="tipo_servicio" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                <option value="presencial" @selected(old('tipo_servicio', $u->tipo_servicio ?? '') == 'presencial')>Presencial</option>
                <option value="virtual" @selected(old('tipo_servicio', $u->tipo_servicio ?? '') == 'virtual')>Virtual</option>
                <option value="mixto" @selected(old('tipo_servicio', $u->tipo_servicio ?? '') == 'mixto')>Mixto</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium">Rol</label>
            <select name="role" id="role_select" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                <option value="">-- Selecciona un rol --</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->name }}" @selected(old('role', $u?->getRoleNames()->first()) == $role->name)>
                        {{ ucfirst($role->name) }}
                    </option>
                @endforeach
            </select>
            @error('role')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- PIN condicional: solo visible si el rol seleccionado es médico --}}
        @php
            $rolActual = old('role', $u?->getRoleNames()->first() ?? '');
            $esMedicoActual = strtolower(trim($rolActual)) === 'medico';
        @endphp

        <div id="pin-container" class="md:col-span-2 {{ $esMedicoActual ? '' : 'hidden' }}">
            <label class="block text-sm font-medium">
                PIN de 4 dígitos (obligatorio para médicos)
            </label>

            <div class="mt-1 flex items-center gap-2">
                <input type="text" name="pin" id="pin_input" inputmode="numeric" pattern="\d{4}" maxlength="4"
                    value="{{ old('pin') }}" placeholder="{{ $u && $u->pin ? '••••' : '' }}"
                    class="w-40 border-gray-300 rounded-md shadow-sm text-center tracking-widest text-lg font-mono">

                <button type="button" onclick="generarPin()"
                    class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">
                    Generar PIN
                </button>

                <button type="button" onclick="copiarPin()"
                    class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">
                    Copiar
                </button>
            </div>

            @error('pin')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror

            <p class="text-xs text-gray-500 mt-1">
                Comparte este PIN con el médico de forma segura. Lo necesitará después de su contraseña para acceder.
            </p>
        </div>
    </div>
</section>

<hr>

{{-- 4. Documentos y firma --}}
<section>
    <h3 class="text-lg font-bold text-gray-800 mb-1">Documentos y firma</h3>
    <p class="text-sm text-gray-500 mb-4">
        Sube los archivos necesarios o dibuja la firma directamente en el lienzo interactivo.
    </p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium">Imagen de Perfil</label>
            <input type="file" name="foto_perfil" accept="image/*" class="mt-1 block w-full text-sm">
            @if ($u && $u->foto_perfil)
                <img src="{{ asset('storage/' . $u->foto_perfil) }}"
                    class="mt-2 w-20 h-20 rounded-full object-cover">
            @endif
        </div>
        <div>
            <label class="block text-sm font-medium">Firma (archivo)</label>
            <input type="file" name="firma_archivo" accept="image/*" class="mt-1 block w-full text-sm">
            @if ($u && $u->firma_archivo)
                <img src="{{ asset('storage/' . $u->firma_archivo) }}" class="mt-2 h-16 bg-white border">
            @endif
        </div>
    </div>

    <div class="mt-6">
        <label class="block text-sm font-medium mb-2">Firmar en pantalla</label>
        <canvas id="firmaCanvas" width="600" height="200"
            class="border-2 border-dashed border-gray-300 rounded-md w-full touch-none bg-white"></canvas>
        <input type="hidden" name="firma_canvas" id="firma_canvas_input">
        <div class="mt-2 flex justify-between items-center">
            <p class="text-xs text-gray-500">
                Dibuja la firma con tu dedo, stylus o mouse antes de guardar.
            </p>
            <button type="button" onclick="limpiarFirma()"
                class="text-sm bg-gray-100 hover:bg-gray-200 px-3 py-1 rounded">
                Limpiar firma
            </button>
        </div>
    </div>
</section>

<hr>

{{-- 5. Estado --}}
<section>
    <h3 class="text-lg font-bold text-gray-800 mb-1">Estado del colaborador</h3>
    <p class="text-sm text-gray-500 mb-4">
        Activa al empleado desde el primer día para que tenga acceso inmediato.
    </p>
    <label class="inline-flex items-center">
        <input type="checkbox" name="activo" value="1" @checked(old('activo', $u->activo ?? true))
            class="rounded border-gray-300 text-blue-600 shadow-sm">
        <span class="ml-2 text-sm">Activar colaborador al registrar</span>
    </label>
</section>
