<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-3">
            <div class="min-w-0">
                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight truncate">
                    Especialidades Médicas
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Catálogo de especialidades, médicos y servicios asociados</p>
            </div>
            <button type="button" onclick="openModal('modal-create')"
                class="bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white px-5 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 shadow-sm transition-all shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                Nueva Especialidad
            </button>
        </div>
    </x-slot>

    @php
        $inputCls =
            'w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-indigo-500 focus:ring-0 outline-none';
        $labelCls = 'block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5';
        $thCls = 'px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider';

        // ==== Datos pre-serializados para el JS ====
        $jsonFlags = JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP;

        $medicosMapJson = json_encode(
            $especialidades->mapWithKeys(fn($e) => [$e->id => $e->medicos])->toArray(),
            $jsonFlags,
        );

        $serviciosMapJson = json_encode(
            $especialidades->mapWithKeys(fn($e) => [$e->id => $e->servicios])->toArray(),
            $jsonFlags,
        );

        $serviciosDisponiblesJson = json_encode(
            $serviciosDisponibles
                ->map(
                    fn($s) => [
                        'id' => $s->id,
                        'nombre' => $s->nombre,
                        'tipo_label' => $s->tipo_label ?? '',
                        'tipo_color' => $s->tipo_color ?? '',
                        'ubicacion' => $s->ubicacion ?? '',
                    ],
                )
                ->values()
                ->toArray(),
            $jsonFlags,
        );

        $medicosDisponiblesJson = json_encode(
            $medicosDisponibles
                ->map(
                    fn($m) => [
                        'id' => $m->id,
                        'nombre' => $m->nombre_completo ?: $m->name,
                        'rol' => $m->getRoleNames()->first() ?? 'sin rol',
                        'email' => $m->email,
                    ],
                )
                ->values()
                ->toArray(),
            $jsonFlags,
        );
    @endphp

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- ============ ALERTAS ============ --}}
        @if (session('success'))
            <div
                class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-2xl text-xs font-semibold flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div
                class="p-4 bg-rose-50 border border-rose-100 text-rose-800 rounded-2xl text-xs font-semibold flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- ============ STATS ============ --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total</p>
                <p class="text-3xl font-black text-slate-800 tracking-tight mt-1">{{ $stats['total'] }}</p>
            </div>
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm">
                <p class="text-[11px] font-bold text-emerald-600/70 uppercase tracking-wider">Activas</p>
                <p class="text-3xl font-black text-emerald-600 tracking-tight mt-1">{{ $stats['activas'] }}</p>
            </div>
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm">
                <p class="text-[11px] font-bold text-indigo-600/70 uppercase tracking-wider">Grupos</p>
                <p class="text-3xl font-black text-indigo-600 tracking-tight mt-1">{{ $stats['grupos'] }}</p>
            </div>
        </div>

        {{-- ============ FILTROS ============ --}}
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-5">
            <form method="GET" class="flex gap-2 flex-wrap items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="{{ $labelCls }}">Buscar</label>
                    <input type="text" name="buscar" value="{{ $busqueda }}" placeholder="Nombre o código"
                        class="{{ $inputCls }}">
                </div>
                <div class="min-w-[180px]">
                    <label class="{{ $labelCls }}">Grupo</label>
                    <select name="grupo" class="{{ $inputCls }}">
                        <option value="">Todos los grupos</option>
                        <option value="clinica" @selected($grupo === 'clinica')>Clínica</option>
                        <option value="quirurgica" @selected($grupo === 'quirurgica')>Quirúrgica</option>
                        <option value="diagnostica" @selected($grupo === 'diagnostica')>Diagnóstica</option>
                        <option value="basica" @selected($grupo === 'basica')>Básica</option>
                        <option value="otra" @selected($grupo === 'otra')>Otra</option>
                    </select>
                </div>
                <button type="submit"
                    class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all">
                    Filtrar
                </button>
                @if ($busqueda || $grupo)
                    <a href="{{ route('especialidades.index') }}"
                        class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition-all">
                        Limpiar
                    </a>
                @endif
            </form>
        </div>

        {{-- ============ TABLA ============ --}}
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h3 class="font-extrabold text-slate-800 text-base">Catálogo de especialidades</h3>
                <p class="text-[11px] text-slate-400">{{ $especialidades->total() }} especialidades registradas</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-slate-50/70 border-b border-slate-100">
                        <tr>
                            <th class="{{ $thCls }} text-left">Especialidad</th>
                            <th class="{{ $thCls }} text-left">Grupo</th>
                            <th class="{{ $thCls }} text-left">Duración</th>
                            <th class="{{ $thCls }} text-center">Médicos</th>
                            <th class="{{ $thCls }} text-center">Servicios</th>
                            <th class="{{ $thCls }} text-center">Consultas</th>
                            <th class="{{ $thCls }} text-left">Estado</th>
                            <th class="{{ $thCls }} text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($especialidades as $esp)
                            @php
                                $espJson = json_encode(
                                    $esp->only([
                                        'id',
                                        'codigo',
                                        'nombre',
                                        'descripcion',
                                        'grupo',
                                        'duracion_consulta_default',
                                        'color',
                                        'activo',
                                    ]),
                                    JSON_HEX_APOS | JSON_HEX_QUOT,
                                );
                            @endphp
                            <tr class="group hover:bg-slate-50 transition-colors" data-especialidad="{{ $espJson }}">

                                {{-- ESPECIALIDAD --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="w-12 h-12 rounded-2xl bg-slate-100 group-hover:bg-indigo-950 flex items-center justify-center text-xs font-extrabold text-slate-500 group-hover:text-indigo-400 transition-colors duration-200 shrink-0">
                                            {{ mb_strtoupper(mb_substr($esp->nombre, 0, 2)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2">
                                                @if ($esp->color)
                                                    <span class="w-2.5 h-2.5 rounded-full shrink-0"
                                                        style="background-color: {{ $esp->color_hex }}"></span>
                                                @endif
                                                <p
                                                    class="text-sm font-extrabold text-slate-900 group-hover:text-indigo-600 transition-colors truncate">
                                                    {{ $esp->nombre }}
                                                </p>
                                            </div>
                                            <p class="text-[11px] text-slate-400 mt-0.5 font-mono">{{ $esp->codigo }}</p>
                                        </div>
                                    </div>
                                </td>

                                {{-- GRUPO --}}
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex px-3 py-1 rounded-full text-xs font-bold {{ $esp->grupo_color }}">
                                        {{ $esp->grupo_label }}
                                    </span>
                                </td>

                                {{-- DURACIÓN --}}
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-100 text-slate-700 rounded-full text-xs font-bold">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor"
                                            stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $esp->duracion_consulta_default }} min
                                    </span>
                                </td>

                                {{-- MÉDICOS --}}
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="inline-flex px-3 py-1 bg-indigo-50 text-indigo-700 rounded-full text-xs font-bold">
                                        {{ $esp->medicos_count }}
                                    </span>
                                </td>

                                {{-- SERVICIOS --}}
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="inline-flex px-3 py-1 bg-purple-50 text-purple-700 rounded-full text-xs font-bold">
                                        {{ $esp->servicios_count }}
                                    </span>
                                </td>

                                {{-- CONSULTAS --}}
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="inline-flex px-3 py-1 bg-emerald-50 text-emerald-700 rounded-full text-xs font-bold">
                                        {{ $esp->consultas_count }}
                                    </span>
                                </td>

                                {{-- ESTADO --}}
                                <td class="px-6 py-4">
                                    @if ($esp->activo)
                                        <span
                                            class="inline-flex px-3 py-1 bg-emerald-50 text-emerald-700 rounded-full text-xs font-bold">Activa</span>
                                    @else
                                        <span
                                            class="inline-flex px-3 py-1 bg-rose-50 text-rose-600 rounded-full text-xs font-bold">Inactiva</span>
                                    @endif
                                </td>

                                {{-- ACCIONES --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-1 whitespace-nowrap">

                                        {{-- Ver --}}
                                        <button type="button" onclick="openShowModal(this.closest('tr'))"
                                            title="Ver detalles"
                                            class="p-2 rounded-xl text-slate-800 hover:bg-slate-200/70 active:scale-90 transition-all">
                                            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor"
                                                stroke-width="1.8" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </button>

                                        {{-- Editar --}}
                                        <button type="button" onclick="openEditModal(this.closest('tr'))"
                                            title="Editar"
                                            class="p-2 rounded-xl text-purple-500 hover:bg-purple-50 active:scale-90 transition-all">
                                            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor"
                                                stroke-width="1.8" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                            </svg>
                                        </button>

                                        {{-- Médicos --}}
                                        <button type="button" onclick="openMedicosModal(this.closest('tr'))"
                                            title="Gestionar médicos"
                                            class="p-2 rounded-xl text-indigo-500 hover:bg-indigo-50 active:scale-90 transition-all">
                                            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor"
                                                stroke-width="1.8" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                            </svg>
                                        </button>

                                        {{-- Servicios --}}
                                        <button type="button" onclick="openServiciosModal(this.closest('tr'))"
                                            title="Gestionar servicios"
                                            class="p-2 rounded-xl text-amber-500 hover:bg-amber-50 active:scale-90 transition-all">
                                            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor"
                                                stroke-width="1.8" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z" />
                                            </svg>
                                        </button>

                                        {{-- Eliminar --}}
                                        <button type="button" onclick="openDeleteModal(this.closest('tr'))"
                                            title="Eliminar"
                                            class="p-2 rounded-xl text-rose-500 hover:bg-rose-50 active:scale-90 transition-all">
                                            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor"
                                                stroke-width="1.8" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <p class="text-xs font-semibold text-slate-400">Sin especialidades registradas.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($especialidades->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $especialidades->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- ==================== MODAL: CREAR ========================== --}}
    {{-- ============================================================ --}}
    <div id="modal-create" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-modal="true" role="dialog">
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" onclick="closeModal('modal-create')"></div>

            <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div
                    class="px-6 py-5 border-b border-slate-100 flex justify-between items-center sticky top-0 bg-white rounded-t-3xl z-10">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-slate-800 text-lg">Nueva Especialidad</h3>
                            <p class="text-[11px] text-slate-400">Registra una nueva especialidad médica</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeModal('modal-create')"
                        class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 text-slate-400 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('especialidades.store') }}" method="POST" class="p-6 space-y-5">
                    @csrf
                    @include('especialidades._form', ['especialidad' => null, 'prefix' => 'create'])
                    <div class="flex justify-end gap-2 pt-4 border-t border-slate-100">
                        <button type="button" onclick="closeModal('modal-create')"
                            class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition-all">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all">
                            Guardar Especialidad
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- ==================== MODAL: EDITAR ========================= --}}
    {{-- ============================================================ --}}
    <div id="modal-edit" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-modal="true" role="dialog">
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" onclick="closeModal('modal-edit')"></div>

            <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div
                    class="px-6 py-5 border-b border-slate-100 flex justify-between items-center sticky top-0 bg-white rounded-t-3xl z-10">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-2xl bg-purple-50 flex items-center justify-center text-purple-600 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-slate-800 text-lg">Editar Especialidad</h3>
                            <p class="text-[11px] text-slate-400" id="edit-subtitle">Modifica los datos</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeModal('modal-edit')"
                        class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 text-slate-400 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form id="form-edit" method="POST" class="p-6 space-y-5">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="especialidad_id" id="edit_especialidad_id"
                        value="{{ old('especialidad_id') }}">
                    @include('especialidades._form', ['especialidad' => null, 'prefix' => 'edit'])
                    <div class="flex justify-end gap-2 pt-4 border-t border-slate-100">
                        <button type="button" onclick="closeModal('modal-edit')"
                            class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition-all">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all">
                            Actualizar Especialidad
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- ==================== MODAL: ELIMINAR ======================= --}}
    {{-- ============================================================ --}}
    <div id="modal-delete" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-modal="true" role="dialog">
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" onclick="closeModal('modal-delete')"></div>

            <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-md p-6 space-y-5 text-center">
                <div class="w-14 h-14 rounded-2xl bg-rose-50 flex items-center justify-center text-rose-500 mx-auto">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>

                <div>
                    <h3 class="text-base font-extrabold text-slate-800">¿Eliminar especialidad?</h3>
                    <p class="text-xs text-slate-500 mt-1">
                        Se eliminará <span id="delete-nombre" class="font-bold text-slate-700"></span>.
                        Esta acción no se puede deshacer.
                    </p>
                </div>

                <form id="form-delete" method="POST"
                    class="flex justify-center gap-2 pt-4 border-t border-slate-100">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="closeModal('modal-delete')"
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
    </div>

    {{-- ============================================================ --}}
    {{-- ==================== MODAL: VER (SHOW) ===================== --}}
    {{-- ============================================================ --}}
    <div id="modal-show" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-modal="true" role="dialog">
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" onclick="closeModal('modal-show')"></div>

            <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
                <div
                    class="px-6 py-5 border-b border-slate-100 flex justify-between items-center sticky top-0 bg-white rounded-t-3xl z-10">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-700 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-slate-800 text-lg" id="show-title">Detalle de la
                                Especialidad</h3>
                            <p class="text-[11px] text-slate-400" id="show-subtitle">Información completa</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeModal('modal-show')"
                        class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 text-slate-400 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="p-6 space-y-5">
                    <div class="bg-slate-50 border border-slate-100 rounded-2xl p-5">
                        <div class="flex flex-wrap items-center gap-2">
                            <span id="show-codigo"
                                class="px-2 py-1 bg-white border border-slate-200 text-slate-600 rounded-lg text-[11px] font-mono font-bold"></span>
                            <span id="show-grupo"
                                class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide"></span>
                            <span id="show-estado"
                                class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide"></span>
                        </div>
                        <div class="flex items-center gap-2 mt-3">
                            <span id="show-color" class="w-4 h-4 rounded-full shrink-0 hidden"></span>
                            <h4 id="show-nombre" class="text-2xl font-black text-slate-800"></h4>
                        </div>
                        <p id="show-descripcion"
                            class="text-xs text-slate-600 mt-3 whitespace-pre-line leading-relaxed"></p>

                        <div class="grid grid-cols-2 gap-3 mt-4">
                            <div class="bg-white border border-slate-100 rounded-xl p-3">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Duración por
                                    defecto</p>
                                <p id="show-duracion" class="text-sm font-extrabold text-slate-800 mt-0.5"></p>
                            </div>
                            <div class="bg-white border border-slate-100 rounded-xl p-3">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total médicos
                                </p>
                                <p id="show-total-medicos" class="text-sm font-extrabold text-indigo-600 mt-0.5"></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-slate-100 rounded-2xl overflow-hidden">
                        <div class="px-5 py-3.5 border-b border-slate-100">
                            <h5 class="font-extrabold text-slate-800 text-sm">Médicos asignados</h5>
                            <p class="text-[11px] text-slate-400" id="show-medicos-count">0 médicos</p>
                        </div>
                        <ul id="show-medicos-list" class="divide-y divide-slate-100"></ul>
                    </div>

                    <div class="bg-white border border-slate-100 rounded-2xl overflow-hidden">
                        <div class="px-5 py-3.5 border-b border-slate-100">
                            <h5 class="font-extrabold text-slate-800 text-sm">Servicios asociados</h5>
                            <p class="text-[11px] text-slate-400" id="show-servicios-count">0 servicios</p>
                        </div>
                        <div id="show-servicios-list" class="p-5 flex flex-wrap gap-2"></div>
                    </div>

                    <div class="flex flex-wrap gap-2 pt-4 border-t border-slate-100">
                        <button type="button" id="show-btn-editar"
                            class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-sm transition-all">
                            Editar
                        </button>
                        <button type="button" id="show-btn-medicos"
                            class="px-4 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold shadow-sm transition-all">
                            Gestionar médicos
                        </button>
                        <button type="button" id="show-btn-servicios"
                            class="px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-bold shadow-sm transition-all">
                            Gestionar servicios
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- ==================== MODAL: MÉDICOS ======================== --}}
    {{-- ============================================================ --}}
    <div id="modal-medicos" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-modal="true" role="dialog">
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" onclick="closeModal('modal-medicos')"></div>

            <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
                <div
                    class="px-6 py-5 border-b border-slate-100 flex justify-between items-center sticky top-0 bg-white rounded-t-3xl z-10">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-slate-800 text-lg">Médicos de la Especialidad</h3>
                            <p class="text-[11px] text-slate-400" id="medicos-subtitle">Asignación y gestión</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeModal('modal-medicos')"
                        class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 text-slate-400 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="p-6 space-y-5">
                    <div class="bg-white border border-slate-100 rounded-2xl overflow-hidden">
                        <div class="px-5 py-3.5 border-b border-slate-100 flex items-center gap-3">
                            <div
                                class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-extrabold text-slate-800 text-sm">Asignar médico</h4>
                                <p class="text-[11px] text-slate-400">Agrega un médico a esta especialidad</p>
                            </div>
                        </div>

                        <form id="form-asignar-medico" method="POST"
                            class="p-5 grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                            @csrf
                            <div class="md:col-span-2">
                                <label class="{{ $labelCls }}">Médico *</label>
                                <select name="user_id" required class="{{ $inputCls }}">
                                    <option value="">— Selecciona —</option>
                                    @foreach ($medicosDisponibles as $u)
                                        <option value="{{ $u->id }}">
                                            {{ $u->nombre_completo ?: $u->name }}
                                            ({{ $u->getRoleNames()->first() ?? 'sin rol' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="{{ $labelCls }}">Cédula esp.</label>
                                <input type="text" name="numero_cedula_especialidad" class="{{ $inputCls }}">
                            </div>
                            <div class="flex items-end">
                                <label class="inline-flex items-center gap-2 cursor-pointer pb-2.5">
                                    <input type="checkbox" name="es_principal" value="1"
                                        class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 focus:ring-0 cursor-pointer">
                                    <span class="text-xs font-bold text-slate-700">Principal</span>
                                </label>
                            </div>

                            <div class="md:col-span-4 flex justify-end pt-3 border-t border-slate-100">
                                <button type="submit"
                                    class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all">
                                    Asignar médico
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="bg-white border border-slate-100 rounded-2xl overflow-hidden">
                        <div class="px-5 py-3.5 border-b border-slate-100">
                            <h4 class="font-extrabold text-slate-800 text-sm">Médicos asignados</h4>
                            <p class="text-[11px] text-slate-400" id="medicos-count">0 médicos</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead class="bg-slate-50/70 border-b border-slate-100">
                                    <tr>
                                        <th class="{{ $thCls }} text-left">Médico</th>
                                        <th class="{{ $thCls }} text-left">Rol</th>
                                        <th class="{{ $thCls }} text-left">Cédula</th>
                                        <th class="{{ $thCls }} text-center">Principal</th>
                                        <th class="px-5 py-3.5"></th>
                                    </tr>
                                </thead>
                                <tbody id="medicos-tbody" class="divide-y divide-slate-100"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- ==================== MODAL: SERVICIOS ====================== --}}
    {{-- ============================================================ --}}
    <div id="modal-servicios" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-modal="true" role="dialog">
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" onclick="closeModal('modal-servicios')"></div>

            <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
                <div
                    class="px-6 py-5 border-b border-slate-100 flex justify-between items-center sticky top-0 bg-white rounded-t-3xl z-10">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-600 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-slate-800 text-lg">Servicios de la Especialidad</h3>
                            <p class="text-[11px] text-slate-400" id="servicios-subtitle">Asociación y gestión</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeModal('modal-servicios')"
                        class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 text-slate-400 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="p-6 space-y-5">
                    <div class="bg-white border border-slate-100 rounded-2xl overflow-hidden">
                        <div class="px-5 py-3.5 border-b border-slate-100 flex items-center gap-3">
                            <div
                                class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600 shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-extrabold text-slate-800 text-sm">Asociar servicio</h4>
                                <p class="text-[11px] text-slate-400">Agrega un servicio a esta especialidad</p>
                            </div>
                        </div>

                        <form id="form-asignar-servicio" method="POST"
                            class="p-5 flex flex-col sm:flex-row gap-3 items-end">
                            @csrf
                            <div class="flex-1 w-full">
                                <label class="{{ $labelCls }}">Servicio *</label>
                                <select name="servicio_id" required class="{{ $inputCls }}">
                                    <option value="">— Selecciona —</option>
                                    @foreach ($serviciosDisponibles as $s)
                                        <option value="{{ $s->id }}">
                                            {{ $s->nombre }} ({{ $s->tipo_label ?? '' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit"
                                class="w-full sm:w-auto px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all">
                                Asociar servicio
                            </button>
                        </form>
                    </div>

                    <div class="bg-white border border-slate-100 rounded-2xl overflow-hidden">
                        <div class="px-5 py-3.5 border-b border-slate-100">
                            <h4 class="font-extrabold text-slate-800 text-sm">Servicios asociados</h4>
                            <p class="text-[11px] text-slate-400" id="servicios-count">0 servicios</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead class="bg-slate-50/70 border-b border-slate-100">
                                    <tr>
                                        <th class="{{ $thCls }} text-left">Servicio</th>
                                        <th class="{{ $thCls }} text-left">Tipo</th>
                                        <th class="{{ $thCls }} text-left">Ubicación</th>
                                        <th class="px-5 py-3.5"></th>
                                    </tr>
                                </thead>
                                <tbody id="servicios-tbody" class="divide-y divide-slate-100"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- ==================== SCRIPT ================================ --}}
    {{-- ============================================================ --}}
    <script>
        // ============ Datos globales para los modales ============
        window.MEDICOS_MAP = {!! $medicosMapJson !!};
        window.SERVICIOS_MAP = {!! $serviciosMapJson !!};
        window.SERVICIOS_DISPONIBLES = {!! $serviciosDisponiblesJson !!};
        window.MEDICOS_DISPONIBLES = {!! $medicosDisponiblesJson !!};

        const MODALES = ['modal-create', 'modal-edit', 'modal-show', 'modal-medicos', 'modal-servicios', 'modal-delete'];

        // ============ Abrir / Cerrar ============
        function openModal(id) {
            const modal = document.getElementById(id);
            if (!modal) return;
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            if (!modal) return;
            modal.classList.add('hidden');
            if (document.querySelectorAll('.fixed.inset-0.z-50:not(.hidden)').length === 0) {
                document.body.style.overflow = '';
            }
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') MODALES.forEach(closeModal);
        });

        // ============ Helper: parsear data-especialidad (viene en el <tr>) ============
        function parseEsp(el) {
            try {
                return JSON.parse(el.dataset.especialidad);
            } catch (e) {
                console.error('Error parseando data-especialidad:', e);
                return null;
            }
        }

        // ============ Modal EDITAR ============
        function openEditModal(el) {
            const esp = parseEsp(el);
            if (!esp) return;

            const form = document.getElementById('form-edit');
            form.action = `{{ url('especialidades') }}/${esp.id}`;

            document.getElementById('edit_especialidad_id').value = esp.id;
            document.getElementById('edit-subtitle').textContent = `Editando: ${esp.nombre}`;
            document.getElementById('edit_codigo').value = esp.codigo || '';
            document.getElementById('edit_nombre').value = esp.nombre || '';
            document.getElementById('edit_descripcion').value = esp.descripcion || '';
            document.getElementById('edit_grupo').value = esp.grupo || 'clinica';
            document.getElementById('edit_duracion_consulta_default').value = esp.duracion_consulta_default || 30;
            document.getElementById('edit_color').value = esp.color ? '#' + String(esp.color).replace('#', '') :
                '#3B82F6';
            document.getElementById('edit_activo').checked = esp.activo == 1 || esp.activo === true;

            openModal('modal-edit');
        }

        // ============ Modal ELIMINAR ============
        function openDeleteModal(el) {
            const esp = parseEsp(el);
            if (!esp) return;

            document.getElementById('form-delete').action = `{{ url('especialidades') }}/${esp.id}`;
            document.getElementById('delete-nombre').textContent = esp.nombre;

            openModal('modal-delete');
        }

        // ============ Modal SHOW ============
        function openShowModal(el) {
            const esp = parseEsp(el);
            if (!esp) return;

            const medicos = window.MEDICOS_MAP[esp.id] || [];
            const servicios = window.SERVICIOS_MAP[esp.id] || [];

            document.getElementById('show-title').textContent = `Especialidad: ${esp.nombre}`;
            document.getElementById('show-subtitle').textContent = 'Información completa';
            document.getElementById('show-codigo').textContent = esp.codigo || '';
            document.getElementById('show-nombre').textContent = esp.nombre || '';
            document.getElementById('show-descripcion').textContent = esp.descripcion || 'Sin descripción.';
            document.getElementById('show-duracion').textContent = (esp.duracion_consulta_default || 0) + ' min';
            document.getElementById('show-total-medicos').textContent = medicos.length;

            const colorSpan = document.getElementById('show-color');
            if (esp.color) {
                colorSpan.classList.remove('hidden');
                colorSpan.style.backgroundColor = '#' + String(esp.color).replace('#', '');
            } else {
                colorSpan.classList.add('hidden');
            }

            const grupoLabels = {
                clinica: 'Clínica',
                quirurgica: 'Quirúrgica',
                diagnostica: 'Diagnóstica',
                basica: 'Básica',
                otra: 'Otra'
            };
            const grupoEl = document.getElementById('show-grupo');
            grupoEl.textContent = grupoLabels[esp.grupo] || esp.grupo;
            grupoEl.className = 'px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide bg-slate-100 text-slate-700';

            const estado = document.getElementById('show-estado');
            estado.textContent = esp.activo == 1 ? 'Activa' : 'Inactiva';
            estado.className = 'px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide ' +
                (esp.activo == 1 ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' :
                    'bg-rose-50 text-rose-700 border border-rose-100');

            // Médicos
            const medicosList = document.getElementById('show-medicos-list');
            document.getElementById('show-medicos-count').textContent = medicos.length + ' médicos';
            medicosList.innerHTML = '';

            if (medicos.length === 0) {
                medicosList.innerHTML =
                    '<li class="px-5 py-8 text-center"><p class="text-xs font-semibold text-slate-400">Sin médicos asignados.</p></li>';
            } else {
                medicos.forEach(m => {
                    const nombre = m.nombre_completo || m.name || 'Sin nombre';
                    const rol = (m.roles && m.roles.length) ? m.roles[0].name : (m.rol || 'sin rol');
                    const pivot = m.pivot || {};
                    const principal = pivot.es_principal ?
                        '<span class="px-2 py-0.5 bg-amber-50 text-amber-700 border border-amber-100 rounded-full text-[10px] font-bold uppercase">Principal</span>' :
                        '';
                    const cedula = pivot.numero_cedula_especialidad ?
                        `<p class="text-[11px] text-slate-400 mt-0.5">Cédula: ${pivot.numero_cedula_especialidad}</p>` :
                        '';
                    medicosList.innerHTML += `
                        <li class="px-5 py-3.5 flex flex-wrap justify-between items-center gap-3">
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <p class="text-xs font-bold text-slate-800">${nombre}</p>
                                    ${principal}
                                </div>
                                ${cedula}
                            </div>
                            <span class="px-2.5 py-1 bg-slate-100 text-slate-600 rounded-full text-[10px] font-bold uppercase">${rol}</span>
                        </li>`;
                });
            }

            // Servicios
            const serviciosList = document.getElementById('show-servicios-list');
            document.getElementById('show-servicios-count').textContent = servicios.length + ' servicios';
            serviciosList.innerHTML = '';

            if (servicios.length === 0) {
                serviciosList.innerHTML =
                    '<p class="text-xs font-semibold text-slate-400">Sin servicios asociados.</p>';
            } else {
                servicios.forEach(s => {
                    serviciosList.innerHTML += `
                        <span class="px-3 py-1.5 bg-slate-100 text-slate-700 border border-slate-200 rounded-xl text-[11px] font-bold">
                            ${s.nombre}
                        </span>`;
                });
            }

            document.getElementById('show-btn-editar').onclick = () => {
                closeModal('modal-show');
                openEditModal(el);
            };
            document.getElementById('show-btn-medicos').onclick = () => {
                closeModal('modal-show');
                openMedicosModal(el);
            };
            document.getElementById('show-btn-servicios').onclick = () => {
                closeModal('modal-show');
                openServiciosModal(el);
            };

            openModal('modal-show');
        }

        // ============ Modal MÉDICOS ============
        function openMedicosModal(el) {
            const esp = parseEsp(el);
            if (!esp) return;

            document.getElementById('medicos-subtitle').textContent = `Especialidad: ${esp.nombre}`;
            document.getElementById('form-asignar-medico').action = `{{ url('especialidades') }}/${esp.id}/medicos`;

            renderMedicosTable(esp.id);
            openModal('modal-medicos');
        }

        function renderMedicosTable(espId) {
            const medicos = window.MEDICOS_MAP[espId] || [];
            const tbody = document.getElementById('medicos-tbody');
            document.getElementById('medicos-count').textContent = medicos.length + ' médicos';
            tbody.innerHTML = '';

            if (medicos.length === 0) {
                tbody.innerHTML =
                    '<tr><td colspan="5" class="px-5 py-8 text-center text-xs font-semibold text-slate-400">Sin médicos asignados.</td></tr>';
                return;
            }

            medicos.forEach(m => {
                const nombre = m.nombre_completo || m.name || 'Sin nombre';
                const email = m.email || '';
                const rol = (m.roles && m.roles.length) ? m.roles[0].name : (m.rol || 'sin rol');
                const pivot = m.pivot || {};
                const cedula = pivot.numero_cedula_especialidad ?
                    `<span class="px-2 py-1 bg-slate-100 text-slate-600 rounded-lg text-[11px] font-mono font-bold">${pivot.numero_cedula_especialidad}</span>` :
                    '<span class="text-slate-300 text-xs">—</span>';
                const principal = pivot.es_principal ?
                    '<span class="px-2.5 py-1 bg-amber-50 text-amber-700 border border-amber-100 rounded-full text-[10px] font-bold uppercase">★ Principal</span>' :
                    '<span class="text-slate-300 text-xs">—</span>';

                tbody.innerHTML += `
                    <tr class="hover:bg-indigo-50/30 transition-colors">
                        <td class="px-5 py-3.5">
                            <p class="text-xs font-bold text-slate-800">${nombre}</p>
                            <p class="text-[10px] font-medium text-slate-400 mt-0.5">${email}</p>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="px-2.5 py-1 bg-indigo-50 text-indigo-700 border border-indigo-100 rounded-full text-[10px] font-bold uppercase">${rol}</span>
                        </td>
                        <td class="px-5 py-3.5">${cedula}</td>
                        <td class="px-5 py-3.5 text-center">${principal}</td>
                        <td class="px-5 py-3.5">
                            <div class="flex justify-end">
                                <form action="{{ url('especialidades') }}/${espId}/medicos/${pivot.id}" method="POST"
                                    onsubmit="return confirm('¿Quitar este médico de la especialidad?')">
                                    @csrf @method('DELETE')
                                    <button class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-[11px] font-bold transition-all">Quitar</button>
                                </form>
                            </div>
                        </td>
                    </tr>`;
            });
        }

        // ============ Modal SERVICIOS ============
        function openServiciosModal(el) {
            const esp = parseEsp(el);
            if (!esp) return;

            document.getElementById('servicios-subtitle').textContent = `Especialidad: ${esp.nombre}`;
            document.getElementById('form-asignar-servicio').action = `{{ url('especialidades') }}/${esp.id}/servicios`;

            renderServiciosTable(esp.id);
            openModal('modal-servicios');
        }

        function renderServiciosTable(espId) {
            const servicios = window.SERVICIOS_MAP[espId] || [];
            const tbody = document.getElementById('servicios-tbody');
            document.getElementById('servicios-count').textContent = servicios.length + ' servicios';
            tbody.innerHTML = '';

            if (servicios.length === 0) {
                tbody.innerHTML =
                    '<tr><td colspan="4" class="px-5 py-8 text-center text-xs font-semibold text-slate-400">Sin servicios asociados.</td></tr>';
                return;
            }

            servicios.forEach(s => {
                const pivot = s.pivot || {};
                const tipoLabel = s.tipo_label || s.tipo || '—';
                const ubicacion = s.ubicacion || '<span class="text-slate-300 text-xs">—</span>';

                tbody.innerHTML += `
                    <tr class="hover:bg-indigo-50/30 transition-colors">
                        <td class="px-5 py-3.5"><p class="text-xs font-bold text-slate-800">${s.nombre}</p></td>
                        <td class="px-5 py-3.5">
                            <span class="px-2.5 py-1 bg-purple-50 text-purple-700 border border-purple-100 rounded-full text-[10px] font-bold uppercase">${tipoLabel}</span>
                        </td>
                        <td class="px-5 py-3.5"><p class="text-xs font-medium text-slate-600">${ubicacion}</p></td>
                        <td class="px-5 py-3.5">
                            <div class="flex justify-end">
                                <form action="{{ url('especialidades') }}/${espId}/servicios/${pivot.id}" method="POST"
                                    onsubmit="return confirm('¿Quitar este servicio de la especialidad?')">
                                    @csrf @method('DELETE')
                                    <button class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-[11px] font-bold transition-all">Quitar</button>
                                </form>
                            </div>
                        </td>
                    </tr>`;
            });
        }

        // ============ Reabrir el modal correcto si falla la validación ============
        @if ($errors->any() && in_array(old('_modal'), ['create', 'edit']))
            document.addEventListener('DOMContentLoaded', function() {
                @if (old('_modal') === 'edit')
                    const editId = @json(old('especialidad_id'));
                    document.getElementById('form-edit').action = `{{ url('especialidades') }}/${editId}`;
                    document.getElementById('edit-subtitle').textContent = 'Corrige los errores marcados';
                    openModal('modal-edit');
                @else
                    openModal('modal-create');
                @endif
            });
        @endif
    </script>
</x-app-layout>