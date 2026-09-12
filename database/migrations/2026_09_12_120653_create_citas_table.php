<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->foreignId('medico_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('turno_id')->nullable()->constrained('turnos')->nullOnDelete();
            $table->foreignId('signo_vital_id')->nullable()->constrained('signos_vitales')->nullOnDelete();
            $table->foreignId('creado_por')->nullable()->constrained('users')->nullOnDelete();

            $table->dateTime('fecha_hora');
            $table->integer('duracion_minutos')->default(30);

            $table->enum('estado', ['programada', 'confirmada', 'en_curso', 'atendida', 'cancelada', 'no_asistio'])
                ->default('programada');

            $table->enum('triage_al_momento', ['rojo', 'naranja', 'amarillo', 'verde', 'azul'])->nullable();

            $table->text('motivo')->nullable();
            $table->text('notas')->nullable();

            $table->timestamps();

            $table->index(['medico_id', 'fecha_hora']);
            $table->index(['paciente_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};