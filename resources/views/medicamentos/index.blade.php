<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('medicamentos', {
            openCreate: false,
        });
    });
</script>

<x-app-layout>

    <x-slot name="header">
        <div class="flex justify-between items-center gap-3">
            <div class="min-w-0">
                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight truncate">
                    Gestión de Medicamentos
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Catálogo, inventario y precios de medicamentos</p>
            </div>

            <button @click="$store.medicamentos.openCreate = true" type="button"
                class="bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white px-5 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 shadow-sm transition-all shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                Nuevo Medicamento
            </button>
        </div>
    </x-slot>

    @php
        $inputCls =
            'w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-indigo-500 focus:ring-0 outline-none';
        $labelCls = 'block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5';
        $thCls = 'px-5 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider';
        $errorCls = 'text-rose-600 text-xs mt-1 font-medium';
    @endphp

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        @if (session('success'))
            <div
                class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-2xl text-xs font-semibold flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-100 text-rose-800 rounded-2xl">
                <p class="text-xs font-bold mb-2 uppercase tracking-wider">Corrige los siguientes errores:</p>
                <ul class="list-disc list-inside text-xs space-y-1 font-medium">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Buscador --}}
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-5">
            <form method="GET" action="{{ route('medicamentos.index') }}"
                class="flex flex-col sm:flex-row gap-2 items-end">
                <div class="relative flex-1 min-w-[200px]">
                    <label class="{{ $labelCls }}">Buscar</label>
                    <div class="relative">
                        <span
                            class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Nombre, sustancia activa, laboratorio o código..."
                            class="{{ $inputCls }} pl-9">
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit"
                        class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all">
                        Buscar
                    </button>
                    @if (request('search'))
                        <a href="{{ route('medicamentos.index') }}"
                            class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition-all">
                            Limpiar
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Tabla --}}
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="font-extrabold text-slate-800 text-base">Catálogo de medicamentos</h3>
                <p class="text-[11px] text-slate-400">{{ $medicamentos->total() }} medicamentos registrados</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-slate-50/70 border-b border-slate-100">
                        <tr>
                            <th class="{{ $thCls }} text-left">Medicamento</th>
                            <th class="{{ $thCls }} text-left">Sustancia Activa</th>
                            <th class="{{ $thCls }} text-left">Presentación</th>
                            <th class="{{ $thCls }} text-left">Estado</th>
                            <th class="px-5 py-3.5"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($medicamentos as $med)
                            <tr class="hover:bg-indigo-50/30 transition-colors">
                                <td class="px-5 py-3.5">
                                    <p class="text-xs font-bold text-slate-800">{{ $med->nombre }}</p>
                                </td>
                                <td class="px-5 py-3.5">
                                    <p class="text-xs font-medium text-slate-600">{{ $med->sustancia_activa ?? '—' }}
                                    </p>
                                </td>
                                <td class="px-5 py-3.5">
                                    <p class="text-xs font-medium text-slate-600">{{ $med->presentacion ?? '—' }}</p>
                                </td>
                                <td class="px-5 py-3.5">
                                    @if ($med->activo ?? true)
                                        <span
                                            class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-full text-[10px] font-bold uppercase tracking-wide">
                                            Activo
                                        </span>
                                    @else
                                        <span
                                            class="px-2.5 py-1 bg-slate-100 text-slate-500 rounded-full text-[10px] font-bold uppercase tracking-wide">
                                            Inactivo
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex justify-end gap-2" x-data="{ openShow: false, openEdit: false, openDelete: false }">

                                        <button @click="openShow = true" title="Ver" type="button"
                                            class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-[11px] font-bold transition-all">
                                            Ver
                                        </button>

                                        <button @click="openEdit = true" title="Editar" type="button"
                                            class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg text-[11px] font-bold transition-all">
                                            Editar
                                        </button>

                                        <button @click="openDelete = true" title="Eliminar" type="button"
                                            class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-[11px] font-bold transition-all">
                                            Eliminar
                                        </button>

                                        {{-- MODAL VER --}}
                                        <template x-teleport="body">
                                            <div x-show="openShow" x-cloak
                                                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
                                                <div @click.away="openShow = false"
                                                    class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 space-y-5 text-left max-h-[90vh] overflow-y-auto">

                                                    <div
                                                        class="flex justify-between items-center pb-4 border-b border-slate-100">
                                                        <div class="flex items-center gap-3">
                                                            <div
                                                                class="w-10 h-10 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0">
                                                                <svg class="w-5 h-5" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                                                </svg>
                                                            </div>
                                                            <div>
                                                                <h3 class="font-extrabold text-slate-800 text-base">
                                                                    Detalles del Medicamento</h3>
                                                                <p class="text-[11px] text-slate-400">Información
                                                                    completa</p>
                                                            </div>
                                                        </div>
                                                        <button @click="openShow = false"
                                                            class="text-slate-400 hover:text-slate-700 text-xl leading-none">✕</button>
                                                    </div>

                                                    <div class="space-y-4">
                                                        <div class="flex items-start justify-between gap-3">
                                                            <div>
                                                                <p class="{{ $labelCls }}">Identificación</p>
                                                                <p
                                                                    class="text-base font-extrabold text-slate-800 mt-1">
                                                                    {{ $med->nombre }}</p>
                                                                <p class="text-xs font-bold text-indigo-600 mt-0.5">
                                                                    {{ $med->sustancia_activa ?? 'Sin sustancia activa' }}
                                                                </p>
                                                            </div>
                                                            @if ($med->activo ?? true)
                                                                <span
                                                                    class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-full text-[10px] font-bold uppercase tracking-wide shrink-0">
                                                                    Activo
                                                                </span>
                                                            @else
                                                                <span
                                                                    class="px-2.5 py-1 bg-slate-100 text-slate-500 rounded-full text-[10px] font-bold uppercase tracking-wide shrink-0">
                                                                    Inactivo
                                                                </span>
                                                            @endif
                                                        </div>

                                                        <div
                                                            class="grid grid-cols-2 gap-3 pt-4 border-t border-slate-100">
                                                            <div
                                                                class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                                                                <p
                                                                    class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                                                    Laboratorio</p>
                                                                <p
                                                                    class="text-xs font-extrabold text-slate-800 mt-0.5">
                                                                    {{ $med->laboratorio ?? '—' }}</p>
                                                            </div>
                                                            <div
                                                                class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                                                                <p
                                                                    class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                                                    Presentación</p>
                                                                <p
                                                                    class="text-xs font-extrabold text-slate-800 mt-0.5">
                                                                    {{ $med->presentacion ?? '—' }}</p>
                                                            </div>
                                                            <div
                                                                class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                                                                <p
                                                                    class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                                                    Concentración</p>
                                                                <p
                                                                    class="text-xs font-extrabold text-slate-800 mt-0.5">
                                                                    {{ $med->concentracion ?? '—' }}</p>
                                                            </div>
                                                            <div
                                                                class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                                                                <p
                                                                    class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                                                    Vía Admin.</p>
                                                                <p
                                                                    class="text-xs font-extrabold text-slate-800 mt-0.5">
                                                                    {{ $med->via_administracion ?? '—' }}</p>
                                                            </div>
                                                            <div
                                                                class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                                                                <p
                                                                    class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                                                    Grupo Terapéutico</p>
                                                                <p
                                                                    class="text-xs font-extrabold text-slate-800 mt-0.5">
                                                                    {{ $med->grupo_terapeutico ?? '—' }}</p>
                                                            </div>
                                                            <div
                                                                class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                                                                <p
                                                                    class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                                                    Reg. Sanitario</p>
                                                                <p
                                                                    class="text-xs font-extrabold text-slate-800 mt-0.5">
                                                                    {{ $med->registro_sanitario ?? '—' }}</p>
                                                            </div>
                                                            <div
                                                                class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                                                                <p
                                                                    class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                                                    Código Barras</p>
                                                                <p
                                                                    class="text-xs font-mono font-bold text-slate-800 mt-0.5">
                                                                    {{ $med->codigo_barras ?? '—' }}</p>
                                                            </div>
                                                            <div
                                                                class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                                                                <p
                                                                    class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                                                    Unidad Medida</p>
                                                                <p
                                                                    class="text-xs font-extrabold text-slate-800 mt-0.5">
                                                                    {{ ucfirst($med->unidad_medida ?? 'Pieza') }}</p>
                                                            </div>
                                                        </div>

                                                        <div class="pt-4 border-t border-slate-100">
                                                            <p class="{{ $labelCls }} mb-2">Clasificación</p>
                                                            <div class="flex flex-wrap gap-1.5">
                                                                @if ($med->psicotropico)
                                                                    <span
                                                                        class="px-2.5 py-1 bg-purple-50 text-purple-700 border border-purple-100 rounded-full text-[10px] font-bold uppercase tracking-wide">
                                                                        Psicotrópico
                                                                    </span>
                                                                @endif
                                                                @if ($med->antibiotico)
                                                                    <span
                                                                        class="px-2.5 py-1 bg-indigo-50 text-indigo-700 border border-indigo-100 rounded-full text-[10px] font-bold uppercase tracking-wide">
                                                                        Antibiótico
                                                                    </span>
                                                                @endif
                                                                @if ($med->controlado)
                                                                    <span
                                                                        class="px-2.5 py-1 bg-rose-50 text-rose-700 border border-rose-100 rounded-full text-[10px] font-bold uppercase tracking-wide">
                                                                        Controlado
                                                                    </span>
                                                                @endif
                                                                @if (!$med->psicotropico && !$med->antibiotico && !$med->controlado)
                                                                    <span
                                                                        class="text-slate-300 text-xs italic">Ninguna</span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div
                                                            class="pt-4 border-t border-slate-100 grid grid-cols-3 gap-3">
                                                            <div
                                                                class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3 text-center">
                                                                <p
                                                                    class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                                                    Stock Mín / Máx</p>
                                                                <p
                                                                    class="text-xs font-extrabold text-slate-800 mt-0.5">
                                                                    {{ $med->stock_minimo ?? 0 }} /
                                                                    {{ $med->stock_maximo ?? 0 }}</p>
                                                            </div>
                                                            <div
                                                                class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3 text-center">
                                                                <p
                                                                    class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                                                    Precio Compra</p>
                                                                <p
                                                                    class="text-xs font-extrabold text-slate-800 mt-0.5">
                                                                    {{ isset($med->precio_compra) ? '$' . number_format($med->precio_compra, 2) : '—' }}
                                                                </p>
                                                            </div>
                                                            <div
                                                                class="bg-emerald-50/60 border border-emerald-100 rounded-2xl p-3 text-center">
                                                                <p
                                                                    class="text-[10px] font-bold text-emerald-600/70 uppercase tracking-wider">
                                                                    Precio Venta</p>
                                                                <p
                                                                    class="text-xs font-extrabold text-emerald-600 mt-0.5">
                                                                    {{ isset($med->precio_venta) ? '$' . number_format($med->precio_venta, 2) : '—' }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="flex justify-end pt-4 border-t border-slate-100">
                                                        <button @click="openShow = false" type="button"
                                                            class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-600 rounded-xl text-xs font-bold transition-all">
                                                            Cerrar
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>

                                        {{-- MODAL EDITAR --}}
                                        <template x-teleport="body">
                                            <div x-show="openEdit" x-cloak
                                                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
                                                <div @click.away="openEdit = false"
                                                    class="bg-white rounded-3xl max-w-2xl w-full p-6 shadow-2xl border border-slate-100 space-y-5 text-left max-h-[90vh] overflow-y-auto">

                                                    <div
                                                        class="flex justify-between items-center pb-4 border-b border-slate-100">
                                                        <div class="flex items-center gap-3">
                                                            <div
                                                                class="w-10 h-10 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0">
                                                                <svg class="w-5 h-5" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                                </svg>
                                                            </div>
                                                            <div>
                                                                <h3 class="font-extrabold text-slate-800 text-base">
                                                                    Editar Medicamento</h3>
                                                                <p class="text-[11px] text-slate-400">
                                                                    {{ $med->nombre }}</p>
                                                            </div>
                                                        </div>
                                                        <button @click="openEdit = false"
                                                            class="text-slate-400 hover:text-slate-700 text-xl leading-none">✕</button>
                                                    </div>

                                                    <form action="{{ route('medicamentos.update', $med) }}"
                                                        method="POST" class="space-y-5">
                                                        @csrf @method('PUT')

                                                        <div>
                                                            <p class="{{ $labelCls }} mb-3">Identificación</p>
                                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                                <div class="md:col-span-2">
                                                                    <label class="{{ $labelCls }}">Nombre
                                                                        comercial *</label>
                                                                    <input type="text" name="nombre"
                                                                        value="{{ old('nombre', $med->nombre) }}"
                                                                        required class="{{ $inputCls }}">
                                                                </div>
                                                                <div>
                                                                    <label class="{{ $labelCls }}">Sustancia
                                                                        activa</label>
                                                                    <input type="text" name="sustancia_activa"
                                                                        value="{{ old('sustancia_activa', $med->sustancia_activa) }}"
                                                                        class="{{ $inputCls }}">
                                                                </div>
                                                                <div>
                                                                    <label
                                                                        class="{{ $labelCls }}">Laboratorio</label>
                                                                    <input type="text" name="laboratorio"
                                                                        value="{{ old('laboratorio', $med->laboratorio) }}"
                                                                        class="{{ $inputCls }}">
                                                                </div>
                                                                <div>
                                                                    <label
                                                                        class="{{ $labelCls }}">Presentación</label>
                                                                    <select name="presentacion"
                                                                        class="{{ $inputCls }}">
                                                                        <option value="">— Selecciona —</option>
                                                                        @foreach (['Tableta', 'Cápsula', 'Jarabe', 'Suspensión', 'Solución inyectable', 'Crema', 'Ungüento', 'Gotas', 'Supositorio', 'Parche'] as $pres)
                                                                            <option value="{{ $pres }}"
                                                                                @selected(old('presentacion', $med->presentacion) == $pres)>
                                                                                {{ $pres }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div>
                                                                    <label
                                                                        class="{{ $labelCls }}">Concentración</label>
                                                                    <input type="text" name="concentracion"
                                                                        value="{{ old('concentracion', $med->concentracion) }}"
                                                                        placeholder="500 mg, 10 ml, 5 %"
                                                                        class="{{ $inputCls }}">
                                                                </div>
                                                                <div>
                                                                    <label class="{{ $labelCls }}">Vía de
                                                                        administración</label>
                                                                    <select name="via_administracion"
                                                                        class="{{ $inputCls }}">
                                                                        <option value="">— Selecciona —</option>
                                                                        @foreach (['Oral', 'Intravenosa', 'Intramuscular', 'Subcutánea', 'Tópica', 'Oftálmica', 'Ótica', 'Nasal', 'Rectal', 'Sublingual', 'Inhalatoria'] as $via)
                                                                            <option value="{{ $via }}"
                                                                                @selected(old('via_administracion', $med->via_administracion) == $via)>
                                                                                {{ $via }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div>
                                                                    <label class="{{ $labelCls }}">Grupo
                                                                        terapéutico</label>
                                                                    <input type="text" name="grupo_terapeutico"
                                                                        value="{{ old('grupo_terapeutico', $med->grupo_terapeutico) }}"
                                                                        placeholder="Analgésico, Antibiótico..."
                                                                        class="{{ $inputCls }}">
                                                                </div>
                                                                <div>
                                                                    <label class="{{ $labelCls }}">Código de
                                                                        barras</label>
                                                                    <input type="text" name="codigo_barras"
                                                                        value="{{ old('codigo_barras', $med->codigo_barras) }}"
                                                                        class="{{ $inputCls }}">
                                                                </div>
                                                                <div>
                                                                    <label class="{{ $labelCls }}">Registro
                                                                        sanitario</label>
                                                                    <input type="text" name="registro_sanitario"
                                                                        value="{{ old('registro_sanitario', $med->registro_sanitario) }}"
                                                                        class="{{ $inputCls }}">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="pt-5 border-t border-slate-100">
                                                            <p class="{{ $labelCls }} mb-3">Clasificación</p>
                                                            <div class="flex flex-wrap gap-3">
                                                                <label
                                                                    class="inline-flex items-center gap-2.5 cursor-pointer px-3 py-2 bg-slate-50/70 border border-slate-100 rounded-xl">
                                                                    <input type="checkbox" name="psicotropico"
                                                                        value="1"
                                                                        {{ old('psicotropico', $med->psicotropico) ? 'checked' : '' }}
                                                                        class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 focus:ring-0 cursor-pointer">
                                                                    <span
                                                                        class="text-xs font-bold text-slate-700">Psicotrópico</span>
                                                                </label>
                                                                <label
                                                                    class="inline-flex items-center gap-2.5 cursor-pointer px-3 py-2 bg-slate-50/70 border border-slate-100 rounded-xl">
                                                                    <input type="checkbox" name="antibiotico"
                                                                        value="1"
                                                                        {{ old('antibiotico', $med->antibiotico) ? 'checked' : '' }}
                                                                        class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 focus:ring-0 cursor-pointer">
                                                                    <span
                                                                        class="text-xs font-bold text-slate-700">Antibiótico</span>
                                                                </label>
                                                                <label
                                                                    class="inline-flex items-center gap-2.5 cursor-pointer px-3 py-2 bg-slate-50/70 border border-slate-100 rounded-xl">
                                                                    <input type="checkbox" name="controlado"
                                                                        value="1"
                                                                        {{ old('controlado', $med->controlado) ? 'checked' : '' }}
                                                                        class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 focus:ring-0 cursor-pointer">
                                                                    <span
                                                                        class="text-xs font-bold text-slate-700">Controlado</span>
                                                                </label>
                                                            </div>
                                                        </div>

                                                        <div class="pt-5 border-t border-slate-100">
                                                            <p class="{{ $labelCls }} mb-3">Inventario y Precios
                                                            </p>
                                                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                                                <div>
                                                                    <label class="{{ $labelCls }}">Unidad medida
                                                                        *</label>
                                                                    <select name="unidad_medida" required
                                                                        class="{{ $inputCls }}">
                                                                        @foreach (['pieza', 'caja', 'frasco', 'ampolleta', 'tubo', 'sobre', 'blíster'] as $um)
                                                                            <option value="{{ $um }}"
                                                                                @selected(old('unidad_medida', $med->unidad_medida ?? 'pieza') == $um)>
                                                                                {{ ucfirst($um) }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div>
                                                                    <label class="{{ $labelCls }}">Stock mín
                                                                        *</label>
                                                                    <input type="number" name="stock_minimo"
                                                                        min="0"
                                                                        value="{{ old('stock_minimo', $med->stock_minimo ?? 0) }}"
                                                                        required class="{{ $inputCls }}">
                                                                </div>
                                                                <div>
                                                                    <label class="{{ $labelCls }}">Stock máx
                                                                        *</label>
                                                                    <input type="number" name="stock_maximo"
                                                                        min="0"
                                                                        value="{{ old('stock_maximo', $med->stock_maximo ?? 0) }}"
                                                                        required class="{{ $inputCls }}">
                                                                </div>
                                                                <div>
                                                                    <label class="{{ $labelCls }}">Precio
                                                                        compra</label>
                                                                    <input type="number" step="0.01"
                                                                        min="0" name="precio_compra"
                                                                        value="{{ old('precio_compra', $med->precio_compra) }}"
                                                                        class="{{ $inputCls }}">
                                                                </div>
                                                                <div class="col-span-2 md:col-span-4">
                                                                    <label class="{{ $labelCls }}">Precio
                                                                        venta</label>
                                                                    <input type="number" step="0.01"
                                                                        min="0" name="precio_venta"
                                                                        value="{{ old('precio_venta', $med->precio_venta) }}"
                                                                        class="{{ $inputCls }}">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="pt-5 border-t border-slate-100">
                                                            <p class="{{ $labelCls }} mb-3">Estado</p>
                                                            <label
                                                                class="inline-flex items-center gap-2.5 cursor-pointer px-3 py-2 bg-emerald-50/60 border border-emerald-100 rounded-xl">
                                                                <input type="checkbox" name="activo" value="1"
                                                                    {{ old('activo', $med->activo ?? true) ? 'checked' : '' }}
                                                                    class="w-4 h-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 focus:ring-0 cursor-pointer">
                                                                <span
                                                                    class="text-xs font-bold text-emerald-700">Medicamento
                                                                    activo</span>
                                                            </label>
                                                        </div>

                                                        <div
                                                            class="flex justify-end gap-2 pt-5 border-t border-slate-100">
                                                            <button @click="openEdit = false" type="button"
                                                                class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-600 rounded-xl text-xs font-bold transition-all">
                                                                Cancelar
                                                            </button>
                                                            <button type="submit"
                                                                class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all">
                                                                Guardar cambios
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </template>

                                        {{-- MODAL ELIMINAR --}}
                                        <template x-teleport="body">
                                            <div x-show="openDelete" x-cloak
                                                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
                                                <div @click.away="openDelete = false"
                                                    class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-5 text-center">
                                                    <div
                                                        class="w-14 h-14 rounded-2xl bg-rose-50 flex items-center justify-center text-rose-500 mx-auto">
                                                        <svg class="w-7 h-7" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <h3 class="text-base font-extrabold text-slate-800">¿Eliminar
                                                            medicamento?</h3>
                                                        <p class="text-xs text-slate-500 mt-1">Esta acción no se puede
                                                            deshacer.</p>
                                                    </div>
                                                    <form action="{{ route('medicamentos.destroy', $med) }}"
                                                        method="POST"
                                                        class="flex justify-center gap-2 pt-4 border-t border-slate-100">
                                                        @csrf @method('DELETE')
                                                        <button @click="openDelete = false" type="button"
                                                            class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-600 rounded-xl text-xs font-bold transition-all">
                                                            Cancelar
                                                        </button>
                                                        <button type="submit"
                                                            class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all">
                                                            Sí, eliminar
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </template>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-12 text-center">
                                    <p class="text-xs font-semibold text-slate-400">No se encontraron medicamentos
                                        registrados.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- MODAL CREAR GLOBAL --}}
    <template x-teleport="body">
        <div x-show="$store.medicamentos.openCreate" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
            <div @click.away="$store.medicamentos.openCreate = false"
                class="bg-white rounded-3xl max-w-2xl w-full p-6 shadow-2xl border border-slate-100 space-y-5 text-left max-h-[90vh] overflow-y-auto">

                <div class="flex justify-between items-center pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-slate-800 text-base">Nuevo Medicamento</h3>
                            <p class="text-[11px] text-slate-400">Completa la información del medicamento</p>
                        </div>
                    </div>
                    <button @click="$store.medicamentos.openCreate = false"
                        class="text-slate-400 hover:text-slate-700 text-xl leading-none">✕</button>
                </div>

                <form action="{{ route('medicamentos.store') }}" method="POST" class="space-y-5">
                    @csrf

                    <div>
                        <p class="{{ $labelCls }} mb-3">Identificación</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div class="md:col-span-2">
                                <label class="{{ $labelCls }}">Nombre comercial *</label>
                                <input type="text" name="nombre" value="{{ old('nombre') }}" required
                                    class="{{ $inputCls }}">
                            </div>
                            <div>
                                <label class="{{ $labelCls }}">Sustancia activa</label>
                                <input type="text" name="sustancia_activa" value="{{ old('sustancia_activa') }}"
                                    class="{{ $inputCls }}">
                            </div>
                            <div>
                                <label class="{{ $labelCls }}">Laboratorio</label>
                                <input type="text" name="laboratorio" value="{{ old('laboratorio') }}"
                                    class="{{ $inputCls }}">
                            </div>
                            <div>
                                <label class="{{ $labelCls }}">Presentación</label>
                                <select name="presentacion" class="{{ $inputCls }}">
                                    <option value="">— Selecciona —</option>
                                    @foreach (['Tableta', 'Cápsula', 'Jarabe', 'Suspensión', 'Solución inyectable', 'Crema', 'Ungüento', 'Gotas', 'Supositorio', 'Parche'] as $pres)
                                        <option value="{{ $pres }}" @selected(old('presentacion') == $pres)>
                                            {{ $pres }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="{{ $labelCls }}">Concentración</label>
                                <input type="text" name="concentracion" value="{{ old('concentracion') }}"
                                    placeholder="500 mg, 10 ml, 5 %" class="{{ $inputCls }}">
                            </div>
                            <div>
                                <label class="{{ $labelCls }}">Vía de administración</label>
                                <select name="via_administracion" class="{{ $inputCls }}">
                                    <option value="">— Selecciona —</option>
                                    @foreach (['Oral', 'Intravenosa', 'Intramuscular', 'Subcutánea', 'Tópica', 'Oftálmica', 'Ótica', 'Nasal', 'Rectal', 'Sublingual', 'Inhalatoria'] as $via)
                                        <option value="{{ $via }}" @selected(old('via_administracion') == $via)>
                                            {{ $via }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="{{ $labelCls }}">Grupo terapéutico</label>
                                <input type="text" name="grupo_terapeutico"
                                    value="{{ old('grupo_terapeutico') }}" placeholder="Analgésico, Antibiótico..."
                                    class="{{ $inputCls }}">
                            </div>
                            <div>
                                <label class="{{ $labelCls }}">Código de barras</label>
                                <input type="text" name="codigo_barras" value="{{ old('codigo_barras') }}"
                                    class="{{ $inputCls }}">
                            </div>
                            <div>
                                <label class="{{ $labelCls }}">Registro sanitario</label>
                                <input type="text" name="registro_sanitario"
                                    value="{{ old('registro_sanitario') }}" class="{{ $inputCls }}">
                            </div>
                        </div>
                    </div>

                    <div class="pt-5 border-t border-slate-100">
                        <p class="{{ $labelCls }} mb-3">Clasificación</p>
                        <div class="flex flex-wrap gap-3">
                            <label
                                class="inline-flex items-center gap-2.5 cursor-pointer px-3 py-2 bg-slate-50/70 border border-slate-100 rounded-xl">
                                <input type="checkbox" name="psicotropico" value="1"
                                    {{ old('psicotropico') ? 'checked' : '' }}
                                    class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 focus:ring-0 cursor-pointer">
                                <span class="text-xs font-bold text-slate-700">Psicotrópico</span>
                            </label>
                            <label
                                class="inline-flex items-center gap-2.5 cursor-pointer px-3 py-2 bg-slate-50/70 border border-slate-100 rounded-xl">
                                <input type="checkbox" name="antibiotico" value="1"
                                    {{ old('antibiotico') ? 'checked' : '' }}
                                    class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 focus:ring-0 cursor-pointer">
                                <span class="text-xs font-bold text-slate-700">Antibiótico</span>
                            </label>
                            <label
                                class="inline-flex items-center gap-2.5 cursor-pointer px-3 py-2 bg-slate-50/70 border border-slate-100 rounded-xl">
                                <input type="checkbox" name="controlado" value="1"
                                    {{ old('controlado') ? 'checked' : '' }}
                                    class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 focus:ring-0 cursor-pointer">
                                <span class="text-xs font-bold text-slate-700">Controlado</span>
                            </label>
                        </div>
                    </div>

                    <div class="pt-5 border-t border-slate-100">
                        <p class="{{ $labelCls }} mb-3">Inventario y Precios</p>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <div>
                                <label class="{{ $labelCls }}">Unidad medida *</label>
                                <select name="unidad_medida" required class="{{ $inputCls }}">
                                    @foreach (['pieza', 'caja', 'frasco', 'ampolleta', 'tubo', 'sobre', 'blíster'] as $um)
                                        <option value="{{ $um }}" @selected(old('unidad_medida', 'pieza') == $um)>
                                            {{ ucfirst($um) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="{{ $labelCls }}">Stock mín *</label>
                                <input type="number" name="stock_minimo" min="0"
                                    value="{{ old('stock_minimo', 0) }}" required class="{{ $inputCls }}">
                            </div>
                            <div>
                                <label class="{{ $labelCls }}">Stock máx *</label>
                                <input type="number" name="stock_maximo" min="0"
                                    value="{{ old('stock_maximo', 0) }}" required class="{{ $inputCls }}">
                            </div>
                            <div>
                                <label class="{{ $labelCls }}">Precio compra</label>
                                <input type="number" step="0.01" min="0" name="precio_compra"
                                    value="{{ old('precio_compra') }}" class="{{ $inputCls }}">
                            </div>
                            <div class="col-span-2 md:col-span-4">
                                <label class="{{ $labelCls }}">Precio venta</label>
                                <input type="number" step="0.01" min="0" name="precio_venta"
                                    value="{{ old('precio_venta') }}" class="{{ $inputCls }}">
                            </div>
                        </div>
                    </div>

                    <div class="pt-5 border-t border-slate-100">
                        <p class="{{ $labelCls }} mb-3">Estado</p>
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
                            class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-600 rounded-xl text-xs font-bold transition-all">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all">
                            Guardar medicamento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

</x-app-layout>
