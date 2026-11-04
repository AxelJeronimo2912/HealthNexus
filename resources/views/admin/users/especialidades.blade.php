<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-start gap-4">
            <div class="min-w-0 flex-1">
                <nav class="flex items-center gap-1.5 text-xs text-slate-400 mb-1.5" aria-label="Breadcrumb">
                    <a href="{{ route('admin.users.index') }}" class="hover:text-brand-600 transition-colors">Usuarios</a>
                    <svg class="w-3 h-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                    </svg>
                    <a href="{{ route('admin.users.show', $user) }}"
                        class="hover:text-brand-600 transition-colors truncate max-w-[160px]">
                        {{ $user->nombre_completo }}
                    </a>
                    <svg class="w-3 h-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                    </svg>
                    <span class="text-slate-600 font-medium">Especialidades</span>
                </nav>

                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight truncate">
                    Especialidades de {{ $user->nombre_completo }}
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">
                    Asignación de especialidades médicas y cédulas
                </p>
            </div>

            <a href="{{ route('admin.users.show', $user) }}"
                class="bg-white hover:bg-brand-50 text-slate-700 hover:text-brand-600 border border-slate-200 hover:border-brand-200 px-5 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 transition-all active:scale-95 group shrink-0">
                <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
                Volver
            </a>
        </div>
    </x-slot>

    @php
        $inputCls =
            'w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition';
        $labelCls = 'block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5';
        $thCls = 'px-5 py-3.5 text-xs font-bold text-slate-400 uppercase tracking-wider';

        $total = $user->especialidades->count();
        $principal = $user->especialidades->firstWhere('pivot.es_principal', true);
        $conCedula = $user->especialidades->filter(fn($e) => !empty($e->pivot->numero_cedula_especialidad))->count();
    @endphp

    <div class="py-8 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- ============ RESUMEN RÁPIDO ============ --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white p-5 rounded-2xl ring-1 ring-slate-200/70 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Asignadas</p>
                    <div class="w-8 h-8 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 14l9-5-9-5-9 5 9 5z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-black text-slate-800 tracking-tight">{{ $total }}</p>
                <p class="text-xs text-slate-500 mt-1">especialidades activas</p>
            </div>

            <div class="bg-white p-5 rounded-2xl ring-1 ring-slate-200/70 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Principal</p>
                    <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                    </div>
                </div>
                <p class="text-sm font-bold text-slate-800 truncate">
                    {{ $principal?->nombre ?? 'Sin definir' }}
                </p>
                <p class="text-xs text-slate-500 mt-1">
                    {{ $principal ? 'Especialidad principal' : 'Marca una como principal' }}
                </p>
            </div>

            <div class="bg-white p-5 rounded-2xl ring-1 ring-slate-200/70 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Con cédula</p>
                    <div class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-black text-slate-800 tracking-tight">{{ $conCedula }}</p>
                <p class="text-xs text-slate-500 mt-1">de {{ $total }} registradas</p>
            </div>
        </div>

        {{-- ============ FORMULARIO DE ASIGNACIÓN ============ --}}
        <div class="bg-white rounded-2xl ring-1 ring-slate-200/70 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-600 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-800 text-base">Asignar especialidad</h3>
                    <p class="text-xs text-slate-400">Agrega una especialidad al colaborador</p>
                </div>
            </div>

            <div class="p-5">
                @if ($disponibles->isEmpty())
                    <div class="flex flex-col items-center justify-center py-10 text-center">
                        <div
                            class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="text-sm font-semibold text-slate-700">Sin especialidades disponibles</p>
                        <p class="text-xs text-slate-500 mt-1">
                            Este usuario ya tiene asignadas todas las especialidades activas.
                        </p>
                    </div>
                @else
                    <form action="{{ route('admin.users.especialidades.asignar', $user) }}" method="POST"
                        class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                        @csrf

                        <div class="md:col-span-5">
                            <label class="{{ $labelCls }}">Especialidad *</label>
                            <select name="especialidad_id" required class="{{ $inputCls }}">
                                <option value="">— Selecciona —</option>
                                @foreach ($disponibles as $esp)
                                    <option value="{{ $esp->id }}">
                                        {{ $esp->nombre }} ({{ $esp->codigo }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-4">
                            <label class="{{ $labelCls }}">Cédula especialidad</label>
                            <input type="text" name="numero_cedula_especialidad" placeholder="Ej. 12345678"
                                class="{{ $inputCls }}">
                        </div>

                        <div class="md:col-span-3">
                            <label
                                class="inline-flex items-center gap-2.5 cursor-pointer px-3 py-2.5 bg-amber-50/60 border border-amber-100 rounded-xl w-full hover:bg-amber-50 transition">
                                <input type="checkbox" name="es_principal" value="1"
                                    class="w-4 h-4 rounded border-slate-300 text-amber-600 focus:ring-amber-500 cursor-pointer">
                                <span class="text-xs font-bold text-amber-700">Marcar como principal</span>
                            </label>
                        </div>

                        <div class="md:col-span-12 flex justify-end pt-2 border-t border-slate-100">
                            <button type="submit"
                                class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all inline-flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                Asignar
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>

        {{-- ============ LISTA ============ --}}
        <div class="bg-white rounded-2xl ring-1 ring-slate-200/70 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between gap-3">
                <div>
                    <h3 class="font-extrabold text-slate-800 text-base">Especialidades asignadas</h3>
                    <p class="text-xs text-slate-400">
                        {{ $total }} {{ \Illuminate\Support\Str::plural('especialidad', $total) }} en total
                    </p>
                </div>
                @if ($total > 0)
                    <span
                        class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-full text-[11px] font-bold text-slate-600">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Activas
                    </span>
                @endif
            </div>

            @if ($user->especialidades->isEmpty())
                <div class="px-5 py-14 text-center">
                    <div
                        class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-slate-700">Sin especialidades asignadas</p>
                    <p class="text-xs text-slate-500 mt-1">Usa el formulario superior para asignar la primera.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-slate-50/70 border-b border-slate-100">
                            <tr>
                                <th class="{{ $thCls }} text-left">Especialidad</th>
                                <th class="{{ $thCls }} text-left">Grupo</th>
                                <th class="{{ $thCls }} text-left">Cédula esp.</th>
                                <th class="{{ $thCls }} text-center">Principal</th>
                                <th class="px-5 py-3.5"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($user->especialidades as $esp)
                                <tr
                                    class="hover:bg-brand-50/30 transition-colors {{ $esp->pivot->es_principal ? 'bg-amber-50/20' : '' }}">
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center gap-2">
                                            <p class="text-xs font-bold text-slate-800">{{ $esp->nombre }}</p>
                                            @if ($esp->codigo)
                                                <span
                                                    class="text-[10px] font-mono text-slate-400">{{ $esp->codigo }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span
                                            class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide border {{ $esp->grupo_color }}">
                                            {{ $esp->grupo_label }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        @if ($esp->pivot->numero_cedula_especialidad)
                                            <span
                                                class="px-2 py-1 bg-slate-100 text-slate-600 rounded-lg text-[11px] font-mono font-bold">
                                                {{ $esp->pivot->numero_cedula_especialidad }}
                                            </span>
                                        @else
                                            <span class="text-slate-300 text-xs">—</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 text-center">
                                        @if ($esp->pivot->es_principal)
                                            <span
                                                class="inline-flex items-center gap-1 px-2.5 py-1 bg-amber-50 text-amber-700 border border-amber-200 rounded-full text-[11px] font-bold uppercase tracking-wide">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                                Principal
                                            </span>
                                        @else
                                            <form
                                                action="{{ route('admin.users.especialidades.principal', [$user, $esp->pivot->id]) }}"
                                                method="POST" class="inline">
                                                @csrf
                                                <button
                                                    class="px-3 py-1.5 bg-white hover:bg-amber-50 text-amber-700 border border-amber-200 rounded-lg text-[11px] font-bold transition-all active:scale-95">
                                                    Marcar principal
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <div class="flex justify-end">
                                            <form
                                                action="{{ route('admin.users.especialidades.quitar', [$user, $esp->pivot->id]) }}"
                                                method="POST"
                                                onsubmit="return confirm('¿Quitar la especialidad {{ $esp->nombre }} de {{ $user->nombre_completo }}?')">
                                                @csrf @method('DELETE')
                                                <button
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-[11px] font-bold transition-all active:scale-95">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    Quitar
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- ============ NOTA INFORMATIVA ============ --}}
        @if ($total > 0)
            <div class="bg-sky-50/70 border border-sky-100 rounded-2xl p-5">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="text-xs text-sky-900 leading-relaxed">
                        <p class="font-bold mb-1">Sobre la especialidad principal</p>
                        <p class="text-sky-800/90">
                            La especialidad marcada como <span class="font-semibold">principal</span> se usará
                            por defecto en la agenda médica y en los registros de consulta. Solo puede haber una
                            principal por colaborador.
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
