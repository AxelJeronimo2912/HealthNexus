<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Pacientes activos</title>
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
        <p>Reporte de pacientes activos — Generado el {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <p><strong>Total de pacientes activos:</strong> {{ $pacientes->count() }}</p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nombre completo</th>
                <th>Edad</th>
                <th>CURP</th>
                <th>Médico asignado</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pacientes as $i => $p)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $p->nombre_completo }}</td>
                    <td>{{ $p->edad ?? '—' }}</td>
                    <td>{{ $p->curp ?? '—' }}</td>
                    <td>{{ $p->medicoAsignado?->medico?->nombre_completo ?? 'Sin asignar' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align:center; color:#999;">No hay pacientes activos.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">HealthNexus — Sistema de Gestión Hospitalaria</div>
</body>

</html>
