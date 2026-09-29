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

        .label {
            color: #555;
        }
    </style>
</head>

<body>
    <h1>HealthNexus — Reporte de pacientes</h1>
    <p><span class="label">Médico:</span> {{ $medico->nombre_completo }}</p>
    <p><span class="label">Fecha de emisión:</span> {{ now()->format('d/m/Y H:i') }}</p>
    <p><span class="label">Total de pacientes:</span> {{ $pacientes->count() }}</p>

    <h2>Listado de pacientes</h2>
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>CURP</th>
                <th>Edad</th>
                <th>Sexo</th>
                <th>Alergias</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pacientes as $p)
                <tr>
                    <td>{{ $p->nombre_completo }}</td>
                    <td>{{ $p->curp ?? '—' }}</td>
                    <td>{{ $p->edad }}</td>
                    <td>{{ ucfirst($p->sexo) }}</td>
                    <td>{{ $p->alergias ?? 'Ninguna' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
