<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paciente_medico', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->foreignId('medico_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('asignado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('asignado_en');
            $table->boolean('activo')->default(true);
            $table->text('motivo')->nullable();
            $table->timestamps();

            // Un paciente solo puede tener un médico activo
            $table->unique(['paciente_id', 'activo'], 'paciente_medico_activo_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paciente_medico');
    }
};