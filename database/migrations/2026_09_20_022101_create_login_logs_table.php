<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('login_logs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
        $table->string('email')->nullable(); // Útil si el usuario no existe pero intentaron usarlo
        $table->string('ip_address', 45)->nullable();
        $table->text('user_agent')->nullable();
        $table->boolean('success')->default(false);
        $table->string('motivo')->nullable(); // Ej: "Credenciales incorrectas", "PIN incorrecto"
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_logs');
    }
};
