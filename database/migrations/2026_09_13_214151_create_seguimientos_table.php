<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seguimientos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('cama_id')->nullable()->constrained('camas')->nullOnDelete();

            // Estado del seguimiento
            $table->enum('tipo', ['evolucion', 'nota_enfermeria', 'interconsulta', 'traslado', 'alta']);
            $table->enum('estado_paciente', ['estable', 'mejorando', 'grave', 'critico', 'fallecido'])->nullable();

            // Contenido
            $table->text('contenido');

            // Signos vitales del momento (opcional)
            $table->decimal('temperatura', 4, 1)->nullable();
            $table->integer('frecuencia_cardiaca')->nullable();
            $table->integer('frecuencia_respiratoria')->nullable();
            $table->string('presion_arterial')->nullable();
            $table->integer('saturacion_oxigeno')->nullable();

            $table->timestamps();

            $table->index(['paciente_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seguimientos');
    }
};