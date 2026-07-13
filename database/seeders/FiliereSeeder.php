<?php

namespace Database\Seeders;

use App\Models\Filiere;
use Illuminate\Database\Seeder;

class FiliereSeeder extends Seeder
{
    public function run(): void
    {
        $filieres = [
            ['code_filiere' => 'INFO-L1', 'nom_filiere' => 'Licence 1 Informatique', 'id_departement' => 1],
            ['code_filiere' => 'INFO-L2', 'nom_filiere' => 'Licence 2 Informatique', 'id_departement' => 1],
            ['code_filiere' => 'INFO-L3', 'nom_filiere' => 'Licence 3 Informatique', 'id_departement' => 1],
            ['code_filiere' => 'INFO-M1', 'nom_filiere' => 'Master 1 Informatique', 'id_departement' => 1],
            ['code_filiere' => 'INFO-M2', 'nom_filiere' => 'Master 2 Informatique', 'id_departement' => 1],
            ['code_filiere' => 'MATH-L1', 'nom_filiere' => 'Licence 1 Mathématiques', 'id_departement' => 2],
            ['code_filiere' => 'MATH-L2', 'nom_filiere' => 'Licence 2 Mathématiques', 'id_departement' => 2],
            ['code_filiere' => 'MATH-L3', 'nom_filiere' => 'Licence 3 Mathématiques', 'id_departement' => 2],
            ['code_filiere' => 'PHYS-L1', 'nom_filiere' => 'Licence 1 Physique', 'id_departement' => 3],
            ['code_filiere' => 'PHYS-L2', 'nom_filiere' => 'Licence 2 Physique', 'id_departement' => 3],
            ['code_filiere' => 'GEST-L1', 'nom_filiere' => 'Licence 1 Gestion', 'id_departement' => 4],
            ['code_filiere' => 'GEST-L2', 'nom_filiere' => 'Licence 2 Gestion', 'id_departement' => 4],
            ['code_filiere' => 'GEST-L3', 'nom_filiere' => 'Licence 3 Gestion', 'id_departement' => 4],
        ];

        foreach ($filieres as $f) {
            Filiere::create($f);
        }
    }
}