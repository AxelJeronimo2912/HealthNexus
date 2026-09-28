<?php

namespace App\Services;

use App\Models\Admision;
use App\Models\Cama;
use App\Models\Cita;
use App\Models\Consulta;
use App\Models\Medicamento;
use App\Models\Paciente;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;

class ReporteService
{
    /**
     * Genera un PDF con los pacientes de un médico.
     */
    public static function pacientesDeMedico(int $medicoId): array
    {
        $medico = User::find($medicoId);
        if (!$medico) {
            return ['error' => 'Médico no encontrado.'];
        }

        $pacientes = Paciente::whereHas('citas', fn($q) => $q->where('medico_id', $medicoId))
            ->orWhereHas('consultas', fn($q) => $q->where('medico_id', $medicoId))
            ->with(['citas' => fn($q) => $q->where('medico_id', $medicoId)])
            ->get();

        $pdf = Pdf::loadView('reportes.pacientes-medico', compact('medico', 'pacientes'));

        $url = self::guardarPdf($pdf, 'pacientes-medico-' . $medicoId . '-' . now()->format('YmdHis'));

        return [
            'url' => $url,
            'nombre' => 'Reporte de pacientes del Dr. ' . $medico->nombre_completo,
            'total_pacientes' => $pacientes->count(),
        ];
    }

    /**
     * Genera un PDF con las citas de una fecha.
     */
    public static function citasDelDia(string $fecha): array
    {
        $citas = Cita::whereDate('fecha_hora', $fecha)
            ->with(['paciente', 'medico'])
            ->orderBy('fecha_hora')
            ->get();

        $pdf = Pdf::loadView('reportes.citas-dia', compact('citas', 'fecha'));

        $url = self::guardarPdf($pdf, 'citas-' . $fecha);

        return [
            'url' => $url,
            'nombre' => 'Reporte de citas del ' . $fecha,
            'total_citas' => $citas->count(),
        ];
    }

    /**
     * Genera un PDF con el inventario bajo.
     */
    public static function inventarioBajo(): array
    {
        $medicamentos = Medicamento::where('activo', true)
            ->with('lotes')
            ->get()
            ->filter(fn($m) => $m->stock_total_calculado <= $m->stock_minimo);

        $pdf = Pdf::loadView('reportes.inventario-bajo', compact('medicamentos'));

        $url = self::guardarPdf($pdf, 'inventario-bajo-' . now()->format('YmdHis'));

        return [
            'url' => $url,
            'nombre' => 'Reporte de inventario bajo',
            'total_medicamentos' => $medicamentos->count(),
        ];
    }

    // ============================================================
    // NUEVOS REPORTES
    // ============================================================

    /**
     * Genera un PDF con los medicamentos con stock alto.
     */
    public static function medicamentosStockAlto(): array
    {
        $medicamentos = Medicamento::where('activo', true)
            ->with('lotes')
            ->get()
            ->sortByDesc(fn($m) => $m->stock_total_calculado)
            ->values();

        $pdf = Pdf::loadView('reportes.medicamentos-stock-alto', compact('medicamentos'));

        $url = self::guardarPdf($pdf, 'medicamentos-stock-alto-' . now()->format('YmdHis'));

        return [
            'url' => $url,
            'nombre' => 'Reporte de medicamentos con stock alto',
            'total_medicamentos' => $medicamentos->count(),
        ];
    }

    /**
     * Genera un PDF con el listado completo de pacientes activos.
     */
    public static function pacientesActivos(): array
    {
        $pacientes = Paciente::where('activo', true)
            ->with('medicoAsignado.medico')
            ->orderBy('apellido_paterno')
            ->get();

        $pdf = Pdf::loadView('reportes.pacientes-activos', compact('pacientes'));

        $url = self::guardarPdf($pdf, 'pacientes-activos-' . now()->format('YmdHis'));

        return [
            'url' => $url,
            'nombre' => 'Reporte de pacientes activos',
            'total_pacientes' => $pacientes->count(),
        ];
    }

    /**
     * Genera un PDF con el personal activo del hospital.
     */
    public static function personalActivo(): array
    {
        $personal = User::where('activo', true)
            ->with(['roles', 'especialidades'])
            ->orderBy('apellido_paterno')
            ->get();

        $pdf = Pdf::loadView('reportes.personal-activo', compact('personal'));

        $url = self::guardarPdf($pdf, 'personal-activo-' . now()->format('YmdHis'));

        return [
            'url' => $url,
            'nombre' => 'Reporte de personal activo',
            'total_personal' => $personal->count(),
        ];
    }

    /**
     * Genera un PDF con las admisiones de una fecha.
     */
    public static function admisionesDelDia(string $fecha): array
    {
        $admisiones = Admision::whereDate('fecha_hora_llegada', $fecha)
            ->with('paciente')
            ->orderBy('fecha_hora_llegada')
            ->get();

        $pdf = Pdf::loadView('reportes.admisiones-dia', compact('admisiones', 'fecha'));

        $url = self::guardarPdf($pdf, 'admisiones-' . $fecha);

        return [
            'url' => $url,
            'nombre' => 'Reporte de admisiones del ' . $fecha,
            'total_admisiones' => $admisiones->count(),
        ];
    }

    /**
     * Genera un PDF con las camas del hospital y su estado.
     */
    public static function camasEstado(): array
    {
        $camas = Cama::where('activo', true)
            ->with('pacienteActual.paciente')
            ->orderBy('area')
            ->orderBy('codigo')
            ->get();

        $pdf = Pdf::loadView('reportes.camas-estado', compact('camas'));

        $url = self::guardarPdf($pdf, 'camas-estado-' . now()->format('YmdHis'));

        return [
            'url' => $url,
            'nombre' => 'Reporte de camas del hospital',
            'total_camas' => $camas->count(),
        ];
    }

    /**
     * Genera un PDF con estadísticas generales del hospital.
     */
    public static function estadisticasGenerales(): array
    {
        $stats = [
            'pacientes_activos' => Paciente::where('activo', true)->count(),
            'citas_mes' => Cita::whereMonth('fecha_hora', now()->month)
                ->whereYear('fecha_hora', now()->year)
                ->count(),
            'camas_total' => Cama::where('activo', true)->count(),
            'camas_ocupadas' => Cama::where('estado', 'ocupada')->count(),
            'medicamentos_total' => Medicamento::where('activo', true)->count(),
            'personal_activo' => User::where('activo', true)->count(),
            'admisiones_mes' => Admision::whereMonth('fecha_hora_llegada', now()->month)->count(),
            'fecha_generacion' => now()->format('d/m/Y H:i'),
        ];

        $pdf = Pdf::loadView('reportes.estadisticas-generales', compact('stats'));

        $url = self::guardarPdf($pdf, 'estadisticas-generales-' . now()->format('YmdHis'));

        return [
            'url' => $url,
            'nombre' => 'Reporte de estadísticas generales',
            'stats' => $stats,
        ];
    }

    /**
     * Genera un PDF con las consultas de un paciente.
     */
    public static function consultasDePaciente(int $pacienteId): array
    {
        $paciente = Paciente::find($pacienteId);
        if (!$paciente) {
            return ['error' => 'Paciente no encontrado.'];
        }

        $consultas = Consulta::where('paciente_id', $pacienteId)
            ->with(['medico', 'diagnosticoPrincipal'])
            ->orderByDesc('created_at')
            ->get();

        $pdf = Pdf::loadView('reportes.consultas-paciente', compact('paciente', 'consultas'));

        $url = self::guardarPdf($pdf, 'consultas-paciente-' . $pacienteId . '-' . now()->format('YmdHis'));

        return [
            'url' => $url,
            'nombre' => 'Historial de consultas de ' . $paciente->nombre_completo,
            'total_consultas' => $consultas->count(),
        ];
    }

    // ============================================================
    // AUXILIAR
    // ============================================================

    /**
     * Guarda el PDF en storage/app/public/reportes y devuelve su URL pública.
     */
    private static function guardarPdf($pdf, string $nombreBase): string
    {
        $nombreArchivo = 'reportes/' . $nombreBase . '.pdf';

        $carpeta = storage_path('app/public/reportes');
        if (!file_exists($carpeta)) {
            mkdir($carpeta, 0755, true);
        }

        \Storage::disk('public')->put($nombreArchivo, $pdf->output());

        return asset('storage/' . $nombreArchivo);
    }
}