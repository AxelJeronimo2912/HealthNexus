<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-slate-100 rounded-2xl text-slate-600">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-2xl text-slate-800">Gestión de Camas</h2>
                    <p class="text-xs text-slate-500 font-medium">Control de disponibilidad y equipamiento hospitalario de HealthNexus</p>
                </div>
            </div>
            <button @click="$dispatch('open-modal', 'modal-crear-cama')"
                class="bg-indigo-950 hover:bg-indigo-900 text-white font-semibold px-5 py-2.5 rounded-full text-sm shadow-md transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Nueva Cama</span>
            </button>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6" x-data="{ modalDetalle: null, modalEditar: null }">

        @if (session('success'))
            <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded-r-xl shadow-sm text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        {{-- Tarjetas de Métricas estilo HealthNexus --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4">
                <div class="p-3.5 bg-slate-100 text-slate-600 rounded-2xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">TOTAL CAMAS</p>
                    <p class="text-2xl font-black text-slate-800">{{ $stats['total'] }}</p>
                </div>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4">
                <div class="p-3.5 bg-purple-50 text-purple-600 rounded-2xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">DISPONIBLES</p>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-600 mt-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> {{ $stats['disponibles'] }} Libres
                    </span>
                </div>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4">
                <div class="p-3.5 bg-amber-50 text-amber-500 rounded-2xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">OCUPADAS</p>
                    <p class="text-xl font-bold text-slate-800 mt-0.5">{{ $stats['ocupadas'] }} Pacientes</p>
                </div>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4">
                <div class="p-3.5 bg-rose-50 text-rose-500 rounded-2xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">EN MANTENIMIENTO</p>
                    <p class="text-xl font-bold text-slate-800 mt-0.5">{{ $stats['mantenimiento'] }} Camas</p>
                </div>
            </div>
        </div>

        {{-- Barra de Filtros --}}
        <form method="GET" action="{{ route('camas.index') }}" class="bg-white p-3 rounded-2xl shadow-sm border border-slate-100 flex flex-wrap gap-3 items-center">
            <div class="flex-1 min-w-[240px] relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <input type="text" name="buscar" value="{{ $busqueda }}"
                    placeholder="Buscar por código, área o habitación..."
                    class="w-full pl-10 pr-4 py-2 bg-slate-50 border-0 rounded-xl text-sm focus:ring-2 focus:ring-indigo-950">
            </div>
            <select name="estado" class="bg-slate-50 border-0 rounded-xl text-sm py-2 px-4 focus:ring-2 focus:ring-indigo-950 text-slate-600">
                <option value="">Todos los estados</option>
                @foreach (['disponible' => 'Disponible', 'ocupada' => 'Ocupada', 'mantenimiento' => 'Mantenimiento', 'limpieza' => 'Limpieza', 'fuera_servicio' => 'Fuera de Servicio'] as $val => $lbl)
                    <option value="{{ $val }}" @selected($filtroEstado == $val)>{{ $lbl }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-5 py-2 bg-indigo-950 hover:bg-indigo-900 text-white font-medium rounded-xl text-sm shadow-sm transition-all">
                Filtrar
            </button>
            @if ($busqueda || $filtroEstado)
                <a href="{{ route('camas.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-medium rounded-xl text-sm transition-all">
                    Limpiar
                </a>
            @endif
        </form>

        {{-- Tabla de Camas estilo Directorio --}}
        <div class="bg-white shadow-sm rounded-3xl border border-slate-100 overflow-hidden">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">CAMAS / CÓDIGO</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">UBICACIÓN</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">TIPO</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">ESTADO</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-slate-400 uppercase tracking-wider">ACCIONES</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white text-sm">
                    @forelse ($camas as $cama)
                        <tr class="group hover:bg-slate-50/80 transition-colors">
                            {{-- Columna 1: Código / Identificador --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-slate-100 group-hover:bg-indigo-950 text-slate-700 group-hover:text-white font-bold text-xs flex items-center justify-center uppercase transition-all shadow-sm group-hover:scale-105">
                                        {{ substr($cama->codigo, 0, 2) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-800 group-hover:text-indigo-950 transition-colors">{{ $cama->codigo }}</div>
                                        <div class="text-xs text-slate-400">ID: #{{ $cama->id }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- Columna 2: Ubicación --}}
                            <td class="px-6 py-4 text-slate-600">
                                <div class="font-medium text-slate-700">{{ $cama->area ?? 'Sin área' }}</div>
                                <div class="text-xs text-slate-400">Hab: {{ $cama->habitacion ?? 'N/A' }} | Piso: {{ $cama->piso ?? 'N/A' }}</div>
                            </td>

                            {{-- Columna 3: Tipo --}}
                            <td class="px-6 py-4 text-slate-600">
                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-600 inline-flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $cama->tipo_label }}
                                </span>
                            </td>

                            {{-- Columna 4: Estado --}}
                            <td class="px-6 py-4">
                                @switch($cama->estado)
                                    @case('disponible')
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            {{ $cama->estado_label }}
                                        </span>
                                        @break

                                    @case('ocupada')
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold inline-flex items-center gap-1.5 bg-amber-50 text-amber-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            {{ $cama->estado_label }}
                                        </span>
                                        @break

                                    @case('mantenimiento')
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold inline-flex items-center gap-1.5 bg-rose-50 text-rose-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                            {{ $cama->estado_label }}
                                        </span>
                                        @break

                                    @case('limpieza')
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold inline-flex items-center gap-1.5 bg-sky-50 text-sky-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                                            {{ $cama->estado_label }}
                                        </span>
                                        @break

                                    @default
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold inline-flex items-center gap-1.5 bg-slate-100 text-slate-600">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                            {{ $cama->estado_label }}
                                        </span>
                                @endswitch
                            </td>

                            {{-- Columna 5: Acciones --}}
                            <td class="px-6 py-4 text-right space-x-1">
                                <button @click="modalDetalle = {{ $cama->toJson() }}; $dispatch('open-modal', 'modal-ver-cama')"
                                    class="p-1.5 text-slate-700 hover:text-indigo-900 rounded-lg transition-colors" title="Ver detalles">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                                <button @click="modalEditar = {{ $cama->toJson() }}; $dispatch('open-modal', 'modal-editar-cama')"
                                    class="p-1.5 text-purple-600 hover:text-purple-800 rounded-lg transition-colors" title="Editar">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <form action="{{ route('camas.destroy', $cama) }}" method="POST" class="inline"
                                    onsubmit="return confirm('¿Eliminar esta cama?')">
                                    @csrf @method('DELETE')
                                    <button class="p-1.5 text-rose-500 hover:text-rose-700 rounded-lg transition-colors" title="Eliminar">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                No se encontraron camas registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $camas->links() }}</div>

        {{-- Modal Crear Cama --}}
        <x-modal name="modal-crear-cama" :show="$errors->any()" focusable>
            <form action="{{ route('camas.store') }}" method="POST" class="p-6">
                @csrf
                <div class="flex justify-between items-center pb-4 border-b border-slate-100">
                    <h3 class="text-lg font-bold text-slate-800">Registrar Nueva Cama</h3>
                    <button type="button" @click="$dispatch('close-modal', 'modal-crear-cama')" class="text-slate-400 hover:text-slate-600">✕</button>
                </div>
                <div class="py-4">
                    @include('camas._form', ['cama' => null])
                </div>
                <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" @click="$dispatch('close-modal', 'modal-crear-cama')" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl text-sm font-medium">Cancelar</button>
                    <button type="submit" class="px-5 py-2 bg-indigo-950 text-white rounded-xl text-sm font-medium">Guardar Cama</button>
                </div>
            </form>
        </x-modal>

        {{-- Modal Editar Cama --}}
        <x-modal name="modal-editar-cama" focusable>
            <template x-if="modalEditar">
                <form :action="`/camas/${modalEditar.id}`" method="POST" class="p-6">
                    @csrf
                    @method('PUT')
                    <div class="flex justify-between items-center pb-4 border-b border-slate-100">
                        <h3 class="text-lg font-bold text-slate-800">Editar Cama: <span class="text-indigo-950" x-text="modalEditar.codigo"></span></h3>
                        <button type="button" @click="$dispatch('close-modal', 'modal-editar-cama')" class="text-slate-400 hover:text-slate-600">✕</button>
                    </div>
                    <div class="py-4">
                        @include('camas._form_js')
                    </div>
                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" @click="$dispatch('close-modal', 'modal-editar-cama')" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl text-sm font-medium">Cancelar</button>
                        <button type="submit" class="px-5 py-2 bg-indigo-950 text-white rounded-xl text-sm font-medium">Actualizar Cama</button>
                    </div>
                </form>
            </template>
        </x-modal>

        {{-- Modal Ver Cama --}}
<x-modal name="modal-ver-cama" focusable>
    <template x-if="modalDetalle">
        <div class="p-6">
            {{-- Encabezado con Código y Estado de Cama --}}
            <div class="flex justify-between items-center pb-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <h3 class="text-xl font-bold text-slate-800" x-text="modalDetalle.codigo"></h3>
                    <template x-if="modalDetalle.estado">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider"
                              :class="{
                                  'bg-emerald-50 text-emerald-700': modalDetalle.estado === 'disponible',
                                  'bg-amber-50 text-amber-700': modalDetalle.estado === 'ocupada',
                                  'bg-rose-50 text-rose-700': modalDetalle.estado === 'mantenimiento',
                                  'bg-sky-50 text-sky-700': modalDetalle.estado === 'limpieza',
                                  'bg-slate-100 text-slate-600': !['disponible','ocupada','mantenimiento','limpieza'].includes(modalDetalle.estado)
                              }"
                              x-text="modalDetalle.estado_label || modalDetalle.estado">
                        </span>
                    </template>
                </div>
                <button type="button" @click="$dispatch('close-modal', 'modal-ver-cama')" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
            </div>

            <div class="py-6 space-y-6 text-sm text-slate-700">
                {{-- Sección Ubicación --}}
                <div>
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Ubicación</h4>
                    <div class="grid grid-cols-2 gap-4 bg-slate-50 p-4 rounded-2xl border border-slate-100">
                        <div>
                            <span class="text-xs font-semibold text-slate-400 block uppercase">ÁREA</span>
                            <span class="font-bold text-slate-800" x-text="modalDetalle.area || 'Sin área'"></span>
                        </div>
                        <div>
                            <span class="text-xs font-semibold text-slate-400 block uppercase">HABITACIÓN</span>
                            <span class="font-bold text-slate-800" x-text="modalDetalle.habitacion || 'N/A'"></span>
                        </div>
                        <div>
                            <span class="text-xs font-semibold text-slate-400 block uppercase">PISO</span>
                            <span class="font-bold text-slate-800" x-text="modalDetalle.piso || 'N/A'"></span>
                        </div>
                        <div>
                            <span class="text-xs font-semibold text-slate-400 block uppercase">ALA</span>
                            <span class="font-bold text-slate-800" x-text="modalDetalle.ala || 'N/A'"></span>
                        </div>
                    </div>
                </div>

                {{-- Sección Info Adicional (Grilla de 2 columnas) --}}
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Tipo</h4>
                        <p class="font-semibold text-slate-800 capitalize" x-text="modalDetalle.tipo_label || modalDetalle.tipo || 'N/A'"></p>
                    </div>

                    <div>
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Estado del registro</h4>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold inline-flex items-center gap-1.5"
                              :class="modalDetalle.is_active !== false ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600'"
                              x-text="modalDetalle.is_active !== false ? 'Activa' : 'Inactiva'">
                        </span>
                    </div>

                    <div class="col-span-2">
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Equipo</h4>
                        <p class="text-slate-600 font-medium" x-text="modalDetalle.equipo || 'Sin equipo especial'"></p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-slate-100">
                <button type="button" @click="$dispatch('close-modal', 'modal-ver-cama')" class="px-5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-sm font-medium transition-colors">
                    Volver
                </button>
            </div>
        </div>
    </template>
</x-modal>

    </div>
</x-app-layout>