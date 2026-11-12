<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('archivos_estudio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitud_estudio_id')->constrained('solicitudes_estudio')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('nombre_original');
            $table->string('ruta');                  // storage/app/...
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('tamano_bytes');
            $table->enum('tipo', ['pdf', 'imagen', 'otro'])->default('otro');
            $table->string('descripcion')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('archivos_estudio');
    }
};