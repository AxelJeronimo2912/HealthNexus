<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Modo sin conexión (fase 1: signos vitales).
 *
 * - client_uuid: identificador que genera el navegador o el teléfono; si el mismo registro llega dos veces, no se duplica.
 * - capturado_en: hora real de la toma, cuando se capturó sin conexión y se envió después.
 * - sin_conexion: marca los registros que llegaron así.
 *
 * La API móvil tiene el script equivalente (database/sin-conexion.sql); si ya se aplicó, esta migración no repite las columnas.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('signos_vitales', 'client_uuid')) {
            return;
        }

        Schema::table('signos_vitales', function (Blueprint $table) {
            $table->char('client_uuid', 36)->nullable()->unique()->after('user_id');
            $table->dateTime('capturado_en')->nullable()->after('notas');
            $table->boolean('sin_conexion')->default(false)->after('capturado_en');
        });
    }

    public function down(): void
    {
        Schema::table('signos_vitales', function (Blueprint $table) {
            $table->dropUnique(['client_uuid']);
            $table->dropColumn(['client_uuid', 'capturado_en', 'sin_conexion']);
        });
    }
};
