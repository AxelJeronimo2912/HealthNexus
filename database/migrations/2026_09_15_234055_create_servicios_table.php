<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('servicios', function (Blueprint $table) {
            $table->id();

            $table->string('codigo', 20)->unique();     // Ej. CONS-EXT, URG, HOSP
            $table->string('nombre');                    // Ej. Consulta Externa
            $table->string('tipo');                      // consulta_externa, urgencias, hospitalizacion, quirofano, farmacia, enfermeria, laboratorio, imagenologia, otro

            // Ubicación
            $table->string('ubicacion')->nullable();
            $table->string('piso')->nullable();
            $table->string('ala')->nullable();

            // Horario
            $table->time('hora_apertura')->nullable();
            $table->time('hora_cierre')->nullable();
            $table->boolean('abierto_24h')->default(false);

            // Capacidad
            $table->integer('capacidad')->nullable();    // pacientes simultáneos, camas, etc.

            // Contacto interno
            $table->string('extension_telefonica', 20)->nullable();

            // Descripción
            $table->text('descripcion')->nullable();
            $table->text('notas')->nullable();

            $table->boolean('activo')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servicios');
    }
};