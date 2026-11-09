<x-app-layout>
    <div x-data="{ modal: null, url: '' }" class="relative">

        <x-slot name="header">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    Estudios e Imagenología
                </h2>
                <p class="text-sm text-slate-500 mt-1">Control de solicitudes, resultados y archivos clínicos</p>
            </div>
        </x-slot>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

                @if (session('success'))
                    <div
                        class="p-4 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-xl flex items-center gap-3">
                        <x-heroicon-o-check-circle class="w-5 h-5" />
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="p-4 bg-rose-50 text-rose-700 border border-rose-200 rounded-xl flex items-center gap-3">
                        <x-heroicon-o-x-circle class="w-5 h-5" />
                        {{ session('error') }}
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center gap-4">
                        <div
                            class="w-12 h-12 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center shrink-0">
                            <x-heroicon-o-clipboard-document-list class="w-6 h-6" />
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Solicitados</p>
                            <p class="text-2xl font-extrabold text-slate-800">{{ $stats['solicitados'] }}</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center gap-4">
                        <div
                            class="w-12 h-12 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center shrink-0">
                            <x-heroicon-o-arrow-path class="w-6 h-6" />
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">En Proceso</p>
                            <p class="text-2xl font-extrabold text-slate-800">{{ $stats['en_proceso'] }}</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center gap-4">
                        <div
                            class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center shrink-0">
                            <x-heroicon-o-document-check class="w-6 h-6" />
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Con Resultado</p>
                            <p class="text-2xl font-extrabold text-slate-800">{{ $stats['con_resultado'] }}</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center gap-4">
                        <div
                            class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-500 flex items-center justify-center shrink-0">
                            <x-heroicon-o-calendar-days class="w-6 h-6" />
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total del Mes</p>
                            <p class="text-2xl font-extrabold text-slate-800">{{ $stats['total_mes'] }}</p>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-wrap items-center gap-3">
                    <form method="GET" class="flex-1 flex flex-wrap gap-3 items-center w-full">
                        <div class="relative flex-1 min-w-[250px]">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <x-heroicon-o-magnifying-glass class="h-5 w-5 text-slate-400" />
                            </div>
                            <input type="text" name="buscar" value="{{ $buscar }}"
                                placeholder="Buscar por folio, paciente o estudio..."
                                class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:border-brand-500 focus:ring-0 transition-colors">
                        </div>

                        <select name="estado"
                            class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-brand-500 focus:ring-0">
                            <option value="todas">Todos los estados</option>
                            @foreach (['solicitado', 'en_proceso', 'resultado_listo', 'entregado', 'cancelado'] as $e)
                                <option value="{{ $e }}" @selected($estado === $e)>
                                    {{ ucfirst(str_replace('_', ' ', $e)) }}
                                </option>
                            @endforeach
                        </select>

                        <select name="tipo_servicio"
                            class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-brand-500 focus:ring-0">
                            <option value="todos">Todos los servicios</option>
                            <option value="laboratorio" @selected($servicioTipo === 'laboratorio')>Laboratorio</option>
                            <option value="imagenes" @selected($servicioTipo === 'imagenes')>Imagenología</option>
                        </select>

                        <button type="submit"
                            class="px-6 py-2.5 bg-brand-700 hover:bg-brand-800 text-white font-semibold text-sm rounded-xl transition-colors shrink-0">
                            Filtrar
                        </button>

                        @if (auth()->user()->can('estudios.crear') && !auth()->user()->hasRole('medico'))
                            <button type="button"
                                @click="modal = 'crear'; url = '{{ route('estudios.create') }}'; fetch(url).then(r => r.text()).then(h => { document.getElementById('modal-content').innerHTML = h; setTimeout(() => window.Alpine.initTree(document.getElementById('modal-content')), 50); })"
                                class="px-6 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-semibold text-sm rounded-xl transition-colors shrink-0 flex items-center gap-2">
                                <x-heroicon-o-plus class="w-4 h-4" />
                                Nueva Solicitud
                            </button>
                        @endif
                    </form>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead
                                class="bg-slate-50 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-200">
                                <tr>
                                    <th class="px-6 py-4">Folio</th>
                                    <th class="px-6 py-4">Paciente</th>
                                    <th class="px-6 py-4">Estudio / Servicio</th>
                                    <th class="px-6 py-4 text-center">Archivos</th>
                                    <th class="px-6 py-4">Estado</th>
                                    @if (auth()->user()->hasRole('administrador'))
                                        <th class="px-6 py-4">Solicita</th>
                                    @endif
                                    <th class="px-6 py-4 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($solicitudes as $s)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="px-6 py-4">
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-mono text-xs font-semibold">
                                                {{ $s->folio }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-9 h-9 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center text-xs font-bold shrink-0">
                                                    {{ substr($s->paciente?->nombre ?? 'N', 0, 1) }}{{ substr($s->paciente?->apellido_paterno ?? 'A', 0, 1) }}
                                                </div>
                                                <div>
                                                    <p class="font-bold text-slate-800">
                                                        {{ $s->paciente?->nombre_completo }}</p>
                                                    <p class="text-xs text-slate-400">ID: #{{ $s->paciente_id }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="font-medium text-slate-700">{{ $s->servicio?->nombre }}</p>
                                            <p class="text-xs text-slate-400 truncate max-w-[200px]">
                                                {{ $s->tipo_estudio }}</p>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @if ($s->archivos->count() > 0)
                                                <span
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-50 text-blue-600 font-bold text-xs">
                                                    {{ $s->archivos->count() }}
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-slate-50 text-slate-400 font-bold text-xs">
                                                    0
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <span @class([
                                                'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold uppercase tracking-wide',
                                                'bg-amber-100 text-amber-700' => $s->estado === 'solicitado',
                                                'bg-blue-100 text-blue-700' => $s->estado === 'en_proceso',
                                                'bg-emerald-100 text-emerald-700' => $s->estado === 'resultado_listo',
                                                'bg-slate-200 text-slate-700' => $s->estado === 'entregado',
                                                'bg-rose-100 text-rose-700' => $s->estado === 'cancelado',
                                            ])>
                                                <span @class([
                                                    'w-1.5 h-1.5 rounded-full',
                                                    'bg-amber-500' => $s->estado === 'solicitado',
                                                    'bg-blue-500 animate-pulse' => $s->estado === 'en_proceso',
                                                    'bg-emerald-500' => $s->estado === 'resultado_listo',
                                                    'bg-slate-500' => $s->estado === 'entregado',
                                                    'bg-rose-500' => $s->estado === 'cancelado',
                                                ])></span>
                                                {{ str_replace('_', ' ', $s->estado) }}
                                            </span>
                                        </td>
                                        @if (auth()->user()->hasRole('administrador'))
                                            <td class="px-6 py-4">
                                                <p class="text-sm font-medium text-slate-700">
                                                    {{ $s->medicoSolicita?->name ?? 'N/A' }}</p>
                                                <p class="text-xs text-slate-400">
                                                    {{ $s->created_at->format('d/m/Y h:i A') }}</p>
                                            </td>
                                        @endif
                                        <td class="px-6 py-4 text-right">
                                            <button type="button"
                                                @click="modal = 'ver'; url = '{{ route('estudios.show', $s) }}'; fetch(url).then(r => r.text()).then(h => { document.getElementById('modal-content').innerHTML = h; setTimeout(() => window.Alpine.initTree(document.getElementById('modal-content')), 50); })"
                                                class="inline-flex items-center justify-center px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-xl transition-colors">
                                                Ver detalle
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ auth()->user()->hasRole('administrador') ? 7 : 6 }}"
                                            class="px-6 py-12 text-center">
                                            <div
                                                class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 mb-4">
                                                <x-heroicon-o-beaker class="w-8 h-8 text-slate-300" />
                                            </div>
                                            <p class="text-slate-500 font-medium">No hay solicitudes de estudios
                                                registradas</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-4">
                    {{ $solicitudes->links() }}
                </div>
            </div>
        </div>

        <div x-show="modal !== null" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
            <div x-show="modal !== null" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm"
                @click="modal = null"></div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div x-show="modal !== null" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative w-full max-w-4xl bg-slate-50 rounded-2xl shadow-xl overflow-hidden flex flex-col max-h-[90vh]">

                    <div
                        class="bg-white px-6 py-4 border-b border-slate-200 flex items-center justify-between shrink-0">
                        <h3 class="font-bold text-lg text-slate-800"
                            x-text="modal === 'crear' ? 'Nueva Solicitud' : 'Detalle del Estudio'"></h3>
                        <button @click="modal = null"
                            class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-full transition-colors">
                            <x-heroicon-o-x-mark class="w-5 h-5" />
                        </button>
                    </div>

                    <div id="modal-content" class="p-6 overflow-y-auto">
                        <div class="flex justify-center py-10">
                            <x-heroicon-o-arrow-path class="w-8 h-8 text-brand-500 animate-spin" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            window.Alpine.data('buscadorPaciente', () => ({
                query: '',
                resultados: [],
                abierto: false,
                buscando: false,
                seleccionadoId: null,
                seleccionado: null,

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
            }));
        });

        document.addEventListener('change', function(e) {
            if (e.target && e.target.id === 'servicio_id') {
                const opt = e.target.selectedOptions[0];
                const p = opt?.dataset.precio;
                const precioInfo = document.getElementById('precio-info');
                if (precioInfo) {
                    precioInfo.textContent = p ? `Costo: $${parseFloat(p).toFixed(2)}` : '';
                }
            }
        });

        document.addEventListener('cerrar-modal', () => {
            document.querySelector('[x-data]').__x.$data.modal = null;
        });
    </script>
</x-app-layout>
