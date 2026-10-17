<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-3">
            <div class="min-w-0">
                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight truncate">
                    Turnos de {{ $user->nombre_completo }}
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Asignaciones de turno, horarios y vigencia</p>
            </div>
            <a href="{{ route('admin.users.turnos.create', $user) }}"
                class="bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white px-5 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 shadow-sm transition-all shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                Asignar Turno
            </a>
        </div>
    </x-slot>

    @php
        $thCls = 'px-5 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider';
    @endphp

    <div class="py-8 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

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
                <a href="{{ route('admin.users.index') }}"
                    class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-600 rounded-xl text-[11px] font-bold transition-all">
                    ← Volver a usuarios
                </a>
            </div>

            {{-- 💡 Ayuda compacta (una sola línea) --}}
            <div class="px-5 py-3 bg-indigo-50/50 border-b border-indigo-100/60 flex items-start gap-2">
                <svg class="w-3.5 h-3.5 text-indigo-500 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-[11px] text-indigo-800 leading-relaxed">
                    <strong>Día = "Todos"</strong> aplica cualquier día de la semana.
                    <strong>Vigencia "indefinido"</strong> no tiene fecha de término.
                    No se permiten turnos con <strong>horario solapado</strong> para el mismo usuario.
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-slate-50/70 border-b border-slate-100">
                        <tr>
                            <th class="{{ $thCls }} text-left" title="Nombre y código del turno">Turno</th>
                            <th class="{{ $thCls }} text-left" title="Rango de horas del turno">Horario</th>
                            <th class="{{ $thCls }} text-left" title="Día de la semana en que aplica">Día</th>
                            <th class="{{ $thCls }} text-left" title="Desde cuándo y hasta cuándo es válido">
                                Vigencia</th>
                            <th class="{{ $thCls }} text-left" title="Área o servicio donde se desempeña">Área
                            </th>
                            <th class="{{ $thCls }} text-left" title="Estado de la asignación">Estado</th>
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
                            <tr class="hover:bg-indigo-50/30 transition-colors">
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
                                    @if ($esTodosDias)
                                        <span
                                            class="px-2.5 py-1 bg-slate-100 text-slate-600 rounded-full text-[10px] font-bold uppercase tracking-wide"
                                            title="Aplica cualquier día de la semana">
                                            Todos
                                        </span>
                                    @else
                                        <span
                                            class="px-2.5 py-1 bg-slate-100 text-slate-600 rounded-full text-[10px] font-bold uppercase tracking-wide"
                                            title="Solo aplica ese día de la semana">
                                            {{ $dias[$p->dia_semana] }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    <p class="text-xs font-medium text-slate-600">
                                        {{ \Carbon\Carbon::parse($p->fecha_inicio)->format('d/m/Y') }}
                                        <span class="text-slate-300">—</span>
                                        @if ($esIndefinido)
                                            <span class="italic text-slate-400"
                                                title="Sin fecha de término">indefinido</span>
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
                                            class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-full text-[10px] font-bold uppercase tracking-wide"
                                            title="Turno vigente y aplicable">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Activo
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-100 text-slate-500 rounded-full text-[10px] font-bold uppercase tracking-wide"
                                            title="Turno desactivado (no aplica)">
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
                                                title="{{ $p->activo ? 'Desactivar esta asignación' : 'Reactivar esta asignación' }}"
                                                class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg text-[11px] font-bold transition-all">
                                                {{ $p->activo ? 'Desactivar' : 'Activar' }}
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.users.turnos.destroy', [$user, $pivotId]) }}"
                                            method="POST" onsubmit="return confirm('¿Eliminar esta asignación?')">
                                            @csrf @method('DELETE')
                                            <button title="Eliminar definitivamente esta asignación"
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
                                    <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="text-xs font-semibold text-slate-400">Sin turnos asignados.</p>
                                    <a href="{{ route('admin.users.turnos.create', $user) }}"
                                        class="inline-block mt-3 text-[11px] font-bold text-indigo-600 hover:text-indigo-800 hover:underline">
                                        Asignar el primer turno →
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
