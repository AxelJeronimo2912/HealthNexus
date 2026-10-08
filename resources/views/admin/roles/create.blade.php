<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-3">
            <div class="min-w-0">
                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight truncate">
                    Nuevo Rol
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Configura los permisos de acceso del nuevo rol</p>
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
        $inputCls =
            'w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-indigo-500 focus:ring-0 outline-none';
        $labelCls = 'block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5';
        $errorCls = 'text-rose-600 text-xs mt-1 font-medium';
    @endphp

    <div class="py-8 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- ============ STATS ============ --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4">
                <div
                    class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-600 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Tipo</p>
                    <p class="text-lg font-black text-slate-800 tracking-tight mt-0.5">Rol del sistema</p>
                </div>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4">
                <div
                    class="w-12 h-12 rounded-2xl bg-purple-50 flex items-center justify-center text-purple-600 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Acción</p>
                    <p class="text-lg font-black text-slate-800 tracking-tight mt-0.5">Creación</p>
                </div>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4">
                <div
                    class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Estado</p>
                    <p class="text-lg font-black text-slate-800 tracking-tight mt-0.5">Pendiente</p>
                </div>
            </div>
        </div>

        {{-- ============ FORMULARIO ============ --}}
        <form action="{{ route('admin.roles.store') }}" method="POST"
            class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            @csrf

            <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-800 text-base">Información del rol</h3>
                    <p class="text-[11px] text-slate-400">Define el nombre y los menús visibles para este rol.</p>
                </div>
            </div>

            <div class="p-6 space-y-6">

                {{-- Nombre --}}
                <div>
                    <label class="{{ $labelCls }}">Nombre del Rol *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        placeholder="Ej. laboratorio, radiologia, administracion" class="{{ $inputCls }}">
                    @error('name')
                        <p class="{{ $errorCls }}">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Menús --}}
                <div class="pt-5 border-t border-slate-100">
                    <div class="flex flex-wrap items-end justify-between gap-3 mb-4">
                        <div>
                            <p class="{{ $labelCls }} mb-0">Menús del navbar</p>
                            <p class="text-[11px] font-medium text-slate-400 mt-0.5">
                                Marca los menús que este rol podrá ver. Solo aparecerán los que selecciones.
                            </p>
                        </div>
                        <span id="contador-seleccionados"
                            class="inline-flex items-center gap-1 px-3 py-1 bg-indigo-50 text-indigo-700 border border-indigo-100 rounded-full text-[11px] font-bold">
                            0 seleccionados
                        </span>
                    </div>

                    {{-- Filtro --}}
                    <div class="relative mb-4">
                        <span
                            class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input type="text" id="filtro-menus" aria-label="Buscar menú" placeholder="Buscar menú..."
                            class="{{ $inputCls }} pl-9">
                    </div>

                    <div class="space-y-3" id="lista-secciones">
                        @foreach ($menus as $seccion)
                            <div class="seccion-bloque rounded-2xl border border-slate-100 bg-slate-50/50 overflow-hidden"
                                data-seccion="{{ $loop->index }}">
                                <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
                                    <label class="flex cursor-pointer items-center gap-2.5">
                                        <input type="checkbox"
                                            class="seccion-toggle w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 focus:ring-0 cursor-pointer"
                                            data-seccion="{{ $loop->index }}">
                                        <span class="text-xs font-bold text-slate-800">
                                            {{ $seccion['titulo'] }}
                                        </span>
                                    </label>
                                    <span
                                        class="px-2.5 py-1 bg-slate-100 text-slate-500 rounded-full text-[10px] font-bold uppercase tracking-wide">
                                        {{ count($seccion['items']) }} menús
                                    </span>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-1 p-2">
                                    @foreach ($seccion['items'] as $item)
                                        @php
                                            $checked = in_array($item['permiso'], old('permissions', []));
                                        @endphp
                                        <label
                                            class="item-menu inline-flex cursor-pointer items-center gap-2 rounded-xl px-3 py-2 text-xs font-medium text-slate-700 transition hover:bg-indigo-50/50"
                                            data-nombre="{{ strtolower($item['label']) }}">
                                            <input type="checkbox" name="permissions[]"
                                                value="{{ $item['permiso'] }}" @checked($checked)
                                                class="item-checkbox w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 focus:ring-0 cursor-pointer"
                                                data-seccion="{{ $loop->parent->index }}">
                                            @if (!empty($item['icono']))
                                                <x-dynamic-component :component="'heroicon-o-' . $item['icono']"
                                                    class="w-4 h-4 flex-shrink-0 text-slate-400" />
                                            @endif
                                            <span class="truncate">{{ $item['label'] }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                        <div id="sin-resultados" class="hidden py-8 text-center">
                            <p class="text-xs font-semibold text-slate-400">No se encontraron menús con ese criterio.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="px-6 py-4 bg-slate-50/70 border-t border-slate-100 flex flex-wrap justify-between items-center gap-3">
                <a href="{{ route('admin.roles.index') }}"
                    class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-600 rounded-xl text-xs font-bold transition-all">
                    Cancelar
                </a>
                <button type="submit"
                    class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                    Guardar Rol
                </button>
            </div>
        </form>
    </div>

    @include('admin.roles._scripts')
</x-app-layout>
