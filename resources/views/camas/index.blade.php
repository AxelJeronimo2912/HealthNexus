<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-slate-100 rounded-2xl text-slate-600">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-2xl text-slate-800">Gestión de Camas</h2>
                    <p class="text-xs text-slate-500 font-medium">Control de disponibilidad y equipamiento hospitalario
                        de HealthNexus</p>
                </div>
            </div>
            <button @click="$dispatch('open-modal', 'modal-crear-cama')"
                class="bg-indigo-950 hover:bg-indigo-900 text-white font-semibold px-5 py-2.5 rounded-full text-sm shadow-md transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Nueva Cama</span>
            </button>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6" x-data="{ modalDetalle: null, modalEditar: null }">

        @if (session('success'))
            <div
                class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded-r-xl shadow-sm text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div
                class="p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 rounded-r-xl shadow-sm text-sm font-medium">
                {{ session('error') }}
            </div>
        @endif
        @if (session('info'))
            <div
                class="p-4 bg-sky-50 border-l-4 border-sky-500 text-sky-800 rounded-r-xl shadow-sm text-sm font-medium">
                {{ session('info') }}
            </div>
        @endif

        {{-- Tarjetas de Métricas estilo HealthNexus --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4">
                <div class="p-3.5 bg-slate-100 text-slate-600 rounded-2xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">TOTAL CAMAS</p>
                    <p class="text-2xl font-black text-slate-800">{{ $stats['total'] }}</p>
                </div>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4">
                <div class="p-3.5 bg-purple-50 text-purple-600 rounded-2xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">DISPONIBLES</p>
                    <span
                        class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-600 mt-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> {{ $stats['disponibles'] }} Libres
                    </span>
                </div>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4">
                <div class="p-3.5 bg-amber-50 text-amber-500 rounded-2xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">OCUPADAS</p>
                    <p class="text-xl font-bold text-slate-800 mt-0.5">{{ $stats['ocupadas'] }} Pacientes</p>
                </div>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4">
                <div class="p-3.5 bg-rose-50 text-rose-500 rounded-2xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">EN MANTENIMIENTO</p>
                    <p class="text-xl font-bold text-slate-800 mt-0.5">{{ $stats['mantenimiento'] }} Camas</p>
                </div>
            </div>
        </div>

        {{-- Barra de Filtros --}}
        <form method="GET" action="{{ route('camas.index') }}"
            class="bg-white p-3 rounded-2xl shadow-sm border border-slate-100 flex flex-wrap gap-3 items-center">
            <div class="flex-1 min-w-[240px] relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" name="buscar" value="{{ $busqueda }}"
                    placeholder="Buscar por código, área o habitación..."
                    class="w-full pl-10 pr-4 py-2 bg-slate-50 border-0 rounded-xl text-sm focus:ring-2 focus:ring-indigo-950">
            </div>
            <select name="estado"
                class="bg-slate-50 border-0 rounded-xl text-sm py-2 px-4 focus:ring-2 focus:ring-indigo-950 text-slate-600">
                <option value="">Todos los estados</option>
                @foreach (['disponible' => 'Disponible', 'ocupada' => 'Ocupada', 'mantenimiento' => 'Mantenimiento', 'limpieza' => 'Limpieza', 'fuera_servicio' => 'Fuera de Servicio'] as $val => $lbl)
                    <option value="{{ $val }}" @selected($filtroEstado == $val)>{{ $lbl }}</option>
                @endforeach
            </select>
            <button type="submit"
                class="px-5 py-2 bg-indigo-950 hover:bg-indigo-900 text-white font-medium rounded-xl text-sm shadow-sm transition-all">
                Filtrar
            </button>
            @if ($busqueda || $filtroEstado)
                <a href="{{ route('camas.index') }}"
                    class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-medium rounded-xl text-sm transition-all">
                    Limpiar
                </a>
            @endif
        </form>

        {{-- ══════════════════════════════════════════════════════════════ --}}
        {{-- GRID VISUAL DE CAMAS (BED BOARD)                              --}}
        {{-- ══════════════════════════════════════════════════════════════ --}}
        <div class="bg-white shadow-sm rounded-3xl border border-slate-100 p-6">

            {{-- Leyenda --}}
            <div class="flex flex-wrap items-center gap-4 mb-6 pb-4 border-b border-slate-100">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Leyenda:</span>
                <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-600">
                    <span class="w-3 h-3 rounded-sm bg-emerald-400"></span> Disponible
                </span>
                <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-600">
                    <span class="w-3 h-3 rounded-sm bg-amber-400"></span> Ocupada
                </span>
                <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-600">
                    <span class="w-3 h-3 rounded-sm bg-sky-400"></span> Limpieza
                </span>
                <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-600">
                    <span class="w-3 h-3 rounded-sm bg-rose-400"></span> Mantenimiento
                </span>
                <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-600">
                    <span class="w-3 h-3 rounded-sm bg-slate-300"></span> Fuera de Servicio
                </span>
            </div>

            @php
                $camasPorArea = $camas->groupBy(fn($c) => $c->area ?? 'Sin área asignada');
            @endphp

            @forelse ($camasPorArea as $area => $camasArea)
                <div class="mb-8 last:mb-0">
                    {{-- Título del área --}}
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-4 h-4 text-indigo-950" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <h3 class="font-bold text-slate-700">{{ $area }}</h3>
                        <span class="text-xs font-medium text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full">
                            {{ $camasArea->count() }} camas
                        </span>
                    </div>

                    {{-- Grid de camas --}}
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        @foreach ($camasArea as $cama)
                            @php
                                $colorClasses = match ($cama->estado) {
                                    'disponible' => [
                                        'bg-emerald-50 border-emerald-300 hover:border-emerald-500',
                                        'text-emerald-700',
                                        'bg-emerald-400',
                                    ],
                                    'ocupada' => [
                                        'bg-amber-50 border-amber-300 hover:border-amber-500',
                                        'text-amber-700',
                                        'bg-amber-400',
                                    ],
                                    'limpieza' => [
                                        'bg-sky-50 border-sky-300 hover:border-sky-500',
                                        'text-sky-700',
                                        'bg-sky-400',
                                    ],
                                    'mantenimiento' => [
                                        'bg-rose-50 border-rose-300 hover:border-rose-500',
                                        'text-rose-700',
                                        'bg-rose-400',
                                    ],
                                    default => [
                                        'bg-slate-50 border-slate-300 hover:border-slate-500',
                                        'text-slate-600',
                                        'bg-slate-300',
                                    ],
                                };
                            @endphp

                            <div class="relative group">
                                {{-- Cuerpo de la cama (click → ver detalle) --}}
                                <button type="button"
                                    @click="modalDetalle = {{ $cama->toJson() }}; $dispatch('open-modal', 'modal-ver-cama')"
                                    class="w-full border-2 {{ $colorClasses[0] }} rounded-2xl p-3 transition-all duration-200 hover:shadow-lg hover:-translate-y-0.5 text-left">

                                    {{-- Ícono cama vista cenital --}}
                                    <div class="flex items-center justify-center mb-2">
                                        <div class="relative">
                                            <svg class="w-14 h-14 {{ $colorClasses[1] }}" viewBox="0 0 64 64"
                                                fill="none" stroke="currentColor" stroke-width="2.5"
                                                stroke-linecap="round" stroke-linejoin="round">
                                                <rect x="6" y="14" width="8" height="36" rx="2"
                                                    fill="currentColor" fill-opacity="0.15" />
                                                <rect x="14" y="18" width="40" height="28" rx="3"
                                                    fill="currentColor" fill-opacity="0.15" />
                                                <rect x="18" y="22" width="10" height="10" rx="2"
                                                    fill="currentColor" fill-opacity="0.35" />
                                                <line x1="32" y1="22" x2="32" y2="42" />
                                                <line x1="40" y1="22" x2="40" y2="42" />
                                                <line x1="18" y1="50" x2="18" y2="54" />
                                                <line x1="50" y1="50" x2="50" y2="54" />
                                            </svg>
                                            <span
                                                class="absolute -top-1 -right-1 w-3 h-3 rounded-full {{ $colorClasses[2] }} ring-2 ring-white"></span>
                                        </div>
                                    </div>

                                    {{-- Info --}}
                                    <div class="text-center">
                                        <p class="font-bold text-sm {{ $colorClasses[1] }} truncate">
                                            {{ $cama->codigo }}</p>
                                        <p class="text-[10px] font-medium text-slate-500 truncate mt-0.5">
                                            {{ $cama->habitacion ? 'Hab. ' . $cama->habitacion : 'Sin hab.' }}
                                            @if ($cama->piso)
                                                · {{ $cama->piso }}
                                            @endif
                                        </p>
                                        <p
                                            class="text-[10px] font-semibold uppercase tracking-wider mt-1 {{ $colorClasses[1] }} opacity-80">
                                            {{ $cama->estado_label }}
                                        </p>
                                    </div>
                                </button>

                                {{-- Acciones rápidas al hover --}}
                                <div
                                    class="absolute top-1.5 right-1.5 flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    {{-- Editar --}}
                                    <button type="button"
                                        @click.stop="modalEditar = {{ $cama->toJson() }}; $dispatch('open-modal', 'modal-editar-cama')"
                                        class="w-7 h-7 flex items-center justify-center bg-white/95 backdrop-blur rounded-lg shadow-sm text-purple-600 hover:bg-purple-50 transition-colors"
                                        title="Editar cama">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>

                                    {{-- Eliminar --}}
                                    <form action="{{ route('camas.destroy', $cama) }}" method="POST" class="inline"
                                        onsubmit="return confirm('¿Eliminar la cama {{ $cama->codigo }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="w-7 h-7 flex items-center justify-center bg-white/95 backdrop-blur rounded-lg shadow-sm text-rose-500 hover:bg-rose-50 transition-colors"
                                            title="Eliminar cama">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>

                                {{-- Cambio rápido de estado --}}
                                <div class="absolute bottom-1.5 right-1.5 opacity-0 group-hover:opacity-100 transition-opacity"
                                    x-data="{ openEstado: false }">
                                    <button type="button" @click.stop="openEstado = !openEstado"
                                        class="w-7 h-7 flex items-center justify-center bg-white/95 backdrop-blur rounded-lg shadow-sm text-slate-500 hover:bg-slate-100"
                                        title="Cambiar estado">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                    </button>

                                    {{-- Dropdown de estados --}}
                                    <div x-show="openEstado" x-transition @click.outside="openEstado = false"
                                        class="absolute bottom-9 right-0 w-44 bg-white rounded-xl shadow-xl border border-slate-100 py-1 z-20">
                                        @foreach (['disponible' => 'Disponible', 'ocupada' => 'Ocupada', 'limpieza' => 'Limpieza', 'mantenimiento' => 'Mantenimiento', 'fuera_servicio' => 'Fuera de Servicio'] as $val => $lbl)
                                            <form action="{{ route('camas.cambiarEstado', $cama) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="estado" value="{{ $val }}">
                                                <button type="submit"
                                                    class="w-full text-left px-3 py-1.5 text-xs font-medium hover:bg-slate-50 flex items-center gap-2
                                                        {{ $cama->estado === $val ? 'text-indigo-950 font-bold' : 'text-slate-600' }}">
                                                    <span
                                                        class="w-2 h-2 rounded-full
                                                        @switch($val)
                                                            @case('disponible') bg-emerald-400 @break
                                                            @case('ocupada') bg-amber-400 @break
                                                            @case('limpieza') bg-sky-400 @break
                                                            @case('mantenimiento') bg-rose-400 @break
                                                            @default bg-slate-300
                                                        @endswitch
                                                    "></span>
                                                    {{ $lbl }}
                                                    @if ($cama->estado === $val)
                                                        <svg class="w-3 h-3 ml-auto" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    @endif
                                                </button>
                                            </form>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="py-16 text-center">
                    <svg class="w-16 h-16 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <p class="text-slate-400 font-medium">No se encontraron camas registradas.</p>
                </div>
            @endforelse
        </div>

        <div class="mb-6 pb-4 border-b border-slate-100 flex items-center justify-between">
            <p class="text-sm text-slate-500 font-medium">
                Mostrando <span class="font-bold text-slate-700">{{ $camas->count() }}</span>
                {{ $camas->count() === 1 ? 'cama' : 'camas' }}
                @if ($busqueda || $filtroEstado)
                    <span class="text-slate-400">· con filtros aplicados</span>
                @endif
            </p>
        </div>
        {{-- Modal Crear Cama --}}
        <x-modal name="modal-crear-cama" :show="$errors->any()" focusable>
            <form action="{{ route('camas.store') }}" method="POST" class="p-6">
                @csrf
                <div class="flex justify-between items-center pb-4 border-b border-slate-100">
                    <h3 class="text-lg font-bold text-slate-800">Registrar Nueva Cama</h3>
                    <button type="button" @click="$dispatch('close-modal', 'modal-crear-cama')"
                        class="text-slate-400 hover:text-slate-600">✕</button>
                </div>
                <div class="py-4">
                    @include('camas._form', ['cama' => null])
                </div>
                <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" @click="$dispatch('close-modal', 'modal-crear-cama')"
                        class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl text-sm font-medium">Cancelar</button>
                    <button type="submit"
                        class="px-5 py-2 bg-indigo-950 text-white rounded-xl text-sm font-medium">Guardar Cama</button>
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
                        <h3 class="text-lg font-bold text-slate-800">Editar Cama: <span class="text-indigo-950"
                                x-text="modalEditar.codigo"></span></h3>
                        <button type="button" @click="$dispatch('close-modal', 'modal-editar-cama')"
                            class="text-slate-400 hover:text-slate-600">✕</button>
                    </div>
                    <div class="py-4">
                        @include('camas._form_js')
                    </div>
                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" @click="$dispatch('close-modal', 'modal-editar-cama')"
                            class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl text-sm font-medium">Cancelar</button>
                        <button type="submit"
                            class="px-5 py-2 bg-indigo-950 text-white rounded-xl text-sm font-medium">Actualizar
                            Cama</button>
                    </div>
                </form>
            </template>
        </x-modal>

        {{-- Modal Ver Cama --}}
        <x-modal name="modal-ver-cama" focusable>
            <template x-if="modalDetalle">
                <div class="p-6">
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
                                        'bg-slate-100 text-slate-600': !['disponible', 'ocupada', 'mantenimiento',
                                            'limpieza'
                                        ].includes(modalDetalle.estado)
                                    }"
                                    x-text="modalDetalle.estado_label || modalDetalle.estado">
                                </span>
                            </template>
                        </div>
                        <button type="button" @click="$dispatch('close-modal', 'modal-ver-cama')"
                            class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
                    </div>

                    <div class="py-6 space-y-6 text-sm text-slate-700">
                        <div>
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Ubicación</h4>
                            <div class="grid grid-cols-2 gap-4 bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                <div>
                                    <span class="text-xs font-semibold text-slate-400 block uppercase">ÁREA</span>
                                    <span class="font-bold text-slate-800"
                                        x-text="modalDetalle.area || 'Sin área'"></span>
                                </div>
                                <div>
                                    <span
                                        class="text-xs font-semibold text-slate-400 block uppercase">HABITACIÓN</span>
                                    <span class="font-bold text-slate-800"
                                        x-text="modalDetalle.habitacion || 'N/A'"></span>
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

                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Tipo</h4>
                                <p class="font-semibold text-slate-800 capitalize"
                                    x-text="modalDetalle.tipo_label || modalDetalle.tipo || 'N/A'"></p>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Estado del
                                    registro</h4>
                                <span
                                    class="px-3 py-1 rounded-full text-xs font-semibold inline-flex items-center gap-1.5"
                                    :class="modalDetalle.activo !== false ? 'bg-emerald-50 text-emerald-700' :
                                        'bg-slate-100 text-slate-600'"
                                    x-text="modalDetalle.activo !== false ? 'Activa' : 'Inactiva'">
                                </span>
                            </div>
                            <div class="col-span-2">
                                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Notas</h4>
                                <p class="text-slate-600 font-medium" x-text="modalDetalle.notas || 'Sin notas'"></p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-slate-100">
                        <button type="button" @click="$dispatch('close-modal', 'modal-ver-cama')"
                            class="px-5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-sm font-medium transition-colors">
                            Volver
                        </button>
                    </div>
                </div>
            </template>
        </x-modal>

    </div>
</x-app-layout>
