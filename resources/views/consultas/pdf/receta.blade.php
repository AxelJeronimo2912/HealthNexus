<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Receta</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #111;
        }

        h1 {
            font-size: 18px;
            margin-bottom: 4px;
        }

        .hospital {
            text-align: center;
            margin-bottom: 16px;
        }

        .paciente {
            margin-bottom: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #666;
            padding: 5px 8px;
            text-align: left;
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

    <div class="hospital">
        <h1>HealthNexus</h1>
        <p>Receta médica</p>
    </div>

    <div class="paciente">
        <p><strong>Paciente:</strong> {{ $consulta->paciente->nombre_completo }}</p>
        <p><strong>Edad:</strong> {{ $consulta->paciente->edad }} años</p>
        <p><strong>Fecha:</strong> {{ $consulta->created_at->format('d/m/Y') }}</p>
        <p><strong>Diagnóstico:</strong> {{ $consulta->diagnostico_principal ?? '—' }}</p>
    </div>

    @if ($consulta->medicamentos->count())
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
        <p style="margin-top:12px;"><strong>Indicaciones adicionales:</strong></p>
        <p>{{ $consulta->receta_libre }}</p>
    @endif

    <div class="firma">
        <div class="firma-line">Firma del médico</div>
        <p>{{ $consulta->medico->nombre_completo }}</p>
        <p>Cédula: {{ $consulta->medico->cedula_profesional ?? '—' }}</p>
    </div>

</body>

</html>
