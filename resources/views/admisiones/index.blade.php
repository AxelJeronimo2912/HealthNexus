<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-3">
            <div class="min-w-0">
                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight truncate">
                    Admisión Hospitalaria
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Registro de llegada y derivaciones de pacientes</p>
            </div>
            <button type="button" id="btn-abrir-admision"
                class="group relative inline-flex items-center justify-center px-5 py-2.5 text-sm font-semibold text-nexus-accent bg-nexus-primary rounded-2xl shadow-md hover:shadow-lg hover:bg-slate-900 transition-all duration-200">
                <svg class="w-5 h-5 me-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Nueva Admisión</span>
            </button>
        </div>
    </x-slot>

    @php
        $inputCls =
            'w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-brand-500 focus:ring-0 outline-none';
        $labelCls = 'block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5';
        $thCls = 'px-5 py-3.5 text-xs font-bold text-slate-400 uppercase tracking-wider';
    @endphp

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">


        {{-- Disponibilidad --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-2xl ring-1 ring-slate-200/70 shadow-card">
                <p class="text-xs font-bold text-emerald-600/70 uppercase tracking-wider">Camas libres</p>
                <p class="text-3xl font-black text-emerald-600 tracking-tight mt-1">
                    {{ $disponibilidad['camas_libres'] }}</p>
            </div>
            <div class="bg-white p-5 rounded-2xl ring-1 ring-slate-200/70 shadow-card">
                <p class="text-xs font-bold text-rose-600/70 uppercase tracking-wider">Camas ocupadas</p>
                <p class="text-3xl font-black text-rose-600 tracking-tight mt-1">{{ $disponibilidad['camas_ocupadas'] }}
                </p>
            </div>
            <div class="bg-white p-5 rounded-2xl ring-1 ring-slate-200/70 shadow-card">
                <p class="text-xs font-bold text-brand-600/70 uppercase tracking-wider">Médicos activos</p>
                <p class="text-3xl font-black text-brand-600 tracking-tight mt-1">
                    {{ $disponibilidad['medicos_activos'] }}</p>
            </div>
            <div class="bg-white p-5 rounded-2xl ring-1 ring-slate-200/70 shadow-card">
                <p class="text-xs font-bold text-amber-600/70 uppercase tracking-wider">En espera</p>
                <p class="text-3xl font-black text-amber-600 tracking-tight mt-1">{{ $disponibilidad['en_espera'] }}</p>
            </div>
        </div>

        {{-- Filtros --}}
        <div class="bg-white rounded-2xl ring-1 ring-slate-200/70 shadow-card p-5">
            <form method="GET" class="flex gap-2 flex-wrap items-end">
                <div class="min-w-[150px]">
                    <label class="{{ $labelCls }}">Fecha</label>
                    <input type="date" name="fecha" value="{{ $fecha }}" class="{{ $inputCls }}">
                </div>
                <div class="flex-1 min-w-[200px]">
                    <label class="{{ $labelCls }}">Buscar</label>
                    <input type="text" name="buscar" value="{{ $buscar }}" placeholder="Folio, nombre o CURP"
                        class="{{ $inputCls }}">
                </div>
                <div class="min-w-[180px]">
                    <label class="{{ $labelCls }}">Estado</label>
                    <select name="estado" class="{{ $inputCls }}">
                        <option value="">Todos los estados</option>
                        <option value="en_espera" @selected($filtroEstado === 'en_espera')>En espera</option>
                        <option value="hospitalizado" @selected($filtroEstado === 'hospitalizado')>Hospitalizado</option>
                        <option value="derivado" @selected($filtroEstado === 'derivado')>Derivado</option>
                        <option value="alta" @selected($filtroEstado === 'alta')>Alta</option>
                        <option value="fallecido" @selected($filtroEstado === 'fallecido')>Fallecido</option>
                    </select>
                </div>
                <button type="submit"
                    class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all">
                    Filtrar
                </button>
                <a href="{{ route('admisiones.index') }}"
                    class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition-all">
                    Limpiar
                </a>
            </form>
        </div>

        {{-- Tabla --}}
        <div class="bg-white rounded-2xl ring-1 ring-slate-200/70 shadow-card overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="font-extrabold text-slate-800 text-base">Admisiones registradas</h3>
                <p class="text-xs text-slate-400">{{ $admisiones->total() }} registros en total</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-slate-50/70 border-b border-slate-100">
                        <tr>
                            <th class="{{ $thCls }} text-left">Folio</th>
                            <th class="{{ $thCls }} text-left">Hora</th>
                            <th class="{{ $thCls }} text-left">Paciente</th>
                            <th class="{{ $thCls }} text-left">Tipo</th>
                            <th class="{{ $thCls }} text-left">Triage</th>
                            <th class="{{ $thCls }} text-left">Estado</th>
                            <th class="{{ $thCls }} text-left">Destino</th>
                            <th class="px-5 py-3.5"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($admisiones as $adm)
                            <tr class="hover:bg-brand-50/30 transition-colors">
                                <td class="px-5 py-3.5">
                                    <span
                                        class="px-2 py-1 bg-slate-100 text-slate-600 rounded-lg text-xs font-mono font-bold">
                                        {{ $adm->folio }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-xs font-medium text-slate-400 whitespace-nowrap">
                                    {{ $adm->fecha_hora_llegada->format('H:i') }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <p class="text-xs font-bold text-slate-800">
                                        {{ $adm->paciente?->nombre_completo ?? '—' }}</p>
                                    @if ($adm->paciente?->curp)
                                        <p class="text-xs font-medium text-slate-400 mt-0.5">
                                            {{ $adm->paciente->curp }}</p>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    <span
                                        class="px-2.5 py-1 bg-brand-50 text-brand-700 border border-brand-100 rounded-full text-xs font-bold uppercase tracking-wide">
                                        {{ $adm->tipo_label }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    @if ($adm->triage)
                                        @php
                                            $triageColor = match ($adm->triage) {
                                                'rojo' => 'bg-rose-50 text-rose-700 border-rose-100',
                                                'naranja' => 'bg-orange-50 text-orange-700 border-orange-100',
                                                'amarillo' => 'bg-amber-50 text-amber-700 border-amber-100',
                                                'verde' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                                'azul' => 'bg-brand-50 text-brand-700 border-brand-100',
                                                default => 'bg-slate-100 text-slate-600 border-slate-200',
                                            };
                                        @endphp
                                        <span
                                            class="px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wide border {{ $triageColor }}">
                                            {{ ucfirst($adm->triage) }}
                                        </span>
                                    @else
                                        <span class="text-slate-300 text-xs">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    <span
                                        class="px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wide {{ $adm->estado_color }}">
                                        {{ $adm->estado_label }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    @if ($adm->hospitalDerivado)
                                        <span
                                            class="px-2.5 py-1 bg-orange-50 text-orange-700 border border-orange-100 rounded-full text-xs font-bold">
                                            → {{ $adm->hospitalDerivado->nombre }}
                                        </span>
                                    @elseif ($adm->cama)
                                        <span
                                            class="px-2.5 py-1 bg-brand-50 text-brand-700 border border-brand-100 rounded-full text-xs font-bold">
                                            {{ $adm->cama->codigo }}
                                        </span>
                                    @else
                                        <span class="text-slate-300 text-xs">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex justify-end">
                                        <a href="{{ route('admisiones.show', $adm) }}"
                                            class="w-9 h-9 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl flex items-center justify-center transition-all shadow-sm"
                                            title="Ver detalles">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-12 text-center">
                                    <p class="text-xs font-semibold text-slate-400">Sin admisiones.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">{{ $admisiones->links() }}</div>
    </div>

    {{-- INCLUSIÓN DE MODALES --}}
    @include('admisiones.partials._modal-admision')
    @include('admisiones.partials._modal-paciente-rapido')

    {{-- INCLUSIÓN DE SCRIPTS --}}
    @include('admisiones.partials._scripts-paciente-rapido')

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const modal = document.getElementById('modal-admision');
                const btnAbrir = document.getElementById('btn-abrir-admision');
                const btnCerrar = document.getElementById('btn-cerrar-admision');
                const btnCancelar = document.getElementById('btn-cancelar-admision');

                function abrirModal() {
                    modal.classList.remove('hidden');
                }

                function cerrarModal() {
                    modal.classList.add('hidden');
                }

                if (btnAbrir) btnAbrir.addEventListener('click', abrirModal);
                if (btnCerrar) btnCerrar.addEventListener('click', cerrarModal);
                if (btnCancelar) btnCancelar.addEventListener('click', cerrarModal);

                // Cerrar al hacer clic fuera del contenido
                window.addEventListener('click', (e) => {
                    if (e.target === modal) cerrarModal();
                });
            });
        </script>
    @endpush
</x-app-layout>
