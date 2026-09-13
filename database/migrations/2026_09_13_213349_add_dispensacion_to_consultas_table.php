<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consultas', function (Blueprint $table) {
            $table->boolean('dispensada')->default(false)->after('estado');
            $table->timestamp('dispensada_en')->nullable()->after('dispensada');
            $table->foreignId('dispensada_por')->nullable()->after('dispensada_en')
                ->constrained('users')->nullOnDelete();
            $table->text('notas_dispensacion')->nullable()->after('dispensada_por');
        });
    }

    public function down(): void
    {
        Schema::table('consultas', function (Blueprint $table) {
            $table->dropForeign(['dispensada_por']);
            $table->dropColumn(['dispensada', 'dispensada_en', 'dispensada_por', 'notas_dispensacion']);
        });
    }
};