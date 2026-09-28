<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sla_eventos', function (Blueprint $table) {
            $table->id();

            // Qué módulo
            $table->enum('modulo', ['quirofano', 'urgencias', 'farmacia', 'hospitalizacion']);

            // Qué evento específico
            $table->string('event_type'); // cita_atendida, consulta_cerrada, dispensacion, alta_paciente

            // Referencia al origen
            $table->string('referencia_tipo')->nullable();
            $table->unsignedBigInteger('referencia_id')->nullable();

            // Duración (métrica del SLA)
            $table->decimal('duration_minutes', 10, 2);

            // Hora del evento (0-23) para patrones horarios
            $table->tinyInteger('start_hour');

            // Análisis estadístico
            $table->boolean('is_outlier')->default(false);
            $table->decimal('outlier_z_score', 8, 4)->nullable();

            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamp('evento_en');

            $table->timestamps();

            $table->index(['modulo', 'evento_en']);
            $table->index(['is_outlier', 'modulo']);
            $table->index('start_hour');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sla_eventos');
    }
};