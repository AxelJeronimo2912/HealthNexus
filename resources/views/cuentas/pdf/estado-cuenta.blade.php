<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Estado de cuenta {{ $cuenta->folio }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #333;
            padding: 30px;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #1e293b;
        }

        .header h1 {
            margin: 0;
            font-size: 22px;
            color: #1e293b;
        }

        .header p {
            margin: 4px 0;
            color: #666;
        }

        .folio {
            font-size: 14px;
            color: #1e293b;
            font-weight: bold;
            margin-top: 6px;
        }

        .estado-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: bold;
            margin-top: 6px;
        }

        .estado-abierta {
            background: #fef3c7;
            color: #92400e;
        }

        .estado-cerrada {
            background: #d1fae5;
            color: #065f46;
        }

        .estado-cancelada {
            background: #fee2e2;
            color: #991b1b;
        }

        .section {
            margin-bottom: 20px;
        }

        .section h2 {
            font-size: 13px;
            color: #1e293b;
            text-transform: uppercase;
            margin: 0 0 8px 0;
            padding-bottom: 4px;
            border-bottom: 1px solid #e2e8f0;
        }

        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .info-grid .row {
            display: table-row;
        }

        .info-grid .cell {
            display: table-cell;
            padding: 4px 8px;
            vertical-align: top;
        }

        .info-grid .label {
            color: #666;
            width: 30%;
        }

        .info-grid .value {
            font-weight: bold;
            color: #1e293b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th {
            background: #f1f5f9;
            padding: 6px 8px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            color: #475569;
            border-bottom: 1px solid #cbd5e1;
        }

        td {
            padding: 6px 8px;
            border-bottom: 1px solid #e2e8f0;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        tfoot td {
            background: #f8fafc;
            font-weight: bold;
        }

        .total-final {
            font-size: 14px;
            color: #1e293b;
        }

        .saldo-pendiente {
            color: #dc2626;
        }

        .saldo-cero {
            color: #059669;
        }

        .cancelado {
            text-decoration: line-through;
            color: #94a3b8;
        }

        .resumen {
            margin-top: 20px;
            padding: 15px;
            background: #f8fafc;
            border-left: 4px solid #1e293b;
        }

        .resumen table td {
            padding: 4px 8px;
            border: none;
        }

        .resumen .label {
            color: #666;
        }

        .resumen .valor {
            font-weight: bold;
            text-align: right;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            color: #94a3b8;
            font-size: 9px;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
    </style>
</head>

<body>

    {{-- ============ HEADER ============ --}}
    <div class="header">
        <h1>{{ config('app.name', 'HealthNexus') }}</h1>
        <p>Estado de cuenta</p>
        <p class="folio">{{ $cuenta->folio }}</p>
        <span class="estado-badge estado-{{ $cuenta->estado }}">
            {{ strtoupper($cuenta->estado) }}
        </span>
    </div>

    {{-- ============ INFORMACIÓN DEL PACIENTE ============ --}}
    <div class="section">
        <h2>Información del paciente</h2>
        <div class="info-grid">
            <div class="row">
                <div class="cell label">Nombre:</div>
                <div class="cell value">{{ $cuenta->paciente->nombre_completo }}</div>
                <div class="cell label">Fecha de emisión:</div>
                <div class="cell value">{{ now()->format('d/m/Y H:i') }}</div>
            </div>
            <div class="row">
                <div class="cell label">CURP:</div>
                <div class="cell value">{{ $cuenta->paciente->curp ?? '—' }}</div>
                <div class="cell label">Teléfono:</div>
                <div class="cell value">{{ $cuenta->paciente->telefono_principal ?? '—' }}</div>
            </div>
            <div class="row">
                <div class="cell label">Cuenta creada:</div>
                <div class="cell value">{{ $cuenta->created_at->format('d/m/Y H:i') }}</div>
                @if ($cuenta->cerrada_en)
                    <div class="cell label">Cuenta cerrada:</div>
                    <div class="cell value">{{ $cuenta->cerrada_en->format('d/m/Y H:i') }}</div>
                @endif
            </div>
        </div>
    </div>

    {{-- ============ CARGOS / SERVICIOS ============ --}}
    <div class="section">
        <h2>Cargos y servicios</h2>
        <table>
            <thead>
                <tr>
                    <th style="width: 15%;">Fecha</th>
                    <th style="width: 45%;">Concepto</th>
                    <th class="text-center" style="width: 8%;">Cant.</th>
                    <th class="text-right" style="width: 16%;">P. Unitario</th>
                    <th class="text-right" style="width: 16%;">Importe</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($cuenta->items as $item)
                    <tr>
                        <td>{{ $item->created_at->format('d/m/Y') }}</td>
                        <td>
                            {{ $item->concepto }}
                            @if ($item->cita)
                                <br><span style="font-size: 9px; color: #94a3b8;">Cita #{{ $item->cita->id }}</span>
                            @endif
                        </td>
                        <td class="text-center">{{ $item->cantidad }}</td>
                        <td class="text-right">${{ number_format((float) $item->precio_unitario, 2) }}</td>
                        <td class="text-right">${{ number_format((float) $item->importe, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center" style="color: #94a3b8;">Sin cargos registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ============ PAGOS ============ --}}
    <div class="section">
        <h2>Pagos registrados</h2>
        <table>
            <thead>
                <tr>
                    <th style="width: 20%;">Folio</th>
                    <th style="width: 20%;">Fecha</th>
                    <th style="width: 25%;">Método</th>
                    <th class="text-right" style="width: 20%;">Monto</th>
                    <th class="text-center" style="width: 15%;">Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($cuenta->pagos as $pago)
                    <tr class="{{ $pago->estado === 'cancelado' ? 'cancelado' : '' }}">
                        <td>{{ $pago->folio }}</td>
                        <td>{{ $pago->pagado_en->format('d/m/Y H:i') }}</td>
                        <td>
                            {{ $pago->metodo_label }}
                            @if ($pago->referencia)
                                <br><span style="font-size: 9px; color: #94a3b8;">Ref: {{ $pago->referencia }}</span>
                            @endif
                        </td>
                        <td class="text-right">${{ number_format((float) $pago->monto, 2) }}</td>
                        <td class="text-center">
                            {{ $pago->estado === 'aplicado' ? 'Aplicado' : 'Cancelado' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center" style="color: #94a3b8;">Sin pagos registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ============ RESUMEN FINAL ============ --}}
    <div class="resumen">
        <table>
            <tr>
                <td class="label">Subtotal:</td>
                <td class="valor">${{ number_format((float) $cuenta->subtotal, 2) }}</td>
            </tr>
            @if ((float) $cuenta->descuento_global > 0)
                <tr>
                    <td class="label">Descuento global:</td>
                    <td class="valor" style="color: #dc2626;">
                        -${{ number_format((float) $cuenta->descuento_global, 2) }}
                        @if ($cuenta->motivo_descuento)
                            <br><span style="font-size: 9px; color: #94a3b8; font-weight: normal;">
                                {{ $cuenta->motivo_descuento }}
                            </span>
                        @endif
                    </td>
                </tr>
            @endif
            <tr style="border-top: 1px solid #cbd5e1;">
                <td class="label total-final">TOTAL:</td>
                <td class="valor total-final">${{ number_format((float) $cuenta->total, 2) }}</td>
            </tr>
            <tr>
                <td class="label">Total pagado:</td>
                <td class="valor" style="color: #059669;">
                    ${{ number_format((float) $cuenta->pagado, 2) }}
                </td>
            </tr>
            <tr style="border-top: 2px solid #1e293b;">
                <td class="label total-final">SALDO PENDIENTE:</td>
                <td class="valor total-final {{ (float) $cuenta->saldo > 0 ? 'saldo-pendiente' : 'saldo-cero' }}">
                    ${{ number_format((float) $cuenta->saldo, 2) }}
                </td>
            </tr>
        </table>
    </div>

    {{-- ============ FOOTER ============ --}}
    <div class="footer">
        <p>
            Documento generado el {{ now()->format('d/m/Y \a \l\a\s H:i') }} por
            {{ auth()->user()->nombre_completo ?? (auth()->user()->name ?? 'sistema') }}.
        </p>
        <p>Este estado de cuenta es un comprobante interno de {{ config('app.name', 'HealthNexus') }}.</p>
    </div>

</body>

</html>
