<?php

namespace App\Console\Commands;

use App\Services\AlertaService;
use Illuminate\Console\Command;

class GenerarAlertas extends Command
{
    protected $signature = 'alertas:generar';
    protected $description = 'Genera alertas inteligentes analizando el sistema';

    public function handle(): int
    {
        $this->info('Analizando sistema...');

        $resultados = AlertaService::generarTodas();

        $total = array_sum($resultados);

        foreach ($resultados as $categoria => $count) {
            $this->line("  {$categoria}: {$count} alertas nuevas");
        }

        $this->info("Total: {$total} alertas generadas.");

        return self::SUCCESS;
    }
}