@php $esAdmin = auth()->user()->hasRole('administrador'); @endphp

<x-app-layout>
    @php
        $nombre = \Illuminate\Support\Str::before(auth()->user()->name, ' ');
        $hora = (int) now()->format('H');
        $saludo = $hora < 12 ? 'Buenos días' : ($hora < 19 ? 'Buenas tardes' : 'Buenas noches');
        $ocupacion = $camasTotales > 0 ? round((($camasTotales - $camasDisponibles) / $camasTotales) * 100) : 0;

        $pendientes = collect([
            $alertasCriticas > 0 ? ['tono' => 'red', 'icono' => 'bell-alert', 'texto' => $alertasCriticas . ' ' . \Illuminate\Support\Str::plural('alerta crítica', $alertasCriticas) . ' sin atender', 'url' => route('alertas.index'), 'cta' => 'Revisar'] : null,
            $medicamentosBajos > 0 ? ['tono' => 'amber', 'icono' => 'beaker', 'texto' => $medicamentosBajos . ' ' . \Illuminate\Support\Str::plural('medicamento', $medicamentosBajos) . ' por debajo del mínimo', 'url' => route('existencias.index', ['filtro' => 'bajo']), 'cta' => 'Ver stock'] : null,
            $ocupacion > 85 ? ['tono' => 'amber', 'icono' => 'home-modern', 'texto' => 'Ocupación de camas al ' . $ocupacion . '%', 'url' => route('camas.index'), 'cta' => 'Ver camas'] : null,
            $auditoriaCritica > 0 ? ['tono' => 'brand', 'icono' => 'shield-exclamation', 'texto' => $auditoriaCritica . ' ' . \Illuminate\Support\Str::plural('evento crítico', $auditoriaCritica) . ' en auditoría hoy', 'url' => route('auditoria.index', ['severidad' => 'critical']), 'cta' => 'Ver logs'] : null,
        ])->filter();

        $tonos = [
            'red' => 'bg-red-50 text-red-600 ring-red-100',
            'amber' => 'bg-amber-50 text-amber-600 ring-amber-100',
            'brand' => 'bg-brand-50 text-brand-600 ring-brand-100',
        ];
    @endphp

    <x-slot name="header">
        <x-page-header :title="$saludo . ', ' . $nombre"
            :subtitle="ucfirst(now()->translatedFormat('l d \d\e F \d\e Y')) . ' · Panel de administración'">
            <x-slot:actions>
                <x-badge tone="emerald" dot>Sistema en línea</x-badge>
            </x-slot:actions>
        </x-page-header>
    </x-slot>

    <div x-data="{ modalAbierto: null }" @abrir-modal-sla.window="modalAbierto = 'sla'"
        @abrir-modal-fenotipado.window="modalAbierto = 'fenotipado'" @abrir-modal-dwh.window="modalAbierto = 'dwh'"
        @abrir-modal-ia-medica.window="modalAbierto = 'ia-medica'" @keydown.escape.window="modalAbierto = null"
        class="py-7">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- REQUIERE ATENCIÓN --}}
            @if ($pendientes->isNotEmpty())
                <x-card :padding="false">
                    <div class="flex items-center gap-2 px-5 pt-4 pb-2">
                        <span class="relative flex h-2.5 w-2.5">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-red-400 opacity-60"></span>
                            <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-red-500"></span>
                        </span>
                        <h2 class="text-sm font-semibold text-slate-900">Requiere atención</h2>
                    </div>
                    <ul class="divide-y divide-slate-100">
                        @foreach ($pendientes as $p)
                            <li>
                                <a href="{{ $p['url'] }}" class="group flex items-center gap-3 px-5 py-3 hover:bg-slate-50/80 transition-colors">
                                    <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg ring-1 ring-inset {{ $tonos[$p['tono']] }}">
                                        <x-dynamic-component :component="'heroicon-o-' . $p['icono']" class="h-[18px] w-[18px]" />
                                    </span>
                                    <span class="flex-1 text-sm text-slate-700">{{ $p['texto'] }}</span>
                                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-slate-500 group-hover:text-slate-900">
                                        {{ $p['cta'] }} <x-heroicon-m-chevron-right class="h-4 w-4" />
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </x-card>
            @else
                <div class="flex items-center gap-3 rounded-2xl bg-emerald-50/70 px-5 py-3.5 ring-1 ring-inset ring-emerald-100">
                    <x-heroicon-o-check-circle class="h-5 w-5 text-emerald-600" />
                    <p class="text-sm text-emerald-800"><span class="font-semibold">Todo en orden.</span> No hay alertas críticas ni faltantes de inventario.</p>
                </div>
            @endif

            {{-- INDICADORES CRÍTICOS --}}
            <section>
                <h2 class="mb-3 text-sm font-semibold text-slate-900">Indicadores críticos</h2>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <x-kpi label="Alertas activas" :value="$alertasActivas" icon="bell-alert" tone="red"
                        hint="Requieren atención" :href="route('alertas.index')" cta="Ver todas"
                        :badge="$alertasCriticas > 0 ? $alertasCriticas . ' críticas' : null" />

                    <x-kpi label="Stock bajo" :value="$medicamentosBajos" icon="beaker" tone="amber"
                        hint="Medicamentos bajo el mínimo" :href="route('existencias.index', ['filtro' => 'bajo'])" />

                    <x-kpi label="Camas disponibles" :value="$camasDisponibles" icon="home-modern" tone="emerald"
                        :hint="'De ' . $camasTotales . ' camas totales'" :href="route('camas.index')" cta="Ver distribución">
                        <div class="mt-3">
                            <div class="flex justify-between text-xs text-slate-500 mb-1">
                                <span>Ocupación</span>
                                <span class="font-semibold tabular {{ $ocupacion > 85 ? 'text-red-600' : 'text-slate-700' }}">{{ $ocupacion }}%</span>
                            </div>
                            <div class="h-1.5 w-full overflow-hidden rounded-full bg-slate-100">
                                <div class="h-full rounded-full {{ $ocupacion > 85 ? 'bg-red-500' : ($ocupacion > 70 ? 'bg-amber-500' : 'bg-emerald-500') }}"
                                    style="width: {{ $ocupacion }}%"></div>
                            </div>
                        </div>
                    </x-kpi>

                    <x-kpi label="Eventos críticos" :value="$auditoriaCritica" icon="shield-exclamation" tone="brand"
                        hint="Auditoría de hoy" :href="route('auditoria.index', ['severidad' => 'critical'])" cta="Ver logs" />
                </div>
            </section>

            {{-- OPERACIÓN DE HOY --}}
            <section>
                <h2 class="mb-3 text-sm font-semibold text-slate-900">Operación de hoy</h2>
                <div class="grid grid-cols-2 overflow-hidden rounded-2xl bg-white ring-1 ring-slate-200/70 shadow-card lg:grid-cols-4">
                    @foreach ([
                        ['Pacientes activos', $resumen['pacientes_activos'], 'user-group', route('pacientes.index')],
                        ['Citas hoy', $resumen['citas_hoy'], 'calendar-days', route('citas.index')],
                        ['Consultas hoy', $resumen['consultas_hoy'], 'clipboard-document-list', route('citas.index')],
                        ['Camas ocupadas', $resumen['camas_ocupadas'], 'home-modern', route('camas.index')],
                    ] as [$label, $valor, $icono, $url])
                        <a href="{{ $url }}"
                            class="group flex items-center gap-3 p-5 transition-colors hover:bg-slate-50 border-slate-100 {{ $loop->index % 2 === 0 ? 'border-r' : '' }} {{ $loop->index < 2 ? 'border-b lg:border-b-0' : '' }} {{ $loop->index === 1 ? 'lg:border-r' : '' }}">
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-slate-100 text-slate-500 transition-colors group-hover:bg-brand-50 group-hover:text-brand-600">
                                <x-dynamic-component :component="'heroicon-o-' . $icono" class="h-5 w-5" />
                            </span>
                            <span>
                                <span class="block text-2xl font-bold tracking-tight text-slate-900 tabular">{{ $valor }}</span>
                                <span class="block text-xs text-slate-500">{{ $label }}</span>
                            </span>
                        </a>
                    @endforeach
                </div>
            </section>

            {{-- MODELOS ANALÍTICOS --}}
            @if ($esAdmin)
                <section>
                    <div class="mb-3 flex items-end justify-between">
                        <h2 class="text-sm font-semibold text-slate-900">Modelos analíticos</h2>
                        <p class="text-xs text-slate-500">Se abren en un panel sin salir del dashboard</p>
                    </div>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                        @foreach ([
                            ['abrir-modal-sla', 'Pulso operativo SLA', 'Detección de anomalías', 'signal', 'from-sky-500 to-brand-600'],
                            ['abrir-modal-fenotipado', 'Fenotipado clínico', 'K-Means + PCA', 'squares-plus', 'from-accent-500 to-accent-700'],
                            ['abrir-modal-dwh', 'Data Warehouse', 'Big Data · Analítica', 'circle-stack', 'from-brand-500 to-brand-800'],
                            ['abrir-modal-ia-medica', 'IA médica predictiva', '5 algoritmos', 'cpu-chip', 'from-accent-600 to-brand-700'],
                        ] as [$evento, $titulo, $desc, $icono, $grad])
                            <button type="button" @click="$dispatch('{{ $evento }}')"
                                class="group relative overflow-hidden rounded-2xl bg-white p-5 text-left ring-1 ring-slate-200/70 shadow-card transition-all hover:shadow-md hover:ring-slate-300">
                                <div class="flex items-start justify-between">
                                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-gradient-to-br {{ $grad }} text-white shadow-sm">
                                        <x-dynamic-component :component="'heroicon-o-' . $icono" class="h-5 w-5" />
                                    </span>
                                    <x-heroicon-m-arrow-up-right class="h-4 w-4 text-slate-300 transition group-hover:text-slate-500 group-hover:translate-x-0.5 group-" />
                                </div>
                                <p class="mt-4 text-sm font-semibold text-slate-900">{{ $titulo }}</p>
                                <p class="mt-0.5 text-xs text-slate-500">{{ $desc }}</p>
                            </button>
                        @endforeach
                    </div>
                </section>
            @endif

        </div>

        @if ($esAdmin)
            @include('admin.partials._modal-sla')
            @include('admin.partials._modal-fenotipado')
            @include('admin.partials._modal-dwh')
            @include('admin.partials._modal-ia-medica')
        @endif

    </div>
</x-app-layout>
