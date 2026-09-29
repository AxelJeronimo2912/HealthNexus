<?php

namespace App\Services;

use App\Models\Cita;

class CobroService
{
    /**
     * Cobra el servicio de una cita en la cuenta abierta del paciente.
     * Idempotente: si ya se cobró antes, no duplica.
     */
    public function cobrarCita(Cita $cita): void
    {
        $servicio = $cita->servicio;

        if (! $servicio || (float) $servicio->precio <= 0) {
            return;
        }

        $cuenta = $cita->paciente->obtenerCuentaAbierta();

        // Evitar duplicados
        $yaCobrado = $cuenta->items()->where('cita_id', $cita->id)->exists();
        if ($yaCobrado) {
            return;
        }

        $cuenta->items()->create([
            'servicio_id'     => $servicio->id,
            'cita_id'         => $cita->id,
            'user_id'         => auth()->id(),
            'concepto'        => $servicio->nombre,
            'cantidad'        => 1,
            'precio_unitario' => $servicio->precio,
            'notas'           => 'Generado automáticamente',
        ]);
    }
}