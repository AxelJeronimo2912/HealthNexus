<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight">Expedientes</h2>
                <p class="text-xs text-slate-400 mt-0.5">Historial clínico y consultas por paciente</p>
            </div>

            <a href="{{ route('pacientes.index') }}"
                class="bg-white hover:bg-brand-50 text-slate-700 hover:text-brand-600 border border-slate-200 hover:border-brand-200 px-5 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 transition-all active:scale-95 group">
                Ver todos los pacientes
                <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </x-slot>

    {{-- Contenedor principal con Alpine para el modal --}}
    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6" x-data="pacientesIndex()" x-init="init()">

        {{-- Barra de búsqueda + contador --}}
        <div
            class="bg-white border border-slate-100 p-4 rounded-2xl shadow-sm flex flex-col md:flex-row md:items-center gap-4">
            <div class="flex items-center gap-3 md:pr-4 md:border-r border-slate-100 shrink-0">
                <div class="w-12 h-12 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                        {{ $busqueda ? 'RESULTADOS' : 'EXPEDIENTES' }}
                    </p>
                    <p class="text-2xl font-extrabold text-brand-600 mt-0.5">{{ $pacientes->total() }}</p>
                </div>
            </div>

            <form method="GET" action="{{ route('expedientes.index') }}"
                class="flex flex-1 flex-col sm:flex-row gap-2">
                <div class="relative flex-1">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z" />
                    </svg>
                    <input type="text" name="buscar" value="{{ $busqueda }}"
                        placeholder="Buscar por nombre, apellidos o CURP"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-10 pr-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-brand-500 focus:ring-0 outline-none">
                </div>

                <button type="submit"
                    class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all">
                    Buscar
                </button>

                @if ($busqueda)
                    <a href="{{ route('expedientes.index') }}"
                        class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold text-center transition-all">
                        Limpiar
                    </a>
                @endif
            </form>
        </div>

        {{-- Tabla (escritorio) --}}
        <div class="hidden md:block bg-white rounded-2xl ring-1 ring-slate-200/70 shadow-card overflow-hidden">
            <table class="min-w-full">
                <thead class="bg-slate-50/70 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">
                            Paciente</th>
                        <th class="px-5 py-3.5 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">
                            CURP</th>
                        <th class="px-5 py-3.5 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">
                            Edad</th>
                        <th class="px-5 py-3.5 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">
                            Último triage</th>
                        <th class="px-5 py-3.5 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">
                            Consultas</th>
                        <th
                            class="px-5 py-3.5 text-right text-xs font-bold text-slate-400 uppercase tracking-wider">
                            Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($pacientes as $paciente)
                        <tr class="hover:bg-brand-50/30 transition-colors">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-9 h-9 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center text-xs font-bold shrink-0">
                                        {{ strtoupper(mb_substr($paciente->nombre_completo ?? 'P', 0, 2)) }}
                                    </div>
                                    <p class="text-xs font-bold text-slate-800">{{ $paciente->nombre_completo }}</p>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-xs font-medium text-slate-500">{{ $paciente->curp ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-xs font-semibold text-slate-700">{{ $paciente->edad }} años</td>
                            <td class="px-5 py-3.5">
                                @if ($paciente->ultimoSignoVital)
                                    <span
                                        class="px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wide {{ $paciente->ultimoSignoVital->triage_color }}">
                                        {{ $paciente->ultimoSignoVital->triage_label }}
                                    </span>
                                @else
                                    <span class="text-slate-400 text-xs font-semibold">Sin signos vitales</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <span
                                    class="inline-flex items-center justify-center min-w-[1.75rem] px-2 py-1 bg-brand-50 text-brand-700 border border-brand-100 rounded-full text-xs font-bold">
                                    {{ $paciente->consultas_count }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('expedientes.show', $paciente) }}"
                                        class="px-3.5 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-bold shadow-sm transition-all active:scale-95">
                                        Ver expediente
                                    </a>
                                    <button type="button"
                                        onclick="window.abrirModalPaciente && window.abrirModalPaciente('show', '{{ route('pacientes.show', $paciente) }}?partial=1')"
                                        class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition-all active:scale-95">
                                        Ficha
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-14 text-center">
                                <p class="text-xs font-semibold text-slate-400">Sin pacientes con expediente.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Tarjetas (móvil) --}}
        <div class="md:hidden space-y-3">
            @forelse ($pacientes as $paciente)
                <div class="bg-white rounded-2xl ring-1 ring-slate-200/70 shadow-card p-4 space-y-3">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-11 h-11 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center text-xs font-bold shrink-0">
                            {{ strtoupper(mb_substr($paciente->nombre_completo ?? 'P', 0, 2)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-extrabold text-slate-800 truncate">{{ $paciente->nombre_completo }}
                            </p>
                            <p class="text-xs font-medium text-slate-400 truncate">
                                {{ $paciente->curp ?? '—' }} · {{ $paciente->edad }} años
                            </p>
                        </div>
                    </div>

                    <div
                        class="flex items-center justify-between bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Triage</p>
                            @if ($paciente->ultimoSignoVital)
                                <span
                                    class="px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wide {{ $paciente->ultimoSignoVital->triage_color }}">
                                    {{ $paciente->ultimoSignoVital->triage_label }}
                                </span>
                            @else
                                <span class="text-slate-400 text-xs font-semibold">Sin signos vitales</span>
                            @endif
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Consultas</p>
                            <span
                                class="px-2.5 py-1 bg-brand-50 text-brand-700 border border-brand-100 rounded-full text-xs font-bold">
                                {{ $paciente->consultas_count }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <a href="{{ route('expedientes.show', $paciente) }}"
                            class="py-2.5 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-bold text-center shadow-sm">
                            Ver expediente
                        </a>
                        <button type="button"
                            onclick="window.abrirModalPaciente && window.abrirModalPaciente('show', '{{ route('pacientes.show', $paciente) }}?partial=1')"
                            class="py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold text-center">
                            Ficha
                        </button>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl ring-1 ring-slate-200/70 shadow-card p-10 text-center">
                    <p class="text-xs font-semibold text-slate-400">Sin pacientes con expediente.</p>
                </div>
            @endforelse
        </div>

        <div>{{ $pacientes->links() }}</div>

        {{-- ================= MODAL GLOBAL (RECICLADO) ================= --}}
        <div x-show="modalAbierto" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
            @keydown.escape.window="cerrarModal()">

            <div x-show="modalAbierto" x-transition.opacity @click="cerrarModal()"
                class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

            <div x-show="modalAbierto" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                class="relative bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] flex flex-col overflow-hidden">

                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="font-bold text-lg text-slate-800"
                        x-text="{
                            create: 'Nuevo Paciente',
                            edit: 'Editar Paciente',
                            show: 'Detalle del Paciente',
                            delete: 'Confirmar eliminación'
                        }[modalTipo] || ''">
                    </h3>

                    <button type="button" @click="cerrarModal()"
                        class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-2xl transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="overflow-y-auto p-6">
                    <div x-show="modalCargando" class="flex items-center justify-center py-16">
                        <svg class="animate-spin w-8 h-8 text-brand-600" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                        </svg>
                    </div>

                    <div x-show="modalError" x-cloak
                        class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-sm"
                        x-text="modalError"></div>

                    <!-- Contenido inyectado por fetch (show) -->
                    <div x-show="!modalCargando && !modalError && modalTipo !== 'delete'" x-ref="modalBody"></div>
                </div>
            </div>
        </div>

    </div>

    {{-- ================= SCRIPT RECICLADO ================= --}}
    <script>
        function pacientesIndex() {
            return {
                modalAbierto: false,
                modalTipo: null,
                modalCargando: false,
                modalError: null,

                init() {
                    // Exponer la función globalmente para que el botón "Ficha" la encuentre
                    window.abrirModalPaciente = (tipo, url) => this.abrirModal(tipo, url);
                    window.cerrarModalPaciente = () => this.cerrarModal();
                },

                async abrirModal(tipo, url) {
                    this.modalTipo = tipo;
                    this.modalAbierto = true;
                    this.modalCargando = true;
                    this.modalError = null;

                    try {
                        const res = await fetch(url, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        if (!res.ok) throw new Error();
                        const html = await res.text();

                        this.modalCargando = false;

                        this.$nextTick(() => {
                            const body = this.$refs.modalBody;
                            body.innerHTML = html;

                            if (window.Alpine && typeof window.Alpine.initTree === 'function') {
                                window.Alpine.initTree(body);
                            }
                        });
                    } catch (e) {
                        this.modalError = 'No se pudo cargar el contenido.';
                        this.modalCargando = false;
                    }
                },

                cerrarModal() {
                    this.modalAbierto = false;
                    this.modalTipo = null;
                    this.modalError = null;

                    if (this.$refs.modalBody) {
                        this.$refs.modalBody.innerHTML = '';
                    }
                }
            };
        }
    </script>
</x-app-layout>
