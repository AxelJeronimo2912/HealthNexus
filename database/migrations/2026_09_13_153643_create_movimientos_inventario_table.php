<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos_inventario', function (Blueprint $table) {
            $table->id();

            $table->foreignId('medicamento_id')->constrained('medicamentos')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // tipo: entrada (+, compra), salida (-, receta), ajuste (+/-), devolucion (+)
            $table->enum('tipo', ['entrada', 'salida', 'ajuste', 'devolucion']);

            $table->integer('cantidad');         // positivo o negativo
            $table->integer('stock_anterior');
            $table->integer('stock_nuevo');

            // Referencia: qué originó el movimiento
            $table->string('referencia_tipo')->nullable();   // 'consulta', 'compra'
            $table->unsignedBigInteger('referencia_id')->nullable();

            $table->text('motivo')->nullable();
            $table->timestamps();

            $table->index(['medicamento_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_inventario');
    }
};