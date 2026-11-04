<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Estado de cuenta {{ $cuenta->folio }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        @page {
            margin: 100px 35px 70px 35px;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10.5px;
            color: #1f2937;
            line-height: 1.45;
        }

        /* ---------- HEADER / FOOTER fijos ---------- */
        header {
            position: fixed;
            top: -75px;
            left: 0;
            right: 0;
            height: 60px;
            border-bottom: 2px solid #1e293b;
            padding-bottom: 8px;
        }

        header .brand {
            display: table;
            width: 100%;
        }

        header .brand-left,
        header .brand-right {
            display: table-cell;
            vertical-align: middle;
        }

        header .brand-left h1 {
            margin: 0;
            font-size: 18px;
            color: #1e293b;
            letter-spacing: 0.5px;
        }

        header .brand-left small {
            color: #64748b;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        header .brand-right {
            text-align: right;
            font-size: 9px;
            color: #64748b;
        }

        header .brand-right strong {
            display: block;
            font-size: 11px;
            color: #1e293b;
        }

        footer {
            position: fixed;
            bottom: -50px;
            left: 0;
            right: 0;
            height: 40px;
            font-size: 8.5px;
            color: #94a3b8;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
        }

        footer .page:after {
            content: "Página " counter(page) " de " counter(pages);
        }

        /* ---------- TÍTULO DEL DOCUMENTO ---------- */
        .doc-title {
            text-align: center;
            margin-bottom: 18px;
        }

        .doc-title h2 {
            margin: 0;
            font-size: 15px;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .doc-title .folio {
            margin-top: 4px;
            font-size: 12px;
            color: #334155;
            font-weight: bold;
            font-family: 'DejaVu Sans Mono', monospace;
        }

        .estado-badge {
            display: inline-block;
            margin-top: 6px;
            padding: 3px 12px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .estado-abierta {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fcd34d;
        }

        .estado-cerrada {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        .estado-cancelada {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        /* ---------- SECCIONES ---------- */
        .section {
            margin-bottom: 18px;
        }

        .section h3 {
            font-size: 10.5px;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0 0 8px 0;
            padding-bottom: 4px;
            border-bottom: 2px solid #1e293b;
        }

        /* ---------- INFO GRID ---------- */
        .info-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 10px 12px;
            display: table;
            width: 100%;
        }

        .info-col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 12px;
        }

        .info-row {
            margin-bottom: 5px;
            font-size: 10px;
        }

        .info-row .label {
            color: #64748b;
            display: inline-block;
            width: 40%;
        }

        .info-row .value {
            color: #1e293b;
            font-weight: bold;
            display: inline-block;
            width: 58%;
        }

        /* ---------- TABLAS ---------- */
        table.data {
            width: 100%;
            border-collapse: collapse;
        }

        table.data thead th {
            background: #1e293b;
            color: #ffffff;
            padding: 7px 8px;
            text-align: left;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        table.data tbody td {
            padding: 7px 8px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }

        table.data tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        table.data tbody tr:last-child td {
            border-bottom: 1px solid #cbd5e1;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .mono {
            font-family: 'DejaVu Sans Mono', monospace;
            font-size: 9.5px;
        }

        .muted {
            color: #94a3b8;
            font-size: 9px;
        }

        .empty-row {
            text-align: center;
            color: #94a3b8;
            padding: 14px 0 !important;
            font-style: italic;
        }

        /* ---------- RESUMEN FINAL ---------- */
        .totales {
            margin-top: 6px;
            width: 55%;
            margin-left: auto;
            border-collapse: collapse;
        }

        .totales td {
            padding: 5px 10px;
            font-size: 10.5px;
        }

        .totales .label {
            color: #475569;
            text-align: right;
        }

        .totales .valor {
            text-align: right;
            font-weight: bold;
            color: #1e293b;
            font-family: 'DejaVu Sans Mono', monospace;
            width: 40%;
        }

        .totales .separator td {
            border-top: 1px solid #cbd5e1;
            padding: 0;
            height: 4px;
        }

        .totales .total-row td {
            background: #1e293b;
            color: #ffffff;
            font-size: 12px;
            padding: 9px 10px;
        }

        .totales .total-row .label {
            color: #cbd5e1;
            text-align: right;
        }

        .totales .total-row .valor {
            color: #ffffff;
            font-size: 13px;
        }

        .totales .saldo-row td {
            background: #fef3c7;
            color: #92400e;
            font-size: 12px;
            padding: 9px 10px;
            border-top: 2px solid #f59e0b;
        }

        .totales .saldo-row .label {
            color: #92400e;
            text-align: right;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .totales .saldo-row .valor {
            color: #92400e;
            font-size: 13px;
        }

        .totales .saldo-cero td {
            background: #d1fae5;
            color: #065f46;
            border-top: 2px solid #10b981;
        }

        .totales .saldo-cero .label,
        .totales .saldo-cero .valor {
            color: #065f46;
        }

        /* ---------- NOTAS / MOTIVO ---------- */
        .nota {
            margin-top: 12px;
            padding: 8px 12px;
            background: #fef9c3;
            border-left: 3px solid #eab308;
            font-size: 9.5px;
            color: #713f12;
        }

        /* ---------- CANCELADO ---------- */
        .cancelado td {
            color: #94a3b8;
            text-decoration: line-through;
        }

        .cancelado td .badge-cancel {
            text-decoration: none;
            display: inline-block;
        }

        .badge-cancel {
            background: #fee2e2;
            color: #991b1b;
            padding: 1px 7px;
            border-radius: 8px;
            font-size: 8.5px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
    </style>
</head>

<body>

    {{-- ============ HEADER (repetido en cada página) ============ --}}
    <header>
        <div class="brand">
            <div class="brand-left">
                <h1>{{ config('app.name', 'HealthNexus') }}</h1>
                <small>Estado de cuenta</small>
            </div>
            <div class="brand-right">
                <strong>{{ $cuenta->folio }}</strong>
                Emitido: {{ now()->format('d/m/Y H:i') }}
            </div>
        </div>
    </header>

    {{-- ============ FOOTER (repetido en cada página) ============ --}}
    <footer>
        <div class="page"></div>
        Documento generado por {{ auth()->user()->nombre_completo ?? (auth()->user()->name ?? 'sistema') }}
        · Este comprobante es de uso interno.
    </footer>

    {{-- ============ TÍTULO ============ --}}
    <div class="doc-title">
        <h2>Estado de cuenta</h2>
        <div class="folio">{{ $cuenta->folio }}</div>
        <span class="estado-badge estado-{{ $cuenta->estado }}">
            {{ strtoupper($cuenta->estado) }}
        </span>
    </div>

    {{-- ============ INFORMACIÓN DEL PACIENTE ============ --}}
    <div class="section">
        <h3>Información del paciente</h3>
        <div class="info-card">
            <div class="info-col">
                <div class="info-row">
                    <span class="label">Nombre:</span>
                    <span class="value">{{ $cuenta->paciente->nombre_completo }}</span>
                </div>
                <div class="info-row">
                    <span class="label">CURP:</span>
                    <span class="value">{{ $cuenta->paciente->curp ?? '—' }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Teléfono:</span>
                    <span class="value">{{ $cuenta->paciente->telefono_principal ?? '—' }}</span>
                </div>
            </div>
            <div class="info-col">
                <div class="info-row">
                    <span class="label">Cuenta creada:</span>
                    <span class="value">{{ $cuenta->created_at->format('d/m/Y H:i') }}</span>
                </div>
                @if ($cuenta->cerrada_en)
                    <div class="info-row">
                        <span class="label">Cuenta cerrada:</span>
                        <span class="value">{{ $cuenta->cerrada_en->format('d/m/Y H:i') }}</span>
                    </div>
                @endif
                <div class="info-row">
                    <span class="label">Estado:</span>
                    <span class="value">{{ ucfirst($cuenta->estado) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ============ CARGOS / SERVICIOS ============ --}}
    <div class="section">
        <h3>Cargos y servicios</h3>
        <table class="data">
            <thead>
                <tr>
                    <th style="width: 13%;">Fecha</th>
                    <th style="width: 42%;">Concepto</th>
                    <th class="text-center" style="width: 7%;">Cant.</th>
                    <th class="text-right" style="width: 14%;">P. Unit.</th>
                    <th class="text-right" style="width: 10%;">Desc.</th>
                    <th class="text-right" style="width: 14%;">Importe</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($cuenta->items as $item)
                    <tr>
                        <td class="mono">{{ $item->created_at->format('d/m/Y') }}</td>
                        <td>
                            <strong>{{ $item->concepto }}</strong>
                            @if ($item->cita)
                                <br><span class="muted">Cita #{{ $item->cita->id }}</span>
                            @endif
                            @if ($item->notas)
                                <br><span class="muted">{{ $item->notas }}</span>
                            @endif
                        </td>
                        <td class="text-center">{{ $item->cantidad }}</td>
                        <td class="text-right mono">${{ number_format((float) $item->precio_unitario, 2) }}</td>
                        <td class="text-right mono">
                            @if ((float) $item->descuento > 0)
                                -${{ number_format((float) $item->descuento, 2) }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="text-right mono"><strong>${{ number_format((float) $item->importe, 2) }}</strong>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty-row">Sin cargos registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ============ PAGOS ============ --}}
    <div class="section">
        <h3>Pagos registrados</h3>
        <table class="data">
            <thead>
                <tr>
                    <th style="width: 18%;">Folio</th>
                    <th style="width: 17%;">Fecha</th>
                    <th style="width: 27%;">Método</th>
                    <th class="text-right" style="width: 20%;">Monto</th>
                    <th class="text-center" style="width: 18%;">Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($cuenta->pagos as $pago)
                    <tr class="{{ $pago->estado === 'cancelado' ? 'cancelado' : '' }}">
                        <td class="mono">{{ $pago->folio }}</td>
                        <td class="mono">{{ $pago->pagado_en->format('d/m/Y H:i') }}</td>
                        <td>
                            {{ $pago->metodo_label }}
                            @if ($pago->referencia)
                                <br><span class="muted">Ref: {{ $pago->referencia }}</span>
                            @endif
                        </td>
                        <td class="text-right mono">${{ number_format((float) $pago->monto, 2) }}</td>
                        <td class="text-center">
                            @if ($pago->estado === 'aplicado')
                                <span style="color: #059669; font-weight: bold;">Aplicado</span>
                            @else
                                <span class="badge-cancel">CANCELADO</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="empty-row">Sin pagos registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ============ RESUMEN FINAL ============ --}}
    <div class="section">
        <h3>Resumen</h3>
        <table class="totales">
            <tr>
                <td class="label">Subtotal:</td>
                <td class="valor">${{ number_format((float) $cuenta->subtotal, 2) }}</td>
            </tr>
            @if ((float) $cuenta->descuento_global > 0)
                <tr>
                    <td class="label">Descuento global:</td>
                    <td class="valor" style="color: #dc2626;">
                        -${{ number_format((float) $cuenta->descuento_global, 2) }}
                    </td>
                </tr>
            @endif
            <tr class="separator">
                <td colspan="2"></td>
            </tr>
            <tr>
                <td class="label">Total pagado:</td>
                <td class="valor" style="color: #059669;">
                    ${{ number_format((float) $cuenta->pagado, 2) }}
                </td>
            </tr>
            <tr class="total-row">
                <td class="label">Total:</td>
                <td class="valor">${{ number_format((float) $cuenta->total, 2) }}</td>
            </tr>
            @php $saldo = (float) $cuenta->saldo; @endphp
            <tr class="{{ $saldo > 0 ? 'saldo-row' : 'saldo-row saldo-cero' }}">
                <td class="label">Saldo pendiente:</td>
                <td class="valor">${{ number_format($saldo, 2) }}</td>
            </tr>
        </table>
    </div>

    @if ($cuenta->motivo_descuento)
        <div class="nota">
            <strong>Motivo del descuento:</strong> {{ $cuenta->motivo_descuento }}
        </div>
    @endif

</body>

</html>
