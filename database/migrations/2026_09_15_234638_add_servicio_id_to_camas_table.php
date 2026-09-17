<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('camas', function (Blueprint $table) {
            $table->foreignId('servicio_id')->nullable()->after('id')
                ->constrained('servicios')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('camas', function (Blueprint $table) {
            $table->dropForeign(['servicio_id']);
            $table->dropColumn('servicio_id');
        });
    }
};