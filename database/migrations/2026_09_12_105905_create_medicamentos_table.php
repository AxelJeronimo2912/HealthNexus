<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medicamentos', function (Blueprint $table) {
            $table->id();

            // Identificación
            $table->string('nombre');                          // Nombre comercial
            $table->string('sustancia_activa')->nullable();    // Principio activo
            $table->string('presentacion')->nullable();        // Tableta, cápsula, jarabe, etc.
            $table->string('concentracion')->nullable();       // 500 mg, 10 ml, etc.
            $table->string('via_administracion')->nullable();  // Oral, IV, IM, etc.
            $table->string('laboratorio')->nullable();
            $table->string('codigo_barras')->nullable()->unique();
            $table->string('registro_sanitario')->nullable();

            // Clasificación
            $table->string('grupo_terapeutico')->nullable();
            $table->boolean('psicotropico')->default(false);
            $table->boolean('antibiotico')->default(false);
            $table->boolean('controlado')->default(false);

            // Inventario
            $table->string('unidad_medida')->default('pieza'); // pieza, caja, frasco
            $table->integer('stock_minimo')->default(0);
            $table->integer('stock_maximo')->default(0);
            $table->decimal('precio_compra', 10, 2)->nullable();
            $table->decimal('precio_venta', 10, 2)->nullable();

            // Estado
            $table->boolean('activo')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medicamentos');
    }
};