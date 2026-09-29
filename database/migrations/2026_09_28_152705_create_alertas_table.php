<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alertas', function (Blueprint $table) {
            $table->id();

            $table->string('tipo');               // inventario, paciente, operacion, seguridad
            $table->string('categoria');          // stock_bajo, caducidad_proxima, etc.
            $table->enum('nivel', ['info', 'advertencia', 'critico'])->default('info');

            $table->string('titulo');
            $table->text('mensaje');
            $table->json('datos')->nullable();    // datos específicos de la alerta

            // Referencia opcional al objeto que disparó la alerta
            $table->string('referencia_tipo')->nullable();
            $table->unsignedBigInteger('referencia_id')->nullable();

            // Gestión
            $table->enum('estado', ['activa', 'vista', 'resuelta', 'descartada'])->default('activa');
            $table->foreignId('resuelta_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resuelta_en')->nullable();

            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // a quién va dirigida (null = todos)
            $table->timestamp('expira_en')->nullable();

            $table->timestamps();

            $table->index(['estado', 'nivel', 'created_at']);
            $table->index(['tipo', 'estado']);
            $table->index(['referencia_tipo', 'referencia_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alertas');
    }
};