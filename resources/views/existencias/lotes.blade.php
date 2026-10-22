<div class="p-2">
    {{-- Encabezado con Botón Cerrar --}}
    <div class="flex justify-between items-center mb-5 border-b border-slate-100 pb-4">
        <div>
            <h3 class="text-lg font-bold text-slate-800">Lotes Registrados</h3>
            <p class="text-xs text-slate-500 mt-0.5">Trazabilidad y caducidad por lote de medicamento</p>
        </div>
        <button type="button" onclick="Alpine.store('inventario').openLotes = false"
            class="w-9 h-9 flex items-center justify-center rounded-full bg-slate-50 text-slate-400 hover:text-rose-500 hover:bg-rose-50 transition-colors text-xl leading-none border border-slate-200">&times;</button>
    </div>

    {{-- Filtros (Ahora usan la clase btn-filtro-lote y data-url) --}}
    <div class="flex flex-wrap gap-2 mb-5">
        <button type="button" data-url="{{ route('existencias.lotes') }}"
            class="btn-filtro-lote px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ !$filtro ? 'bg-brand-600 text-white shadow-sm' : 'bg-slate-50 border border-slate-200 text-slate-600 hover:bg-slate-100' }}">
            Todos
        </button>
        <button type="button" data-url="{{ route('existencias.lotes', ['filtro' => 'vigentes']) }}"
            class="btn-filtro-lote px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ $filtro === 'vigentes' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-slate-50 border border-slate-200 text-slate-600 hover:bg-emerald-50 hover:text-emerald-700' }}">
            Vigentes
        </button>
        <button type="button" data-url="{{ route('existencias.lotes', ['filtro' => 'proximos']) }}"
            class="btn-filtro-lote px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ $filtro === 'proximos' ? 'bg-amber-500 text-white shadow-sm' : 'bg-slate-50 border border-slate-200 text-slate-600 hover:bg-amber-50 hover:text-amber-700' }}">
            Próximos a caducar (30d)
        </button>
        <button type="button" data-url="{{ route('existencias.lotes', ['filtro' => 'caducados']) }}"
            class="btn-filtro-lote px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ $filtro === 'caducados' ? 'bg-rose-600 text-white shadow-sm' : 'bg-slate-50 border border-slate-200 text-slate-600 hover:bg-rose-50 hover:text-rose-700' }}">
            Caducados
        </button>
    </div>

    {{-- Tabla --}}
    <div
        class="overflow-x-auto max-h-[60vh] overflow-y-auto rounded-2xl border border-slate-200 shadow-sm custom-scrollbar">
        <table class="min-w-full">
            <thead class="bg-slate-50 sticky top-0 z-10 shadow-sm">
                <tr>
                    <th class="px-5 py-3.5 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Lote
                    </th>
                    <th class="px-5 py-3.5 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">
                        Medicamento</th>
                    <th class="px-5 py-3.5 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">
                        Caducidad</th>
                    <th class="px-5 py-3.5 text-right text-xs font-bold text-slate-400 uppercase tracking-wider">
                        Disponible</th>
                    <th class="px-5 py-3.5 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">
                        Estado</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                @forelse ($lotes as $lote)
                    <tr class="hover:bg-brand-50/30 transition-colors">
                        <td class="px-5 py-3.5">
                            <span
                                class="px-2 py-1 bg-slate-100 text-slate-600 rounded-lg text-xs font-mono font-bold">
                                {{ $lote->codigo_lote ?? 'LOTE-' . $lote->id }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5">
                            <p class="text-xs font-bold text-slate-800">{{ $lote->medicamento?->nombre ?? '—' }}</p>
                            @if ($lote->medicamento?->concentracion)
                                <p class="text-xs font-medium text-slate-400 mt-0.5">
                                    {{ $lote->medicamento->concentracion }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            <p class="text-xs font-semibold text-slate-700">
                                {{ $lote->fecha_caducidad->format('d/m/Y') }}</p>
                            <p class="text-xs font-medium text-slate-400 mt-0.5">
                                @if ($lote->esta_caducado)
                                    Caducó hace {{ abs($lote->dias_para_caducar) }} días
                                @else
                                    Vence en {{ $lote->dias_para_caducar }} días
                                @endif
                            </p>
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <span
                                class="px-2 py-1 bg-slate-100 text-slate-700 rounded-lg text-xs font-mono font-bold">
                                {{ $lote->cantidad_disponible }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5">
                            <span
                                class="px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wide border {{ $lote->estado_color }}">
                                {{ $lote->estado_label }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center">
                            <p class="text-xs font-semibold text-slate-400">Sin lotes para mostrar.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
