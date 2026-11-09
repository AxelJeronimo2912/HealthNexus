<div x-data="buscadorPaciente()">
    @if ($errors->any())
        <div class="mb-6 p-4 bg-rose-50 border border-rose-200 rounded-xl">
            <ul class="list-disc list-inside text-sm text-rose-700 font-medium">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('estudios.store') }}" class="space-y-5">
        @csrf

        <div class="relative">
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Paciente *</label>
            <input type="hidden" name="paciente_id" x-model="seleccionadoId">

            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <x-heroicon-o-magnifying-glass class="h-5 w-5 text-slate-400" />
                </div>
                <input type="text" x-model="query" @input.debounce.300ms="buscar" @focus="abierto = true"
                    placeholder="Buscar por nombre, apellido, CURP o ID..."
                    class="w-full pl-10 pr-4 py-3 bg-white border border-slate-200 rounded-xl text-sm focus:border-brand-500 focus:ring-0 transition-colors">
            </div>

            <template x-if="seleccionado">
                <div class="mt-3 p-3 bg-brand-50 border border-brand-200 rounded-xl flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-check-circle class="w-5 h-5 text-brand-600" />
                        <span class="font-bold text-brand-900 text-sm" x-text="seleccionado.nombre"></span>
                        <span class="text-xs text-brand-600 font-medium"
                            x-text="seleccionado.curp ? '— ' + seleccionado.curp : ''"></span>
                    </div>
                    <button type="button" @click="limpiar"
                        class="text-xs font-bold text-rose-600 hover:underline">Cambiar</button>
                </div>
            </template>

            <div x-show="abierto && resultados.length > 0" x-cloak @click.outside="abierto = false"
                class="absolute z-50 mt-2 w-full bg-white border border-slate-200 rounded-xl shadow-lg max-h-60 overflow-y-auto">
                <template x-for="p in resultados" :key="p.id">
                    <button type="button" @click="elegir(p)"
                        class="block w-full text-left px-4 py-3 hover:bg-slate-50 text-sm border-b border-slate-100 last:border-0 transition-colors">
                        <span class="font-bold text-slate-800" x-text="p.nombre"></span>
                        <span class="text-slate-400 text-xs ml-2" x-text="p.curp ? '— ' + p.curp : ''"></span>
                    </button>
                </template>
            </div>
            <p x-show="buscando" x-cloak class="text-xs font-medium text-brand-600 mt-2 animate-pulse">Buscando
                paciente...</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Servicio *</label>
                <select name="servicio_id" id="servicio_id" required
                    class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm focus:border-brand-500 focus:ring-0 transition-colors">
                    <option value="">— Seleccionar —</option>
                    @foreach ($servicios->groupBy('tipo') as $tipo => $grupo)
                        <optgroup label="{{ ucfirst($tipo) }}">
                            @foreach ($grupo as $s)
                                <option value="{{ $s->id }}" data-precio="{{ $s->precio }}"
                                    @selected(old('servicio_id') == $s->id)>
                                    {{ $s->nombre }} — ${{ number_format($s->precio, 2) }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
                <p id="precio-info" class="text-xs font-bold text-brand-600 mt-2"></p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tipo de estudio
                    *</label>
                <input type="text" name="tipo_estudio" value="{{ old('tipo_estudio') }}" required
                    placeholder="Ej. Radiografía de tórax PA"
                    class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm focus:border-brand-500 focus:ring-0 transition-colors">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Indicaciones
                clínicas</label>
            <textarea name="indicaciones" rows="2" placeholder="Detalles clínicos..."
                class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm focus:border-brand-500 focus:ring-0 transition-colors">{{ old('indicaciones') }}</textarea>
        </div>

        <div class="flex items-center gap-3 p-4 bg-white border border-slate-200 rounded-xl">
            <input type="checkbox" name="cargar_a_cuenta" id="cargar_a_cuenta" value="1" checked
                class="w-4 h-4 text-brand-600 border-slate-300 rounded focus:ring-brand-500">
            <label for="cargar_a_cuenta" class="text-sm font-medium text-slate-700 cursor-pointer">
                Cargar automáticamente a la cuenta del paciente
            </label>
        </div>

        <div class="pt-4 border-t border-slate-200 flex justify-end gap-3">
            {{-- Al cancelar, disparamos el evento para cerrar el modal --}}
            <button type="button" @click="$dispatch('cerrar-modal')"
                class="px-6 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-bold rounded-xl transition-colors">
                Cancelar
            </button>
            <button type="submit" :disabled="!seleccionadoId"
                class="px-6 py-2.5 bg-brand-700 hover:bg-brand-800 text-white text-sm font-bold rounded-xl transition-colors shadow-sm disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                <x-heroicon-o-check class="w-4 h-4" />
                Crear Solicitud
            </button>
        </div>
    </form>
</div>

<script>
    function buscadorPaciente() {
        return {
            query: '',
            resultados: [],
            abierto: false,
            buscando: false,
            seleccionadoId: @js($paciente?->id ?? old('paciente_id')),
            seleccionado: @js($paciente ? ['id' => $paciente->id, 'nombre' => $paciente->nombre_completo ?? trim("{$paciente->nombre} {$paciente->apellido_paterno} {$paciente->apellido_materno}"), 'curp' => $paciente->curp] : null),

            async buscar() {
                if (this.query.trim().length < 2) {
                    this.resultados = [];
                    return;
                }
                this.buscando = true;
                try {
                    const res = await fetch(
                        `{{ route('estudios.buscar-pacientes') }}?q=${encodeURIComponent(this.query)}`, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });
                    this.resultados = await res.json();
                    this.abierto = true;
                } catch (e) {
                    this.resultados = [];
                } finally {
                    this.buscando = false;
                }
            },
            elegir(p) {
                this.seleccionadoId = p.id;
                this.seleccionado = p;
                this.query = '';
                this.abierto = false;
                this.resultados = [];
            },
            limpiar() {
                this.seleccionadoId = null;
                this.seleccionado = null;
            }
        }
    }

    setTimeout(() => {
        const sel = document.getElementById('servicio_id');
        if (sel) {
            sel.addEventListener('change', function() {
                const opt = this.selectedOptions[0];
                const p = opt?.dataset.precio;
                document.getElementById('precio-info').textContent = p ?
                    `Costo: $${parseFloat(p).toFixed(2)}` : '';
            });
        }
    }, 100);
</script>
