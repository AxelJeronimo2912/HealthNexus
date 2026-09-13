<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consulta_medicamento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consulta_id')->constrained('consultas')->cascadeOnDelete();
            $table->foreignId('medicamento_id')->constrained('medicamentos')->cascadeOnDelete();

            $table->string('dosis')->nullable();          // "500 mg"
            $table->string('via')->nullable();            // "Oral"
            $table->string('frecuencia')->nullable();     // "Cada 8 horas"
            $table->string('duracion')->nullable();       // "7 días"
            $table->text('indicaciones')->nullable();     // "Tomar después de alimentos"

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consulta_medicamento');
    }
};