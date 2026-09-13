<?php

namespace Database\Seeders;

use App\Models\Lote;
use App\Models\Medicamento;
use Illuminate\Database\Seeder;

class LoteSeeder extends Seeder
{
    public function run(): void
    {
        $medicamentos = Medicamento::all();

        foreach ($medicamentos as $med) {
            // Crear 1-3 lotes por medicamento
            $numLotes = rand(1, 3);
            $totalStock = 0;

            for ($i = 0; $i < $numLotes; $i++) {
                $cantidad = rand(30, 150);
                $totalStock += $cantidad;

                // Caducidad: entre hoy y 2 años
                $diasCaducidad = rand(-30, 730);
                $fechaCaducidad = now()->addDays($diasCaducidad);

                Lote::create([
                    'medicamento_id' => $med->id,
                    'user_id' => null,
                    'codigo_lote' => 'LOTE-' . strtoupper(bin2hex(random_bytes(3))),
                    'fecha_caducidad' => $fechaCaducidad,
                    'fecha_ingreso' => now()->subDays(rand(1, 60)),
                    'cantidad_inicial' => $cantidad,
                    'cantidad_disponible' => $cantidad,
                    'precio_compra' => rand(20, 500),
                    'proveedor' => ['Distribuidora Médica', 'Farmacéutica Nacional', 'Proveedor Local'][rand(0, 2)],
                    'activo' => true,
                ]);
            }

            // Actualizar stock global con la suma de lotes
            $med->update(['stock_actual' => $totalStock]);
        }

        $this->command->info(' Lotes creados para ' . $medicamentos->count() . ' medicamentos.');
    }
}