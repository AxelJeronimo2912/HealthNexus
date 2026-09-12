<?php

namespace App\Services;

class TriageService
{
    /**
     * Calcula el nivel de triage basado en los signos vitales.
     * Retorna: rojo, naranja, amarillo, verde o azul.
     */
    public static function calcular(array $datos): string
    {
        $puntos = 0;

        $temp = $datos['temperatura'] ?? null;
        $fc = $datos['frecuencia_cardiaca'] ?? null;
        $fr = $datos['frecuencia_respiratoria'] ?? null;
        $sat = $datos['saturacion_oxigeno'] ?? null;
        $ta = $datos['presion_arterial'] ?? null;
        $glucosa = $datos['glucosa'] ?? null;
        $dolor = $datos['escala_dolor'] ?? null;

        // ---- Temperatura ----
        if ($temp !== null) {
            if ($temp >= 40 || $temp < 35) $puntos += 4;       // crítico
            elseif ($temp >= 39 || $temp < 36) $puntos += 2;   // urgente
            elseif ($temp >= 38) $puntos += 1;                 // fiebre
        }

        // ---- Frecuencia cardíaca ----
        if ($fc !== null) {
            if ($fc > 130 || $fc < 40) $puntos += 4;
            elseif ($fc > 110 || $fc < 50) $puntos += 2;
            elseif ($fc > 100) $puntos += 1;
        }

        // ---- Frecuencia respiratoria ----
        if ($fr !== null) {
            if ($fr > 30 || $fr < 8) $puntos += 4;
            elseif ($fr > 24 || $fr < 10) $puntos += 2;
            elseif ($fr > 20) $puntos += 1;
        }

        // ---- Saturación O2 ----
        if ($sat !== null) {
            if ($sat < 85) $puntos += 4;
            elseif ($sat < 90) $puntos += 3;
            elseif ($sat < 94) $puntos += 1;
        }

        // ---- Presión arterial ----
        if ($ta && str_contains($ta, '/')) {
            [$sis, $dia] = array_map('intval', explode('/', $ta));
            if ($sis >= 180 || $sis < 80 || $dia >= 120 || $dia < 50) $puntos += 4;
            elseif ($sis >= 160 || $sis < 90 || $dia >= 100 || $dia < 60) $puntos += 2;
            elseif ($sis >= 140 || $dia >= 90) $puntos += 1;
        }

        // ---- Glucosa ----
        if ($glucosa !== null) {
            if ($glucosa > 400 || $glucosa < 50) $puntos += 4;
            elseif ($glucosa > 250 || $glucosa < 70) $puntos += 2;
            elseif ($glucosa > 180) $puntos += 1;
        }

        // ---- Escala de dolor ----
        if ($dolor !== null) {
            if ($dolor >= 8) $puntos += 3;
            elseif ($dolor >= 6) $puntos += 2;
            elseif ($dolor >= 4) $puntos += 1;
        }

        // ---- Clasificación final ----
        return match (true) {
            $puntos >= 12 => 'rojo',
            $puntos >= 8  => 'naranja',
            $puntos >= 5  => 'amarillo',
            $puntos >= 2  => 'verde',
            default       => 'azul',
        };
    }
}