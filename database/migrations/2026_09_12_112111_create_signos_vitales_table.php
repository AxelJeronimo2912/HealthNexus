<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('signos_vitales', function (Blueprint $table) {
            $table->id();

            // Paciente y usuario que registra
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // Signos vitales
            $table->decimal('temperatura', 4, 1)->nullable();          // °C
            $table->integer('frecuencia_cardiaca')->nullable();        // lpm
            $table->integer('frecuencia_respiratoria')->nullable();    // rpm
            $table->string('presion_arterial')->nullable();            // "120/80"
            $table->integer('saturacion_oxigeno')->nullable();         // %
            $table->integer('glucosa')->nullable();                    // mg/dL
            $table->decimal('peso', 5, 2)->nullable();                 // kg
            $table->decimal('talla', 4, 2)->nullable();                // m
            $table->integer('escala_dolor')->nullable();               // 0-10

            // Triage
            $table->enum('triage', ['rojo', 'naranja', 'amarillo', 'verde', 'azul'])->nullable();
            $table->boolean('triage_manual')->default(false); // si el usuario lo cambió

            // Notas
            $table->text('motivo_consulta')->nullable();
            $table->text('notas')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('signos_vitales');
    }
};