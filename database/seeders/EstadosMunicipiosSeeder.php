<?php

namespace Database\Seeders;

use App\Models\Estado;
use App\Models\Municipio;
use Illuminate\Database\Seeder;

class EstadosMunicipiosSeeder extends Seeder
{
    public function run(): void
    {
        $datos = [
            'Aguascalientes' => ['Aguascalientes', 'Asientos', 'Calvillo', 'Jesús María', 'Pabellón de Arteaga', 'Rincón de Romos', 'San Francisco de los Romo'],
            'Baja California' => ['Ensenada', 'Mexicali', 'Playas de Rosarito', 'Tecate', 'Tijuana'],
            'Baja California Sur' => ['Comondú', 'La Paz', 'Loreto', 'Los Cabos', 'Mulegé'],
            'Campeche' => ['Calkiní', 'Campeche', 'Carmen', 'Champotón', 'Escárcega', 'Hopelchén', 'Palizada', 'Tenabo'],
            'Chiapas' => ['Comitán de Domínguez', 'Chiapa de Corzo', 'Pichucalco', 'San Cristóbal de las Casas', 'Tapachula', 'Tuxtla Gutiérrez'],
            'Chihuahua' => ['Chihuahua', 'Ciudad Juárez', 'Delicias', 'Cuauhtémoc', 'Hidalgo del Parral'],
            'Ciudad de México' => ['Álvaro Obregón', 'Azcapotzalco', 'Benito Juárez', 'Coyoacán', 'Cuajimalpa', 'Cuauhtémoc', 'Gustavo A. Madero', 'Iztacalco', 'Iztapalapa', 'Magdalena Contreras', 'Miguel Hidalgo', 'Milpa Alta', 'Tláhuac', 'Tlalpan', 'Venustiano Carranza', 'Xochimilco'],
            'Coahuila' => ['Saltillo', 'Torreón', 'Monclova', 'Piedras Negras', 'Acuña', 'Matamoros'],
            'Colima' => ['Colima', 'Manzanillo', 'Tecomán', 'Villa de Álvarez', 'Armería', 'Comala'],
            'Durango' => ['Durango', 'Gómez Palacio', 'Lerdo', 'Pueblo Nuevo', 'Santiago Papasquiaro'],
            'Estado de México' => ['Ecatepec', 'Nezahualcóyotl', 'Toluca', 'Naucalpan', 'Tlalnepantla', 'Chimalhuacán', 'Ixtapaluca', 'Cuautitlán Izcalli'],
            'Guanajuato' => ['León', 'Irapuato', 'Celaya', 'Salamanca', 'Guanajuato', 'Pénjamo', 'Acámbaro'],
            'Guerrero' => ['Acapulco', 'Chilpancingo', 'Iguala', 'Taxco', 'Zihuatanejo'],
            'Hidalgo' => ['Pachuca', 'Tulancingo', 'Tula', 'Huejutla', 'Ixmiquilpan'],
            'Jalisco' => ['Guadalajara', 'Zapopan', 'Tlaquepaque', 'Tonalá', 'Tlajomulco', 'Puerto Vallarta', 'Lagos de Moreno'],
            'Michoacán' => ['Morelia', 'Uruapan', 'Zamora', 'Lázaro Cárdenas', 'Apatzingán', 'Pátzcuaro'],
            'Morelos' => ['Cuernavaca', 'Jiutepec', 'Cuautla', 'Temixco', 'Yautepec'],
            'Nayarit' => ['Tepic', 'Bahía de Banderas', 'Santiago Ixcuintla', 'Compostela', 'Acaponeta'],
            'Nuevo León' => ['Monterrey', 'Guadalupe', 'San Nicolás', 'Apodaca', 'Escobedo', 'Santa Catarina', 'San Pedro Garza García'],
            'Oaxaca' => ['Oaxaca', 'San Juan Bautista Tuxtepec', 'Salina Cruz', 'Juchitán', 'Santa Lucía del Camino'],
            'Puebla' => ['Puebla', 'Tehuacán', 'San Martín Texmelucan', 'Atlixco', 'San Pedro Cholula', 'Amozoc'],
            'Querétaro' => ['Querétaro', 'San Juan del Río', 'Corregidora', 'El Marqués', 'Tequisquiapan'],
            'Quintana Roo' => ['Cancún', 'Chetumal', 'Playa del Carmen', 'Cozumel', 'Tulum', 'Felipe Carrillo Puerto'],
            'San Luis Potosí' => ['San Luis Potosí', 'Soledad', 'Ciudad Valles', 'Matehuala', 'Rioverde'],
            'Sinaloa' => ['Culiacán', 'Mazatlán', 'Los Mochis', 'Guasave', 'Navolato'],
            'Sonora' => ['Hermosillo', 'Cajeme', 'Nogales', 'San Luis Río Colorado', 'Guaymas'],
            'Tabasco' => ['Villahermosa', 'Cárdenas', 'Comalcalco', 'Huimanguillo', 'Macuspana'],
            'Tamaulipas' => ['Reynosa', 'Matamoros', 'Nuevo Laredo', 'Tampico', 'Ciudad Victoria', 'Altamira'],
            'Tlaxcala' => ['Tlaxcala', 'Apizaco', 'Huamantla', 'San Pablo del Monte', 'Chiautempan'],
            'Veracruz' => ['Veracruz', 'Xalapa', 'Coatzacoalcos', 'Córdoba', 'Orizaba', 'Poza Rica', 'Boca del Río'],
            'Yucatán' => ['Mérida', 'Valladolid', 'Tizimín', 'Progreso', 'Kanasín'],
            'Zacatecas' => ['Zacatecas', 'Guadalupe', 'Fresnillo', 'Jerez', 'Río Grande'],
        ];

        foreach ($datos as $estadoNombre => $municipios) {
            $estado = Estado::firstOrCreate(
                ['nombre' => $estadoNombre],
                ['abreviatura' => null]
            );

            foreach ($municipios as $municipioNombre) {
                Municipio::firstOrCreate([
                    'estado_id' => $estado->id,
                    'nombre' => $municipioNombre,
                ]);
            }
        }
    }
}