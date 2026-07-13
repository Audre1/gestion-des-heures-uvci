<?php

namespace Database\Seeders;

use App\Models\Departement;
use Illuminate\Database\Seeder;

class DepartementSeeder extends Seeder
{
    public function run(): void
    {
        $departements = [
            ['code_departement' => 'INFO', 'nom_departement' => 'Département Informatique'],
            ['code_departement' => 'MATH', 'nom_departement' => 'Département Mathématiques'],
            ['code_departement' => 'PHYS', 'nom_departement' => 'Département Physique'],
            ['code_departement' => 'GEST', 'nom_departement' => 'Département Gestion'],
            ['code_departement' => 'LANG', 'nom_departement' => 'Département Langues'],
        ];

        foreach ($departements as $dep) {
            Departement::create($dep);
        }
    }
}