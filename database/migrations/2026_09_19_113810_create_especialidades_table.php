<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('especialidades', function (Blueprint $table) {
            $table->id();

            $table->string('codigo', 20)->unique();   // CARD, PED, GIN...
            $table->string('nombre');                 // Cardiología
            $table->text('descripcion')->nullable();

            // Grupo: clinica, quirurgica, diagnostica, basica, otra
            $table->string('grupo')->default('clinica');

            // Duración por defecto de la consulta en minutos
            $table->integer('duracion_consulta_default')->default(30);

            // Color para el calendario (hex sin #)
            $table->string('color', 7)->nullable();

            $table->boolean('activo')->default(true);

            $table->timestamps();

            $table->index('nombre');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('especialidades');
    }
};