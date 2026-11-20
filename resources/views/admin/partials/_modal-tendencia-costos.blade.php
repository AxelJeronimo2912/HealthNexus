<div x-show="modalAbierto === 'tendencia-costos'" x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm"
    role="dialog" aria-modal="true">
    <div @click.away="modalAbierto = null"
        class="bg-white rounded-2xl max-w-5xl w-full shadow-2xl border border-slate-100 overflow-hidden max-h-[90vh] flex flex-col">

        {{-- Banner superior estilo Planificación Presupuestal --}}
        <div class="bg-gradient-to-r from-emerald-800 via-emerald-700 to-teal-700 px-6 py-5 text-white flex items-center justify-between gap-4 shrink-0">
            <div class="flex items-center gap-3.5 min-w-0">
                <div class="w-11 h-11 rounded-xl bg-white/15 ring-1 ring-white/25 flex items-center justify-center shrink-0">
                    <x-heroicon-o-presentation-chart-line class="w-6 h-6 text-white" />
                </div>
                <div class="min-w-0">
                    <h3 class="text-lg font-extrabold tracking-tight leading-snug truncate">
                        Tendencia y Costos Proyectados
                    </h3>
                    <p class="text-xs text-emerald-100/90 truncate">
                        Indicadores por periodo y proyección de costos · {{ $desde->format('d/m/Y') }} – {{ $hasta->format('d/m/Y') }}
                    </p>
                </div>
            </div>

            <button type="button" @click="modalAbierto = null"
                class="w-9 h-9 rounded-xl bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition shrink-0"
                aria-label="Cerrar">
                <x-heroicon-o-x-mark class="w-5 h-5" />
            </button>
        </div>

        {{-- Cuerpo desplazable del modal --}}
        <div class="p-6 space-y-6 overflow-y-auto">

            {{-- Filtros por periodo --}}
            <form method="GET" action="{{ route('admin.dashboard') }}"
                class="flex flex-wrap items-center justify-between gap-3 bg-slate-50/90 p-3.5 rounded-2xl ring-1 ring-slate-200/70">
                <div class="flex flex-wrap items-center gap-1.5">
                    @foreach (['7d' => '7 días', '30d' => '30 días', 'mes' => 'Este mes', 'trimestre' => 'Trimestre'] as $key => $etiqueta)
                        <button type="submit" name="periodo" value="{{ $key }}"
                            class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $periodo === $key ? 'bg-emerald-700 text-white shadow-sm' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-slate-100' }}">
                            {{ $etiqueta }}
                        </button>
                    @endforeach
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <input type="hidden" name="periodo" value="personalizado">
                    <input type="date" name="desde" value="{{ $desde->toDateString() }}"
                        class="bg-white border border-slate-200 rounded-xl px-2.5 py-1.5 text-xs text-slate-700 focus:border-emerald-600 outline-none">
                    <input type="date" name="hasta" value="{{ $hasta->toDateString() }}"
                        class="bg-white border border-slate-200 rounded-xl px-2.5 py-1.5 text-xs text-slate-700 focus:border-emerald-600 outline-none">
                    <button type="submit"
                        class="px-4 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold transition-all">
                        Aplicar
                    </button>
                </div>
            </form>

            {{-- Tarjetas de indicadores del periodo --}}
            <div class="grid grid-cols-1 gap-3.5 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-2xl bg-slate-50/70 p-4 ring-1 ring-slate-200/80">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Citas en el periodo</p>
                    <p class="mt-1.5 text-2xl font-extrabold tracking-tight text-slate-900 tabular">
                        {{ number_format($kpisPeriodo['citas_periodo']) }}
                    </p>
                    <p class="mt-0.5 text-xs text-slate-400">Agenda del rango elegido</p>
                </div>

                <div class="rounded-2xl bg-slate-50/70 p-4 ring-1 ring-slate-200/80">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-brand-700">Movimientos inventario</p>
                    <p class="mt-1.5 text-2xl font-extrabold tracking-tight text-brand-700 tabular">
                        {{ number_format($kpisPeriodo['movimientos_periodo']) }}
                    </p>
                    <p class="mt-0.5 text-xs text-slate-400">Entradas, salidas y ajustes</p>
                </div>

                <div class="rounded-2xl bg-amber-50/50 p-4 ring-1 ring-amber-200/80">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-amber-700">Reposición stock crítico</p>
                    <p class="mt-1.5 text-2xl font-extrabold tracking-tight text-slate-900 tabular">
                        ${{ number_format($kpisPeriodo['costo_reposicion'], 2) }}
                    </p>
                    <p class="mt-0.5 text-xs text-slate-500">Medicamentos en/bajo mínimo</p>
                </div>

                <div class="rounded-2xl bg-emerald-50/60 p-4 ring-1 ring-emerald-200/80">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-emerald-700">Costo proyectado</p>
                    <p class="mt-1.5 text-2xl font-extrabold tracking-tight text-slate-900 tabular">
                        ${{ number_format($kpisPeriodo['costo_proyectado'], 2) }}
                    </p>
                    <p class="mt-0.5 text-xs text-slate-500">Reposición + consumo 30d est.</p>
                </div>
            </div>

            {{-- Gráficas de tendencia y costos proyectados --}}
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                <div class="rounded-2xl bg-white p-4 ring-1 ring-slate-200/80">
                    <p class="text-xs font-extrabold uppercase tracking-wider text-slate-700">
                        Tendencia operativa del periodo
                    </p>
                    <p class="text-xs text-slate-400 mb-3">Comportamiento diario de citas y movimientos</p>
                    <div class="h-60">
                        <canvas id="chartTendenciaModal"></canvas>
                    </div>
                </div>

                <div class="rounded-2xl bg-white p-4 ring-1 ring-slate-200/80">
                    <p class="text-xs font-extrabold uppercase tracking-wider text-slate-700">
                        Costos proyectados
                    </p>
                    <p class="text-xs text-slate-400 mb-3">Estimación financiera para toma de decisiones</p>
                    <div class="h-60">
                        <canvas id="chartCostosModal"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        let chartTendenciaInst = null;
        let chartCostosInst = null;

        function renderChartsModal() {
            if (!window.Chart) return;

            const tEl = document.getElementById('chartTendenciaModal');
            const cEl = document.getElementById('chartCostosModal');
            if (!tEl || !cEl) return;

            if (chartTendenciaInst) chartTendenciaInst.destroy();
            if (chartCostosInst) chartCostosInst.destroy();

            chartTendenciaInst = new Chart(tEl, {
                type: 'line',
                data: {
                    labels: @json($tendenciaLabels),
                    datasets: [
                        {
                            label: 'Citas',
                            data: @json($tendenciaCitas),
                            borderColor: '#0f172a',
                            backgroundColor: 'rgba(15, 23, 42, 0.08)',
                            tension: 0.35,
                            fill: true
                        },
                        {
                            label: 'Movimientos',
                            data: @json($tendenciaMovimientos),
                            borderColor: '#059669',
                            backgroundColor: 'rgba(5, 150, 105, 0.1)',
                            borderDash: [5, 4],
                            tension: 0.35,
                            fill: false
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'top' } }
                }
            });

            chartCostosInst = new Chart(cEl, {
                type: 'bar',
                data: {
                    labels: @json($costosLabels),
                    datasets: [{
                        label: 'Costo estimado ($)',
                        data: @json($costosValores),
                        backgroundColor: '#059669',
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } }
                }
            });
        }

        window.addEventListener('abrir-modal-tendencia-costos', () => {
            setTimeout(renderChartsModal, 60);
        });

        @if (request()->hasAny(['periodo', 'desde', 'hasta', 'tipo_costo']))
            setTimeout(() => {
                window.dispatchEvent(new CustomEvent('abrir-modal-tendencia-costos'));
            }, 120);
        @endif
    });
</script>