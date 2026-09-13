<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diagnosticos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();  // CIE-10: A09, E11, I10, etc.
            $table->string('nombre');                // Descripción
            $table->string('grupo')->nullable();     // Categoría: Infecciosas, Endocrinas, etc.
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index('nombre');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diagnosticos');
    }
};