<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800">Alertas Inteligentes</h2>
                <p class="text-xs text-gray-500 mt-0.5">Monitoreo automático del sistema hospitalario</p>
            </div>
            <form action="{{ route('alertas.generar') }}" method="POST">
                @csrf
                <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                    Analizar ahora
                </button>
            </form>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        @if (session('success'))
            <div class="p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
        @endif

        {{-- Estadísticas --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-lg shadow border-l-4 border-red-500">
                <p class="text-xs text-gray-500 uppercase">Críticas</p>
                <p class="text-2xl font-bold text-red-600">{{ $stats['criticas'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow border-l-4 border-yellow-500">
                <p class="text-xs text-gray-500 uppercase">Advertencias</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $stats['advertencias'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow border-l-4 border-blue-500">
                <p class="text-xs text-gray-500 uppercase">Total activas</p>
                <p class="text-2xl font-bold text-blue-600">{{ $stats['total_activas'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow border-l-4 border-green-500">
                <p class="text-xs text-gray-500 uppercase">Resueltas hoy</p>
                <p class="text-2xl font-bold text-green-600">{{ $stats['resueltas_hoy'] }}</p>
            </div>
        </div>

        {{-- Filtros --}}
        <form method="GET" class="flex gap-2 flex-wrap">
            <select name="estado" class="border-gray-300 rounded-md shadow-sm text-sm">
                <option value="activas" @selected($filtroEstado === 'activas')>Activas</option>
                <option value="vista" @selected($filtroEstado === 'vista')>Vistas</option>
                <option value="resuelta" @selected($filtroEstado === 'resuelta')>Resueltas</option>
                <option value="descartada" @selected($filtroEstado === 'descartada')>Descartadas</option>
                <option value="" @selected($filtroEstado === '')>Todas</option>
            </select>
            <select name="nivel" class="border-gray-300 rounded-md shadow-sm text-sm">
                <option value="">Todos los niveles</option>
                <option value="critico" @selected($filtroNivel === 'critico')>Crítico</option>
                <option value="advertencia" @selected($filtroNivel === 'advertencia')>Advertencia</option>
                <option value="info" @selected($filtroNivel === 'info')>Información</option>
            </select>
            <select name="tipo" class="border-gray-300 rounded-md shadow-sm text-sm">
                <option value="">Todos los tipos</option>
                <option value="inventario" @selected($filtroTipo === 'inventario')>Inventario</option>
                <option value="paciente" @selected($filtroTipo === 'paciente')>Paciente</option>
                <option value="operacion" @selected($filtroTipo === 'operacion')>Operación</option>
                <option value="seguridad" @selected($filtroTipo === 'seguridad')>Seguridad</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">
                Filtrar
            </button>
            <a href="{{ route('alertas.index') }}" class="px-4 py-2 bg-white border rounded-md text-sm">
                Limpiar
            </a>
        </form>

        {{-- Lista de alertas --}}
        <div class="space-y-3">
            @forelse ($alertas as $alerta)
                <div
                    class="bg-white rounded-lg shadow border-l-4
                    {{ $alerta->nivel === 'critico' ? 'border-red-500' : ($alerta->nivel === 'advertencia' ? 'border-yellow-500' : 'border-blue-500') }}">

                    <div class="p-4">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="px-2 py-0.5 rounded text-xs font-bold {{ $alerta->nivel_color }}">
                                        {{ $alerta->nivel_label }}
                                    </span>
                                    <span class="text-xs text-gray-500 uppercase">
                                        {{ $alerta->tipo }}
                                    </span>
                                    @if ($alerta->estado === 'vista')
                                        <span class="text-xs text-yellow-600">· Vista</span>
                                    @elseif ($alerta->estado === 'resuelta')
                                        <span class="text-xs text-green-600">· Resuelta por
                                            {{ $alerta->resueltaPor?->nombre_completo }}</span>
                                    @endif
                                </div>

                                <h3 class="font-bold text-gray-800">{{ $alerta->titulo }}</h3>
                                <p class="text-sm text-gray-700 mt-1">{{ $alerta->mensaje }}</p>

                                <p class="text-xs text-gray-400 mt-2">
                                    {{ $alerta->created_at->format('d/m/Y H:i') }}
                                    · {{ $alerta->created_at->diffForHumans() }}
                                </p>
                            </div>

                            {{-- Acciones --}}
                            <div class="flex flex-col gap-1 ml-4">
                                @if ($alerta->estado === 'activa')
                                    <form action="{{ route('alertas.vista', $alerta) }}" method="POST">
                                        @csrf
                                        <button class="text-xs text-gray-600 hover:underline">Marcar vista</button>
                                    </form>
                                @endif

                                @if (in_array($alerta->estado, ['activa', 'vista']))
                                    <form action="{{ route('alertas.resolver', $alerta) }}" method="POST">
                                        @csrf
                                        <button class="text-xs text-green-600 hover:underline">✓ Resolver</button>
                                    </form>
                                    <form action="{{ route('alertas.descartar', $alerta) }}" method="POST">
                                        @csrf
                                        <button class="text-xs text-gray-400 hover:underline">Descartar</button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        {{-- Datos específicos --}}
                        @if ($alerta->datos && count($alerta->datos))
                            <div class="mt-3 pt-3 border-t grid grid-cols-2 md:grid-cols-4 gap-2 text-xs">
                                @foreach ($alerta->datos as $key => $value)
                                    <div>
                                        <span class="text-gray-500">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                        <span class="font-medium">
                                            @if (is_array($value))
                                                {{ implode(', ', $value) }}
                                            @else
                                                {{ $value }}
                                            @endif
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white p-8 rounded-lg shadow text-center">
                    <p class="text-gray-500">No hay alertas para mostrar.</p>
                    <form action="{{ route('alertas.generar') }}" method="POST" class="mt-3">
                        @csrf
                        <button type="submit" class="text-indigo-600 hover:underline text-sm">
                            Generar alertas ahora
                        </button>
                    </form>
                </div>
            @endforelse
        </div>

        <div class="mt-4">{{ $alertas->links() }}</div>
    </div>
</x-app-layout>
