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
                class="bg-brand-950 hover:bg-brand-900 text-white font-semibold px-5 py-2.5 rounded-full text-sm shadow-md transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Nueva Cama</span>
            </button>
        </div>
    </x-slot>

    {{-- Inicializamos Alpine con el estado de búsqueda en vivo --}}
    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6" x-data="{
        modalDetalle: null,
        modalEditar: null,
        busquedaLive: ''
    }">


        {{-- Tarjetas de Métricas --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-2xl ring-1 ring-slate-200/70 shadow-card flex flex-col justify-center">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Camas</p>
                <p class="text-2xl font-black text-slate-800">{{ $stats['total'] }}</p>
            </div>
            <div
                class="bg-emerald-50/50 p-4 rounded-2xl border border-emerald-100 shadow-sm flex flex-col justify-center relative overflow-hidden">
                <div class="absolute -right-4 -top-4 opacity-10 text-emerald-600">
                    <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                    </svg>
                </div>
                <p class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Disponibles</p>
                <p class="text-2xl font-black text-emerald-700">{{ $stats['disponibles'] }}</p>
            </div>
            <div
                class="bg-amber-50/50 p-4 rounded-2xl border border-amber-100 shadow-sm flex flex-col justify-center relative overflow-hidden">
                <p class="text-xs font-bold text-amber-600 uppercase tracking-wider">Ocupadas</p>
                <p class="text-2xl font-black text-amber-700">{{ $stats['ocupadas'] }}</p>
            </div>
            <div
                class="bg-rose-50/50 p-4 rounded-2xl border border-rose-100 shadow-sm flex flex-col justify-center relative overflow-hidden">
                <p class="text-xs font-bold text-rose-600 uppercase tracking-wider">En Mantenimiento</p>
                <p class="text-2xl font-black text-rose-700">{{ $stats['mantenimiento'] }}</p>
            </div>
        </div>

        {{-- Alerta de camas no disponibles --}}
        @if (($stats['mantenimiento'] ?? 0) > 0)
            <div
                class="p-3 bg-rose-50 border border-rose-200 rounded-xl flex items-center gap-3 text-xs text-rose-800 shadow-sm">
                <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                Hay <strong>{{ $stats['mantenimiento'] }}</strong> cama(s) fuera de servicio o en mantenimiento.
            </div>
        @endif

        {{-- Filtros Rápidos --}}
        <div class="flex flex-wrap gap-2 pb-2">
            @php
                $estados = [
                    '' => [
                        'label' => 'Todas',
                        'count' => $stats['total'],
                        'color' => 'bg-slate-800 text-white border-slate-800',
                        'inactive' => 'bg-white text-slate-600 hover:bg-slate-50 border-slate-200',
                    ],
                    'disponible' => [
                        'label' => 'Disponibles',
                        'count' => $stats['disponibles'],
                        'color' => 'bg-emerald-500 text-white border-emerald-500',
                        'inactive' => 'bg-white text-emerald-700 border-emerald-200 hover:bg-emerald-50',
                    ],
                    'ocupada' => [
                        'label' => 'Ocupadas',
                        'count' => $stats['ocupadas'],
                        'color' => 'bg-amber-500 text-white border-amber-500',
                        'inactive' => 'bg-white text-amber-700 border-amber-200 hover:bg-amber-50',
                    ],
                    'mantenimiento' => [
                        'label' => 'Mantenimiento',
                        'count' => $stats['mantenimiento'],
                        'color' => 'bg-rose-500 text-white border-rose-500',
                        'inactive' => 'bg-white text-rose-700 border-rose-200 hover:bg-rose-50',
                    ],
                    'limpieza' => [
                        'label' => 'Limpieza',
                        'count' => $camas->where('estado', 'limpieza')->count(),
                        'color' => 'bg-sky-500 text-white border-sky-500',
                        'inactive' => 'bg-white text-sky-700 border-sky-200 hover:bg-sky-50',
                    ],
                ];
            @endphp

            @foreach ($estados as $val => $config)
                <a href="{{ route('camas.index', array_merge(request()->query(), ['estado' => $val])) }}"
                    class="px-4 py-2 rounded-xl text-xs font-bold border transition-all flex items-center gap-2
                          {{ $filtroEstado === $val || ($val === '' && !$filtroEstado) ? $config['color'] . ' shadow-sm' : $config['inactive'] }}">
                    {{ $config['label'] }}
                    <span
                        class="px-1.5 py-0.5 rounded-md text-xs {{ $filtroEstado === $val || ($val === '' && !$filtroEstado) ? 'bg-white/20' : 'bg-slate-100' }}">
                        {{ $config['count'] }}
                    </span>
                </a>
            @endforeach
        </div>

        {{-- 🔽 BÚSQUEDA EN TIEMPO REAL (Frontend) --}}
        <div class="bg-white p-3 rounded-2xl ring-1 ring-slate-200/70 shadow-card flex flex-wrap gap-3 items-center">
            <div class="flex-1 min-w-[240px] relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" x-model="busquedaLive"
                    placeholder="Buscar en tiempo real por código, área, piso o paciente..."
                    class="w-full pl-10 pr-4 py-2 bg-slate-50 border-0 rounded-xl text-sm focus:ring-2 focus:ring-brand-950 transition-all">
            </div>

            <button x-show="busquedaLive !== ''" @click="busquedaLive = ''" x-cloak
                class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-medium rounded-xl text-sm transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Limpiar
            </button>

            {{-- Indicador visual de coincidencias --}}
            <div x-show="busquedaLive !== ''" x-cloak
                class="text-xs font-semibold text-brand-600 bg-brand-50 px-3 py-1.5 rounded-lg border border-brand-100">
                Filtrando en vivo...
            </div>
        </div>

        {{-- GRID VISUAL DE CAMAS (BED BOARD) --}}
        <div class="bg-white shadow-sm rounded-2xl border border-slate-100 p-6">

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
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-4 h-4 text-brand-950" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <h3 class="font-bold text-slate-700">{{ $area }}</h3>
                        <span class="text-xs font-medium text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full">
                            {{ $camasArea->count() }} camas
                        </span>
                    </div>

                    <div
                        class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 max-h-[600px] overflow-y-auto pr-2 pb-2">
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

                                $paciente = null;
                                $triage = null;
                                $signoId = null;

                                if ($cama->estado === 'ocupada') {
                                    $asignacion = $cama->pacienteActual;
                                    $paciente = $asignacion?->paciente;
                                    $triage = $cama->triage_actual;
                                    $signoId = $cama->signo_vital_actual_id;
                                }

                                $triageColor = match ($triage) {
                                    'rojo' => 'bg-rose-100 text-rose-700 border-rose-200',
                                    'naranja' => 'bg-orange-100 text-orange-700 border-orange-200',
                                    'amarillo' => 'bg-amber-100 text-amber-700 border-amber-200',
                                    'verde' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                    'azul' => 'bg-sky-100 text-sky-700 border-sky-200',
                                    default => null,
                                };

                                // 🔽 Armamos un "string" con todos los datos buscables de la cama
                                $textoBuscable = strtolower(
                                    trim(
                                        $cama->codigo .
                                            ' ' .
                                            ($cama->area ?? '') .
                                            ' ' .
                                            ($cama->habitacion ?? '') .
                                            ' ' .
                                            ($cama->piso ?? '') .
                                            ' ' .
                                            ($cama->nombre ?? '') .
                                            ' ' .
                                            ($paciente ? $paciente->nombre_completo . ' ' . $paciente->id : ''),
                                    ),
                                );
                            @endphp

                            {{-- 🔽 Lógica de filtro en tiempo real (Alpine) --}}
                            <div class="relative group"
                                x-show="busquedaLive === '' || '{{ $textoBuscable }}'.includes(busquedaLive.toLowerCase())">

                                <button type="button"
                                    @click="
                                        modalDetalle = {{ $cama->toJson() }};
                                        @if ($paciente) modalDetalle.paciente = {{ $paciente->toJson() }};
                                            modalDetalle.triage = '{{ $triage ?? 'sin_triage' }}';
                                            modalDetalle.fecha_ingreso = '{{ $cama->pacienteActual?->fecha_ingreso?->toIso8601String() }}';
                                            modalDetalle.signo_vital_id = {{ $signoId ?? 'null' }}; @endif
                                        $dispatch('open-modal', 'modal-ver-cama')
                                    "
                                    class="w-full border-2 {{ $colorClasses[0] }} rounded-2xl p-3 transition-all duration-200 hover:shadow-lg text-left">

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

                                    <div class="text-center">
                                        <p class="font-bold text-sm {{ $colorClasses[1] }} truncate">
                                            {{ $cama->codigo }}</p>

                                        @if ($paciente)
                                            <p
                                                class="text-xs font-bold text-slate-800 truncate mt-1 leading-tight">
                                                {{ $paciente->nombre_completo }}
                                            </p>
                                            <p class="text-[9px] text-slate-500 truncate">ID: {{ $paciente->id }}</p>

                                            @if ($triage)
                                                <span
                                                    class="inline-block mt-1 px-1.5 py-0.5 rounded-full text-[9px] font-bold uppercase border {{ $triageColor }}">
                                                    {{ $triage }}
                                                </span>
                                            @endif
                                        @else
                                            <p class="text-xs font-medium text-slate-500 truncate mt-0.5">
                                                {{ $cama->habitacion ? 'Hab. ' . $cama->habitacion : 'Sin hab.' }}
                                                @if ($cama->piso)
                                                    · {{ $cama->piso }}
                                                @endif
                                            </p>
                                            <p
                                                class="text-xs font-semibold uppercase tracking-wider mt-1 {{ $colorClasses[1] }} opacity-80">
                                                {{ $cama->estado_label }}
                                            </p>
                                        @endif
                                    </div>
                                </button>

                                {{-- Acciones rápidas al hover --}}
                                <div
                                    class="absolute top-1.5 right-1.5 flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button type="button"
                                        @click.stop="modalEditar = {{ $cama->toJson() }}; $dispatch('open-modal', 'modal-editar-cama')"
                                        class="w-7 h-7 flex items-center justify-center bg-white/95 backdrop-blur rounded-lg shadow-sm text-accent-600 hover:bg-accent-50 transition-colors"
                                        title="Editar cama">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>

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

                                    <div x-show="openEstado" x-transition @click.outside="openEstado = false"
                                        class="absolute bottom-9 right-0 w-44 bg-white rounded-xl shadow-xl border border-slate-100 py-1 z-20">
                                        @foreach (['disponible' => 'Disponible', 'ocupada' => 'Ocupada', 'limpieza' => 'Limpieza', 'mantenimiento' => 'Mantenimiento', 'fuera_servicio' => 'Fuera de Servicio'] as $val => $lbl)
                                            <form action="{{ route('camas.cambiarEstado', $cama) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="estado" value="{{ $val }}">
                                                <button type="submit"
                                                    class="w-full text-left px-3 py-1.5 text-xs font-medium hover:bg-slate-50 flex items-center gap-2
                                                        {{ $cama->estado === $val ? 'text-brand-950 font-bold' : 'text-slate-600' }}">
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

        {{-- MODALES --}}
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
                        class="px-5 py-2 bg-brand-950 text-white rounded-xl text-sm font-medium">Guardar Cama</button>
                </div>
            </form>
        </x-modal>

        <x-modal name="modal-editar-cama" focusable>
            <template x-if="modalEditar">
                <form :action="`/camas/${modalEditar.id}`" method="POST" class="p-6">
                    @csrf
                    @method('PUT')
                    <div class="flex justify-between items-center pb-4 border-b border-slate-100">
                        <h3 class="text-lg font-bold text-slate-800">Editar Cama: <span class="text-brand-950"
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
                            class="px-5 py-2 bg-brand-950 text-white rounded-xl text-sm font-medium">Actualizar
                            Cama</button>
                    </div>
                </form>
            </template>
        </x-modal>

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
                        <button type="button"
                            @click="modalDetalle = null; $dispatch('close-modal', 'modal-ver-cama')"
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

                        <template x-if="modalDetalle.paciente">
                            <div>
                                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Paciente
                                    asignado</h4>
                                <div class="bg-brand-50/50 border border-brand-100 p-4 rounded-2xl space-y-2">
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="text-sm font-bold text-slate-800 truncate"
                                                x-text="modalDetalle.paciente.nombre_completo"></p>
                                            <p class="text-xs text-slate-500">ID: <span
                                                    x-text="modalDetalle.paciente.id"></span></p>
                                        </div>
                                        <template x-if="modalDetalle.triage">
                                            <span
                                                class="px-2.5 py-1 rounded-full text-xs font-bold uppercase border shrink-0"
                                                :class="{
                                                    'bg-rose-100 text-rose-700 border-rose-200': modalDetalle
                                                        .triage === 'rojo',
                                                    'bg-orange-100 text-orange-700 border-orange-200': modalDetalle
                                                        .triage === 'naranja',
                                                    'bg-amber-100 text-amber-700 border-amber-200': modalDetalle
                                                        .triage === 'amarillo',
                                                    'bg-emerald-100 text-emerald-700 border-emerald-200': modalDetalle
                                                        .triage === 'verde',
                                                    'bg-sky-100 text-sky-700 border-sky-200': modalDetalle
                                                        .triage === 'azul',
                                                }"
                                                x-text="modalDetalle.triage">
                                            </span>
                                        </template>
                                    </div>

                                    <template x-if="modalDetalle.fecha_ingreso">
                                        <p class="text-xs text-slate-500">
                                            Ingreso:
                                            <span class="font-semibold text-slate-700"
                                                x-text="new Date(modalDetalle.fecha_ingreso).toLocaleString('es-MX', { dateStyle: 'short', timeStyle: 'short' })"></span>
                                        </p>
                                    </template>

                                    <div class="flex flex-wrap gap-3 pt-2">
                                        <template x-if="modalDetalle.paciente.id">
                                            <a :href="`/pacientes/${modalDetalle.paciente.id}`"
                                                class="text-xs font-bold text-brand-700 hover:text-brand-900 hover:underline">Ver
                                                expediente →</a>
                                        </template>
                                        <template x-if="modalDetalle.signo_vital_id">
                                            <a :href="`/signos-vitales/${modalDetalle.signo_vital_id}`"
                                                class="text-xs font-bold text-brand-700 hover:text-brand-900 hover:underline">Ver
                                                signos vitales →</a>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <template x-if="!modalDetalle.paciente">
                            <div>
                                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Paciente
                                    asignado</h4>
                                <div class="bg-slate-50 border border-slate-100 p-4 rounded-2xl text-center">
                                    <p class="text-xs text-slate-500">Cama disponible — sin paciente asignado</p>
                                </div>
                            </div>
                        </template>

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
                        <button type="button"
                            @click="modalDetalle = null; $dispatch('close-modal', 'modal-ver-cama')"
                            class="px-5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-sm font-medium transition-colors">
                            Volver
                        </button>
                    </div>
                </div>
            </template>
        </x-modal>

    </div>
</x-app-layout>
