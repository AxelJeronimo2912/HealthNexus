@php $u = $user ?? null; @endphp

@php
    $inputCls =
        'w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-indigo-500 focus:ring-0 outline-none';
    $labelCls = 'block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5';
    $errorCls = 'text-rose-600 text-xs mt-1 font-medium';
    $sectionTitleCls = 'font-extrabold text-slate-800 text-base';
    $sectionSubCls = 'text-[11px] text-slate-400 mt-0.5';
@endphp

{{-- 1. Identidad del colaborador --}}
<section>
    <div class="flex items-center gap-3 mb-4">
        <div class="w-8 h-8 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
        </div>
        <div>
            <h3 class="{{ $sectionTitleCls }}">Identidad del colaborador</h3>
            <p class="{{ $sectionSubCls }}">Introduce los datos personales tal como aparecen en su documentación
                oficial.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="{{ $labelCls }}">Nombre *</label>
            <input type="text" name="nombre" value="{{ old('nombre', $u->nombre ?? '') }}" required
                class="{{ $inputCls }}">
            @error('nombre')
                <p class="{{ $errorCls }}">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="{{ $labelCls }}">Apellido Paterno *</label>
            <input type="text" name="apellido_paterno"
                value="{{ old('apellido_paterno', $u->apellido_paterno ?? '') }}" required class="{{ $inputCls }}">
            @error('apellido_paterno')
                <p class="{{ $errorCls }}">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="{{ $labelCls }}">Apellido Materno *</label>
            <input type="text" name="apellido_materno"
                value="{{ old('apellido_materno', $u->apellido_materno ?? '') }}" required
                class="{{ $inputCls }}">
            @error('apellido_materno')
                <p class="{{ $errorCls }}">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="{{ $labelCls }}">CURP *</label>
            <input type="text" name="curp" value="{{ old('curp', $u->curp ?? '') }}" maxlength="18" required
                class="{{ $inputCls }} uppercase font-mono">
            @error('curp')
                <p class="{{ $errorCls }}">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="{{ $labelCls }}">Fecha de Nacimiento *</label>
            <input type="date" name="fecha_nacimiento"
                value="{{ old('fecha_nacimiento', $u && $u->fecha_nacimiento ? $u->fecha_nacimiento->format('Y-m-d') : '') }}"
                required class="{{ $inputCls }}">
            @error('fecha_nacimiento')
                <p class="{{ $errorCls }}">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="{{ $labelCls }}">
                Cédula Profesional <span id="cedula-required" class="text-rose-500 hidden">*</span>
            </label>
            <input type="text" name="cedula_profesional" id="cedula_input"
                value="{{ old('cedula_profesional', $u->cedula_profesional ?? '') }}" class="{{ $inputCls }}">
            @error('cedula_profesional')
                <p class="{{ $errorCls }}">{{ $message }}</p>
            @enderror
        </div>
    </div>
</section>

<div class="border-t border-slate-100"></div>

{{-- 2. Contacto y acceso --}}
<section>
    <div class="flex items-center gap-3 mb-4">
        <div class="w-8 h-8 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
        </div>
        <div>
            <h3 class="{{ $sectionTitleCls }}">Contacto y acceso</h3>
            <p class="{{ $sectionSubCls }}">Define cómo podremos contactar al colaborador y establece las credenciales
                de ingreso.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="{{ $labelCls }}">Teléfono *</label>
            <input type="text" name="telefono" value="{{ old('telefono', $u->telefono ?? '') }}" required
                class="{{ $inputCls }}">
            @error('telefono')
                <p class="{{ $errorCls }}">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="{{ $labelCls }}">Teléfono Contacto</label>
            <input type="text" name="telefono_contacto"
                value="{{ old('telefono_contacto', $u->telefono_contacto ?? '') }}" class="{{ $inputCls }}">
            @error('telefono_contacto')
                <p class="{{ $errorCls }}">{{ $message }}</p>
            @enderror
        </div>
        <div class="md:col-span-2">
            <label class="{{ $labelCls }}">Correo electrónico *</label>
            <input type="email" name="email" value="{{ old('email', $u->email ?? '') }}" required
                class="{{ $inputCls }}">
            @error('email')
                <p class="{{ $errorCls }}">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="{{ $labelCls }}">
                Contraseña
                @if ($u)
                    <span class="text-slate-400 normal-case font-medium">(dejar vacío para no cambiar)</span>
                @else
                    <span class="text-rose-500">*</span>
                @endif
            </label>
            <input type="password" name="password" id="password" {{ $u ? '' : 'required' }}
                class="{{ $inputCls }}">
            @error('password')
                <p class="{{ $errorCls }}">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="{{ $labelCls }}">
                Confirmar Contraseña
                @if (!$u)
                    <span class="text-rose-500">*</span>
                @endif
            </label>
            <input type="password" name="password_confirmation" id="password_confirmation" {{ $u ? '' : 'required' }}
                class="{{ $inputCls }}">
        </div>
    </div>

    <div class="mt-4 p-3 bg-slate-50/70 border border-slate-100 rounded-2xl">
        <p class="text-[11px] font-medium text-slate-500 mb-2">
            ¿Necesitas una contraseña segura? Genérala automáticamente.
        </p>
        <button type="button" onclick="generarPassword()"
            class="px-3.5 py-2 bg-indigo-50 hover:bg-indigo-100 active:scale-95 text-indigo-700 border border-indigo-100 rounded-xl text-[11px] font-bold transition-all">
            Generar contraseña
        </button>
    </div>
</section>

<div class="border-t border-slate-100"></div>

{{-- 3. Rol y servicio --}}
<section>
    <div class="flex items-center gap-3 mb-4">
        <div class="w-8 h-8 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
        </div>
        <div>
            <h3 class="{{ $sectionTitleCls }}">Rol y servicio</h3>
            <p class="{{ $sectionSubCls }}">Selecciona el rol operativo y confirma la modalidad de servicio.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="{{ $labelCls }}">Tipo de servicio *</label>
            <select name="tipo_servicio" required class="{{ $inputCls }}">
                <option value="presencial" @selected(old('tipo_servicio', $u->tipo_servicio ?? '') == 'presencial')>Presencial</option>
                <option value="virtual" @selected(old('tipo_servicio', $u->tipo_servicio ?? '') == 'virtual')>Virtual</option>
                <option value="mixto" @selected(old('tipo_servicio', $u->tipo_servicio ?? '') == 'mixto')>Mixto</option>
            </select>
            @error('tipo_servicio')
                <p class="{{ $errorCls }}">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="{{ $labelCls }}">Rol *</label>
            <select name="role" id="role_select" required class="{{ $inputCls }}">
                <option value="">— Selecciona un rol —</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->name }}" @selected(old('role', $u?->getRoleNames()->first()) == $role->name)>
                        {{ ucfirst($role->name) }}
                    </option>
                @endforeach
            </select>
            @error('role')
                <p class="{{ $errorCls }}">{{ $message }}</p>
            @enderror
        </div>

        @php
            $rolActual = old('role', $u?->getRoleNames()->first() ?? '');
            $rolLower = strtolower(trim($rolActual));
            $esMedicoActual = $rolLower === 'm' || str_starts_with($rolLower, 'medic');
        @endphp

        <div id="pin-container" class="md:col-span-2 {{ $esMedicoActual ? '' : 'hidden' }}">
            <div class="p-4 bg-amber-50/60 border border-amber-100 rounded-2xl space-y-3">
                <div class="flex items-center gap-2">
                    <div
                        class="w-7 h-7 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600 shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                    </div>
                    <p class="{{ $labelCls }} mb-0">PIN de 4 dígitos *</p>
                </div>
                <p class="text-[11px] font-medium text-amber-700">
                    Obligatorio para médicos. Lo necesitará después de su contraseña para acceder.
                </p>

                <div class="flex flex-wrap items-center gap-2">
                    <input type="text" name="pin" id="pin_input" inputmode="numeric" pattern="\d{4}"
                        maxlength="4" value="{{ old('pin') }}"
                        placeholder="{{ $u && $u->pin ? '••••' : '0000' }}"
                        class="w-32 bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-center tracking-widest text-lg font-mono text-slate-800 focus:bg-white focus:border-indigo-500 focus:ring-0 outline-none">

                    <button type="button" onclick="generarPin()"
                        class="px-3.5 py-2 bg-white hover:bg-amber-50 active:scale-95 text-amber-700 border border-amber-200 rounded-xl text-[11px] font-bold transition-all">
                        Generar PIN
                    </button>

                    <button type="button" onclick="copiarPin()"
                        class="px-3.5 py-2 bg-white hover:bg-amber-50 active:scale-95 text-amber-700 border border-amber-200 rounded-xl text-[11px] font-bold transition-all">
                        Copiar
                    </button>
                </div>

                @error('pin')
                    <p class="{{ $errorCls }}">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>
</section>

<div class="border-t border-slate-100"></div>

{{-- 4. Documentos y firma --}}
<section>
    <div class="flex items-center gap-3 mb-4">
        <div class="w-8 h-8 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
            </svg>
        </div>
        <div>
            <h3 class="{{ $sectionTitleCls }}">Documentos y firma</h3>
            <p class="{{ $sectionSubCls }}">
                Sube los archivos necesarios o dibuja la firma directamente en el lienzo interactivo.
                <strong class="text-slate-600">La firma es obligatoria.</strong>
            </p>
        </div>
    </div>

    @php
        $tieneFirmaPrevia = $u && ($u->firma_archivo || $u->firma_canvas);
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
            <label class="{{ $labelCls }}">Imagen de Perfil</label>
            <input type="file" name="foto_perfil" accept="image/*"
                class="mt-1 block w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-[11px] file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 file:cursor-pointer cursor-pointer bg-slate-50 border border-slate-200 rounded-xl">
            @error('foto_perfil')
                <p class="{{ $errorCls }}">{{ $message }}</p>
            @enderror
            @if ($u && $u->foto_perfil)
                <img src="{{ asset('storage/' . $u->foto_perfil) }}"
                    class="mt-3 w-20 h-20 rounded-2xl object-cover border border-slate-200">
            @endif
        </div>

        <div>
            <label class="{{ $labelCls }}">
                Firma (archivo)
                @if (!$tieneFirmaPrevia)
                    <span class="text-rose-500">*</span>
                @endif
            </label>
            <input type="file" name="firma_archivo" accept="image/*"
                class="mt-1 block w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-[11px] file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 file:cursor-pointer cursor-pointer bg-slate-50 border border-slate-200 rounded-xl">

            @error('firma_archivo')
                <p class="{{ $errorCls }}">{{ $message }}</p>
            @enderror

            @if ($u && $u->firma_archivo)
                <div class="mt-3 bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                    <img src="{{ asset('storage/' . $u->firma_archivo) }}"
                        class="h-16 bg-white border border-slate-200 rounded-lg px-2">
                    <p class="text-[10px] font-medium text-slate-400 mt-2">
                        Ya existe una firma registrada. Sube otra solo si deseas reemplazarla.
                    </p>
                </div>
            @endif
        </div>
    </div>

    <div class="mt-5">
        <label class="{{ $labelCls }} mb-2">
            Firmar en pantalla
            @if (!$tieneFirmaPrevia)
                <span class="text-rose-500">*</span>
            @endif
        </label>
        <canvas id="firmaCanvas" width="600" height="200"
            class="border-2 border-dashed border-slate-300 rounded-2xl w-full touch-none bg-white"></canvas>
        <input type="hidden" name="firma_canvas" id="firma_canvas_input">

        @error('firma_canvas')
            <p class="{{ $errorCls }}">{{ $message }}</p>
        @enderror

        <div class="mt-3 flex flex-wrap justify-between items-center gap-2">
            <p class="text-[11px] font-medium text-slate-400">
                Dibuja la firma con tu dedo, stylus o mouse antes de guardar.
            </p>
            <button type="button" onclick="limpiarFirma()"
                class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-600 rounded-xl text-[11px] font-bold transition-all">
                Limpiar firma
            </button>
        </div>
    </div>
</section>

<div class="border-t border-slate-100"></div>

{{-- 5. Estado --}}
<section>
    <div class="flex items-center gap-3 mb-4">
        <div class="w-8 h-8 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
            </svg>
        </div>
        <div>
            <h3 class="{{ $sectionTitleCls }}">Estado del colaborador</h3>
            <p class="{{ $sectionSubCls }}">Activa al empleado desde el primer día para que tenga acceso inmediato.
            </p>
        </div>
    </div>

    <label
        class="inline-flex items-center gap-2.5 cursor-pointer px-3 py-2 bg-emerald-50/60 border border-emerald-100 rounded-xl">
        <input type="checkbox" name="activo" value="1" @checked(old('activo', $u->activo ?? true))
            class="w-4 h-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 focus:ring-0 cursor-pointer">
        <span class="text-xs font-bold text-emerald-700">Activar colaborador al registrar</span>
    </label>
</section>
