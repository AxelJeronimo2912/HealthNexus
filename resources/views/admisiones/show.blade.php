<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">
                Admisión {{ $admision->folio }} — {{ $admision->paciente?->nombre_completo }}
            </h2>
            <a href="{{ route('admisiones.index') }}" class="text-sm text-gray-600 hover:underline">← Volver</a>
        </div>
    </x-slot>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        @if (session('success'))
            <div class="p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="p-3 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
        @endif

        {{-- DATOS DEL PACIENTE --}}
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs text-gray-500 font-mono">{{ $admision->folio }}</p>
                    <h3 class="text-lg font-bold">{{ $admision->paciente?->nombre_completo }}</h3>
                    <p class="text-sm text-gray-500">
                        {{ $admision->paciente?->edad }} años — {{ ucfirst($admision->paciente?->sexo) }}
                        @if ($admision->paciente?->curp)
                            — {{ $admision->paciente->curp }}
                        @endif
                    </p>
                </div>
                <span class="px-3 py-1 rounded text-sm {{ $admision->estado_color }}">
                    {{ $admision->estado_label }}
                </span>
            </div>

            <dl class="grid grid-cols-2 gap-3 text-sm mt-4">
                <dt class="font-semibold">Tipo:</dt>
                <dd>{{ $admision->tipo_label }}</dd>
                <dt class="font-semibold">Triage:</dt>
                <dd>{{ $admision->triage ? ucfirst($admision->triage) : '—' }}</dd>
                <dt class="font-semibold">Motivo:</dt>
                <dd>{{ $admision->motivo ?? '—' }}</dd>
                <dt class="font-semibold">Diagnóstico presuntivo:</dt>
                <dd>{{ $admision->diagnostico_presuntivo ?? '—' }}</dd>
                <dt class="font-semibold">Llegada:</dt>
                <dd>{{ $admision->fecha_hora_llegada->format('d/m/Y H:i') }}</dd>
                @if ($admision->medico)
                    <dt class="font-semibold">Médico asignado:</dt>
                    <dd>Dr. {{ $admision->medico->nombre_completo }}</dd>
                @endif
                @if ($admision->cama)
                    <dt class="font-semibold">Cama:</dt>
                    <dd>{{ $admision->cama->codigo }} — {{ $admision->cama->area }}</dd>
                @endif
            </dl>
        </div>

        {{-- HOSPITALIZAR --}}
        @if ($admision->estado === 'en_espera' && $camasDisponibles->count() > 0 && $medicosDisponibles->count() > 0)
            <div class="bg-white p-6 rounded-lg shadow border-l-4 border-green-500">
                <h3 class="font-bold text-gray-800 mb-3">✓ Hospitalizar paciente</h3>
                <p class="text-xs text-gray-500 mb-4">Asigna cama y médico para hospitalizar.</p>
                <form action="{{ route('admisiones.asignar', $admision) }}" method="POST"
                    class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium">Cama</label>
                        <select name="cama_id" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">— Selecciona —</option>
                            @foreach ($camasDisponibles as $cama)
                                <option value="{{ $cama->id }}">
                                    {{ $cama->codigo }} — {{ $cama->area }} ({{ $cama->tipo_label }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Médico</label>
                        <select name="medico_id" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">— Selecciona —</option>
                            @foreach ($medicosDisponibles as $med)
                                <option value="{{ $med->id }}">Dr. {{ $med->nombre_completo }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit"
                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md text-sm">
                            Hospitalizar
                        </button>
                    </div>
                </form>
            </div>
        @endif

        {{-- DERIVAR --}}
        @if (in_array($admision->estado, ['en_espera', 'hospitalizado']))
            <div class="bg-white p-6 rounded-lg shadow border-l-4 border-orange-500">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <h3 class="font-bold text-gray-800">Derivar a otro hospital</h3>
                        <p class="text-xs text-gray-500 mt-1">Selecciona un hospital cercano desde el mapa.</p>
                    </div>
                    @if ($camasDisponibles->count() === 0 || $medicosDisponibles->count() === 0)
                        <span class="px-3 py-1 bg-red-100 text-red-800 rounded text-xs font-bold">
                            ⚠️ Sin recursos locales
                        </span>
                    @endif
                </div>

                <div id="mapa" class="h-96 rounded-lg border border-gray-200 mb-4 z-0"></div>

                <div class="space-y-2 mb-4 max-h-64 overflow-y-auto pr-1">
                    @foreach ($hospitales as $h)
                        <div class="hospital-card p-3 border rounded-lg hover:bg-orange-50 cursor-pointer transition-colors"
                            data-id="{{ $h->id }}">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-semibold text-sm">{{ $h->nombre }}</p>
                                    <p class="text-xs text-gray-500">{{ $h->direccion }}</p>
                                    <div class="flex gap-2 mt-1">
                                        <span
                                            class="text-[10px] px-2 py-0.5 bg-gray-100 rounded">{{ strtoupper($h->tipo) }}</span>
                                        @if ($h->tiene_urgencias)
                                            <span
                                                class="text-[10px] px-2 py-0.5 bg-red-100 text-red-800 rounded">Urgencias</span>
                                        @endif
                                        @if ($h->tiene_uci)
                                            <span
                                                class="text-[10px] px-2 py-0.5 bg-purple-100 text-purple-800 rounded">UCI</span>
                                        @endif
                                    </div>
                                </div>
                                <span class="text-xs text-gray-500 whitespace-nowrap ml-2">{{ $h->distancia }}
                                    km</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <form id="form-derivar" action="{{ route('admisiones.derivar', $admision) }}" method="POST"
                    class="space-y-3 border-t pt-4">
                    @csrf

                    <input type="hidden" name="hospital_derivado_id" id="hospital_derivado_id">

                    <div>
                        <label class="block text-sm font-medium">Hospital seleccionado *</label>
                        <input type="text" id="hospital_nombre" required readonly
                            placeholder="Selecciona un hospital del mapa o la lista"
                            class="mt-1 w-full bg-gray-50 border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Motivo de la derivación *</label>
                        <textarea name="motivo_derivacion" rows="3" required
                            placeholder="Ej. Sin camas disponibles, requiere UCI, sin especialista..."
                            class="mt-1 w-full border-gray-300 rounded-md shadow-sm"></textarea>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" id="btn-derivar" disabled
                            class="px-5 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-md text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                            Derivar y generar pase de salida
                        </button>
                    </div>
                </form>
            </div>
        @endif

        {{-- YA DERIVADO --}}
        @if ($admision->estado === 'derivado')
            <div class="bg-white p-6 rounded-lg shadow border-l-4 border-blue-500">
                <h3 class="font-bold text-gray-800 mb-3">Paciente derivado</h3>
                <dl class="grid grid-cols-2 gap-3 text-sm">
                    <dt class="font-semibold">Hospital destino:</dt>
                    <dd>{{ $admision->hospitalDerivado?->nombre ?? '—' }}</dd>
                    <dt class="font-semibold">Dirección:</dt>
                    <dd>{{ $admision->hospitalDerivado?->direccion ?? '—' }}</dd>
                    <dt class="font-semibold">Teléfono:</dt>
                    <dd>{{ $admision->hospitalDerivado?->telefono ?? '—' }}</dd>
                    <dt class="font-semibold">Fecha:</dt>
                    <dd>{{ $admision->fecha_derivacion?->format('d/m/Y H:i') }}</dd>
                    <dt class="font-semibold">Motivo:</dt>
                    <dd class="col-span-2">{{ $admision->motivo_derivacion ?? '—' }}</dd>
                </dl>
                <div class="mt-4 flex gap-2">
                    <a href="{{ route('admisiones.pase-salida', $admision) }}" target="_blank"
                        class="inline-block px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md text-sm">
                        📄 Ver Pase de Salida (PDF)
                    </a>
                </div>
            </div>
        @endif
    </div>

    {{-- MAPA --}}
    @if (in_array($admision->estado, ['en_espera', 'hospitalizado']))
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const hospitalActual = {
                    lat: {{ $hospitalActual['lat'] }},
                    lng: {{ $hospitalActual['lng'] }},
                    nombre: '{{ $hospitalActual['nombre'] }}'
                };

                const hospitales = @json($hospitales);

                const map = L.map('mapa').setView([hospitalActual.lat, hospitalActual.lng], 12);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors',
                    maxZoom: 19,
                }).addTo(map);

                const iconoActual = L.divIcon({
                    html: '<div style="background:#2563EB; width:20px; height:20px; border-radius:50%; border:3px solid white; box-shadow:0 2px 6px rgba(0,0,0,0.3);"></div>',
                    className: '',
                    iconSize: [20, 20],
                    iconAnchor: [10, 10],
                });

                L.marker([hospitalActual.lat, hospitalActual.lng], {
                        icon: iconoActual
                    })
                    .addTo(map)
                    .bindPopup(`<strong>${hospitalActual.nombre}</strong><br><small>Hospital actual</small>`);

                const iconoHospital = L.divIcon({
                    html: '<div style="background:#DC2626; width:18px; height:18px; border-radius:50%; border:3px solid white; box-shadow:0 2px 6px rgba(0,0,0,0.3);"></div>',
                    className: '',
                    iconSize: [18, 18],
                    iconAnchor: [9, 9],
                });

                const iconoSeleccionado = L.divIcon({
                    html: '<div style="background:#F59E0B; width:24px; height:24px; border-radius:50%; border:3px solid white; box-shadow:0 2px 8px rgba(0,0,0,0.4);"></div>',
                    className: '',
                    iconSize: [24, 24],
                    iconAnchor: [12, 12],
                });

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
            });
        </script>
    @endif
</x-app-layout>
