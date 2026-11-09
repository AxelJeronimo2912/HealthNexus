<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitudes_estudio', function (Blueprint $table) {
            $table->id();
            $table->string('folio', 30)->unique();

            // Relaciones
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->foreignId('servicio_id')->constrained('servicios');   // Imagenología o Laboratorio
            $table->foreignId('cita_id')->nullable()->constrained('citas')->nullOnDelete();
            $table->foreignId('cuenta_id')->nullable()->constrained('cuentas')->nullOnDelete();
            $table->foreignId('cuenta_item_id')->nullable()->constrained('cuenta_items')->nullOnDelete();
            $table->foreignId('medico_solicita_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users'); // quien captura

            // Datos clínicos
            $table->string('tipo_estudio', 100);                // "Radiografía de tórax", "Biometría hemática", etc.
            $table->text('indicaciones')->nullable();           // indicaciones clínicas / justificación
            $table->text('notas')->nullable();

            // Estado
            $table->enum('estado', ['solicitado', 'en_proceso', 'resultado_listo', 'entregado', 'cancelado'])
                  ->default('solicitado');

            // Fechas
            $table->dateTime('solicitado_en')->nullable();
            $table->dateTime('resultado_en')->nullable();
            $table->dateTime('entregado_en')->nullable();

            $table->timestamps();

            $table->index(['paciente_id', 'estado']);
            $table->index('tipo_estudio');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes_estudio');
    }
};