<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medicamento_id')->constrained('medicamentos')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('codigo_lote')->nullable();           // número de lote del proveedor
            $table->date('fecha_caducidad');
            $table->date('fecha_ingreso');
            $table->integer('cantidad_inicial');
            $table->integer('cantidad_disponible');

            $table->decimal('precio_compra', 10, 2)->nullable();
            $table->string('proveedor')->nullable();

            $table->text('notas')->nullable();
            $table->boolean('activo')->default(true);

            $table->timestamps();

            $table->index(['medicamento_id', 'fecha_caducidad']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lotes');
    }
};