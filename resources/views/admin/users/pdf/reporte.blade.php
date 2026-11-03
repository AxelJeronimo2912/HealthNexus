<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Colaboradores</title>
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
        .activo { background-color: #d1fae5; color: #065f46; }
        .inactivo { background-color: #ffe4e6; color: #9f1239; }
    </style>
</head>
<body>

    <div class="header">
        <h1>HealthNexus - Hospital Central</h1>
        <p>Reporte Operativo de Colaboradores y Personal</p>
        <p>Fecha de emisión: {{ $fechaEmision }}</p>
    </div>

    <table class="stats">
        <tr>
            <td><strong>Total Colaboradores:</strong> {{ $stats['total'] }}</td>
            <td><strong>Activos:</strong> {{ $stats['activos'] }}</td>
            <td><strong>Inactivos:</strong> {{ $stats['inactivos'] }}</td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 25%;">Nombre Completo</th>
                <th style="width: 18%;">CURP</th>
                <th style="width: 27%;">Correo Electrónico</th>
                <th style="width: 18%;">Rol</th>
                <th style="width: 12%;" class="text-center">Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td><strong>{{ $user->nombre_completo ?: $user->name }}</strong></td>
                    <td>{{ $user->curp ?? 'N/A' }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->getRoleNames()->first() ?? 'Sin rol' }}</td>
                    <td class="text-center">
                        <span class="badge {{ $user->activo ? 'activo' : 'inactivo' }}">
                            {{ $user->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No hay colaboradores registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>