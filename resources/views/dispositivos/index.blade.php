<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800">Dispositivos</h2>
                <p class="text-xs text-gray-500 mt-0.5">
                    {{ auth()->user()->hasRole('administrador')
                        ? 'Todos los dispositivos conectados al sistema'
                        : 'Tus dispositivos autorizados' }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        @if (session('success'))
            <div class="p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="p-3 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
        @endif

        {{-- Stats --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-xs text-gray-500 uppercase">Total</p>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-xs text-gray-500 uppercase">Confiables</p>
                <p class="text-2xl font-bold text-green-600">{{ $stats['confiables'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-xs text-gray-500 uppercase">Pendientes</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $stats['pendientes'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-xs text-gray-500 uppercase">Bloqueados</p>
                <p class="text-2xl font-bold text-red-600">{{ $stats['bloqueados'] }}</p>
            </div>
        </div>

        {{-- Filtros --}}
        <form method="GET" class="flex gap-2 flex-wrap">
            <input type="text" name="buscar" value="{{ $busqueda }}"
                placeholder="Buscar por nombre, IP o usuario"
                class="flex-1 min-w-[200px] border-gray-300 rounded-md shadow-sm">
            <select name="filtro" class="border-gray-300 rounded-md shadow-sm">
                <option value="">Todos</option>
                <option value="confiable" @selected($filtro === 'confiable')>Confiables</option>
                <option value="pendiente" @selected($filtro === 'pendiente')>Pendientes</option>
                <option value="bloqueado" @selected($filtro === 'bloqueado')>Bloqueados</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">
                Filtrar
            </button>
            @if ($busqueda || $filtro)
                <a href="{{ route('dispositivos.index') }}"
                    class="px-4 py-2 bg-white border rounded-md text-sm">Limpiar</a>
            @endif
        </form>

        {{-- Tabla --}}
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dispositivo</th>
                        @if (auth()->user()->hasRole('administrador'))
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usuario</th>
                        @endif
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Último acceso</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Accesos</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($dispositivos as $d)
                        <tr>
                            <td class="px-4 py-3 text-sm">
                                <div class="flex items-center gap-2">
                                    <x-dynamic-component :component="'heroicon-o-' . $d->tipo_icono" class="w-5 h-5 text-gray-500" />
                                    <div>
                                        <p class="font-medium">{{ $d->nombre ?? 'Dispositivo desconocido' }}</p>
                                        <p class="text-xs text-gray-500">
                                            {{ $d->navegador }} — {{ $d->sistema_operativo }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            @if (auth()->user()->hasRole('administrador'))
                                <td class="px-4 py-3 text-sm">
                                    <div class="font-medium">{{ $d->user?->nombre_completo ?? '—' }}</div>
                                    <div class="text-xs text-gray-500">{{ $d->user?->email ?? '—' }}</div>
                                </td>
                            @endif
                            <td class="px-4 py-3 text-sm font-mono text-xs">
                                {{ $d->ip_ultimo_acceso ?? ($d->ip_registro ?? '—') }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                {{ $d->ultimo_acceso?->diffForHumans() ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-center">
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">
                                    {{ $d->total_accesos }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 rounded text-xs {{ $d->estado_color }}">
                                    {{ $d->estado_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right text-sm space-x-2 whitespace-nowrap">
                                <a href="{{ route('dispositivos.show', $d) }}"
                                    class="text-blue-600 hover:underline">Ver</a>

                                @if (auth()->user()->hasRole('administrador') && !$d->confiable && $d->activo)
                                    <form action="{{ route('dispositivos.confiar', $d) }}" method="POST"
                                        class="inline">
                                        @csrf
                                        <button class="text-green-600 hover:underline">Confiar</button>
                                    </form>
                                @endif

                                @if ($d->activo)
                                    <form action="{{ route('dispositivos.bloquear', $d) }}" method="POST"
                                        class="inline" onsubmit="return confirm('¿Bloquear este dispositivo?')">
                                        @csrf
                                        <button class="text-red-600 hover:underline">Bloquear</button>
                                    </form>
                                @elseif (auth()->user()->hasRole('administrador'))
                                    <form action="{{ route('dispositivos.reactivar', $d) }}" method="POST"
                                        class="inline">
                                        @csrf
                                        <button class="text-yellow-600 hover:underline">Reactivar</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->hasRole('administrador') ? 7 : 6 }}"
                                class="px-4 py-6 text-center text-gray-500">
                                Sin dispositivos registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $dispositivos->links() }}</div>
    </div>
</x-app-layout>
