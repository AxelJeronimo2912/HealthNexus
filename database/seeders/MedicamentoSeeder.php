<?php

namespace Database\Seeders;

use App\Models\Medicamento;
use Illuminate\Database\Seeder;

class MedicamentoSeeder extends Seeder
{
    public function run(): void
    {
        $medicamentos = [
            // Analgésicos / Antiinflamatorios
            ['Paracetamol', 'Paracetamol', 'Tableta', '500 mg', 'Oral', 'Analgésico'],
            ['Paracetamol', 'Paracetamol', 'Jarabe', '120 mg/5 ml', 'Oral', 'Analgésico'],
            ['Ketorolaco', 'Ketorolaco trometamina', 'Tableta', '10 mg', 'Oral', 'Analgésico'],
            ['Ketorolaco', 'Ketorolaco trometamina', 'Solución inyectable', '30 mg/ml', 'Intravenosa', 'Analgésico'],
            ['Ibuprofeno', 'Ibuprofeno', 'Tableta', '400 mg', 'Oral', 'Antiinflamatorio'],
            ['Ibuprofeno', 'Ibuprofeno', 'Suspensión', '100 mg/5 ml', 'Oral', 'Antiinflamatorio'],
            ['Naproxeno', 'Naproxeno sódico', 'Tableta', '550 mg', 'Oral', 'Antiinflamatorio'],
            ['Diclofenaco', 'Diclofenaco sódico', 'Tableta', '100 mg', 'Oral', 'Antiinflamatorio'],
            ['Diclofenaco', 'Diclofenaco sódico', 'Solución inyectable', '75 mg/3 ml', 'Intramuscular', 'Antiinflamatorio'],
            ['Metamizol', 'Metamizol sódico', 'Solución inyectable', '1 g/2 ml', 'Intravenosa', 'Analgésico'],
            ['Tramadol', 'Tramadol clorhidrato', 'Cápsula', '50 mg', 'Oral', 'Analgésico opioide'],
            ['Tramadol', 'Tramadol clorhidrato', 'Solución inyectable', '100 mg/2 ml', 'Intravenosa', 'Analgésico opioide'],
            ['Morfina', 'Morfina sulfato', 'Solución inyectable', '10 mg/ml', 'Intravenosa', 'Analgésico opioide'],
            ['Fentanilo', 'Fentanilo citrato', 'Solución inyectable', '0.05 mg/ml', 'Intravenosa', 'Analgésico opioide'],

            // Antibióticos
            ['Amoxicilina', 'Amoxicilina trihidratada', 'Cápsula', '500 mg', 'Oral', 'Antibiótico'],
            ['Amoxicilina + Ácido Clavulánico', 'Amoxicilina + Clavulanato', 'Tableta', '875/125 mg', 'Oral', 'Antibiótico'],
            ['Amoxicilina', 'Amoxicilina', 'Suspensión', '250 mg/5 ml', 'Oral', 'Antibiótico'],
            ['Azitromicina', 'Azitromicina dihidrato', 'Tableta', '500 mg', 'Oral', 'Antibiótico'],
            ['Azitromicina', 'Azitromicina dihidrato', 'Suspensión', '200 mg/5 ml', 'Oral', 'Antibiótico'],
            ['Ceftriaxona', 'Ceftriaxona sódica', 'Solución inyectable', '1 g', 'Intravenosa', 'Antibiótico'],
            ['Cefalexina', 'Cefalexina monohidrato', 'Cápsula', '500 mg', 'Oral', 'Antibiótico'],
            ['Ciprofloxacino', 'Ciprofloxacino', 'Tableta', '500 mg', 'Oral', 'Antibiótico'],
            ['Ciprofloxacino', 'Ciprofloxacino', 'Solución inyectable', '200 mg/100 ml', 'Intravenosa', 'Antibiótico'],
            ['Levofloxacino', 'Levofloxacino', 'Tableta', '500 mg', 'Oral', 'Antibiótico'],
            ['Claritromicina', 'Claritromicina', 'Tableta', '500 mg', 'Oral', 'Antibiótico'],
            ['Metronidazol', 'Metronidazol', 'Tableta', '500 mg', 'Oral', 'Antibiótico'],
            ['Metronidazol', 'Metronidazol', 'Solución inyectable', '500 mg/100 ml', 'Intravenosa', 'Antibiótico'],
            ['Vancomicina', 'Vancomicina clorhidrato', 'Solución inyectable', '500 mg', 'Intravenosa', 'Antibiótico'],
            ['Gentamicina', 'Gentamicina sulfato', 'Solución inyectable', '80 mg/2 ml', 'Intramuscular', 'Antibiótico'],
            ['Penicilina G', 'Penicilina G sódica', 'Solución inyectable', '5,000,000 UI', 'Intravenosa', 'Antibiótico'],
            ['Ampicilina', 'Ampicilina sódica', 'Solución inyectable', '1 g', 'Intravenosa', 'Antibiótico'],
            ['Clindamicina', 'Clindamicina', 'Cápsula', '300 mg', 'Oral', 'Antibiótico'],
            ['Trimetoprim + Sulfametoxazol', 'Trimetoprim + Sulfametoxazol', 'Tableta', '160/800 mg', 'Oral', 'Antibiótico'],

            // Antihipertensivos / Cardiovasculares
            ['Enalapril', 'Enalapril maleato', 'Tableta', '10 mg', 'Oral', 'Antihipertensivo'],
            ['Losartán', 'Losartán potásico', 'Tableta', '50 mg', 'Oral', 'Antihipertensivo'],
            ['Amlodipino', 'Amlodipino besilato', 'Tableta', '5 mg', 'Oral', 'Antihipertensivo'],
            ['Metoprolol', 'Metoprolol tartrato', 'Tableta', '100 mg', 'Oral', 'Antihipertensivo'],
            ['Atenolol', 'Atenolol', 'Tableta', '50 mg', 'Oral', 'Antihipertensivo'],
            ['Hidroclorotiazida', 'Hidroclorotiazida', 'Tableta', '25 mg', 'Oral', 'Diurético'],
            ['Furosemida', 'Furosemida', 'Tableta', '40 mg', 'Oral', 'Diurético'],
            ['Furosemida', 'Furosemida', 'Solución inyectable', '20 mg/2 ml', 'Intravenosa', 'Diurético'],
            ['Espironolactona', 'Espironolactona', 'Tableta', '100 mg', 'Oral', 'Diurético'],
            ['Digoxina', 'Digoxina', 'Tableta', '0.25 mg', 'Oral', 'Cardiotónico'],
            ['Nitroglicerina', 'Nitroglicerina', 'Solución inyectable', '5 mg/ml', 'Intravenosa', 'Vasodilatador'],
            ['Atorvastatina', 'Atorvastatina cálcica', 'Tableta', '20 mg', 'Oral', 'Hipolipemiante'],
            ['Simvastatina', 'Simvastatina', 'Tableta', '20 mg', 'Oral', 'Hipolipemiante'],
            ['Clopidogrel', 'Clopidogrel bisulfato', 'Tableta', '75 mg', 'Oral', 'Antiagregante'],
            ['Ácido Acetilsalicílico', 'Ácido Acetilsalicílico', 'Tableta', '100 mg', 'Oral', 'Antiagregante'],

            // Antidiabéticos
            ['Metformina', 'Metformina clorhidrato', 'Tableta', '850 mg', 'Oral', 'Antidiabético'],
            ['Metformina', 'Metformina clorhidrato', 'Tableta', '500 mg', 'Oral', 'Antidiabético'],
            ['Glibenclamida', 'Glibenclamida', 'Tableta', '5 mg', 'Oral', 'Antidiabético'],
            ['Insulina Glargina', 'Insulina glargina', 'Solución inyectable', '100 UI/ml', 'Subcutánea', 'Antidiabético'],
            ['Insulina Regular', 'Insulina humana regular', 'Solución inyectable', '100 UI/ml', 'Subcutánea', 'Antidiabético'],
            ['Sitagliptina', 'Sitagliptina', 'Tableta', '100 mg', 'Oral', 'Antidiabético'],

            // Gastrointestinales
            ['Omeprazol', 'Omeprazol', 'Cápsula', '20 mg', 'Oral', 'Inhibidor de bomba de protones'],
            ['Omeprazol', 'Omeprazol sódico', 'Solución inyectable', '40 mg', 'Intravenosa', 'Inhibidor de bomba de protones'],
            ['Pantoprazol', 'Pantoprazol sódico', 'Tableta', '40 mg', 'Oral', 'Inhibidor de bomba de protones'],
            ['Ranitidina', 'Ranitidina clorhidrato', 'Tableta', '150 mg', 'Oral', 'Antiácido'],
            ['Metoclopramida', 'Metoclopramida clorhidrato', 'Tableta', '10 mg', 'Oral', 'Procinético'],
            ['Metoclopramida', 'Metoclopramida clorhidrato', 'Solución inyectable', '10 mg/2 ml', 'Intravenosa', 'Procinético'],
            ['Ondansetrón', 'Ondansetrón clorhidrato', 'Tableta', '8 mg', 'Oral', 'Antiemético'],
            ['Ondansetrón', 'Ondansetrón clorhidrato', 'Solución inyectable', '8 mg/4 ml', 'Intravenosa', 'Antiemético'],
            ['Butilhioscina', 'Butilhioscina bromuro', 'Tableta', '10 mg', 'Oral', 'Antiespasmódico'],
            ['Butilhioscina', 'Butilhioscina bromuro', 'Solución inyectable', '20 mg/2 ml', 'Intravenosa', 'Antiespasmódico'],
            ['Loperamida', 'Loperamida clorhidrato', 'Cápsula', '2 mg', 'Oral', 'Antidiarreico'],
            ['Sales de Rehidratación Oral', 'Sales de rehidratación oral', 'Sobre', '27.9 g', 'Oral', 'Rehidratante'],

            // Respiratorios
            ['Salbutamol', 'Salbutamol sulfato', 'Inhalador', '100 mcg/dosis', 'Inhalatoria', 'Broncodilatador'],
            ['Salbutamol', 'Salbutamol sulfato', 'Jarabe', '2 mg/5 ml', 'Oral', 'Broncodilatador'],
            ['Budesonida', 'Budesonida', 'Inhalador', '200 mcg/dosis', 'Inhalatoria', 'Corticoide inhalado'],
            ['Budesonida + Formoterol', 'Budesonida + Formoterol', 'Inhalador', '160/4.5 mcg', 'Inhalatoria', 'Broncodilatador'],
            ['Ipratropio', 'Ipratropio bromuro', 'Inhalador', '20 mcg/dosis', 'Inhalatoria', 'Broncodilatador'],
            ['Montelukast', 'Montelukast sódico', 'Tableta', '10 mg', 'Oral', 'Antiasmático'],
            ['Loratadina', 'Loratadina', 'Tableta', '10 mg', 'Oral', 'Antihistamínico'],
            ['Cetirizina', 'Cetirizina diclorhidrato', 'Tableta', '10 mg', 'Oral', 'Antihistamínico'],
            ['Difenhidramina', 'Difenhidramina clorhidrato', 'Cápsula', '25 mg', 'Oral', 'Antihistamínico'],
            ['Difenhidramina', 'Difenhidramina clorhidrato', 'Solución inyectable', '10 mg/ml', 'Intravenosa', 'Antihistamínico'],

            // Corticosteroides
            ['Dexametasona', 'Dexametasona fosfato', 'Solución inyectable', '8 mg/2 ml', 'Intravenosa', 'Corticoide'],
            ['Dexametasona', 'Dexametasona', 'Tableta', '4 mg', 'Oral', 'Corticoide'],
            ['Prednisona', 'Prednisona', 'Tableta', '50 mg', 'Oral', 'Corticoide'],
            ['Prednisona', 'Prednisona', 'Tableta', '20 mg', 'Oral', 'Corticoide'],
            ['Hidrocortisona', 'Hidrocortisona succinato', 'Solución inyectable', '100 mg', 'Intravenosa', 'Corticoide'],
            ['Metilprednisolona', 'Metilprednisolona succinato', 'Solución inyectable', '500 mg', 'Intravenosa', 'Corticoide'],

            // Antifúngicos / Antivirales
            ['Fluconazol', 'Fluconazol', 'Cápsula', '150 mg', 'Oral', 'Antifúngico'],
            ['Fluconazol', 'Fluconazol', 'Solución inyectable', '200 mg/100 ml', 'Intravenosa', 'Antifúngico'],
            ['Ketoconazol', 'Ketoconazol', 'Tableta', '200 mg', 'Oral', 'Antifúngico'],
            ['Clotrimazol', 'Clotrimazol', 'Crema', '1 %', 'Tópica', 'Antifúngico'],
            ['Aciclovir', 'Aciclovir', 'Tableta', '400 mg', 'Oral', 'Antiviral'],
            ['Aciclovir', 'Aciclovir sódico', 'Solución inyectable', '250 mg', 'Intravenosa', 'Antiviral'],
            ['Oseltamivir', 'Oseltamivir fosfato', 'Cápsula', '75 mg', 'Oral', 'Antiviral'],

            // Analgésicos / Anestésicos
            ['Lidocaína', 'Lidocaína clorhidrato', 'Solución inyectable', '2 %', 'Subcutánea', 'Anestésico local'],
            ['Bupivacaína', 'Bupivacaína clorhidrato', 'Solución inyectable', '0.5 %', 'Subcutánea', 'Anestésico local'],
            ['Midazolam', 'Midazolam clorhidrato', 'Solución inyectable', '5 mg/ml', 'Intravenosa', 'Sedante'],
            ['Propofol', 'Propofol', 'Emulsión inyectable', '10 mg/ml', 'Intravenosa', 'Anestésico general'],
            ['Ketamina', 'Ketamina clorhidrato', 'Solución inyectable', '50 mg/ml', 'Intravenosa', 'Anestésico general'],

            // Vitaminas / Suplementos
            ['Complejo B', 'Vitaminas B1, B6, B12', 'Solución inyectable', '1 ml', 'Intramuscular', 'Vitamina'],
            ['Ácido Fólico', 'Ácido fólico', 'Tableta', '5 mg', 'Oral', 'Vitamina'],
            ['Hierro', 'Sulfato ferroso', 'Tableta', '200 mg', 'Oral', 'Suplemento'],
            ['Vitamina C', 'Ácido ascórbico', 'Tableta', '500 mg', 'Oral', 'Vitamina'],
            ['Vitamina D', 'Colecalciferol', 'Cápsula', '400 UI', 'Oral', 'Vitamina'],
            ['Calcio + Vitamina D', 'Carbonato de calcio + Colecalciferol', 'Tableta', '600 mg/400 UI', 'Oral', 'Suplemento'],

            // Soluciones / Electrolitos
            ['Solución Salina', 'Cloruro de sodio 0.9%', 'Solución inyectable', '500 ml', 'Intravenosa', 'Electrolito'],
            ['Solución Salina', 'Cloruro de sodio 0.9%', 'Solución inyectable', '1000 ml', 'Intravenosa', 'Electrolito'],
            ['Solución Glucosada', 'Dextrosa 5%', 'Solución inyectable', '500 ml', 'Intravenosa', 'Electrolito'],
            ['Solución Glucosada', 'Dextrosa 10%', 'Solución inyectable', '500 ml', 'Intravenosa', 'Electrolito'],
            ['Solución Hartmann', 'Solución Hartmann', 'Solución inyectable', '500 ml', 'Intravenosa', 'Electrolito'],
            ['Cloruro de Potasio', 'Cloruro de potasio', 'Solución inyectable', '20 mEq/10 ml', 'Intravenosa', 'Electrolito'],
            ['Bicarbonato de Sodio', 'Bicarbonato de sodio', 'Solución inyectable', '1 mEq/ml', 'Intravenosa', 'Electrolito'],

            // Oftálmicos / Óticos
            ['Tobramicina', 'Tobramicina', 'Solución oftálmica', '0.3 %', 'Oftálmica', 'Antibiótico oftálmico'],
            ['Ciprofloxacino', 'Ciprofloxacino', 'Solución oftálmica', '0.3 %', 'Oftálmica', 'Antibiótico oftálmico'],
            ['Lágrimas Artificiales', 'Carboximetilcelulosa', 'Solución oftálmica', '0.5 %', 'Oftálmica', 'Lubricante ocular'],

            // Dermatológicos
            ['Hidrocortisona', 'Hidrocortisona acetato', 'Crema', '1 %', 'Tópica', 'Corticoide tópico'],
            ['Betametasona', 'Betametasona valerato', 'Crema', '0.1 %', 'Tópica', 'Corticoide tópico'],
            ['Mupirocina', 'Mupirocina', 'Ungüento', '2 %', 'Tópica', 'Antibiótico tópico'],
            ['Silver Sulfadiazina', 'Sulfadiazina de plata', 'Crema', '1 %', 'Tópica', 'Antibiótico tópico'],

            // Anticoagulantes
            ['Heparina', 'Heparina sódica', 'Solución inyectable', '5000 UI/ml', 'Intravenosa', 'Anticoagulante'],
            ['Enoxaparina', 'Enoxaparina sódica', 'Solución inyectable', '40 mg/0.4 ml', 'Subcutánea', 'Anticoagulante'],
            ['Warfarina', 'Warfarina sódica', 'Tableta', '5 mg', 'Oral', 'Anticoagulante'],

            // Varios
            ['Adrenalina', 'Epinefrina', 'Solución inyectable', '1 mg/ml', 'Intravenosa', 'Vasopresor'],
            ['Atropina', 'Atropina sulfato', 'Solución inyectable', '1 mg/ml', 'Intravenosa', 'Anticolinérgico'],
            ['Naloxona', 'Naloxona clorhidrato', 'Solución inyectable', '0.4 mg/ml', 'Intravenosa', 'Antídoto'],
            ['Flumazenil', 'Flumazenil', 'Solución inyectable', '0.1 mg/ml', 'Intravenosa', 'Antídoto'],
            ['Fenitoína', 'Fenitoína sódica', 'Solución inyectable', '250 mg/5 ml', 'Intravenosa', 'Anticonvulsivo'],
            ['Ácido Valproico', 'Ácido valproico', 'Tableta', '500 mg', 'Oral', 'Anticonvulsivo'],
            ['Carbamazepina', 'Carbamazepina', 'Tableta', '200 mg', 'Oral', 'Anticonvulsivo'],
            ['Levetiracetam', 'Levetiracetam', 'Tableta', '500 mg', 'Oral', 'Anticonvulsivo'],
            ['Fenobarbital', 'Fenobarbital', 'Tableta', '100 mg', 'Oral', 'Anticonvulsivo'],

            // Psicotrópicos
            ['Sertralina', 'Sertralina clorhidrato', 'Tableta', '50 mg', 'Oral', 'Antidepresivo'],
            ['Fluoxetina', 'Fluoxetina clorhidrato', 'Cápsula', '20 mg', 'Oral', 'Antidepresivo'],
            ['Escitalopram', 'Escitalopram oxalato', 'Tableta', '10 mg', 'Oral', 'Antidepresivo'],
            ['Alprazolam', 'Alprazolam', 'Tableta', '0.5 mg', 'Oral', 'Ansiolítico'],
            ['Clonazepam', 'Clonazepam', 'Tableta', '2 mg', 'Oral', 'Ansiolítico'],
            ['Diazepam', 'Diazepam', 'Tableta', '10 mg', 'Oral', 'Ansiolítico'],
            ['Diazepam', 'Diazepam', 'Solución inyectable', '10 mg/2 ml', 'Intravenosa', 'Ansiolítico'],
            ['Quetiapina', 'Quetiapina fumarato', 'Tableta', '100 mg', 'Oral', 'Antipsicótico'],
            ['Risperidona', 'Risperidona', 'Tableta', '2 mg', 'Oral', 'Antipsicótico'],
            ['Haloperidol', 'Haloperidol', 'Solución inyectable', '5 mg/ml', 'Intramuscular', 'Antipsicótico'],

            // Hormonales
            ['Levotiroxina', 'Levotiroxina sódica', 'Tableta', '50 mcg', 'Oral', 'Hormona tiroidea'],
            ['Levotiroxina', 'Levotiroxina sódica', 'Tableta', '100 mcg', 'Oral', 'Hormona tiroidea'],
            ['Prednisona', 'Prednisona', 'Tableta', '5 mg', 'Oral', 'Corticoide'],
            ['Estrógenos Conjugados', 'Estrógenos conjugados', 'Tableta', '0.625 mg', 'Oral', 'Hormonal'],
            ['Progesterona', 'Progesterona micronizada', 'Cápsula', '200 mg', 'Oral', 'Hormonal'],

            // Antiparasitarios
            ['Albendazol', 'Albendazol', 'Tableta', '400 mg', 'Oral', 'Antiparasitario'],
            ['Metronidazol', 'Metronidazol', 'Óvulo', '500 mg', 'Vaginal', 'Antiparasitario'],
            ['Ivermectina', 'Ivermectina', 'Tableta', '6 mg', 'Oral', 'Antiparasitario'],
            ['Prazicuantel', 'Prazicuantel', 'Tableta', '600 mg', 'Oral', 'Antiparasitario'],

            // Antigripales / Antitusígenos
            ['Ambroxol', 'Ambroxol clorhidrato', 'Jarabe', '30 mg/5 ml', 'Oral', 'Mucolítico'],
            ['Bromhexina', 'Bromhexina clorhidrato', 'Jarabe', '4 mg/5 ml', 'Oral', 'Mucolítico'],
            ['Dextrometorfano', 'Dextrometorfano bromhidrato', 'Jarabe', '15 mg/5 ml', 'Oral', 'Antitusígeno'],
            ['Codeína', 'Codeína fosfato', 'Jarabe', '10 mg/5 ml', 'Oral', 'Antitusígeno'],

            // Antiinflamatorios tópicos
            ['Piroxicam', 'Piroxicam', 'Gel', '0.5 %', 'Tópica', 'Antiinflamatorio tópico'],
            ['Diclofenaco', 'Diclofenaco dietilamina', 'Gel', '1.16 %', 'Tópica', 'Antiinflamatorio tópico'],
            ['Ketoprofeno', 'Ketoprofeno', 'Gel', '2.5 %', 'Tópica', 'Antiinflamatorio tópico'],

            // Antihipertensivos IV
            ['Hidralazina', 'Hidralazina clorhidrato', 'Solución inyectable', '20 mg/ml', 'Intravenosa', 'Antihipertensivo'],
            ['Labetalol', 'Labetalol clorhidrato', 'Solución inyectable', '100 mg/20 ml', 'Intravenosa', 'Antihipertensivo'],
            ['Nifedipino', 'Nifedipino', 'Cápsula', '10 mg', 'Oral', 'Antihipertensivo'],

            // Broncodilatadores / Mucolíticos
            ['Acetilcisteína', 'Acetilcisteína', 'Sobre', '600 mg', 'Oral', 'Mucolítico'],
            ['Acetilcisteína', 'Acetilcisteína', 'Solución inyectable', '300 mg/3 ml', 'Intravenosa', 'Mucolítico'],
            ['Aminofilina', 'Aminofilina', 'Solución inyectable', '250 mg/10 ml', 'Intravenosa', 'Broncodilatador'],

            // Antihipertensivos / Vasodilatadores
            ['Isosorbide', 'Isosorbide dinitrato', 'Tableta', '5 mg', 'Oral', 'Vasodilatador'],
            ['Amlodipino', 'Amlodipino besilato', 'Tableta', '10 mg', 'Oral', 'Antihipertensivo'],
            ['Valsartán', 'Valsartán', 'Tableta', '80 mg', 'Oral', 'Antihipertensivo'],
            ['Telmisartán', 'Telmisartán', 'Tableta', '40 mg', 'Oral', 'Antihipertensivo'],
            ['Carvedilol', 'Carvedilol', 'Tableta', '6.25 mg', 'Oral', 'Antihipertensivo'],
            ['Bisoprolol', 'Bisoprolol fumarato', 'Tableta', '5 mg', 'Oral', 'Antihipertensivo'],
            ['Nebivolol', 'Nebivolol', 'Tableta', '5 mg', 'Oral', 'Antihipertensivo'],

            // Antihipertensivos combinados
            ['Losartán + Hidroclorotiazida', 'Losartán + Hidroclorotiazida', 'Tableta', '50/12.5 mg', 'Oral', 'Antihipertensivo'],
            ['Valsartán + Hidroclorotiazida', 'Valsartán + Hidroclorotiazida', 'Tableta', '80/12.5 mg', 'Oral', 'Antihipertensivo'],
            ['Enalapril + Hidroclorotiazida', 'Enalapril + Hidroclorotiazida', 'Tableta', '10/25 mg', 'Oral', 'Antihipertensivo'],

            // Anticoagulantes / Antiagregantes
            ['Rivaroxabán', 'Rivaroxabán', 'Tableta', '20 mg', 'Oral', 'Anticoagulante'],
            ['Apixabán', 'Apixabán', 'Tableta', '5 mg', 'Oral', 'Anticoagulante'],
            ['Ticagrelor', 'Ticagrelor', 'Tableta', '90 mg', 'Oral', 'Antiagregante'],

            // Hipolipemiantes
            ['Rosuvastatina', 'Rosuvastatina cálcica', 'Tableta', '10 mg', 'Oral', 'Hipolipemiante'],
            ['Ezetimiba', 'Ezetimiba', 'Tableta', '10 mg', 'Oral', 'Hipolipemiante'],
            ['Fenofibrato', 'Fenofibrato', 'Cápsula', '200 mg', 'Oral', 'Hipolipemiante'],

            // Antidiabéticos
            ['Empagliflozina', 'Empagliflozina', 'Tableta', '10 mg', 'Oral', 'Antidiabético'],
            ['Dapagliflozina', 'Dapagliflozina', 'Tableta', '10 mg', 'Oral', 'Antidiabético'],
            ['Linagliptina', 'Linagliptina', 'Tableta', '5 mg', 'Oral', 'Antidiabético'],
            ['Insulina Lispro', 'Insulina lispro', 'Solución inyectable', '100 UI/ml', 'Subcutánea', 'Antidiabético'],
            ['Insulina Aspart', 'Insulina aspart', 'Solución inyectable', '100 UI/ml', 'Subcutánea', 'Antidiabético'],
            ['Insulina NPH', 'Insulina humana NPH', 'Suspensión inyectable', '100 UI/ml', 'Subcutánea', 'Antidiabético'],
            ['Liraglutida', 'Liraglutida', 'Solución inyectable', '6 mg/ml', 'Subcutánea', 'Antidiabético'],
            ['Semaglutida', 'Semaglutida', 'Solución inyectable', '1.34 mg/ml', 'Subcutánea', 'Antidiabético'],
        ];

        $laboratorios = [
            'Pfizer', 'Roche', 'Novartis', 'Bayer', 'Sanofi', 'GSK',
            'Boehringer Ingelheim', 'AstraZeneca', 'Merck', 'Lilly',
            'Pisa', 'Genomma Lab', 'Laboratorios Sophia', 'Chinoin',
            'Rimsa', 'Sandoz', 'Teva', 'Viatris', 'Carnot', 'Liomont',
        ];

        $unidades = ['pieza', 'caja', 'frasco', 'ampolleta', 'tubo', 'sobre', 'blíster'];

        foreach ($medicamentos as $index => $med) {
            [$nombre, $sustancia, $presentacion, $concentracion, $via, $grupo] = $med;

            $esAntibiotico = str_contains(strtolower($grupo), 'antibiótico');
            $esPsicotropico = in_array(strtolower($grupo), ['antidepresivo', 'ansiolítico', 'antipsicótico']);
            $esControlado = in_array(strtolower($nombre), ['morfina', 'fentanilo', 'tramadol', 'midazolam', 'ketamina', 'propofol', 'clonazepam', 'alprazolam', 'diazepam']);

            Medicamento::create([
                'nombre' => $nombre,
                'sustancia_activa' => $sustancia,
                'presentacion' => $presentacion,
                'concentracion' => $concentracion,
                'via_administracion' => $via,
                'laboratorio' => $laboratorios[array_rand($laboratorios)],
                'codigo_barras' => str_pad((string) (7500000000000 + $index + 1), 13, '0', STR_PAD_LEFT),
                'registro_sanitario' => 'RS-' . str_pad((string) ($index + 1), 6, '0', STR_PAD_LEFT),
                'grupo_terapeutico' => $grupo,
                'psicotropico' => $esPsicotropico,
                'antibiotico' => $esAntibiotico,
                'controlado' => $esControlado,
                'unidad_medida' => $unidades[array_rand($unidades)],
                'stock_minimo' => rand(5, 50),
                'stock_maximo' => rand(100, 500),
                'precio_compra' => rand(20, 800) + (rand(0, 99) / 100),
                'precio_venta' => rand(30, 1200) + (rand(0, 99) / 100),
                'activo' => true,
            ]);
        }

        $this->command->info(' ' . count($medicamentos) . ' medicamentos creados.');
    }
}