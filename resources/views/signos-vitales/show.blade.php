<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-3">
            <div class="min-w-0">
                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight truncate">
                    Detalle del Registro
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Signos vitales, triage y asignación de cama</p>
            </div>
            <a href="{{ route('signos-vitales.index') }}"
                class="bg-white hover:bg-brand-50 text-slate-700 hover:text-brand-600 border border-slate-200 hover:border-brand-200 px-5 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 transition-all active:scale-95 group shrink-0">
                <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
                Volver
            </a>
        </div>
    </x-slot>

    @php
        $labelCls = 'text-xs font-bold text-slate-400 uppercase tracking-wider';
        $inputCls =
            'w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-brand-500 focus:ring-0 outline-none';
        $cardCls = 'bg-white rounded-2xl border border-slate-200 shadow-sm';

        $asignacion = $signoVital->asignaciones()->where('activa', true)->first();

        $vitales = [
            ['label' => 'Temperatura', 'valor' => $signoVital->temperatura, 'unidad' => '°C', 'icono' => 'thermometer'],
            [
                'label' => 'Frec. cardíaca',
                'valor' => $signoVital->frecuencia_cardiaca,
                'unidad' => 'lpm',
                'icono' => 'heart',
            ],
            [
                'label' => 'Frec. respiratoria',
                'valor' => $signoVital->frecuencia_respiratoria,
                'unidad' => 'rpm',
                'icono' => 'lungs',
            ],
            [
                'label' => 'Presión arterial',
                'valor' => $signoVital->presion_arterial,
                'unidad' => 'mmHg',
                'icono' => 'drop',
            ],
            [
                'label' => 'Saturación O₂',
                'valor' => $signoVital->saturacion_oxigeno,
                'unidad' => '%',
                'icono' => 'wind',
            ],
            ['label' => 'Glucosa', 'valor' => $signoVital->glucosa, 'unidad' => 'mg/dL', 'icono' => 'beaker'],
            ['label' => 'Peso', 'valor' => $signoVital->peso, 'unidad' => 'kg', 'icono' => 'scale'],
            ['label' => 'Talla', 'valor' => $signoVital->talla, 'unidad' => 'm', 'icono' => 'ruler'],
            ['label' => 'IMC', 'valor' => $signoVital->imc, 'unidad' => '', 'icono' => 'chart'],
        ];

        $dolor = $signoVital->escala_dolor;
        $dolorCls =
            $dolor === null
                ? 'bg-slate-300'
                : ($dolor <= 3
                    ? 'bg-emerald-500'
                    : ($dolor <= 6
                        ? 'bg-amber-500'
                        : 'bg-rose-500'));
    @endphp

    <div class="w-full px-4 sm:px-6 lg:px-10 py-6 bg-slate-50 min-h-screen space-y-6">


        {{-- ═══════════ HERO: triage + paciente ═══════════ --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <div
                class="lg:col-span-5 xl:col-span-4 p-6 rounded-2xl border-2 flex flex-col justify-center relative overflow-hidden transition-all hover:shadow-md {{ $signoVital->triage_color }}">
                <div class="relative z-10">
                    <p class="{{ $labelCls }}">Clasificación de triage</p>
                    <p class="text-5xl font-black tracking-tight mt-1">{{ $signoVital->triage_label }}</p>
                    <p class="text-xs font-medium mt-2 opacity-80">{{ $signoVital->triage_descripcion }}</p>
                    @if ($signoVital->triage_manual)
                        <span
                            class="self-start inline-flex items-center gap-1 mt-3 px-2.5 py-1 bg-slate-800 text-white rounded-full text-xs font-bold uppercase tracking-wide">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                            Manual
                        </span>
                    @endif
                </div>
            </div>

            <div
                class="{{ $cardCls }} lg:col-span-7 xl:col-span-8 p-6 flex items-center gap-5 transition-all hover:shadow-md">
                <div
                    class="h-16 w-16 rounded-2xl bg-gradient-to-br from-brand-600 to-blue-600 text-white flex items-center justify-center font-black text-2xl shadow shrink-0">
                    {{ strtoupper(mb_substr($signoVital->paciente->nombre_completo, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="{{ $labelCls }}">Paciente</p>
                    <p class="text-3xl font-black text-slate-800 tracking-tight mt-0.5 truncate">
                        {{ $signoVital->paciente->nombre_completo }}
                    </p>
                    <p class="text-xs font-medium text-slate-400 mt-1 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Registrado el {{ $signoVital->created_at->format('d/m/Y H:i') }} por
                        {{ $signoVital->user->name ?? '—' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">

            {{-- ═══════════ COLUMNA PRINCIPAL ═══════════ --}}
            <div class="xl:col-span-8 2xl:col-span-9 space-y-6">

                {{-- Signos vitales --}}
                <section class="{{ $cardCls }} p-6">
                    <h3 class="font-extrabold text-slate-800 text-base mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Signos vitales
                    </h3>
                    <dl class="grid grid-cols-2 md:grid-cols-3 2xl:grid-cols-5 gap-3">
                        @foreach ($vitales as $v)
                            <div
                                class="group bg-slate-50/70 border border-slate-100 hover:border-brand-300 hover:bg-white hover:shadow-sm rounded-2xl p-4 transition-all duration-200 cursor-default">
                                <dt
                                    class="flex items-center justify-between text-xs font-bold text-slate-400 uppercase tracking-wider group-hover:text-brand-600 transition-colors">
                                    {{ $v['label'] }}
                                    <span class="text-slate-400 group-hover:text-brand-500 transition-colors">
                                        @switch($v['icono'])
                                            @case('thermometer')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 11.25v6a3 3 0 11-6 0v-6a3 3 0 116 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 3.75v7.5" />
                                                </svg>
                                            @break

                                            @case('heart')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                                                </svg>
                                            @break

                                            @case('lungs')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 4.5v15m0 0c-2.25 0-4.5-1.5-4.5-3.75V9.75A2.25 2.25 0 015.25 7.5 2.25 2.25 0 003 9.75v3.75A3.75 3.75 0 006.75 17.25H12zm0 0c2.25 0 4.5-1.5 4.5-3.75V9.75A2.25 2.25 0 0118.75 7.5 2.25 2.25 0 0121 9.75v3.75A3.75 3.75 0 0117.25 17.25H12z" />
                                                </svg>
                                            @break

                                            @case('drop')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 3.75c-3.75 4.5-6 7.5-6 10.5a6 6 0 0012 0c0-3-2.25-6-6-10.5z" />
                                                </svg>
                                            @break

                                            @case('wind')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M3 8.25h11.25a2.25 2.25 0 100-4.5M3 12h15a2.25 2.25 0 110 4.5M3 15.75h7.5a2.25 2.25 0 110 4.5" />
                                                </svg>
                                            @break

                                            @case('beaker')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5" />
                                                </svg>
                                            @break

                                            @case('scale')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 3v18m0-18L8.25 9h7.5L12 3zM4.5 21h15" />
                                                </svg>
                                            @break

                                            @case('ruler')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M3.75 9.75l10.5 10.5a1.5 1.5 0 002.121 0l3.879-3.879a1.5 1.5 0 000-2.121L9.75 3.75H3.75v6z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M7.5 12l1.5-1.5M10.5 15l1.5-1.5M13.5 18l1.5-1.5" />
                                                </svg>
                                            @break

                                            @case('chart')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M3 20.25h18M6.75 16.5V9m5.25 7.5V4.5m5.25 12V12" />
                                                </svg>
                                            @break

                                            @default
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 6v12m6-6H6" />
                                                </svg>
                                        @endswitch
                                    </span>
                                </dt>
                                <dd
                                    class="text-2xl font-black text-slate-800 mt-1.5 group-hover:text-brand-700 transition-colors">
                                    {{ $v['valor'] ?: '—' }}
                                    @if ($v['valor'] && $v['unidad'])
                                        <span class="text-xs font-semibold text-slate-400">{{ $v['unidad'] }}</span>
                                    @endif
                                </dd>
                            </div>
                        @endforeach

                        {{-- Escala de dolor con barra --}}
                        <div
                            class="group bg-slate-50/70 border border-slate-100 hover:border-brand-300 hover:bg-white hover:shadow-sm rounded-2xl p-4 col-span-2 md:col-span-3 2xl:col-span-1 transition-all duration-200">
                            <dt
                                class="flex items-center justify-between text-xs font-bold text-slate-400 uppercase tracking-wider group-hover:text-brand-600 transition-colors">
                                Escala de dolor
                                <svg class="w-4 h-4 text-slate-400 group-hover:text-brand-500 transition-colors"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </dt>
                            <dd
                                class="text-2xl font-black text-slate-800 mt-1.5 group-hover:text-brand-700 transition-colors">
                                {{ $dolor !== null ? $dolor : '—' }}
                                @if ($dolor !== null)
                                    <span class="text-xs font-semibold text-slate-400">/ 10</span>
                                @endif
                            </dd>
                            @if ($dolor !== null)
                                <div class="mt-2 h-1.5 rounded-full bg-slate-200 overflow-hidden">
                                    <div class="h-full rounded-full {{ $dolorCls }} transition-all duration-500"
                                        style="width: {{ min(max($dolor, 0), 10) * 10 }}%"></div>
                                </div>
                            @endif
                        </div>
                    </dl>
                </section>

                {{-- Motivo y notas --}}
                @if ($signoVital->motivo_consulta || $signoVital->notas)
                    <div
                        class="grid grid-cols-1 {{ $signoVital->motivo_consulta && $signoVital->notas ? 'lg:grid-cols-2' : '' }} gap-6">
                        @if ($signoVital->motivo_consulta)
                            <section class="{{ $cardCls }} p-6 hover:shadow-md transition-shadow">
                                <p class="{{ $labelCls }} mb-2 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-brand-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                    </svg>
                                    Motivo de consulta
                                </p>
                                <p
                                    class="text-sm text-slate-600 leading-relaxed whitespace-pre-line bg-slate-50 border border-slate-100 rounded-2xl p-4">
                                    {{ $signoVital->motivo_consulta }}
                                </p>
                            </section>
                        @endif
                        @if ($signoVital->notas)
                            <section class="{{ $cardCls }} p-6 hover:shadow-md transition-shadow">
                                <p class="{{ $labelCls }} mb-2 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-brand-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Notas adicionales
                                </p>
                                <p
                                    class="text-sm text-slate-600 leading-relaxed whitespace-pre-line bg-slate-50 border border-slate-100 rounded-2xl p-4">
                                    {{ $signoVital->notas }}
                                </p>
                            </section>
                        @endif
                    </div>
                @endif
            </div>

            {{-- ═══════════ COLUMNA LATERAL ═══════════ --}}
            <aside class="xl:col-span-4 2xl:col-span-3 space-y-6 self-start xl:sticky xl:top-4">

                {{-- Cama asignada --}}
                <section class="{{ $cardCls }} p-6">
                    <p class="{{ $labelCls }} mb-3 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Cama asignada
                    </p>
                    @if ($asignacion)
                        <div
                            class="bg-brand-50/60 border border-brand-100 rounded-2xl p-4 hover:bg-brand-50 transition-colors">
                            <div class="flex items-center gap-3">
                                <div
                                    class="h-10 w-10 rounded-xl bg-brand-600 text-white flex items-center justify-center shrink-0 shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-extrabold text-slate-800">{{ $asignacion->cama->codigo }}
                                    </p>
                                    <p class="text-xs font-medium text-slate-500">
                                        {{ $asignacion->cama->area }} / Hab. {{ $asignacion->cama->habitacion }}
                                    </p>
                                </div>
                            </div>
                            <p class="text-xs font-medium text-slate-400 mt-3 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Ingreso: {{ $asignacion->fecha_ingreso->format('d/m/Y H:i') }}
                            </p>
                            <form action="{{ route('signos-vitales.liberar-cama', $signoVital) }}" method="POST"
                                onsubmit="return confirm('¿Liberar la cama?')" class="mt-3">
                                @csrf
                                <button
                                    class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-white border border-rose-200 hover:bg-rose-50 text-rose-600 rounded-lg text-xs font-bold transition-all active:scale-95 shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Liberar cama
                                </button>
                            </form>
                        </div>
                    @else
                        <p class="text-xs font-semibold text-slate-400 mb-3">Sin cama asignada.</p>

                        @if (in_array($signoVital->triage, ['rojo', 'naranja']))
                            <button type="button" onclick="abrirModalCama()"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-rose-600 hover:bg-rose-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                Asignar cama urgente
                            </button>
                        @endif
                    @endif
                </section>

                {{-- Acciones --}}
                <section class="{{ $cardCls }} p-6">
                    <p class="{{ $labelCls }} mb-3 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Acciones
                    </p>
                    <div class="flex flex-col gap-2">
                        <a href="{{ route('signos-vitales.edit', $signoVital) }}"
                            class="px-4 py-2.5 bg-brand-600 hover:bg-brand-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all inline-flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Editar registro
                        </a>
                        <a href="{{ route('signos-vitales.index') }}"
                            class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-600 rounded-xl text-xs font-bold text-center transition-all flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                            </svg>
                            Volver al listado
                        </a>
                    </div>
                </section>
            </aside>
        </div>
    </div>

    {{-- Modal de asignación de cama (Mejorado) --}}
    <div id="modal-cama"
        class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4 transition-opacity duration-300"
        onclick="if (event.target === this) cerrarModalCama()">

        <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-2xl w-full max-h-[90vh] overflow-y-auto transform transition-transform duration-300 scale-95 opacity-0"
            id="modal-cama-content">
            <div
                class="flex justify-between items-center px-6 py-5 border-b border-slate-100 bg-slate-50/50 rounded-t-3xl">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-2xl bg-brand-100 flex items-center justify-center text-brand-600 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-800 text-base">Asignar cama al paciente</h3>
                        <p class="text-xs text-slate-400">Selecciona una cama disponible del inventario</p>
                    </div>
                </div>
                <button onclick="cerrarModalCama()"
                    class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-xl transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('signos-vitales.asignar-cama', $signoVital) }}" method="POST"
                class="p-6 space-y-5">
                @csrf

                <div
                    class="bg-brand-50/50 border border-brand-100 rounded-2xl p-4 flex items-center justify-between">
                    <div>
                        <p class="{{ $labelCls }}">Paciente</p>
                        <p class="text-sm font-extrabold text-slate-800 mt-0.5">
                            {{ $signoVital->paciente->nombre_completo }}</p>
                    </div>
                    <div class="text-right">
                        <p class="{{ $labelCls }}">Triage</p>
                        <p class="text-sm font-extrabold text-slate-800 mt-0.5">{{ $signoVital->triage_label }}</p>
                    </div>
                </div>

                @if ($camasDisponibles->isEmpty())
                    <div
                        class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs font-semibold flex items-center gap-2">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        No hay camas disponibles en este momento.
                    </div>
                @else
                    <div>
                        <label class="{{ $labelCls }}">Selecciona una cama disponible <span
                                class="text-rose-500">*</span></label>
                        <select name="cama_id" required
                            class="{{ $inputCls }} mt-1.5 font-medium cursor-pointer">
                            <option value="">— Selecciona una opción —</option>
                            @foreach ($camasDisponibles as $c)
                                <option value="{{ $c->id }}">
                                    {{ $c->codigo }} — {{ $c->area }} / Hab. {{ $c->habitacion }}
                                    ({{ $c->tipo_label }})
                                    @if ($c->oxigeno)
                                        | O₂
                                    @endif
                                    @if ($c->monitor)
                                        | Monitor
                                    @endif
                                    @if ($c->ventilador)
                                        | Ventilador
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex justify-end gap-3 pt-5 border-t border-slate-100">
                        <button type="button" onclick="cerrarModalCama()"
                            class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-600 rounded-xl text-xs font-bold transition-all">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-600 hover:bg-brand-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Asignar cama
                        </button>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <script>
        function abrirModalCama() {
            const modal = document.getElementById('modal-cama');
            const content = document.getElementById('modal-cama-content');
            modal.classList.remove('hidden');
            // Pequeño delay para que la transición de Tailwind se aplique
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function cerrarModalCama() {
            const modal = document.getElementById('modal-cama');
            const content = document.getElementById('modal-cama-content');
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') cerrarModalCama();
        });

        @if (session('sugerir_cama') && $camasDisponibles->isNotEmpty())
            abrirModalCama();
        @endif
    </script>
</x-app-layout>
