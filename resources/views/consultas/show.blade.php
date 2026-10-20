@php
    $esFinalizada = $consulta->estado === 'finalizada';

    $soap = [
        ['letra' => 'S', 'titulo' => 'Subjetivo', 'valor' => $consulta->subjetivo, 'color' => 'bg-blue-600'],
        ['letra' => 'O', 'titulo' => 'Objetivo', 'valor' => $consulta->objetivo, 'color' => 'bg-emerald-600'],
        ['letra' => 'A', 'titulo' => 'Análisis', 'valor' => $consulta->analisis, 'color' => 'bg-amber-500'],
        ['letra' => 'P', 'titulo' => 'Plan', 'valor' => $consulta->plan, 'color' => 'bg-violet-600'],
    ];

    $vitales = [
        ['label' => 'Temperatura', 'valor' => $consulta->temperatura, 'unidad' => '°C', 'icono' => 'thermometer'],
        ['label' => 'Frec. cardíaca', 'valor' => $consulta->frecuencia_cardiaca, 'unidad' => 'lpm', 'icono' => 'heart'],
        [
            'label' => 'Frec. respiratoria',
            'valor' => $consulta->frecuencia_respiratoria,
            'unidad' => 'rpm',
            'icono' => 'lungs',
        ],
        ['label' => 'Presión arterial', 'valor' => $consulta->presion_arterial, 'unidad' => 'mmHg', 'icono' => 'drop'],
        ['label' => 'Saturación O₂', 'valor' => $consulta->saturacion_oxigeno, 'unidad' => '%', 'icono' => 'wind'],
        ['label' => 'Glucosa', 'valor' => $consulta->glucosa, 'unidad' => 'mg/dL', 'icono' => 'beaker'],
        ['label' => 'Peso', 'valor' => $consulta->peso, 'unidad' => 'kg', 'icono' => 'scale'],
        ['label' => 'Talla', 'valor' => $consulta->talla, 'unidad' => 'm', 'icono' => 'ruler'],
        ['label' => 'IMC', 'valor' => $consulta->imc, 'unidad' => '', 'icono' => 'chart'],
        [
            'label' => 'Perímetro abdominal',
            'valor' => $consulta->perimetro_abdominal,
            'unidad' => 'cm',
            'icono' => 'ruler',
        ],
    ];

    $imc = (float) $consulta->imc;
    $imcClase = 'text-gray-900';
    if ($consulta->imc) {
        $imcClase = match (true) {
            $imc < 18.5 => 'text-blue-600',
            $imc < 25 => 'text-green-600',
            $imc < 30 => 'text-yellow-600',
            default => 'text-red-600',
        };
    }
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div
                    class="h-12 w-12 rounded-full bg-gradient-to-br from-blue-600 to-indigo-600 text-white flex items-center justify-center font-bold text-lg shadow">
                    {{ strtoupper(mb_substr($consulta->paciente->nombre_completo, 0, 1)) }}
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wider text-gray-500">Consulta médica</p>
                    <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                        {{ $consulta->paciente->nombre_completo }}
                    </h2>
                </div>
                <span
                    class="ml-2 inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold ring-1 ring-inset
                    {{ $esFinalizada ? 'bg-green-50 text-green-700 ring-green-600/20' : 'bg-yellow-50 text-yellow-800 ring-yellow-600/20' }}">
                    <span class="h-1.5 w-1.5 rounded-full {{ $esFinalizada ? 'bg-green-500' : 'bg-yellow-500' }}"></span>
                    {{ ucfirst($consulta->estado) }}
                </span>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('consultas.pdf', $consulta) }}" target="_blank"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium shadow-sm transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                    PDF consulta
                </a>
                @if ($consulta->tiene_receta)
                    <a href="{{ route('consultas.receta.pdf', $consulta) }}" target="_blank"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium shadow-sm transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                            stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5" />
                        </svg>
                        PDF receta
                    </a>
                @endif
                @if ($consulta->estado === 'borrador')
                    <a href="{{ route('consultas.edit', $consulta) }}"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium shadow-sm transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                            stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.862 4.487zm0 0L19.5 7.125" />
                        </svg>
                        Editar
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="w-full px-4 sm:px-6 lg:px-10 py-6 bg-gray-50 min-h-screen">

        @if (session('success'))
            <div class="mb-6 flex items-center gap-3 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-600" fill="none"
                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">

            {{-- ============ COLUMNA PRINCIPAL ============ --}}
            <div class="xl:col-span-8 2xl:col-span-9 space-y-6">

                {{-- SOAP --}}
                <section class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="font-bold text-gray-900 text-lg">Nota SOAP</h3>
                        <span class="text-xs text-gray-400">Subjetivo · Objetivo · Análisis · Plan</span>
                    </div>
                    <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-5">
                        @foreach ($soap as $s)
                            <div class="rounded-lg border border-gray-200 overflow-hidden flex flex-col">
                                <div class="flex items-center gap-3 px-4 py-2.5 bg-gray-50 border-b border-gray-200">
                                    <span
                                        class="h-7 w-7 rounded-md {{ $s['color'] }} text-white text-sm font-bold flex items-center justify-center">
                                        {{ $s['letra'] }}
                                    </span>
                                    <h4 class="font-semibold text-gray-800 text-sm">{{ $s['titulo'] }}</h4>
                                </div>
                                <p class="p-4 text-sm text-gray-700 leading-relaxed whitespace-pre-line min-h-[110px]">
                                    {{ $s['valor'] ?: '—' }}</p>
                            </div>
                        @endforeach
                    </div>
                </section>

                {{-- RECETA --}}
                @if ($consulta->medicamentos->count() || $consulta->receta_libre)
                    <section class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <h3 class="font-bold text-gray-900 text-lg">Receta</h3>
                                <span
                                    class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-xs font-medium">
                                    {{ $consulta->medicamentos->count() }}
                                    {{ Str::plural('medicamento', $consulta->medicamentos->count()) }}
                                </span>
                            </div>
                            @if ($consulta->tiene_receta)
                                <a href="{{ route('consultas.receta.pdf', $consulta) }}" target="_blank"
                                    class="inline-flex items-center gap-1 text-sm bg-emerald-50 hover:bg-emerald-100 text-emerald-700 px-3 py-1.5 rounded-lg font-medium transition">
                                    Ver receta PDF
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                    </svg>
                                </a>
                            @endif
                        </div>

                        <div class="p-6 space-y-5">
                            @if ($consulta->medicamentos->count())
                                <div class="overflow-x-auto rounded-lg border border-gray-200">
                                    <table class="min-w-full text-sm">
                                        <thead class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider">
                                            <tr>
                                                <th class="px-4 py-3 text-left font-semibold">Medicamento</th>
                                                <th class="px-4 py-3 text-left font-semibold">Dosis</th>
                                                <th class="px-4 py-3 text-left font-semibold">Vía</th>
                                                <th class="px-4 py-3 text-left font-semibold">Frecuencia</th>
                                                <th class="px-4 py-3 text-left font-semibold">Duración</th>
                                                <th class="px-4 py-3 text-left font-semibold">Indicaciones</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            @foreach ($consulta->medicamentos as $m)
                                                <tr class="hover:bg-gray-50 transition">
                                                    <td class="px-4 py-3 font-semibold text-gray-900">
                                                        {{ $m->nombre }}
                                                        <span
                                                            class="block text-xs font-normal text-gray-500">{{ $m->concentracion }}</span>
                                                    </td>
                                                    <td class="px-4 py-3 text-gray-700">{{ $m->pivot->dosis }}</td>
                                                    <td class="px-4 py-3 text-gray-700">{{ $m->pivot->via }}</td>
                                                    <td class="px-4 py-3 text-gray-700">{{ $m->pivot->frecuencia }}
                                                    </td>
                                                    <td class="px-4 py-3 text-gray-700">{{ $m->pivot->duracion }}</td>
                                                    <td class="px-4 py-3 text-gray-600">{{ $m->pivot->indicaciones }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif

                            @if ($consulta->receta_libre)
                                <div class="rounded-lg bg-amber-50 border border-amber-200 p-4">
                                    <p class="text-xs font-semibold uppercase tracking-wider text-amber-700 mb-2">
                                        Receta libre</p>
                                    <p class="whitespace-pre-line text-sm text-gray-800 leading-relaxed">
                                        {{ $consulta->receta_libre }}</p>
                                </div>
                            @endif
                        </div>
                    </section>
                @endif
            </div>

            {{-- ============ COLUMNA LATERAL ============ --}}
            <aside class="xl:col-span-4 2xl:col-span-3 space-y-6 xl:sticky xl:top-6 self-start">

                {{-- Resumen --}}
                <section class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="h-1.5 bg-gradient-to-r from-blue-600 to-indigo-600"></div>
                    <div class="p-6 space-y-4">
                        <h3 class="font-bold text-gray-900 text-lg">Resumen</h3>

                        <div>
                            <p class="text-xs uppercase tracking-wider text-gray-400">Fecha</p>
                            <p class="text-sm font-medium text-gray-800">
                                {{ $consulta->created_at->format('d/m/Y') }} ·
                                {{ $consulta->created_at->format('H:i') }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs uppercase tracking-wider text-gray-400">Médico</p>
                            <p class="text-sm font-medium text-gray-800">{{ $consulta->medico->nombre_completo }}</p>
                        </div>

                        <div class="pt-4 border-t border-gray-100 space-y-3">
                            <div class="rounded-lg bg-blue-50 p-3">
                                <p class="text-xs uppercase tracking-wider text-blue-600 font-semibold">Diagnóstico
                                    principal</p>
                                <p class="text-sm font-medium text-gray-900 mt-1">
                                    {{ $consulta->diagnosticoPrincipal?->nombre ?? '—' }}</p>
                            </div>
                            <div class="rounded-lg bg-gray-50 p-3">
                                <p class="text-xs uppercase tracking-wider text-gray-500 font-semibold">Diagnóstico
                                    secundario</p>
                                <p class="text-sm font-medium text-gray-900 mt-1">
                                    {{ $consulta->diagnosticoSecundario?->nombre ?? '—' }}</p>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- Signos vitales --}}
                <section class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="font-bold text-gray-900 text-lg mb-4">Signos vitales y somatometría</h3>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach ($vitales as $v)
                            <div
                                class="rounded-lg border border-gray-200 p-3 hover:border-blue-300 hover:shadow-sm transition">
                                <div class="flex items-center justify-between">
                                    <p class="text-[11px] uppercase tracking-wide text-gray-500 leading-tight">
                                        {{ $v['label'] }}</p>
                                    <span class="text-gray-400">
                                        @switch($v['icono'])
                                            @case('thermometer')
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M15 11.25v6a3 3 0 11-6 0v-6a3 3 0 116 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3.75v7.5" />
                                                </svg>
                                            @break

                                            @case('heart')
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                                                </svg>
                                            @break

                                            @case('lungs')
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M12 4.5v15m0 0c-2.25 0-4.5-1.5-4.5-3.75V9.75A2.25 2.25 0 015.25 7.5 2.25 2.25 0 003 9.75v3.75A3.75 3.75 0 006.75 17.25H12zm0 0c2.25 0 4.5-1.5 4.5-3.75V9.75A2.25 2.25 0 0118.75 7.5 2.25 2.25 0 0121 9.75v3.75A3.75 3.75 0 0117.25 17.25H12z" />
                                                </svg>
                                            @break

                                            @case('drop')
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M12 3.75c-3.75 4.5-6 7.5-6 10.5a6 6 0 0012 0c0-3-2.25-6-6-10.5z" />
                                                </svg>
                                            @break

                                            @case('wind')
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M3 8.25h11.25a2.25 2.25 0 100-4.5M3 12h15a2.25 2.25 0 110 4.5M3 15.75h7.5a2.25 2.25 0 110 4.5" />
                                                </svg>
                                            @break

                                            @case('beaker')
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5" />
                                                </svg>
                                            @break

                                            @case('scale')
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M12 3v18m0-18L8.25 9h7.5L12 3zM4.5 21h15" />
                                                </svg>
                                            @break

                                            @case('ruler')
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M3.75 9.75l10.5 10.5a1.5 1.5 0 002.121 0l3.879-3.879a1.5 1.5 0 000-2.121L9.75 3.75H3.75v6z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M7.5 12l1.5-1.5M10.5 15l1.5-1.5M13.5 18l1.5-1.5" />
                                                </svg>
                                            @break

                                            @case('chart')
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M3 20.25h18M6.75 16.5V9m5.25 7.5V4.5m5.25 12V12" />
                                                </svg>
                                            @break

                                            @default
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6" />
                                                </svg>
                                        @endswitch
                                    </span>
                                </div>
                                <p
                                    class="mt-1.5 text-xl font-bold {{ $v['label'] === 'IMC' ? $imcClase : 'text-gray-900' }}">
                                    {{ $v['valor'] ?? '—' }}
                                    @if ($v['valor'] !== null && $v['unidad'])
                                        <span class="text-xs font-normal text-gray-500">{{ $v['unidad'] }}</span>
                                    @endif
                                </p>
                            </div>
                        @endforeach
                    </div>
                </section>
            </aside>
        </div>
    </div>
</x-app-layout>
