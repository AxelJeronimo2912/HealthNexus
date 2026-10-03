<div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
    <h3 class="text-lg font-bold text-slate-800">Historial de dispensaciones</h3>
    <button type="button" onclick="cerrarModalHistorial()"
        class="text-slate-400 hover:text-slate-600 text-2xl leading-none">&times;</button>
</div>

<div class="px-6 py-3 border-b border-slate-100 bg-slate-50">
    <form method="GET" onsubmit="event.preventDefault(); buscarHistorial(this);">
        <input type="text" name="buscar" value="{{ $busqueda }}" placeholder="Buscar por paciente o CURP..."
            class="w-full border-slate-200 rounded-lg shadow-sm text-sm focus:border-purple-500 focus:ring-purple-500">
    </form>
</div>

<div class="max-h-[60vh] overflow-y-auto">
    @if ($historial->isEmpty())
        <div class="p-10 text-center text-slate-400 text-sm">
            No hay dispensaciones registradas.
        </div>
    @else
        <table class="min-w-full divide-y divide-slate-100">
            <thead class="bg-slate-50 sticky top-0">
                <tr>
                    <th class="px-4 py-2.5 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                        Fecha</th>
                    <th class="px-4 py-2.5 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                        Paciente</th>
                    <th class="px-4 py-2.5 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                        Médico</th>
                    <th class="px-4 py-2.5 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Meds
                    </th>
                    <th class="px-4 py-2.5 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                        Dispensó</th>
                    <th class="px-4 py-2.5"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                @foreach ($historial as $item)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="px-4 py-2.5 text-xs text-slate-500 whitespace-nowrap">
                            {{ $item->dispensada_en?->format('d/m/Y H:i') ?? '—' }}
                        </td>
                        <td class="px-4 py-2.5 text-sm">
                            <div class="font-semibold text-slate-800">{{ $item->paciente?->nombre_completo ?? '—' }}
                            </div>
                            <div class="text-xs text-slate-400">{{ $item->paciente?->curp ?? '—' }}</div>
                        </td>
                        <td class="px-4 py-2.5 text-xs text-slate-600">
                            {{ $item->medico?->nombre_completo ?? '—' }}
                        </td>
                        <td class="px-4 py-2.5 text-xs">
                            <span class="px-2 py-0.5 bg-blue-50 text-blue-700 rounded-full font-semibold">
                                {{ $item->medicamentos->count() }}
                            </span>
                        </td>
                        <td class="px-4 py-2.5 text-xs text-slate-600">
                            {{ $item->dispensadaPor?->name ?? '—' }}
                        </td>
                        <td class="px-4 py-2.5 text-right">
                            <a href="{{ route('dispensaciones.show', $item) }}"
                                class="text-xs text-purple-600 hover:text-purple-800 font-bold transition-colors">
                                Ver
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

<div class="px-6 py-3 border-t border-slate-100 bg-slate-50 flex justify-between items-center">
    <span class="text-xs text-slate-500">{{ $historial->count() }} resultado(s)</span>
    <a href="{{ route('dispensaciones.index', ['filtro' => 'dispensadas']) }}"
        class="text-xs text-purple-600 hover:text-purple-800 font-bold transition-colors">
        Ver historial completo →
    </a>
</div>

<script>
    function buscarHistorial(form) {
        const q = form.buscar.value;
        fetch(`{{ route('dispensaciones.historial') }}?buscar=${encodeURIComponent(q)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(r => r.text())
            .then(html => {
                document.getElementById('modal-historial-content').innerHTML = html;
            });
    }
</script>
