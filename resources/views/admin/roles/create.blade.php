<x-app-layout>
    <x-slot name="header">
        {{-- Header tipo "Directorio" --}}
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-slate-100 text-slate-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-900">Nuevo Rol</h2>
                    <p class="text-xs text-slate-500">Configura los permisos de acceso del nuevo rol</p>
                </div>
            </div>

            <a href="{{ route('admin.roles.index') }}"
                class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Volver al listado
            </a>
        </div>
    </x-slot>

    <div class="min-h-screen bg-slate-50">
        <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">

            {{-- Card de Stats --}}
            <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="flex items-center gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 text-slate-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Tipo</p>
                        <p class="text-lg font-bold text-slate-900">Rol del sistema</p>
                    </div>
                </div>

                <div class="flex items-center gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-violet-100 text-violet-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Acción</p>
                        <p class="text-lg font-bold text-slate-900">Creación</p>
                    </div>
                </div>

                <div class="flex items-center gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Estado</p>
                        <p class="text-lg font-bold text-slate-900">Pendiente</p>
                    </div>
                </div>
            </div>

            {{-- Formulario --}}
            <form action="{{ route('admin.roles.store') }}" method="POST"
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                @csrf

                <div class="border-b border-slate-100 px-6 py-5">
                    <h3 class="text-sm font-semibold text-slate-900">Información del rol</h3>
                    <p class="text-xs text-slate-500">Define el nombre y los menús visibles para este rol.</p>
                </div>

                <div class="space-y-6 px-6 py-6">

                    {{-- Nombre --}}
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Nombre del Rol
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            placeholder="Ej. laboratorio, radiologia, administracion"
                            class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 shadow-sm transition focus:border-indigo-500 focus:bg-white focus:ring-indigo-500">
                        @error('name')
                        <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Menús --}}
                    <div>
                        <div class="mb-3 flex flex-wrap items-end justify-between gap-3">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    Menús del navbar
                                </label>
                                <p class="text-xs text-slate-500">
                                    Marca los menús que este rol podrá ver. Solo aparecerán los que selecciones.
                                </p>
                            </div>
                            <span id="contador-seleccionados"
                                class="inline-flex items-center gap-1 rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">
                                0 seleccionados
                            </span>
                        </div>

                        {{-- Filtro --}}
                        <div class="relative mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" />
                            </svg>
                            <input type="text" id="filtro-menus" aria-label="Buscar menú"
                                placeholder="Buscar menú..."
                                class="w-full rounded-xl border-slate-200 bg-slate-50 py-2.5 pl-10 pr-3 text-sm text-slate-900 shadow-sm transition focus:border-indigo-500 focus:bg-white focus:ring-indigo-500">
                        </div>

                        <div class="space-y-4" id="lista-secciones">
                            @foreach ($menus as $seccion)
                            <div class="seccion-bloque overflow-hidden rounded-xl border border-slate-200"
                                data-seccion="{{ $loop->index }}">
                                <div
                                    class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-4 py-2.5">
                                    <label class="flex cursor-pointer items-center">
                                        <input type="checkbox"
                                            class="seccion-toggle rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                            data-seccion="{{ $loop->index }}">
                                        <span class="ml-2 text-sm font-semibold text-slate-800">
                                            {{ $seccion['titulo'] }}
                                        </span>
                                    </label>
                                    <span class="text-xs font-medium text-slate-500">
                                        {{ count($seccion['items']) }} menús
                                    </span>
                                </div>

                                <div class="grid grid-cols-1 gap-1 p-2 sm:grid-cols-2 md:grid-cols-3">
                                    @foreach ($seccion['items'] as $item)
                                    @php
                                    $checked = in_array($item['permiso'], old('permissions', []));
                                    @endphp
                                    <label
                                        class="item-menu inline-flex cursor-pointer items-center rounded-lg px-3 py-2 text-sm text-slate-700 transition hover:bg-slate-50"
                                        data-nombre="{{ strtolower($item['label']) }}">
                                        <input type="checkbox" name="permissions[]"
                                            value="{{ $item['permiso'] }}" @checked($checked)
                                            class="item-checkbox rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                            data-seccion="{{ $loop->parent->index }}">
                                        @if (!empty($item['icono']))
                                        <x-dynamic-component :component="'heroicon-o-' . $item['icono']"
                                            class="ml-2 h-4 w-4 flex-shrink-0 text-slate-400" />
                                        @endif
                                        <span class="ml-2 truncate">{{ $item['label'] }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach

                            <div id="sin-resultados" class="hidden py-8 text-center text-sm text-slate-400">
                                No se encontraron menús con ese criterio.
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Acciones --}}
                <div class="flex items-center justify-between border-t border-slate-100 bg-slate-50 px-6 py-4">
                    <a href="{{ route('admin.roles.index') }}"
                        class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-100">
                        Cancelar
                    </a>
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        Guardar Rol
                    </button>
                </div>
            </form>
        </div>
    </div>

    @include('admin.roles._scripts')
</x-app-layout>
