<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-3">
            <div class="min-w-0">
                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight truncate">
                    Detalle del Colaborador
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Información completa, PIN y especialidades</p>
            </div>
            <a href="{{ route('admin.users.index') }}"
                class="bg-white hover:bg-indigo-50 text-slate-700 hover:text-indigo-600 border border-slate-200 hover:border-indigo-200 px-5 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 transition-all active:scale-95 group shrink-0">
                <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
                Volver
            </a>
        </div>
    </x-slot>

    @php
        $labelCls = 'text-[11px] font-bold text-slate-400 uppercase tracking-wider';
        $esMedico = $user->roles->contains(function ($rol) {
            $n = strtolower($rol->name);
            return str_contains($n, 'medic') || str_contains($n, 'doctor') || str_contains($n, 'médic');
        });
    @endphp

    <div class="py-8 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- ============ PIN RECIÉN GENERADO ============ --}}
        @if (session('pin_generado'))
            <div class="p-5 bg-amber-50 border border-amber-100 rounded-2xl flex items-start gap-3">
                <div
                    class="w-10 h-10 rounded-2xl bg-amber-100 flex items-center justify-center text-amber-600 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-extrabold text-amber-900">Nuevo PIN generado</p>
                    <p class="text-xs font-medium text-amber-700 mt-0.5">
                        Copia este PIN y compártelo con el médico. <strong>No se volverá a mostrar.</strong>
                    </p>
                    <div class="flex flex-wrap items-center gap-2 mt-3">
                        <span
                            class="font-mono text-2xl font-black tracking-widest text-amber-900 bg-white px-4 py-2 rounded-xl border border-amber-200">
                            {{ session('pin_generado') }}
                        </span>
                        <button type="button"
                            onclick="navigator.clipboard.writeText('{{ session('pin_generado') }}').then(() => alert('PIN copiado'))"
                            class="px-3 py-2 bg-amber-100 hover:bg-amber-200 active:scale-95 text-amber-800 rounded-xl text-xs font-bold transition-all inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            Copiar
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- ============ FICHA PRINCIPAL ============ --}}
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-6">

            {{-- Avatar + nombre --}}
            <div class="flex flex-col sm:flex-row sm:items-center gap-4 pb-5 border-b border-slate-100">
                @if ($user->foto_perfil)
                    <img src="{{ asset('storage/' . $user->foto_perfil) }}"
                        class="w-20 h-20 rounded-3xl object-cover border border-slate-200 shrink-0">
                @else
                    <div
                        class="w-20 h-20 rounded-3xl bg-indigo-50 text-indigo-700 border border-indigo-100 flex items-center justify-center text-2xl font-black shrink-0">
                        {{ strtoupper(substr($user->nombre ?? $user->name, 0, 1)) }}
                    </div>
                @endif

                <div class="flex-1 min-w-0">
                    <p class="{{ $labelCls }}">Colaborador</p>
                    <p class="text-2xl font-black text-slate-800 tracking-tight mt-0.5 truncate">
                        {{ $user->nombre_completo }}
                    </p>
                    <p class="text-xs font-medium text-slate-400 mt-1">{{ $user->email }}</p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <span
                        class="px-2.5 py-1 bg-indigo-50 text-indigo-700 border border-indigo-100 rounded-full text-[10px] font-bold uppercase tracking-wide">
                        {{ $user->getRoleNames()->first() }}
                    </span>
                    @if ($user->activo)
                        <span
                            class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-full text-[10px] font-bold uppercase tracking-wide">
                            Activo
                        </span>
                    @else
                        <span
                            class="px-2.5 py-1 bg-rose-50 text-rose-700 border border-rose-100 rounded-full text-[10px] font-bold uppercase tracking-wide">
                            Inactivo
                        </span>
                    @endif
                </div>
            </div>

            {{-- Datos en grid --}}
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                    <dt class="{{ $labelCls }}">CURP</dt>
                    <dd class="mt-1">
                        @if ($user->curp)
                            <span
                                class="px-2 py-1 bg-slate-100 text-slate-700 rounded-lg text-[11px] font-mono font-bold">
                                {{ $user->curp }}
                            </span>
                        @else
                            <span class="text-slate-300 text-xs">—</span>
                        @endif
                    </dd>
                </div>

                <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                    <dt class="{{ $labelCls }}">Fecha de nacimiento</dt>
                    <dd class="text-xs font-extrabold text-slate-800 mt-1">
                        {{ $user->fecha_nacimiento?->format('d/m/Y') ?? '—' }}
                    </dd>
                </div>

                <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                    <dt class="{{ $labelCls }}">Cédula profesional</dt>
                    <dd class="text-xs font-extrabold text-slate-800 mt-1">
                        {{ $user->cedula_profesional ?? '—' }}
                    </dd>
                </div>

                <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                    <dt class="{{ $labelCls }}">Tipo de servicio</dt>
                    <dd class="text-xs font-extrabold text-slate-800 mt-1">
                        {{ ucfirst($user->tipo_servicio ?? '—') }}
                    </dd>
                </div>

                <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                    <dt class="{{ $labelCls }}">Teléfono</dt>
                    <dd class="text-xs font-extrabold text-slate-800 mt-1">{{ $user->telefono ?? '—' }}</dd>
                </div>

                <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                    <dt class="{{ $labelCls }}">Teléfono de contacto</dt>
                    <dd class="text-xs font-extrabold text-slate-800 mt-1">{{ $user->telefono_contacto ?? '—' }}</dd>
                </div>

                <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3 sm:col-span-2">
                    <dt class="{{ $labelCls }}">Correo electrónico</dt>
                    <dd class="text-xs font-extrabold text-slate-800 mt-1">{{ $user->email }}</dd>
                </div>
            </dl>

            {{-- ============ PIN DEL MÉDICO ============ --}}
            @if (strtolower($user->getRoleNames()->first() ?? '') === 'medico')
                <div class="border-t border-slate-100 pt-5">
                    <p class="{{ $labelCls }} mb-3">PIN de acceso</p>

                    @if ($user->pin)
                        <div
                            class="bg-emerald-50/60 border border-emerald-100 rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-2xl bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-extrabold text-emerald-800">PIN configurado</p>
                                    <p class="text-[11px] font-medium text-emerald-600 mt-0.5">
                                        Por seguridad, el PIN está hasheado y no puede mostrarse.
                                    </p>
                                </div>
                            </div>

                            <form action="{{ route('admin.users.regenerar-pin', $user) }}" method="POST"
                                onsubmit="return confirm('¿Regenerar el PIN? El médico deberá usar el nuevo PIN.')"
                                class="shrink-0">
                                @csrf
                                <button type="submit"
                                    class="px-4 py-2.5 bg-amber-500 hover:bg-amber-600 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all whitespace-nowrap">
                                    Regenerar PIN
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="bg-rose-50/60 border border-rose-100 rounded-2xl p-4 flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-2xl bg-rose-100 flex items-center justify-center text-rose-600 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-extrabold text-rose-800">Sin PIN configurado</p>
                                <p class="text-[11px] font-medium text-rose-600 mt-0.5">
                                    Este médico no podrá iniciar sesión hasta que se le asigne un PIN.
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            {{-- ============ FIRMAS ============ --}}
            @if ($user->firma_archivo || $user->firma_canvas)
                <div class="border-t border-slate-100 pt-5">
                    <p class="{{ $labelCls }} mb-3">Firmas registradas</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @if ($user->firma_archivo)
                            <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Firma
                                    (archivo)</p>
                                <img src="{{ asset('storage/' . $user->firma_archivo) }}"
                                    class="h-16 bg-white border border-slate-200 rounded-lg px-2">
                            </div>
                        @endif
                        @if ($user->firma_canvas)
                            <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Firma en
                                    pantalla</p>
                                <img src="{{ $user->firma_canvas }}"
                                    class="h-16 bg-white border border-slate-200 rounded-lg px-2">
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- ============ ESPECIALIDADES ============ --}}
            @if ($esMedico)
                <div class="border-t border-slate-100 pt-5">
                    <div class="flex flex-wrap justify-between items-center gap-2 mb-3">
                        <p class="{{ $labelCls }}">Especialidades médicas</p>
                        <a href="{{ route('admin.users.especialidades', $user) }}"
                            class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 active:scale-95 text-indigo-700 border border-indigo-100 rounded-lg text-[11px] font-bold transition-all">
                            Gestionar →
                        </a>
                    </div>

                    @if ($user->especialidades->isEmpty())
                        <div class="py-6 text-center">
                            <p class="text-xs font-semibold text-slate-400">Sin especialidades asignadas.</p>
                        </div>
                    @else
                        <div class="flex flex-wrap gap-2">
                            @foreach ($user->especialidades as $esp)
                                <span
                                    class="px-3 py-1.5 rounded-full text-[11px] font-bold border
                                    {{ $esp->pivot->es_principal ? 'bg-amber-50 text-amber-700 border-amber-100' : 'bg-indigo-50 text-indigo-700 border-indigo-100' }}">
                                    {{ $esp->nombre }}
                                    @if ($esp->pivot->es_principal)
                                        <span class="ml-1">★</span>
                                    @endif
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif

            {{-- ============ ACCIONES ============ --}}
            <div class="pt-5 border-t border-slate-100 flex flex-wrap gap-2">
                <a href="{{ route('admin.users.edit', $user) }}"
                    class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Editar
                </a>
                <a href="{{ route('admin.users.index') }}"
                    class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-600 rounded-xl text-xs font-bold transition-all">
                    Volver
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
