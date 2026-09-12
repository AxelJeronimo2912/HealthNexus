<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('camas', function (Blueprint $table) {
            $table->id();

            // Identificación
            $table->string('codigo')->unique();               // Ej. CAMA-101
            $table->string('nombre')->nullable();             // Nombre descriptivo opcional

            // Ubicación
            $table->string('piso')->nullable();               // Ej. Piso 1
            $table->string('ala')->nullable();                // Ej. Ala Norte
            $table->string('habitacion')->nullable();         // Ej. 101
            $table->string('area')->nullable();               // Ej. Hospitalización, Urgencias, UCI

            // Tipo
            $table->enum('tipo', ['general', 'pediatrica', 'uci', 'aislamiento', 'recuperacion', 'urgencias'])
                ->default('general');

            // Estado operativo
            $table->enum('estado', ['disponible', 'ocupada', 'mantenimiento', 'limpieza', 'fuera_servicio'])
                ->default('disponible');

            // Características
            $table->boolean('oxigeno')->default(false);
            $table->boolean('monitor')->default(false);
            $table->boolean('ventilador')->default(false);

            // Notas
            $table->text('notas')->nullable();

            // Estado del registro
            $table->boolean('activo')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('camas');
    }
};