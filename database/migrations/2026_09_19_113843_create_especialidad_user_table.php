<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('especialidad_user', function (Blueprint $table) {
            $table->id();

            $table->foreignId('especialidad_id')->constrained('especialidades')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->boolean('es_principal')->default(false);
            $table->string('numero_cedula_especialidad')->nullable();
            $table->date('fecha_certificacion')->nullable();
            $table->boolean('activo')->default(true);

            $table->timestamps();

            $table->unique(['especialidad_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('especialidad_user');
    }
};