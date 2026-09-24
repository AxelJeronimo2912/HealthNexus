<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Pase de Salida</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #111;
        }

        h1 {
            font-size: 16px;
            margin: 0 0 4px;
        }

        h2 {
            font-size: 13px;
            margin: 16px 0 6px;
            border-bottom: 1px solid #999;
            padding-bottom: 2px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }

        td,
        th {
            border: 1px solid #ccc;
            padding: 4px 6px;
            text-align: left;
        }

        .label {
            color: #555;
        }

        .grid2 {
            width: 100%;
        }

        .grid2 td {
            border: none;
            padding: 2px 0;
        }

        .firma {
            margin-top: 60px;
            text-align: center;
        }

        .firma-line {
            border-top: 1px solid #333;
            width: 250px;
            margin: 0 auto;
            padding-top: 4px;
        }
    </style>
</head>

<body>

    <h1>HealthNexus — Pase de Salida por Derivación</h1>
    <p class="label">Folio: {{ $admision->folio }}</p>
    <p class="label">Fecha de emisión: {{ now()->format('d/m/Y H:i') }}</p>

    <h2>Datos del paciente</h2>
    <table class="grid2">
        <tr>
            <td><span class="label">Nombre:</span> {{ $admision->paciente?->nombre_completo ?? '—' }}</td>
            <td><span class="label">Edad:</span> {{ $admision->paciente?->edad ?? '—' }} años</td>
        </tr>
        <tr>
            <td><span class="label">Sexo:</span> {{ ucfirst($admision->paciente?->sexo ?? '—') }}</td>
            <td><span class="label">CURP:</span> {{ $admision->paciente?->curp ?? '—' }}</td>
        </tr>
        <tr>
            <td><span class="label">Tipo sanguíneo:</span> {{ $admision->paciente?->tipo_sanguineo ?? '—' }}</td>
            <td><span class="label">Teléfono:</span> {{ $admision->paciente?->telefono_principal ?? '—' }}</td>
        </tr>
        <tr>
            <td colspan="2"><span class="label">Alergias:</span> {{ $admision->paciente?->alergias ?? 'Ninguna' }}
            </td>
        </tr>
        <tr>
            <td colspan="2"><span class="label">Enfermedades crónicas:</span>
                {{ $admision->paciente?->enfermedades_cronicas ?? 'Ninguna' }}</td>
        </tr>
    </table>

    <h2>Motivo de la admisión</h2>
    <p>{{ $admision->motivo ?? '—' }}</p>

    <h2>Diagnóstico presuntivo</h2>
    <p>{{ $admision->diagnostico_presuntivo ?? '—' }}</p>

    <h2>Motivo de la derivación</h2>
    <p>{{ $admision->motivo_derivacion ?? '—' }}</p>

    <h2>Datos de la derivación</h2>
    <table>
        <tr>
            <td><span class="label">Fecha:</span></td>
            <td>{{ $admision->fecha_derivacion?->format('d/m/Y H:i') ?? '—' }}</td>
        </tr>
        <tr>
            <td><span class="label">Admitido por:</span></td>
            <td>{{ $admision->user?->nombre_completo ?? '—' }}</td>
        </tr>
        <tr>
            <td><span class="label">Tipo de admisión:</span></td>
            <td>{{ $admision->tipo_label }}</td>
        </tr>
        <tr>
            <td><span class="label">Triage:</span></td>
            <td>{{ $admision->triage ? ucfirst($admision->triage) : 'Sin clasificar' }}</td>
        </tr>
    </table>

    <h2>Hospital destino</h2>
    <table>
        <tr>
            <td><span class="label">Nombre:</span></td>
            <td>{{ $admision->hospitalDerivado?->nombre ?? '—' }}</td>
        </tr>
        <tr>
            <td><span class="label">Tipo:</span></td>
            <td>{{ strtoupper($admision->hospitalDerivado?->tipo ?? '—') }}</td>
        </tr>
        <tr>
            <td><span class="label">Nivel:</span></td>
            <td>{{ ucfirst($admision->hospitalDerivado?->nivel ?? '—') }}</td>
        </tr>
        <tr>
            <td><span class="label">Dirección:</span></td>
            <td>{{ $admision->hospitalDerivado?->direccion ?? '—' }}</td>
        </tr>
        <tr>
            <td><span class="label">Teléfono:</span></td>
            <td>{{ $admision->hospitalDerivado?->telefono ?? '—' }}</td>
        </tr>
        <tr>
            <td><span class="label">Servicios:</span></td>
            <td>{{ $admision->hospitalDerivado?->servicios ?? '—' }}</td>
        </tr>
    </table>

    <div class="firma">
        <div class="firma-line">Firma del médico responsable</div>
        <p>{{ $admision->medico?->nombre_completo ?? '____________________' }}</p>
        <p>Cédula: {{ $admision->medico?->cedula_profesional ?? '—' }}</p>
    </div>

</body>

</html>
