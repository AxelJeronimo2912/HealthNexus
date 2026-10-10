<?php

namespace App\Services;

use App\Models\Admision;
use App\Models\Alerta;
use App\Models\Cama;
use App\Models\Cita;
use App\Models\Lote;
use App\Models\Medicamento;
use App\Models\Paciente;
use App\Models\SignoVital;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AlertaService
{
    /**
     * Ejecuta todos los chequeos y genera las alertas necesarias.
     * Se llama desde un comando Artisan programado.
     */
    public static function generarTodas(): array
    {
        $generadas = [
            'stock_bajo' => self::chequearStockBajo(),
            'caducidad_proxima' => self::chequearCaducidadProxima(),
            'triage_sin_cama' => self::chequearTriageSinCama(),
            'citas_sin_confirmar' => self::chequearCitasSinConfirmar(),
            'signos_criticos' => self::chequearSignosCriticos(),
            'medico_sin_turno' => self::chequearMedicosSinTurno(),
        ];

        return $generadas;
    }

    /**
     * Evita duplicados: solo crea la alerta si no existe una igual activa
     * para la misma referencia en las últimas 24h.
     */
    private static function crearSiNoExiste(array $datos): ?Alerta
    {
        $existe = Alerta::where('categoria', $datos['categoria'])
            ->where('referencia_tipo', $datos['referencia_tipo'] ?? null)
            ->where('referencia_id', $datos['referencia_id'] ?? null)
            ->whereIn('estado', ['activa', 'vista'])
            ->where('created_at', '>=', now()->subHours(24))
            ->exists();

        if ($existe) return null;

        return Alerta::create($datos);
    }

    // ============ INVENTARIO ============

    private static function chequearStockBajo(): int
    {
        $count = 0;

        $medicamentos = Medicamento::where('activo', true)
            ->with('lotes')
            ->get();

        foreach ($medicamentos as $med) {
            $stock = $med->stock_total_calculado;

            if ($stock > $med->stock_minimo) continue;

            $nivel = $stock <= 0 ? 'critico' : 'advertencia';

            $alerta = self::crearSiNoExiste([
                'tipo' => 'inventario',
                'categoria' => 'stock_bajo',
                'nivel' => $nivel,
                'titulo' => $stock <= 0 ? 'Medicamento agotado' : 'Stock bajo',
                'mensaje' => "{$med->nombre} {$med->concentracion} tiene {$stock} unidades (mínimo: {$med->stock_minimo}).",
                'referencia_tipo' => 'medicamento',
                'referencia_id' => $med->id,
                'datos' => [
                    'medicamento' => $med->nombre . ' ' . $med->concentracion,
                    'stock_actual' => $stock,
                    'stock_minimo' => $med->stock_minimo,
                ],
                'estado' => 'activa',
            ]);

            if ($alerta) $count++;
        }

        return $count;
    }

    private static function chequearCaducidadProxima(): int
    {
        $count = 0;

        $lotes = Lote::where('cantidad_disponible', '>', 0)
            ->where('activo', true)
            ->whereBetween('fecha_caducidad', [now(), now()->addDays(30)])
            ->with('medicamento')
            ->get();

        foreach ($lotes as $lote) {
            $dias = $lote->dias_para_caducar;
            $nivel = $dias <= 7 ? 'critico' : 'advertencia';

            $alerta = self::crearSiNoExiste([
                'tipo' => 'inventario',
                'categoria' => 'caducidad_proxima',
                'nivel' => $nivel,
                'titulo' => "Medicamento caduca en {$dias} días",
                'mensaje' => "El lote {$lote->codigo_lote} de {$lote->medicamento?->nombre} caduca el {$lote->fecha_caducidad->format('d/m/Y')}. Disponibles: {$lote->cantidad_disponible} unidades.",
                'referencia_tipo' => 'lote',
                'referencia_id' => $lote->id,
                'datos' => [
                    'medicamento' => $lote->medicamento?->nombre,
                    'lote' => $lote->codigo_lote,
                    'caducidad' => $lote->fecha_caducidad->format('d/m/Y'),
                    'dias_restantes' => $dias,
                    'cantidad' => $lote->cantidad_disponible,
                ],
                'estado' => 'activa',
            ]);

            if ($alerta) $count++;
        }

        return $count;
    }

    // ============ PACIENTES ============

    private static function chequearTriageSinCama(): int
    {
        $count = 0;

        // Pacientes con triage rojo/naranja en las últimas 6h sin cama asignada
        $signos = SignoVital::whereIn('triage', ['rojo', 'naranja'])
            ->where('created_at', '>=', now()->subHours(6))
            ->with('paciente')
            ->get();

        foreach ($signos as $signo) {
            if (!$signo->paciente) continue;

            // ¿Tiene cama asignada activa?
            $tieneCama = DB::table('cama_paciente')
                ->where('paciente_id', $signo->paciente_id)
                ->where('activa', true)
                ->exists();

            if ($tieneCama) continue;

            $alerta = self::crearSiNoExiste([
                'tipo' => 'paciente',
                'categoria' => 'triage_sin_cama',
                'nivel' => 'critico',
                'titulo' => 'Paciente crítico sin cama',
                'mensaje' => "{$signo->paciente->nombre_completo} tiene triage " . strtoupper($signo->triage) . " desde " . $signo->created_at->diffForHumans() . " y no tiene cama asignada.",
                'referencia_tipo' => 'paciente',
                'referencia_id' => $signo->paciente_id,
                'datos' => [
                    'paciente' => $signo->paciente->nombre_completo,
                    'triage' => $signo->triage,
                    'registrado' => $signo->created_at->format('d/m/Y H:i'),
                ],
                'estado' => 'activa',
            ]);

            if ($alerta) $count++;
        }

        return $count;
    }

    private static function chequearSignosCriticos(): int
    {
        $count = 0;

        // Signos vitales anormales en las últimas 2h
        $signos = SignoVital::where('created_at', '>=', now()->subHours(2))
            ->where(function ($q) {
                $q->where('temperatura', '>=', 39.5)
                  ->orWhere('temperatura', '<=', 35)
                  ->orWhere('frecuencia_cardiaca', '>=', 130)
                  ->orWhere('frecuencia_cardiaca', '<=', 45)
                  ->orWhere('saturacion_oxigeno', '<=', 88)
                  ->orWhere('frecuencia_respiratoria', '>=', 30);
            })
            ->with('paciente')
            ->get();

        foreach ($signos as $signo) {
            if (!$signo->paciente) continue;

            $problemas = [];
            if ($signo->temperatura >= 39.5) $problemas[] = "T° {$signo->temperatura}°C";
            if ($signo->temperatura <= 35 && $signo->temperatura > 0) $problemas[] = "T° {$signo->temperatura}°C";
            if ($signo->frecuencia_cardiaca >= 130) $problemas[] = "FC {$signo->frecuencia_cardiaca}";
            if ($signo->frecuencia_cardiaca <= 45 && $signo->frecuencia_cardiaca > 0) $problemas[] = "FC {$signo->frecuencia_cardiaca}";
            if ($signo->saturacion_oxigeno <= 88) $problemas[] = "SpO₂ {$signo->saturacion_oxigeno}%";
            if ($signo->frecuencia_respiratoria >= 30) $problemas[] = "FR {$signo->frecuencia_respiratoria}";

            $alerta = self::crearSiNoExiste([
                'tipo' => 'paciente',
                'categoria' => 'signos_criticos',
                'nivel' => 'critico', 
                'titulo' => 'Signos vitales críticos',
                'mensaje' => "{$signo->paciente->nombre_completo}: " . implode(', ', $problemas),
                'referencia_tipo' => 'paciente',
                'referencia_id' => $signo->paciente_id,
                'datos' => [
                    'paciente' => $signo->paciente->nombre_completo,
                    'problemas' => $problemas,
                    'registrado' => $signo->created_at->format('d/m/Y H:i'),
                ],
                'estado' => 'activa',
            ]);

            if ($alerta) $count++;
        }

        return $count;
    }

    // ============ OPERACIÓN ============

    private static function chequearCitasSinConfirmar(): int
    {
        $count = 0;

        // Citas programadas para mañana sin confirmar
        $citas = Cita::whereDate('fecha_hora', today()->addDay())
            ->where('estado', 'programada')
            ->with(['paciente', 'medico'])
            ->get();

        foreach ($citas as $cita) {
            $alerta = self::crearSiNoExiste([
                'tipo' => 'operacion',
                'categoria' => 'cita_sin_confirmar',
                'nivel' => 'advertencia',
                'titulo' => 'Cita sin confirmar para mañana',
                'mensaje' => "Paciente {$cita->paciente?->nombre_completo} con {$cita->medico?->nombre_completo} a las {$cita->fecha_hora->format('H:i')}.",
                'referencia_tipo' => 'cita',
                'referencia_id' => $cita->id,
                'datos' => [
                    'paciente' => $cita->paciente?->nombre_completo,
                    'medico' => $cita->medico?->nombre_completo,
                    'fecha' => $cita->fecha_hora->format('d/m/Y H:i'),
                ],
                'estado' => 'activa',
            ]);

            if ($alerta) $count++;
        }

        return $count;
    }

    private static function chequearMedicosSinTurno(): int
    {
        $count = 0;

        // Médicos activos sin turno asignado hoy
        $hoy = today();

        $medicos = User::whereHas('roles', fn($q) => $q->whereRaw('LOWER(name) LIKE ?', ['%medic%']))
            ->where('activo', true)
            ->with(['turnos' => function ($q) use ($hoy) {
                $q->wherePivot('activo', true)
                  ->wherePivot('fecha_inicio', '<=', $hoy->toDateString())
                  ->where(function ($sub) use ($hoy) {
                      $sub->whereNull('turno_user.fecha_fin')
                          ->orWhere('turno_user.fecha_fin', '>=', $hoy->toDateString());
                  });
            }])
            ->get();

        foreach ($medicos as $medico) {
            if ($medico->turnos->isEmpty()) {
                $alerta = self::crearSiNoExiste([
                    'tipo' => 'operacion',
                    'categoria' => 'medico_sin_turno',
                    'nivel' => 'info',
                    'titulo' => 'Médico sin turno hoy',
                    'mensaje' => "Dr. {$medico->nombre_completo} no tiene turno asignado para hoy.",
                    'referencia_tipo' => 'user',
                    'referencia_id' => $medico->id,
                    'estado' => 'activa',
                ]);

                if ($alerta) $count++;
            }
        }

        return $count;
    }
}