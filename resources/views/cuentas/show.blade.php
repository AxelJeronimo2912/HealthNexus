<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">
                Cuenta de {{ $paciente->nombre_completo }}
            </h2>
            <a href="{{ url()->previous() }}" class="text-sm text-gray-600 hover:underline">
                ← Volver
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

        @if (session('success'))
            <div class="p-3 bg-emerald-100 text-emerald-800 rounded">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="p-3 bg-rose-100 text-rose-800 rounded">{{ session('error') }}</div>
        @endif

        {{-- Encabezado de la cuenta --}}
        <div class="bg-white p-6 rounded-lg shadow flex justify-between items-start">
            <div>
                <p class="text-xs text-gray-500 font-mono">{{ $cuenta->folio }}</p>
                <p class="text-2xl font-bold mt-1">
                    {{ $cuenta->total_formateado }}
                </p>
                <p class="text-sm text-gray-500 mt-1">
                    Saldo pendiente: <strong>{{ $cuenta->saldo_formateado }}</strong>
                </p>
            </div>
            <div class="text-right">
                <span class="px-3 py-1 rounded text-sm {{ $cuenta->estado_color }}">
                    {{ ucfirst($cuenta->estado) }}
                </span>
                @if ($cuenta->estado === 'abierta')
                    <form action="{{ route('cuentas.cerrar', $cuenta) }}" method="POST" class="mt-3">
                        @csrf
                        <button onclick="return confirm('¿Cerrar la cuenta? Ya no podrás modificar cargos.')"
                            class="text-xs text-rose-600 hover:underline">
                            Cerrar cuenta
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Tabla de items --}}
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Concepto</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Cant.</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">P. Unitario</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Importe</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($cuenta->items as $item)
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                {{ $item->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <span class="font-medium">{{ $item->concepto }}</span>
                                @if ($item->cita)
                                    <span class="block text-xs text-gray-500">
                                        Cita #{{ $item->cita->id }} — {{ $item->cita->fecha_hora->format('d/m/Y') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center">{{ $item->cantidad }}</td>
                            <td class="px-4 py-3 text-sm text-right">
                                ${{ number_format((float) $item->precio_unitario, 2) }}
                            </td>
                            <td class="px-4 py-3 text-sm text-right font-semibold">
                                ${{ number_format((float) $item->importe, 2) }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                @if ($cuenta->estado === 'abierta')
                                    <form action="{{ route('cuentas.items.destroy', $item) }}" method="POST"
                                        onsubmit="return confirm('¿Eliminar este cargo?')">
                                        @csrf @method('DELETE')
                                        <button class="text-xs text-rose-600 hover:underline">Eliminar</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                Sin cargos registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="4" class="px-4 py-3 text-right font-bold">Total:</td>
                        <td class="px-4 py-3 text-right font-bold text-lg">
                            {{ $cuenta->total_formateado }}
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</x-app-layout>
