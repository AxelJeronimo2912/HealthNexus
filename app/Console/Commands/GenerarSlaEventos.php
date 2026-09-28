<?php

namespace App\Console\Commands;

use App\Services\SlaService;
use Illuminate\Console\Command;

class GenerarSlaEventos extends Command
{
    protected $signature = 'sla:generar {--dias=30}';
    protected $description = 'Genera eventos SLA desde los datos reales del sistema';

    public function handle(): int
    {
        $dias = (int) $this->option('dias');

        $this->info("Analizando eventos de los últimos {$dias} días...");

        $creados = SlaService::generarDesdeDatos($dias);

        $this->info(" {$creados} eventos SLA procesados.");

        return self::SUCCESS;
    }
}