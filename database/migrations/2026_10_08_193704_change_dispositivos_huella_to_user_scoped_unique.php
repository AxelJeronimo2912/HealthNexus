<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dispositivos', function (Blueprint $table) {
            $table->unique(['user_id', 'huella']);
            $table->dropUnique('dispositivos_huella_unique');
        });
    }

    public function down(): void
    {
        Schema::table('dispositivos', function (Blueprint $table) {
            $table->unique('huella');
            $table->dropUnique('dispositivos_user_id_huella_unique');
        });
    }
};
