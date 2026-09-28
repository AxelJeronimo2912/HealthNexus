<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #111;
        }

        h1 {
            font-size: 16px;
            margin-bottom: 4px;
        }

        h2 {
            font-size: 13px;
            margin-top: 16px;
            border-bottom: 1px solid #999;
            padding-bottom: 2px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 4px 6px;
            text-align: left;
        }
    </style>
</head>

<body>
    <h1>HealthNexus — Reporte de citas</h1>
    <p>Fecha: {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</p>
    <p>Total de citas: {{ $citas->count() }}</p>

    <table>
        <thead>
            <tr>
                <th>Hora</th>
                <th>Paciente</th>
                <th>Médico</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($citas as $c)
                <tr>
                    <td>{{ $c->fecha_hora->format('H:i') }}</td>
                    <td>{{ $c->paciente?->nombre_completo }}</td>
                    <td>{{ $c->medico?->nombre_completo }}</td>
                    <td>{{ $c->estado_label }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
