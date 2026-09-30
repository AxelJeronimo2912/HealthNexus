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
        Schema::table('cuentas', function (Blueprint $table) {
    $table->decimal('descuento_global', 12, 2)->default(0)->after('subtotal');
    $table->string('motivo_descuento', 200)->nullable()->after('descuento_global');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cuentas', function (Blueprint $table) {
            //
        });
    }
};
