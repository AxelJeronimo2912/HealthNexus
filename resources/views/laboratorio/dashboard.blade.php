<x-app-layout>
    @php
        $hora = (int) now()->format('H');
        $saludo = $hora < 12 ? 'Buenos días' : ($hora < 19 ? 'Buenas tardes' : 'Buenas noches');
        $nombreCorto = \Illuminate\Support\Str::before(auth()->user()->nombre_completo ?? auth()->user()->name, ' ');
    @endphp

    <x-slot name="header">
        <x-page-header :title="$saludo . ', ' . $nombreCorto" :subtitle="ucfirst(now()->translatedFormat('l d \d\e F \d\e Y')) . ' · Panel de Laboratorio e Imagenología'">
            <x-slot:actions>
                <x-badge tone="emerald" dot>Sistema en línea</x-badge>
            </x-slot:actions>
        </x-page-header>
    </x-slot>

    <div class="py-7">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- INDICADORES PRINCIPALES --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">

                <a href="{{ route('estudios.index', ['estado' => 'solicitado']) }}"
                    class="group bg-white p-6 rounded-2xl ring-1 ring-slate-200/70 shadow-card hover:shadow-md transition-all">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center">
                        <x-heroicon-o-clipboard-document-list class="w-6 h-6" />
                    </div>
                    <p class="text-xs font-bold text-slate-400 uppercase mt-4">Pendientes por procesar</p>
                    <p class="text-3xl font-extrabold text-slate-800 mt-1">{{ $pendientes }}</p>
                    <p class="text-xs text-slate-500 mt-1">Solicitados o en proceso</p>
                </a>

                <a href="{{ route('estudios.index', ['estado' => 'resultado_listo']) }}"
                    class="group bg-white p-6 rounded-2xl ring-1 ring-slate-200/70 shadow-card hover:shadow-md transition-all">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <x-heroicon-o-document-check class="w-6 h-6" />
                    </div>
                    <p class="text-xs font-bold text-slate-400 uppercase mt-4">Resultados hoy</p>
                    <p class="text-3xl font-extrabold text-slate-800 mt-1">{{ $resultadosHoy }}</p>
                    <p class="text-xs text-slate-500 mt-1">Listos para entregar</p>
                </a>

                <div class="bg-white p-6 rounded-2xl ring-1 ring-slate-200/70 shadow-card">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center">
                        <x-heroicon-o-check-badge class="w-6 h-6" />
                    </div>
                    <p class="text-xs font-bold text-slate-400 uppercase mt-4">Entregados hoy</p>
                    <p class="text-3xl font-extrabold text-slate-800 mt-1">{{ $entregadosHoy }}</p>
                    <p class="text-xs text-slate-500 mt-1">Archivos descargados/entregados</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- SOLICITUDES PENDIENTES --}}
                <div class="bg-white rounded-2xl border border-slate-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-sm font-bold text-slate-700 flex items-center gap-2">
                            <x-heroicon-o-fire class="w-5 h-5 text-amber-500" />
                            Solicitudes pendientes (más antiguas)
                        </h4>
                        <a href="{{ route('estudios.index') }}" class="text-xs font-bold text-brand-600">Ver todas →</a>
                    </div>

                    @forelse($solicitudesPendientes as $sol)
                        <div class="flex items-center gap-3 py-3 border-b border-slate-100 last:border-0">
                            <div
                                class="w-2 h-2 rounded-full {{ $sol->estado === 'en_proceso' ? 'bg-blue-500 animate-pulse' : 'bg-amber-500' }}">
                            </div>

                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-slate-800 truncate">
                                    {{ $sol->paciente->nombre_completo ?? 'Paciente' }}
                                </p>
                                <p class="text-xs text-slate-500 truncate">
                                    {{ $sol->tipo_estudio }} · Dr. {{ $sol->medicoSolicita->name ?? 'N/A' }}
                                </p>
                            </div>

                            <div class="text-right shrink-0">
                                <p class="text-[10px] font-bold text-slate-400 uppercase">
                                    {{ $sol->created_at->diffForHumans() }}</p>
                                <a href="{{ route('estudios.show', $sol) }}"
                                    class="text-xs font-bold text-brand-600 whitespace-nowrap">Procesar →</a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <x-heroicon-o-check-circle class="w-12 h-12 text-emerald-300 mx-auto mb-2" />
                            <p class="text-sm text-slate-400">¡Todo al día! No hay pendientes.</p>
                        </div>
                    @endforelse
                </div>

                {{-- ÚLTIMOS RESULTADOS --}}
                <div class="bg-white rounded-2xl border border-slate-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-sm font-bold text-slate-700 flex items-center gap-2">
                            <x-heroicon-o-document-text class="w-5 h-5 text-emerald-600" />
                            Últimos resultados listos
                        </h4>
                    </div>

                    @forelse($ultimosResultados as $res)
                        <div class="flex items-center gap-3 py-3 border-b border-slate-100 last:border-0">
                            <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-slate-800 truncate">
                                    {{ $res->paciente->nombre_completo }}</p>
                                <p class="text-xs text-slate-500">{{ $res->tipo_estudio }}</p>
                            </div>
                            <a href="{{ route('estudios.show', $res) }}"
                                class="text-xs font-bold text-slate-500 whitespace-nowrap">Ver →</a>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <p class="text-sm text-slate-400">Aún no hay resultados subidos hoy.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- ACCESOS RÁPIDOS --}}
            <div>
                <h4 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-4">Accesos rápidos</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    <a href="{{ route('estudios.index') }}"
                        class="group bg-white p-5 rounded-2xl ring-1 ring-slate-200/70 shadow-card hover:shadow-md transition-all">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center">
                                <x-heroicon-o-beaker class="w-5 h-5" />
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-800">Bandeja de Estudios</p>
                                <p class="text-xs text-slate-500">Ver todas las solicitudes (Lab e Imagen)</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('estudios.create') }}"
                        class="group bg-gradient-to-br from-brand-600 to-brand-800 p-5 rounded-2xl shadow-sm hover:shadow-lg transition-all text-left text-white">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                                <x-heroicon-o-plus class="w-5 h-5" />
                            </div>
                            <div>
                                <p class="text-sm font-bold">Nueva solicitud</p>
                                <p class="text-xs text-brand-100">Registrar estudio manualmente</p>
                            </div>
                        </div>
                    </a>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
