<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Consulta</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #111;
        }

        h1 {
            font-size: 16px;
            margin: 0 0 4px;
        }

        h2 {
            font-size: 13px;
            margin: 16px 0 6px;
            border-bottom: 1px solid #999;
            padding-bottom: 2px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }

        td,
        th {
            border: 1px solid #ccc;
            padding: 4px 6px;
            text-align: left;
        }

        .label {
            color: #555;
        }

        .grid2 {
            width: 100%;
        }

        .grid2 td {
            border: none;
            padding: 2px 0;
        }

        .firma {
            margin-top: 60px;
            text-align: center;
        }

        .firma-line {
            border-top: 1px solid #333;
            width: 250px;
            margin: 0 auto;
            padding-top: 4px;
        }
    </style>
</head>

<body>

    <h1>HealthNexus — Consulta médica</h1>
    <p class="label">Fecha: {{ $consulta->created_at->format('d/m/Y H:i') }}</p>
    <p class="label">Médico: {{ $consulta->medico->nombre_completo }}</p>

    <h2>Datos del paciente</h2>
    <table class="grid2">
        <tr>
            <td><span class="label">Nombre:</span> {{ $consulta->paciente->nombre_completo }}</td>
            <td><span class="label">Edad:</span> {{ $consulta->paciente->edad }} años</td>
        </tr>
        <tr>
            <td><span class="label">Sexo:</span> {{ ucfirst($consulta->paciente->sexo) }}</td>
            <td><span class="label">CURP:</span> {{ $consulta->paciente->curp ?? '—' }}</td>
        </tr>
        <tr>
            <td><span class="label">Alergias:</span> {{ $consulta->paciente->alergias ?? 'Ninguna' }}</td>
            <td><span class="label">Tipo sanguíneo:</span> {{ $consulta->paciente->tipo_sanguineo ?? '—' }}</td>
        </tr>
    </table>

    <h2>Signos vitales</h2>
    <table>
        <tr>
            <th>Temp.</th>
            <th>FC</th>
            <th>FR</th>
            <th>TA</th>
            <th>SpO₂</th>
            <th>Glucosa</th>
        </tr>
        <tr>
            <td>{{ $consulta->temperatura ?? '—' }} °C</td>
            <td>{{ $consulta->frecuencia_cardiaca ?? '—' }}</td>
            <td>{{ $consulta->frecuencia_respiratoria ?? '—' }}</td>
            <td>{{ $consulta->presion_arterial ?? '—' }}</td>
            <td>{{ $consulta->saturacion_oxigeno ?? '—' }}%</td>
            <td>{{ $consulta->glucosa ?? '—' }}</td>
        </tr>
    </table>

    <h2>Somatometría</h2>
    <table>
        <tr>
            <th>Peso</th>
            <th>Talla</th>
            <th>IMC</th>
            <th>Perímetro abdominal</th>
        </tr>
        <tr>
            <td>{{ $consulta->peso ?? '—' }} kg</td>
            <td>{{ $consulta->talla ?? '—' }} m</td>
            <td>{{ $consulta->imc ?? '—' }}</td>
            <td>{{ $consulta->perimetro_abdominal ?? '—' }} cm</td>
        </tr>
    </table>

    <h2>SOAP</h2>
    <p><strong>S — Subjetivo:</strong><br>{{ $consulta->subjetivo ?? '—' }}</p>
    <p><strong>O — Objetivo:</strong><br>{{ $consulta->objetivo ?? '—' }}</p>
    <p><strong>A — Análisis:</strong><br>{{ $consulta->analisis ?? '—' }}</p>
    <p><strong>P — Plan:</strong><br>{{ $consulta->plan ?? '—' }}</p>

    <h2>Diagnóstico</h2>
    <p>
        <strong>Principal:</strong>
        @if ($consulta->diagnosticoPrincipal)
            [{{ $consulta->diagnosticoPrincipal->codigo }}]
            {{ $consulta->diagnosticoPrincipal->nombre }}
        @else
            —
        @endif
    </p>
    <p>
        <strong>Secundario:</strong>
        @if ($consulta->diagnosticoSecundario)
            [{{ $consulta->diagnosticoSecundario->codigo }}]
            {{ $consulta->diagnosticoSecundario->nombre }}
        @else
            —
        @endif
    </p>
    @if ($consulta->medicamentos->count())
        <h2>Receta</h2>
        <table>
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
                    <td>{{ $m->nombre }} {{ $m->concentracion }}</td>
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
        <p>{{ $consulta->receta_libre }}</p>
    @endif

    @if ($consulta->notas)
        <h2>Notas</h2>
        <p>{{ $consulta->notas }}</p>
    @endif

    <div class="firma">
        <div class="firma-line">Firma del médico</div>
        <p>{{ $consulta->medico->nombre_completo }}</p>
        <p>Cédula: {{ $consulta->medico->cedula_profesional ?? '—' }}</p>
    </div>

</body>

</html>
