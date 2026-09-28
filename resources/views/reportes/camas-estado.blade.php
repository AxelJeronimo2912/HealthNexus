<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Estado de camas</title>
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

        .estado-disponible {
            color: #0f766e;
            font-weight: bold;
        }

        .estado-ocupada {
            color: #b91c1c;
            font-weight: bold;
        }

        .estado-mantenimiento {
            color: #b45309;
            font-weight: bold;
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
        <p>Reporte de estado de camas — Generado el {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    @php
        $total = $camas->count();
        $disponibles = $camas->where('estado', 'disponible')->count();
        $ocupadas = $camas->where('estado', 'ocupada')->count();
        $mantenimiento = $total - $disponibles - $ocupadas;
    @endphp

    <p>
        <strong>Total:</strong> {{ $total }} &nbsp;|&nbsp;
        <strong>Disponibles:</strong> {{ $disponibles }} &nbsp;|&nbsp;
        <strong>Ocupadas:</strong> {{ $ocupadas }} &nbsp;|&nbsp;
        <strong>Mantenimiento:</strong> {{ $mantenimiento }}
    </p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Código</th>
                <th>Área</th>
                <th>Estado</th>
                <th>Paciente</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($camas as $i => $c)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $c->codigo }}</td>
                    <td>{{ $c->area }}</td>
                    <td class="estado-{{ $c->estado }}">{{ ucfirst($c->estado) }}</td>
                    <td>{{ $c->pacienteActual?->paciente?->nombre_completo ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align:center; color:#999;">No hay camas registradas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">HealthNexus — Sistema de Gestión Hospitalaria</div>
</body>

</html>
