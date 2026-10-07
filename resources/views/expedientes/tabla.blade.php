<div data-total-pacientes="{{ $pacientes->total() }}">

    <h4 class="text-sm font-bold tracking-wider text-slate-500 uppercase mb-4">Pacientes</h4>

    <div class="bg-white rounded-3xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="px-6 py-4 text-left text-xs font-bold tracking-wider text-slate-400 uppercase">
                            Paciente</th>
                        <th class="px-6 py-4 text-left text-xs font-bold tracking-wider text-slate-400 uppercase">
                            CURP</th>
                        <th class="px-6 py-4 text-left text-xs font-bold tracking-wider text-slate-400 uppercase">
                            Edad</th>
                        <th class="px-6 py-4 text-left text-xs font-bold tracking-wider text-slate-400 uppercase">
                            Último triage</th>
                        <th class="px-6 py-4 text-left text-xs font-bold tracking-wider text-slate-400 uppercase">
                            Consultas</th>
                        <th class="px-6 py-4 text-right text-xs font-bold tracking-wider text-slate-400 uppercase">
                            Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($pacientes as $paciente)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 shrink-0 rounded-xl bg-indigo-50 text-indigo-600 font-bold flex items-center justify-center">
                                        {{ strtoupper(mb_substr($paciente->nombre_completo, 0, 1)) }}
                                    </div>
                                    <span class="font-semibold text-slate-800">
                                        {{ $paciente->nombre_completo }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-500 font-mono text-xs">
                                {{ $paciente->curp ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-slate-600">{{ $paciente->edad }} años</td>
                            <td class="px-6 py-4">
                                @if ($paciente->ultimoSignoVital)
                                    <span
                                        class="px-3 py-1 rounded-full text-xs font-bold {{ $paciente->ultimoSignoVital->triage_color }}">
                                        {{ $paciente->ultimoSignoVital->triage_label }}
                                    </span>
                                @else
                                    <span class="text-slate-400 text-xs">Sin signos vitales</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center justify-center min-w-[2rem] px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 text-xs font-bold">
                                    {{ $paciente->consultas_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('expedientes.show', $paciente) }}"
                                        class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-sm transition">
                                        Ver expediente
                                    </a>
                                    <a href="{{ route('pacientes.show', $paciente) }}"
                                        class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold transition">
                                        Ficha
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-14 text-center">
                                <div
                                    class="mx-auto w-14 h-14 rounded-2xl bg-slate-50 flex items-center justify-center mb-3">
                                    <svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor"
                                        stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z" />
                                    </svg>
                                </div>
                                <p class="text-slate-500 font-medium">Sin pacientes con expediente.</p>
                                @if (!empty($busqueda))
                                    <p class="text-slate-400 text-sm mt-1">Prueba con otro nombre o CURP.</p>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">{{ $pacientes->links() }}</div>
</div>
