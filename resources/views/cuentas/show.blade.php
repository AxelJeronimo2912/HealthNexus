<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-3">
            <div class="min-w-0">
                <h2 class="font-extrabold text-2xl text-slate-800 leading-tight truncate">
                    Cuenta de {{ $paciente->nombre_completo }}
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Cargos, descuentos y pagos del paciente</p>
            </div>
            <a href="{{ url()->previous() }}"
                class="bg-white hover:bg-indigo-50 text-slate-700 hover:text-indigo-600 border border-slate-200 hover:border-indigo-200 px-5 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 transition-all active:scale-95 group shrink-0">
                <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
                Volver
            </a>
        </div>
    </x-slot>

    @php
        $abierta = $cuenta->estado === 'abierta';
        $inputCls =
            'w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-indigo-500 focus:ring-0 outline-none';
        $labelCls = 'block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5';
        $thCls = 'px-5 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider';
        $btnCancel = 'px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold';
    @endphp

    <div class="py-8 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        @if (session('success'))
            <div
                class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-2xl text-xs font-semibold flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div
                class="p-4 bg-rose-50 border border-rose-100 text-rose-800 rounded-2xl text-xs font-semibold flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- ============ AVISO: CUENTA CERRADA ============ --}}
        @if ($cuenta->estado === 'cerrada')
            <div class="p-4 bg-slate-50 border border-slate-200 text-slate-700 rounded-3xl flex items-center gap-4">
                <div
                    class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-500 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-extrabold text-slate-800">Cuenta cerrada</p>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Cerrada el {{ $cuenta->cerrada_en?->format('d/m/Y H:i') }}.
                        Ya no se puede modificar. Solo puedes descargar el estado de cuenta.
                    </p>
                </div>
            </div>
        @endif

        {{-- ============ ENCABEZADO CON TOTALES ============ --}}
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
            <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-5">
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-1 bg-slate-100 text-slate-600 rounded-lg text-[11px] font-mono font-bold">
                            {{ $cuenta->folio }}
                        </span>
                        <span
                            class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide {{ $cuenta->estado_color }}">
                            {{ ucfirst($cuenta->estado) }}
                        </span>
                    </div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mt-4">TOTAL DE LA CUENTA</p>
                    <p class="text-4xl font-black text-slate-800 tracking-tight mt-0.5">
                        {{ $cuenta->total_formateado }}</p>

                    <div class="mt-4 grid grid-cols-3 gap-3">
                        <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-3">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Subtotal</p>
                            <p class="text-sm font-extrabold text-slate-800 mt-0.5">
                                ${{ number_format((float) $cuenta->subtotal, 2) }}</p>
                        </div>
                        <div class="bg-emerald-50/60 border border-emerald-100 rounded-2xl p-3">
                            <p class="text-[10px] font-bold text-emerald-600/70 uppercase tracking-wider">Pagado</p>
                            <p class="text-sm font-extrabold text-emerald-600 mt-0.5">
                                ${{ number_format((float) $cuenta->pagado, 2) }}</p>
                        </div>
                        <div
                            class="{{ (float) $cuenta->saldo > 0 ? 'bg-rose-50/60 border-rose-100' : 'bg-emerald-50/60 border-emerald-100' }} border rounded-2xl p-3">
                            <p
                                class="text-[10px] font-bold uppercase tracking-wider {{ (float) $cuenta->saldo > 0 ? 'text-rose-500/70' : 'text-emerald-600/70' }}">
                                Saldo</p>
                            <p
                                class="text-sm font-extrabold mt-0.5 {{ (float) $cuenta->saldo > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                ${{ number_format((float) $cuenta->saldo, 2) }}</p>
                        </div>
                    </div>
                </div>

                <div class="md:text-right space-y-2 md:max-w-[220px]">
                    {{-- Solo cuenta ABIERTA: botones de cerrar --}}
                    @if ($abierta)
                        @if ((float) $cuenta->saldo > 0)
                            <button type="button" disabled title="No se puede cerrar con saldo pendiente"
                                class="w-full md:w-auto px-4 py-2.5 bg-slate-100 text-slate-400 cursor-not-allowed rounded-xl text-xs font-bold inline-flex items-center justify-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                Cerrar cuenta
                            </button>
                            <p class="text-[11px] text-slate-400 leading-snug">
                                Paga el saldo de
                                <strong class="text-slate-600">${{ number_format((float) $cuenta->saldo, 2) }}</strong>
                                para poder cerrarla.
                            </p>
                        @else
                            <form action="{{ route('cuentas.cerrar', $cuenta) }}" method="POST">
                                @csrf
                                <button onclick="return confirm('¿Cerrar la cuenta? Ya no podrás modificar cargos.')"
                                    class="w-full md:w-auto px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all inline-flex items-center justify-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    Cerrar cuenta
                                </button>
                            </form>
                        @endif
                    @endif

                    {{-- Solo cuenta CERRADA: imprimir estado de cuenta --}}
                    @if ($cuenta->estado === 'cerrada')
                        <a href="{{ route('cuentas.pdf', $cuenta) }}" target="_blank"
                            class="w-full md:w-auto inline-flex items-center justify-center gap-1.5 px-4 py-2.5 bg-slate-800 hover:bg-slate-900 active:scale-95 text-white rounded-xl text-xs font-bold shadow-sm transition-all">
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
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex flex-wrap justify-between items-center gap-3">
                <div>
                    <h3 class="font-extrabold text-slate-800 text-base">Cargos</h3>
                    <p class="text-[11px] text-slate-400">{{ $cuenta->items->count() }} conceptos en la cuenta</p>
                </div>

                {{-- Solo cuenta ABIERTA: botones de agregar --}}
                @if ($abierta)
                    <div class="flex flex-wrap gap-2">
                        <button type="button" onclick="abrirModalServicio()"
                            class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white rounded-xl text-[11px] font-bold shadow-sm transition-all">
                            + Agregar servicio
                        </button>

                        <button type="button"
                            onclick="document.getElementById('modal-cargo').classList.remove('hidden')"
                            class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-700 rounded-xl text-[11px] font-bold transition-all">
                            + Cargo manual
                        </button>

                        <button type="button"
                            onclick="document.getElementById('modal-descuento').classList.remove('hidden')"
                            class="px-3.5 py-2 bg-amber-50 hover:bg-amber-100 active:scale-95 text-amber-700 border border-amber-200 rounded-xl text-[11px] font-bold transition-all">
                            % Descuento
                        </button>
                    </div>
                @else
                    <span class="text-[11px] text-slate-400 font-semibold">🔒 Cuenta cerrada — no editable</span>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-slate-50/70 border-b border-slate-100">
                        <tr>
                            <th class="{{ $thCls }} text-left">Fecha</th>
                            <th class="{{ $thCls }} text-left">Concepto</th>
                            <th class="{{ $thCls }} text-center">Cant.</th>
                            <th class="{{ $thCls }} text-right">P. Unitario</th>
                            <th class="{{ $thCls }} text-right">Descuento</th>
                            <th class="{{ $thCls }} text-right">Importe</th>
                            @if ($abierta)
                                <th class="px-5 py-3.5"></th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($cuenta->items as $item)
                            <tr class="hover:bg-indigo-50/30 transition-colors">
                                <td class="px-5 py-3.5 text-[11px] font-medium text-slate-400 whitespace-nowrap">
                                    {{ $item->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <p class="text-xs font-bold text-slate-800">{{ $item->concepto }}</p>
                                    @if ($item->cita)
                                        <span
                                            class="inline-block mt-1 px-2 py-0.5 bg-indigo-50 text-indigo-600 rounded-full text-[10px] font-bold">
                                            Cita #{{ $item->cita->id }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-center text-xs font-semibold text-slate-700">
                                    {{ $item->cantidad }}</td>
                                <td class="px-5 py-3.5 text-right text-xs font-medium text-slate-600">
                                    ${{ number_format((float) $item->precio_unitario, 2) }}
                                </td>
                                <td class="px-5 py-3.5 text-right text-xs font-semibold text-rose-500">
                                    @if ((float) $item->descuento > 0)
                                        -${{ number_format((float) $item->descuento, 2) }}
                                    @else
                                        <span class="text-slate-300">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-right text-xs font-extrabold text-slate-800">
                                    ${{ number_format((float) $item->importe, 2) }}
                                </td>
                                @if ($abierta)
                                    <td class="px-5 py-3.5 text-right">
                                        <form action="{{ route('cuentas.items.destroy', $item) }}" method="POST"
                                            onsubmit="return confirm('¿Eliminar este cargo?')">
                                            @csrf @method('DELETE')
                                            <button
                                                class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-[11px] font-bold transition-all">
                                                Eliminar
                                            </button>
                                        </form>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $abierta ? 7 : 6 }}" class="px-5 py-12 text-center">
                                    <p class="text-xs font-semibold text-slate-400">Sin cargos.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-slate-50/70 border-t border-slate-100">
                        <tr>
                            <td colspan="5" class="px-5 py-2.5 text-right text-xs font-bold text-slate-500">
                                Subtotal</td>
                            <td class="px-5 py-2.5 text-right text-xs font-extrabold text-slate-800">
                                ${{ number_format((float) $cuenta->subtotal, 2) }}
                            </td>
                            @if ($abierta)
                                <td></td>
                            @endif
                        </tr>
                        @if ((float) $cuenta->descuento_global > 0)
                            <tr>
                                <td colspan="5" class="px-5 py-2.5 text-right text-xs font-bold text-rose-500">
                                    Descuento global
                                    @if ($cuenta->motivo_descuento)
                                        <span
                                            class="font-medium text-rose-400">({{ $cuenta->motivo_descuento }})</span>
                                    @endif
                                </td>
                                <td class="px-5 py-2.5 text-right text-xs font-extrabold text-rose-500">
                                    -${{ number_format((float) $cuenta->descuento_global, 2) }}
                                </td>
                                @if ($abierta)
                                    <td></td>
                                @endif
                            </tr>
                        @endif
                        <tr class="border-t border-slate-200">
                            <td colspan="5" class="px-5 py-3.5 text-right text-sm font-extrabold text-slate-800">
                                Total
                            </td>
                            <td class="px-5 py-3.5 text-right text-base font-black text-indigo-600">
                                {{ $cuenta->total_formateado }}</td>
                            @if ($abierta)
                                <td></td>
                            @endif
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- ============ PAGOS ============ --}}
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex flex-wrap justify-between items-center gap-3">
                <div>
                    <h3 class="font-extrabold text-slate-800 text-base">Pagos registrados</h3>
                    <p class="text-[11px] text-slate-400">{{ $cuenta->pagos->count() }} movimientos</p>
                </div>

                @if ($abierta && (float) $cuenta->saldo > 0)
                    <button type="button" onclick="document.getElementById('modal-pago').classList.remove('hidden')"
                        class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white rounded-xl text-[11px] font-bold shadow-sm transition-all">
                        + Registrar pago
                    </button>
                @elseif ($cuenta->estado === 'cerrada')
                    <span class="text-[11px] text-slate-400 font-semibold">🔒 Cuenta cerrada — no editable</span>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-slate-50/70 border-b border-slate-100">
                        <tr>
                            <th class="{{ $thCls }} text-left">Folio</th>
                            <th class="{{ $thCls }} text-left">Fecha</th>
                            <th class="{{ $thCls }} text-left">Método</th>
                            <th class="{{ $thCls }} text-right">Monto</th>
                            <th class="{{ $thCls }} text-center">Estado</th>
                            <th class="px-5 py-3.5"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($cuenta->pagos as $pago)
                            <tr
                                class="hover:bg-indigo-50/30 transition-colors {{ $pago->estado === 'cancelado' ? 'opacity-50 line-through' : '' }}">
                                <td class="px-5 py-3.5">
                                    <span
                                        class="px-2 py-1 bg-slate-100 text-slate-600 rounded-lg text-[11px] font-mono font-bold">
                                        {{ $pago->folio }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-xs font-medium text-slate-600 whitespace-nowrap">
                                    {{ $pago->pagado_en->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <p class="text-xs font-bold text-slate-700">{{ $pago->metodo_label }}</p>
                                    @if ($pago->referencia)
                                        <p class="text-[10px] font-medium text-slate-400">Ref: {{ $pago->referencia }}
                                        </p>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-right text-xs font-extrabold text-emerald-600">
                                    ${{ number_format((float) $pago->monto, 2) }}
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    @if ($pago->estado === 'aplicado')
                                        <span
                                            class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-full text-[10px] font-bold uppercase tracking-wide">
                                            Aplicado
                                        </span>
                                    @else
                                        <span
                                            class="px-2.5 py-1 bg-rose-50 text-rose-700 border border-rose-100 rounded-full text-[10px] font-bold uppercase tracking-wide">
                                            Cancelado
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex justify-end gap-2">
                                        {{-- El recibo del pago siempre se puede ver --}}
                                        <a href="{{ route('cuentas.pagos.recibo', $pago) }}" target="_blank"
                                            class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg text-[11px] font-bold transition-all">
                                            Recibo
                                        </a>

                                        {{-- Cancelar solo si está abierta --}}
                                        @if ($pago->estado === 'aplicado' && $abierta)
                                            <button type="button"
                                                onclick="cancelarPago({{ $pago->id }}, '{{ $pago->folio }}')"
                                                class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-[11px] font-bold transition-all">
                                                Cancelar
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center">
                                    <p class="text-xs font-semibold text-slate-400">Sin pagos registrados.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-slate-50/70 border-t border-slate-100">
                        <tr>
                            <td colspan="3" class="px-5 py-2.5 text-right text-xs font-bold text-slate-500">Total
                                pagado
                            </td>
                            <td class="px-5 py-2.5 text-right text-xs font-extrabold text-emerald-600">
                                ${{ number_format((float) $cuenta->pagado, 2) }}
                            </td>
                            <td colspan="2"></td>
                        </tr>
                        <tr class="border-t border-slate-200">
                            <td colspan="3" class="px-5 py-3.5 text-right text-sm font-extrabold text-slate-800">
                                Saldo
                                pendiente</td>
                            <td
                                class="px-5 py-3.5 text-right text-base font-black {{ (float) $cuenta->saldo > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                ${{ number_format((float) $cuenta->saldo, 2) }}
                            </td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>

    {{-- =====================================================================
         MODALES — Solo se renderizan si la cuenta está ABIERTA.
         Si está cerrada, no hay formularios activos en la página.
    ====================================================================== --}}
    @if ($abierta)

        {{-- ==================== MODAL AGREGAR SERVICIO ==================== --}}
        <div id="modal-servicio"
            class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div
                class="bg-white rounded-3xl shadow-2xl border border-slate-100 max-w-xl w-full p-6 space-y-5 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center gap-3 border-b border-slate-100 pb-3">
                    <div
                        class="w-10 h-10 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-800 text-base">Agregar servicio</h3>
                        <p class="text-xs text-slate-400">Selecciona del catálogo de servicios</p>
                    </div>
                </div>

                <form action="{{ route('cuentas.items.store', $cuenta) }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="{{ $labelCls }}">Servicio *</label>
                        <select id="servicio-select" name="servicio_id" required class="{{ $inputCls }}">
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
                        <p id="servicio-detalle" class="text-[11px] font-medium text-indigo-600 mt-1.5"></p>
                    </div>

                    <div>
                        <label class="{{ $labelCls }}">Concepto *</label>
                        <input type="text" name="concepto" id="servicio-concepto" required
                            class="{{ $inputCls }}">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="{{ $labelCls }}">Cantidad *</label>
                            <input type="number" name="cantidad" value="1" min="1" required
                                class="{{ $inputCls }}">
                        </div>
                        <div>
                            <label class="{{ $labelCls }}">Precio unitario *</label>
                            <input type="number" name="precio_unitario" id="servicio-precio" step="0.01"
                                min="0" required class="{{ $inputCls }}">
                        </div>
                    </div>

                    <div>
                        <label class="{{ $labelCls }}">Notas</label>
                        <textarea name="notas" rows="2" class="{{ $inputCls }} resize-none"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" onclick="cerrarModalServicio()"
                            class="{{ $btnCancel }}">Cancelar</button>
                        <button type="submit"
                            class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-sm">
                            Agregar servicio
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ==================== MODAL CARGO MANUAL ==================== --}}
        <div id="modal-cargo"
            class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div
                class="bg-white rounded-3xl shadow-2xl border border-slate-100 max-w-lg w-full p-6 space-y-5 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center gap-3 border-b border-slate-100 pb-3">
                    <div
                        class="w-10 h-10 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-600 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-800 text-base">Agregar cargo manual</h3>
                        <p class="text-xs text-slate-400">Para conceptos libres fuera del catálogo</p>
                    </div>
                </div>

                <form action="{{ route('cuentas.items.store', $cuenta) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="{{ $labelCls }}">Concepto *</label>
                        <input type="text" name="concepto" required class="{{ $inputCls }}">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="{{ $labelCls }}">Cantidad *</label>
                            <input type="number" name="cantidad" value="1" min="1" required
                                class="{{ $inputCls }}">
                        </div>
                        <div>
                            <label class="{{ $labelCls }}">Precio unitario *</label>
                            <input type="number" name="precio_unitario" step="0.01" min="0" required
                                class="{{ $inputCls }}">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="{{ $labelCls }}">Descuento</label>
                            <input type="number" name="descuento" step="0.01" min="0" value="0"
                                class="{{ $inputCls }}">
                        </div>
                        <div>
                            <label class="{{ $labelCls }}">Motivo descuento</label>
                            <input type="text" name="motivo_descuento" class="{{ $inputCls }}">
                        </div>
                    </div>
                    <div>
                        <label class="{{ $labelCls }}">Notas</label>
                        <textarea name="notas" rows="2" class="{{ $inputCls }} resize-none"></textarea>
                    </div>
                    <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button"
                            onclick="document.getElementById('modal-cargo').classList.add('hidden')"
                            class="{{ $btnCancel }}">Cancelar</button>
                        <button type="submit"
                            class="px-5 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-bold shadow-sm">
                            Agregar cargo
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ==================== MODAL DESCUENTO GLOBAL ==================== --}}
        <div id="modal-descuento"
            class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div
                class="bg-white rounded-3xl shadow-2xl border border-slate-100 max-w-md w-full p-6 space-y-5 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center gap-3 border-b border-slate-100 pb-3">
                    <div
                        class="w-10 h-10 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-600 shrink-0 font-black text-lg">
                        %
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-800 text-base">Aplicar descuento</h3>
                        <p class="text-xs text-slate-400">Descuento global a toda la cuenta</p>
                    </div>
                </div>

                <div class="p-3 bg-amber-50 border border-amber-100 rounded-2xl text-xs">
                    <p class="text-amber-800">
                        Subtotal actual:
                        <strong>${{ number_format((float) $cuenta->subtotal, 2) }}</strong>
                    </p>
                    @if ((float) $cuenta->descuento_global > 0)
                        <p class="text-amber-800 mt-1">
                            Descuento actual:
                            <strong>-${{ number_format((float) $cuenta->descuento_global, 2) }}</strong>
                            @if ($cuenta->motivo_descuento)
                                <span class="text-[11px]">({{ $cuenta->motivo_descuento }})</span>
                            @endif
                        </p>
                    @endif
                </div>

                <form action="{{ route('cuentas.descuento', $cuenta) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="{{ $labelCls }}">Monto del descuento *</label>
                        <input type="number" name="descuento_global" step="0.01" min="0"
                            max="{{ $cuenta->subtotal }}" required value="{{ (float) $cuenta->descuento_global }}"
                            class="{{ $inputCls }}">
                        <p class="text-[11px] text-slate-400 mt-1">
                            No puede superar el subtotal (${{ number_format((float) $cuenta->subtotal, 2) }}).
                        </p>
                    </div>

                    <div>
                        <label class="{{ $labelCls }}">Motivo</label>
                        <input type="text" name="motivo_descuento" placeholder="Ej. Convenio, cortesía, ajuste"
                            value="{{ $cuenta->motivo_descuento }}" class="{{ $inputCls }}">
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button"
                            onclick="document.getElementById('modal-descuento').classList.add('hidden')"
                            class="{{ $btnCancel }}">Cancelar</button>
                        <button type="submit"
                            class="px-5 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-bold shadow-sm">
                            Aplicar descuento
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ==================== MODAL PAGO ==================== --}}
        <div id="modal-pago"
            class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div
                class="bg-white rounded-3xl shadow-2xl border border-slate-100 max-w-lg w-full p-6 space-y-5 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center gap-3 border-b border-slate-100 pb-3">
                    <div
                        class="w-10 h-10 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-800 text-base">Registrar pago</h3>
                        <p class="text-xs text-slate-400">
                            Saldo pendiente:
                            <strong class="text-rose-600">${{ number_format((float) $cuenta->saldo, 2) }}</strong>
                        </p>
                    </div>
                </div>

                <form action="{{ route('cuentas.pagos.store', $cuenta) }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="{{ $labelCls }}">Monto *</label>
                            <input type="number" name="monto" step="0.01" min="0.01"
                                max="{{ (float) $cuenta->saldo }}" required value="{{ (float) $cuenta->saldo }}"
                                class="{{ $inputCls }}">
                        </div>
                        <div>
                            <label class="{{ $labelCls }}">Método *</label>
                            <select name="metodo" required class="{{ $inputCls }}">
                                <option value="efectivo">Efectivo</option>
                                <option value="tarjeta">Tarjeta</option>
                                <option value="transferencia">Transferencia</option>
                                <option value="otro">Otro</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="{{ $labelCls }}">Referencia (opcional)</label>
                        <input type="text" name="referencia" placeholder="Ej. últimos 4 dígitos, no. autorización"
                            class="{{ $inputCls }}">
                    </div>
                    <div>
                        <label class="{{ $labelCls }}">Notas</label>
                        <textarea name="notas" rows="2" class="{{ $inputCls }} resize-none"></textarea>
                    </div>
                    <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" onclick="document.getElementById('modal-pago').classList.add('hidden')"
                            class="{{ $btnCancel }}">Cancelar</button>
                        <button type="submit"
                            class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm">
                            Registrar pago
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ==================== MODAL CANCELAR PAGO ==================== --}}
        <div id="modal-cancelar-pago"
            class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl shadow-2xl border border-slate-100 max-w-md w-full p-6 space-y-5">
                <div class="flex items-center gap-3 border-b border-slate-100 pb-3">
                    <div
                        class="w-10 h-10 rounded-2xl bg-rose-50 flex items-center justify-center text-rose-500 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-800 text-base">Cancelar pago</h3>
                        <p class="text-xs text-slate-400">
                            Pago <strong id="cancelar-folio" class="font-mono text-slate-600"></strong> · esta acción
                            queda registrada
                        </p>
                    </div>
                </div>

                <form id="form-cancelar-pago" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="{{ $labelCls }}">Motivo de cancelación *</label>
                        <textarea name="motivo_cancelacion" rows="3" required class="{{ $inputCls }} resize-none"></textarea>
                    </div>
                    <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button"
                            onclick="document.getElementById('modal-cancelar-pago').classList.add('hidden')"
                            class="{{ $btnCancel }}">Cancelar</button>
                        <button type="submit"
                            class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-sm">
                            Sí, cancelar pago
                        </button>
                    </div>
                </form>
            </div>
        </div>

    @endif {{-- fin @if ($abierta) --}}

    @push('scripts')
        <script>
            // Solo cargamos el JS si la cuenta está abierta (los modales existen)
            @if ($abierta)

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
