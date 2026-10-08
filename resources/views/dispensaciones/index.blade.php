<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-3">
            <div class="min-w-0">
                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight truncate">
                    Dispensación de Recetas
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Cola de recetas pendientes y dispensadas</p>
            </div>

            <button type="button" onclick="abrirModalHistorial()"
                class="bg-white hover:bg-indigo-50 text-slate-700 hover:text-indigo-600 border border-slate-200 hover:border-indigo-200 px-5 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 transition-all active:scale-95 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Historial
            </button>
        </div>
    </x-slot>

    @php
        $inputCls =
            'w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-indigo-500 focus:ring-0 outline-none';
        $labelCls = 'block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5';
        $thCls = 'px-5 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider';
    @endphp

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- ============ STATS ============ --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('dispensaciones.index', ['filtro' => 'pendientes']) }}"
                class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm hover:shadow-md hover:border-amber-200 transition-all active:scale-95 group">
                <div class="flex items-center justify-between">
                    <p class="text-[11px] font-bold text-amber-600/70 uppercase tracking-wider">Pendientes</p>
                    <svg class="w-4 h-4 text-slate-300 group-hover:text-amber-500 transition-colors" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
                <p class="text-3xl font-black text-amber-600 tracking-tight mt-1">{{ $stats['pendientes'] }}</p>
            </a>
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm">
                <p class="text-[11px] font-bold text-emerald-600/70 uppercase tracking-wider">Dispensadas hoy</p>
                <p class="text-3xl font-black text-emerald-600 tracking-tight mt-1">{{ $stats['dispensadas_hoy'] }}</p>
            </div>
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm">
                <p class="text-[11px] font-bold text-indigo-600/70 uppercase tracking-wider">Dispensadas este mes</p>
                <p class="text-3xl font-black text-indigo-600 tracking-tight mt-1">{{ $stats['dispensadas_mes'] }}</p>
            </div>
        </div>

        {{-- ============ FILTROS ============ --}}
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-5">
            <form method="GET" class="flex gap-2 flex-wrap items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="{{ $labelCls }}">Buscar</label>
                    <input type="text" name="buscar" value="{{ $busqueda }}" placeholder="Nombre del paciente"
                        class="{{ $inputCls }}">
                </div>
                <div class="min-w-[180px]">
                    <label class="{{ $labelCls }}">Filtro</label>
                    <select name="filtro" class="{{ $inputCls }}">
                        <option value="pendientes" @selected($filtro === 'pendientes')>Pendientes</option>
                        <option value="dispensadas" @selected($filtro === 'dispensadas')>Dispensadas</option>
                        <option value="todas" @selected($filtro === 'todas')>Todas</option>
                    </select>
                </div>
                <button type="submit"
                    class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all">
                    Filtrar
                </button>
            </form>
        </div>

        {{-- ============ TABLA ============ --}}
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="font-extrabold text-slate-800 text-base">Recetas</h3>
                <p class="text-[11px] text-slate-400">{{ $recetas->total() }} recetas en total</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-slate-50/70 border-b border-slate-100">
                        <tr>
                            <th class="{{ $thCls }} text-left">Fecha</th>
                            <th class="{{ $thCls }} text-left">Paciente</th>
                            <th class="{{ $thCls }} text-left">Médico</th>
                            <th class="{{ $thCls }} text-left">Medicamentos</th>
                            <th class="{{ $thCls }} text-left">Estado</th>
                            <th class="px-5 py-3.5"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($recetas as $receta)
                            <tr class="hover:bg-indigo-50/30 transition-colors">
                                <td class="px-5 py-3.5 text-[11px] font-medium text-slate-400 whitespace-nowrap">
                                    {{ $receta->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <p class="text-xs font-bold text-slate-800">
                                        {{ $receta->paciente?->nombre_completo ?? '—' }}</p>
                                    @if ($receta->paciente?->curp)
                                        <p class="text-[10px] font-medium text-slate-400 mt-0.5">CURP:
                                            {{ $receta->paciente->curp }}</p>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    <p class="text-xs font-medium text-slate-600">
                                        {{ $receta->medico?->nombre_completo ?? '—' }}</p>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex flex-wrap gap-1.5">
                                        <span
                                            class="px-2.5 py-1 bg-indigo-50 text-indigo-700 border border-indigo-100 rounded-full text-[10px] font-bold uppercase tracking-wide">
                                            {{ $receta->medicamentos->count() }} meds
                                        </span>
                                        @if ($receta->receta_libre)
                                            <span
                                                class="px-2.5 py-1 bg-purple-50 text-purple-700 border border-purple-100 rounded-full text-[10px] font-bold uppercase tracking-wide">
                                                Libre
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-3.5">
                                    @if ($receta->dispensada)
                                        <span
                                            class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-full text-[10px] font-bold uppercase tracking-wide">
                                            Dispensada
                                        </span>
                                        <p class="text-[10px] font-medium text-slate-400 mt-1">
                                            {{ $receta->dispensada_en?->format('d/m/Y H:i') }}
                                        </p>
                                    @else
                                        <span
                                            class="px-2.5 py-1 bg-amber-50 text-amber-700 border border-amber-100 rounded-full text-[10px] font-bold uppercase tracking-wide">
                                            Pendiente
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex justify-end">
                                        <a href="{{ route('dispensaciones.show', $receta) }}"
                                            class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg text-[11px] font-bold transition-all">
                                            Ver receta
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center">
                                    <p class="text-xs font-semibold text-slate-400">No se encontraron recetas para
                                        mostrar.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">{{ $recetas->links() }}</div>
    </div>

    {{-- Contenedor del modal de historial --}}
    <div id="modal-historial"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-3xl shadow-2xl border border-slate-100 w-full max-w-4xl overflow-hidden">
            <div id="modal-historial-content">
                <div class="p-10 text-center">
                    <div class="inline-flex items-center gap-2 text-slate-400 text-xs font-semibold">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                            </path>
                        </svg>
                        Cargando historial...
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function abrirModalHistorial() {
            const modal = document.getElementById('modal-historial');
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            fetch('{{ route('dispensaciones.historial') }}', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(r => r.text())
                .then(html => {
                    document.getElementById('modal-historial-content').innerHTML = html;
                })
                .catch(() => {
                    document.getElementById('modal-historial-content').innerHTML =
                        '<div class="p-6 text-rose-600 text-xs font-semibold">Error al cargar el historial.</div>';
                });
        }

        function cerrarModalHistorial() {
            const modal = document.getElementById('modal-historial');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        document.getElementById('modal-historial').addEventListener('click', function(e) {
            if (e.target === this) cerrarModalHistorial();
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') cerrarModalHistorial();
        });
    </script>
</x-app-layout>
