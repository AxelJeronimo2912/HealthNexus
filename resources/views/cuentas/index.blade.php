<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight">Cuentas por cobrar</h2>
                <p class="text-xs text-slate-400 mt-0.5">Control de cargos y pagos de pacientes</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        @if (session('success'))
            <div
                class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-2xl text-xs font-semibold flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div
                class="p-4 bg-rose-50 border border-rose-100 text-rose-800 rounded-2xl text-xs font-semibold flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- KPIs --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-600 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">ABIERTAS</p>
                    <p class="text-2xl font-extrabold text-slate-800 mt-0.5">{{ $stats['abiertas'] }}</p>
                </div>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-rose-50 flex items-center justify-center text-rose-500 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">POR COBRAR</p>
                    <p class="text-xl font-extrabold text-rose-600 mt-0.5 truncate">
                        ${{ number_format($stats['por_cobrar'], 2) }}</p>
                </div>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4">
                <div
                    class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">COBRADO HOY</p>
                    <p class="text-xl font-extrabold text-emerald-600 mt-0.5 truncate">
                        ${{ number_format($stats['cobrado_hoy'], 2) }}</p>
                </div>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4">
                <div
                    class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">CERRADAS (MES)</p>
                    <p class="text-2xl font-extrabold text-indigo-600 mt-0.5">{{ $stats['cerradas_mes'] }}</p>
                </div>
            </div>
        </div>

        {{-- Filtros --}}
        <form method="GET"
            class="bg-white border border-slate-100 p-4 rounded-3xl shadow-sm flex flex-col lg:flex-row gap-3">

            <div class="relative flex-1 min-w-[200px]">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z" />
                </svg>
                <input type="text" name="buscar" value="{{ $buscar }}"
                    placeholder="Buscar por folio, nombre o CURP"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-10 pr-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-indigo-500 focus:ring-0 outline-none">
            </div>

            <select name="estado"
                class="bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-indigo-500 focus:ring-0 outline-none">
                <option value="todas" @selected($filtroEstado === 'todas')>Todas</option>
                <option value="abierta" @selected($filtroEstado === 'abierta')>Abiertas</option>
                <option value="cerrada" @selected($filtroEstado === 'cerrada')>Cerradas</option>
                <option value="cancelada" @selected($filtroEstado === 'cancelada')>Canceladas</option>
            </select>

            <label
                class="inline-flex items-center gap-2 px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer hover:bg-white transition-all">
                <input type="checkbox" name="con_saldo" value="1" @checked($soloConSaldo)
                    class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                <span class="text-xs font-semibold text-slate-700">Solo con saldo</span>
            </label>

            <div class="flex gap-2">
                <button type="submit"
                    class="flex-1 lg:flex-none px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all">
                    Filtrar
                </button>

                @if ($buscar || $filtroEstado || $soloConSaldo)
                    <a href="{{ route('cuentas.index') }}"
                        class="flex-1 lg:flex-none px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold text-center transition-all">
                        Limpiar
                    </a>
                @endif
            </div>
        </form>

        {{-- Tabla (escritorio) --}}
        <div class="hidden md:block bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-slate-50/70 border-b border-slate-100">
                        <tr>
                            <th
                                class="px-5 py-3.5 text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                Folio</th>
                            <th
                                class="px-5 py-3.5 text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                Paciente</th>
                            <th
                                class="px-5 py-3.5 text-center text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                Items</th>
                            <th
                                class="px-5 py-3.5 text-right text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                Total</th>
                            <th
                                class="px-5 py-3.5 text-right text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                Pagado</th>
                            <th
                                class="px-5 py-3.5 text-right text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                Saldo</th>
                            <th
                                class="px-5 py-3.5 text-center text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                Estado</th>
                            <th
                                class="px-5 py-3.5 text-right text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($cuentas as $cuenta)
                            <tr class="hover:bg-indigo-50/30 transition-colors">
                                <td class="px-5 py-3.5">
                                    <span
                                        class="px-2 py-1 bg-slate-100 text-slate-600 rounded-lg text-[11px] font-mono font-bold">
                                        {{ $cuenta->folio }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-9 h-9 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-[11px] font-bold shrink-0">
                                            {{ strtoupper(mb_substr($cuenta->paciente?->nombre_completo ?? 'P', 0, 2)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-slate-800 truncate">
                                                {{ $cuenta->paciente?->nombre_completo ?? '—' }}
                                            </p>
                                            <p class="text-[10px] font-medium text-slate-400">ID:
                                                #{{ $cuenta->paciente_id }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <span
                                        class="inline-flex items-center justify-center min-w-[1.75rem] px-2 py-1 bg-slate-100 text-slate-700 rounded-full text-[11px] font-bold">
                                        {{ $cuenta->items->count() }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-right text-xs font-bold text-slate-800">
                                    ${{ number_format((float) $cuenta->total, 2) }}
                                </td>
                                <td class="px-5 py-3.5 text-right text-xs font-semibold text-emerald-600">
                                    ${{ number_format((float) $cuenta->pagado, 2) }}
                                </td>
                                <td
                                    class="px-5 py-3.5 text-right text-xs font-extrabold {{ (float) $cuenta->saldo > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                    ${{ number_format((float) $cuenta->saldo, 2) }}
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <span
                                        class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide {{ $cuenta->estado_color }}">
                                        {{ ucfirst($cuenta->estado) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <a href="{{ route('cuentas.paciente', $cuenta->paciente) }}"
                                        class="inline-block px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-[11px] font-bold shadow-sm transition-all active:scale-95">
                                        Ver detalle
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-14 text-center">
                                    <p class="text-xs font-semibold text-slate-400">
                                        No hay cuentas que coincidan con el filtro.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Tarjetas (móvil) --}}
        <div class="md:hidden space-y-3">
            @forelse ($cuentas as $cuenta)
                <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-4 space-y-3">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div
                                class="w-11 h-11 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-bold shrink-0">
                                {{ strtoupper(mb_substr($cuenta->paciente?->nombre_completo ?? 'P', 0, 2)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-extrabold text-slate-800 truncate">
                                    {{ $cuenta->paciente?->nombre_completo ?? '—' }}
                                </p>
                                <p class="text-[11px] font-mono font-medium text-slate-400">{{ $cuenta->folio }}</p>
                            </div>
                        </div>
                        <span
                            class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide shrink-0 {{ $cuenta->estado_color }}">
                            {{ ucfirst($cuenta->estado) }}
                        </span>
                    </div>

                    <div
                        class="grid grid-cols-3 gap-2 bg-slate-50/70 border border-slate-100 rounded-2xl p-3 text-center">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total</p>
                            <p class="text-xs font-extrabold text-slate-800 mt-0.5">
                                ${{ number_format((float) $cuenta->total, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Pagado</p>
                            <p class="text-xs font-extrabold text-emerald-600 mt-0.5">
                                ${{ number_format((float) $cuenta->pagado, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Saldo</p>
                            <p
                                class="text-xs font-extrabold mt-0.5 {{ (float) $cuenta->saldo > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                ${{ number_format((float) $cuenta->saldo, 2) }}</p>
                        </div>
                    </div>

                    <a href="{{ route('cuentas.paciente', $cuenta->paciente) }}"
                        class="block py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold text-center shadow-sm">
                        Ver detalle · {{ $cuenta->items->count() }} items
                    </a>
                </div>
            @empty
                <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-10 text-center">
                    <p class="text-xs font-semibold text-slate-400">No hay cuentas que coincidan con el filtro.</p>
                </div>
            @endforelse
        </div>

        <div>{{ $cuentas->links() }}</div>
    </div>
</x-app-layout>
