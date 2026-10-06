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
        Schema::table('cuenta_items', function (Blueprint $table) {
    $table->decimal('descuento', 10, 2)->default(0)->after('precio_unitario');
    $table->string('motivo_descuento', 200)->nullable()->after('descuento');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cuenta_items', function (Blueprint $table) {
            //
        });
    }
};
