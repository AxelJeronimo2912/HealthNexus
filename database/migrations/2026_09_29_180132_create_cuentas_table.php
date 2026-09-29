<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuentas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->string('folio', 30)->unique();     // CTA-20260929-001
            $table->string('estado', 20)->default('abierta'); // abierta | cerrada | cancelada
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('pagado', 12, 2)->default(0);
            $table->decimal('saldo', 12, 2)->default(0);
            $table->timestamp('cerrada_en')->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();

            $table->index(['paciente_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuentas');
    }
};