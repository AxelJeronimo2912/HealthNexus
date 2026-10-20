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
    Schema::table('administraciones_medicamento', function (Blueprint $table) {
        // Agregamos la llave foránea que une la administración con la nota
        $table->foreignId('seguimiento_id')->nullable()->constrained('seguimientos')->nullOnDelete();
    });
}

public function down(): void
{
    Schema::table('administraciones_medicamento', function (Blueprint $table) {
        $table->dropForeign(['seguimiento_id']);
        $table->dropColumn('seguimiento_id');
    });
}
};

