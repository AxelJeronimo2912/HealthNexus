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

            // Huella única del dispositivo (hash)
            $table->string('huella', 64)->unique();

            // Datos del dispositivo
            $table->string('nombre')->nullable();            
            $table->string('tipo')->nullable();              
            $table->string('sistema_operativo')->nullable();  
            $table->string('navegador')->nullable();         
            $table->string('user_agent', 500)->nullable();    

            // Datos de red
            $table->string('ip_registro', 45)->nullable();
            $table->string('ip_ultimo_acceso', 45)->nullable();
            $table->string('ciudad')->nullable();
            $table->string('pais')->nullable();

            // Estado
            $table->boolean('confiable')->default(false);
            $table->boolean('activo')->default(true);
            $table->timestamp('aprobado_en')->nullable();
            $table->foreignId('aprobado_por')->nullable()->constrained('users')->nullOnDelete();

            // Uso
            $table->timestamp('ultimo_acceso')->nullable();
            $table->integer('total_accesos')->default(0);

            $table->timestamps();

            $table->index(['user_id', 'activo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispositivos');
    }
};