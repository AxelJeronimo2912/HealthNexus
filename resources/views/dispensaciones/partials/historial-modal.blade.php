<div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-2xl bg-purple-50 flex items-center justify-center text-purple-600 shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div>
            <h3 class="font-extrabold text-slate-800 text-base">Historial de dispensaciones</h3>
            <p class="text-[11px] text-slate-400">Registro completo de recetas dispensadas</p>
        </div>
    </div>
    <button type="button" onclick="cerrarModalHistorial()"
        class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-700 flex items-center justify-center transition-all active:scale-95 leading-none">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>
</div>

<div class="px-6 py-4 border-b border-slate-100 bg-slate-50/70">
    <form method="GET" onsubmit="event.preventDefault(); buscarHistorial(this);">
        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Buscar</label>
        <div class="relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </span>
            <input type="text" name="buscar" value="{{ $busqueda }}" placeholder="Buscar por paciente o CURP..."
                class="w-full bg-white border border-slate-200 rounded-xl pl-9 pr-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-purple-500 focus:ring-0 outline-none">
        </div>
    </form>
</div>

<div class="max-h-[60vh] overflow-y-auto">
    @if ($historial->isEmpty())
        <div class="px-5 py-12 text-center">
            <p class="text-xs font-semibold text-slate-400">No hay dispensaciones registradas.</p>
        </div>
    @else
        <table class="min-w-full">
            <thead class="bg-slate-50/70 border-b border-slate-100 sticky top-0 backdrop-blur-sm">
                <tr>
                    <th class="px-5 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider text-left">
                        Fecha</th>
                    <th class="px-5 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider text-left">
                        Paciente</th>
                    <th class="px-5 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider text-left">
                        Médico</th>
                    <th class="px-5 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider text-left">
                        Meds</th>
                    <th class="px-5 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider text-left">
                        Dispensó</th>
                    <th class="px-5 py-3.5"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                @foreach ($historial as $item)
                    <tr class="hover:bg-purple-50/30 transition-colors">
                        <td class="px-5 py-3.5 text-[11px] font-medium text-slate-400 whitespace-nowrap">
                            {{ $item->dispensada_en?->format('d/m/Y H:i') ?? '—' }}
                        </td>
                        <td class="px-5 py-3.5">
                            <p class="text-xs font-bold text-slate-800">{{ $item->paciente?->nombre_completo ?? '—' }}
                            </p>
                            @if ($item->paciente?->curp)
                                <p class="text-[10px] font-medium text-slate-400 mt-0.5">{{ $item->paciente->curp }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            <p class="text-xs font-medium text-slate-600">{{ $item->medico?->nombre_completo ?? '—' }}
                            </p>
                        </td>
                        <td class="px-5 py-3.5">
                            <span
                                class="px-2.5 py-1 bg-purple-50 text-purple-700 border border-purple-100 rounded-full text-[10px] font-bold">
                                {{ $item->medicamentos->count() }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5">
                            <p class="text-xs font-medium text-slate-600">{{ $item->dispensadaPor?->name ?? '—' }}</p>
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex justify-end">
                                <a href="{{ route('dispensaciones.show', $item) }}"
                                    class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg text-[11px] font-bold transition-all">
                                    Ver
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

<div class="px-6 py-4 border-t border-slate-100 bg-slate-50/70 flex justify-between items-center">
    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">
        {{ $historial->count() }} resultado(s)
    </span>
    <a href="{{ route('dispensaciones.index', ['filtro' => 'dispensadas']) }}"
        class="text-xs font-bold text-purple-600 hover:text-purple-800 transition-colors">
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
