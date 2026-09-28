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
    <h1>HealthNexus — Medicamentos con stock bajo</h1>
    <p>Fecha de emisión: {{ now()->format('d/m/Y H:i') }}</p>
    <p>Total: {{ $medicamentos->count() }} medicamentos</p>

    <table>
        <thead>
            <tr>
                <th>Medicamento</th>
                <th>Stock actual</th>
                <th>Stock mínimo</th>
                <th>Faltante</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($medicamentos as $m)
                <tr>
                    <td>{{ $m->nombre }} {{ $m->concentracion }}</td>
                    <td>{{ $m->stock_total_calculado }}</td>
                    <td>{{ $m->stock_minimo }}</td>
                    <td>{{ max(0, $m->stock_minimo - $m->stock_total_calculado) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
