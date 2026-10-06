@php
    $logoPath = public_path('images/logo-healthnexus.png');
    $logo = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : null;
    $p = $consulta->paciente;
    $dx = $consulta->diagnosticoPrincipal
        ? '[' . $consulta->diagnosticoPrincipal->codigo . '] ' . $consulta->diagnosticoPrincipal->nombre
        : $consulta->diagnostico_principal ?? '—';
@endphp
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Receta médica — {{ $p->nombre_completo }}</title>
    <style>
        @page {
            margin: 30px 36px 55px 36px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #222;
            line-height: 1.45;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        /* Encabezado */
        .head td {
            vertical-align: middle;
            padding: 0 0 10px 0;
        }

        .head img {
            height: 52px;
        }

        .clinic {
            font-size: 17px;
            font-weight: bold;
            color: #312e81;
        }

        .clinic-sub {
            font-size: 9.5px;
            color: #666;
        }

        .right {
            text-align: right;
        }

        .doc-title {
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .rule {
            border-top: 2px solid #4338ca;
            margin-bottom: 4px;
        }

        h2 {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #312e81;
            margin: 16px 0 5px;
        }

        /* Tablas */
        .grid th,
        .grid td {
            border: 1px solid #cfd2d9;
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
        }

        .grid th {
            background: #f1f2f6;
            font-size: 9px;
            font-weight: bold;
            color: #444;
            text-transform: uppercase;
        }

        .grid td.k {
            background: #f1f2f6;
            width: 17%;
            font-size: 9px;
            font-weight: bold;
            color: #444;
            text-transform: uppercase;
        }

        .rx-mark {
            font-size: 22px;
            font-weight: bold;
            color: #312e81;
            margin: 14px 0 0;
        }

        /* Firma */
        .firma {
            margin-top: 60px;
            text-align: center;
        }

        .firma-line {
            border-top: 1px solid #333;
            width: 240px;
            margin: 0 auto;
            padding-top: 4px;
            font-weight: bold;
        }

        .firma small {
            color: #666;
        }

        /* Pie */
        .footer {
            position: fixed;
            bottom: -38px;
            left: 0;
            right: 0;
            border-top: 1px solid #cfd2d9;
            padding-top: 5px;
            font-size: 8.5px;
            color: #888;
        }

        .pagenum:before {
            content: counter(page);
        }
    </style>
</head>

<body>

    <div class="footer">
        <table>
            <tr>
                <td>HealthNexus · Sistema Médico — Esta receta es válida únicamente con la firma del médico.</td>
                <td class="right">Página <span class="pagenum"></span></td>
            </tr>
        </table>
    </div>

    {{-- Encabezado --}}
    <table class="head">
        <tr>
            @if ($logo)
                <td style="width: 70px;"><img src="{{ $logo }}" alt="HealthNexus"></td>
            @endif
            <td>
                <div class="clinic">HealthNexus</div>
                <div class="clinic-sub">Sistema Médico</div>
            </td>
            <td class="right">
                <div class="doc-title">Receta médica</div>
                <div class="clinic-sub">Fecha: {{ $consulta->created_at->format('d/m/Y') }}</div>
            </td>
        </tr>
    </table>
    <div class="rule"></div>

    {{-- Paciente --}}
    <h2>Datos del paciente</h2>
    <table class="grid">
        <tr>
            <td class="k">Paciente</td>
            <td colspan="3"><strong>{{ $p->nombre_completo }}</strong></td>
        </tr>
        <tr>
            <td class="k">Edad</td>
            <td>{{ $p->edad }} años</td>
            <td class="k">Fecha</td>
            <td>{{ $consulta->created_at->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td class="k">Diagnóstico</td>
            <td colspan="3">{{ $dx }}</td>
        </tr>
    </table>

    {{-- Medicamentos --}}
    @if ($consulta->medicamentos->count())
        <div class="rx-mark">℞</div>
        <table class="grid" style="margin-top: 6px;">
            <tr>
                <th>Medicamento</th>
                <th>Dosis</th>
                <th>Vía</th>
                <th>Frecuencia</th>
                <th>Duración</th>
                <th>Indicaciones</th>
            </tr>
            @foreach ($consulta->medicamentos as $m)
                <tr>
                    <td><strong>{{ $m->nombre }}</strong> {{ $m->concentracion }}</td>
                    <td>{{ $m->pivot->dosis }}</td>
                    <td>{{ $m->pivot->via }}</td>
                    <td>{{ $m->pivot->frecuencia }}</td>
                    <td>{{ $m->pivot->duracion }}</td>
                    <td>{{ $m->pivot->indicaciones }}</td>
                </tr>
            @endforeach
        </table>
    @endif

    @if ($consulta->receta_libre)
        <h2>Indicaciones adicionales</h2>
        <table class="grid">
            <tr>
                <td>{{ $consulta->receta_libre }}</td>
            </tr>
        </table>
    @endif

    {{-- Firma --}}
    <div class="firma">
        <div class="firma-line">Dr. {{ $consulta->medico->nombre_completo }}</div>
        <small>Cédula profesional: {{ $consulta->medico->cedula_profesional ?? '—' }}</small>
    </div>

</body>

</html>