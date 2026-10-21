<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-3">
            <div class="min-w-0">
                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight truncate">
                    Seguimiento de Pacientes
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Monitoreo continuo de signos vitales y evolución</p>
            </div>
            @can('seguimientos.crear')
                <button type="button"
                    onclick="abrirModalSeguimiento('{{ route('seguimientos.create', $paciente ?? 'nuevo') }}')"
                    class="bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white px-5 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 shadow-sm transition-all shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    Nuevo Seguimiento
                </button>
            @endcan
        </div>
    </x-slot>

    @php
        $inputCls =
            'w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-indigo-500 focus:ring-0 outline-none';
        $labelCls = 'block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5';
        $thCls = 'px-5 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider';
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

        {{-- STATS --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('seguimientos.index', ['filtro' => 'todos']) }}"
                class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm hover:shadow-md hover:border-indigo-200 transition-all active:scale-95 group">
                <div class="flex items-center justify-between">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total en seguimiento</p>
                    <svg class="w-4 h-4 text-slate-300 group-hover:text-indigo-500 transition-colors" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
                <p class="text-3xl font-black text-slate-800 tracking-tight mt-1">{{ $stats['total'] }}</p>
            </a>
            <a href="{{ route('seguimientos.index', ['filtro' => 'cama']) }}"
                class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm hover:shadow-md hover:border-indigo-200 transition-all active:scale-95 group">
                <div class="flex items-center justify-between">
                    <p class="text-[11px] font-bold text-indigo-600/70 uppercase tracking-wider">Con cama asignada</p>
                    <svg class="w-4 h-4 text-slate-300 group-hover:text-indigo-500 transition-colors" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
                <p class="text-3xl font-black text-indigo-600 tracking-tight mt-1">{{ $stats['con_cama'] }}</p>
            </a>
            <a href="{{ route('seguimientos.index', ['filtro' => 'triage']) }}"
                class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm hover:shadow-md hover:border-rose-200 transition-all active:scale-95 group">
                <div class="flex items-center justify-between">
                    <p class="text-[11px] font-bold text-rose-600/70 uppercase tracking-wider">Triage grave (24h)</p>
                    <svg class="w-4 h-4 text-slate-300 group-hover:text-rose-500 transition-colors" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
                <p class="text-3xl font-black text-rose-600 tracking-tight mt-1">{{ $stats['con_triage'] }}</p>
            </a>
        </div>

        {{-- BUSCADOR --}}
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-5">
            <form method="GET" class="flex gap-2 flex-wrap items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="{{ $labelCls }}">Buscar</label>
                    <input type="text" name="buscar" value="{{ $busqueda }}" placeholder="Nombre del paciente"
                        class="{{ $inputCls }}">
                </div>
                <div class="min-w-[180px]">
                    <label class="{{ $labelCls }}">Filtro</label>
                    <select name="filtro" class="{{ $inputCls }}">
                        <option value="todos" @selected($filtro === 'todos')>Todos</option>
                        <option value="cama" @selected($filtro === 'cama')>Con cama</option>
                        <option value="triage" @selected($filtro === 'triage')>Con triage grave</option>
                    </select>
                </div>
                <button type="submit"
                    class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all">
                    Filtrar
                </button>
                @if ($busqueda || $filtro !== 'todos')
                    <a href="{{ route('seguimientos.index') }}"
                        class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition-all">
                        Limpiar
                    </a>
                @endif
            </form>
        </div>

        {{-- TABLA --}}
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="font-extrabold text-slate-800 text-base">Pacientes en seguimiento</h3>
                <p class="text-[11px] text-slate-400">{{ $pacientes->total() }} pacientes activos</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-slate-50/70 border-b border-slate-100">
                        <tr>
                            <th class="{{ $thCls }} text-left">Paciente</th>
                            <th class="{{ $thCls }} text-left">Cama</th>
                            <th class="{{ $thCls }} text-left">Triage</th>
                            <th class="{{ $thCls }} text-left">Último seguimiento</th>
                            <th class="px-5 py-3.5"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($pacientes as $paciente)
                            <tr class="hover:bg-indigo-50/30 transition-colors">
                                <td class="px-5 py-3.5">
                                    <p class="text-xs font-bold text-slate-800">{{ $paciente->nombre_completo }}</p>
                                    <p class="text-[10px] font-medium text-slate-400 mt-0.5">
                                        {{ $paciente->edad }} años — {{ ucfirst($paciente->sexo) }}
                                    </p>
                                </td>
                                <td class="px-5 py-3.5">
                                    @if ($paciente->cama_actual)
                                        <span
                                            class="px-2.5 py-1 bg-indigo-50 text-indigo-700 border border-indigo-100 rounded-full text-[10px] font-bold uppercase tracking-wide">
                                            {{ $paciente->cama_actual->cama->codigo }}
                                        </span>
                                        <p class="text-[10px] font-medium text-slate-400 mt-1">
                                            {{ $paciente->cama_actual->cama->area }}</p>
                                    @else
                                        <span
                                            class="px-2.5 py-1 bg-slate-100 text-slate-500 rounded-full text-[10px] font-bold uppercase tracking-wide">Sin
                                            cama</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    @if ($paciente->ultimoSignoVital)
                                        <span
                                            class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide {{ $paciente->ultimoSignoVital->triage_color }}">
                                            {{ $paciente->ultimoSignoVital->triage_label }}
                                        </span>
                                        <p class="text-[10px] font-medium text-slate-400 mt-1">
                                            {{ $paciente->ultimoSignoVital->created_at->diffForHumans() }}</p>
                                    @else
                                        <span
                                            class="px-2.5 py-1 bg-slate-100 text-slate-500 rounded-full text-[10px] font-bold uppercase tracking-wide">Sin
                                            signos</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    @if ($paciente->seguimientoActual)
                                        <p class="text-[11px] font-medium text-slate-400">
                                            {{ $paciente->seguimientoActual->created_at->diffForHumans() }}</p>
                                        <p class="text-xs font-bold text-slate-700 mt-0.5">
                                            {{ $paciente->seguimientoActual->user?->nombre_completo ?? '—' }}</p>
                                    @else
                                        <span
                                            class="px-2.5 py-1 bg-slate-100 text-slate-500 rounded-full text-[10px] font-bold uppercase tracking-wide">Sin
                                            seguimiento</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex justify-end gap-2 whitespace-nowrap">
                                        <a href="{{ route('seguimientos.show', $paciente) }}"
                                            class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-[11px] font-bold transition-all">
                                            Ver seguimiento
                                        </a>

                                        {{-- BOTÓN "+ NUEVO" MODIFICADO PARA ABRIR EL MODAL --}}
                                        <button type="button"
                                            onclick="abrirModalSeguimiento('{{ route('seguimientos.create', $paciente) }}')"
                                            class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg text-[11px] font-bold transition-all">
                                            + Nuevo
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-12 text-center">
                                    <p class="text-xs font-semibold text-slate-400">Sin pacientes en seguimiento.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">{{ $pacientes->links() }}</div>
    </div>

    {{-- MODAL DE NUEVO SEGUIMIENTO --}}
    <template x-teleport="body">
        <div id="modal-seguimiento" x-data="{ open: false }" x-show="open" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
            <div @click.away="open = false"
                class="bg-white rounded-3xl max-w-3xl w-full p-6 shadow-xl border border-slate-100 max-h-[90vh] overflow-y-auto relative">
                <div id="modal-seguimiento-content">
                    <div class="p-10 text-center text-slate-400">Cargando formulario...</div>
                </div>
            </div>
        </div>
    </template>

    <script>
        function abrirModalSeguimiento(url) {
            const modalElement = document.getElementById('modal-seguimiento');
            Alpine.$data(modalElement).open = true;

            const contenedor = document.getElementById('modal-seguimiento-content');
            contenedor.innerHTML =
                '<div class="p-10 text-center text-slate-400"><svg class="animate-spin h-6 w-6 mx-auto text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></div>';

            fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.text())
                .then(html => contenedor.innerHTML = html);
        }
    </script>
</x-app-layout>



////////////////////////////////////////////////

ir a resources/views/seguimientos/create.blade.php y remplazar el código por este

<div class="p-2">
    {{-- Encabezado con Botón Cerrar --}}
    <div class="flex justify-between items-center mb-5 border-b border-slate-100 pb-4">
        <div class="min-w-0">
            <h3 class="text-lg font-bold text-slate-800">Nuevo seguimiento — {{ $paciente->nombre_completo }}</h3>
            <p class="text-xs text-slate-500 mt-0.5">Registra la evolución y signos vitales del paciente</p>
        </div>
        <button type="button" onclick="Alpine.$data(document.getElementById('modal-seguimiento')).open = false"
            class="w-9 h-9 flex items-center justify-center rounded-full bg-slate-50 text-slate-400 hover:text-rose-500 hover:bg-rose-50 transition-colors text-xl leading-none border border-slate-200 shrink-0">&times;</button>
    </div>

    @php
        $inputCls =
            'w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-indigo-500 focus:ring-0 outline-none';
        $labelCls = 'block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5';
        $errorCls = 'text-rose-600 text-xs mt-1 font-medium';
    @endphp

    <form action="{{ route('seguimientos.store', $paciente) }}" method="POST" class="space-y-6">
        @csrf

        @if (session('error'))
            <div
                class="p-4 bg-rose-50 border border-rose-100 text-rose-800 rounded-2xl text-xs font-semibold flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- Tipo + Estado --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="{{ $labelCls }}">Tipo de nota *</label>
                <select name="tipo" required class="{{ $inputCls }}">
                    <option value="evolucion" @selected(old('tipo') == 'evolucion')>Evolución médica</option>
                    <option value="nota_enfermeria" @selected(old('tipo') == 'nota_enfermeria')>Nota de enfermería</option>
                    <option value="interconsulta" @selected(old('tipo') == 'interconsulta')>Interconsulta</option>
                    <option value="traslado" @selected(old('tipo') == 'traslado')>Traslado</option>
                    <option value="alta" @selected(old('tipo') == 'alta')>Alta</option>
                </select>
                @error('tipo')
                    <p class="{{ $errorCls }}">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="{{ $labelCls }}">Estado del paciente</label>
                <select name="estado_paciente" class="{{ $inputCls }}">
                    <option value="">— Sin especificar —</option>
                    <option value="estable" @selected(old('estado_paciente') == 'estable')>Estable</option>
                    <option value="mejorando" @selected(old('estado_paciente') == 'mejorando')>Mejorando</option>
                    <option value="grave" @selected(old('estado_paciente') == 'grave')>Grave</option>
                    <option value="critico" @selected(old('estado_paciente') == 'critico')>Crítico</option>
                    <option value="fallecido" @selected(old('estado_paciente') == 'fallecido')>Fallecido</option>
                </select>
                @error('estado_paciente')
                    <p class="{{ $errorCls }}">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Contenido --}}
        <div>
            <label class="{{ $labelCls }}">Contenido de la nota *</label>
            <textarea name="contenido" rows="6" required placeholder="Describe la evolución del paciente..."
                class="{{ $inputCls }} resize-none">{{ old('contenido') }}</textarea>
            @error('contenido')
                <p class="{{ $errorCls }}">{{ $message }}</p>
            @enderror
        </div>

        {{-- Signos vitales --}}
        <div class="pt-5 border-t border-slate-100">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-8 h-8 rounded-xl bg-rose-50 flex items-center justify-center text-rose-500 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-800 text-sm">Signos vitales del momento *</h3>
                    <p class="text-[11px] text-slate-400">Completa todos los campos para un registro clínico correcto
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                <div>
                    <label class="{{ $labelCls }}">Temperatura (°C) *</label>
                    <input type="number" step="0.1" min="34" max="42" name="temperatura"
                        value="{{ old('temperatura') }}" required class="{{ $inputCls }}">
                    @error('temperatura')
                        <p class="{{ $errorCls }}">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="{{ $labelCls }}">Frec. cardíaca *</label>
                    <input type="number" min="30" max="200" name="frecuencia_cardiaca"
                        value="{{ old('frecuencia_cardiaca') }}" required class="{{ $inputCls }}">
                    @error('frecuencia_cardiaca')
                        <p class="{{ $errorCls }}">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="{{ $labelCls }}">Frec. respiratoria *</label>
                    <input type="number" min="8" max="60" name="frecuencia_respiratoria"
                        value="{{ old('frecuencia_respiratoria') }}" required class="{{ $inputCls }}">
                    @error('frecuencia_respiratoria')
                        <p class="{{ $errorCls }}">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="{{ $labelCls }}">Presión arterial *</label>
                    <input type="text" name="presion_arterial" value="{{ old('presion_arterial') }}"
                        placeholder="120/80" pattern="\d{2,3}/\d{2,3}" required class="{{ $inputCls }}">
                    @error('presion_arterial')
                        <p class="{{ $errorCls }}">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="{{ $labelCls }}">Saturación O₂ (%) *</label>
                    <input type="number" min="50" max="100" name="saturacion_oxigeno"
                        value="{{ old('saturacion_oxigeno') }}" required class="{{ $inputCls }}">
                    @error('saturacion_oxigeno')
                        <p class="{{ $errorCls }}">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Botones --}}
        <div class="pt-5 border-t border-slate-100 flex flex-wrap justify-end items-center gap-3">
            <button type="button" onclick="Alpine.$data(document.getElementById('modal-seguimiento')).open = false"
                class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-600 rounded-xl text-xs font-bold transition-all">
                Cancelar
            </button>
            <button type="submit"
                class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all inline-flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
                Guardar seguimiento
            </button>
        </div>
    </form>
</div>


////////////////////////////////
ir a resources/views/seguimientos/show.blade.php y remplazar el código por este
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-3">
            <div class="min-w-0">
                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight truncate">
                    Seguimiento — {{ $paciente->nombre_completo }}
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Historial clínico y evolución del paciente</p>
            </div>
            <a href="{{ route('seguimientos.index') }}"
                class="bg-white hover:bg-indigo-50 text-slate-700 hover:text-indigo-600 border border-slate-200 hover:border-indigo-200 px-5 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 transition-all active:scale-95 group shrink-0">
                <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
                Volver
            </a>
        </div>
    </x-slot>

    @php
        $labelCls = 'text-[11px] font-bold text-slate-400 uppercase tracking-wider';
    @endphp

    <div class="py-8 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

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

        {{-- ============ FICHA DEL PACIENTE ============ --}}
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-5">
            <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-5">
                <div class="flex-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        @if ($paciente->ultimoSignoVital)
                            <span
                                class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide {{ $paciente->ultimoSignoVital->triage_color }}">
                                {{ $paciente->ultimoSignoVital->triage_label }}
                            </span>
                        @endif
                    </div>

                    <p class="{{ $labelCls }} mt-4">Paciente</p>
                    <p class="text-3xl font-black text-slate-800 tracking-tight mt-0.5">
                        {{ $paciente->nombre_completo }}
                    </p>
                    <p class="text-xs text-slate-400 mt-1">
                        {{ $paciente->edad }} años — {{ ucfirst($paciente->sexo) }}
                    </p>
                </div>

                <div class="md:text-right space-y-2 md:max-w-[220px]">
                    @if ($camaActual)
                        <div class="bg-indigo-50/60 border border-indigo-100 rounded-2xl p-3 md:text-right">
                            <p class="{{ $labelCls }}">Cama actual</p>
                            <p class="text-sm font-extrabold text-indigo-600 mt-0.5">
                                {{ $camaActual->cama->codigo }}
                            </p>
                            <p class="text-[10px] font-medium text-slate-400">{{ $camaActual->cama->area }}</p>
                        </div>
                    @else
                        <div class="bg-slate-50 border border-slate-100 rounded-2xl p-3 md:text-right">
                            <p class="{{ $labelCls }}">Cama actual</p>
                            <p class="text-xs font-bold text-slate-500 mt-0.5">Sin cama</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Alergias y crónicas --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-4 border-t border-slate-100">
                <div class="bg-rose-50/60 border border-rose-100 rounded-2xl p-4">
                    <div class="flex items-center gap-2">
                        <div
                            class="w-7 h-7 rounded-lg bg-rose-100 flex items-center justify-center text-rose-600 shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <p class="text-[11px] font-bold text-rose-700 uppercase tracking-wider">Alergias</p>
                    </div>
                    <p class="text-xs font-bold text-slate-800 mt-2">{{ $paciente->alergias ?? 'Ninguna' }}</p>
                </div>
                <div class="bg-amber-50/60 border border-amber-100 rounded-2xl p-4">
                    <div class="flex items-center gap-2">
                        <div
                            class="w-7 h-7 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600 shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <p class="text-[11px] font-bold text-amber-700 uppercase tracking-wider">Enfermedades crónicas
                        </p>
                    </div>
                    <p class="text-xs font-bold text-slate-800 mt-2">
                        {{ $paciente->enfermedades_cronicas ?? 'Ninguna' }}</p>
                </div>
            </div>

            {{-- Acciones --}}
            <div class="pt-5 border-t border-slate-100 flex flex-wrap gap-2">
                {{-- 🔴 BOTÓN MODIFICADO PARA ABRIR EL MODAL --}}
                <button type="button" onclick="abrirModalSeguimiento('{{ route('seguimientos.create', $paciente) }}')"
                    class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    Nuevo seguimiento
                </button>
                <a href="{{ route('expedientes.show', $paciente) }}"
                    class="px-4 py-2.5 bg-slate-800 hover:bg-slate-900 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Ver expediente completo
                </a>
            </div>
        </div>

        {{-- ============ TIMELINE ============ --}}
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="font-extrabold text-slate-800 text-base">Historial de seguimientos</h3>
                <p class="text-[11px] text-slate-400">{{ $seguimientos->count() }} registros clínicos</p>
            </div>

            @if ($seguimientos->isEmpty())
                <div class="px-5 py-12 text-center">
                    <p class="text-xs font-semibold text-slate-400">Sin seguimientos registrados.</p>
                </div>
            @else
                <div class="p-5 space-y-4">
                    @foreach ($seguimientos as $seg)
                        <div
                            class="relative bg-slate-50/70 border border-slate-100 rounded-2xl p-4 hover:border-indigo-200 hover:bg-indigo-50/20 transition-all">
                            <span class="absolute left-0 top-4 bottom-4 w-1 rounded-r-full bg-indigo-500"></span>

                            <div class="pl-4">
                                <div class="flex flex-wrap justify-between items-start gap-3">
                                    <div class="min-w-0">
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                            {{ $seg->created_at->format('d/m/Y H:i') }}
                                        </p>
                                        <div class="flex items-center gap-2 flex-wrap mt-1">
                                            <p class="text-xs font-extrabold text-slate-800">{{ $seg->tipo_label }}
                                            </p>
                                            @if ($seg->estado_paciente)
                                                <span
                                                    class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide {{ $seg->estado_color }}">
                                                    {{ ucfirst($seg->estado_paciente) }}
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-[11px] font-medium text-slate-400 mt-1">
                                            Por {{ $seg->user?->nombre_completo ?? 'Sistema' }}
                                            @if ($seg->cama)
                                                <span class="text-slate-300">·</span> Cama {{ $seg->cama->codigo }}
                                            @endif
                                        </p>
                                    </div>

                                    <form action="{{ route('seguimientos.destroy', $seg) }}" method="POST"
                                        onsubmit="return confirm('¿Eliminar este seguimiento?')">
                                        @csrf @method('DELETE')
                                        <button
                                            class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-[11px] font-bold transition-all">
                                            Eliminar
                                        </button>
                                    </form>
                                </div>

                                <p
                                    class="text-xs text-slate-600 leading-relaxed whitespace-pre-line mt-3 bg-white border border-slate-100 rounded-xl p-3">
                                    {{ $seg->contenido }}
                                </p>

                                {{-- Signos vitales --}}
                                @if ($seg->temperatura || $seg->frecuencia_cardiaca || $seg->presion_arterial)
                                    <div class="mt-3 grid grid-cols-2 md:grid-cols-5 gap-2">
                                        @if ($seg->temperatura)
                                            <div class="bg-white border border-slate-100 rounded-xl p-2 text-center">
                                                <p
                                                    class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">
                                                    Temp</p>
                                                <p class="text-xs font-extrabold text-slate-800 mt-0.5">
                                                    {{ $seg->temperatura }} °C</p>
                                            </div>
                                        @endif
                                        @if ($seg->frecuencia_cardiaca)
                                            <div class="bg-white border border-slate-100 rounded-xl p-2 text-center">
                                                <p
                                                    class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">
                                                    FC</p>
                                                <p class="text-xs font-extrabold text-slate-800 mt-0.5">
                                                    {{ $seg->frecuencia_cardiaca }} lpm</p>
                                            </div>
                                        @endif
                                        @if ($seg->frecuencia_respiratoria)
                                            <div class="bg-white border border-slate-100 rounded-xl p-2 text-center">
                                                <p
                                                    class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">
                                                    FR</p>
                                                <p class="text-xs font-extrabold text-slate-800 mt-0.5">
                                                    {{ $seg->frecuencia_respiratoria }} rpm</p>
                                            </div>
                                        @endif
                                        @if ($seg->presion_arterial)
                                            <div class="bg-white border border-slate-100 rounded-xl p-2 text-center">
                                                <p
                                                    class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">
                                                    TA</p>
                                                <p class="text-xs font-extrabold text-slate-800 mt-0.5">
                                                    {{ $seg->presion_arterial }}</p>
                                            </div>
                                        @endif
                                        @if ($seg->saturacion_oxigeno)
                                            <div class="bg-white border border-slate-100 rounded-xl p-2 text-center">
                                                <p
                                                    class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">
                                                    SpO₂</p>
                                                <p class="text-xs font-extrabold text-slate-800 mt-0.5">
                                                    {{ $seg->saturacion_oxigeno }}%</p>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- MODAL DE NUEVO SEGUIMIENTO --}}
    <template x-teleport="body">
        <div id="modal-seguimiento" x-data="{ open: false }" x-show="open" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
            <div @click.away="open = false"
                class="bg-white rounded-3xl max-w-3xl w-full p-6 shadow-xl border border-slate-100 max-h-[90vh] overflow-y-auto relative">
                <div id="modal-seguimiento-content">
                    <div class="p-10 text-center text-slate-400">Cargando formulario...</div>
                </div>
            </div>
        </div>
    </template>

    <script>
        // Esta función debe coincidir con la que pusiste en el index
        function abrirModalSeguimiento(url) {
            const modalElement = document.getElementById('modal-seguimiento');
            Alpine.$data(modalElement).open = true;

            const contenedor = document.getElementById('modal-seguimiento-content');
            contenedor.innerHTML =
                '<div class="p-10 text-center text-slate-400"><svg class="animate-spin h-6 w-6 mx-auto text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></div>';

            fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.text())
                .then(html => contenedor.innerHTML = html);
        }
    </script>
</x-app-layout>
