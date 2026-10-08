<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight">Agenda Médica</h2>
                <p class="text-xs text-slate-400 mt-0.5">Control de citas y disponibilidad de especialistas</p>
            </div>

            <button type="button" @click="$dispatch('abrir-modal-crear')"
                class="bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white px-5 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 shadow-sm hover:shadow-indigo-200 hover:shadow-lg transition-all duration-200 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        @if (session('success'))
            <div
                class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-2xl text-xs font-semibold flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- Métricas --}}
        @php
            $metricas = [
                ['TOTAL SEMANA', $stats['total_semana'] ?? 0, 'text-slate-800'],
                ['PROGRAMADAS', $stats['programadas'] ?? 0, 'text-indigo-600'],
                ['ATENDIDAS', $stats['atendidas'] ?? 0, 'text-emerald-600'],
                ['CANCELADAS', $stats['canceladas'] ?? 0, 'text-rose-600'],
            ];
        @endphp
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach ($metricas as [$label, $valor, $textColor])
                <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ $label }}</p>
                    <p class="text-2xl font-extrabold {{ $textColor }} mt-0.5">{{ $valor }}</p>
                </div>
            @endforeach
        </div>

        {{-- Leyenda de triage --}}
        <div class="flex flex-wrap items-center gap-3 text-[11px] font-semibold text-slate-500">
            <span class="text-slate-400 uppercase tracking-wider font-bold">Triage:</span>
            <span class="flex items-center gap-1"><i class="w-2.5 h-2.5 rounded-full bg-rose-500"></i> Rojo</span>
            <span class="flex items-center gap-1"><i class="w-2.5 h-2.5 rounded-full bg-amber-500"></i> Naranja</span>
            <span class="flex items-center gap-1"><i class="w-2.5 h-2.5 rounded-full bg-yellow-400"></i> Amarillo</span>
            <span class="flex items-center gap-1"><i class="w-2.5 h-2.5 rounded-full bg-emerald-500"></i> Verde</span>
            <span class="flex items-center gap-1"><i class="w-2.5 h-2.5 rounded-full bg-sky-500"></i> Azul</span>
            <span class="flex items-center gap-1"><i class="w-2.5 h-2.5 rounded-full bg-slate-400"></i> Sin
                triage</span>
        </div>

        {{-- ==================== FILTROS ==================== --}}
        <div class="bg-white p-4 sm:p-5 rounded-3xl border border-slate-100 shadow-sm space-y-4">

            {{-- Header de filtros --}}
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    <h3 class="font-extrabold text-slate-700 text-sm">Filtros</h3>

                    {{-- Badge de filtros activos --}}
                    <span x-show="filtrosActivos > 0" x-cloak x-text="filtrosActivos"
                        class="inline-flex items-center justify-center min-w-[18px] h-[18px] px-1.5 rounded-full bg-indigo-600 text-white text-[10px] font-bold">
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
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">Estado</p>
                <div class="flex flex-wrap gap-1.5">
                    <template x-for="est in estadosDisponibles" :key="est.value">
                        <button type="button" @click="toggleEstado(est.value)"
                            :class="filtros.estados.includes(est.value) ?
                                'bg-indigo-600 text-white border-indigo-600' :
                                'bg-white text-slate-600 border-slate-200 hover:border-slate-300'"
                            class="px-3 py-1.5 rounded-lg border text-[11px] font-bold transition-all">
                            <span x-text="est.label"></span>
                        </button>
                    </template>
                </div>
            </div>

            {{-- Fila 2: Triage + Médico + Especialidad --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                {{-- Triage --}}
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">Triage</p>
                    <div class="flex flex-wrap gap-1.5">
                        <template x-for="tr in triagesDisponibles" :key="tr.value">
                            <button type="button" @click="toggleTriage(tr.value)"
                                :class="filtros.triages.includes(tr.value) ?
                                    'ring-2 ring-offset-1 ring-slate-400 border-slate-400' :
                                    'border-slate-200 hover:border-slate-300'"
                                class="flex items-center gap-1 px-2 py-1 rounded-lg border bg-white text-[10px] font-bold text-slate-600 transition-all">
                                <span class="w-2 h-2 rounded-full" :style="`background-color: ${tr.color}`"></span>
                                <span x-text="tr.label"></span>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Médico (solo admin) --}}
                @if ($medicos->isNotEmpty())
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">Médico</p>
                        <select x-model="filtros.medico_id" @change="aplicarFiltros()"
                            class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-[11px] text-slate-700 focus:bg-white focus:border-indigo-500 outline-none">
                            <option value="">— Todos los médicos —</option>
                            @foreach ($medicos as $m)
                                <option value="{{ $m->id }}">{{ $m->name }} {{ $m->apellido_paterno }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                {{-- Especialidad --}}
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">Especialidad</p>
                    <select x-model="filtros.especialidad_id" @change="aplicarFiltros()"
                        class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-[11px] text-slate-700 focus:bg-white focus:border-indigo-500 outline-none">
                        <option value="">— Todas las especialidades —</option>
                        @foreach ($especialidades as $esp)
                            <option value="{{ $esp->id }}">{{ $esp->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- CALENDARIO --}}
        <div class="bg-white p-4 sm:p-6 rounded-3xl border border-slate-100 shadow-sm">
            <div id="calendar"></div>
        </div>

        {{-- MODALES --}}
        @include('agenda.modal-crear')
        @include('agenda.modal-detalle')
    </div>
</x-app-layout>
