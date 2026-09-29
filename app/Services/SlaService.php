<?php

namespace App\Services;

use App\Models\Admision;
use App\Models\CamaPaciente;
use App\Models\Cita;
use App\Models\Consulta;
use App\Models\SlaEvento;
use Carbon\Carbon;

class SlaService
{
    /**
     * Calcula media, desviación estándar y límite para un módulo.
     */
    public static function estadisticasPorModulo(string $modulo): array
    {
        $duraciones = SlaEvento::where('modulo', $modulo)
            ->pluck('duration_minutes')
            ->map(fn($d) => (float) $d)
            ->toArray();

        $n = count($duraciones);

        if ($n === 0) {
            return [
                'n' => 0,
                'media' => 0,
                'desviacion' => 0,
                'limite' => 0,
            ];
        }

        $media = array_sum($duraciones) / $n;

        // Desviación estándar (poblacional)
        $sumaCuadrados = 0;
        foreach ($duraciones as $d) {
            $sumaCuadrados += ($d - $media) ** 2;
        }
        $varianza = $sumaCuadrados / $n;
        $desviacion = sqrt($varianza);

        return [
            'n' => $n,
            'media' => round($media, 2),
            'desviacion' => round($desviacion, 2),
            'limite' => round($media + (2.5 * $desviacion), 2),
        ];
    }

    /**
     * Calcula el z-score de un evento y marca si es outlier.
     */
    public static function analizarEvento(float $duracion, string $modulo): array
    {
        $stats = self::estadisticasPorModulo($modulo);

        if ($stats['desviacion'] == 0) {
            return [
                'is_outlier' => false,
                'z_score' => 0,
            ];
        }

        $z = ($duracion - $stats['media']) / $stats['desviacion'];

        return [
            'is_outlier' => $z > 2.5,
            'z_score' => round($z, 4),
        ];
    }

    /**
     * Recalcula el análisis de TODOS los eventos de un módulo.
     * Se usa después de generar nuevos eventos.
     */
    public static function recalcularModulo(string $modulo): int
    {
        $stats = self::estadisticasPorModulo($modulo);

        if ($stats['desviacion'] == 0) {
            return 0;
        }

        $eventos = SlaEvento::where('modulo', $modulo)->get();
        $outliers = 0;

        foreach ($eventos as $evento) {
            $z = ($evento->duration_minutes - $stats['media']) / $stats['desviacion'];
            $isOutlier = $z > 2.5;

            $evento->update([
                'is_outlier' => $isOutlier,
                'outlier_z_score' => round($z, 4),
            ]);

            if ($isOutlier) $outliers++;
        }

        return $outliers;
    }

    /**
     * Genera eventos SLA desde los datos reales del sistema.
     * Se llama desde un comando Artisan programado.
     */
    public static function generarDesdeDatos(int $dias = 30): int
    {
        $desde = now()->subDays($dias);
        $creados = 0;

        // === QUIROFANO: consultas con especialidad quirúrgica ===
        $consultasQuirofano = Consulta::where('created_at', '>=', $desde)
            ->where('estado', 'finalizada')
            ->whereHas('especialidad', fn($q) => $q->where('grupo', 'quirurgica'))
            ->with('especialidad')
            ->get();

        foreach ($consultasQuirofano as $consulta) {
            if (!$consulta->finalizada_en) continue;

            $duracion = $consulta->created_at->diffInMinutes($consulta->finalizada_en);
            if ($duracion <= 0) continue;

            self::crearEventoSiNoExiste(
                'quirofano',
                'consulta_quirurgica',
                'consulta',
                $consulta->id,
                $duracion,
                $consulta->created_at
            );
            $creados++;
        }

        // === URGENCIAS: admisiones de tipo urgencias ===
        $admisionesUrgencias = Admision::where('fecha_hora_llegada', '>=', $desde)
            ->where('tipo', 'urgencias')
            ->whereNotNull('updated_at')
            ->get();

        foreach ($admisionesUrgencias as $admision) {
            $duracion = $admision->fecha_hora_llegada->diffInMinutes($admision->updated_at);
            if ($duracion <= 0) continue;

            self::crearEventoSiNoExiste(
                'urgencias',
                'atencion_urgencias',
                'admision',
                $admision->id,
                $duracion,
                $admision->fecha_hora_llegada
            );
            $creados++;
        }

        // === FARMACIA: tiempo entre receta y dispensación ===
        $consultasDispensadas = Consulta::where('dispensada_en', '>=', $desde)
            ->where('dispensada', true)
            ->get();

        foreach ($consultasDispensadas as $consulta) {
            if (!$consulta->dispensada_en || !$consulta->created_at) continue;

            $duracion = $consulta->created_at->diffInMinutes($consulta->dispensada_en);
            if ($duracion <= 0) continue;

            self::crearEventoSiNoExiste(
                'farmacia',
                'dispensacion_receta',
                'consulta',
                $consulta->id,
                $duracion,
                $consulta->created_at
            );
            $creados++;
        }

        // === HOSPITALIZACIÓN: días de estancia ===
        $asignacionesCerradas = CamaPaciente::where('activa', false)
            ->whereNotNull('fecha_egreso')
            ->where('fecha_egreso', '>=', $desde)
            ->get();

        foreach ($asignacionesCerradas as $asig) {
            if (!$asig->fecha_ingreso || !$asig->fecha_egreso) continue;

            $duracionMinutos = $asig->fecha_ingreso->diffInMinutes($asig->fecha_egreso);
            if ($duracionMinutos <= 0) continue;

            self::crearEventoSiNoExiste(
                'hospitalizacion',
                'estancia_paciente',
                'cama_paciente',
                $asig->id,
                $duracionMinutos,
                $asig->fecha_ingreso
            );
            $creados++;
        }

        // Recalcular análisis de outliers en cada módulo
        foreach (['quirofano', 'urgencias', 'farmacia', 'hospitalizacion'] as $modulo) {
            self::recalcularModulo($modulo);
        }

        return $creados;
    }

    private static function crearEventoSiNoExiste(
        string $modulo,
        string $eventType,
        string $refTipo,
        int $refId,
        int $duracion,
        Carbon $fecha
    ): void {
        $existe = SlaEvento::where('modulo', $modulo)
            ->where('referencia_tipo', $refTipo)
            ->where('referencia_id', $refId)
            ->exists();

        if ($existe) return;

        SlaEvento::create([
            'modulo' => $modulo,
            'event_type' => $eventType,
            'referencia_tipo' => $refTipo,
            'referencia_id' => $refId,
            'duration_minutes' => $duracion,
            'start_hour' => $fecha->hour,
            'evento_en' => $fecha,
        ]);
    }

    /**
     * Resumen completo para el modal del dashboard.
     */
    public static function resumen(): array
    {
        $modulos = ['quirofano', 'urgencias', 'farmacia', 'hospitalizacion'];
        $resumen = [];

        foreach ($modulos as $modulo) {
            $stats = self::estadisticasPorModulo($modulo);
            $total = SlaEvento::where('modulo', $modulo)->count();
            $outliers = SlaEvento::where('modulo', $modulo)->where('is_outlier', true)->count();

            $resumen[$modulo] = [
                'label' => match ($modulo) {
                    'quirofano' => 'Quirófano',
                    'urgencias' => 'Urgencias',
                    'farmacia' => 'Farmacia',
                    'hospitalizacion' => 'Hospitalización',
                },
                'n' => $total,
                'media' => $stats['media'],
                'desviacion' => $stats['desviacion'],
                'limite' => $stats['limite'],
                'outliers' => $outliers,
            ];
        }

        return $resumen;
    }
}