<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Citas - {{ $fechaTexto }}</title>
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
        .programada { background-color: #e0f2fe; color: #0369a1; }
        .confirmada { background-color: #e0e7ff; color: #3730a3; }
        .atendida { background-color: #d1fae5; color: #065f46; }
        .cancelada { background-color: #ffe4e6; color: #9f1239; }
    </style>
</head>
<body>

    <div class="header">
        <h1>HealthNexus - Hospital Central</h1>
        <p>Reporte Operativo de Agenda de Citas Médicas</p>
        <p>Fecha de la agenda: <strong>{{ ucfirst($fechaTexto) }}</strong> | Emitido: {{ $fechaEmision }}</p>
    </div>

    <table class="stats">
        <tr>
            <td><strong>Total:</strong> {{ $stats['total'] }}</td>
            <td><strong>Programadas:</strong> {{ $stats['programadas'] }}</td>
            <td><strong>Confirmadas:</strong> {{ $stats['confirmadas'] }}</td>
            <td><strong>Atendidas:</strong> {{ $stats['atendidas'] }}</td>
            <td><strong>Canceladas:</strong> {{ $stats['canceladas'] }}</td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 10%;">Hora</th>
                <th style="width: 35%;">Paciente</th>
                <th style="width: 15%;">Triage</th>
                <th style="width: 25%;">Médico</th>
                <th style="width: 15%;" class="text-center">Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($citas as $cita)
                <tr>
                    <td><strong>{{ $cita->fecha_hora->format('H:i') }}</strong></td>
                    <td>{{ $cita->paciente->nombre_completo ?? 'Paciente eliminado' }}</td>
                    <td>{{ $cita->triage_al_momento ? ucfirst($cita->triage_al_momento) : '—' }}</td>
                    <td>{{ $cita->medico->nombre_completo ?? '—' }}</td>
                    <td class="text-center">
                        <span class="badge {{ $cita->estado }}">
                            {{ $cita->estado_label ?? ucfirst($cita->estado) }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No hay citas registradas para este día.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>