
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de colaboradores</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #333;
        }

        .header {
            border-bottom: 3px solid #0d9488;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }

        h1 {
            color: #0d9488;
            font-size: 19px;
            margin: 0 0 8px;
        }

        .fecha {
            color: #666;
            font-size: 10px;
        }

        .estadisticas {
            width: 100%;
            margin-bottom: 20px;
        }

        .estadisticas td {
            padding: 10px;
            background: #f1f5f9;
            text-align: center;
            border: 1px solid #ddd;
        }

        .numero {
            font-size: 18px;
            font-weight: bold;
            color: #0d9488;
        }

        table.personal {
            width: 100%;
            border-collapse: collapse;
        }

        .personal th {
            background: #0d9488;
            color: white;
            padding: 9px 6px;
            text-align: left;
        }

        .personal td {
            padding: 8px 6px;
            border-bottom: 1px solid #ddd;
        }

        .badge {
            padding: 4px 7px;
            border-radius: 4px;
            font-weight: bold;
        }

        .activo {
            color: #15803d;
            background: #dcfce7;
        }

        .inactivo {
            color: #b91c1c;
            background: #fee2e2;
        }

        .vacio {
            text-align: center;
            padding: 20px;
            color: #777;
        }

        .footer {
            margin-top: 25px;
            text-align: center;
            color: #999;
            font-size: 9px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Reporte Operativo de Colaboradores y Personal</h1>
        <p class="fecha">HealthNexus - Sistema de Gestión Hospitalaria</p>
        <p class="fecha">Fecha de emisión: {{ $fechaEmision }}</p>
    </div>

    <table class="estadisticas">
        <tr>
            <td>
                <div class="numero">{{ $stats['total'] }}</div>
                Total Colaboradores:
            </td>
            <td>
                <div class="numero">{{ $stats['activos'] }}</div>
                Activos
            </td>
            <td>
                <div class="numero">{{ $stats['inactivos'] }}</div>
                Inactivos
            </td>
        </tr>
    </table>

    <table class="personal">
        <thead>
            <tr>
                <th>#</th>
                <th>Nombre completo</th>
                <th>Correo electrónico</th>
                <th>Rol</th>
                <th>Estado</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($users as $i => $user)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $user->nombre_completo ?: $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->getRoleNames()->first() ?? 'Sin rol' }}</td>
                    <td>
                        @if ($user->activo)
                            <span class="badge activo">badge activo</span>
                        @else
                            <span class="badge inactivo">badge inactivo</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="vacio">
                        No hay colaboradores registrados.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        HealthNexus - Sistema de Gestión Hospitalaria
    </div>
</body>
</html>
