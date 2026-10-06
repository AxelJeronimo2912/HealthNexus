<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cuenta_id')->constrained('cuentas')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('folio', 30)->unique();            // PAG-20260929-001
            $table->decimal('monto', 12, 2);
            $table->string('metodo', 30);                     // efectivo | tarjeta | transferencia | otro
            $table->string('referencia', 100)->nullable();    // últimos 4 dígitos, no. autorización, etc.

            $table->string('estado', 20)->default('aplicado'); // aplicado | cancelado
            $table->text('motivo_cancelacion')->nullable();
            $table->timestamp('cancelado_en')->nullable();
            $table->foreignId('cancelado_por')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamp('pagado_en')->useCurrent();
            $table->text('notas')->nullable();
            $table->timestamps();

            $table->index(['cuenta_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
