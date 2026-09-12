<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('turno_user', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('turno_id')->constrained('turnos')->cascadeOnDelete();

            // Día de la semana: 0=domingo, 1=lunes, ... 6=sábado
            $table->tinyInteger('dia_semana')->nullable();  // null = todos los días

            // Vigencia
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();          // null = indefinido

            // Área/servicio donde aplica
            $table->string('area')->nullable();             // Hospitalización, Urgencias, etc.

            $table->boolean('activo')->default(true);
            $table->text('notas')->nullable();

            $table->timestamps();

            // Índices para consultas rápidas
            $table->index(['user_id', 'activo']);
            $table->index(['dia_semana', 'activo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('turno_user');
    }
};