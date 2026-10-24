@if ($historial->isEmpty())
    <div
        class="flex flex-col items-center justify-center py-12 text-center bg-slate-50/50 rounded-2xl border border-slate-100">
        <div
            class="w-16 h-16 bg-white rounded-full flex items-center justify-center mb-3 ring-1 ring-slate-200/70 shadow-card">
            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
        </div>
        <p class="text-slate-600 font-bold text-sm">No hay citas en el historial.</p>
        <p class="text-slate-400 text-xs mt-1">Prueba cambiando los filtros de búsqueda.</p>
    </div>
@else
    <div
        class="overflow-x-auto overflow-y-auto max-h-[55vh] rounded-2xl border border-slate-200 shadow-sm custom-scrollbar bg-white">
        <table class="min-w-full divide-y divide-slate-100 text-sm relative">
            <thead class="bg-slate-50 sticky top-0 z-10 shadow-sm">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Fecha
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                        Paciente</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Médico
                    </th>
                    <th class="px-4 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">
                        Estado</th>
                    <th class="px-4 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">
                        Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                @foreach ($historial as $cita)
                    <tr class="hover:bg-slate-50/80 transition-colors" id="fila-cita-{{ $cita->id }}">
                        <td class="px-4 py-3.5 font-semibold text-slate-700 whitespace-nowrap">
                            {{ $cita->fecha_hora->format('d/m/Y') }}
                            <span
                                class="block text-xs text-slate-400 font-normal">{{ $cita->fecha_hora->format('H:i') }}
                                hrs</span>
                        </td>
                        <td class="px-4 py-3.5">
                            <p class="font-bold text-slate-800">{{ $cita->paciente->nombre_completo ?? '—' }}</p>
                        </td>
                        <td class="px-4 py-3.5 text-slate-600 font-medium">
                            {{ $cita->medico->nombre_completo ?? '—' }}
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            <select onchange="cambiarEstadoCita('{{ $cita->id }}', this)"
                                class="text-xs font-bold rounded-full px-3 py-1.5 border-0 cursor-pointer focus:ring-2 focus:ring-offset-1 transition-all
                                @if ($cita->estado === 'confirmada') bg-emerald-50 text-emerald-700 focus:ring-emerald-500
                                @elseif($cita->estado === 'cancelada') bg-rose-50 text-rose-700 focus:ring-rose-500
                                @elseif($cita->estado === 'atendida') bg-brand-50 text-brand-700 focus:ring-brand-500
                                @elseif($cita->estado === 'no_asistio') bg-amber-50 text-amber-700 focus:ring-amber-500
                                @else bg-slate-100 text-slate-700 focus:ring-slate-500 @endif">
                                <option value="confirmada" @selected($cita->estado === 'confirmada')>Confirmada</option>
                                <option value="atendida" @selected($cita->estado === 'atendida')>Atendida</option>
                                <option value="cancelada" @selected($cita->estado === 'cancelada')>Cancelada</option>
                                <option value="no_asistio" @selected($cita->estado === 'no_asistio')>No asistió</option>
                            </select>
                        </td>
                        <td class="px-4 py-3.5 text-right">
                            <a href="{{ route('citas.show', $cita) }}" target="_blank"
                                class="inline-flex items-center gap-1 text-brand-600 hover:text-brand-800 font-bold text-xs bg-brand-50 hover:bg-brand-100 px-3 py-1.5 rounded-lg transition-colors border border-brand-100">
                                Ver detalle
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                    </path>
                                </svg>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4" id="paginacion-historial">
        {{ $historial->links() }}
    </div>
@endif
