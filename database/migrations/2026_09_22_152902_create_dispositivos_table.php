<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admisiones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // Datos de la admisión
            $table->string('folio')->unique();              // ADM-20260922-001
            $table->dateTime('fecha_hora_llegada');
            $table->enum('tipo', ['urgencias', 'consulta_externa', 'hospitalizacion', 'traslado']);
            $table->enum('triage', ['rojo', 'naranja', 'amarillo', 'verde', 'azul'])->nullable();
            $table->text('motivo')->nullable();
            $table->text('diagnostico_presuntivo')->nullable();

            // Estado
            $table->enum('estado', ['en_espera', 'atendido', 'hospitalizado', 'derivado', 'alta', 'fallecido'])
                ->default('en_espera');

            // Si fue derivado
            $table->foreignId('hospital_derivado_id')->nullable()->constrained('hospitales')->nullOnDelete();
            $table->dateTime('fecha_derivacion')->nullable();
            $table->text('motivo_derivacion')->nullable();
            $table->string('pase_salida_pdf')->nullable();  // ruta del PDF generado

            // Si fue hospitalizado
            $table->foreignId('cama_id')->nullable()->constrained('camas')->nullOnDelete();
            $table->foreignId('medico_id')->nullable()->constrained('users')->nullOnDelete();

            $table->text('notas')->nullable();

            $table->timestamps();

            $table->index(['fecha_hora_llegada', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admisiones');
    }
};