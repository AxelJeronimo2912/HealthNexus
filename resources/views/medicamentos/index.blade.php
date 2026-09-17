<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('medicamentos', {
            openCreate: false,
        });
    });
</script>

<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestión de Medicamentos') }}
            </h2>

            {{-- BOTÓN QUE ABRE EL MODAL DE CREACIÓN (usa el store global) --}}
            <button @click="$store.medicamentos.openCreate = true"
                    type="button"
                    class="inline-flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm rounded-xl shadow-sm transition-all transform active:scale-95">
                <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nuevo Medicamento
            </button>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Mensajes de Notificación --}}
        @if (session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl space-y-1">
                <div class="font-bold text-sm">Hubo errores al procesar la solicitud:</div>
                <ul class="list-disc list-inside text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Buscador y Filtros --}}
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
            <form method="GET" action="{{ route('medicamentos.index') }}" class="flex flex-col sm:flex-row gap-4 justify-between">
                <div class="relative flex-1">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Buscar por nombre, sustancia activa, laboratorio o código..."
                           class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="px-4 py-2 bg-gray-900 hover:bg-gray-800 text-white text-sm font-medium rounded-xl transition">
                        Buscar
                    </button>
                    @if(request('search'))
                        <a href="{{ route('medicamentos.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-xl transition">
                            Limpiar
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Tabla Principal --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider bg-gray-50/50">
                            <th class="px-5 py-3">Medicamento</th>
                            <th class="px-5 py-3">Sustancia Activa</th>
                            <th class="px-5 py-3">Presentación</th>
                            <th class="px-5 py-3">Estado</th>
                            <th class="px-5 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($medicamentos as $med)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-5 py-4 font-semibold text-gray-900 text-sm">
                                    {{ $med->nombre }}
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-600">
                                    {{ $med->sustancia_activa ?? '—' }}
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-600">
                                    {{ $med->presentacion ?? '—' }}
                                </td>
                                <td class="px-5 py-4 text-sm">
                                    @if ($med->activo ?? true)
                                        <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded-full font-semibold text-[11px]">Activo</span>
                                    @else
                                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded-full font-semibold text-[11px]">Inactivo</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-sm text-right font-medium">
                                    <div class="inline-flex items-center justify-end gap-3" x-data="{ openShow: false, openEdit: false, openDelete: false }">

                                        {{-- VER --}}
                                        <button @click="openShow = true" title="Ver" type="button" class="p-1.5 text-slate-700 hover:text-slate-900 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>

                                        {{-- EDITAR --}}
                                        <button @click="openEdit = true" title="Editar" type="button" class="p-1.5 text-purple-600 hover:text-purple-800 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>

                                        {{-- ELIMINAR --}}
                                        <button @click="openDelete = true" title="Eliminar" type="button" class="p-1.5 text-rose-500 hover:text-rose-700 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>

                                        {{-- MODAL VER --}}
                                        <template x-teleport="body">
                                            <div x-show="openShow" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm">
                                                <div @click.away="openShow = false" class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-gray-100 space-y-4 text-left max-h-[90vh] overflow-y-auto">
                                                    <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                                                        <h3 class="text-lg font-bold text-gray-900">Detalles del Medicamento</h3>
                                                        <button @click="openShow = false" class="text-gray-400 hover:text-gray-600">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                        </button>
                                                    </div>
                                                    <div class="space-y-4 text-xs">
                                                        <div class="flex items-start justify-between">
                                                            <div>
                                                                <span class="text-gray-400 block uppercase tracking-wider font-semibold text-[10px]">Identificación</span>
                                                                <h4 class="text-base font-bold text-gray-900">{{ $med->nombre }}</h4>
                                                                <p class="text-indigo-600 font-medium text-sm">{{ $med->sustancia_activa ?? 'Sin sustancia activa' }}</p>
                                                            </div>
                                                            @if ($med->activo ?? true)
                                                                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded-full font-semibold text-[10px]">Activo</span>
                                                            @else
                                                                <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded-full font-semibold text-[10px]">Inactivo</span>
                                                            @endif
                                                        </div>
                                                        <div class="grid grid-cols-2 gap-3 pt-2 border-t border-gray-100">
                                                            <div><span class="text-gray-500 font-medium">Laboratorio:</span> <span class="text-gray-800 font-semibold block">{{ $med->laboratorio ?? '—' }}</span></div>
                                                            <div><span class="text-gray-500 font-medium">Presentación:</span> <span class="text-gray-800 font-semibold block">{{ $med->presentacion ?? '—' }}</span></div>
                                                            <div><span class="text-gray-500 font-medium">Concentración:</span> <span class="text-gray-800 font-semibold block">{{ $med->concentracion ?? '—' }}</span></div>
                                                            <div><span class="text-gray-500 font-medium">Vía Admin.:</span> <span class="text-gray-800 font-semibold block">{{ $med->via_administracion ?? '—' }}</span></div>
                                                            <div><span class="text-gray-500 font-medium">Grupo Terapéutico:</span> <span class="text-gray-800 font-semibold block">{{ $med->grupo_terapeutico ?? '—' }}</span></div>
                                                            <div><span class="text-gray-500 font-medium">Reg. Sanitario:</span> <span class="text-gray-800 font-semibold block">{{ $med->registro_sanitario ?? '—' }}</span></div>
                                                            <div><span class="text-gray-500 font-medium">Código Barras:</span> <span class="text-gray-800 font-mono block">{{ $med->codigo_barras ?? '—' }}</span></div>
                                                            <div><span class="text-gray-500 font-medium">Unidad Medida:</span> <span class="text-gray-800 font-semibold block">{{ ucfirst($med->unidad_medida ?? 'Pieza') }}</span></div>
                                                        </div>
                                                        <div class="pt-2 border-t border-gray-100">
                                                            <span class="text-gray-500 font-medium block mb-1">Clasificación:</span>
                                                            <div class="flex flex-wrap gap-1.5">
                                                                @if ($med->psicotropico) <span class="px-2 py-0.5 bg-purple-100 text-purple-800 rounded font-semibold text-[10px]">Psicotrópico</span> @endif
                                                                @if ($med->antibiotico) <span class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded font-semibold text-[10px]">Antibiótico</span> @endif
                                                                @if ($med->controlado) <span class="px-2 py-0.5 bg-rose-100 text-rose-800 rounded font-semibold text-[10px]">Controlado</span> @endif
                                                                @if (!$med->psicotropico && !$med->antibiotico && !$med->controlado) <span class="text-gray-400 italic text-[11px]">Ninguna</span> @endif
                                                            </div>
                                                        </div>
                                                        <div class="pt-2 border-t border-gray-100 grid grid-cols-3 gap-2 text-center">
                                                            <div class="bg-gray-50 p-2 rounded-xl border border-gray-100"><span class="text-gray-400 block text-[10px]">Stock Mín / Máx</span><span class="font-bold text-gray-800">{{ $med->stock_minimo ?? 0 }} / {{ $med->stock_maximo ?? 0 }}</span></div>
                                                            <div class="bg-gray-50 p-2 rounded-xl border border-gray-100"><span class="text-gray-400 block text-[10px]">Precio Compra</span><span class="font-bold text-gray-800">{{ isset($med->precio_compra) ? '$' . number_format($med->precio_compra, 2) : '—' }}</span></div>
                                                            <div class="bg-gray-50 p-2 rounded-xl border border-gray-100"><span class="text-gray-400 block text-[10px]">Precio Venta</span><span class="font-bold text-emerald-600">{{ isset($med->precio_venta) ? '$' . number_format($med->precio_venta, 2) : '—' }}</span></div>
                                                        </div>
                                                    </div>
                                                    <div class="flex justify-end pt-3 border-t border-gray-100">
                                                        <button @click="openShow = false" type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-medium transition">Cerrar</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>

                                        {{-- MODAL EDITAR --}}
                                        <template x-teleport="body">
                                            <div x-show="openEdit" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm">
                                                <div @click.away="openEdit = false" class="bg-white rounded-2xl max-w-2xl w-full p-6 shadow-xl border border-gray-100 space-y-4 text-left max-h-[90vh] overflow-y-auto">
                                                    <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                                                        <h3 class="text-lg font-bold text-gray-900">Editar Medicamento</h3>
                                                        <button @click="openEdit = false" class="text-gray-400 hover:text-gray-600">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                        </button>
                                                    </div>
                                                    <form action="{{ route('medicamentos.update', $med) }}" method="POST" class="space-y-4">
                                                        @csrf @method('PUT')

                                                        {{-- 1. Identificación --}}
                                                        <div>
                                                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Identificación</h4>
                                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                                <div class="col-span-2">
                                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Nombre comercial *</label>
                                                                    <input type="text" name="nombre" value="{{ old('nombre', $med->nombre) }}" required class="w-full border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500">
                                                                </div>
                                                                <div>
                                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Sustancia activa</label>
                                                                    <input type="text" name="sustancia_activa" value="{{ old('sustancia_activa', $med->sustancia_activa) }}" class="w-full border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500">
                                                                </div>
                                                                <div>
                                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Laboratorio</label>
                                                                    <input type="text" name="laboratorio" value="{{ old('laboratorio', $med->laboratorio) }}" class="w-full border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500">
                                                                </div>
                                                                <div>
                                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Presentación</label>
                                                                    <select name="presentacion" class="w-full border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500">
                                                                        <option value="">— Selecciona —</option>
                                                                        @foreach (['Tableta', 'Cápsula', 'Jarabe', 'Suspensión', 'Solución inyectable', 'Crema', 'Ungüento', 'Gotas', 'Supositorio', 'Parche'] as $pres)
                                                                            <option value="{{ $pres }}" @selected(old('presentacion', $med->presentacion) == $pres)>{{ $pres }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div>
                                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Concentración</label>
                                                                    <input type="text" name="concentracion" value="{{ old('concentracion', $med->concentracion) }}" placeholder="500 mg, 10 ml, 5 %" class="w-full border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500">
                                                                </div>
                                                                <div>
                                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Vía de administración</label>
                                                                    <select name="via_administracion" class="w-full border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500">
                                                                        <option value="">— Selecciona —</option>
                                                                        @foreach (['Oral', 'Intravenosa', 'Intramuscular', 'Subcutánea', 'Tópica', 'Oftálmica', 'Ótica', 'Nasal', 'Rectal', 'Sublingual', 'Inhalatoria'] as $via)
                                                                            <option value="{{ $via }}" @selected(old('via_administracion', $med->via_administracion) == $via)>{{ $via }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div>
                                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Grupo terapéutico</label>
                                                                    <input type="text" name="grupo_terapeutico" value="{{ old('grupo_terapeutico', $med->grupo_terapeutico) }}" placeholder="Analgésico, Antibiótico, etc." class="w-full border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500">
                                                                </div>
                                                                <div>
                                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Código de barras</label>
                                                                    <input type="text" name="codigo_barras" value="{{ old('codigo_barras', $med->codigo_barras) }}" class="w-full border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500">
                                                                </div>
                                                                <div>
                                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Registro sanitario</label>
                                                                    <input type="text" name="registro_sanitario" value="{{ old('registro_sanitario', $med->registro_sanitario) }}" class="w-full border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- 2. Clasificación --}}
                                                        <div class="pt-2 border-t border-gray-100">
                                                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Clasificación</h4>
                                                            <div class="flex flex-wrap gap-4 text-xs font-medium text-gray-700">
                                                                <label class="inline-flex items-center gap-2"><input type="checkbox" name="psicotropico" value="1" {{ old('psicotropico', $med->psicotropico) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600"> Psicotrópico</label>
                                                                <label class="inline-flex items-center gap-2"><input type="checkbox" name="antibiotico" value="1" {{ old('antibiotico', $med->antibiotico) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600"> Antibiótico</label>
                                                                <label class="inline-flex items-center gap-2"><input type="checkbox" name="controlado" value="1" {{ old('controlado', $med->controlado) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600"> Controlado</label>
                                                            </div>
                                                        </div>

                                                        {{-- 3. Inventario --}}
                                                        <div class="pt-2 border-t border-gray-100">
                                                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Inventario y Precios</h4>
                                                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                                                <div>
                                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Unidad de medida *</label>
                                                                    <select name="unidad_medida" required class="w-full border-gray-200 rounded-xl text-sm">
                                                                        @foreach (['pieza', 'caja', 'frasco', 'ampolleta', 'tubo', 'sobre', 'blíster'] as $um)
                                                                            <option value="{{ $um }}" @selected(old('unidad_medida', $med->unidad_medida ?? 'pieza') == $um)>{{ ucfirst($um) }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div><label class="block text-xs font-medium text-gray-700 mb-1">Stock mín *</label><input type="number" name="stock_minimo" min="0" value="{{ old('stock_minimo', $med->stock_minimo ?? 0) }}" required class="w-full border-gray-200 rounded-xl text-sm"></div>
                                                                <div><label class="block text-xs font-medium text-gray-700 mb-1">Stock máx *</label><input type="number" name="stock_maximo" min="0" value="{{ old('stock_maximo', $med->stock_maximo ?? 0) }}" required class="w-full border-gray-200 rounded-xl text-sm"></div>
                                                                <div><label class="block text-xs font-medium text-gray-700 mb-1">Precio compra</label><input type="number" step="0.01" min="0" name="precio_compra" value="{{ old('precio_compra', $med->precio_compra) }}" class="w-full border-gray-200 rounded-xl text-sm"></div>
                                                                <div class="col-span-2 md:col-span-4"><label class="block text-xs font-medium text-gray-700 mb-1">Precio venta</label><input type="number" step="0.01" min="0" name="precio_venta" value="{{ old('precio_venta', $med->precio_venta) }}" class="w-full border-gray-200 rounded-xl text-sm"></div>
                                                            </div>
                                                        </div>

                                                        {{-- 4. Estado --}}
                                                        <div class="pt-2 border-t border-gray-100">
                                                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Estado</h4>
                                                            <label class="inline-flex items-center gap-2 text-xs font-medium text-gray-700">
                                                                <input type="checkbox" name="activo" value="1" {{ old('activo', $med->activo ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                                                                Medicamento activo
                                                            </label>
                                                        </div>

                                                        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                                                            <button @click="openEdit = false" type="button" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl text-sm font-medium">Cancelar</button>
                                                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-sm font-medium">Guardar cambios</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </template>

                                        {{-- MODAL ELIMINAR --}}
                                        <template x-teleport="body">
                                            <div x-show="openDelete" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm">
                                                <div @click.away="openDelete = false" class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-xl border border-gray-100 space-y-4 text-center">
                                                    <h3 class="text-lg font-bold text-gray-900">¿Eliminar medicamento?</h3>
                                                    <p class="text-xs text-gray-500">Esta acción no se puede deshacer.</p>
                                                    <form action="{{ route('medicamentos.destroy', $med) }}" method="POST" class="flex justify-center gap-2 pt-2">
                                                        @csrf @method('DELETE')
                                                        <button @click="openDelete = false" type="button" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl text-sm font-medium">Cancelar</button>
                                                        <button type="submit" class="px-4 py-2 bg-rose-600 text-white rounded-xl text-sm font-medium hover:bg-rose-700">Sí, eliminar</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </template>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-8 text-center text-gray-400 text-sm italic">
                                    No se encontraron medicamentos registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- MODAL DE CREACIÓN GLOBAL (Controlado por el store $store.medicamentos.openCreate) --}}
    <template x-teleport="body">
        <div x-show="$store.medicamentos.openCreate" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm">
            <div @click.away="$store.medicamentos.openCreate = false" class="bg-white rounded-2xl max-w-2xl w-full p-6 shadow-xl border border-gray-100 space-y-4 text-left max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900">Nuevo Medicamento</h3>
                    <button @click="$store.medicamentos.openCreate = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form action="{{ route('medicamentos.store') }}" method="POST" class="space-y-4">
                    @csrf

                    {{-- 1. Identificación --}}
                    <div>
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Identificación</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div class="col-span-2">
                                <label class="block text-xs font-medium text-gray-700 mb-1">Nombre comercial *</label>
                                <input type="text" name="nombre" value="{{ old('nombre') }}" required class="w-full border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Sustancia activa</label>
                                <input type="text" name="sustancia_activa" value="{{ old('sustancia_activa') }}" class="w-full border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Laboratorio</label>
                                <input type="text" name="laboratorio" value="{{ old('laboratorio') }}" class="w-full border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Presentación</label>
                                <select name="presentacion" class="w-full border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500">
                                    <option value="">— Selecciona —</option>
                                    @foreach (['Tableta', 'Cápsula', 'Jarabe', 'Suspensión', 'Solución inyectable', 'Crema', 'Ungüento', 'Gotas', 'Supositorio', 'Parche'] as $pres)
                                        <option value="{{ $pres }}" @selected(old('presentacion') == $pres)>{{ $pres }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Concentración</label>
                                <input type="text" name="concentracion" value="{{ old('concentracion') }}" placeholder="500 mg, 10 ml, 5 %" class="w-full border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Vía de administración</label>
                                <select name="via_administracion" class="w-full border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500">
                                    <option value="">— Selecciona —</option>
                                    @foreach (['Oral', 'Intravenosa', 'Intramuscular', 'Subcutánea', 'Tópica', 'Oftálmica', 'Ótica', 'Nasal', 'Rectal', 'Sublingual', 'Inhalatoria'] as $via)
                                        <option value="{{ $via }}" @selected(old('via_administracion') == $via)>{{ $via }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Grupo terapéutico</label>
                                <input type="text" name="grupo_terapeutico" value="{{ old('grupo_terapeutico') }}" placeholder="Analgésico, Antibiótico, etc." class="w-full border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Código de barras</label>
                                <input type="text" name="codigo_barras" value="{{ old('codigo_barras') }}" class="w-full border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Registro sanitario</label>
                                <input type="text" name="registro_sanitario" value="{{ old('registro_sanitario') }}" class="w-full border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500">
                            </div>
                        </div>
                    </div>

                    {{-- 2. Clasificación --}}
                    <div class="pt-2 border-t border-gray-100">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Clasificación</h4>
                        <div class="flex flex-wrap gap-4 text-xs font-medium text-gray-700">
                            <label class="inline-flex items-center gap-2"><input type="checkbox" name="psicotropico" value="1" {{ old('psicotropico') ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600"> Psicotrópico</label>
                            <label class="inline-flex items-center gap-2"><input type="checkbox" name="antibiotico" value="1" {{ old('antibiotico') ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600"> Antibiótico</label>
                            <label class="inline-flex items-center gap-2"><input type="checkbox" name="controlado" value="1" {{ old('controlado') ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600"> Controlado</label>
                        </div>
                    </div>

                    {{-- 3. Inventario --}}
                    <div class="pt-2 border-t border-gray-100">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Inventario y Precios</h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Unidad de medida *</label>
                                <select name="unidad_medida" required class="w-full border-gray-200 rounded-xl text-sm">
                                    @foreach (['pieza', 'caja', 'frasco', 'ampolleta', 'tubo', 'sobre', 'blíster'] as $um)
                                        <option value="{{ $um }}" @selected(old('unidad_medida', 'pieza') == $um)>{{ ucfirst($um) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div><label class="block text-xs font-medium text-gray-700 mb-1">Stock mín *</label><input type="number" name="stock_minimo" min="0" value="{{ old('stock_minimo', 0) }}" required class="w-full border-gray-200 rounded-xl text-sm"></div>
                            <div><label class="block text-xs font-medium text-gray-700 mb-1">Stock máx *</label><input type="number" name="stock_maximo" min="0" value="{{ old('stock_maximo', 0) }}" required class="w-full border-gray-200 rounded-xl text-sm"></div>
                            <div><label class="block text-xs font-medium text-gray-700 mb-1">Precio compra</label><input type="number" step="0.01" min="0" name="precio_compra" value="{{ old('precio_compra') }}" class="w-full border-gray-200 rounded-xl text-sm"></div>
                            <div class="col-span-2 md:col-span-4"><label class="block text-xs font-medium text-gray-700 mb-1">Precio venta</label><input type="number" step="0.01" min="0" name="precio_venta" value="{{ old('precio_venta') }}" class="w-full border-gray-200 rounded-xl text-sm"></div>
                        </div>
                    </div>

                    {{-- 4. Estado --}}
                    <div class="pt-2 border-t border-gray-100">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Estado</h4>
                        <label class="inline-flex items-center gap-2 text-xs font-medium text-gray-700">
                            <input type="checkbox" name="activo" value="1" {{ old('activo', true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                            Medicamento activo
                        </label>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <button @click="$store.medicamentos.openCreate = false" type="button" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl text-sm font-medium">Cancelar</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-sm font-medium">Guardar medicamento</button>
                    </div>
                </form>
            </div>
        </div>
    </template>

</x-app-layout>