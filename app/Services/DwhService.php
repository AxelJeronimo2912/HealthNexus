<?php

namespace App\Services;

use App\Models\SignoVital;
use Illuminate\Support\Facades\DB;

class DwhService
{
    /**
     * Devuelve todas las métricas del Data Warehouse.
     */
    public function metricas(): array
    {
        return [
            'kpis'         => $this->kpis(),
            'triage'       => $this->distribucionTriage(),
            'por_hora'     => $this->actividadPorHora(),
            'fc_stats'     => $this->estadisticasFC(),
            'percentiles'  => $this->percentilesFC(),
            'correlaciones'=> $this->correlaciones(),
            'por_usuario'  => $this->topUsuarios(),
            'calidad'      => $this->calidadDataset(),
            'por_especialidad' => $this->porEspecialidad(),
        ];
    }

    /**
     * KPIs principales: documentos, rango de fechas, calidad.
     */
    public function kpis(): array
    {
        $total       = SignoVital::count();
        $conTriage   = SignoVital::whereNotNull('triage')->count();
        $conFC       = SignoVital::whereNotNull('frecuencia_cardiaca')->count();
        $minFecha    = SignoVital::min('created_at');
        $maxFecha    = SignoVital::max('created_at');

        return [
            'total_registros'  => $total,
            'con_triage'       => $conTriage,
            'con_fc'           => $conFC,
            'colecciones'      => 6, // simuladas para la demo
            'rango_fechas'     => [
                'desde' => $minFecha ? \Carbon\Carbon::parse($minFecha)->format('M Y') : '—',
                'hasta' => $maxFecha ? \Carbon\Carbon::parse($maxFecha)->format('M Y') : '—',
            ],
            'calidad' => $total > 0 ? round(($conFC / $total) * 100, 1) : 0,
        ];
    }

    /**
     * Distribución por nivel de triaje.
     */
    public function distribucionTriage(): array
    {
        $orden = ['rojo', 'naranja', 'amarillo', 'verde', 'azul'];
        $colores = [
            'rojo'     => '#dc2626',
            'naranja'  => '#ea580c',
            'amarillo' => '#eab308',
            'verde'    => '#16a34a',
            'azul'     => '#2563eb',
        ];

        $raw = SignoVital::select('triage', DB::raw('COUNT(*) as total'))
            ->whereNotNull('triage')
            ->groupBy('triage')
            ->pluck('total', 'triage')
            ->toArray();

        $resultado = [];
        foreach ($orden as $nivel) {
            $resultado[] = [
                'nivel'  => ucfirst($nivel),
                'clave'  => $nivel,
                'total'  => $raw[$nivel] ?? 0,
                'color'  => $colores[$nivel],
            ];
        }

        return $resultado;
    }

    /**
     * Actividad por hora del día (0-23).
     */
    public function actividadPorHora(): array
    {
        $raw = SignoVital::select(
                DB::raw('HOUR(created_at) as hora'),
                DB::raw('COUNT(*) as total'),
                DB::raw('AVG(frecuencia_cardiaca) as fc_prom')
            )
            ->whereNotNull('frecuencia_cardiaca')
            ->groupBy('hora')
            ->orderBy('hora')
            ->get()
            ->keyBy('hora');

        $resultado = [];
        for ($h = 0; $h < 24; $h++) {
            $fila = $raw[$h] ?? null;
            $resultado[] = [
                'hora'    => str_pad((string) $h, 2, '0', STR_PAD_LEFT) . ':00',
                'total'   => $fila ? (int) $fila->total : 0,
                'fc_prom' => $fila && $fila->fc_prom ? round((float) $fila->fc_prom, 1) : 0,
            ];
        }
        return $resultado;
    }

    /**
     * Estadísticas básicas de frecuencia cardiaca.
     */
    public function estadisticasFC(): array
    {
        $stats = SignoVital::whereNotNull('frecuencia_cardiaca')
            ->selectRaw('
                COUNT(*) as n,
                AVG(frecuencia_cardiaca) as media,
                MAX(frecuencia_cardiaca) as maximo,
                MIN(frecuencia_cardiaca) as minimo,
                STDDEV(frecuencia_cardiaca) as desviacion
            ')
            ->first();

        return [
            'n'          => (int)   ($stats->n ?? 0),
            'media'      => round((float) ($stats->media ?? 0), 2),
            'maximo'     => (int)   ($stats->maximo ?? 0),
            'minimo'     => (int)   ($stats->minimo ?? 0),
            'desviacion' => round((float) ($stats->desviacion ?? 0), 2),
        ];
    }

    /**
     * Percentiles y cuartiles de FC.
     */
    public function percentilesFC(): array
    {
        $valores = SignoVital::whereNotNull('frecuencia_cardiaca')
            ->orderBy('frecuencia_cardiaca')
            ->pluck('frecuencia_cardiaca')
            ->toArray();

        if (empty($valores)) {
            return ['p10'=>0,'p25'=>0,'p50'=>0,'p75'=>0,'p90'=>0,'q1'=>0,'q2'=>0,'q3'=>0,'iqr'=>0];
        }

        $p = fn($pct) => $this->percentil($valores, $pct);

        $q1 = $p(25);
        $q3 = $p(75);

        return [
            'p10' => $p(10),
            'p25' => $p(25),
            'p50' => $p(50),
            'p75' => $p(75),
            'p90' => $p(90),
            'q1'  => $q1,
            'q2'  => $p(50),
            'q3'  => $q3,
            'iqr' => $q3 - $q1,
        ];
    }

    /**
     * Correlaciones de Pearson:
     *  - hora vs FC
     *  - triage vs FC
     *  - edad vs FC
     */
    public function correlaciones(): array
    {
        $datos = SignoVital::whereNotNull('frecuencia_cardiaca')
            ->whereNotNull('created_at')
            ->with('paciente:id,fecha_nacimiento')
            ->get(['id','paciente_id','frecuencia_cardiaca','triage','created_at']);

        if ($datos->count() < 3) {
            return [
                'hora_fc'   => 0,
                'triage_fc' => 0,
                'edad_fc'   => 0,
            ];
        }

        $horaArr   = [];
        $triageArr = [];
        $edadArr   = [];
        $fcArr     = [];

        $mapaTriage = ['rojo'=>5,'naranja'=>4,'amarillo'=>3,'verde'=>2,'azul'=>1];

        foreach ($datos as $d) {
            $fc = (float) $d->frecuencia_cardiaca;
            if ($fc <= 0) continue;

            $horaArr[] = (float) $d->created_at->hour;
            $fcArr[]   = $fc;

            if ($d->triage && isset($mapaTriage[$d->triage])) {
                $triageArr[] = (float) $mapaTriage[$d->triage];
            } else {
                $triageArr[] = null;
            }

            $edad = $d->paciente?->edad;
            $edadArr[] = $edad !== null ? (float) $edad : null;
        }

        return [
            'hora_fc'   => round($this->pearson($horaArr, $fcArr), 4),
            'triage_fc' => round($this->pearsonFiltrado($triageArr, $fcArr), 4),
            'edad_fc'   => round($this->pearsonFiltrado($edadArr, $fcArr), 4),
        ];
    }

    /**
     * Top 5 usuarios que más registros capturan.
     */
    public function topUsuarios(): array
    {
        return SignoVital::select(
                'user_id',
                DB::raw('COUNT(*) as total'),
                DB::raw('AVG(frecuencia_cardiaca) as fc_prom')
            )
            ->whereNotNull('user_id')
            ->with('user:id,name,nombre,apellido_paterno,apellido_materno')
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(function ($fila) {
                $u = $fila->user;
                $nombre = $u?->nombre_completo ?: ($u?->name ?? 'Usuario #' . $fila->user_id);
                return [
                    'usuario' => $nombre,
                    'total'   => (int) $fila->total,
                    'fc_prom' => round((float) $fila->fc_prom, 1),
                ];
            })
            ->toArray();
    }

    /**
     * Calidad del dataset.
     */
    public function calidadDataset(): array
    {
        $total = SignoVital::count();
        if ($total === 0) {
            return ['nulos'=>0,'duplicados'=>0,'outliers'=>0,'valido_pct'=>0];
        }

        $nulos = SignoVital::where(function ($q) {
            $q->whereNull('frecuencia_cardiaca')
              ->orWhereNull('temperatura')
              ->orWhereNull('saturacion_oxigeno');
        })->count();

        $duplicados = SignoVital::select('paciente_id', 'created_at')
            ->groupBy('paciente_id', 'created_at')
            ->havingRaw('COUNT(*) > 1')
            ->get()
            ->count();

        $outliers = SignoVital::where(function ($q) {
            $q->where('frecuencia_cardiaca', '<', 40)
              ->orWhere('frecuencia_cardiaca', '>', 200)
              ->orWhere('temperatura', '<', 35)
              ->orWhere('temperatura', '>', 42);
        })->count();

        $validos = $total - $nulos - $outliers;

        return [
            'nulos'      => $nulos,
            'duplicados' => $duplicados,
            'outliers'   => $outliers,
            'valido_pct' => round(($validos / $total) * 100, 1),
        ];
    }

    /**
     * Distribución por especialidad del médico (si existe).
     */
    public function porEspecialidad(): array
    {
        // Como no tenemos especialidad directa en signos_vitales,
        // usamos la especialidad principal del user (médico).
        $usuarios = SignoVital::select('user_id', DB::raw('COUNT(*) as total'))
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->limit(10)
            ->with(['user.especialidadPrincipal'])
            ->get();

        $porEsp = [];
        foreach ($usuarios as $u) {
            $esp = optional($u->user)->especialidadPrincipal->first();
            $nombre = $esp?->nombre ?? 'Sin especialidad';
            if (!isset($porEsp[$nombre])) {
                $porEsp[$nombre] = 0;
            }
            $porEsp[$nombre] += $u->total;
        }

        arsort($porEsp);
        $resultado = [];
        foreach (array_slice($porEsp, 0, 5, true) as $nombre => $total) {
            $resultado[] = ['especialidad' => $nombre, 'total' => $total];
        }
        return $resultado;
    }

    // ============ Helpers ============

    private function percentil(array $ordenados, float $p): float
    {
        $n = count($ordenados);
        if ($n === 0) return 0;
        $pos = ($n - 1) * ($p / 100);
        $base = (int) floor($pos);
        $resto = $pos - $base;
        if (!isset($ordenados[$base + 1])) {
            return round((float) $ordenados[$base], 2);
        }
        return round($ordenados[$base] + $resto * ($ordenados[$base + 1] - $ordenados[$base]), 2);
    }

    private function pearson(array $x, array $y): float
    {
        $n = min(count($x), count($y));
        if ($n < 2) return 0.0;

        $mx = array_sum($x) / $n;
        $my = array_sum($y) / $n;

        $num = 0.0; $dx = 0.0; $dy = 0.0;
        for ($i = 0; $i < $n; $i++) {
            $a = $x[$i] - $mx;
            $b = $y[$i] - $my;
            $num += $a * $b;
            $dx += $a * $a;
            $dy += $b * $b;
        }
        if ($dx * $dy == 0) return 0.0;
        return $num / sqrt($dx * $dy);
    }

    /**
     * Pearson ignorando pares con null.
     */
    private function pearsonFiltrado(array $x, array $y): float
    {
        $xs = []; $ys = [];
        foreach ($x as $i => $v) {
            if ($v === null || !isset($y[$i]) || $y[$i] === null) continue;
            $xs[] = $v;
            $ys[] = $y[$i];
        }
        return $this->pearson($xs, $ys);
    }
}