<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight">Expedientes</h2>
                <p class="text-xs text-slate-400 mt-0.5">Historial clínico y consultas por paciente</p>
            </div>

            <a href="{{ route('pacientes.index') }}"
                class="bg-white hover:bg-indigo-50 text-slate-700 hover:text-indigo-600 border border-slate-200 hover:border-indigo-200 px-5 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 transition-all active:scale-95 group">
                Ver todos los pacientes
                <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Barra de búsqueda + contador --}}
        <div
            class="bg-white border border-slate-100 p-4 rounded-3xl shadow-sm flex flex-col md:flex-row md:items-center gap-4">

            <div class="flex items-center gap-3 md:pr-4 md:border-r border-slate-100 shrink-0">
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        {{ $busqueda ? 'RESULTADOS' : 'EXPEDIENTES' }}
                    </p>
                    <p class="text-2xl font-extrabold text-indigo-600 mt-0.5">{{ $pacientes->total() }}</p>
                </div>
            </div>

            <form method="GET" action="{{ route('expedientes.index') }}"
                class="flex flex-1 flex-col sm:flex-row gap-2">
                <div class="relative flex-1">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z" />
                    </svg>
                    <input type="text" name="buscar" value="{{ $busqueda }}"
                        placeholder="Buscar por nombre, apellidos o CURP"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-10 pr-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-indigo-500 focus:ring-0 outline-none">
                </div>

                <button type="submit"
                    class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all">
                    Buscar
                </button>

                @if ($busqueda)
                    <a href="{{ route('expedientes.index') }}"
                        class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold text-center transition-all">
                        Limpiar
                    </a>
                @endif
            </form>
        </div>

        {{-- Tabla (escritorio) --}}
        <div class="hidden md:block bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            <table class="min-w-full">
                <thead class="bg-slate-50/70 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                            Paciente</th>
                        <th class="px-5 py-3.5 text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                            CURP</th>
                        <th class="px-5 py-3.5 text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                            Edad</th>
                        <th class="px-5 py-3.5 text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                            Último triage</th>
                        <th class="px-5 py-3.5 text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                            Consultas</th>
                        <th
                            class="px-5 py-3.5 text-right text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                            Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($pacientes as $paciente)
                        <tr class="hover:bg-indigo-50/30 transition-colors">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-9 h-9 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-[11px] font-bold shrink-0">
                                        {{ strtoupper(mb_substr($paciente->nombre_completo ?? 'P', 0, 2)) }}
                                    </div>
                                    <p class="text-xs font-bold text-slate-800">{{ $paciente->nombre_completo }}</p>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-xs font-medium text-slate-500">
                                {{ $paciente->curp ?? '—' }}
                            </td>
                            <td class="px-5 py-3.5 text-xs font-semibold text-slate-700">
                                {{ $paciente->edad }} años
                            </td>
                            <td class="px-5 py-3.5">
                                @if ($paciente->ultimoSignoVital)
                                    <span
                                        class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide {{ $paciente->ultimoSignoVital->triage_color }}">
                                        {{ $paciente->ultimoSignoVital->triage_label }}
                                    </span>
                                @else
                                    <span class="text-slate-400 text-[11px] font-semibold">Sin signos vitales</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <span
                                    class="inline-flex items-center justify-center min-w-[1.75rem] px-2 py-1 bg-indigo-50 text-indigo-700 border border-indigo-100 rounded-full text-[11px] font-bold">
                                    {{ $paciente->consultas_count }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('expedientes.show', $paciente) }}"
                                        class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-[11px] font-bold shadow-sm transition-all active:scale-95">
                                        Ver expediente
                                    </a>
                                    <a href="{{ route('pacientes.show', $paciente) }}"
                                        class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-[11px] font-bold transition-all active:scale-95">
                                        Ficha
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-14 text-center">
                                <p class="text-xs font-semibold text-slate-400">Sin pacientes con expediente.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Tarjetas (móvil) --}}
        <div class="md:hidden space-y-3">
            @forelse ($pacientes as $paciente)
                <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-4 space-y-3">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-11 h-11 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-bold shrink-0">
                            {{ strtoupper(mb_substr($paciente->nombre_completo ?? 'P', 0, 2)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-extrabold text-slate-800 truncate">{{ $paciente->nombre_completo }}
                            </p>
                            <p class="text-[11px] font-medium text-slate-400 truncate">
                                {{ $paciente->curp ?? '—' }} · {{ $paciente->edad }} años
                            </p>
                        </div>
                    </div>

                    <div
                        class="flex items-center justify-between bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Triage</p>
                            @if ($paciente->ultimoSignoVital)
                                <span
                                    class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide {{ $paciente->ultimoSignoVital->triage_color }}">
                                    {{ $paciente->ultimoSignoVital->triage_label }}
                                </span>
                            @else
                                <span class="text-slate-400 text-[11px] font-semibold">Sin signos vitales</span>
                            @endif
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Consultas</p>
                            <span
                                class="px-2.5 py-1 bg-indigo-50 text-indigo-700 border border-indigo-100 rounded-full text-[11px] font-bold">
                                {{ $paciente->consultas_count }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <a href="{{ route('expedientes.show', $paciente) }}"
                            class="py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold text-center shadow-sm">
                            Ver expediente
                        </a>
                        <a href="{{ route('pacientes.show', $paciente) }}"
                            class="py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold text-center">
                            Ficha
                        </a>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-10 text-center">
                    <p class="text-xs font-semibold text-slate-400">Sin pacientes con expediente.</p>
                </div>
            @endforelse
        </div>

        <div>{{ $pacientes->links() }}</div>
    </div>
</x-app-layout>
