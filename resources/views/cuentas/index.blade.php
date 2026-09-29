<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800">Cuentas por cobrar</h2>
                <p class="text-xs text-gray-500 mt-0.5">Control de cargos y pagos de pacientes</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        @if (session('success'))
            <div class="p-3 bg-emerald-100 text-emerald-800 rounded">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="p-3 bg-rose-100 text-rose-800 rounded">{{ session('error') }}</div>
        @endif

        {{-- KPIs --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-lg shadow border-l-4 border-amber-500">
                <p class="text-xs text-gray-500 uppercase">Cuentas abiertas</p>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['abiertas'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow border-l-4 border-rose-500">
                <p class="text-xs text-gray-500 uppercase">Por cobrar</p>
                <p class="text-2xl font-bold text-rose-700">
                    ${{ number_format($stats['por_cobrar'], 2) }}
                </p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow border-l-4 border-emerald-500">
                <p class="text-xs text-gray-500 uppercase">Cobrado hoy</p>
                <p class="text-2xl font-bold text-emerald-700">
                    ${{ number_format($stats['cobrado_hoy'], 2) }}
                </p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow border-l-4 border-blue-500">
                <p class="text-xs text-gray-500 uppercase">Cerradas este mes</p>
                <p class="text-2xl font-bold text-blue-700">{{ $stats['cerradas_mes'] }}</p>
            </div>
        </div>

        {{-- Filtros --}}
        <form method="GET" class="flex flex-wrap gap-2 bg-white p-4 rounded-lg shadow">
            <input type="text" name="buscar" value="{{ $buscar }}"
                placeholder="Buscar por folio, nombre o CURP"
                class="flex-1 min-w-[200px] border-gray-300 rounded-md shadow-sm">

            <select name="estado" class="border-gray-300 rounded-md shadow-sm">
                <option value="todas" @selected($filtroEstado === 'todas')>Todas</option>
                <option value="abierta" @selected($filtroEstado === 'abierta')>Abiertas</option>
                <option value="cerrada" @selected($filtroEstado === 'cerrada')>Cerradas</option>
                <option value="cancelada" @selected($filtroEstado === 'cancelada')>Canceladas</option>
            </select>

            <label class="inline-flex items-center gap-2 px-3 border border-gray-300 rounded-md bg-white">
                <input type="checkbox" name="con_saldo" value="1" @checked($soloConSaldo)
                    class="rounded border-gray-300 text-blue-600 shadow-sm">
                <span class="text-sm">Solo con saldo</span>
            </label>

            <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">
                Filtrar
            </button>

            @if ($buscar || $filtroEstado || $soloConSaldo)
                <a href="{{ route('cuentas.index') }}" class="px-4 py-2 bg-white border rounded-md text-sm">Limpiar</a>
            @endif
        </form>

        {{-- Tabla --}}
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Folio</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paciente</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Items</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Pagado</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Saldo</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($cuentas as $cuenta)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-mono text-gray-600">
                                {{ $cuenta->folio }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <p class="font-medium text-gray-800">
                                    {{ $cuenta->paciente?->nombre_completo ?? '—' }}
                                </p>
                                <p class="text-xs text-gray-500">ID: #{{ $cuenta->paciente_id }}</p>
                            </td>
                            <td class="px-4 py-3 text-sm text-center">
                                <span class="px-2 py-1 bg-slate-100 text-slate-700 rounded text-xs">
                                    {{ $cuenta->items->count() }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-right font-medium">
                                ${{ number_format((float) $cuenta->total, 2) }}
                            </td>
                            <td class="px-4 py-3 text-sm text-right text-emerald-700">
                                ${{ number_format((float) $cuenta->pagado, 2) }}
                            </td>
                            <td
                                class="px-4 py-3 text-sm text-right font-bold
                                {{ (float) $cuenta->saldo > 0 ? 'text-rose-700' : 'text-emerald-700' }}">
                                ${{ number_format((float) $cuenta->saldo, 2) }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 rounded text-xs {{ $cuenta->estado_color }}">
                                    {{ ucfirst($cuenta->estado) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right text-sm">
                                <a href="{{ route('cuentas.paciente', $cuenta->paciente) }}"
                                    class="text-emerald-600 hover:underline">
                                    Ver detalle
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                No hay cuentas que coincidan con el filtro.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $cuentas->links() }}</div>
    </div>
</x-app-layout>
