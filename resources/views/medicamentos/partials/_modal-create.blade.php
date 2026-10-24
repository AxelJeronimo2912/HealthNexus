{{-- MODAL CREAR GLOBAL --}}
<template x-teleport="body">
    <div x-show="$store.medicamentos.openCreate" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
        <div @click.away="$store.medicamentos.openCreate = false"
            class="bg-white rounded-2xl max-w-2xl w-full p-6 shadow-2xl border border-slate-100 space-y-5 text-left max-h-[90vh] overflow-y-auto">

            <div class="flex justify-between items-center pb-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-600 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-800 text-base">Nuevo Medicamento</h3>
                        <p class="text-xs text-slate-400">Completa la información del medicamento</p>
                    </div>
                </div>
                <button @click="$store.medicamentos.openCreate = false"
                    class="text-slate-400 hover:text-slate-700 text-xl leading-none">✕</button>
            </div>

            {{-- AQUI MOSTRAMOS LOS ERRORES DE VALIDACIÓN --}}
            @if ($errors->any())
                <div class="p-3 bg-rose-50 border border-rose-100 text-rose-800 rounded-xl text-xs">
                    <ul class="list-disc list-inside space-y-1 font-medium">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('medicamentos.store') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <p class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Identificación
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div class="md:col-span-2">
                            <label
                                class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Nombre
                                comercial *</label>
                            <input type="text" name="nombre" value="{{ old('nombre') }}" required
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-brand-500 focus:ring-0 outline-none">
                        </div>
                        <div>
                            <label
                                class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Sustancia
                                activa *</label>
                            <input type="text" name="sustancia_activa" value="{{ old('sustancia_activa') }}" required
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-brand-500 focus:ring-0 outline-none">
                        </div>
                        <div>
                            <label
                                class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Laboratorio</label>
                            <input type="text" name="laboratorio" value="{{ old('laboratorio') }}"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-brand-500 focus:ring-0 outline-none">
                        </div>
                        <div>
                            <label
                                class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Presentación
                                *</label>
                            <select name="presentacion" required
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-brand-500 focus:ring-0 outline-none">
                                <option value="">— Selecciona —</option>
                                @foreach (['Tableta', 'Cápsula', 'Jarabe', 'Suspensión', 'Solución inyectable', 'Crema', 'Ungüento', 'Gotas', 'Supositorio', 'Parche'] as $pres)
                                    <option value="{{ $pres }}" @selected(old('presentacion') == $pres)>{{ $pres }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label
                                class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Concentración
                                *</label>
                            <input type="text" name="concentracion" value="{{ old('concentracion') }}"
                                placeholder="500 mg, 10 ml, 5 %" required
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-brand-500 focus:ring-0 outline-none">
                        </div>
                        <div>
                            <label
                                class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Vía
                                de administración</label>
                            <select name="via_administracion"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-brand-500 focus:ring-0 outline-none">
                                <option value="">— Selecciona —</option>
                                @foreach (['Oral', 'Intravenosa', 'Intramuscular', 'Subcutánea', 'Tópica', 'Oftálmica', 'Ótica', 'Nasal', 'Rectal', 'Sublingual', 'Inhalatoria'] as $via)
                                    <option value="{{ $via }}" @selected(old('via_administracion') == $via)>{{ $via }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label
                                class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Grupo
                                terapéutico</label>
                            <input type="text" name="grupo_terapeutico" value="{{ old('grupo_terapeutico') }}"
                                placeholder="Analgésico, Antibiótico..."
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-brand-500 focus:ring-0 outline-none">
                        </div>
                        <div>
                            <label
                                class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Código
                                de barras</label>
                            <input type="text" name="codigo_barras" value="{{ old('codigo_barras') }}"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-brand-500 focus:ring-0 outline-none">
                        </div>
                        <div>
                            <label
                                class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Registro
                                sanitario</label>
                            <input type="text" name="registro_sanitario" value="{{ old('registro_sanitario') }}"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-brand-500 focus:ring-0 outline-none">
                        </div>
                    </div>
                </div>

                <div class="pt-5 border-t border-slate-100">
                    <p class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Clasificación
                    </p>
                    <div class="flex flex-wrap gap-3">
                        <label
                            class="inline-flex items-center gap-2.5 cursor-pointer px-3 py-2 bg-slate-50/70 border border-slate-100 rounded-xl">
                            <input type="checkbox" name="psicotropico" value="1"
                                {{ old('psicotropico') ? 'checked' : '' }}
                                class="w-4 h-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500 focus:ring-0 cursor-pointer">
                            <span class="text-xs font-bold text-slate-700">Psicotrópico</span>
                        </label>
                        <label
                            class="inline-flex items-center gap-2.5 cursor-pointer px-3 py-2 bg-slate-50/70 border border-slate-100 rounded-xl">
                            <input type="checkbox" name="antibiotico" value="1"
                                {{ old('antibiotico') ? 'checked' : '' }}
                                class="w-4 h-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500 focus:ring-0 cursor-pointer">
                            <span class="text-xs font-bold text-slate-700">Antibiótico</span>
                        </label>
                        <label
                            class="inline-flex items-center gap-2.5 cursor-pointer px-3 py-2 bg-slate-50/70 border border-slate-100 rounded-xl">
                            <input type="checkbox" name="controlado" value="1"
                                {{ old('controlado') ? 'checked' : '' }}
                                class="w-4 h-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500 focus:ring-0 cursor-pointer">
                            <span class="text-xs font-bold text-slate-700">Controlado</span>
                        </label>
                    </div>
                </div>

                <div class="pt-5 border-t border-slate-100">
                    <p class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Inventario y
                        Precios</p>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <div>
                            <label
                                class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Unidad
                                medida *</label>
                            <select name="unidad_medida" required
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-brand-500 focus:ring-0 outline-none">
                                @foreach (['pieza', 'caja', 'frasco', 'ampolleta', 'tubo', 'sobre', 'blíster'] as $um)
                                    <option value="{{ $um }}" @selected(old('unidad_medida', 'pieza') == $um)>
                                        {{ ucfirst($um) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label
                                class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Stock
                                mín *</label>
                            <input type="number" name="stock_minimo" min="1"
                                value="{{ old('stock_minimo', 10) }}" required
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-brand-500 focus:ring-0 outline-none">
                        </div>
                        <div>
                            <label
                                class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Stock
                                máx *</label>
                            <input type="number" name="stock_maximo" min="0"
                                value="{{ old('stock_maximo', 0) }}" required
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-brand-500 focus:ring-0 outline-none">
                        </div>
                        <div>
                            <label
                                class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Precio
                                compra</label>
                            <input type="number" step="0.01" min="0" name="precio_compra"
                                value="{{ old('precio_compra') }}"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-brand-500 focus:ring-0 outline-none">
                        </div>
                        <div class="col-span-2 md:col-span-4">
                            <label
                                class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Precio
                                venta</label>
                            <input type="number" step="0.01" min="0" name="precio_venta"
                                value="{{ old('precio_venta') }}"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-brand-500 focus:ring-0 outline-none">
                        </div>
                    </div>
                </div>

                <div class="pt-5 border-t border-slate-100">
                    <p class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Estado</p>
                    <label
                        class="inline-flex items-center gap-2.5 cursor-pointer px-3 py-2 bg-emerald-50/60 border border-emerald-100 rounded-xl">
                        <input type="checkbox" name="activo" value="1"
                            {{ old('activo', true) ? 'checked' : '' }}
                            class="w-4 h-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 focus:ring-0 cursor-pointer">
                        <span class="text-xs font-bold text-emerald-700">Medicamento activo</span>
                    </label>
                </div>

                <div class="flex justify-end gap-2 pt-5 border-t border-slate-100">
                    <button @click="$store.medicamentos.openCreate = false" type="button"
                        class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-600 rounded-xl text-xs font-bold transition-all">Cancelar</button>
                    <button type="submit"
                        class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all">Guardar
                        medicamento</button>
                </div>
            </form>
        </div>
    </div>
</template>

