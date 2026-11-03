<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Inventario Bajo</title>
    <style>
        body { font-family: sans-serif; color: #334155; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #cbd5e1; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18px; color: #1e293b; }
        .header p { margin: 2px 0; color: #64748b; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #e2e8f0; padding: 8px; text-align: left; }
        th { background-color: #f1f5f9; color: #334155; font-weight: bold; font-size: 11px; }
        .text-center { text-align: center; }
        .badge { background-color: #fee2e2; color: #991b1b; padding: 2px 6px; border-radius: 4px; font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">
        <h1>HealthNexus - Hospital Central</h1>
        <p>Reporte Operativo: Medicamentos con Stock Bajo</p>
        <p>Fecha de emisión: {{ $fecha }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Código / Barras</th>
                <th>Medicamento</th>
                <th>Presentación</th>
                <th class="text-center">Stock Actual</th>
                <th class="text-center">Stock Mínimo</th>
            </tr>
        </thead>
        <tbody>
            @forelse($medicamentos as $med)
                <tr>
                    <td>{{ $med->codigo_barras ?? 'N/A' }}</td>
                    <td>{{ $med->nombre }}</td>
                    <td>{{ $med->presentacion }}</td>
                    <td class="text-center"><span class="badge">{{ $med->stock_actual }}</span></td>
                    <td class="text-center">{{ $med->stock_minimo }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No hay medicamentos con stock bajo en este momento.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>