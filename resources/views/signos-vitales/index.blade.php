<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-3">
            <div class="min-w-0">
                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight truncate">
                    Signos Vitales y Triage
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Registro y clasificación de triage por paciente</p>
            </div>
            <a href="{{ route('signos-vitales.create') }}"
                class="bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white px-5 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 shadow-sm transition-all shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                Nuevo Registro
            </a>
        </div>
    </x-slot>

    @php
        $inputCls =
            'w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-indigo-500 focus:ring-0 outline-none';
        $labelCls = 'block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5';
        $thCls = 'px-5 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider';
    @endphp

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

        {{-- ============ FILTROS ============ --}}
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-5">
            <form method="GET" class="flex gap-2 flex-wrap items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="{{ $labelCls }}">Buscar paciente</label>
                    <input type="text" name="buscar" value="{{ $busqueda }}" placeholder="Nombre del paciente"
                        class="{{ $inputCls }}">
                </div>
                <div class="min-w-[180px]">
                    <label class="{{ $labelCls }}">Triage</label>
                    <select name="triage" class="{{ $inputCls }}">
                        <option value="">Todos los triages</option>
                        <option value="rojo" @selected($filtroTriage == 'rojo')>🔴 Rojo</option>
                        <option value="naranja" @selected($filtroTriage == 'naranja')>🟠 Naranja</option>
                        <option value="amarillo" @selected($filtroTriage == 'amarillo')>🟡 Amarillo</option>
                        <option value="verde" @selected($filtroTriage == 'verde')>🟢 Verde</option>
                        <option value="azul" @selected($filtroTriage == 'azul')>🔵 Azul</option>
                    </select>
                </div>
                <button type="submit"
                    class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all">
                    Filtrar
                </button>
                @if ($busqueda || $filtroTriage)
                    <a href="{{ route('signos-vitales.index') }}"
                        class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition-all">
                        Limpiar
                    </a>
                @endif
            </form>
        </div>

        {{-- ============ TABLA ============ --}}
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="font-extrabold text-slate-800 text-base">Registros de signos vitales</h3>
                <p class="text-[11px] text-slate-400">{{ $registros->total() }} registros en total</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-slate-50/70 border-b border-slate-100">
                        <tr>
                            <th class="{{ $thCls }} text-left">Paciente</th>
                            <th class="{{ $thCls }} text-left">Fecha</th>
                            <th class="{{ $thCls }} text-left">Temp</th>
                            <th class="{{ $thCls }} text-left">FC</th>
                            <th class="{{ $thCls }} text-left">FR</th>
                            <th class="{{ $thCls }} text-left">SpO₂</th>
                            <th class="{{ $thCls }} text-left">TA</th>
                            <th class="{{ $thCls }} text-left">Triage</th>
                            <th class="px-5 py-3.5"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($registros as $r)
                            <tr class="hover:bg-indigo-50/30 transition-colors">
                                <td class="px-5 py-3.5">
                                    <p class="text-xs font-bold text-slate-800">{{ $r->paciente->nombre_completo }}</p>
                                </td>
                                <td class="px-5 py-3.5 text-[11px] font-medium text-slate-400 whitespace-nowrap">
                                    {{ $r->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-5 py-3.5 text-xs font-semibold text-slate-700">
                                    {{ $r->temperatura ? $r->temperatura . '°C' : '—' }}
                                </td>
                                <td class="px-5 py-3.5 text-xs font-semibold text-slate-700">
                                    {{ $r->frecuencia_cardiaca ?? '—' }}
                                </td>
                                <td class="px-5 py-3.5 text-xs font-semibold text-slate-700">
                                    {{ $r->frecuencia_respiratoria ?? '—' }}
                                </td>
                                <td class="px-5 py-3.5 text-xs font-semibold text-slate-700">
                                    {{ $r->saturacion_oxigeno ? $r->saturacion_oxigeno . '%' : '—' }}
                                </td>
                                <td class="px-5 py-3.5 text-xs font-semibold text-slate-700">
                                    {{ $r->presion_arterial ?? '—' }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <span
                                        class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide border {{ $r->triage_color }}">
                                        {{ $r->triage_label }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex justify-end">
                                        <a href="{{ route('signos-vitales.show', $r) }}"
                                            class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-[11px] font-bold transition-all">
                                            Ver
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-5 py-12 text-center">
                                    <p class="text-xs font-semibold text-slate-400">Sin registros.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">{{ $registros->links() }}</div>
    </div>
</x-app-layout>
