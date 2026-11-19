<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Índices para Atención (cuentas)
        Schema::table('cuentas', function (Blueprint $table) {
            // Índice para buscar por fecha
            $table->index('created_at', 'idx_cuentas_created_at');
        });

        Schema::table('cuenta_items', function (Blueprint $table) {
            // Índices para agrupar por servicio y cruzar con cuentas
            $table->index('servicio_id', 'idx_cuenta_items_servicio_id');
            $table->index('cuenta_id', 'idx_cuenta_items_cuenta_id');
        });

        // Índices para Insumos (movimientos_inventario)
        Schema::table('movimientos_inventario', function (Blueprint $table) {
            // Índice para filtrar por fecha y tipo (entrada)
            $table->index(['created_at', 'tipo'], 'idx_movimientos_created_tipo');
            $table->index('medicamento_id', 'idx_movimientos_medicamento_id');
        });
    }

    public function down(): void
    {
        Schema::table('cuentas', function (Blueprint $table) {
            $table->dropIndex('idx_cuentas_created_at');
        });

        Schema::table('cuenta_items', function (Blueprint $table) {
            $table->dropIndex('idx_cuenta_items_servicio_id');
            $table->dropIndex('idx_cuenta_items_cuenta_id');
        });

        Schema::table('movimientos_inventario', function (Blueprint $table) {
            $table->dropIndex('idx_movimientos_created_tipo');
            $table->dropIndex('idx_movimientos_medicamento_id');
        });
    }
};