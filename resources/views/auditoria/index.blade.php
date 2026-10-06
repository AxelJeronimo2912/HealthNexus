<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800">Auditoría del Sistema</h2>
                <p class="text-xs text-gray-500 mt-0.5">Registro inmutable de todas las acciones</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- Stats --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-xs text-gray-500 uppercase">Eventos hoy</p>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['total_hoy'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow border-l-4 border-red-500">
                <p class="text-xs text-gray-500 uppercase">Críticos hoy</p>
                <p class="text-2xl font-bold text-red-600">{{ $stats['criticos_hoy'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow border-l-4 border-orange-500">
                <p class="text-xs text-gray-500 uppercase">Accesos denegados</p>
                <p class="text-2xl font-bold text-orange-600">{{ $stats['accesos_denegados'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="text-xs text-gray-500 uppercase">Última semana</p>
                <p class="text-2xl font-bold text-blue-600">{{ $stats['total_semana'] }}</p>
            </div>
        </div>

        {{-- Filtros --}}
        <form method="GET" class="bg-white p-4 rounded-lg shadow grid grid-cols-1 md:grid-cols-6 gap-3">
            <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar..."
                class="md:col-span-2 border-gray-300 rounded-md shadow-sm text-sm">

            <select name="modulo" class="border-gray-300 rounded-md shadow-sm text-sm">
                <option value="">Todos los módulos</option>
                @foreach ($modulos as $m)
                    <option value="{{ $m }}" @selected(request('modulo') === $m)>{{ ucfirst($m) }}</option>
                @endforeach
            </select>

            <select name="evento" class="border-gray-300 rounded-md shadow-sm text-sm">
                <option value="">Todos los eventos</option>
                @foreach ($eventos as $e)
                    <option value="{{ $e->value }}" @selected(request('evento') === $e->value)>
                        {{ $e->etiqueta() }}
                    </option>
                @endforeach
            </select>

            <select name="severidad" class="border-gray-300 rounded-md shadow-sm text-sm">
                <option value="">Toda severidad</option>
                <option value="info" @selected(request('severidad') === 'info')>Info</option>
                <option value="warning" @selected(request('severidad') === 'warning')>Warning</option>
                <option value="critical" @selected(request('severidad') === 'critical')>Critical</option>
            </select>

            <select name="user_id" class="border-gray-300 rounded-md shadow-sm text-sm">
                <option value="">Todos los usuarios</option>
                @foreach ($usuarios as $u)
                    <option value="{{ $u->id }}" @selected(request('user_id') == $u->id)>
                        {{ $u->nombre_completo }}
                    </option>
                @endforeach
            </select>

            <input type="date" name="desde" value="{{ request('desde') }}"
                class="border-gray-300 rounded-md shadow-sm text-sm">
            <input type="date" name="hasta" value="{{ request('hasta') }}"
                class="border-gray-300 rounded-md shadow-sm text-sm">

            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md text-sm">
                Filtrar
            </button>
            <a href="{{ route('auditoria.index') }}"
                class="px-4 py-2 bg-white border rounded-md text-sm text-center">Limpiar</a>
        </form>

        {{-- Tabla --}}
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs text-gray-500 uppercase">Fecha</th>
                        <th class="px-4 py-3 text-left text-xs text-gray-500 uppercase">Usuario</th>
                        <th class="px-4 py-3 text-left text-xs text-gray-500 uppercase">Evento</th>
                        <th class="px-4 py-3 text-left text-xs text-gray-500 uppercase">Módulo</th>
                        <th class="px-4 py-3 text-left text-xs text-gray-500 uppercase">Descripción</th>
                        <th class="px-4 py-3 text-right text-xs text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($logs as $log)
                        <tr class="{{ $log->severidad === 'critical' ? 'bg-red-50/40' : '' }}">
                            <td class="px-4 py-3 text-sm whitespace-nowrap">
                                {{ $log->created_at->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <div class="font-medium">{{ $log->user_nombre }}</div>
                                <div class="text-xs text-gray-500">{{ $log->user_rol }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 rounded text-xs {{ $log->evento_color }}">
                                    {{ $log->evento_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm">{{ $log->modulo_label }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $log->descripcion }}</td>
                            <td class="px-4 py-3 text-right text-sm">
                                <a href="{{ route('auditoria.show', $log) }}"
                                    class="text-blue-600 hover:underline text-xs">Ver</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-gray-500">Sin registros.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $logs->links() }}</div>
    </div>
</x-app-layout>
