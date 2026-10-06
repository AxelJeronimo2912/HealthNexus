@php
    $logoPath = public_path('images/logo-healthnexus.png');
    $logo = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : null;
    $p = $consulta->paciente;
@endphp
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Consulta médica — {{ $p->nombre_completo }}</title>
    <style>
        @page {
            margin: 30px 36px 55px 36px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10.5px;
            color: #222;
            line-height: 1.4;
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

        /* Títulos de sección */
        h2 {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #312e81;
            margin: 16px 0 5px;
        }

        /* Tablas de datos */
        .grid th,
        .grid td {
            border: 1px solid #cfd2d9;
            padding: 5px 8px;
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

        .center {
            text-align: center !important;
        }

        .alert {
            color: #b91c1c;
            font-weight: bold;
        }

        .dx-code {
            font-weight: bold;
            color: #312e81;
        }

        /* Firma */
        .firma {
            margin-top: 55px;
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
                <td>HealthNexus · Sistema Médico — Generado el {{ now()->format('d/m/Y H:i') }}</td>
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
                <div class="doc-title">Consulta médica</div>
                <div class="clinic-sub">Fecha: {{ $consulta->created_at->format('d/m/Y H:i') }}</div>
            </td>
        </tr>
    </table>
    <div class="rule"></div>

    {{-- Paciente --}}
    <h2>Datos del paciente</h2>
    <table class="grid">
        <tr>
            <td class="k">Nombre</td>
            <td colspan="3"><strong>{{ $p->nombre_completo }}</strong></td>
        </tr>
        <tr>
            <td class="k">Edad</td>
            <td>{{ $p->edad }} años</td>
            <td class="k">Sexo</td>
            <td>{{ ucfirst($p->sexo) }}</td>
        </tr>
        <tr>
            <td class="k">CURP</td>
            <td>{{ $p->curp ?? '—' }}</td>
            <td class="k">Tipo sanguíneo</td>
            <td>{{ $p->tipo_sanguineo ?? '—' }}</td>
        </tr>
        <tr>
            <td class="k">Alergias</td>
            <td colspan="3" class="{{ $p->alergias ? 'alert' : '' }}">{{ $p->alergias ?? 'Ninguna' }}</td>
        </tr>
    </table>

    {{-- Signos vitales --}}
    <h2>Signos vitales</h2>
    <table class="grid">
        <tr>
            <th class="center">Temp. (°C)</th>
            <th class="center">FC (lpm)</th>
            <th class="center">FR (rpm)</th>
            <th class="center">TA (mmHg)</th>
            <th class="center">SpO₂ (%)</th>
            <th class="center">Glucosa</th>
        </tr>
        <tr>
            <td class="center">{{ $consulta->temperatura ?? '—' }}</td>
            <td class="center">{{ $consulta->frecuencia_cardiaca ?? '—' }}</td>
            <td class="center">{{ $consulta->frecuencia_respiratoria ?? '—' }}</td>
            <td class="center">{{ $consulta->presion_arterial ?? '—' }}</td>
            <td class="center">{{ $consulta->saturacion_oxigeno ?? '—' }}</td>
            <td class="center">{{ $consulta->glucosa ?? '—' }}</td>
        </tr>
    </table>

    {{-- Somatometría --}}
    <h2>Somatometría</h2>
    <table class="grid">
        <tr>
            <th class="center">Peso (kg)</th>
            <th class="center">Talla (m)</th>
            <th class="center">IMC</th>
            <th class="center">Perímetro abdominal (cm)</th>
        </tr>
        <tr>
            <td class="center">{{ $consulta->peso ?? '—' }}</td>
            <td class="center">{{ $consulta->talla ?? '—' }}</td>
            <td class="center">{{ $consulta->imc ?? '—' }}</td>
            <td class="center">{{ $consulta->perimetro_abdominal ?? '—' }}</td>
        </tr>
    </table>

    {{-- SOAP --}}
    <h2>Nota clínica (SOAP)</h2>
    <table class="grid">
        <tr>
            <td class="k">Subjetivo</td>
            <td>{{ $consulta->subjetivo ?? '—' }}</td>
        </tr>
        <tr>
            <td class="k">Objetivo</td>
            <td>{{ $consulta->objetivo ?? '—' }}</td>
        </tr>
        <tr>
            <td class="k">Análisis</td>
            <td>{{ $consulta->analisis ?? '—' }}</td>
        </tr>
        <tr>
            <td class="k">Plan</td>
            <td>{{ $consulta->plan ?? '—' }}</td>
        </tr>
    </table>

    {{-- Diagnóstico --}}
    <h2>Diagnóstico</h2>
    <table class="grid">
        <tr>
            <td class="k">Principal</td>
            <td>
                @if ($consulta->diagnosticoPrincipal)
                    <span class="dx-code">[{{ $consulta->diagnosticoPrincipal->codigo }}]</span>
                    {{ $consulta->diagnosticoPrincipal->nombre }}
                @else
                    —
                @endif
            </td>
        </tr>
        <tr>
            <td class="k">Secundario</td>
            <td>
                @if ($consulta->diagnosticoSecundario)
                    <span class="dx-code">[{{ $consulta->diagnosticoSecundario->codigo }}]</span>
                    {{ $consulta->diagnosticoSecundario->nombre }}
                @else
                    —
                @endif
            </td>
        </tr>
    </table>

    {{-- Receta --}}
    @if ($consulta->medicamentos->count())
        <h2>Receta</h2>
        <table class="grid">
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
        <h2>Receta libre</h2>
        <table class="grid">
            <tr>
                <td>{{ $consulta->receta_libre }}</td>
            </tr>
        </table>
    @endif

    @if ($consulta->notas)
        <h2>Notas</h2>
        <table class="grid">
            <tr>
                <td>{{ $consulta->notas }}</td>
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