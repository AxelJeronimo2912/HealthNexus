<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('turnos', function (Blueprint $table) {
            $table->id();

            $table->string('nombre')->unique();          // Mañana, Tarde, Noche, Guardia 24h
            $table->string('codigo', 20)->unique();      // MAT, TAR, NOC, G24
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->boolean('cruza_medianoche')->default(false); // si hora_fin < hora_inicio
            $table->integer('duracion_horas')->nullable();        // calculado
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('turnos');
    }
};