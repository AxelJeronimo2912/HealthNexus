<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dispositivos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->string('huella')->index();
            $table->string('nombre')->nullable();
            $table->string('tipo')->nullable();
            $table->string('sistema_operativo')->nullable();
            $table->string('navegador')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('ip_registro', 45)->nullable();
            $table->string('ip_ultimo_acceso', 45)->nullable();
            $table->string('ciudad')->nullable();
            $table->string('pais')->nullable();

            $table->boolean('confiable')->default(false);
            $table->boolean('activo')->default(true);
            $table->dateTime('aprobado_en')->nullable();
            $table->foreignId('aprobado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('ultimo_acceso')->nullable();
            $table->unsignedInteger('total_accesos')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispositivos');
    }
};