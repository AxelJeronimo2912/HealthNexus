<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-extrabold leading-tight text-slate-800">Agenda Médica</h2>
                <p class="mt-0.5 text-xs text-slate-400">Control de citas y disponibilidad de especialistas</p>
            </div>

            <button type="button" @click="$dispatch('abrir-modal-crear')"
                class="flex cursor-pointer items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-xs font-bold text-white shadow-sm transition-all duration-200 hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-200 active:scale-95">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                Nueva Cita
            </button>
        </div>
    </x-slot>

    {{-- Estilos FullCalendar --}}
    <style>
        .fc {
            --fc-border-color: #e2e8f0;
            --fc-button-bg-color: #4f46e5;
            --fc-button-border-color: #4f46e5;
            --fc-button-hover-bg-color: #4338ca;
            --fc-button-hover-border-color: #4338ca;
            --fc-button-active-bg-color: #3730a3;
            --fc-button-active-border-color: #3730a3;
            --fc-today-bg-color: rgba(99, 102, 241, .07);
            --fc-event-border-color: transparent;
            font-size: 12px;
        }

        .fc .fc-toolbar-title {
            font-size: 1.1rem;
            font-weight: 900;
            color: #1e293b;
            text-transform: capitalize;
        }

        .fc .fc-button {
            font-size: 11px;
            font-weight: 700;
            border-radius: 12px;
            padding: .45rem .9rem;
            text-transform: capitalize;
        }

        .fc .fc-col-header-cell-cushion {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            padding: 8px 4px;
        }

        .fc-event {
            cursor: pointer;
            border-radius: 8px;
            padding: 1px 3px;
            font-weight: 600;
        }

        .fc-theme-standard .fc-scrollgrid {
            border-radius: 16px;
            overflow: hidden;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>

    @include('agenda._scripts')

    <div x-data="agendaData()" x-init="initCalendar()" @abrir-modal-crear.window="abrirModal()"
        class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">

        @if (session('success'))
            <div
                class="flex items-center gap-2 rounded-2xl border border-emerald-100 bg-emerald-50 p-4 text-xs font-semibold text-emerald-800">
                <svg class="h-4 w-4 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- Métricas --}}
        @php
            $metricas = [
                ['TOTAL SEMANA', $stats['total_semana'] ?? 0, 'text-slate-800'],                 ['PROGRAMADAS',$stats['programadas'] ?? 0, 'text-indigo-600'],
                ['ATENDIDAS', $stats['atendidas'] ?? 0, 'text-emerald-600'],                 ['CANCELADAS',$stats['canceladas'] ?? 0, 'text-rose-600'],
            ];
        @endphp
        <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
            @foreach ($metricas as [$label, $valor,$textColor])
                <div class="rounded-3xl border border-slate-100 bg-white p-5 shadow-sm">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">{{ $label }}</p>
                    <p class="mt-0.5 text-2xl font-extrabold {{ $textColor }}">{{ $valor }}</p>
                </div>
            @endforeach
        </div>

        {{-- Leyenda de triage --}}
        <div class="flex flex-wrap items-center gap-3 text-[11px] font-semibold text-slate-500">
            <span class="font-bold uppercase tracking-wider text-slate-400">Triage:</span>
            <span class="flex items-center gap-1"><i class="h-2.5 w-2.5 rounded-full bg-rose-500"></i> Rojo</span>
            <span class="flex items-center gap-1"><i class="h-2.5 w-2.5 rounded-full bg-amber-500"></i> Naranja</span>
            <span class="flex items-center gap-1"><i class="h-2.5 w-2.5 rounded-full bg-yellow-400"></i> Amarillo</span>
            <span class="flex items-center gap-1"><i class="h-2.5 w-2.5 rounded-full bg-emerald-500"></i> Verde</span>
            <span class="flex items-center gap-1"><i class="h-2.5 w-2.5 rounded-full bg-sky-500"></i> Azul</span>
            <span class="flex items-center gap-1"><i class="h-2.5 w-2.5 rounded-full bg-slate-400"></i> Sin triage</span>
        </div>

        {{-- ==================== FILTROS ==================== --}}
        <div class="space-y-4 rounded-3xl border border-slate-100 bg-white p-4 shadow-sm sm:p-5">

            {{-- Header de filtros --}}
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    <h3 class="text-sm font-extrabold text-slate-700">Filtros</h3>

                    {{-- Badge de filtros activos --}}
                    <span x-show="filtrosActivos > 0" x-cloak x-text="filtrosActivos"
                        class="inline-flex h-[18px] min-w-[18px] items-center justify-center rounded-full bg-indigo-600 px-1.5 text-[10px] font-bold text-white">
                    </span>
                </div>

                <div class="flex items-center gap-3">
                    <span class="text-[11px] font-semibold transition-all duration-200"
                        :class="filtrando ? 'text-indigo-600 scale-110' : 'text-slate-400'">
                        <span x-text="citasFiltradas"></span> de <span x-text="citasTotales"></span> citas
                    </span>

                    <button type="button" @click="limpiarFiltros()" x-show="filtrosActivos > 0" x-cloak
                        class="text-[11px] font-bold text-indigo-600 hover:text-indigo-800 hover:underline">
                        Limpiar filtros
                    </button>
                </div>
            </div>

            {{-- Fila 1: Estados --}}
            <div>
                <p class="mb-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">Estado</p>
                <div class="flex flex-wrap gap-1.5">
                    <template x-for="est in estadosDisponibles" :key="est.value">
                        <button type="button" @click="toggleEstado(est.value)"
                            :class="filtros.estados.includes(est.value) ?
                                'bg-indigo-600 text-white border-indigo-600' :
                                'bg-white text-slate-600 border-slate-200 hover:border-slate-300'"
                            class="rounded-lg border px-3 py-1.5 text-[11px] font-bold transition-all">
                            <span x-text="est.label"></span>
                        </button>
                    </template>
                </div>
            </div>

            {{-- Fila 2: Triage + Médico + Especialidad --}}
            <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
                {{-- Triage --}}
                <div>
                    <p class="mb-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">Triage</p>
                    <div class="flex flex-wrap gap-1.5">
                        <template x-for="tr in triagesDisponibles" :key="tr.value">
                            <button type="button" @click="toggleTriage(tr.value)"
                                :class="filtros.triages.includes(tr.value) ?
                                    'ring-2 ring-offset-1 ring-slate-400 border-slate-400' :
                                    'border-slate-200 hover:border-slate-300'"
                                class="flex items-center gap-1 rounded-lg border bg-white px-2 py-1 text-[10px] font-bold text-slate-600 transition-all">
                                <span class="h-2 w-2 rounded-full" :style="`background-color: ${tr.color}`"></span>
                                <span x-text="tr.label"></span>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Médico (solo admin/enfermería si existen registros) --}}
                @if (isset($medicos) &&$medicos->isNotEmpty())
                    <div>
                        <p class="mb-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">Médico</p>
                        <select x-model="filtros.medico_id" @change="aplicarFiltros()"
                            class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-[11px] text-slate-700 outline-none focus:border-indigo-500 focus:bg-white">
                            <option value="">— Todos los médicos —</option>
                            @foreach ($medicos as$m)
                                <option value="{{ $m->id }}">
                                    {{ $m->name }} {{$m->apellido_paterno ?? '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                {{-- Especialidad --}}
                <div>
                    <p class="mb-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">Especialidad</p>
                    <select x-model="filtros.especialidad_id" @change="aplicarFiltros()"
                        class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-[11px] text-slate-700 outline-none focus:border-indigo-500 focus:bg-white">
                        <option value="">— Todas las especialidades —</option>
                        @foreach ($especialidades ?? [] as $esp)
                            <option value="{{ $esp->id }}">{{ $esp->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- CALENDARIO --}}
        <div class="rounded-3xl border border-slate-100 bg-white p-4 shadow-sm sm:p-6">
            <div id="calendar"></div>
        </div>

        {{-- MODALES --}}
        @include('agenda.modal-crear')
        @include('agenda.modal-detalle')
    </div>
</x-app-layout>