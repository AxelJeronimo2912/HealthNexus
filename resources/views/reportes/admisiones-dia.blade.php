<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Admisiones del día</title>
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
        <p>Reporte de admisiones del {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }} — Generado el
            {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <p><strong>Total de admisiones:</strong> {{ $admisiones->count() }}</p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Hora</th>
                <th>Paciente</th>
                <th>Estado</th>
                <th>Motivo</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($admisiones as $i => $a)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $a->fecha_hora_llegada?->format('H:i') ?? '—' }}</td>
                    <td>{{ $a->paciente?->nombre_completo ?? '—' }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $a->estado ?? '—')) }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($a->motivo ?? '—', 60) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align:center; color:#999;">No hay admisiones registradas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">HealthNexus — Sistema de Gestión Hospitalaria</div>
</body>

</html>
