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
            color: #1e293b;
            font-size: 1.1rem;
            font-weight: 900;
            text-transform: capitalize;
        }

        .fc .fc-button {
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
            padding: .45rem .9rem;
            text-transform: capitalize;
        }

        .fc .fc-col-header-cell-cushion {
            color: #64748b;
            font-size: 11px;
            font-weight: 700;
            padding: 8px 4px;
            text-transform: uppercase;
        }

        .fc-event {
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            padding: 1px 3px;
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

        @php
            $metricas = [
                ['TOTAL SEMANA', $stats['total_semana'] ?? 0, 'text-slate-800'],
                ['PROGRAMADAS', $stats['programadas'] ?? 0, 'text-indigo-600'],
                ['ATENDIDAS', $stats['atendidas'] ?? 0, 'text-emerald-600'],
                ['CANCELADAS', $stats['canceladas'] ?? 0, 'text-rose-600'],
            ];
        @endphp
        <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
            @foreach ($metricas as [$label, $valor, $textColor])
                <div class="rounded-3xl border border-slate-100 bg-white p-5 shadow-sm">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">{{ $label }}</p>
                    <p class="mt-0.5 text-2xl font-extrabold {{ $textColor }}">{{ $valor }}</p>
                </div>
            @endforeach
        </div>

        <div class="flex flex-wrap items-center gap-3 text-[11px] font-semibold text-slate-500">
            <span class="font-bold uppercase tracking-wider text-slate-400">Triage:</span>
            <span class="flex items-center gap-1"><i class="h-2.5 w-2.5 rounded-full bg-rose-500"></i> Rojo</span>
            <span class="flex items-center gap-1"><i class="h-2.5 w-2.5 rounded-full bg-amber-500"></i> Naranja</span>
            <span class="flex items-center gap-1"><i class="h-2.5 w-2.5 rounded-full bg-yellow-400"></i> Amarillo</span>
            <span class="flex items-center gap-1"><i class="h-2.5 w-2.5 rounded-full bg-emerald-500"></i> Verde</span>
            <span class="flex items-center gap-1"><i class="h-2.5 w-2.5 rounded-full bg-sky-500"></i> Azul</span>
            <span class="flex items-center gap-1"><i class="h-2.5 w-2.5 rounded-full bg-slate-400"></i> Sin triage</span>
        </div>

        <div class="rounded-3xl border border-slate-100 bg-white p-4 shadow-sm sm:p-6">
            <p x-show="calendarError" x-cloak x-text="calendarError" role="alert"
                class="mb-4 rounded-xl border border-rose-100 bg-rose-50 p-3 text-xs font-semibold text-rose-700"></p>
            <div id="calendar"></div>
        </div>

        @include('agenda.modal-crear')
        @include('agenda.modal-detalle')
    </div>
</x-app-layout>
