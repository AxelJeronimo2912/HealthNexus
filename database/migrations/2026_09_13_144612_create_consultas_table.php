<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cita_id')->constrained('citas')->cascadeOnDelete();
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->foreignId('medico_id')->constrained('users')->cascadeOnDelete();

            // SOAP
            $table->text('subjetivo')->nullable();      // S — síntomas referidos
            $table->text('objetivo')->nullable();       // O — exploración física
            $table->text('analisis')->nullable();       // A — diagnóstico
            $table->text('plan')->nullable();           // P — tratamiento

            // Diagnóstico CIE-10 (texto libre por ahora)
            $table->string('diagnostico_principal')->nullable();
            $table->string('diagnostico_secundario')->nullable();

            // Signos vitales en el momento de la consulta
            $table->decimal('temperatura', 4, 1)->nullable();
            $table->integer('frecuencia_cardiaca')->nullable();
            $table->integer('frecuencia_respiratoria')->nullable();
            $table->string('presion_arterial')->nullable();
            $table->integer('saturacion_oxigeno')->nullable();
            $table->integer('glucosa')->nullable();

            // Somatometría
            $table->decimal('peso', 5, 2)->nullable();
            $table->decimal('talla', 4, 2)->nullable();
            $table->decimal('imc', 4, 1)->nullable();
            $table->decimal('perimetro_abdominal', 5, 2)->nullable();

            // Receta (texto libre por si no se usan medicamentos del catálogo)
            $table->text('receta_libre')->nullable();

            // Notas adicionales
            $table->text('notas')->nullable();

            // Estado
            $table->enum('estado', ['borrador', 'finalizada'])->default('borrador');
            $table->timestamp('finalizada_en')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultas');
    }
};