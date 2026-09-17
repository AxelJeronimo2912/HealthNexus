<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
                Citas del {{ $fecha->format('d/m/Y') }}
            </h2>
            @can('agenda.crear')
                <a href="{{ route('agenda.create', ['fecha' => $fecha->format('Y-m-d')]) }}"
                    class="bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white px-4 py-2 rounded-xl text-sm font-semibold flex items-center shadow-md shadow-purple-500/10 transition-all">
                    <x-heroicon-o-plus class="w-4 h-4 mr-1.5 stroke-2" />
                    Nueva Cita
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">

        @if (session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded-r-lg shadow-sm font-medium text-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- Navegación de fecha --}}
        <div class="flex justify-between items-center mb-6 bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
            <a href="{{ route('citas.index', ['fecha' => $fecha->copy()->subDay()->format('Y-m-d')]) }}"
                class="px-3.5 py-2 bg-slate-50 hover:bg-purple-50 hover:text-purple-700 rounded-xl text-xs font-semibold text-slate-600 transition-all">
                ← Día anterior
            </a>

            <div class="text-center">
                <p class="text-base font-extrabold text-slate-900">
                    {{ ucfirst($fecha->locale('es')->translatedFormat('l d \d\e F \d\e Y')) }}
                </p>
                <div class="flex gap-3 justify-center mt-1">
                    <a href="{{ route('citas.index', ['fecha' => today()->format('Y-m-d')]) }}"
                        class="text-xs font-semibold text-purple-600 hover:text-purple-800 transition-colors">Hoy</a>
                    <span class="text-slate-300 text-xs">•</span>
                    <a href="{{ route('agenda.index', ['fecha' => $fecha->format('Y-m-d')]) }}"
                        class="text-xs font-semibold text-purple-600 hover:text-purple-800 transition-colors">Ver semana</a>
                </div>
            </div>

            <a href="{{ route('citas.index', ['fecha' => $fecha->copy()->addDay()->format('Y-m-d')]) }}"
                class="px-3.5 py-2 bg-slate-50 hover:bg-purple-50 hover:text-purple-700 rounded-xl text-xs font-semibold text-slate-600 transition-all">
                Día siguiente →
            </a>
        </div>

        {{-- Estadísticas --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
            <div class="bg-slate-900 text-white p-4 rounded-2xl shadow-sm border border-slate-800">
                <p class="text-[10px] font-bold tracking-widest text-slate-400 uppercase">Total</p>
                <p class="text-2xl font-black text-white mt-1">{{ $stats['total'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100">
                <p class="text-[10px] font-bold tracking-widest text-slate-400 uppercase">Programadas</p>
                <p class="text-2xl font-black text-blue-600 mt-1">{{ $stats['programadas'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100">
                <p class="text-[10px] font-bold tracking-widest text-slate-400 uppercase">Confirmadas</p>
                <p class="text-2xl font-black text-purple-600 mt-1">{{ $stats['confirmadas'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100">
                <p class="text-[10px] font-bold tracking-widest text-slate-400 uppercase">Atendidas</p>
                <p class="text-2xl font-black text-emerald-600 mt-1">{{ $stats['atendidas'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100">
                <p class="text-[10px] font-bold tracking-widest text-slate-400 uppercase">Canceladas</p>
                <p class="text-2xl font-black text-rose-600 mt-1">{{ $stats['canceladas'] }}</p>
            </div>
        </div>

        {{-- Listado de citas --}}
        <div class="bg-white shadow-sm border border-slate-100 rounded-2xl overflow-hidden">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/70">
                    <tr>
                        <th class="px-5 py-3.5 text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider">Hora</th>
                        <th class="px-5 py-3.5 text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider">Paciente</th>
                        <th class="px-5 py-3.5 text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider">Triage</th>
                        @if (auth()->user()->hasRole('administrador'))
                            <th class="px-5 py-3.5 text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider">Médico</th>
                        @endif
                        <th class="px-5 py-3.5 text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider">Duración</th>
                        <th class="px-5 py-3.5 text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider">Estado</th>
                        <th class="px-5 py-3.5 text-right text-[11px] font-bold text-slate-400 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-100">
                    @forelse ($citas as $cita)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-5 py-4 text-sm font-extrabold text-slate-900">
                                {{ $cita->fecha_hora->format('H:i') }}
                            </td>
                            <td class="px-5 py-4 text-sm font-semibold text-slate-800">
                                {{ $cita->paciente->nombre_completo ?? 'Paciente eliminado' }}
                            </td>
                            <td class="px-5 py-4 text-sm">
                                @if ($cita->triage_al_momento)
                                    @php
                                        $color = match ($cita->triage_al_momento) {
                                            'rojo' => 'bg-rose-100 text-rose-800 font-bold',
                                            'naranja' => 'bg-amber-100 text-amber-900 font-bold',
                                            'amarillo' => 'bg-yellow-100 text-yellow-900 font-semibold',
                                            'verde' => 'bg-emerald-100 text-emerald-800 font-semibold',
                                            'azul' => 'bg-sky-100 text-sky-800 font-semibold',
                                            default => 'bg-slate-100 text-slate-700',
                                        };
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-full text-xs {{ $color }}">
                                        {{ ucfirst($cita->triage_al_momento) }}
                                    </span>
                                @else
                                    <span class="text-slate-300">—</span>
                                @endif
                            </td>
                            @if (auth()->user()->hasRole('administrador'))
                                <td class="px-5 py-4 text-sm text-slate-600 font-medium">
                                    {{ $cita->medico->nombre_completo ?? '—' }}
                                </td>
                            @endif
                            <td class="px-5 py-4 text-sm text-slate-500">{{ $cita->duracion_minutos }} min</td>
                            <td class="px-5 py-4 text-sm">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold shadow-sm {{ $cita->estado_color }}">
                                    {{ $cita->estado_label }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right text-sm">
                                <a href="{{ route('citas.show', $cita) }}"
                                    class="inline-flex items-center px-3 py-1.5 bg-purple-50 hover:bg-purple-600 text-purple-700 hover:text-white rounded-lg font-bold text-xs transition-all">
                                    Ver detalle
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->hasRole('administrador') ? 7 : 6 }}"
                                class="px-5 py-10 text-center text-slate-400 text-sm">
                                No hay citas registradas para este día.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>