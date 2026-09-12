<?php

namespace Database\Seeders;

use App\Models\Estado;
use App\Models\Municipio;
use App\Models\Paciente;
use Illuminate\Database\Seeder;

class PacienteSeeder extends Seeder
{
    public function run(): void
    {
        // Cargar estados y municipios reales de la BD
        $estados = Estado::with('municipios')->get();

        if ($estados->isEmpty()) {
            $this->command->error(' No hay estados en la base de datos. Corre primero EstadosMunicipiosSeeder.');
            return;
        }

        // Datos base
        $nombresHombres = [
            'Juan', 'Carlos', 'Luis', 'Miguel', 'José', 'Pedro', 'Diego', 'Fernando',
            'Roberto', 'Alejandro', 'Ricardo', 'Jorge', 'Raúl', 'Arturo', 'Manuel',
            'Eduardo', 'Antonio', 'Francisco', 'Sergio', 'Enrique', 'Alberto',
            'Javier', 'Rubén', 'Óscar', 'Héctor', 'Emilio', 'Rodrigo', 'Andrés',
        ];
        $nombresMujeres = [
            'María', 'Ana', 'Laura', 'Patricia', 'Sofía', 'Gabriela', 'Daniela',
            'Alejandra', 'Verónica', 'Claudia', 'Adriana', 'Mónica', 'Cristina',
            'Fernanda', 'Lucía', 'Isabel', 'Rosa', 'Elena', 'Silvia', 'Beatriz',
            'Carolina', 'Valeria', 'Regina', 'Ximena', 'Renata', 'Camila',
        ];
        $apellidos = [
            'García', 'Hernández', 'Martínez', 'López', 'González', 'Pérez',
            'Rodríguez', 'Sánchez', 'Ramírez', 'Cruz', 'Flores', 'Gómez',
            'Morales', 'Vázquez', 'Jiménez', 'Reyes', 'Torres', 'Díaz',
            'Ruiz', 'Mendoza', 'Aguilar', 'Ortiz', 'Castillo', 'Chávez',
            'Ramos', 'Núñez', 'Guerrero', 'Vargas', 'Romero', 'Herrera',
            'Silva', 'Rojas', 'Medina', 'Suárez', 'Salazar', 'Delgado',
        ];
        $ocupaciones = [
            'Empleado', 'Comerciante', 'Estudiante', 'Hogar', 'Docente',
            'Ingeniero', 'Abogado', 'Contador', 'Chofer', 'Obrero',
            'Enfermero', 'Vendedor', 'Agricultor', 'Policía', 'Pensionado',
            'Desempleado', 'Otro',
        ];
        $estadosCiviles = ['Soltero', 'Casado', 'Divorciado', 'Viudo', 'Unión Libre'];
        $tiposSanguineos = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        $alergiasComunes = [
            'Penicilina', 'Sulfas', 'Aspirina', 'Ibuprofeno', 'Mariscos',
            'Cacahuates', 'Lácteos', 'Polvo', 'Polen', 'Picadura de abeja',
            'Látex', 'Ninguna conocida',
        ];
        $enfermedadesCronicas = [
            'Diabetes tipo 2', 'Hipertensión arterial', 'Asma', 'EPOC',
            'Artritis reumatoide', 'Hipotiroidismo', 'Epilepsia',
            'Insuficiencia renal crónica', 'Cardiopatía isquémica',
            'Ninguna',
        ];
        $calles = [
            'Av. Juárez', 'Calle Hidalgo', 'Av. Reforma', 'Calle Morelos',
            'Av. Insurgentes', 'Calle Zaragoza', 'Av. Revolución',
            'Calle Allende', 'Av. Constitución', 'Calle 5 de Mayo',
            'Av. Independencia', 'Calle Guerrero', 'Av. Universidad',
            'Calle Matamoros', 'Av. López Mateos',
        ];
        $colonias = [
            'Centro', 'Las Flores', 'El Mirador', 'Lomas Verdes',
            'San José', 'La Esperanza', 'Villa Nueva', 'Los Pinos',
            'Reforma', 'Del Valle', 'Guadalupe', 'Santa María',
        ];

        $total = 200;
        $contadorMexicanos = 0;
        $contadorExtranjeros = 0;
        $contadorHombres = 0;
        $contadorMujeres = 0;

        for ($i = 1; $i <= $total; $i++) {
            // 90% mexicanos, 10% extranjeros
            $esMexicano = rand(1, 100) <= 90;

            // Sexo
            $esHombre = rand(0, 1) === 1;
            $esHombre ? $contadorHombres++ : $contadorMujeres++;

            $nombre = $esHombre
                ? $nombresHombres[array_rand($nombresHombres)]
                : $nombresMujeres[array_rand($nombresMujeres)];
            $apellidoPaterno = $apellidos[array_rand($apellidos)];
            $apellidoMaterno = $apellidos[array_rand($apellidos)];

            // Edad aleatoria entre 0 y 95 años
            $edad = rand(0, 95);
            $fechaNacimiento = now()
                ->subYears($edad)
                ->subDays(rand(0, 364))
                ->format('Y-m-d');

            // Estado y municipio reales
            $estado = $estados->random();
            $municipio = $estado->municipios->isNotEmpty()
                ? $estado->municipios->random()
                : null;

            // Datos según nacionalidad
            $nacionalidad = $esMexicano ? 'MEXICANA' : 'EXTRANJERA';
            $estadoNacimiento = $esMexicano ? $estado->nombre : null;
            $paisNacimiento = $esMexicano ? null : $this->paisAleatorio();

            // CURP para mexicanos, pasaporte para extranjeros
            $curp = $esMexicano
                ? $this->generarCurp(
                    $nombre,
                    $apellidoPaterno,
                    $apellidoMaterno,
                    $fechaNacimiento,
                    $esHombre ? 'H' : 'M',
                    $estado->nombre
                )
                : null;

            $pasaporte = !$esMexicano
                ? strtoupper(chr(rand(65, 90)) . rand(1000000, 9999999))
                : null;

            // Email
            $email = strtolower(
                $this->limpiar($nombre) . '.' .
                $this->limpiar($apellidoPaterno) . $i . '@example.com'
            );

            // Teléfono
            $telefono = '55' . str_pad((string) rand(0, 99999999), 8, '0', STR_PAD_LEFT);

            // Alergias: 70% ninguna, 30% con alguna
            $alergias = rand(1, 100) <= 70
                ? 'Ninguna conocida'
                : $alergiasComunes[array_rand($alergiasComunes)];

            // Enfermedades crónicas
            $enfermedad = rand(1, 100) <= 60
                ? 'Ninguna'
                : $enfermedadesCronicas[array_rand($enfermedadesCronicas)];

            Paciente::create([
                // Identidad
                'nombre' => $nombre,
                'apellido_paterno' => $apellidoPaterno,
                'apellido_materno' => $apellidoMaterno,
                'fecha_nacimiento' => $fechaNacimiento,
                'sexo' => $esHombre ? 'hombre' : 'mujer',
                'estado_civil' => $estadosCiviles[array_rand($estadosCiviles)],
                'nacionalidad' => $nacionalidad,
                'pais_nacimiento' => $paisNacimiento,
                'estado_nacimiento' => $estadoNacimiento,
                'curp' => $curp,
                'pasaporte' => $pasaporte,

                // Contacto
                'telefono_principal' => $telefono,
                'correo_electronico' => $email,
                'ocupacion' => $ocupaciones[array_rand($ocupaciones)],
                'responsable_nombre' => $edad < 18
                    ? 'Padre/Madre de ' . $nombre
                    : null,

                // Salud
                'tipo_sanguineo' => $tiposSanguineos[array_rand($tiposSanguineos)],
                'alergias' => $alergias,
                'enfermedades_cronicas' => $enfermedad,

                // Domicilio
                'estado_id' => $estado->id,
                'municipio_id' => $municipio?->id,
                'colonia' => $colonias[array_rand($colonias)],
                'calle' => $calles[array_rand($calles)],
                'numero_exterior' => (string) rand(1, 999),
                'numero_interior' => rand(0, 1) ? (string) rand(1, 50) : null,

                'activo' => true,
            ]);

            if ($esMexicano) $contadorMexicanos++;
            else $contadorExtranjeros++;
        }

        $this->command->info(' ' . $total . ' pacientes creados:');
        $this->command->line("   Mexicanos:    {$contadorMexicanos}");
        $this->command->line("   Extranjeros:  {$contadorExtranjeros}");
        $this->command->line("   Hombres:      {$contadorHombres}");
        $this->command->line("   Mujeres:      {$contadorMujeres}");
    }

    /**
     * Genera una CURP simplificada (18 caracteres).
     */
   private function generarCurp(
    string $nombre,
    string $apellidoPaterno,
    string $apellidoMaterno,
    string $fechaNacimiento,
    string $sexo,
    string $estado
): string {
    $iniciales = strtoupper(
        substr($apellidoPaterno, 0, 1) .
        $this->primeraVocal($apellidoPaterno) .
        substr($apellidoMaterno, 0, 1) .
        substr($nombre, 0, 1)
    );

    $fecha = str_replace('-', '', $fechaNacimiento);
    $yy = substr($fecha, 2, 2);
    $mm = substr($fecha, 4, 2);
    $dd = substr($fecha, 6, 2);

    $estadoClave = $this->claveEstado($estado);
    $consonantes = strtoupper(
        $this->primeraConsonante($apellidoPaterno) .
        $this->primeraConsonante($apellidoMaterno) .
        $this->primeraConsonante($nombre)
    );

    // Homoclave: SOLO 1 carácter (dígito) + 1 dígito verificador = 18 total
    $homoclave = rand(0, 9);
    $digito = rand(0, 9);

    return $iniciales . $yy . $mm . $dd . $sexo . $estadoClave . $consonantes . $homoclave . $digito;
}

    private function limpiar(string $texto): string
    {
        $texto = strtolower($texto);
        $texto = str_replace(
            ['á', 'é', 'í', 'ó', 'ú', 'ñ', 'ü'],
            ['a', 'e', 'i', 'o', 'u', 'n', 'u'],
            $texto
        );
        return preg_replace('/[^a-z]/', '', $texto);
    }

    private function primeraVocal(string $palabra): string
    {
        $palabra = strtoupper($palabra);
        preg_match('/[AEIOU]/', substr($palabra, 1), $matches);
        return $matches[0] ?? 'X';
    }

    private function primeraConsonante(string $palabra): string
    {
        $palabra = strtoupper($palabra);
        preg_match('/[BCDFGHJKLMNPQRSTVWXYZ]/', substr($palabra, 1), $matches);
        return $matches[0] ?? 'X';
    }

    private function claveEstado(string $estado): string
    {
        $mapa = [
            'Aguascalientes' => 'AS',
            'Baja California' => 'BC',
            'Baja California Sur' => 'BS',
            'Campeche' => 'CC',
            'Chiapas' => 'CS',
            'Chihuahua' => 'CH',
            'Ciudad de México' => 'DF',
            'Coahuila' => 'CL',
            'Colima' => 'CM',
            'Durango' => 'DG',
            'Estado de México' => 'MC',
            'Guanajuato' => 'GT',
            'Guerrero' => 'GR',
            'Hidalgo' => 'HG',
            'Jalisco' => 'JC',
            'Michoacán' => 'MN',
            'Morelos' => 'MS',
            'Nayarit' => 'NT',
            'Nuevo León' => 'NL',
            'Oaxaca' => 'OC',
            'Puebla' => 'PL',
            'Querétaro' => 'QT',
            'Quintana Roo' => 'QR',
            'San Luis Potosí' => 'SP',
            'Sinaloa' => 'SL',
            'Sonora' => 'SR',
            'Tabasco' => 'TC',
            'Tamaulipas' => 'TS',
            'Tlaxcala' => 'TL',
            'Veracruz' => 'VZ',
            'Yucatán' => 'YN',
            'Zacatecas' => 'ZS',
        ];
        return $mapa[$estado] ?? 'NE';
    }

    private function paisAleatorio(): string
    {
        $paises = [
            'Estados Unidos', 'Canadá', 'Guatemala', 'Belice', 'Honduras',
            'El Salvador', 'Costa Rica', 'Panamá', 'Colombia', 'Venezuela',
            'Argentina', 'Chile', 'Perú', 'Ecuador', 'España', 'Francia',
            'Italia', 'Alemania', 'China', 'Japón', 'Corea del Sur',
            'Brasil', 'Cuba', 'República Dominicana', 'Haití',
        ];
        return $paises[array_rand($paises)];
    }
}