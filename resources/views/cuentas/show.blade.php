<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">
                Cuenta de {{ $paciente->nombre_completo }}
            </h2>
            <a href="{{ url()->previous() }}" class="text-sm text-gray-600 hover:underline">← Volver</a>
        </div>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

        @if (session('success'))
            <div class="p-3 bg-emerald-100 text-emerald-800 rounded">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="p-3 bg-rose-100 text-rose-800 rounded">{{ session('error') }}</div>
        @endif

        {{-- ============ AVISO: CUENTA CERRADA ============ --}}
        @if ($cuenta->estado === 'cerrada')
            <div class="p-4 bg-slate-100 border border-slate-200 text-slate-700 rounded-lg flex items-center gap-3">
                <svg class="w-6 h-6 shrink-0 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                <div class="text-sm">
                    <p class="font-semibold">Cuenta cerrada</p>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Cerrada el {{ $cuenta->cerrada_en?->format('d/m/Y H:i') }}.
                        Ya no se puede modificar. Solo puedes descargar el estado de cuenta.
                    </p>
                </div>
            </div>
        @endif

        {{-- ============ ENCABEZADO CON TOTALES ============ --}}
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs text-gray-500 font-mono">{{ $cuenta->folio }}</p>
                    <p class="text-3xl font-bold mt-1">{{ $cuenta->total_formateado }}</p>
                    <div class="mt-3 grid grid-cols-3 gap-4 text-sm">
                        <div>
                            <p class="text-xs text-gray-500 uppercase">Subtotal</p>
                            <p class="font-semibold">${{ number_format((float) $cuenta->subtotal, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase">Pagado</p>
                            <p class="font-semibold text-emerald-700">
                                ${{ number_format((float) $cuenta->pagado, 2) }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase">Saldo</p>
                            <p
                                class="font-bold {{ (float) $cuenta->saldo > 0 ? 'text-rose-700' : 'text-emerald-700' }}">
                                ${{ number_format((float) $cuenta->saldo, 2) }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="text-right space-y-2">
                    <span class="px-3 py-1 rounded text-sm {{ $cuenta->estado_color }}">
                        {{ ucfirst($cuenta->estado) }}
                    </span>

                    {{-- Solo cuenta ABIERTA: botones de cerrar --}}
                    @if ($cuenta->estado === 'abierta')
                        @if ((float) $cuenta->saldo > 0)
                            <button type="button" disabled title="No se puede cerrar con saldo pendiente"
                                class="text-xs text-gray-400 cursor-not-allowed inline-flex items-center gap-1 justify-end w-full">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                Cerrar cuenta
                            </button>
                            <p class="text-[10px] text-gray-400 mt-1">
                                Paga el saldo de
                                <strong>${{ number_format((float) $cuenta->saldo, 2) }}</strong>
                                para poder cerrarla.
                            </p>
                        @else
                            <form action="{{ route('cuentas.cerrar', $cuenta) }}" method="POST">
                                @csrf
                                <button onclick="return confirm('¿Cerrar la cuenta? Ya no podrás modificar cargos.')"
                                    class="text-xs text-emerald-600 hover:underline font-medium">
                                    ✓ Cerrar cuenta
                                </button>
                            </form>
                        @endif
                    @endif

                    {{-- Solo cuenta CERRADA: botón imprimir estado de cuenta --}}
                    @if ($cuenta->estado === 'cerrada')
                        <a href="{{ route('cuentas.pdf', $cuenta) }}" target="_blank"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 hover:bg-slate-900 text-white rounded text-xs font-medium">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            Descargar estado de cuenta
                        </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- ============ CARGOS ============ --}}
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b flex flex-wrap justify-between items-center gap-2">
                <h3 class="font-bold text-gray-800">Cargos</h3>

                {{-- Solo cuenta ABIERTA: botones de agregar --}}
                @if ($cuenta->estado === 'abierta')
                    <div class="flex flex-wrap gap-2">
                        <button type="button" onclick="abrirModalServicio()"
                            class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded text-xs">
                            + Agregar servicio
                        </button>

                        <button type="button"
                            onclick="document.getElementById('modal-cargo').classList.remove('hidden')"
                            class="px-3 py-1.5 bg-slate-700 hover:bg-slate-800 text-white rounded text-xs">
                            + Agregar cargo manual
                        </button>

                        <button type="button"
                            onclick="document.getElementById('modal-descuento').classList.remove('hidden')"
                            class="px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white rounded text-xs">
                            % Aplicar descuento
                        </button>
                    </div>
                @else
                    <span class="text-xs text-slate-400 italic">🔒 Cuenta cerrada — no editable</span>
                @endif
            </div>

            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Concepto</th>
                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Cant.</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">P. Unitario</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Descuento</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Importe</th>
                        {{-- Solo columna acciones si la cuenta está abierta --}}
                        @if ($cuenta->estado === 'abierta')
                            <th class="px-4 py-2"></th>
                        @endif
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($cuenta->items as $item)
                        <tr>
                            <td class="px-4 py-3 text-xs text-gray-500">
                                {{ $item->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <span class="font-medium">{{ $item->concepto }}</span>
                                @if ($item->cita)
                                    <span class="block text-xs text-gray-500">Cita #{{ $item->cita->id }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center">{{ $item->cantidad }}</td>
                            <td class="px-4 py-3 text-sm text-right">
                                ${{ number_format((float) $item->precio_unitario, 2) }}
                            </td>
                            <td class="px-4 py-3 text-sm text-right text-rose-600">
                                @if ((float) $item->descuento > 0)
                                    -${{ number_format((float) $item->descuento, 2) }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-right font-semibold">
                                ${{ number_format((float) $item->importe, 2) }}
                            </td>
                            @if ($cuenta->estado === 'abierta')
                                <td class="px-4 py-3 text-right">
                                    <form action="{{ route('cuentas.items.destroy', $item) }}" method="POST"
                                        onsubmit="return confirm('¿Eliminar este cargo?')">
                                        @csrf @method('DELETE')
                                        <button class="text-xs text-rose-600 hover:underline">Eliminar</button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $cuenta->estado === 'abierta' ? 7 : 6 }}"
                                class="px-4 py-6 text-center text-gray-500">
                                Sin cargos.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="5" class="px-4 py-2 text-right font-bold">Subtotal:</td>
                        <td class="px-4 py-2 text-right font-bold">
                            ${{ number_format((float) $cuenta->subtotal, 2) }}
                        </td>
                        @if ($cuenta->estado === 'abierta')
                            <td></td>
                        @endif
                    </tr>
                    @if ((float) $cuenta->descuento_global > 0)
                        <tr>
                            <td colspan="5" class="px-4 py-2 text-right text-rose-700">Descuento global:</td>
                            <td class="px-4 py-2 text-right text-rose-700">
                                -${{ number_format((float) $cuenta->descuento_global, 2) }}
                            </td>
                            @if ($cuenta->estado === 'abierta')
                                <td></td>
                            @endif
                        </tr>
                    @endif
                    <tr>
                        <td colspan="5" class="px-4 py-2 text-right font-bold text-lg">Total:</td>
                        <td class="px-4 py-2 text-right font-bold text-lg">{{ $cuenta->total_formateado }}</td>
                        @if ($cuenta->estado === 'abierta')
                            <td></td>
                        @endif
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- ============ PAGOS ============ --}}
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b flex flex-wrap justify-between items-center gap-2">
                <h3 class="font-bold text-gray-800">Pagos registrados</h3>

                @if ($cuenta->estado === 'abierta' && (float) $cuenta->saldo > 0)
                    <button type="button" onclick="document.getElementById('modal-pago').classList.remove('hidden')"
                        class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-xs">
                        + Registrar pago
                    </button>
                @elseif ($cuenta->estado === 'cerrada')
                    <span class="text-xs text-slate-400 italic">🔒 Cuenta cerrada — no editable</span>
                @endif
            </div>

            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Folio</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Método</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Monto</th>
                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-4 py-2"></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($cuenta->pagos as $pago)
                        <tr class="{{ $pago->estado === 'cancelado' ? 'opacity-50 line-through' : '' }}">
                            <td class="px-4 py-3 text-xs font-mono">{{ $pago->folio }}</td>
                            <td class="px-4 py-3 text-sm">{{ $pago->pagado_en->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 text-sm">
                                {{ $pago->metodo_label }}
                                @if ($pago->referencia)
                                    <span class="block text-xs text-gray-500">Ref: {{ $pago->referencia }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-right font-semibold text-emerald-700">
                                ${{ number_format((float) $pago->monto, 2) }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if ($pago->estado === 'aplicado')
                                    <span class="px-2 py-1 bg-emerald-100 text-emerald-800 rounded text-xs">
                                        Aplicado
                                    </span>
                                @else
                                    <span class="px-2 py-1 bg-rose-100 text-rose-800 rounded text-xs">
                                        Cancelado
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right space-x-2">
                                {{-- El recibo del pago siempre se puede ver --}}
                                <a href="{{ route('cuentas.pagos.recibo', $pago) }}" target="_blank"
                                    class="text-xs text-blue-600 hover:underline">Recibo</a>

                                {{-- Cancelar solo si está abierta --}}
                                @if ($pago->estado === 'aplicado' && $cuenta->estado === 'abierta')
                                    <button type="button"
                                        onclick="cancelarPago({{ $pago->id }}, '{{ $pago->folio }}')"
                                        class="text-xs text-rose-600 hover:underline">Cancelar</button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-gray-500">Sin pagos registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="3" class="px-4 py-2 text-right font-bold">Total pagado:</td>
                        <td class="px-4 py-2 text-right font-bold text-emerald-700">
                            ${{ number_format((float) $cuenta->pagado, 2) }}
                        </td>
                        <td colspan="2"></td>
                    </tr>
                    <tr>
                        <td colspan="3" class="px-4 py-2 text-right font-bold text-lg">Saldo pendiente:</td>
                        <td
                            class="px-4 py-2 text-right font-bold text-lg
                            {{ (float) $cuenta->saldo > 0 ? 'text-rose-700' : 'text-emerald-700' }}">
                            ${{ number_format((float) $cuenta->saldo, 2) }}
                        </td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

    </div>

    {{-- =====================================================================
         MODALES — Solo se renderizan si la cuenta está ABIERTA.
         Si está cerrada, no hay formularios activos en la página.
    ====================================================================== --}}
    @if ($cuenta->estado === 'abierta')

        {{-- ==================== MODAL AGREGAR SERVICIO ==================== --}}
        <div id="modal-servicio" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-xl w-full p-6 max-h-[90vh] overflow-y-auto">
                <h3 class="text-lg font-semibold mb-4">Agregar servicio a la cuenta</h3>

                <form action="{{ route('cuentas.items.store', $cuenta) }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium">Servicio *</label>
                        <select id="servicio-select" name="servicio_id" required
                            class="w-full border-gray-300 rounded-md">
                            <option value="">— Selecciona un servicio —</option>
                            @foreach ($servicios as $srv)
                                <option value="{{ $srv->id }}" data-precio="{{ $srv->precio }}"
                                    data-nombre="{{ $srv->nombre }}"
                                    data-descripcion="{{ $srv->precio_descripcion }}">
                                    {{ $srv->codigo }} — {{ $srv->nombre }}
                                    (${{ number_format((float) $srv->precio, 2) }})
                                </option>
                            @endforeach
                        </select>
                        <p id="servicio-detalle" class="text-xs text-gray-500 mt-1"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Concepto *</label>
                        <input type="text" name="concepto" id="servicio-concepto" required
                            class="w-full border-gray-300 rounded-md">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium">Cantidad *</label>
                            <input type="number" name="cantidad" value="1" min="1" required
                                class="w-full border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Precio unitario *</label>
                            <input type="number" name="precio_unitario" id="servicio-precio" step="0.01"
                                min="0" required class="w-full border-gray-300 rounded-md">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Notas</label>
                        <textarea name="notas" rows="2" class="w-full border-gray-300 rounded-md"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t">
                        <button type="button" onclick="cerrarModalServicio()"
                            class="px-4 py-2 bg-gray-100 rounded-md">Cancelar</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md">
                            Agregar servicio
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ==================== MODAL CARGO MANUAL ==================== --}}
        <div id="modal-cargo" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-lg w-full p-6">
                <h3 class="text-lg font-semibold mb-4">Agregar cargo manual</h3>
                <p class="text-xs text-gray-500 mb-3">
                    Para conceptos libres no ligados a un servicio del catálogo.
                </p>
                <form action="{{ route('cuentas.items.store', $cuenta) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium">Concepto *</label>
                        <input type="text" name="concepto" required class="w-full border-gray-300 rounded-md">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium">Cantidad *</label>
                            <input type="number" name="cantidad" value="1" min="1" required
                                class="w-full border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Precio unitario *</label>
                            <input type="number" name="precio_unitario" step="0.01" min="0" required
                                class="w-full border-gray-300 rounded-md">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium">Descuento</label>
                            <input type="number" name="descuento" step="0.01" min="0" value="0"
                                class="w-full border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Motivo descuento</label>
                            <input type="text" name="motivo_descuento" class="w-full border-gray-300 rounded-md">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Notas</label>
                        <textarea name="notas" rows="2" class="w-full border-gray-300 rounded-md"></textarea>
                    </div>
                    <div class="flex justify-end gap-2 pt-3 border-t">
                        <button type="button"
                            onclick="document.getElementById('modal-cargo').classList.add('hidden')"
                            class="px-4 py-2 bg-gray-100 rounded-md">Cancelar</button>
                        <button type="submit" class="px-4 py-2 bg-slate-700 text-white rounded-md">
                            Agregar cargo
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ==================== MODAL DESCUENTO GLOBAL ==================== --}}
        <div id="modal-descuento" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-semibold mb-4">Aplicar descuento a la cuenta</h3>

                <div class="mb-3 p-3 bg-amber-50 border border-amber-200 rounded text-sm">
                    <p class="text-amber-800">
                        Subtotal actual:
                        <strong>${{ number_format((float) $cuenta->subtotal, 2) }}</strong>
                    </p>
                    @if ((float) $cuenta->descuento_global > 0)
                        <p class="text-amber-800 mt-1">
                            Descuento actual:
                            <strong>-${{ number_format((float) $cuenta->descuento_global, 2) }}</strong>
                            @if ($cuenta->motivo_descuento)
                                <span class="text-xs">({{ $cuenta->motivo_descuento }})</span>
                            @endif
                        </p>
                    @endif
                </div>

                <form action="{{ route('cuentas.descuento', $cuenta) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium">Monto del descuento *</label>
                        <input type="number" name="descuento_global" step="0.01" min="0"
                            max="{{ $cuenta->subtotal }}" required value="{{ (float) $cuenta->descuento_global }}"
                            class="w-full border-gray-300 rounded-md">
                        <p class="text-xs text-gray-500 mt-1">
                            No puede superar el subtotal (${{ number_format((float) $cuenta->subtotal, 2) }}).
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Motivo</label>
                        <input type="text" name="motivo_descuento" placeholder="Ej. Convenio, cortesía, ajuste"
                            value="{{ $cuenta->motivo_descuento }}" class="w-full border-gray-300 rounded-md">
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t">
                        <button type="button"
                            onclick="document.getElementById('modal-descuento').classList.add('hidden')"
                            class="px-4 py-2 bg-gray-100 rounded-md">Cancelar</button>
                        <button type="submit" class="px-4 py-2 bg-amber-600 text-white rounded-md">
                            Aplicar descuento
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ==================== MODAL PAGO ==================== --}}
        <div id="modal-pago" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-lg w-full p-6">
                <h3 class="text-lg font-semibold mb-4">Registrar pago</h3>
                <p class="text-sm text-gray-500 mb-3">
                    Saldo pendiente:
                    <strong class="text-rose-700">${{ number_format((float) $cuenta->saldo, 2) }}</strong>
                </p>
                <form action="{{ route('cuentas.pagos.store', $cuenta) }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium">Monto *</label>
                            <input type="number" name="monto" step="0.01" min="0.01"
                                max="{{ (float) $cuenta->saldo }}" required value="{{ (float) $cuenta->saldo }}"
                                class="w-full border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Método *</label>
                            <select name="metodo" required class="w-full border-gray-300 rounded-md">
                                <option value="efectivo">Efectivo</option>
                                <option value="tarjeta">Tarjeta</option>
                                <option value="transferencia">Transferencia</option>
                                <option value="otro">Otro</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Referencia (opcional)</label>
                        <input type="text" name="referencia" placeholder="Ej. últimos 4 dígitos, no. autorización"
                            class="w-full border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Notas</label>
                        <textarea name="notas" rows="2" class="w-full border-gray-300 rounded-md"></textarea>
                    </div>
                    <div class="flex justify-end gap-2 pt-3 border-t">
                        <button type="button" onclick="document.getElementById('modal-pago').classList.add('hidden')"
                            class="px-4 py-2 bg-gray-100 rounded-md">Cancelar</button>
                        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-md">
                            Registrar pago
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ==================== MODAL CANCELAR PAGO ==================== --}}
        <div id="modal-cancelar-pago"
            class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-semibold mb-4">Cancelar pago</h3>
                <p class="text-sm text-gray-500 mb-3">
                    Vas a cancelar el pago <strong id="cancelar-folio" class="font-mono"></strong>.
                    Esta acción queda registrada.
                </p>
                <form id="form-cancelar-pago" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium">Motivo de cancelación *</label>
                        <textarea name="motivo_cancelacion" rows="3" required class="w-full border-gray-300 rounded-md"></textarea>
                    </div>
                    <div class="flex justify-end gap-2 pt-3 border-t">
                        <button type="button"
                            onclick="document.getElementById('modal-cancelar-pago').classList.add('hidden')"
                            class="px-4 py-2 bg-gray-100 rounded-md">Cancelar</button>
                        <button type="submit" class="px-4 py-2 bg-rose-600 text-white rounded-md">
                            Sí, cancelar pago
                        </button>
                    </div>
                </form>
            </div>
        </div>

    @endif {{-- fin @if ($cuenta->estado === 'abierta') --}}

    @push('scripts')
        <script>
            // Solo cargamos el JS si la cuenta está abierta (los modales existen)
            @if ($cuenta->estado === 'abierta')

                function abrirModalServicio() {
                    document.getElementById('modal-servicio').classList.remove('hidden');
                }

                function cerrarModalServicio() {
                    document.getElementById('modal-servicio').classList.add('hidden');
                    document.getElementById('servicio-select').value = '';
                    document.getElementById('servicio-concepto').value = '';
                    document.getElementById('servicio-precio').value = '';
                    document.getElementById('servicio-detalle').textContent = '';
                }

                function cancelarPago(pagoId, folio) {
                    const modal = document.getElementById('modal-cancelar-pago');
                    const form = document.getElementById('form-cancelar-pago');
                    const folioEl = document.getElementById('cancelar-folio');

                    form.action = `/pagos/${pagoId}/cancelar`;
                    folioEl.textContent = folio;
                    modal.classList.remove('hidden');
                }

                document.addEventListener('DOMContentLoaded', () => {
                    const select = document.getElementById('servicio-select');
                    const concepto = document.getElementById('servicio-concepto');
                    const precio = document.getElementById('servicio-precio');
                    const detalle = document.getElementById('servicio-detalle');

                    if (select) {
                        select.addEventListener('change', () => {
                            const opt = select.options[select.selectedIndex];

                            if (!opt.value) {
                                concepto.value = '';
                                precio.value = '';
                                detalle.textContent = '';
                                return;
                            }

                            concepto.value = opt.dataset.nombre || '';
                            precio.value = opt.dataset.precio || '';

                            const desc = opt.dataset.descripcion;
                            const valor = parseFloat(opt.dataset.precio || 0).toFixed(2);
                            detalle.textContent = desc ?
                                `Precio configurado: $${valor} / ${desc}` :
                                `Precio configurado: $${valor}`;
                        });
                    }

                    document.addEventListener('keydown', (e) => {
                        if (e.key === 'Escape') {
                            document.querySelectorAll('.fixed.inset-0.z-50').forEach(m => {
                                m.classList.add('hidden');
                            });
                        }
                    });
                });
            @endif
        </script>
    @endpush
</x-app-layout>
