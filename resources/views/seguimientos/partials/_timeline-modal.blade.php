<div class="p-6 sm:p-8 bg-slate-50 relative rounded-3xl min-h-[85vh]">
    {{-- Encabezado --}}
    <div
        class="flex justify-between items-center mb-6 border-b border-slate-200 pb-4 sticky top-0 bg-slate-50/90 backdrop-blur-md z-20">
        <div>
            <h3 class="text-2xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Línea de Vida Clínica
            </h3>
            <p class="text-sm font-medium text-slate-500 mt-1">Evolución completa de {{ $paciente->nombre_completo }}
            </p>
        </div>
        <button type="button" onclick="document.getElementById('modal-timeline').__x.$data.open = false"
            class="w-10 h-10 flex items-center justify-center rounded-full bg-white text-slate-400 hover:text-rose-500 hover:bg-rose-50 transition-all shadow-sm border border-slate-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    {{-- GRÁFICA DE EVOLUCIÓN (APEXCHARTS) --}}
    @if ($seguimientosGrafica->count() > 1)
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm mb-8 relative overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-rose-400 to-indigo-500"></div>
            <h4 class="text-xs font-black text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
                Tendencia de Signos Vitales
            </h4>
            <div id="chart-evolucion"></div>
        </div>
    @endif

    {{-- TIMELINE --}}
    <div class="relative max-w-3xl mx-auto py-4">

        {{-- Línea vertical de fondo (Gradiente) --}}
        <div
            class="absolute left-6 sm:left-8 top-0 bottom-0 w-1 bg-gradient-to-b from-indigo-500 via-purple-400 to-emerald-400 rounded-full opacity-30">
        </div>

        <div class="space-y-8 relative">
            @foreach ($eventos as $evento)
                <div class="relative flex gap-4 sm:gap-6 items-start group">

                    {{-- Punto en la línea --}}
                    <div class="absolute left-6 sm:left-8 transform -translate-x-1/2 mt-1.5 z-10">
                        <div
                            class="w-4 h-4 rounded-full bg-white border-4 
                            @if ($evento['tipo'] === 'cita') border-indigo-500 shadow-[0_0_10px_rgba(99,102,241,0.5)]
                            @elseif($evento['tipo'] === 'consulta') border-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.5)]
                            @else border-rose-500 shadow-[0_0_10px_rgba(244,63,94,0.5)] @endif 
                            group-hover:scale-125 transition-transform duration-300">
                        </div>
                    </div>

                    {{-- Tarjeta del evento --}}
                    <div class="ml-12 sm:ml-16 w-full">
                        <div
                            class="bg-white rounded-2xl p-5 border border-slate-100 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07)] hover:shadow-xl transition-all duration-300 relative overflow-hidden">

                            {{-- Cinta superior de color --}}
                            <div
                                class="absolute top-0 left-0 right-0 h-1 
                                @if ($evento['tipo'] === 'cita') bg-indigo-500
                                @elseif($evento['tipo'] === 'consulta') bg-emerald-500
                                @else bg-rose-500 @endif">
                            </div>

                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-2 mb-3">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span
                                        class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider
                                        @if ($evento['tipo'] === 'cita') bg-indigo-50 text-indigo-700
                                        @elseif($evento['tipo'] === 'consulta') bg-emerald-50 text-emerald-700
                                        @else bg-rose-50 text-rose-700 @endif">
                                        {{ $evento['titulo'] }}
                                    </span>

                                    {{-- Etiqueta de Triage (Solo para seguimientos) --}}
                                    @if (isset($evento['triage_label']))
                                        <span
                                            class="px-2.5 py-1 rounded-lg text-[10px] font-bold border {{ $evento['triage_color'] }}">
                                            {{ $evento['triage_label'] }}
                                        </span>
                                    @endif
                                </div>
                                <div class="text-left sm:text-right shrink-0">
                                    <p class="text-xs font-extrabold text-slate-700">
                                        {{ $evento['fecha']->format('d M, Y') }}</p>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase">
                                        {{ $evento['fecha']->format('h:i A') }}</p>
                                </div>
                            </div>

                            {{-- Signos Vitales (Solo para seguimientos) --}}
                            @if (isset($evento['signos']) && ($evento['signos']['temp'] || $evento['signos']['fc']))
                                <div class="flex flex-wrap gap-2 mb-4">
                                    @if ($evento['signos']['temp'])
                                        <div
                                            class="px-2.5 py-1 bg-slate-50 border border-slate-200 rounded-md flex items-center gap-1.5">
                                            <span class="text-[9px] font-bold text-slate-400 uppercase">Temp</span>
                                            <span
                                                class="text-xs font-extrabold text-slate-700">{{ $evento['signos']['temp'] }}°C</span>
                                        </div>
                                    @endif
                                    @if ($evento['signos']['fc'])
                                        <div
                                            class="px-2.5 py-1 bg-slate-50 border border-slate-200 rounded-md flex items-center gap-1.5">
                                            <span class="text-[9px] font-bold text-slate-400 uppercase">FC</span>
                                            <span
                                                class="text-xs font-extrabold text-slate-700">{{ $evento['signos']['fc'] }}
                                                lpm</span>
                                        </div>
                                    @endif
                                    @if ($evento['signos']['ta'])
                                        <div
                                            class="px-2.5 py-1 bg-slate-50 border border-slate-200 rounded-md flex items-center gap-1.5">
                                            <span class="text-[9px] font-bold text-slate-400 uppercase">TA</span>
                                            <span
                                                class="text-xs font-extrabold text-slate-700">{{ $evento['signos']['ta'] }}</span>
                                        </div>
                                    @endif
                                    @if ($evento['signos']['spo2'])
                                        <div
                                            class="px-2.5 py-1 bg-slate-50 border border-slate-200 rounded-md flex items-center gap-1.5">
                                            <span class="text-[9px] font-bold text-slate-400 uppercase">SpO₂</span>
                                            <span
                                                class="text-xs font-extrabold text-slate-700">{{ $evento['signos']['spo2'] }}%</span>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <p
                                class="text-sm text-slate-600 leading-relaxed whitespace-pre-line bg-slate-50/50 p-3 rounded-xl border border-slate-100">
                                {{ Str::limit($evento['descripcion'], 200) }}
                            </p>

                            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center gap-2">
                                <div
                                    class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center text-slate-500">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <span class="text-xs font-bold text-slate-600">{{ $evento['autor'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Fin del timeline --}}
            <div class="relative flex items-center mt-4">
                <div class="absolute left-6 sm:left-8 transform -translate-x-1/2 z-10">
                    <div
                        class="w-6 h-6 rounded-full bg-indigo-600 border-4 border-white shadow-lg flex items-center justify-center">
                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>
                <div class="ml-12 sm:ml-16">
                    <p class="text-xs font-black text-indigo-600 uppercase tracking-wider">Estado Actual</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPT DE APEXCHARTS --}}
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if ($seguimientosGrafica->count() > 1)
                var opciones = {
                    series: [{
                        name: 'Temperatura (°C)',
                        data: @json($seguimientosGrafica->pluck('temperatura'))
                    }, {
                        name: 'Frecuencia Cardíaca (lpm)',
                        data: @json($seguimientosGrafica->pluck('frecuencia_cardiaca'))
                    }],
                    chart: {
                        type: 'area', // Tipo área para que se vea más moderno
                        height: 250,
                        toolbar: {
                            show: false
                        },
                        fontFamily: 'inherit',
                    },
                    colors: ['#f43f5e', '#6366f1'], // Rose and Indigo
                    stroke: {
                        curve: 'smooth',
                        width: 3
                    },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.4,
                            opacityTo: 0.05,
                            stops: [0, 100]
                        }
                    },
                    xaxis: {
                        categories: @json($seguimientosGrafica->pluck('fecha')),
                        labels: {
                            style: {
                                colors: '#94a3b8',
                                fontSize: '11px',
                                fontWeight: 600
                            }
                        },
                        axisBorder: {
                            show: false
                        },
                        axisTicks: {
                            show: false
                        }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                colors: '#94a3b8',
                                fontSize: '11px',
                                fontWeight: 600
                            }
                        }
                    },
                    legend: {
                        position: 'top',
                        horizontalAlign: 'right',
                        fontWeight: 700,
                        labels: {
                            colors: '#64748b'
                        }
                    },
                    grid: {
                        borderColor: '#f1f5f9',
                        strokeDashArray: 4,
                    }
                };

                var chart = new ApexCharts(document.querySelector("#chart-evolucion"), opciones);
                chart.render();
            @endif
        });
    </script>
@endpush
