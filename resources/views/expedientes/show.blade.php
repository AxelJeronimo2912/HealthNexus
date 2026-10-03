<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-slate-800">Expediente clínico</h2>
                <p class="text-sm text-slate-400">Historial médico completo del paciente</p>
            </div>
            <a href="{{ route('expedientes.index') }}"
                class="inline-flex items-center gap-1 text-sm font-medium text-slate-500 hover:text-indigo-600 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-8">

        {{-- ═════════ BANNER DEL PACIENTE ═════════ --}}
        <div
            class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-indigo-600 via-indigo-700 to-indigo-900 p-8 shadow-xl shadow-indigo-200 text-white">
            <div class="absolute -right-16 -top-16 w-64 h-64 rounded-full bg-white/5"></div>
            <div class="absolute right-24 -bottom-20 w-48 h-48 rounded-full bg-white/5"></div>

            <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div class="flex items-center gap-5">
                    <div
                        class="w-16 h-16 rounded-2xl bg-white/20 border border-white/20 flex items-center justify-center text-2xl font-bold">
                        {{ strtoupper(mb_substr($paciente->nombre_completo, 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="text-3xl font-extrabold tracking-tight">{{ $paciente->nombre_completo }}</h3>
                        <p class="text-indigo-100 mt-1">
                            {{ $paciente->edad }} años · {{ ucfirst($paciente->sexo) }}
                        </p>
                        <p class="text-indigo-200 text-sm mt-0.5">CURP: {{ $paciente->curp ?? '—' }}</p>
                    </div>
                </div>

                <a href="{{ route('pacientes.show', $paciente) }}"
                    class="self-start md:self-center inline-flex items-center gap-2 rounded-2xl bg-white/10 hover:bg-white/20 border border-white/20 px-5 py-3 text-sm font-semibold backdrop-blur transition">
                    Ver ficha completa
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </a>
            </div>
        </div>

        {{-- ═════════ DATOS CLÍNICOS ═════════ --}}
        <section>
            <h4 class="text-sm font-bold tracking-wider text-slate-500 uppercase mb-4">Datos clínicos</h4>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                {{-- Tipo sanguíneo --}}
                <div class="bg-white rounded-3xl p-6 shadow-sm hover:shadow-md transition">
                    <div class="flex items-start justify-between">
                        <div class="w-12 h-12 rounded-2xl bg-red-50 flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" stroke-width="1.8"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 3c3 4 6 7 6 11a6 6 0 11-12 0c0-4 3-7 6-11z" />
                            </svg>
                        </div>
                    </div>
                    <p class="mt-5 text-xs font-bold tracking-wider text-slate-400 uppercase">Tipo sanguíneo</p>
                    <p class="mt-1 text-4xl font-extrabold text-slate-800">{{ $paciente->tipo_sanguineo ?? '—' }}</p>
                    <p class="mt-1 text-sm text-slate-500">Grupo y RH</p>
                </div>

                {{-- Alergias --}}
                <div class="bg-white rounded-3xl p-6 shadow-sm hover:shadow-md transition">
                    <div class="flex items-start justify-between">
                        <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center">
                            <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" stroke-width="1.8"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 9v4m0 4h.01M10.3 3.9L2.4 17.5A2 2 0 004.1 20.5h15.8a2 2 0 001.7-3L13.7 3.9a2 2 0 00-3.4 0z" />
                            </svg>
                        </div>
                        @if ($paciente->alergias)
                            <span
                                class="px-3 py-1 rounded-full bg-red-50 text-red-600 text-xs font-bold">Atención</span>
                        @endif
                    </div>
                    <p class="mt-5 text-xs font-bold tracking-wider text-slate-400 uppercase">Alergias</p>
                    <p class="mt-1 text-xl font-extrabold text-slate-800 leading-snug">
                        {{ $paciente->alergias ?? 'Ninguna' }}
                    </p>
                    <p class="mt-1 text-sm text-slate-500">Registradas en ficha</p>
                </div>

                {{-- Enfermedades crónicas --}}
                <div class="bg-white rounded-3xl p-6 shadow-sm hover:shadow-md transition">
                    <div class="flex items-start justify-between">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor"
                                stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4.3 6.3a4.5 4.5 0 016.4 0L12 7.6l1.3-1.3a4.5 4.5 0 016.4 6.4L12 20.4l-7.7-7.7a4.5 4.5 0 010-6.4z" />
                            </svg>
                        </div>
                    </div>
                    <p class="mt-5 text-xs font-bold tracking-wider text-slate-400 uppercase">Enf. crónicas</p>
                    <p class="mt-1 text-xl font-extrabold text-slate-800 leading-snug">
                        {{ $paciente->enfermedades_cronicas ?? 'Ninguna' }}
                    </p>
                    <p class="mt-1 text-sm text-slate-500">Antecedentes</p>
                </div>

                {{-- Resumen de actividad --}}
                <div class="bg-white rounded-3xl p-6 shadow-sm hover:shadow-md transition">
                    <div class="flex items-start justify-between">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" stroke-width="1.8"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z" />
                            </svg>
                        </div>
                        <span class="px-3 py-1 rounded-full bg-indigo-50 text-indigo-600 text-xs font-bold">
                            {{ $citas->count() }} citas
                        </span>
                    </div>
                    <p class="mt-5 text-xs font-bold tracking-wider text-slate-400 uppercase">Consultas</p>
                    <p class="mt-1 text-4xl font-extrabold text-slate-800">{{ $consultas->count() }}</p>
                    <p class="mt-1 text-sm text-slate-500">{{ $signosVitales->count() }} registros de signos vitales
                    </p>
                </div>
            </div>
        </section>

        {{-- ═════════ HISTORIAL (TABS) ═════════ --}}
        <section x-data="{ tab: 'consultas' }">
            <h4 class="text-sm font-bold tracking-wider text-slate-500 uppercase mb-4">Historial clínico</h4>

            {{-- Tabs tipo píldora --}}
            <div class="inline-flex gap-1 p-1.5 bg-white rounded-2xl shadow-sm mb-6 flex-wrap">
                @php
                    $tabs = [
                        'consultas' => ['Consultas', $consultas->count()],
                        'signos' => ['Signos vitales', $signosVitales->count()],
                        'citas' => ['Citas', $citas->count()],
                    ];
                @endphp
                @foreach ($tabs as $key => [$label, $count])
                    <button @click="tab = '{{ $key }}'"
                        :class="tab === '{{ $key }}' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-200' :
                            'text-slate-500 hover:bg-slate-50'"
                        class="px-5 py-2 rounded-xl text-sm font-semibold transition flex items-center gap-2">
                        {{ $label }}
                        <span
                            :class="tab === '{{ $key }}' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500'"
                            class="px-2 py-0.5 rounded-full text-xs font-bold">{{ $count }}</span>
                    </button>
                @endforeach
            </div>

            {{-- TAB: Consultas --}}
            <div x-show="tab === 'consultas'" class="space-y-4">
                @forelse ($consultas as $consulta)
                    @php $final = $consulta->estado === 'finalizada'; @endphp
                    <div class="bg-white rounded-3xl p-6 shadow-sm hover:shadow-md transition">
                        <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4">
                            <div class="flex gap-4">
                                <div
                                    class="w-12 h-12 shrink-0 rounded-2xl flex items-center justify-center {{ $final ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-500' }}">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-slate-400">
                                        {{ $consulta->created_at->format('d/m/Y H:i') }}
                                    </p>
                                    <p class="font-bold text-slate-800 text-lg">
                                        Dr. {{ $consulta->medico?->nombre_completo ?? '—' }}
                                    </p>
                                    @if ($consulta->diagnosticoPrincipal)
                                        <p class="text-sm text-indigo-600 mt-1">
                                            <span class="font-bold">Dx:</span>
                                            {{ $consulta->diagnosticoPrincipal->etiqueta }}
                                        </p>
                                    @elseif ($consulta->analisis)
                                        <p class="text-sm text-slate-600 mt-1">
                                            <span class="font-bold">Dx:</span>
                                            {{ Str::limit($consulta->analisis, 100) }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <div class="flex md:flex-col md:items-end items-center gap-3 justify-between">
                                <span
                                    class="px-3 py-1 rounded-full text-xs font-bold {{ $final ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ ucfirst($consulta->estado) }}
                                </span>
                                <div class="flex items-center gap-4 text-sm font-semibold">
                                    <a href="{{ route('consultas.show', $consulta) }}"
                                        class="text-indigo-600 hover:text-indigo-800">Ver →</a>
                                    <a href="{{ route('consultas.pdf', $consulta) }}" target="_blank"
                                        class="text-red-600 hover:text-red-800">PDF</a>
                                    @if ($consulta->tiene_receta)
                                        <a href="{{ route('consultas.receta.pdf', $consulta) }}" target="_blank"
                                            class="text-emerald-600 hover:text-emerald-800">Receta</a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Resumen SOAP --}}
                        @if ($consulta->subjetivo || $consulta->objetivo || $consulta->analisis || $consulta->plan)
                            <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                                @foreach ([['S', 'Subjetivo', $consulta->subjetivo, 'bg-sky-50 text-sky-600'], ['O', 'Objetivo', $consulta->objetivo, 'bg-violet-50 text-violet-600'], ['A', 'Análisis', $consulta->analisis, 'bg-amber-50 text-amber-600'], ['P', 'Plan', $consulta->plan, 'bg-emerald-50 text-emerald-600']] as [$letra, $nombre, $texto, $color])
                                    @if ($texto)
                                        <div class="rounded-2xl bg-slate-50 p-4">
                                            <div class="flex items-center gap-2 mb-2">
                                                <span
                                                    class="w-6 h-6 rounded-lg text-xs font-extrabold flex items-center justify-center {{ $color }}">{{ $letra }}</span>
                                                <span
                                                    class="text-xs font-bold tracking-wider text-slate-400 uppercase">{{ $nombre }}</span>
                                            </div>
                                            <p class="text-sm text-slate-600 leading-snug">
                                                {{ Str::limit($texto, 80) }}</p>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif

                        {{-- Medicamentos --}}
                        @if ($consulta->medicamentos->count())
                            <div class="mt-5 pt-5 border-t border-slate-100">
                                <p class="text-xs font-bold tracking-wider text-slate-400 uppercase mb-3">Receta</p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($consulta->medicamentos as $m)
                                        <span
                                            class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-emerald-50 text-emerald-800 text-xs">
                                            <span class="font-bold">{{ $m->nombre }}
                                                {{ $m->concentracion }}</span>
                                            @if ($m->pivot->dosis)
                                                <span class="text-emerald-600">{{ $m->pivot->dosis }}</span>
                                            @endif
                                            @if ($m->pivot->frecuencia)
                                                <span class="text-emerald-600">· {{ $m->pivot->frecuencia }}</span>
                                            @endif
                                            @if ($m->pivot->duracion)
                                                <span class="text-emerald-600">· {{ $m->pivot->duracion }}</span>
                                            @endif
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="bg-white rounded-3xl p-10 shadow-sm text-center text-slate-400">
                        Sin consultas registradas.
                    </div>
                @endforelse
            </div>

            {{-- TAB: Signos vitales --}}
            <div x-show="tab === 'signos'" x-cloak>
                <div class="bg-white rounded-3xl shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="bg-slate-50">
                                    @foreach (['Fecha', 'Temp', 'FC', 'FR', 'TA', 'SpO₂', 'Triage'] as $th)
                                        <th
                                            class="px-5 py-4 text-left text-xs font-bold tracking-wider text-slate-400 uppercase">
                                            {{ $th }}</th>
                                    @endforeach
                                    <th
                                        class="px-5 py-4 text-right text-xs font-bold tracking-wider text-slate-400 uppercase">
                                        Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($signosVitales as $sv)
                                    <tr class="hover:bg-slate-50/70 transition">
                                        <td class="px-5 py-4 text-slate-600">
                                            {{ $sv->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-5 py-4 font-semibold text-slate-800">
                                            {{ $sv->temperatura ?? '—' }}</td>
                                        <td class="px-5 py-4 font-semibold text-slate-800">
                                            {{ $sv->frecuencia_cardiaca ?? '—' }}</td>
                                        <td class="px-5 py-4 font-semibold text-slate-800">
                                            {{ $sv->frecuencia_respiratoria ?? '—' }}</td>
                                        <td class="px-5 py-4 font-semibold text-slate-800">
                                            {{ $sv->presion_arterial ?? '—' }}</td>
                                        <td class="px-5 py-4 font-semibold text-slate-800">
                                            {{ $sv->saturacion_oxigeno ?? '—' }}</td>
                                        <td class="px-5 py-4">
                                            <span
                                                class="px-3 py-1 rounded-full text-xs font-bold {{ $sv->triage_color }}">
                                                {{ $sv->triage_label }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-4 text-right">
                                            <a href="{{ route('signos-vitales.show', $sv) }}"
                                                class="text-indigo-600 hover:text-indigo-800 font-semibold">Ver →</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-slate-400 py-10">Sin signos
                                            vitales.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- TAB: Citas --}}
            <div x-show="tab === 'citas'" x-cloak>
                <div class="bg-white rounded-3xl shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="bg-slate-50">
                                    @foreach (['Fecha', 'Médico', 'Estado'] as $th)
                                        <th
                                            class="px-5 py-4 text-left text-xs font-bold tracking-wider text-slate-400 uppercase">
                                            {{ $th }}</th>
                                    @endforeach
                                    <th
                                        class="px-5 py-4 text-right text-xs font-bold tracking-wider text-slate-400 uppercase">
                                        Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($citas as $cita)
                                    <tr class="hover:bg-slate-50/70 transition">
                                        <td class="px-5 py-4 text-slate-600">
                                            {{ $cita->fecha_hora->format('d/m/Y H:i') }}</td>
                                        <td class="px-5 py-4 font-semibold text-slate-800">
                                            {{ $cita->medico?->nombre_completo ?? '—' }}</td>
                                        <td class="px-5 py-4">
                                            <span
                                                class="px-3 py-1 rounded-full text-xs font-bold {{ $cita->estado_color }}">
                                                {{ $cita->estado_label }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-4 text-right">
                                            <a href="{{ route('citas.show', $cita) }}"
                                                class="text-indigo-600 hover:text-indigo-800 font-semibold">Ver →</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-slate-400 py-10">Sin citas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
