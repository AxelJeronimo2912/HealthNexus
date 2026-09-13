<?php

namespace App\Console\Commands;

use App\Models\Consulta;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class EncriptarConsultasExistentes extends Command
{
    /**
     * Nombre del comando para ejecutarlo desde la terminal.
     */
    protected $signature = 'consultas:encriptar 
                            {--dry-run : Solo mostrar qué se encriptaría, sin guardar cambios}
                            {--force : No pedir confirmación}';

    /**
     * Descripción del comando.
     */
    protected $description = 'Encripta los campos sensibles existentes (SOAP, receta_libre, notas) de la tabla consultas';

    /**
     * Campos a encriptar.
     */
    protected array $campos = [
        'subjetivo',
        'objetivo',
        'analisis',
        'plan',
        'receta_libre',
        'notas',
    ];

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        $total = Consulta::count();

        if ($total === 0) {
            $this->warn('⚠️  No hay consultas registradas en la base de datos.');
            return self::SUCCESS;
        }

        $this->info("📋 Total de consultas encontradas: {$total}");
        $this->newLine();

        if (!$dryRun && !$this->option('force')) {
            if (!$this->confirm('¿Deseas encriptar los datos existentes? Esta acción modificará la base de datos.', true)) {
                $this->warn('Operación cancelada.');
                return self::SUCCESS;
            }
        }

        if ($dryRun) {
            $this->warn('🔍 MODO DRY-RUN: No se guardará ningún cambio.');
            $this->newLine();
        }

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $encriptados = 0;
        $yaEncriptados = 0;
        $vacios = 0;
        $errores = 0;

        Consulta::query()
            ->orderBy('id')
            ->chunkById(100, function ($consultas) use (
                $dryRun,
                &$encriptados,
                &$yaEncriptados,
                &$vacios,
                &$errores,
                $bar
            ) {
                foreach ($consultas as $consulta) {
                    $update = [];

                    foreach ($this->campos as $campo) {
                        $valor = $consulta->getRawOriginal($campo);

                        // Campo vacío o nulo → saltar
                        if ($valor === null || $valor === '') {
                            $vacios++;
                            continue;
                        }

                        // Detectar si ya está encriptado
                        if ($this->estaEncriptado($valor)) {
                            $yaEncriptados++;
                            continue;
                        }

                        // Encriptar
                        try {
                            $update[$campo] = Crypt::encryptString((string) $valor);
                        } catch (\Throwable $e) {
                            $this->newLine();
                            $this->error("❌ Error en consulta ID {$consulta->id}, campo '{$campo}': {$e->getMessage()}");
                            $errores++;
                        }
                    }

                    if (!empty($update) && !$dryRun) {
                        DB::table('consultas')
                            ->where('id', $consulta->id)
                            ->update($update);
                    }

                    if (!empty($update)) {
                        $encriptados++;
                    }

                    $bar->advance();
                }
            });

        $bar->finish();
        $this->newLine(2);

        // Resumen final
        $this->info('═══════════════════════════════════════');
        $this->info('   RESUMEN');
        $this->info('═══════════════════════════════════════');
        $this->line("   Consultas procesadas:      {$encriptados}");
        $this->line("   Campos ya encriptados:     {$yaEncriptados}");
        $this->line("   Campos vacíos omitidos:    {$vacios}");
        $this->line("  Errores:                   {$errores}");
        $this->info('═══════════════════════════════════════');

        if ($dryRun) {
            $this->newLine();
            $this->warn(' Este fue un DRY-RUN. Ejecuta sin --dry-run para aplicar los cambios.');
        } else {
            $this->newLine();
            $this->info('🎉 ¡Datos encriptados correctamente!');
        }

        return self::SUCCESS;
    }

    /**
     * Detecta si un valor ya está encriptado por Laravel.
     * 
     * Los datos encriptados con Crypt::encryptString() son un payload JSON
     * en base64 con las claves: iv, value, mac, tag.
     */
    private function estaEncriptado(?string $valor): bool
    {
        if (empty($valor)) {
            return false;
        }

        // Los datos encriptados de Laravel son base64 de un JSON con estas claves
        try {
            $decoded = base64_decode($valor, true);
            if ($decoded === false) {
                return false;
            }

            $json = json_decode($decoded, true);
            if (!is_array($json)) {
                return false;
            }

            // El payload de Laravel siempre tiene estas claves
            return isset($json['iv'], $json['value'], $json['mac']);
        } catch (\Throwable $e) {
            return false;
        }
    }
}