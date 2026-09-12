<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pacientes', function (Blueprint $table) {
            $table->id();

            // Datos personales
            $table->string('nombre');
            $table->string('apellido_paterno');
            $table->string('apellido_materno')->nullable();
            $table->date('fecha_nacimiento');
            $table->enum('sexo', ['hombre', 'mujer', 'otro'])->default('hombre');
            $table->string('estado_civil')->nullable();
            $table->string('nacionalidad')->default('MEXICANA');
            $table->string('pais_nacimiento')->nullable();
            $table->string('estado_nacimiento')->nullable();
            $table->string('curp', 18)->nullable()->unique();
            $table->string('pasaporte')->nullable();

            // Contacto
            $table->string('telefono_principal')->nullable();
            $table->string('correo_electronico')->nullable();
            $table->string('ocupacion')->nullable();
            $table->string('responsable_nombre')->nullable();

            // Salud
            $table->string('tipo_sanguineo')->nullable();
            $table->text('alergias')->nullable();
            $table->text('enfermedades_cronicas')->nullable();

            // Domicilio
            $table->foreignId('estado_id')->nullable()->constrained('estados')->nullOnDelete();
            $table->foreignId('municipio_id')->nullable()->constrained('municipios')->nullOnDelete();
            $table->string('colonia')->nullable();
            $table->string('calle')->nullable();
            $table->string('numero_exterior')->nullable();
            $table->string('numero_interior')->nullable();

            // Estado del registro
            $table->boolean('activo')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};