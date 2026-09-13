<?php

namespace Database\Seeders;

use App\Models\Diagnostico;
use Illuminate\Database\Seeder;

class DiagnosticoSeeder extends Seeder
{
    public function run(): void
    {
        $diagnosticos = [
            // Infecciosas y parasitarias
            ['A09', 'Diarrea y gastroenteritis de presunto origen infeccioso', 'Infecciosas'],
            ['A15', 'Tuberculosis respiratoria', 'Infecciosas'],
            ['A46', 'Erisipela', 'Infecciosas'],
            ['A90', 'Dengue clásico', 'Infecciosas'],
            ['B01', 'Varicela', 'Infecciosas'],
            ['B34', 'Infección viral no especificada', 'Infecciosas'],
            ['B35', 'Dermatofitosis', 'Infecciosas'],

            // Neoplasias
            ['C18', 'Tumor maligno del colon', 'Neoplasias'],
            ['C50', 'Tumor maligno de la mama', 'Neoplasias'],
            ['C61', 'Tumor maligno de la próstata', 'Neoplasias'],
            ['D25', 'Leiomioma del útero', 'Neoplasias'],

            // Endocrinas y metabólicas
            ['E03', 'Hipotiroidismo', 'Endocrinas'],
            ['E05', 'Tirotoxicosis (hipertiroidismo)', 'Endocrinas'],
            ['E10', 'Diabetes mellitus tipo 1', 'Endocrinas'],
            ['E11', 'Diabetes mellitus tipo 2', 'Endocrinas'],
            ['E66', 'Obesidad', 'Endocrinas'],
            ['E78', 'Trastornos del metabolismo de lipoproteínas', 'Endocrinas'],

            // Sangre
            ['D50', 'Anemia por deficiencia de hierro', 'Sangre'],
            ['D64', 'Anemia no especificada', 'Sangre'],

            // Mentales
            ['F32', 'Episodio depresivo', 'Mentales'],
            ['F41', 'Trastorno de ansiedad', 'Mentales'],
            ['F51', 'Trastornos del sueño no orgánicos', 'Mentales'],

            // Nerviosas
            ['G40', 'Epilepsia', 'Nerviosas'],
            ['G43', 'Migraña', 'Nerviosas'],
            ['G44', 'Otros síndromes de cefalea', 'Nerviosas'],

            // Ojos y oídos
            ['H10', 'Conjuntivitis', 'Ojos'],
            ['H52', 'Trastornos de la refracción', 'Ojos'],
            ['H66', 'Otitis media supurativa', 'Oídos'],
            ['H81', 'Trastornos de la función vestibular (vértigo)', 'Oídos'],

            // Cardiovasculares
            ['I10', 'Hipertensión esencial (primaria)', 'Cardiovasculares'],
            ['I20', 'Angina de pecho', 'Cardiovasculares'],
            ['I21', 'Infarto agudo de miocardio', 'Cardiovasculares'],
            ['I25', 'Enfermedad isquémica crónica del corazón', 'Cardiovasculares'],
            ['I48', 'Fibrilación y aleteo auricular', 'Cardiovasculares'],
            ['I50', 'Insuficiencia cardíaca', 'Cardiovasculares'],
            ['I64', 'Accidente vascular encefálico', 'Cardiovasculares'],

            // Respiratorias
            ['J00', 'Rinofaringitis aguda (resfriado común)', 'Respiratorias'],
            ['J01', 'Sinusitis aguda', 'Respiratorias'],
            ['J02', 'Faringitis aguda', 'Respiratorias'],
            ['J03', 'Amigdalitis aguda', 'Respiratorias'],
            ['J04', 'Laringitis y traqueítis aguda', 'Respiratorias'],
            ['J06', 'Infección aguda de vías respiratorias superiores', 'Respiratorias'],
            ['J18', 'Neumonía', 'Respiratorias'],
            ['J20', 'Bronquitis aguda', 'Respiratorias'],
            ['J30', 'Rinitis alérgica', 'Respiratorias'],
            ['J44', 'Enfermedad pulmonar obstructiva crónica (EPOC)', 'Respiratorias'],
            ['J45', 'Asma', 'Respiratorias'],

            // Digestivas
            ['K21', 'Enfermedad por reflujo gastroesofágico', 'Digestivas'],
            ['K25', 'Úlcera gástrica', 'Digestivas'],
            ['K29', 'Gastritis y duodenitis', 'Digestivas'],
            ['K30', 'Dispepsia funcional', 'Digestivas'],
            ['K35', 'Apendicitis aguda', 'Digestivas'],
            ['K40', 'Hernia inguinal', 'Digestivas'],
            ['K58', 'Síndrome de intestino irritable', 'Digestivas'],
            ['K59', 'Estreñimiento', 'Digestivas'],
            ['K76', 'Enfermedades del hígado', 'Digestivas'],

            // Piel
            ['L01', 'Impétigo', 'Piel'],
            ['L02', 'Absceso cutáneo', 'Piel'],
            ['L20', 'Dermatitis atópica', 'Piel'],
            ['L23', 'Dermatitis alérgica de contacto', 'Piel'],
            ['L30', 'Dermatitis no especificada', 'Piel'],
            ['L50', 'Urticaria', 'Piel'],
            ['L60', 'Trastornos de las uñas', 'Piel'],

            // Musculoesqueléticas
            ['M15', 'Poliartrosis', 'Musculoesqueléticas'],
            ['M17', 'Gonartrosis', 'Musculoesqueléticas'],
            ['M25', 'Dolor articular', 'Musculoesqueléticas'],
            ['M47', 'Espondilosis', 'Musculoesqueléticas'],
            ['M54', 'Dorsalgia', 'Musculoesqueléticas'],
            ['M75', 'Lesiones del hombro', 'Musculoesqueléticas'],
            ['M79', 'Otros trastornos de tejidos blandos', 'Musculoesqueléticas'],

            // Genitourinarias
            ['N10', 'Nefritis tubulointersticial aguda (pielonefritis)', 'Genitourinarias'],
            ['N30', 'Cistitis', 'Genitourinarias'],
            ['N39', 'Otros trastornos del sistema urinario', 'Genitourinarias'],
            ['N40', 'Hiperplasia de la próstata', 'Genitourinarias'],
            ['N80', 'Endometriosis', 'Genitourinarias'],
            ['N91', 'Menstruación ausente, escasa o rara', 'Genitourinarias'],
            ['N94', 'Dolor pélvico y otros síntomas asociados', 'Genitourinarias'],

            // Embarazo y parto
            ['O20', 'Hemorragia del embarazo temprano', 'Embarazo'],
            ['O24', 'Diabetes mellitus en el embarazo', 'Embarazo'],
            ['O26', 'Atención de la madre por otras complicaciones', 'Embarazo'],
            ['O80', 'Parto único espontáneo', 'Embarazo'],

            // Perinatales
            ['P07', 'Trastornos relacionados con prematurez', 'Perinatales'],
            ['P59', 'Ictericia neonatal', 'Perinatales'],

            // Congénitas
            ['Q21', 'Malformaciones congénitas cardíacas', 'Congénitas'],

            // Síntomas y signos
            ['R05', 'Tos', 'Síntomas'],
            ['R07', 'Dolor de garganta', 'Síntomas'],
            ['R10', 'Dolor abdominal', 'Síntomas'],
            ['R11', 'Náusea y vómito', 'Síntomas'],
            ['R42', 'Mareo y desvanecimiento', 'Síntomas'],
            ['R50', 'Fiebre de origen desconocido', 'Síntomas'],
            ['R51', 'Cefalea', 'Síntomas'],
            ['R53', 'Malestar y fatiga', 'Síntomas'],

            // Traumatismos
            ['S00', 'Traumatismo superficial de la cabeza', 'Traumatismos'],
            ['S06', 'Traumatismo intracraneal', 'Traumatismos'],
            ['S52', 'Fractura del antebrazo', 'Traumatismos'],
            ['S72', 'Fractura del fémur', 'Traumatismos'],
            ['S93', 'Luxación y esguince del tobillo', 'Traumatismos'],
            ['T14', 'Traumatismo no especificado', 'Traumatismos'],
        ];

        foreach ($diagnosticos as $d) {
            Diagnostico::firstOrCreate(
                ['codigo' => $d[0]],
                [
                    'nombre' => $d[1],
                    'grupo' => $d[2],
                    'activo' => true,
                ]
            );
        }

        $this->command->info(' ' . count($diagnosticos) . ' diagnósticos CIE-10 creados.');
    }
}