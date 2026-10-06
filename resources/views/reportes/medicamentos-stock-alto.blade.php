<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Medicamentos con stock alto</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }

        .header {
            border-bottom: 2px solid #0d9488;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header h1 {
            color: #0d9488;
            margin: 0;
            font-size: 20px;
        }

        .header p {
            margin: 4px 0;
            color: #666;
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th {
            background: #0d9488;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 11px;
        }

        td {
            padding: 6px 8px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 11px;
        }

        tr:nth-child(even) {
            background: #f8fafc;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            background: #ccfbf1;
            color: #0f766e;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #999;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>HealthNexus</h1>
        <p>Reporte de medicamentos con stock alto — Generado el {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <p><strong>Total de medicamentos listados:</strong> {{ $medicamentos->count() }}</p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Medicamento</th>
                <th>Concentración</th>
                <th>Stock actual</th>
                <th>Stock mínimo</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($medicamentos as $i => $m)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $m->nombre }}</td>
                    <td>{{ $m->concentracion }}</td>
                    <td>{{ $m->stock_total_calculado }}</td>
                    <td>{{ $m->stock_minimo }}</td>
                    <td><span class="badge">Stock alto</span></td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align:center; color:#999;">No hay medicamentos registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">HealthNexus — Sistema de Gestión Hospitalaria</div>
</body>

</html>
