<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_conversaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('titulo')->nullable();
            $table->timestamp('ultimo_mensaje_en')->nullable();
            $table->integer('total_mensajes')->default(0);
            $table->timestamps();

            $table->index(['user_id', 'ultimo_mensaje_en']);
        });

        Schema::create('chat_mensajes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversacion_id')->constrained('chat_conversaciones')->cascadeOnDelete();
            $table->enum('rol', ['user', 'assistant', 'system', 'tool']);
            $table->text('contenido')->nullable();
            $table->json('tool_calls')->nullable();
            $table->string('tool_call_id')->nullable();
            $table->timestamps();

            $table->index(['conversacion_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_mensajes');
        Schema::dropIfExists('chat_conversaciones');
    }
};