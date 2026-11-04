<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Estado de Camas</title>
    <style>
        body { font-family: sans-serif; color: #334155; font-size: 11px; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #cbd5e1; padding-bottom: 8px; }
        .header h1 { margin: 0; font-size: 16px; color: #1e293b; }
        .header p { margin: 2px 0; color: #64748b; font-size: 9px; }
        .stats { width: 100%; margin-bottom: 15px; border-collapse: collapse; }
        .stats td { text-align: center; padding: 6px; border: 1px solid #e2e8f0; background: #f8fafc; font-size: 10px; }
        table.data { width: 100%; border-collapse: collapse; margin-top: 5px; }
        th, td { border: 1px solid #e2e8f0; padding: 6px; text-align: left; }
        th { background-color: #f1f5f9; color: #334155; font-weight: bold; font-size: 10px; }
        .text-center { text-align: center; }
        .badge { padding: 2px 5px; border-radius: 3px; font-weight: bold; font-size: 9px; text-transform: uppercase; }
        .disponible { background-color: #d1fae5; color: #065f46; }
        .ocupada { background-color: #fef3c7; color: #92400e; }
        .limpieza { background-color: #e0f2fe; color: #0369a1; }
        .mantenimiento { background-color: #ffe4e6; color: #9f1239; }
    </style>
</head>
<body>

    <div class="header">
        <h1>HealthNexus - Hospital Central</h1>
        <p>Reporte Operativo de Disponibilidad y Estado de Camas</p>
        <p>Fecha de emisión: {{ $fecha }}</p>
    </div>

    <table class="stats">
        <tr>
            <td><strong>Total:</strong> {{ $stats['total'] }}</td>
            <td><strong>Disponibles:</strong> {{ $stats['disponibles'] }}</td>
            <td><strong>Ocupadas:</strong> {{ $stats['ocupadas'] }}</td>
            <td><strong>Limpieza:</strong> {{ $stats['limpieza'] }}</td>
            <td><strong>Mantenimiento:</strong> {{ $stats['mantenimiento'] }}</td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th>Código</th>
                <th>Área / Piso</th>
                <th>Habitación</th>
                <th class="text-center">Estado</th>
                <th>Paciente Actual</th>
            </tr>
        </thead>
        <tbody>
            @forelse($camas as $cama)
                <tr>
                    <td><strong>{{ $cama->codigo }}</strong></td>
                    <td>{{ $cama->area ?? 'N/A' }} / {{ $cama->piso ?? 'N/A' }}</td>
                    <td>{{ $cama->habitacion ?? 'N/A' }}</td>
                    <td class="text-center">
                        <span class="badge {{ $cama->estado }}">
                            {{ $cama->estado_label ?? ucfirst($cama->estado) }}
                        </span>
                    </td>
                    <td>
                        {{ $cama->pacienteActual?->paciente?->nombre_completo ?? 'Ninguno' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No hay camas registradas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>