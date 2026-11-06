
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-3">
            <div class="min-w-0">
                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight truncate">
                    Admisión {{ $admision->folio }} — {{ $admision->paciente?->nombre_completo }}
                </h2>
                <p class="text-sm text-slate-500 mt-0.5">Detalle, hospitalización y derivación del paciente</p>
            </div>
            <a href="{{ route('admisiones.index') }}"
                class="bg-white hover:bg-brand-50 text-slate-700 hover:text-brand-600 border border-slate-200 hover:border-brand-200 px-5 py-2.5 rounded-xl text-sm font-bold flex items-center gap-2 transition-all active:scale-95 group shrink-0 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2">
                <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
                Volver
            </a>
        </div>
    </x-slot>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    @php
        $labelCls = 'block text-xs font-semibold text-slate-500';
        $inputCls =
            'w-full mt-1.5 bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 transition focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-50 outline-none';

        // Solo presentación: iniciales para el avatar
        $iniciales = collect(preg_split('/\s+/', trim($admision->paciente?->nombre_completo ?? '')))
            ->filter()
            ->take(2)
            ->map(fn($p) => mb_strtoupper(mb_substr($p, 0, 1)))
            ->implode('');

        // Solo presentación: color del punto de triage
        $triageDot = match (strtolower((string) $admision->triage)) {
            'rojo' => 'bg-rose-500',
            'naranja' => 'bg-orange-500',
            'amarillo' => 'bg-amber-400',
            'verde' => 'bg-emerald-500',
            'azul' => 'bg-sky-500',
            default => 'bg-slate-300',
        };

        // Solo presentación: avance del paciente
        $pasos = [
            'en_espera' => 'En espera',
            'hospitalizado' => 'Hospitalizado',
            'derivado' => 'Derivado',
        ];
        $pasoActual = array_search($admision->estado, array_keys($pasos));
    @endphp

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Datos del paciente --}}
        <section class="bg-white rounded-2xl ring-1 ring-slate-200/70 shadow-card overflow-hidden">
            <div class="p-6">
                <div class="flex flex-col sm:flex-row sm:items-center gap-5">
                    <div
                        class="w-16 h-16 rounded-2xl bg-brand-50 text-brand-700 ring-1 ring-brand-100 flex items-center justify-center text-xl font-black shrink-0">
                        {{ $iniciales ?: '—' }}
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span
                                class="px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg text-xs font-mono font-bold">
                                {{ $admision->folio }}
                            </span>
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $admision->estado_color }}">
                                {{ $admision->estado_label }}
                            </span>
                        </div>

                        <h1 class="text-3xl font-black text-slate-800 tracking-tight mt-2 break-words">
                            {{ $admision->paciente?->nombre_completo }}
                        </h1>
                        <p class="text-sm text-slate-500 mt-1">
                            {{ $admision->paciente?->edad }} años — {{ ucfirst($admision->paciente?->sexo) }}
                            @if ($admision->paciente?->curp)
                                — <span class="font-mono text-slate-600">{{ $admision->paciente->curp }}</span>
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Avance --}}
                @if ($pasoActual !== false)
                    <ol class="mt-6 flex items-center" aria-label="Avance de la admisión">
                        @foreach ($pasos as $clave => $texto)
                            @php
                                $idx = $loop->index;
                                $hecho = $idx < $pasoActual;
                                $actual = $idx === $pasoActual;
                            @endphp
                            <li class="flex items-center {{ $loop->last ? '' : 'flex-1' }}"
                                @if ($actual) aria-current="step" @endif>
                                <div class="flex items-center gap-2.5">
                                    <span
                                        class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold shrink-0
                                        {{ $hecho ? 'bg-brand-600 text-white' : '' }}
                                        {{ $actual ? 'bg-brand-50 text-brand-700 ring-2 ring-brand-500' : '' }}
                                        {{ !$hecho && !$actual ? 'bg-slate-100 text-slate-400' : '' }}">
                                        @if ($hecho)
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                        @else
                                            {{ $idx + 1 }}
                                        @endif
                                    </span>
                                    <span
                                        class="text-sm {{ $actual ? 'font-bold text-slate-800' : ($hecho ? 'font-semibold text-slate-600' : 'font-medium text-slate-400') }}">
                                        {{ $texto }}
                                    </span>
                                </div>
                                @unless ($loop->last)
                                    <span
                                        class="flex-1 h-0.5 mx-3 rounded-full {{ $hecho ? 'bg-brand-600' : 'bg-slate-200' }}"></span>
                                @endunless
                            </li>
                        @endforeach
                    </ol>
                @endif
            </div>

            <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 px-6 pb-6">
                <div class="bg-slate-50 ring-1 ring-slate-100 rounded-xl p-3.5">
                    <dt class="{{ $labelCls }}">Tipo</dt>
                    <dd class="text-sm font-bold text-slate-800 mt-1">{{ $admision->tipo_label }}</dd>
                </div>
                <div class="bg-slate-50 ring-1 ring-slate-100 rounded-xl p-3.5">
                    <dt class="{{ $labelCls }}">Triage</dt>
                    <dd class="text-sm font-bold text-slate-800 mt-1 flex items-center gap-2">
                        @if ($admision->triage)
                            <span class="w-2.5 h-2.5 rounded-full {{ $triageDot }}"></span>
                        @endif
                        {{ $admision->triage ? ucfirst($admision->triage) : '—' }}
                    </dd>
                </div>
                <div class="bg-slate-50 ring-1 ring-slate-100 rounded-xl p-3.5">
                    <dt class="{{ $labelCls }}">Llegada</dt>
                    <dd class="text-sm font-bold text-slate-800 mt-1">
                        {{ $admision->fecha_hora_llegada->format('d/m/Y H:i') }}
                    </dd>
                </div>
                @if ($admision->medico)
                    <div class="bg-slate-50 ring-1 ring-slate-100 rounded-xl p-3.5">
                        <dt class="{{ $labelCls }}">Médico asignado</dt>
                        <dd class="text-sm font-bold text-slate-800 mt-1">
                            Dr. {{ $admision->medico->nombre_completo }}
                        </dd>
                    </div>
                @endif
                @if ($admision->cama)
                    <div class="bg-brand-50/70 ring-1 ring-brand-100 rounded-xl p-3.5">
                        <dt class="{{ $labelCls }} !text-brand-600">Cama</dt>
                        <dd class="text-sm font-bold text-brand-700 mt-1">
                            {{ $admision->cama->codigo }} — {{ $admision->cama->area }}
                        </dd>
                    </div>
                @endif
            </dl>

            <dl class="grid grid-cols-1 md:grid-cols-2 gap-6 px-6 py-5 border-t border-slate-100 bg-slate-50/40">
                <div>
                    <dt class="{{ $labelCls }}">Motivo</dt>
                    <dd class="text-sm text-slate-700 leading-relaxed mt-1.5">{{ $admision->motivo ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="{{ $labelCls }}">Diagnóstico presuntivo</dt>
                    <dd class="text-sm text-slate-700 leading-relaxed mt-1.5">
                        {{ $admision->diagnostico_presuntivo ?? '—' }}
                    </dd>
                </div>
            </dl>
        </section>

        {{-- Hospitalizar --}}
        @if ($admision->estado === 'en_espera' && $camasDisponibles->count() > 0 && $medicosDisponibles->count() > 0)
            <section class="bg-white rounded-2xl ring-1 ring-slate-200/70 shadow-card overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-xl bg-emerald-50 ring-1 ring-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-800 text-base">Hospitalizar paciente</h3>
                        <p class="text-sm text-slate-500">Asigna cama y médico para hospitalizar</p>
                    </div>
                </div>

                <form action="{{ route('admisiones.asignar', $admision) }}" method="POST"
                    class="p-6 grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    @csrf
                    <div>
                        <label class="{{ $labelCls }}">Cama *</label>
                        <select name="cama_id" required class="{{ $inputCls }}">
                            <option value="">— Selecciona —</option>
                            @foreach ($camasDisponibles as $cama)
                                <option value="{{ $cama->id }}">
                                    {{ $cama->codigo }} — {{ $cama->area }} ({{ $cama->tipo_label }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="{{ $labelCls }}">Médico *</label>
                        <select name="medico_id" required class="{{ $inputCls }}">
                            <option value="">— Selecciona —</option>
                            @foreach ($medicosDisponibles as $med)
                                <option value="{{ $med->id }}">Dr. {{ $med->nombre_completo }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <button type="submit"
                            class="w-full px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white rounded-xl text-sm font-bold shadow-sm transition-all focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2">
                            Hospitalizar
                        </button>
                    </div>
                </form>
            </section>
        @endif

        {{-- Derivar --}}
        @if (in_array($admision->estado, ['en_espera', 'hospitalizado']))
            <section class="bg-white rounded-2xl ring-1 ring-slate-200/70 shadow-card overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex flex-wrap justify-between items-center gap-3">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-xl bg-orange-50 ring-1 ring-orange-100 flex items-center justify-center text-orange-600 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-slate-800 text-base">Derivar a otro hospital</h3>
                            <p class="text-sm text-slate-500">Selecciona un hospital cercano desde el mapa</p>
                        </div>
                    </div>
                    @if ($camasDisponibles->count() === 0 || $medicosDisponibles->count() === 0)
                        <span
                            class="px-3 py-1.5 bg-rose-50 text-rose-700 border border-rose-100 rounded-full text-xs font-bold inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                            </svg>
                            Sin recursos locales
                        </span>
                    @endif
                </div>

                <div class="p-6 space-y-5">
                    <div class="relative">
                        <div id="mapa" class="h-96 rounded-2xl border border-slate-200 z-0"></div>

                        {{-- Leyenda del mapa --}}
                        <div
                            class="absolute bottom-3 left-3 z-[400] bg-white/95 backdrop-blur rounded-xl ring-1 ring-slate-200 shadow-sm px-3 py-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs font-medium text-slate-600">
                            <span class="flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-brand-600"></span> Hospital actual
                            </span>
                            <span class="flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-rose-600"></span> Disponible
                            </span>
                            <span class="flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-orange-500"></span> Seleccionado
                            </span>
                        </div>
                    </div>

                    <div>
                        <p class="text-sm font-bold text-slate-700 mb-2">Hospitales cercanos</p>
                        <div class="space-y-2 max-h-72 overflow-y-auto pr-1">
                            @foreach ($hospitales as $h)
                                <div class="hospital-card p-3.5 border border-slate-200 bg-white rounded-xl hover:bg-orange-50 hover:border-orange-300 cursor-pointer transition-colors"
                                    data-id="{{ $h->id }}">
                                    <div class="flex justify-between items-start gap-3">
                                        <div class="min-w-0">
                                            <p class="text-sm font-bold text-slate-800">{{ $h->nombre }}</p>
                                            <p class="text-xs text-slate-500 mt-0.5">{{ $h->direccion }}</p>
                                            <div class="flex flex-wrap gap-1.5 mt-2">
                                                <span
                                                    class="text-xs px-2 py-0.5 bg-slate-100 text-slate-600 rounded-full font-semibold">{{ strtoupper($h->tipo) }}</span>
                                                @if ($h->tiene_urgencias)
                                                    <span
                                                        class="text-xs px-2 py-0.5 bg-rose-50 text-rose-700 border border-rose-100 rounded-full font-semibold">Urgencias</span>
                                                @endif
                                                @if ($h->tiene_uci)
                                                    <span
                                                        class="text-xs px-2 py-0.5 bg-accent-50 text-accent-700 border border-accent-100 rounded-full font-semibold">UCI</span>
                                                @endif
                                            </div>
                                        </div>
                                        <span
                                            class="px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg text-xs font-mono font-bold whitespace-nowrap shrink-0">
                                            {{ $h->distancia }} km
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <form id="form-derivar" action="{{ route('admisiones.derivar', $admision) }}" method="POST"
                        class="space-y-4 pt-5 border-t border-slate-100">
                        @csrf

                        <input type="hidden" name="hospital_derivado_id" id="hospital_derivado_id">

                        <div>
                            <label class="{{ $labelCls }}">Hospital seleccionado *</label>
                            <div class="relative mt-1.5">
                                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <input type="text" id="hospital_nombre" required readonly
                                    placeholder="Selecciona un hospital del mapa o la lista"
                                    class="w-full bg-slate-100 border border-slate-200 rounded-xl pl-10 pr-3.5 py-2.5 text-sm font-semibold text-slate-700 placeholder:font-normal placeholder:text-slate-400 outline-none cursor-not-allowed">
                            </div>
                        </div>

                        <div>
                            <label class="{{ $labelCls }}">Motivo de la derivación *</label>
                            <textarea name="motivo_derivacion" rows="3" required
                                placeholder="Ej. Sin camas disponibles, requiere UCI, sin especialista..."
                                class="{{ $inputCls }} resize-none"></textarea>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" id="btn-derivar" disabled
                                class="w-full sm:w-auto px-6 py-2.5 bg-orange-600 hover:bg-orange-700 active:scale-95 text-white rounded-xl text-sm font-bold shadow-sm transition-all focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 focus-visible:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed disabled:active:scale-100">
                                Derivar y generar pase de salida
                            </button>
                        </div>
                    </form>
                </div>
            </section>
        @endif

        {{-- Ya derivado --}}
        @if ($admision->estado === 'derivado')
            <section class="bg-white rounded-2xl ring-1 ring-slate-200/70 shadow-card overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-xl bg-brand-50 ring-1 ring-brand-100 flex items-center justify-center text-brand-600 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-800 text-base">Paciente derivado</h3>
                        <p class="text-sm text-slate-500">Información del hospital destino</p>
                    </div>
                </div>

                <div class="p-6 space-y-5">
                    <div class="bg-brand-50/60 ring-1 ring-brand-100 rounded-xl p-4">
                        <p class="{{ $labelCls }} !text-brand-600">Hospital destino</p>
                        <p class="text-lg font-extrabold text-slate-800 mt-1">
                            {{ $admision->hospitalDerivado?->nombre ?? '—' }}
                        </p>
                        <p class="text-sm text-slate-600 mt-0.5">
                            {{ $admision->hospitalDerivado?->direccion ?? '—' }}
                        </p>
                    </div>

                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="bg-slate-50 ring-1 ring-slate-100 rounded-xl p-3.5">
                            <dt class="{{ $labelCls }}">Teléfono</dt>
                            <dd class="text-sm font-bold text-slate-800 mt-1">
                                {{ $admision->hospitalDerivado?->telefono ?? '—' }}
                            </dd>
                        </div>
                        <div class="bg-slate-50 ring-1 ring-slate-100 rounded-xl p-3.5">
                            <dt class="{{ $labelCls }}">Fecha de derivación</dt>
                            <dd class="text-sm font-bold text-slate-800 mt-1">
                                {{ $admision->fecha_derivacion?->format('d/m/Y H:i') }}
                            </dd>
                        </div>
                        <div class="bg-slate-50 ring-1 ring-slate-100 rounded-xl p-3.5 sm:col-span-2">
                            <dt class="{{ $labelCls }}">Motivo</dt>
                            <dd class="text-sm text-slate-700 leading-relaxed mt-1">
                                {{ $admision->motivo_derivacion ?? '—' }}
                            </dd>
                        </div>
                    </dl>

                    <div class="pt-5 border-t border-slate-100 flex flex-wrap gap-2">
                        <a href="{{ route('admisiones.pase-salida', $admision) }}" target="_blank"
                            class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 active:scale-95 text-white rounded-xl text-sm font-bold shadow-sm transition-all inline-flex items-center gap-2 focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-500 focus-visible:ring-offset-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            Ver Pase de Salida (PDF)
                        </a>
                    </div>
                </div>
            </section>
        @endif
    </div>

    @push('scripts')
        @if (in_array($admision->estado, ['en_espera', 'hospitalizado']))
            <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const hospitalActual = {
                        lat: {{ $hospitalActual['lat'] }},
                        lng: {{ $hospitalActual['lng'] }},
                        nombre: @json($hospitalActual['nombre']),
                    };
                    const hospitales = @json($hospitales);

                    const map = L.map('mapa').setView([hospitalActual.lat, hospitalActual.lng], 12);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© OpenStreetMap contributors',
                        maxZoom: 19,
                    }).addTo(map);

                    const iconoActual = L.divIcon({
                        html: '<div style="background:#3A40A6; width:20px; height:20px; border-radius:50%; border:3px solid white; box-shadow:0 2px 6px rgba(0,0,0,0.3);"></div>',
                        className: '',
                        iconSize: [20, 20],
                        iconAnchor: [10, 10],
                    });

                    const iconoHospital = L.divIcon({
                        html: '<div style="background:#E11D48; width:18px; height:18px; border-radius:50%; border:3px solid white; box-shadow:0 2px 6px rgba(0,0,0,0.3);"></div>',
                        className: '',
                        iconSize: [18, 18],
                        iconAnchor: [9, 9],
                    });

                    const iconoSeleccionado = L.divIcon({
                        html: '<div style="background:#F97316; width:24px; height:24px; border-radius:50%; border:3px solid white; box-shadow:0 2px 8px rgba(0,0,0,0.4);"></div>',
                        className: '',
                        iconSize: [24, 24],
                        iconAnchor: [12, 12],
                    });

                    L.marker([hospitalActual.lat, hospitalActual.lng], {
                            icon: iconoActual
                        })
                        .addTo(map)
                        .bindPopup(`<strong>${hospitalActual.nombre}</strong><br><small>Hospital actual</small>`);

                    let marcadorSeleccionado = null;

                    hospitales.forEach(h => {
                        const marker = L.marker([h.latitud, h.longitud], {
                                icon: iconoHospital
                            })
                            .addTo(map)
                            .bindPopup(`
                                <div style="max-width:220px;">
                                    <strong>${h.nombre}</strong><br>
                                    <small>${h.direccion ?? ''}</small><br>
                                    <small>${h.distancia} km</small><br>
                                    <button onclick="seleccionarHospital(${h.id})"
                                            style="margin-top:6px; padding:4px 10px; background:#F97316; color:white; border:none; border-radius:4px; cursor:pointer;">
                                        Seleccionar
                                    </button>
                                </div>
                            `);

                        marker.on('click', () => seleccionarHospital(h.id));
                    });

                    window.seleccionarHospital = function(id) {
                        const h = hospitales.find(x => x.id === id);
                        if (!h) return;

                        document.getElementById('hospital_derivado_id').value = h.id;
                        document.getElementById('hospital_nombre').value = h.nombre;
                        document.getElementById('btn-derivar').disabled = false;

                        map.setView([h.latitud, h.longitud], 14);

                        if (marcadorSeleccionado) map.removeLayer(marcadorSeleccionado);
                        marcadorSeleccionado = L.marker([h.latitud, h.longitud], {
                                icon: iconoSeleccionado
                            })
                            .addTo(map);
                    };

                    document.querySelectorAll('.hospital-card').forEach(card => {
                        card.addEventListener('click', () => {
                            seleccionarHospital(parseInt(card.dataset.id));
                        });
                    });

                    const formDerivar = document.getElementById('form-derivar');
                    if (!formDerivar) return;

                    const btnDerivar = document.getElementById('btn-derivar');

                    formDerivar.addEventListener('submit', async (e) => {
                        e.preventDefault();

                        btnDerivar.disabled = true;
                        const textoOriginal = btnDerivar.textContent;
                        btnDerivar.textContent = 'Derivando...';

                        try {
                            const res = await fetch(formDerivar.action, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                        .content,
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                                body: new FormData(formDerivar),
                            });

                            const json = await res.json().catch(() => ({}));

                            if (!res.ok || !json.ok) {
                                const mensaje = json.errors ?
                                    Object.values(json.errors).flat().join('\n') :
                                    (json.message || 'No se pudo derivar al paciente.');
                                alert(mensaje);
                                return;
                            }

                            window.open(json.pase_url, '_blank', 'noopener,noreferrer');
                            window.location.reload();
                        } catch (err) {
                            alert('Error de red. Intenta de nuevo.');
                        } finally {
                            btnDerivar.disabled = false;
                            btnDerivar.textContent = textoOriginal;
                        }
                    });
                });
            </script>
        @endif
    @endpush
</x-app-layout>
