@php
    $thCls = 'px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider';
@endphp

<div class="px-6 py-4 border-b border-slate-100">
    <h3 class="font-extrabold text-slate-800 text-base">Catálogo de medicamentos</h3>
    <p class="text-[11px] text-slate-400">{{ $medicamentos->total() }} medicamentos registrados</p>
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
                            <div class="w-12 h-12 rounded-2xl bg-slate-100 group-hover:bg-indigo-950 flex items-center justify-center text-xs font-extrabold text-slate-500 group-hover:text-indigo-400 transition-colors duration-200 shrink-0">
                                {{ mb_strtoupper(mb_substr($med->nombre, 0, 2)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-extrabold text-slate-900 group-hover:text-indigo-600 transition-colors truncate">
                                    {{ $med->nombre }}
                                </p>
                                <p class="text-[11px] text-slate-400 mt-0.5">ID: #{{ $med->id }}</p>
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
                            <span class="inline-flex px-3 py-1 bg-emerald-50 text-emerald-700 rounded-full text-xs font-bold">Activo</span>
                        @else
                            <span class="inline-flex px-3 py-1 bg-slate-100 text-slate-500 rounded-full text-xs font-bold">Inactivo</span>
                        @endif
                    </td>

                    {{-- ACCIONES --}}
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-1">

                            {{-- Ver --}}
                            <button type="button" @click="openShow = true" title="Ver detalles"
                                class="p-2 rounded-xl text-slate-800 hover:bg-slate-200/70 active:scale-90 transition-all">
                                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </button>

                            {{-- Editar --}}
                            <button type="button" @click="openEdit = true" title="Editar"
                                class="p-2 rounded-xl text-purple-500 hover:bg-purple-50 active:scale-90 transition-all">
                                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                </svg>
                            </button>

                            {{-- Eliminar --}}
                            <button type="button" @click="openDelete = true" title="Eliminar"
                                class="p-2 rounded-xl text-rose-500 hover:bg-rose-50 active:scale-90 transition-all">
                                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                            </button>
                        </div>

                        @include('medicamentos.partials._modal-show')
                        @include('medicamentos.partials._modal-edit')
                        @include('medicamentos.partials._modal-delete')
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <p class="text-xs font-semibold text-slate-400">No se encontraron medicamentos registrados.</p>
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