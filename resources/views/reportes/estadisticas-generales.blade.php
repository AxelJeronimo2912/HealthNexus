<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Estadísticas generales</title>
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

        .grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 20px;
        }

        .card {
            width: 48%;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px;
            background: #f8fafc;
        }

        .card .label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .card .value {
            font-size: 22px;
            color: #0d9488;
            font-weight: bold;
            margin-top: 4px;
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
        <p>Estadísticas generales — Generado el {{ $stats['fecha_generacion'] }}</p>
    </div>

    <div class="grid">
        <div class="card">
            <div class="label">Pacientes activos</div>
            <div class="value">{{ $stats['pacientes_activos'] }}</div>
        </div>
        <div class="card">
            <div class="label">Citas este mes</div>
            <div class="value">{{ $stats['citas_mes'] }}</div>
        </div>
        <div class="card">
            <div class="label">Camas ocupadas</div>
            <div class="value">{{ $stats['camas_ocupadas'] }} / {{ $stats['camas_total'] }}</div>
        </div>
        <div class="card">
            <div class="label">Medicamentos</div>
            <div class="value">{{ $stats['medicamentos_total'] }}</div>
        </div>
        <div class="card">
            <div class="label">Personal activo</div>
            <div class="value">{{ $stats['personal_activo'] }}</div>
        </div>
        <div class="card">
            <div class="label">Admisiones este mes</div>
            <div class="value">{{ $stats['admisiones_mes'] }}</div>
        </div>
    </div>

    <div class="footer">HealthNexus — Sistema de Gestión Hospitalaria</div>
</body>

</html>
