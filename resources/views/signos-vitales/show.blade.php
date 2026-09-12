<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Detalle del Registro</h2>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

        @if (session('success'))
            <div class="p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="p-3 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
        @endif

        {{-- Triage destacado --}}
        <div class="p-6 rounded-lg border-2 {{ $signoVital->triage_color }}">
            <p class="text-sm text-gray-600">Clasificación de triage</p>
            <p class="text-2xl font-bold">{{ $signoVital->triage_label }}</p>
            <p class="text-sm">{{ $signoVital->triage_descripcion }}</p>
            @if ($signoVital->triage_manual)
                <span class="inline-block mt-2 text-xs bg-gray-800 text-white px-2 py-0.5 rounded">Manual</span>
            @endif
        </div>

        <div class="bg-white p-6 rounded-lg shadow space-y-4">
            <div>
                <h3 class="text-lg font-bold">{{ $signoVital->paciente->nombre_completo }}</h3>
                <p class="text-sm text-gray-500">
                    Registrado el {{ $signoVital->created_at->format('d/m/Y H:i') }}
                    por {{ $signoVital->user->name ?? '—' }}
                </p>
            </div>

            <div>
                <h4 class="font-semibold text-sm text-gray-700 mb-2">Signos vitales</h4>
                <dl class="grid grid-cols-2 gap-3 text-sm">
                    <dt class="font-semibold">Temperatura:</dt>
                    <dd>{{ $signoVital->temperatura ? $signoVital->temperatura . ' °C' : '—' }}</dd>
                    <dt class="font-semibold">Frec. cardíaca:</dt>
                    <dd>{{ $signoVital->frecuencia_cardiaca ? $signoVital->frecuencia_cardiaca . ' lpm' : '—' }}</dd>
                    <dt class="font-semibold">Frec. respiratoria:</dt>
                    <dd>{{ $signoVital->frecuencia_respiratoria ? $signoVital->frecuencia_respiratoria . ' rpm' : '—' }}
                    </dd>
                    <dt class="font-semibold">Presión arterial:</dt>
                    <dd>{{ $signoVital->presion_arterial ?? '—' }}</dd>
                    <dt class="font-semibold">Saturación O₂:</dt>
                    <dd>{{ $signoVital->saturacion_oxigeno ? $signoVital->saturacion_oxigeno . ' %' : '—' }}</dd>
                    <dt class="font-semibold">Glucosa:</dt>
                    <dd>{{ $signoVital->glucosa ? $signoVital->glucosa . ' mg/dL' : '—' }}</dd>
                    <dt class="font-semibold">Peso:</dt>
                    <dd>{{ $signoVital->peso ? $signoVital->peso . ' kg' : '—' }}</dd>
                    <dt class="font-semibold">Talla:</dt>
                    <dd>{{ $signoVital->talla ? $signoVital->talla . ' m' : '—' }}</dd>
                    <dt class="font-semibold">IMC:</dt>
                    <dd>{{ $signoVital->imc ?? '—' }}</dd>
                    <dt class="font-semibold">Escala de dolor:</dt>
                    <dd>{{ $signoVital->escala_dolor !== null ? $signoVital->escala_dolor . ' / 10' : '—' }}</dd>
                </dl>
            </div>

            @if ($signoVital->motivo_consulta)
                <div>
                    <h4 class="font-semibold text-sm text-gray-700 mb-2">Motivo de consulta</h4>
                    <p class="text-sm text-gray-700 whitespace-pre-line">{{ $signoVital->motivo_consulta }}</p>
                </div>
            @endif

            @if ($signoVital->notas)
                <div>
                    <h4 class="font-semibold text-sm text-gray-700 mb-2">Notas</h4>
                    <p class="text-sm text-gray-700 whitespace-pre-line">{{ $signoVital->notas }}</p>
                </div>
            @endif

            {{-- Asignaciones de cama --}}
            <div>
                <h4 class="font-semibold text-sm text-gray-700 mb-2">Cama asignada</h4>
                @php $asignacion = $signoVital->asignaciones()->where('activa', true)->first(); @endphp
                @if ($asignacion)
                    <div class="p-3 bg-blue-50 border border-blue-200 rounded">
                        <p class="text-sm">
                            <strong>{{ $asignacion->cama->codigo }}</strong>
                            — {{ $asignacion->cama->area }} / Hab. {{ $asignacion->cama->habitacion }}
                        </p>
                        <p class="text-xs text-gray-600 mt-1">
                            Ingreso: {{ $asignacion->fecha_ingreso->format('d/m/Y H:i') }}
                        </p>
                        <form action="{{ route('signos-vitales.liberar-cama', $signoVital) }}" method="POST"
                            onsubmit="return confirm('¿Liberar la cama?')" class="mt-2">
                            @csrf
                            <button class="text-sm text-red-600 hover:underline">Liberar cama</button>
                        </form>
                    </div>
                @else
                    <p class="text-sm text-gray-500 mb-3">Sin cama asignada.</p>

                    @if (in_array($signoVital->triage, ['rojo', 'naranja']))
                        <button type="button" onclick="abrirModalCama()"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md text-sm">
                            Asignar cama urgente
                        </button>
                    @endif
                @endif
            </div>

            <div class="pt-4 flex space-x-2">
                <a href="{{ route('signos-vitales.edit', $signoVital) }}"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm">Editar</a>
                <a href="{{ route('signos-vitales.index') }}"
                    class="px-4 py-2 bg-gray-100 rounded-md text-sm">Volver</a>
            </div>
        </div>
    </div>

    {{-- Modal de asignación de cama --}}
    <div id="modal-cama" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center p-4 border-b">
                <h3 class="text-lg font-bold">Asignar cama al paciente</h3>
                <button onclick="cerrarModalCama()" class="text-gray-500 hover:text-gray-800 text-xl">✕</button>
            </div>

            <form action="{{ route('signos-vitales.asignar-cama', $signoVital) }}" method="POST"
                class="p-4 space-y-4">
                @csrf

                <p class="text-sm text-gray-600">
                    Triage: <strong>{{ $signoVital->triage_label }}</strong>
                </p>

                @if ($camasDisponibles->isEmpty())
                    <p class="text-red-600 text-sm">No hay camas disponibles en este momento.</p>
                @else
                    <div>
                        <label class="block text-sm font-medium">Selecciona una cama disponible</label>
                        <select name="cama_id" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">— Selecciona —</option>
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

                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="cerrarModalCama()"
                            class="px-4 py-2 bg-gray-100 rounded-md text-sm">Cancelar</button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm">
                            Asignar cama
                        </button>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <script>
        function abrirModalCama() {
            document.getElementById('modal-cama').classList.remove('hidden');
        }

        function cerrarModalCama() {
            document.getElementById('modal-cama').classList.add('hidden');
        }

        // Si venimos de un triage rojo/naranja, abrir el modal automáticamente
        @if (session('sugerir_cama') && $camasDisponibles->isNotEmpty())
            abrirModalCama();
        @endif
    </script>
</x-app-layout>
