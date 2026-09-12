<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cama_paciente', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cama_id')->constrained('camas')->cascadeOnDelete();
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->foreignId('signo_vital_id')->nullable()->constrained('signos_vitales')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->dateTime('fecha_ingreso');
            $table->dateTime('fecha_egreso')->nullable();
            $table->text('motivo')->nullable();
            $table->boolean('activa')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cama_paciente');
    }
};