<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hospitales', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('tipo')->nullable();          // público, privado, IMSS, ISSSTE
            $table->string('nivel')->nullable();         // primero, segundo, tercero
            $table->string('direccion')->nullable();
            $table->string('telefono', 50)->nullable();
            $table->decimal('latitud', 10, 7);
            $table->decimal('longitud', 10, 7);
            $table->text('servicios')->nullable();
            $table->boolean('tiene_urgencias')->default(true);
            $table->boolean('tiene_uci')->default(false);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hospitales');
    }
};