<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('especialidad_servicio', function (Blueprint $table) {
            $table->id();

            $table->foreignId('especialidad_id')->constrained('especialidades')->cascadeOnDelete();
            $table->foreignId('servicio_id')->constrained('servicios')->cascadeOnDelete();

            $table->boolean('activo')->default(true);

            $table->timestamps();

            $table->unique(['especialidad_id', 'servicio_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('especialidad_servicio');
    }
};