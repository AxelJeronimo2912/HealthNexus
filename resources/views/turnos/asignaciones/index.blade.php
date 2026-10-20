<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-3">
            <div class="min-w-0">
                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight truncate">
                    Turnos de {{ $user->nombre_completo }}
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Asignaciones de turno, horarios y vigencia</p>
            </div>

        </div>
    </x-slot>

    @php
    $thCls = 'px-5 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider';

    // ── Calendario semanal ──
    $diasSemana = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
    $mapDia = [1 => 0, 2 => 1, 3 => 2, 4 => 3, 5 => 4, 6 => 5, 0 => 6];

    $horaMin = 8;
    $horaMax = 20;
    foreach ($asignaciones as $a) {
    $ini = (int) substr($a->hora_inicio, 0, 2);
    $fin = (int) substr($a->hora_fin, 0, 2);
    if ($a->cruza_medianoche || $fin <= $ini) {
        $fin=24;
        }
        $horaMin=min($horaMin, $ini);
        $horaMax=max($horaMax, $fin);
        }
        $horaMin=max(0, $horaMin - 1);
        $horaMax=min(24, $horaMax + 1);
        $rangoHoras=range($horaMin, $horaMax);
        $altoPorHora=44;

        $paleta=[ 'bg-indigo-500/90 border-indigo-600 text-white' , 'bg-emerald-500/90 border-emerald-600 text-white' , 'bg-amber-500/90 border-amber-600 text-white' , 'bg-rose-500/90 border-rose-600 text-white' , 'bg-sky-500/90 border-sky-600 text-white' , 'bg-purple-500/90 border-purple-600 text-white' , 'bg-teal-500/90 border-teal-600 text-white' ,
        ];

        $bloques=[];
        foreach ($asignaciones as $a) {
        $p=$a->pivot;
        if (!$p->activo) {
        continue;
        }

        $iniMin = ((int) substr($a->hora_inicio, 0, 2)) * 60 + ((int) substr($a->hora_inicio, 3, 2));
        $finMin = ((int) substr($a->hora_fin, 0, 2)) * 60 + ((int) substr($a->hora_fin, 3, 2));
        if ($a->cruza_medianoche || $finMin <= $iniMin) {
            $finMin=24 * 60;
            }

            $top=(($iniMin - $horaMin * 60) / 60) * $altoPorHora;
            $alto=max(28, (($finMin - $iniMin) / 60) * $altoPorHora);

            $color=$paleta[$a->id % count($paleta)];

            $bloques[] = [
            'id' => $a->id,
            'pivot_id' => $p->id,
            'nombre' => $a->nombre,
            'rango' => $a->rango,
            'dia_semana' => $p->dia_semana,
            'col' => is_null($p->dia_semana) ? null : $mapDia[$p->dia_semana],
            'top' => $top,
            'alto' => $alto,
            'color' => $color,
            'area' => $p->area,
            'fecha_inicio' => $p->fecha_inicio,
            'fecha_fin' => $p->fecha_fin,
            ];
            }

            // Turnos disponibles para el modal
            $turnosJs = $turnos
            ->map(
            fn($t) => [
            'id' => $t->id,
            'nombre' => $t->nombre,
            'codigo' => $t->codigo,
            'hora_inicio' => substr($t->hora_inicio, 0, 5),
            'hora_fin' => substr($t->hora_fin, 0, 5),
            'cruza' => (bool) $t->cruza_medianoche,
            'rango' => substr($t->hora_inicio, 0, 5) . ' — ' . substr($t->hora_fin, 0, 5),
            ],
            )
            ->values();
            @endphp

            <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6" x-data="{ modalCalendario: false, vista: 'calendario' }">

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

                <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 flex flex-wrap justify-between items-center gap-3">
                        <div>
                            <h3 class="font-extrabold text-slate-800 text-base">Turnos asignados</h3>
                            <p class="text-[11px] text-slate-400">{{ $asignaciones->count() }} turnos en total</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="inline-flex bg-slate-100 p-1 rounded-xl text-[11px] font-bold">
                                <button type="button" @click="vista = 'calendario'"
                                    :class="vista === 'calendario' ? 'bg-white shadow text-indigo-600' :
                                'text-slate-500 hover:text-slate-700'"
                                    class="px-3 py-1.5 rounded-lg transition">Calendario</button>
                                <button type="button" @click="vista = 'tabla'"
                                    :class="vista === 'tabla' ? 'bg-white shadow text-indigo-600' :
                                'text-slate-500 hover:text-slate-700'"
                                    class="px-3 py-1.5 rounded-lg transition">Tabla</button>
                            </div>

                            <button type="button" @click="modalCalendario = true"
                                class="px-3 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-xl text-[11px] font-bold transition-all flex items-center gap-1.5"
                                title="Ver calendario en pantalla completa">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                                </svg>
                                Expandir
                            </button>

                            <a href="{{ route('admin.users.index') }}"
                                class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-600 rounded-xl text-[11px] font-bold transition-all">
                                ← Volver
                            </a>
                        </div>
                    </div>

                    {{-- 💡 Ayuda compacta --}}
                    <div class="px-5 py-3 bg-indigo-50/50 border-b border-indigo-100/60 flex items-start gap-2">
                        <svg class="w-3.5 h-3.5 text-indigo-500 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-[11px] text-indigo-800 leading-relaxed">
                            <strong>Haz clic en un hueco del calendario</strong> para asignar un turno en ese día y hora.
                            No se permiten turnos con <strong>horario solapado</strong>.
                        </p>
                    </div>

                    {{-- ════════ VISTA CALENDARIO ════════ --}}
                    <div x-show="vista === 'calendario'" class="p-5">
                        @if ($asignaciones->isEmpty() && $turnos->isEmpty())
                        <div class="py-16 text-center">
                            <p class="text-xs font-semibold text-slate-400">No hay turnos para mostrar.</p>
                        </div>
                        @else
                        @include('turnos.asignaciones._calendario', [
                        'bloques' => $bloques,
                        'diasSemana' => $diasSemana,
                        'rangoHoras' => $rangoHoras,
                        'altoPorHora' => $altoPorHora,
                        'user' => $user,
                        'clickable' => true,
                        ])
                        @endif
                    </div>

                    {{-- ════════ VISTA TABLA ════════ --}}
                    <div x-show="vista === 'tabla'" x-cloak class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-slate-50/70 border-b border-slate-100">
                                <tr>
                                    <th class="{{ $thCls }} text-left">Turno</th>
                                    <th class="{{ $thCls }} text-left">Horario</th>
                                    <th class="{{ $thCls }} text-left">Día</th>
                                    <th class="{{ $thCls }} text-left">Vigencia</th>
                                    <th class="{{ $thCls }} text-left">Área</th>
                                    <th class="{{ $thCls }} text-left">Estado</th>
                                    <th class="px-5 py-3.5"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($asignaciones as $turno)
                                @php
                                $p = $turno->pivot;
                                $pivotId = $p->id;
                                $dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
                                $esIndefinido = is_null($p->fecha_fin);
                                $esTodosDias = is_null($p->dia_semana);
                                @endphp
                                <tr class="hover:bg-indigo-50/30 transition-colors {{ $p->activo ? '' : 'opacity-60' }}">
                                    <td class="px-5 py-3.5">
                                        <p class="text-xs font-bold text-slate-800">{{ $turno->nombre }}</p>
                                        @if ($turno->codigo)
                                        <p class="text-[10px] font-mono font-bold text-slate-400 mt-0.5">
                                            {{ $turno->codigo }}
                                        </p>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span
                                            class="px-2 py-1 bg-indigo-50 text-indigo-700 border border-indigo-100 rounded-lg text-[11px] font-mono font-bold">
                                            {{ $turno->rango }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span
                                            class="px-2.5 py-1 bg-slate-100 text-slate-600 rounded-full text-[10px] font-bold uppercase tracking-wide">
                                            {{ $esTodosDias ? 'Todos' : $dias[$p->dia_semana] }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <p class="text-xs font-medium text-slate-600">
                                            {{ \Carbon\Carbon::parse($p->fecha_inicio)->format('d/m/Y') }}
                                            <span class="text-slate-300">—</span>
                                            @if ($esIndefinido)
                                            <span class="italic text-slate-400">indefinido</span>
                                            @else
                                            {{ \Carbon\Carbon::parse($p->fecha_fin)->format('d/m/Y') }}
                                            @endif
                                        </p>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        @if ($p->area)
                                        <span
                                            class="px-2 py-1 bg-purple-50 text-purple-700 border border-purple-100 rounded-lg text-[11px] font-bold">
                                            {{ $p->area }}
                                        </span>
                                        @else
                                        <span class="text-slate-300 text-xs">—</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5">
                                        @if ($p->activo)
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-full text-[10px] font-bold uppercase tracking-wide">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                            Activo
                                        </span>
                                        @else
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-100 text-slate-500 rounded-full text-[10px] font-bold uppercase tracking-wide">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                            Inactivo
                                        </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <div class="flex flex-wrap justify-end gap-2 whitespace-nowrap">
                                            <form action="{{ route('admin.users.turnos.toggle', [$user, $pivotId]) }}"
                                                method="POST">
                                                @csrf
                                                <button
                                                    class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg text-[11px] font-bold transition-all">
                                                    {{ $p->activo ? 'Desactivar' : 'Activar' }}
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.users.turnos.destroy', [$user, $pivotId]) }}"
                                                method="POST" onsubmit="return confirm('¿Eliminar esta asignación?')">
                                                @csrf @method('DELETE')
                                                <button
                                                    class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-[11px] font-bold transition-all">
                                                    Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-12 text-center">
                                        <p class="text-xs font-semibold text-slate-400">Sin turnos asignados.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- ════════ MODAL CALENDARIO EXPANDIDO ════════ --}}
                <div x-show="modalCalendario" x-cloak class="fixed inset-0 z-40 overflow-y-auto" x-transition.opacity>
                    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="modalCalendario = false"></div>

                    <div class="relative min-h-full flex items-start justify-center p-4 sm:p-8">
                        <div x-show="modalCalendario" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-4"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 translate-y-4" @click.stop
                            class="relative w-full max-w-6xl bg-white rounded-3xl border border-slate-100 shadow-2xl overflow-hidden">

                            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="font-extrabold text-slate-800 text-base">Calendario semanal</h3>
                                        <p class="text-[11px] text-slate-400">Haz clic en un hueco para asignar ·
                                            {{ $user->nombre_completo }}
                                        </p>
                                    </div>
                                </div>
                                <button type="button" @click="modalCalendario = false"
                                    class="w-9 h-9 rounded-xl hover:bg-slate-100 text-slate-400 hover:text-slate-700 transition-colors flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <div class="p-6 max-h-[75vh] overflow-y-auto">
                                @include('turnos.asignaciones._calendario', [
                                'bloques' => $bloques,
                                'diasSemana' => $diasSemana,
                                'rangoHoras' => $rangoHoras,
                                'altoPorHora' => $altoPorHora,
                                'user' => $user,
                                'clickable' => true,
                                ])
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ════════ MODAL CREAR ASIGNACIÓN ════════ --}}
                @include('turnos.asignaciones._modal-crear')
            </div>

            {{-- Inyectamos los turnos disponibles al JS del modal --}}
            <script>
                window.__turnosDisponibles = @js($turnosJs);
            </script>
</x-app-layout>
