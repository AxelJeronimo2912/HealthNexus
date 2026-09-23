<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('citas', function (Blueprint $table) {
            $table->foreignId('especialidad_id')->nullable()->after('medico_id')
                ->constrained('especialidades')->nullOnDelete();
        });

        Schema::table('consultas', function (Blueprint $table) {
            $table->foreignId('especialidad_id')->nullable()->after('medico_id')
                ->constrained('especialidades')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('citas', function (Blueprint $table) {
            $table->dropForeign(['especialidad_id']);
            $table->dropColumn('especialidad_id');
        });

        Schema::table('consultas', function (Blueprint $table) {
            $table->dropForeign(['especialidad_id']);
            $table->dropColumn('especialidad_id');
        });
    }
};