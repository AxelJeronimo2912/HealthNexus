<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('simulador_predictivo_casos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('paciente_id')->nullable()->constrained('pacientes')->nullOnDelete();

            // Inputs
            $table->integer('fc');
            $table->integer('spo2');
            $table->decimal('temp', 4, 1);
            $table->integer('edad');

            // Outputs de los 5 modelos
            $table->decimal('prob_critico', 5, 4)->nullable();      // Reg. Logística
            $table->string('riesgo_svm', 20)->nullable();            // Alto/Bajo
            $table->string('recomendacion_arbol', 50)->nullable();   // UCI/Observación/...
            $table->string('voto_rf', 20)->nullable();               // Crítico/No crítico
            $table->decimal('spo2_esperado', 5, 2)->nullable();      // Reg. Lineal

            // Resultado real (para evaluación)
            $table->string('diagnostico_final', 20)->nullable();     // vivo/fallecio
            $table->decimal('costo_real', 12, 2)->nullable();
            $table->decimal('costo_predicho', 12, 2)->nullable();
            $table->integer('dias_real')->nullable();
            $table->integer('dias_predicho')->nullable();
            $table->boolean('cerrado')->default(false);

            $table->timestamps();

            $table->index(['cerrado', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('simulador_predictivo_casos');
    }
};