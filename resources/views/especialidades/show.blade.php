<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-3">
            <div class="min-w-0">
                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight truncate">
                    Especialidad: {{ $especialidad->nombre }}
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Detalle, médicos y servicios asociados</p>
            </div>
            <a href="{{ route('especialidades.index') }}"
                class="bg-white hover:bg-indigo-50 text-slate-700 hover:text-indigo-600 border border-slate-200 hover:border-indigo-200 px-5 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 transition-all active:scale-95 group shrink-0">
                <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
                Volver
            </a>
        </div>
    </x-slot>

    @php
        $thCls = 'px-5 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider';
    @endphp

    <div class="py-8 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- ============ ENCABEZADO ============ --}}
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
            <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-5">
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        @if ($especialidad->color)
                            <span class="w-4 h-4 rounded-full shrink-0"
                                style="background-color: {{ $especialidad->color_hex }}"></span>
                        @endif
                        <span class="px-2 py-1 bg-slate-100 text-slate-600 rounded-lg text-[11px] font-mono font-bold">
                            {{ $especialidad->codigo }}
                        </span>
                        <span
                            class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide {{ $especialidad->grupo_color }}">
                            {{ $especialidad->grupo_label }}
                        </span>
                    </div>

                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mt-4">ESPECIALIDAD</p>
                    <p class="text-4xl font-black text-slate-800 tracking-tight mt-0.5">
                        {{ $especialidad->nombre }}
                    </p>

                    <div class="mt-4 grid grid-cols-2 gap-3">
                        <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Duración por
                                defecto</p>
                            <p class="text-sm font-extrabold text-slate-800 mt-0.5">
                                {{ $especialidad->duracion_consulta_default }} min
                            </p>
                        </div>
                        <div
                            class="{{ $especialidad->activo ? 'bg-emerald-50/60 border-emerald-100' : 'bg-rose-50/60 border-rose-100' }} border rounded-2xl p-3">
                            <p
                                class="text-[10px] font-bold uppercase tracking-wider {{ $especialidad->activo ? 'text-emerald-600/70' : 'text-rose-500/70' }}">
                                Estado
                            </p>
                            <p
                                class="text-sm font-extrabold mt-0.5 {{ $especialidad->activo ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $especialidad->activo ? 'Activa' : 'Inactiva' }}
                            </p>
                        </div>
                    </div>

                    @if ($especialidad->descripcion)
                        <p class="mt-4 text-xs text-slate-600 whitespace-pre-line leading-relaxed">
                            {{ $especialidad->descripcion }}
                        </p>
                    @endif
                </div>

                <div class="md:text-right space-y-2 md:max-w-[220px]">
                    <a href="{{ route('especialidades.edit', $especialidad) }}"
                        class="w-full md:w-auto inline-flex items-center justify-center gap-1.5 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Editar
                    </a>
                </div>
            </div>
        </div>

        {{-- ============ ESTADÍSTICAS ============ --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm">
                <p class="text-[11px] font-bold text-indigo-600/70 uppercase tracking-wider">Médicos</p>
                <p class="text-3xl font-black text-indigo-600 tracking-tight mt-1">
                    {{ $especialidad->medicos->count() }}</p>
            </div>
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm">
                <p class="text-[11px] font-bold text-purple-600/70 uppercase tracking-wider">Servicios</p>
                <p class="text-3xl font-black text-purple-600 tracking-tight mt-1">
                    {{ $especialidad->servicios->count() }}</p>
            </div>
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm">
                <p class="text-[11px] font-bold text-amber-600/70 uppercase tracking-wider">Citas</p>
                <p class="text-3xl font-black text-amber-600 tracking-tight mt-1">{{ $stats['citas'] }}</p>
            </div>
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm">
                <p class="text-[11px] font-bold text-emerald-600/70 uppercase tracking-wider">Consultas</p>
                <p class="text-3xl font-black text-emerald-600 tracking-tight mt-1">{{ $stats['consultas'] }}</p>
            </div>
        </div>

        {{-- ============ MÉDICOS ============ --}}
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex flex-wrap justify-between items-center gap-3">
                <div>
                    <h3 class="font-extrabold text-slate-800 text-base">Médicos asignados</h3>
                    <p class="text-[11px] text-slate-400">{{ $especialidad->medicos->count() }} médicos en esta
                        especialidad</p>
                </div>
                <a href="{{ route('especialidades.medicos', $especialidad) }}"
                    class="px-3.5 py-2 bg-purple-50 hover:bg-purple-100 active:scale-95 text-purple-700 border border-purple-100 rounded-xl text-[11px] font-bold transition-all">
                    Gestionar médicos →
                </a>
            </div>

            @if ($especialidad->medicos->isEmpty())
                <div class="px-5 py-12 text-center">
                    <p class="text-xs font-semibold text-slate-400">Sin médicos asignados.</p>
                </div>
            @else
                <ul class="divide-y divide-slate-100">
                    @foreach ($especialidad->medicos as $m)
                        <li
                            class="px-5 py-3.5 flex flex-wrap justify-between items-center gap-3 hover:bg-indigo-50/30 transition-colors">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <p class="text-xs font-bold text-slate-800">
                                        {{ $m->nombre_completo ?: $m->name }}
                                    </p>
                                    @if ($m->pivot->es_principal)
                                        <span
                                            class="px-2.5 py-1 bg-amber-50 text-amber-700 border border-amber-100 rounded-full text-[10px] font-bold uppercase tracking-wide">
                                            Principal
                                        </span>
                                    @endif
                                </div>
                                @if ($m->pivot->numero_cedula_especialidad)
                                    <p class="text-[11px] text-slate-400 mt-0.5">
                                        Cédula: {{ $m->pivot->numero_cedula_especialidad }}
                                    </p>
                                @endif
                            </div>
                            <span
                                class="px-2.5 py-1 bg-slate-100 text-slate-600 rounded-full text-[10px] font-bold uppercase tracking-wide shrink-0">
                                {{ $m->getRoleNames()->first() ?? 'sin rol' }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        {{-- ============ SERVICIOS ============ --}}
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex flex-wrap justify-between items-center gap-3">
                <div>
                    <h3 class="font-extrabold text-slate-800 text-base">Servicios asociados</h3>
                    <p class="text-[11px] text-slate-400">{{ $especialidad->servicios->count() }} servicios en esta
                        especialidad</p>
                </div>
                <a href="{{ route('especialidades.servicios', $especialidad) }}"
                    class="px-3.5 py-2 bg-amber-50 hover:bg-amber-100 active:scale-95 text-amber-700 border border-amber-100 rounded-xl text-[11px] font-bold transition-all">
                    Gestionar servicios →
                </a>
            </div>

            @if ($especialidad->servicios->isEmpty())
                <div class="px-5 py-12 text-center">
                    <p class="text-xs font-semibold text-slate-400">Sin servicios asociados.</p>
                </div>
            @else
                <div class="p-5 flex flex-wrap gap-2">
                    @foreach ($especialidad->servicios as $s)
                        <a href="{{ route('servicios.show', $s) }}"
                            class="px-3 py-1.5 bg-slate-100 hover:bg-indigo-50 hover:text-indigo-700 text-slate-700 border border-slate-200 hover:border-indigo-200 rounded-xl text-[11px] font-bold transition-all">
                            {{ $s->nombre }}
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ============ BOTONES DE ACCIÓN ============ --}}
        <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex flex-wrap gap-2">
            <a href="{{ route('especialidades.edit', $especialidad) }}"
                class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all inline-flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Editar
            </a>
            <a href="{{ route('especialidades.medicos', $especialidad) }}"
                class="px-4 py-2.5 bg-purple-600 hover:bg-purple-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all">
                Gestionar médicos
            </a>
            <a href="{{ route('especialidades.servicios', $especialidad) }}"
                class="px-4 py-2.5 bg-amber-500 hover:bg-amber-600 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all">
                Gestionar servicios
            </a>
            <a href="{{ route('especialidades.index') }}"
                class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-600 rounded-xl text-xs font-bold transition-all">
                Volver
            </a>
        </div>
    </div>
</x-app-layout>
