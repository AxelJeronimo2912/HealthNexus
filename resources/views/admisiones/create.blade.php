<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Nueva Admisión</h2>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <form action="{{ route('admisiones.store') }}" method="POST" class="space-y-6 bg-white p-6 rounded-lg shadow"
            id="form-admision">
            @csrf

            {{-- Paciente --}}
            <div>
                <label class="block text-sm font-medium">Paciente *</label>
                <select name="paciente_id" id="paciente_id" required
                    class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                    <option value="">— Selecciona un paciente —</option>
                    @foreach ($pacientes as $p)
                        @php
                            $signo = $p->signosVitales->first();
                            $triageLabel = match ($signo?->triage) {
                                'rojo' => '🔴 Rojo',
                                'naranja' => '🟠 Naranja',
                                'amarillo' => '🟡 Amarillo',
                                'verde' => '🟢 Verde',
                                'azul' => '🔵 Azul',
                                default => '— Sin triage',
                            };
                        @endphp
                        <option value="{{ $p->id }}" data-triage="{{ $signo?->triage ?? '' }}"
                            @selected(old('paciente_id') == $p->id)>
                            {{ $p->nombre_completo }} — {{ $triageLabel }}
                            @if ($signo)
                                ({{ $signo->created_at->format('d/m/Y') }})
                            @endif
                        </option>
                    @endforeach
                </select>
                @error('paciente_id')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Triage actual del paciente --}}
            <div id="panel-triage" class="hidden p-3 rounded border bg-gray-50">
                <p class="text-xs text-gray-500 uppercase tracking-wide">Triage registrado en el último signo vital</p>
                <p id="triage-actual" class="text-lg font-semibold mt-1">—</p>
                <p id="triage-fecha" class="text-xs text-gray-500"></p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Tipo de admisión --}}
                <div>
                    <label class="block text-sm font-medium">Tipo de admisión *</label>
                    <select name="tipo" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                        <option value="urgencias" @selected(old('tipo') == 'urgencias')>Urgencias</option>
                        <option value="consulta_externa" @selected(old('tipo') == 'consulta_externa')>Consulta Externa</option>
                        <option value="hospitalizacion" @selected(old('tipo') == 'hospitalizacion')>Hospitalización</option>
                        <option value="traslado" @selected(old('tipo') == 'traslado')>Traslado</option>
                    </select>
                </div>

                {{-- Triage (se preselecciona automáticamente) --}}
                <div>
                    <label class="block text-sm font-medium">Triage</label>
                    <select name="triage" id="triage" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">— Usar triage del último signo vital —</option>
                        <option value="rojo" @selected(old('triage') == 'rojo')>🔴 Rojo — Emergencia</option>
                        <option value="naranja" @selected(old('triage') == 'naranja')>🟠 Naranja — Muy urgente</option>
                        <option value="amarillo" @selected(old('triage') == 'amarillo')>🟡 Amarillo — Urgente</option>
                        <option value="verde" @selected(old('triage') == 'verde')>🟢 Verde — No urgente</option>
                        <option value="azul" @selected(old('triage') == 'azul')>🔵 Azul — Baja prioridad</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">
                        Si no seleccionas nada, se usará el triage del último signo vital registrado.
                    </p>
                </div>
            </div>

            {{-- Motivo --}}
            <div>
                <label class="block text-sm font-medium">Motivo de la admisión *</label>
                <textarea name="motivo" rows="3" required placeholder="Razón por la que llega el paciente..."
                    class="mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('motivo') }}</textarea>
                @error('motivo')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Diagnóstico presuntivo --}}
            <div>
                <label class="block text-sm font-medium">Diagnóstico presuntivo</label>
                <textarea name="diagnostico_presuntivo" rows="2" placeholder="Diagnóstico inicial (si se conoce)"
                    class="mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('diagnostico_presuntivo') }}</textarea>
            </div>

            {{-- Notas --}}
            <div>
                <label class="block text-sm font-medium">Notas adicionales</label>
                <textarea name="notas" rows="2" class="mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('notas') }}</textarea>
            </div>

            {{-- Botones --}}
            <div class="flex justify-between items-center pt-4 border-t">
                <a href="{{ route('admisiones.index') }}"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">Cancelar</a>
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium">
                    Registrar Admisión
                </button>
            </div>
        </form>
    </div>

    {{-- Script para actualizar el triage dinámicamente --}}
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const selectPaciente = document.getElementById('paciente_id');
                const selectTriage = document.getElementById('triage');
                const panelTriage = document.getElementById('panel-triage');
                const triageActual = document.getElementById('triage-actual');
                const triageFecha = document.getElementById('triage-fecha');

                const labels = {
                    rojo: '🔴 Rojo — Emergencia',
                    naranja: '🟠 Naranja — Muy urgente',
                    amarillo: '🟡 Amarillo — Urgente',
                    verde: '🟢 Verde — No urgente',
                    azul: '🔵 Azul — Baja prioridad',
                };

                const colores = {
                    rojo: 'bg-red-100 text-red-800 border-red-300',
                    naranja: 'bg-orange-100 text-orange-800 border-orange-300',
                    amarillo: 'bg-yellow-100 text-yellow-800 border-yellow-300',
                    verde: 'bg-green-100 text-green-800 border-green-300',
                    azul: 'bg-blue-100 text-blue-800 border-blue-300',
                };

                function actualizarTriage() {
                    const opt = selectPaciente.options[selectPaciente.selectedIndex];
                    const triage = opt?.dataset?.triage;

                    if (!triage) {
                        panelTriage.classList.add('hidden');
                        return;
                    }

                    panelTriage.classList.remove('hidden');
                    triageActual.textContent = labels[triage] || triage;

                    // Quitar colores previos
                    Object.values(colores).forEach(c => panelTriage.classList.remove(...c.split(' ')));
                    // Aplicar color
                    panelTriage.classList.add(...colores[triage].split(' '));

                    // Preseleccionar el triage en el select si el usuario no ha elegido uno
                    if (!selectTriage.dataset.touched) {
                        selectTriage.value = triage;
                    }
                }

                // Detectar cambio manual del triage
                selectTriage.addEventListener('change', function() {
                    this.dataset.touched = '1';
                });

                selectPaciente.addEventListener('change', actualizarTriage);

                // Al cargar, si hay un paciente preseleccionado (old), actualizar
                if (selectPaciente.value) actualizarTriage();
            });
        </script>
    @endpush
</x-app-layout>
