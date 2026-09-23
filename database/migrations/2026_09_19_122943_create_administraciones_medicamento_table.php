<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('administraciones_medicamento', function (Blueprint $table) {
            $table->id();

            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->foreignId('medicamento_id')->constrained('medicamentos')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('consulta_id')->nullable()->constrained('consultas')->nullOnDelete();

            $table->string('dosis');                     // 500 mg
            $table->string('via');                       // Oral, IV, IM
            $table->dateTime('administrado_en');         // fecha y hora de administración

            $table->boolean('reaccion_adversa')->default(false);
            $table->text('observaciones')->nullable();

            $table->timestamps();

            $table->index(['paciente_id', 'administrado_en']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('administraciones_medicamento');
    }
};