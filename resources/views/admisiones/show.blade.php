<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-3">
            <div class="min-w-0">
                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight truncate">
                    Admisión {{ $admision->folio }} — {{ $admision->paciente?->nombre_completo }}
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Detalle, hospitalización y derivación del paciente</p>
            </div>
            <a href="{{ route('admisiones.index') }}"
                class="bg-white hover:bg-indigo-50 text-slate-700 hover:text-indigo-600 border border-slate-200 hover:border-indigo-200 px-5 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 transition-all active:scale-95 group shrink-0">
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
        $labelCls = 'text-[11px] font-bold text-slate-400 uppercase tracking-wider';
        $inputCls =
            'w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-indigo-500 focus:ring-0 outline-none';
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
        @if (session('error'))
            <div
                class="p-4 bg-rose-50 border border-rose-100 text-rose-800 rounded-2xl text-xs font-semibold flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- Datos del paciente --}}
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
            <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-5">
                <div class="flex-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="px-2 py-1 bg-slate-100 text-slate-600 rounded-lg text-[11px] font-mono font-bold">
                            {{ $admision->folio }}
                        </span>
                        <span
                            class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide {{ $admision->estado_color }}">
                            {{ $admision->estado_label }}
                        </span>
                    </div>

                    <p class="{{ $labelCls }} mt-4">Paciente</p>
                    <p class="text-3xl font-black text-slate-800 tracking-tight mt-0.5">
                        {{ $admision->paciente?->nombre_completo }}
                    </p>
                    <p class="text-xs text-slate-400 mt-1">
                        {{ $admision->paciente?->edad }} años — {{ ucfirst($admision->paciente?->sexo) }}
                        @if ($admision->paciente?->curp)
                            — {{ $admision->paciente->curp }}
                        @endif
                    </p>
                </div>
            </div>

            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-5 pt-5 border-t border-slate-100">
                <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                    <dt class="{{ $labelCls }}">Tipo</dt>
                    <dd class="text-xs font-extrabold text-slate-800 mt-1">{{ $admision->tipo_label }}</dd>
                </div>
                <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                    <dt class="{{ $labelCls }}">Triage</dt>
                    <dd class="text-xs font-extrabold text-slate-800 mt-1">
                        {{ $admision->triage ? ucfirst($admision->triage) : '—' }}
                    </dd>
                </div>
                <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3 sm:col-span-2">
                    <dt class="{{ $labelCls }}">Motivo</dt>
                    <dd class="text-xs font-medium text-slate-700 mt-1">{{ $admision->motivo ?? '—' }}</dd>
                </div>
                <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3 sm:col-span-2">
                    <dt class="{{ $labelCls }}">Diagnóstico presuntivo</dt>
                    <dd class="text-xs font-medium text-slate-700 mt-1">{{ $admision->diagnostico_presuntivo ?? '—' }}
                    </dd>
                </div>
                <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                    <dt class="{{ $labelCls }}">Llegada</dt>
                    <dd class="text-xs font-extrabold text-slate-800 mt-1">
                        {{ $admision->fecha_hora_llegada->format('d/m/Y H:i') }}
                    </dd>
                </div>
                @if ($admision->medico)
                    <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                        <dt class="{{ $labelCls }}">Médico asignado</dt>
                        <dd class="text-xs font-extrabold text-slate-800 mt-1">
                            Dr. {{ $admision->medico->nombre_completo }}
                        </dd>
                    </div>
                @endif
                @if ($admision->cama)
                    <div class="bg-indigo-50/60 border border-indigo-100 rounded-2xl p-3 sm:col-span-2">
                        <dt class="{{ $labelCls }}">Cama</dt>
                        <dd class="text-xs font-extrabold text-indigo-700 mt-1">
                            {{ $admision->cama->codigo }} — {{ $admision->cama->area }}
                        </dd>
                    </div>
                @endif
            </dl>
        </div>

        {{-- Hospitalizar --}}
        @if ($admision->estado === 'en_espera' && $camasDisponibles->count() > 0 && $medicosDisponibles->count() > 0)
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-800 text-base">Hospitalizar paciente</h3>
                        <p class="text-[11px] text-slate-400">Asigna cama y médico para hospitalizar</p>
                    </div>
                </div>

                <form action="{{ route('admisiones.asignar', $admision) }}" method="POST"
                    class="p-6 grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
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
                            class="w-full px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all">
                            Hospitalizar
                        </button>
                    </div>
                </form>
            </div>
        @endif

        {{-- Derivar --}}
        @if (in_array($admision->estado, ['en_espera', 'hospitalizado']))
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex flex-wrap justify-between items-center gap-3">
                    <div>
                        <h3 class="font-extrabold text-slate-800 text-base">Derivar a otro hospital</h3>
                        <p class="text-[11px] text-slate-400">Selecciona un hospital cercano desde el mapa</p>
                    </div>
                    @if ($camasDisponibles->count() === 0 || $medicosDisponibles->count() === 0)
                        <span
                            class="px-2.5 py-1 bg-rose-50 text-rose-700 border border-rose-100 rounded-full text-[10px] font-bold uppercase tracking-wide">
                            ⚠️ Sin recursos locales
                        </span>
                    @endif
                </div>

                <div class="p-6 space-y-5">
                    <div id="mapa" class="h-96 rounded-2xl border border-slate-200 z-0"></div>

                    <div class="space-y-2 max-h-64 overflow-y-auto pr-1">
                        @foreach ($hospitales as $h)
                            <div class="hospital-card p-3 border border-slate-100 bg-slate-50/70 rounded-2xl hover:bg-orange-50 hover:border-orange-200 cursor-pointer transition-all"
                                data-id="{{ $h->id }}">
                                <div class="flex justify-between items-start gap-2">
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-slate-800">{{ $h->nombre }}</p>
                                        <p class="text-[11px] font-medium text-slate-400 mt-0.5">{{ $h->direccion }}
                                        </p>
                                        <div class="flex flex-wrap gap-1.5 mt-2">
                                            <span
                                                class="text-[10px] px-2 py-0.5 bg-slate-100 text-slate-600 rounded-full font-bold uppercase">{{ strtoupper($h->tipo) }}</span>
                                            @if ($h->tiene_urgencias)
                                                <span
                                                    class="text-[10px] px-2 py-0.5 bg-rose-50 text-rose-700 border border-rose-100 rounded-full font-bold uppercase">Urgencias</span>
                                            @endif
                                            @if ($h->tiene_uci)
                                                <span
                                                    class="text-[10px] px-2 py-0.5 bg-purple-50 text-purple-700 border border-purple-100 rounded-full font-bold uppercase">UCI</span>
                                            @endif
                                        </div>
                                    </div>
                                    <span
                                        class="px-2 py-1 bg-slate-100 text-slate-600 rounded-lg text-[10px] font-mono font-bold whitespace-nowrap shrink-0">
                                        {{ $h->distancia }} km
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <form id="form-derivar" action="{{ route('admisiones.derivar', $admision) }}" method="POST"
                        class="space-y-4 pt-5 border-t border-slate-100">
                        @csrf

                        <input type="hidden" name="hospital_derivado_id" id="hospital_derivado_id">

                        <div>
                            <label class="{{ $labelCls }}">Hospital seleccionado *</label>
                            <input type="text" id="hospital_nombre" required readonly
                                placeholder="Selecciona un hospital del mapa o la lista"
                                class="w-full bg-slate-100 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-600 outline-none cursor-not-allowed">
                        </div>

                        <div>
                            <label class="{{ $labelCls }}">Motivo de la derivación *</label>
                            <textarea name="motivo_derivacion" rows="3" required
                                placeholder="Ej. Sin camas disponibles, requiere UCI, sin especialista..."
                                class="{{ $inputCls }} resize-none"></textarea>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" id="btn-derivar" disabled
                                class="px-5 py-2.5 bg-orange-600 hover:bg-orange-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all disabled:opacity-50 disabled:cursor-not-allowed disabled:active:scale-100">
                                Derivar y generar pase de salida
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        {{-- Ya derivado --}}
        @if ($admision->estado === 'derivado')
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-5">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-800 text-base">Paciente derivado</h3>
                        <p class="text-[11px] text-slate-400">Información del hospital destino</p>
                    </div>
                </div>

                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3 sm:col-span-2">
                        <dt class="{{ $labelCls }}">Hospital destino</dt>
                        <dd class="text-xs font-extrabold text-slate-800 mt-1">
                            {{ $admision->hospitalDerivado?->nombre ?? '—' }}</dd>
                    </div>
                    <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3 sm:col-span-2">
                        <dt class="{{ $labelCls }}">Dirección</dt>
                        <dd class="text-xs font-medium text-slate-700 mt-1">
                            {{ $admision->hospitalDerivado?->direccion ?? '—' }}</dd>
                    </div>
                    <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                        <dt class="{{ $labelCls }}">Teléfono</dt>
                        <dd class="text-xs font-extrabold text-slate-800 mt-1">
                            {{ $admision->hospitalDerivado?->telefono ?? '—' }}</dd>
                    </div>
                    <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                        <dt class="{{ $labelCls }}">Fecha de derivación</dt>
                        <dd class="text-xs font-extrabold text-slate-800 mt-1">
                            {{ $admision->fecha_derivacion?->format('d/m/Y H:i') }}
                        </dd>
                    </div>
                    <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3 sm:col-span-2">
                        <dt class="{{ $labelCls }}">Motivo</dt>
                        <dd class="text-xs font-medium text-slate-700 mt-1">{{ $admision->motivo_derivacion ?? '—' }}
                        </dd>
                    </div>
                </dl>

                <div class="pt-5 border-t border-slate-100 flex flex-wrap gap-2">
                    <a href="{{ route('admisiones.pase-salida', $admision) }}" target="_blank"
                        class="px-4 py-2.5 bg-rose-600 hover:bg-rose-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all inline-flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Ver Pase de Salida (PDF)
                    </a>
                </div>
            </div>
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
                        html: '<div style="background:#4F46E5; width:20px; height:20px; border-radius:50%; border:3px solid white; box-shadow:0 2px 6px rgba(0,0,0,0.3);"></div>',
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
