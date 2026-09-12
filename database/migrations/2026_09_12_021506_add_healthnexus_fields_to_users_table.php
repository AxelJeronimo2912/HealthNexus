<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nombre')->nullable()->after('name');
            $table->string('apellido_paterno')->nullable()->after('nombre');
            $table->string('apellido_materno')->nullable()->after('apellido_paterno');
            $table->string('curp', 18)->nullable()->unique()->after('apellido_materno');
            $table->date('fecha_nacimiento')->nullable()->after('curp');
            $table->string('cedula_profesional')->nullable()->after('fecha_nacimiento');
            $table->string('telefono', 20)->nullable()->after('cedula_profesional');
            $table->string('telefono_contacto', 20)->nullable()->after('telefono');
            $table->string('tipo_servicio')->nullable()->after('telefono_contacto');
            $table->string('foto_perfil')->nullable()->after('tipo_servicio');
            $table->string('firma_archivo')->nullable()->after('foto_perfil');
            $table->text('firma_canvas')->nullable()->after('firma_archivo');
            $table->boolean('activo')->default(true)->after('firma_canvas');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'nombre', 'apellido_paterno', 'apellido_materno', 'curp',
                'fecha_nacimiento', 'cedula_profesional', 'telefono',
                'telefono_contacto', 'tipo_servicio',
                'foto_perfil', 'firma_archivo', 'firma_canvas', 'activo',
            ]);
        });
    }
};