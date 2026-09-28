<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            // Quién
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('user_nombre')->nullable();
            $table->string('user_rol')->nullable();

            // Qué acción
            $table->string('evento', 50);           // created, updated, deleted, login, etc.
            $table->string('modulo', 50);           // pacientes, consultas, medicamentos...
            $table->string('descripcion');

            // Sobre qué
            $table->string('auditable_type')->nullable();  // App\Models\Paciente
            $table->unsignedBigInteger('auditable_id')->nullable();

            // Datos
            $table->json('datos_antes')->nullable();
            $table->json('datos_despues')->nullable();
            $table->json('metadata')->nullable();           // ip, user_agent, url

            // Clasificación
            $table->enum('severidad', ['info', 'warning', 'critical'])->default('info');
            $table->boolean('es_sensible')->default(false);

            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['auditable_type', 'auditable_id']);
            $table->index(['modulo', 'evento']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};