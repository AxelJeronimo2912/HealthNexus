@php
    $thCls = 'px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider';
@endphp

<div class="px-6 py-4 border-b border-slate-100">
    <h3 class="font-extrabold text-slate-800 text-base">Catálogo de medicamentos</h3>
    <p class="text-xs text-slate-400">{{ $medicamentos->total() }} medicamentos registrados</p>
</div>

<div class="overflow-x-auto">
    <table class="min-w-full">
        <thead class="bg-slate-50/70 border-b border-slate-100">
            <tr>
                <th class="{{ $thCls }} text-left">Medicamento</th>
                <th class="{{ $thCls }} text-left">Sustancia activa</th>
                <th class="{{ $thCls }} text-left">Presentación</th>
                <th class="{{ $thCls }} text-left">Estado</th>
                <th class="{{ $thCls }} text-right">Acciones</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-slate-100">
            @forelse ($medicamentos as $med)
                <tr class="group hover:bg-slate-50 transition-colors"
                    x-data="{
                        openShow: false,
                        openEdit: {{ $errors->any() && session('modal_error') === 'edit' && session('edit_id') == $med->id ? 'true' : 'false' }},
                        openDelete: false
                    }">

                    {{-- MEDICAMENTO --}}
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-slate-100 group-hover:bg-brand-950 flex items-center justify-center text-xs font-extrabold text-slate-500 group-hover:text-brand-400 transition-colors duration-200 shrink-0">
                                {{ mb_strtoupper(mb_substr($med->nombre, 0, 2)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-extrabold text-slate-900 group-hover:text-brand-600 transition-colors truncate">
                                    {{ $med->nombre }}
                                </p>
                                <p class="text-xs text-slate-400 mt-0.5">ID: #{{ $med->id }}</p>
                            </div>
                        </div>
                    </td>

                    {{-- SUSTANCIA ACTIVA --}}
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2 text-slate-500">
                            <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                            </svg>
                            <span class="text-sm">{{ $med->sustancia_activa ?? '—' }}</span>
                        </div>
                    </td>

                    {{-- PRESENTACIÓN --}}
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-100 text-slate-700 rounded-full text-xs font-bold">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            {{ $med->presentacion ?? '—' }}
                        </span>
                    </td>

                    {{-- ESTADO --}}
                    <td class="px-6 py-4">
                        @if ($med->activo ?? true)
                            <x-badge tone="emerald" dot>Activo</x-badge>
                        @else
                            <x-badge tone="slate" dot>Inactivo</x-badge>
                        @endif
                    </td>

                    {{-- ACCIONES --}}
                    <td class="px-6 py-4">
                        <div class="flex justify-end" x-data="{ menu: false, pos: {} }" @keydown.escape.window="menu = false" @scroll.window="menu = false" @resize.window="menu = false">
                            <div class="relative">
                                <button type="button" x-ref="btn" @click.outside="menu = false"
                                    @click="const r = $refs.btn.getBoundingClientRect(); const arriba = r.bottom + 170 > window.innerHeight; pos = arriba ? { right: (window.innerWidth - r.right) + 'px', bottom: (window.innerHeight - r.top + 4) + 'px', top: 'auto' } : { right: (window.innerWidth - r.right) + 'px', top: (r.bottom + 4) + 'px', bottom: 'auto' }; menu = !menu"
                                    class="grid h-8 w-8 place-items-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                                    title="Acciones" :aria-expanded="menu">
                                    <x-heroicon-m-ellipsis-horizontal class="h-5 w-5" />
                                </button>
                                <div x-show="menu" x-cloak x-transition.opacity.duration.100ms
                                    :style="pos" class="fixed z-40 w-44 overflow-hidden rounded-xl bg-white p-1 text-left shadow-pop ring-1 ring-slate-200">
                                    <button type="button" @click="menu = false; openShow = true"
                                        class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                        <x-heroicon-o-eye class="h-4 w-4 text-slate-400" /> Ver detalle
                                    </button>
                                    <button type="button" @click="menu = false; openEdit = true"
                                        class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                        <x-heroicon-o-pencil-square class="h-4 w-4 text-slate-400" /> Editar
                                    </button>
                                    <div class="my-1 h-px bg-slate-100"></div>
                                    <button type="button" @click="menu = false; openDelete = true"
                                        class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-red-600 hover:bg-red-50">
                                        <x-heroicon-o-trash class="h-4 w-4" /> Eliminar
                                    </button>
                                </div>
                            </div>
                        </div>

                        @include('medicamentos.partials._modal-show')
                        @include('medicamentos.partials._modal-edit')
                        @include('medicamentos.partials._modal-delete')
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        <x-empty-state icon="beaker" title="No se encontraron medicamentos"
                            description="Ajusta la búsqueda o registra un nuevo medicamento en el catálogo.">
                            <x-button icon="plus" size="sm" @click="$store.medicamentos.openCreate = true">Nuevo medicamento</x-button>
                        </x-empty-state>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($medicamentos->hasPages())
    <div class="px-6 py-4 border-t border-slate-100">
        {{ $medicamentos->withQueryString()->links() }}
    </div>
@endif