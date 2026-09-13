<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consultas', function (Blueprint $table) {
            $table->foreignId('diagnostico_principal_id')->nullable()->after('plan')->constrained('diagnosticos')->nullOnDelete();
            $table->foreignId('diagnostico_secundario_id')->nullable()->after('diagnostico_principal_id')->constrained('diagnosticos')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('consultas', function (Blueprint $table) {
            $table->dropForeign(['diagnostico_principal_id']);
            $table->dropForeign(['diagnostico_secundario_id']);
            $table->dropColumn(['diagnostico_principal_id', 'diagnostico_secundario_id']);
        });
    }
};