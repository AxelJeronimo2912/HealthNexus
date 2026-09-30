<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Recibo {{ $pago->folio }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #333;
            padding: 30px;
            margin: 0;
        }

        /* ============ HEADER ============ */
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
            font-family: monospace;
        }

        /* ============ BADGES DE ESTADO ============ */
        .estado-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: bold;
            margin-top: 8px;
            text-transform: uppercase;
        }

        .estado-aplicado {
            background: #d1fae5;
            color: #065f46;
        }

        .estado-cancelado {
            background: #fee2e2;
            color: #991b1b;
        }

        .cancelado-warning {
            background: #fef2f2;
            border-left: 4px solid #dc2626;
            padding: 12px;
            margin: 20px 0;
            color: #991b1b;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* ============ SECCIONES ============ */
        .section {
            margin-bottom: 20px;
        }

        .section h2 {
            font-size: 12px;
            color: #1e293b;
            text-transform: uppercase;
            margin: 0 0 8px 0;
            padding-bottom: 4px;
            border-bottom: 1px solid #e2e8f0;
            letter-spacing: 0.5px;
        }

        .info-grid {
            display: table;
            width: 100%;
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
            width: 35%;
        }

        .info-grid .value {
            font-weight: bold;
            color: #1e293b;
        }

        /* ============ TABLA DE RESUMEN ============ */
        table.resumen {
            width: 100%;
            border-collapse: collapse;
        }

        table.resumen td {
            padding: 6px 10px;
            border-bottom: 1px solid #e2e8f0;
        }

        table.resumen td:last-child {
            text-align: right;
            font-weight: bold;
            color: #1e293b;
        }

        table.resumen tr:last-child td {
            border-bottom: none;
        }

        /* ============ TOTAL BOX ============ */
        .total-box {
            border: 2px solid #10b981;
            background: #f0fdf4;
            padding: 20px;
            text-align: center;
            margin: 25px 0;
            border-radius: 6px;
        }

        .total-box .label {
            color: #065f46;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: bold;
        }

        .total-box .amount {
            font-size: 32px;
            font-weight: bold;
            color: #065f46;
            margin-top: 8px;
        }

        /* ============ SECCIÓN DE CUENTA ============ */
        .cuenta-resumen {
            background: #f8fafc;
            padding: 15px;
            border-left: 4px solid #1e293b;
            border-radius: 0 4px 4px 0;
        }

        .cuenta-resumen table {
            width: 100%;
        }

        .cuenta-resumen td {
            padding: 4px 0;
        }

        .cuenta-resumen td:last-child {
            text-align: right;
            font-weight: bold;
        }

        .saldo-pendiente {
            color: #dc2626;
        }

        .saldo-cero {
            color: #059669;
        }

        /* ============ FOOTER ============ */
        .footer {
            margin-top: 40px;
            padding-top: 12px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            color: #94a3b8;
            font-size: 9px;
            line-height: 1.5;
        }

        /* ============ FIRMA ============ */
        .firma {
            margin-top: 50px;
            text-align: center;
            display: table;
            width: 100%;
        }

        .firma .col {
            display: table-cell;
            width: 50%;
            padding: 0 20px;
        }

        .firma .linea {
            border-top: 1px solid #94a3b8;
            padding-top: 6px;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>

<body>

    {{-- ============ HEADER ============ --}}
    <div class="header">
        <h1>{{ config('app.name', 'HealthNexus') }}</h1>
        <p>Recibo de pago</p>
        <p class="folio">{{ $pago->folio }}</p>

        <span class="estado-badge estado-{{ $pago->estado }}">
            {{ $pago->estado === 'aplicado' ? 'Aplicado' : 'Cancelado' }}
        </span>
    </div>

    {{-- ============ AVISO SI CANCELADO ============ --}}
    @if ($pago->estado === 'cancelado')
        <div class="cancelado-warning">
            *** Este pago ha sido cancelado ***
            @if ($pago->cancelado_en)
                <div style="font-weight: normal; margin-top: 4px; font-size: 10px;">
                    Cancelado el {{ $pago->cancelado_en->format('d/m/Y H:i') }}
                    @if ($pago->canceladoPor)
                        por {{ $pago->canceladoPor->nombre_completo ?? $pago->canceladoPor->name }}
                    @endif
                </div>
            @endif
            @if ($pago->motivo_cancelacion)
                <div style="font-weight: normal; margin-top: 6px; font-size: 10px; font-style: italic;">
                    Motivo: {{ $pago->motivo_cancelacion }}
                </div>
            @endif
        </div>
    @endif

    {{-- ============ INFORMACIÓN DEL PAGO ============ --}}
    <div class="section">
        <h2>Información del pago</h2>
        <div class="info-grid">
            <div class="row">
                <div class="cell label">Paciente:</div>
                <div class="cell value">{{ $pago->cuenta->paciente->nombre_completo }}</div>
            </div>
            <div class="row">
                <div class="cell label">Cuenta:</div>
                <div class="cell value">{{ $pago->cuenta->folio }}</div>
            </div>
            <div class="row">
                <div class="cell label">Fecha del pago:</div>
                <div class="cell value">{{ $pago->pagado_en->format('d/m/Y H:i') }}</div>
            </div>
            <div class="row">
                <div class="cell label">Método:</div>
                <div class="cell value">{{ $pago->metodo_label }}</div>
            </div>
            @if ($pago->referencia)
                <div class="row">
                    <div class="cell label">Referencia:</div>
                    <div class="cell value">{{ $pago->referencia }}</div>
                </div>
            @endif
            <div class="row">
                <div class="cell label">Registrado por:</div>
                <div class="cell value">
                    {{ $pago->user?->nombre_completo ?? ($pago->user?->name ?? '—') }}
                </div>
            </div>
        </div>
    </div>

    {{-- ============ TOTAL BOX ============ --}}
    <div class="total-box">
        <div class="label">Monto recibido</div>
        <div class="amount">${{ number_format((float) $pago->monto, 2) }}</div>
    </div>

    {{-- ============ RESUMEN DE LA CUENTA ============ --}}
    <div class="section">
        <h2>Resumen de la cuenta</h2>
        <div class="cuenta-resumen">
            <table>
                <tr>
                    <td>Total de la cuenta:</td>
                    <td>${{ number_format((float) $pago->cuenta->total, 2) }}</td>
                </tr>
                <tr>
                    <td>Total pagado (incluyendo este pago):</td>
                    <td style="color: #059669;">
                        ${{ number_format((float) $pago->cuenta->pagado, 2) }}
                    </td>
                </tr>
                <tr>
                    <td>Saldo pendiente:</td>
                    <td class="{{ (float) $pago->cuenta->saldo > 0 ? 'saldo-pendiente' : 'saldo-cero' }}">
                        ${{ number_format((float) $pago->cuenta->saldo, 2) }}
                    </td>
                </tr>
                <tr>
                    <td>Estado de la cuenta:</td>
                    <td style="text-transform: capitalize;">
                        {{ $pago->cuenta->estado }}
                    </td>
                </tr>
            </table>
        </div>
    </div>

    {{-- ============ FIRMAS ============ --}}
    @if ($pago->estado === 'aplicado')
        <div class="firma">
            <div class="col">
                <div class="linea">
                    Firma del paciente
                    <br>
                    {{ $pago->cuenta->paciente->nombre_completo }}
                </div>
            </div>
            <div class="col">
                <div class="linea">
                    Recibió
                    <br>
                    {{ $pago->user?->nombre_completo ?? ($pago->user?->name ?? '—') }}
                </div>
            </div>
        </div>
    @endif

    {{-- ============ FOOTER ============ --}}
    <div class="footer">
        <p>
            Documento generado el {{ now()->format('d/m/Y \a \l\a\s H:i') }}.
        </p>
        <p>
            Este recibo es un comprobante interno de
            <strong>{{ config('app.name', 'HealthNexus') }}</strong>.
            Conserve este documento para cualquier aclaración.
        </p>
    </div>

</body>

</html>
