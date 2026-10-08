<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-3">
            <div class="min-w-0">
                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight truncate">
                    Detalle del Rol: {{ ucfirst($role->name) }}
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Permisos y usuarios con este rol</p>
            </div>
            <a href="{{ route('admin.roles.index') }}"
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
    @endphp

    <div class="py-8 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-6">

            {{-- Encabezado del rol --}}
            <div class="border-b border-slate-100 pb-5">
                <p class="{{ $labelCls }}">Rol</p>
                <p class="text-3xl font-black text-slate-800 tracking-tight mt-0.5">
                    {{ ucfirst($role->name) }}
                </p>
            </div>

            {{-- Permisos --}}
            <div>
                <p class="{{ $labelCls }} mb-3">Permisos asignados</p>
                @if ($role->permissions->isEmpty())
                    <span
                        class="px-2.5 py-1 bg-slate-100 text-slate-500 rounded-full text-[10px] font-bold uppercase tracking-wide">
                        Sin permisos asignados
                    </span>
                @else
                    <div class="flex flex-wrap gap-1.5">
                        @foreach ($role->permissions as $perm)
                            <span
                                class="px-2.5 py-1 bg-indigo-50 text-indigo-700 border border-indigo-100 rounded-full text-[10px] font-bold uppercase tracking-wide">
                                {{ $perm->name }}
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Usuarios con este rol --}}
            <div class="border-t border-slate-100 pt-5">
                <p class="{{ $labelCls }} mb-3">
                    Usuarios con este rol
                    <span class="ml-1 text-slate-300">·</span>
                    <span class="text-slate-600">{{ $role->users->count() }}</span>
                </p>

                @if ($role->users->isEmpty())
                    <p class="text-xs font-semibold text-slate-400">Ninguno.</p>
                @else
                    <ul class="space-y-2">
                        @foreach ($role->users as $u)
                            <li
                                class="bg-slate-50/70 border border-slate-100 rounded-2xl px-4 py-3 flex items-center gap-3">
                                <div
                                    class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-700 border border-indigo-100 flex items-center justify-center text-xs font-bold shrink-0">
                                    {{ strtoupper(substr($u->nombre ?? $u->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-slate-800 truncate">
                                        {{ $u->nombre_completo ?: $u->name }}
                                    </p>
                                    <p class="text-[11px] font-medium text-slate-400 truncate">{{ $u->email }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            {{-- Acciones --}}
            <div class="pt-5 border-t border-slate-100 flex flex-wrap gap-2">
                <a href="{{ route('admin.roles.edit', $role) }}"
                    class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Editar
                </a>
                <a href="{{ route('admin.roles.index') }}"
                    class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-600 rounded-xl text-xs font-bold transition-all">
                    Volver
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
