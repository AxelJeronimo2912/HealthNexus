<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('servicios', function (Blueprint $table) {
            $table->decimal('precio', 10, 2)->default(0)->after('capacidad');
            $table->string('precio_descripcion', 100)->nullable()->after('precio');
        });
    }

    public function down(): void
    {
        Schema::table('servicios', function (Blueprint $table) {
            $table->dropColumn(['precio', 'precio_descripcion']);
        });
    }
};