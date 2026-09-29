<div class="p-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-bold text-slate-800">Historial de Citas</h3>
        <button type="button" onclick="cerrarModalHistorial()"
            class="text-slate-400 hover:text-slate-700 text-2xl leading-none">&times;</button>
    </div>

    @if ($historial->isEmpty())
        <p class="text-slate-400 text-sm text-center py-8">No hay citas en el historial.</p>
    @else
        <div class="overflow-x-auto max-h-[60vh] overflow-y-auto rounded-xl border border-slate-100">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 sticky top-0">
                    <tr>
                        <th class="px-4 py-3 text-left text-[11px] font-bold text-slate-400 uppercase">Fecha</th>
                        <th class="px-4 py-3 text-left text-[11px] font-bold text-slate-400 uppercase">Paciente</th>
                        <th class="px-4 py-3 text-left text-[11px] font-bold text-slate-400 uppercase">Médico</th>
                        <th class="px-4 py-3 text-left text-[11px] font-bold text-slate-400 uppercase">Estado</th>
                        <th class="px-4 py-3 text-right text-[11px] font-bold text-slate-400 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($historial as $cita)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-4 py-3 font-semibold text-slate-700">
                                {{ $cita->fecha_hora->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-4 py-3 text-slate-700">
                                {{ $cita->paciente->nombre_completo ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-slate-500">
                                {{ $cita->medico->nombre_completo ?? '—' }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $cita->estado_color }}">
                                    {{ $cita->estado_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('citas.show', $cita) }}"
                                    class="text-purple-600 hover:text-purple-800 font-bold text-xs">
                                    Ver detalle
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $historial->links() }}
        </div>
    @endif
</div>
